<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/functions.php';

require_once __DIR__ . '/blocks.php';

$pages = loadPages();
if (json_last_error() !== JSON_ERROR_NONE) {
    exit("Ошибка JSON: " . json_last_error_msg());
}

$uploadDir = '../assets/images/';
$message = null;

if (!isset($pages['catalog'])) {
    $message = ['type' => 'error', 'text' => '❌ Страница catalog не найдена'];
    $products = [];
} else {
    $products = $pages['catalog']['blocks'][0]['data']['products'] ?? [];
    if (!is_array($products)) $products = [];
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['products'])) {
    $updatedProducts = [];

    foreach ($_POST['products'] as $i => $product) {
        $product = array_map('trim', $product);
        $product = array_map('strip_tags', $product);

        $imageField = "product_image_$i";
        if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === 0) {
            $product['image'] = handleImageUpload($_FILES[$imageField], $uploadDir);
        }

        $updatedProducts[] = $product;
    }

    $pages['catalog']['blocks'][0]['data']['products'] = $updatedProducts;
    savePages($pages);
    $message = ['type' => 'success', 'text' => '✔ Товары обновлены'];
    $products = $updatedProducts;
}

function handleImageUpload($file, $uploadDir): ?string {
    $filename = basename($file['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $base = pathinfo($filename, PATHINFO_FILENAME);
    $target = $uploadDir . $filename;

    $n = 1;
    while (file_exists($target)) {
        $filename = $base . "-" . $n . "." . $ext;
        $target = $uploadDir . $filename;
        $n++;
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }

    return null;
}

renderAdminLayout('Товары', function () use ($products, $message) {
    ?>
    <?php if ($message): ?>
        <div class="mb-4 p-3 <?= $message['type'] === 'error' ? 'bg-red-500' : 'bg-green-500' ?> text-white rounded">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="products-form">
        <div id="products-container">
            <?php foreach ($products as $i => $product): ?>
                <?= renderProductForm($i, $product) ?>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-product">➕ Добавить товар</button><br><br>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">💾 Сохранить</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        let productIndex = <?= count($products) ?>;
        $('#add-product').on('click', function () {
            $.post('includes/ajax-render-product.php', { index: productIndex }, function (html) {
                $('#products-container').append(html);
                productIndex++;
            });
        });
        $(document).on('click', '.delete-product', function () {
            $(this).closest('.product-item').remove();
        });
    </script>
<?php });
