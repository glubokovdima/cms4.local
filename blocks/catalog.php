<?php

// Пример данных — можно будет заменить динамически
$availableItems = [
    'chair' => 'Стул',
    'table' => 'Стол',
    'sofa' => 'Диван'
];

return [
    'title' => 'Каталог (выбор товаров)',
    'fields' => [
        [
            'name' => 'items',
            'type' => 'checkbox',
            'label' => 'Выберите товары',
            'options' => $availableItems
        ]
    ],
    'render' => function ($data) use ($availableItems) {
        echo "<div class='grid grid-cols-2 gap-4'>";
        foreach ($data['items'] ?? [] as $slug => $on) {
            if ($on && isset($availableItems[$slug])) {
                echo "<div class='p-4 border rounded bg-white shadow'>";
                echo "<p class='font-bold'>" . htmlspecialchars($availableItems[$slug]) . "</p>";
                echo "</div>";
            }
        }
        echo "</div>";
    }
];
