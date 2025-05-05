<?php
function loadThemeSettings($path) {
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    $theme = json_decode($json, true);
    return json_last_error() === JSON_ERROR_NONE ? $theme : [];
}

$theme = loadThemeSettings(__DIR__ . '/../config/theme.json');

$bg = $theme['background'] ?? 'bg-white';
$accent = $theme['accent'] ?? 'text-indigo-600';
$rounded = $theme['rounded'] ?? '';
$shadow = $theme['shadow'] ?? '';
$maxWidth = $theme['maxWidth'] ?? 'max-w-7xl';

$title = $title ?? 'Админка';
$content = $content ?? '';
$extraHead = $extraHead ?? '';
$extraScripts = $extraScripts ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?= $extraHead ?>
</head>
<body class="bg-gray-100 font-sans text-gray-900">

<header class="bg-white shadow p-4 flex justify-between items-center sticky top-0 z-50">
    <div class="text-2xl font-bold">Админка</div>
    <nav class="space-x-4 text-blue-600 text-sm">
        <a href="/admin/index.php" class="hover:underline">Страницы</a>
        <a href="/admin/products.php" class="hover:underline">Товары</a>
        <a href="/admin/theme-admin.php" class="hover:underline">Тема</a>
        <a href="/" target="_blank" class="hover:underline">↗ На сайт</a>
    </nav>
</header>

<main class="<?= $maxWidth ?> mx-auto py-8 <?= $bg ?> <?= $rounded ?> <?= $shadow ?> space-y-6">
    <h1 class="text-xl font-semibold mb-4"><?= htmlspecialchars($title) ?></h1>
    <?= is_string($content) ? $content : '' ?>
</main>

<?= $extraScripts ?>
</body>
</html>
