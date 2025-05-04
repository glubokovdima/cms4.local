<?php

return [
    'title' => 'Баннер',
    'fields' => [
        ['name' => 'title', 'type' => 'text', 'label' => 'Заголовок'],
        ['name' => 'subtitle', 'type' => 'text', 'label' => 'Подзаголовок'],
        ['name' => 'button_text', 'type' => 'text', 'label' => 'Текст кнопки'],
        ['name' => 'button_url', 'type' => 'text', 'label' => 'Ссылка кнопки'],
        ['name' => 'background', 'type' => 'image', 'label' => 'Фон (URL)']
    ],
    'render' => function ($data) {
        ?>
        <section class="py-16 bg-cover bg-center" style="background-image: url('/assets/images/<?= htmlspecialchars($data['background'] ?? '') ?>');">
            <div class="max-w-2xl mx-auto text-white text-center">
                <h2 class="text-4xl font-bold mb-2"><?= htmlspecialchars($data['title'] ?? '') ?></h2>
                <p class="text-lg mb-4"><?= htmlspecialchars($data['subtitle'] ?? '') ?></p>
                <?php if (!empty($data['button_text'])): ?>
                    <a href="<?= htmlspecialchars($data['button_url'] ?? '#') ?>" class="bg-blue-600 px-6 py-2 rounded text-white">
                        <?= htmlspecialchars($data['button_text']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
];
