<?php
function renderProductForm($i, $product = ['title' => '', 'price' => '', 'image' => '']) {
    $title = htmlspecialchars($product['title'] ?? '');
    $price = htmlspecialchars($product['price'] ?? '');
    $image = htmlspecialchars($product['image'] ?? '');

    ob_start();
    ?>
    <fieldset class="product-item border p-4 mb-4 rounded bg-white shadow">
        <legend class="font-semibold text-lg mb-2">Товар <?= is_numeric($i) ? ($i + 1) : '#' ?></legend>

        <label class="block mb-2">Название:
            <input type="text" name="products[<?= $i ?>][title]" value="<?= $title ?>" class="w-full border px-2 py-1 rounded">
        </label>

        <label class="block mb-2">Цена:
            <input type="text" name="products[<?= $i ?>][price]" value="<?= $price ?>" class="w-full border px-2 py-1 rounded">
        </label>

        <?php if (!empty($image)): ?>
            <label class="block mb-2">Текущее изображение:
                <img src="../assets/images/<?= $image ?>" style="max-width:150px;" class="my-2 block rounded shadow">
            </label>
        <?php endif; ?>

        <input type="hidden" name="products[<?= $i ?>][image]" value="<?= $image ?>">

        <label class="block mb-2">Загрузить новое изображение:
            <input type="file" name="product_image_<?= $i ?>" class="block">
        </label>

        <button type="button" class="delete-product text-red-600 mt-2">🗑 Удалить</button>
    </fieldset>
    <?php
    return ob_get_clean();
}