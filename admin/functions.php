<?php

function loadPages() {
    $file = __DIR__ . '/../config/pages.json';

    if (!file_exists($file)) {
        return []; // если файла нет, возвращаем пустой массив
    }

    $json = file_get_contents($file);
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        die('❌ Ошибка чтения pages.json: ' . json_last_error_msg());
    }

    return $data;
}

function savePages($pages): bool {
    $file = __DIR__ . '/../config/pages.json';
    $json = json_encode($pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('❌ Ошибка при кодировании JSON: ' . json_last_error_msg());
        return false;
    }

    return file_put_contents($file, $json) !== false;
}

function getAvailableForms(): array {
    $formDir = __DIR__ . '/../blocks/forms';
    $forms = [];

    foreach (glob($formDir . '/*.php') as $file) {
        $name = basename($file, '.php');
        $forms[$name] = ucfirst(str_replace('-', ' ', $name));
    }

    return $forms;
}

function getAvailableBlocks(): array {
    $blockDir = __DIR__ . '/../blocks';
    $blocks = [];

    foreach (glob($blockDir . '/*.php') as $file) {
        $name = basename($file, '.php');
        $blocks[$name] = ucfirst(str_replace('-', ' ', $name));
    }

    return $blocks;
}

function renderAdminLayout(string $title, callable $callback)
{
    ob_start();
    $callback(); // собираем HTML страницы
    $content = ob_get_clean();

    $pageSlug = $_GET['slug'] ?? 'admin';
    include __DIR__ . '/layout.php';
}
