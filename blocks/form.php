<?php


// Возвращаем описание самого блока "форма"
return [
    'title' => 'Форма',
    'fields' => [
        [
            'name' => 'form_id',
            'type' => 'select',
            'label' => 'Выберите форму',
            'options' => [] // будет подставлено динамически через BlockManager
        ]
    ],
    'render' => function ($data) {
        $formId = $data['form_id'] ?? '';
        $formPath = dirname(__DIR__) . "/blocks/forms/{$formId}.php";

        if ($formId && file_exists($formPath)) {
            include $formPath;
        } else {
            echo "<div class='text-red-600'>❌ Форма '{$formId}' не найдена</div>";
        }
    }
];
