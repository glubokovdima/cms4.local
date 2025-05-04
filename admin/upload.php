<?php
$uploadDir = dirname(__DIR__, 2) . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (!empty($_FILES['file']['name'])) {
    $filename = basename($_FILES['file']['name']);
    $target = $uploadDir . $filename;

    // если файл с таким именем есть, добавляем -1, -2 и т.д.
    $i = 1;
    $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    while (file_exists($target)) {
        $filename = $nameOnly . '-' . $i . '.' . $ext;
        $target = $uploadDir . $filename;
        $i++;
    }

    move_uploaded_file($_FILES['file']['tmp_name'], $target);
    echo json_encode(['success' => true, 'file' => '/uploads/' . $filename]);
} else {
    echo json_encode(['success' => false]);
}
