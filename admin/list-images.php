<?php

$dir = dirname(__DIR__, 2) . '/uploads/';
$webPath = '/uploads/';
$files = [];

if (!is_dir($dir)) {
    http_response_code(500);
    echo json_encode(['error' => 'Каталог загрузок не найден']);
    exit;
}

// Получаем только изображения с разрешёнными расширениями
$images = glob($dir . '*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE);

foreach ($images as $filePath) {
    // Подстраховка — исключаем подмену имени
    if (!is_file($filePath)) continue;

    $filename = basename($filePath);
    $files[] = $webPath . $filename;
}

sort($files); // можно сортировать по имени

header('Content-Type: application/json; charset=utf-8');
echo json_encode($files, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
