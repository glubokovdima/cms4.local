<?php
require_once __DIR__ . '/functions.php'; // ✅ ОБЯЗАТЕЛЬНО ПЕРВЫМ
require_once __DIR__ . '/BlockManager.php';

$type = $_POST['type'] ?? '';
$index = (int) ($_POST['index'] ?? 0);

// Было:
$blockDef = BlockManager::load($type, true); // 👍 ты уже добавил $force = true

if (!$blockDef) {
    http_response_code(400);
    echo "❌ Блок '{$type}' не найден";
    exit;
}

$data = [];
foreach ($blockDef['fields'] ?? [] as $field) {
    $name = $field['name'];
    $data[$name] = '';
}

$block = [
    'type' => $type,
    'data' => $data
];

echo BlockManager::renderAdminForm($index, $block);
