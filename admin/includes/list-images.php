<?php
$dir = dirname(__DIR__, 2) . '/uploads/';
$webPath = '/uploads/';
$files = [];

foreach (glob($dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $file) {
    $files[] = $webPath . basename($file);
}

header('Content-Type: application/json');
echo json_encode($files);
