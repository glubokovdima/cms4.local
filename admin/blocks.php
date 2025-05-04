<?php

function renderProductForm($i, $product = ['title' => '', 'price' => '', 'image' => '']) {
    ob_start();
    ?>
    <fieldset class="product-item border p-4 mb-4 rounded bg-white shadow">
        <legend>Товар <?= is_numeric($i) ? ($i + 1) : '#' ?></legend>

        <label>Название:<br>
            <input type="text" name="products[<?= $i ?>][title]" value="<?= htmlspecialchars($product['title']) ?>" style="width:100%">
        </label><br><br>

        <label>Цена:<br>
            <input type="text" name="products[<?= $i ?>][price]" value="<?= htmlspecialchars($product['price']) ?>" style="width:100%">
        </label><br><br>

        <?php if ($product['image']): ?>
            <label>Текущее изображение:<br>
                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" style="max-width:150px;"><br>
            </label>
        <?php endif; ?>
        <input type="hidden" name="products[<?= $i ?>][image]" value="<?= htmlspecialchars($product['image']) ?>">
        <label>Загрузить новое изображение:<br>
            <input type="file" name="product_image_<?= $i ?>">
        </label><br><br>

        <button type="button" class="delete-product text-red-600">🗑 Удалить</button>
    </fieldset>
    <?php
    return ob_get_clean();
}
