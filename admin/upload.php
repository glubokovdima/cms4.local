<?php
$uploadDir = dirname(__DIR__, 2) . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$allowedMime = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'
];

if (!empty($_FILES['file']['name'])) {
    $original = basename($_FILES['file']['name']);
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $nameOnly = pathinfo($original, PATHINFO_FILENAME);
    $nameOnly = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nameOnly); // безопасное имя

    if (!in_array($ext, $allowedExt)) {
        echo json_encode(['success' => false, 'error' => 'Недопустимое расширение']);
        exit;
    }

    if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
        echo json_encode(['success' => false, 'error' => 'Файл не загружен через HTTP POST']);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMime)) {
        echo json_encode(['success' => false, 'error' => 'Недопустимый тип файла']);
        exit;
    }

    $filename = $nameOnly . '.' . $ext;
    $target = $uploadDir . $filename;
    $i = 1;
    while (file_exists($target)) {
        $filename = $nameOnly . '-' . $i . '.' . $ext;
        $target = $uploadDir . $filename;
        $i++;
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        echo json_encode(['success' => true, 'file' => '/uploads/' . $filename]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка при перемещении файла']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Файл не передан']);
}
