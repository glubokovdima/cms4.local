<?php

function loadPages() {
    return json_decode(file_get_contents(dirname(__DIR__, 2) . '/pages.json'), true);
}

function savePages($pages) {
    file_put_contents(dirname(__DIR__, 2) . '/pages.json', json_encode($pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getAvailableForms(): array {
    $formDir = dirname(__DIR__, 2) . '/blocks/forms';  // ← поправили путь
    $forms = [];

    foreach (glob($formDir . '/*.php') as $file) {
        $name = basename($file, '.php');
        $forms[$name] = ucfirst(str_replace('-', ' ', $name));
    }

    return $forms;
}


function renderAdminLayout(string $title, callable $callback)
{
    ob_start();
    $callback(); // собираем HTML страницы
    $content = ob_get_clean();

    // Подключаем layout
    $pageSlug = $_GET['slug'] ?? 'admin';
    include dirname(__DIR__) . '/layout.php';
}
