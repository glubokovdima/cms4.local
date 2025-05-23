<?php

return [
    'title' => 'Hero-1',
    'fields' => [
        ['name' => 'heading', 'type' => 'text', 'label' => 'Заголовок'],
        ['name' => 'description', 'type' => 'textarea', 'label' => 'Описание'],

        [
            'name' => 'background_type',
            'type' => 'select',
            'label' => 'Тип фона',
            'options' => [
                'svg' => 'SVG',
                'color' => 'Цвет Tailwind',
                'image' => 'Изображение (URL)'
            ]
        ],

        [
            'name' => 'background_svg',
            'type' => 'textarea',
            'label' => 'SVG фон (HTML)',
            'showIf' => ['background_type' => 'svg']
        ],
        [
            'name' => 'background_color',
            'type' => 'text',
            'label' => 'Цвет Tailwind (например, bg-indigo-100)',
            'showIf' => ['background_type' => 'color']
        ],
        [
            'name' => 'background_image',
            'type' => 'image',
            'label' => 'Фоновое изображение',
            'showIf' => ['background_type' => 'image']
        ],

        [
            'name' => 'buttons',
            'type' => 'repeater',
            'label' => 'Кнопки',
            'fields' => [
                ['name' => 'text', 'type' => 'text', 'label' => 'Текст'],
                ['name' => 'url', 'type' => 'text', 'label' => 'Ссылка'],
                [
                    'name' => 'accent',
                    'type' => 'select',
                    'label' => 'Тип кнопки',
                    'options' => [
                        'primary' => 'Акцентная',
                        'secondary' => 'Вторичная'
                    ]
                ]
            ]
        ]
    ],

    'render' => function ($data) {
        $bgType = $data['background_type'] ?? 'svg';
        $background = '';

        if ($bgType === 'svg') {
            $allowedTags = '<svg><g><path><rect><circle><line><defs><use><clipPath><polygon><polyline>';
            $svg = $data['background_svg'] ?? '';
            $background = "<div class='absolute inset-0 pointer-events-none -z-10'>" . strip_tags($svg, $allowedTags) . "</div>";
        } elseif ($bgType === 'color') {
            $class = htmlspecialchars($data['background_color'] ?? 'bg-gray-100');
            $background = "<div class='absolute inset-0 $class -z-10'></div>";
        } elseif ($bgType === 'image') {
            $url = htmlspecialchars($data['background_image'] ?? '');
            $background = "<div class='absolute inset-0 bg-cover bg-center -z-10' style='background-image: url(\"$url\")'></div>";
        }

        $heading = htmlspecialchars($data['heading'] ?? '');
        $description = nl2br(htmlspecialchars($data['description'] ?? ''));

        ?>
        <div class="relative overflow-hidden bg-white">
            <?= $background ?>
            <div class="relative isolate px-6 pt-24 lg:px-8 z-10">
                <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56 text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl"><?= $heading ?></h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600"><?= $description ?></p>

                    <?php if (!empty($data['buttons']) && is_array($data['buttons'])): ?>
                        <div class="mt-10 flex flex-wrap justify-center gap-4">
                            <?php foreach ($data['buttons'] as $btn): ?>
                                <?php
                                $btnText = htmlspecialchars($btn['text'] ?? '');
                                $btnUrl = htmlspecialchars($btn['url'] ?? '#');
                                $isAccent = ($btn['accent'] ?? 'primary') === 'primary';
                                ?>
                                <?php if ($isAccent): ?>
                                    <a href="<?= $btnUrl ?>" class="rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                        <?= $btnText ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $btnUrl ?>" class="text-sm font-semibold text-indigo-600 border border-indigo-600 px-4 py-2.5 rounded-md hover:bg-indigo-50">
                                        <?= $btnText ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
];
