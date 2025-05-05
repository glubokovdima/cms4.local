<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../blocks.php';

header('Content-Type: text/html; charset=utf-8');

$index = $_POST['index'] ?? 0;
echo renderProductForm($index);
