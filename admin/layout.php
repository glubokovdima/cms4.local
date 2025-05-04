<?php
// layout.php
$theme = json_decode(file_get_contents(__DIR__ . '/theme.json'), true);

$background = $theme['background'] ?? 'bg-white';
$accent = $theme['accent'] ?? 'text-indigo-600';
$rounded = $theme['rounded'] ?? '';
$shadow = $theme['shadow'] ?? '';
$maxWidth = $theme['maxWidth'] ?? 'max-w-7xl';
$glass = !empty($theme['glass']) ? true : false;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Сайт') ?></title>
    <link href="/admin/css/tailwind.js" rel="stylesheet">
</head>
<body class="font-sans <?= $background ?> <?= $accent ?> text-gray-900">

<header class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-bold">Мой сайт</div>
    <nav class="space-x-4">
        <a href="/" class="text-blue-600 hover:underline <?= $pageSlug === 'home' ? 'font-bold' : '' ?>">Главная</a>
        <a href="/catalog" class="text-blue-600 hover:underline <?= $pageSlug === 'catalog' ? 'font-bold' : '' ?>">Каталог</a>
    </nav>
</header>

<main class="mx-auto <?= $maxWidth ?> px-4 <?= $shadow ?> <?= $rounded ?> py-8">
    <?php
    require_once __DIR__ . '/includes/BlockManager.php';
    foreach ($blocks as $block) {
        BlockManager::renderFrontend($block);
    }
    ?>
</main>

<?php if ($glass): ?>
    <div class="fixed bottom-0 inset-x-0 bg-white/40 backdrop-blur-md py-6 text-center">
        <p class="text-gray-700">✨ Опциональный блок с эффектом запотевшего стекла</p>
    </div>
<?php endif; ?>

</body>
</html>
