<?php
// block-loader.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/blocks.php';
require_once __DIR__ . '/BlockManager.php';

header('Content-Type: text/html; charset=utf-8');

// Получаем тип блока
$type = $_POST['type'] ?? null;

if (!$type || !BlockManager::isValidType($type)) {
    http_response_code(400);
    echo '❌ Неверный тип блока';
    exit;
}

// Создаём новый блок с данными по умолчанию
$block = [
    'type' => $type,
    'data' => BlockManager::getDefaultData($type),
];

// Рендерим HTML формы для админки
$index = (int) ($_POST['index'] ?? 0);
echo BlockManager::renderAdminForm($index, $block);
