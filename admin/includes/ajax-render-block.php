<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/BlockManager.php';

$type = $_POST['type'] ?? '';
$index = (int) ($_POST['index'] ?? 0);

$blockDef = BlockManager::load($type, true);

if (!$blockDef) {
    http_response_code(400);
    echo "❌ Блок '{$type}' не найден";
    exit;
}

$data = [];
foreach ($blockDef['fields'] ?? [] as $field) {
    $name = $field['name'];
    if ($field['type'] === 'repeater') {
        $data[$name] = []; // пустой массив по умолчанию
    } else {
        $data[$name] = '';
    }
}

$block = [
    'type' => $type,
    'data' => $data
];

echo BlockManager::renderAdminForm($index, $block);
