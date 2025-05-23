<?php

return [
    'title' => 'Промо-блок (тёмный)',
    'fields' => [
        ['name' => 'heading', 'type' => 'text', 'label' => 'Заголовок'],
        ['name' => 'description', 'type' => 'textarea', 'label' => 'Описание'],
        ['name' => 'button_text', 'type' => 'text', 'label' => 'Текст кнопки'],
        ['name' => 'button_url', 'type' => 'text', 'label' => 'Ссылка кнопки'],
        ['name' => 'secondary_link', 'type' => 'text', 'label' => 'Ссылка вторичная'],
        ['name' => 'secondary_text', 'type' => 'text', 'label' => 'Текст вторичной ссылки'],
        ['name' => 'image_url', 'type' => 'text', 'label' => 'URL изображения справа'],
    ],

    'render' => function ($data) {
        $heading = htmlspecialchars($data['heading'] ?? '');
        $description = nl2br(htmlspecialchars($data['description'] ?? ''));
        $buttonText = htmlspecialchars($data['button_text'] ?? 'Подробнее');
        $buttonUrl = htmlspecialchars($data['button_url'] ?? '#');
        $secondaryText = htmlspecialchars($data['secondary_text'] ?? '');
        $secondaryUrl = htmlspecialchars($data['secondary_link'] ?? '#');
        $image = htmlspecialchars($data['image_url'] ?? '');

        ?>
        <div class="bg-white">
            <div class="mx-auto max-w-7xl py-24 sm:px-6 sm:py-32 lg:px-8">
                <div class="relative isolate overflow-hidden bg-gray-900 px-6 pt-16 shadow-2xl sm:rounded-3xl sm:px-16 md:pt-24 lg:flex lg:gap-x-20 lg:px-24 lg:pt-0">
                    <svg viewBox="0 0 1024 1024" class="absolute top-1/2 left-1/2 -z-10 size-[64rem] -translate-y-1/2 [mask-image:radial-gradient(closest-side,white,transparent)] sm:left-full sm:-ml-80 lg:left-1/2 lg:ml-0 lg:-translate-x-1/2 lg:translate-y-0" aria-hidden="true">
                        <circle cx="512" cy="512" r="512" fill="url(#gradient-dark-promo)" fill-opacity="0.7" />
                        <defs>
                            <radialGradient id="gradient-dark-promo">
                                <stop stop-color="#7775D6" />
                                <stop offset="1" stop-color="#E935C1" />
                            </radialGradient>
                        </defs>
                    </svg>
                    <div class="mx-auto max-w-md text-center lg:mx-0 lg:flex-auto lg:py-32 lg:text-left">
                        <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl"><?= $heading ?></h2>
                        <p class="mt-6 text-lg leading-8 text-gray-300"><?= $description ?></p>
                        <div class="mt-10 flex items-center justify-center gap-x-6 lg:justify-start">
                            <a href="<?= $buttonUrl ?>" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100">
                                <?= $buttonText ?>
                            </a>
                            <a href="<?= $secondaryUrl ?>" class="text-sm font-semibold text-white">
                                <?= $secondaryText ?> <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                    <div class="relative mt-16 h-80 lg:mt-8">
                        <img class="absolute top-0 left-0 w-[57rem] max-w-none rounded-md bg-white/5 ring-1 ring-white/10" src="<?= $image ?>" alt="Изображение">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
];
