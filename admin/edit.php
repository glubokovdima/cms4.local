<?php
$slug = $_GET['page'] ?? 'catalog';
$pagesFile = '../pages.json';
$uploadDir = '../assets/images/';
$pages = json_decode(file_get_contents($pagesFile), true);
$page = $pages[$slug] ?? null;

if (!$page) {
    echo "Страница не найдена.";
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProducts = [];

    foreach ($_POST['products'] as $i => $product) {
        $imageField = "product_image_$i";

        // Загружаем файл, если есть
        if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === 0) {
            $filename = basename($_FILES[$imageField]['name']);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $target = $uploadDir . $filename;

            $n = 1;
            while (file_exists($target)) {
                $filename = $base . "-" . $n . "." . $ext;
                $target = $uploadDir . $filename;
                $n++;
            }

            if (move_uploaded_file($_FILES[$imageField]['tmp_name'], $target)) {
                $product['image'] = $filename;
            }
        }

        $newProducts[] = $product;
    }

    $page['blocks'][0]['data']['products'] = $newProducts;
    $pages[$slug] = $page;
    file_put_contents($pagesFile, json_encode($pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "<p style='color:green'>✔ Сохранено</p>";
}
?>

<h1>Редактирование каталога</h1>
<form method="post" enctype="multipart/form-data" id="catalog-form">
    <div id="products-container">
        <?php foreach ($page['blocks'][0]['data']['products'] as $i => $product): ?>
            <?= renderProductForm($i, $product) ?>
        <?php endforeach; ?>
    </div>

    <button type="button" id="add-product" style="margin: 10px 0;">➕ Добавить товар</button><br>
    <button type="submit">💾 Сохранить</button>
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    let productIndex = <?= count($page['blocks'][0]['data']['products']) ?>;

    $('#add-product').on('click', function () {
        const html = `<?= addslashes(renderProductForm('__INDEX__')) ?>`.replace(/__INDEX__/g, productIndex);
        $('#products-container').append(html);
        productIndex++;
    });

    $(document).on('click', '.delete-product', function () {
        $(this).closest('.product-item').remove();
    });
</script>

<?php
// Функция для вывода блока формы товара
function renderProductForm($i, $product = ['title' => '', 'price' => '', 'image' => '']) {
    ob_start();
    ?>
    <fieldset class="product-item" style="border:1px solid #ccc; padding:10px; margin-bottom:10px">
        <legend>Товар <?= is_numeric($i) ? ($i + 1) : '#' ?></legend>

        <label>Название:<br>
            <input type="text" name="products[<?= $i ?>][title]" value="<?= htmlspecialchars($product['title']) ?>" style="width:100%">
        </label><br><br>

        <label>Цена:<br>
            <input type="text" name="products[<?= $i ?>][price]" value="<?= htmlspecialchars($product['price']) ?>" style="width:100%">
        </label><br><br>

        <?php if ($product['image']): ?>
            <label>Текущее изображение:<br>
                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="" style="max-width:150px;"><br>
            </label>
        <?php endif; ?>
        <input type="hidden" name="products[<?= $i ?>][image]" value="<?= htmlspecialchars($product['image']) ?>">
        <label>Загрузить новое изображение:<br>
            <input type="file" name="product_image_<?= $i ?>">
        </label><br><br>

        <button type="button" class="delete-product" style="color:red">🗑 Удалить</button>
    </fieldset>
    <?php
    return ob_get_clean();
}
?>
