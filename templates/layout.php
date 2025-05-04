<?php
$themePath = __DIR__ . '/admin/theme.json';
$theme = file_exists($themePath)
    ? json_decode(file_get_contents($themePath), true)
    : [
        'background' => 'bg-white',
        'accent' => 'text-indigo-600',
        'rounded' => '',
        'shadow' => '',
        'maxWidth' => 'max-w-7xl'
    ];

$title = $title ?? 'Мой сайт';
$pageSlug = $pageSlug ?? 'home';
$blocks = $blocks ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="/admin/css/tailwind.js" rel="stylesheet">
</head>
<body class="font-sans bg-gray-50 text-gray-800">

<header class="bg-white shadow p-4 flex justify-between items-center">
    <div class="text-xl font-bold">Мой сайт</div>
    <nav class="space-x-4">
        <a href="/" class="text-blue-600 hover:underline <?= $pageSlug === 'home' ? 'font-bold' : '' ?>">Главная</a>
        <a href="/catalog" class="text-blue-600 hover:underline <?= $pageSlug === 'catalog' ? 'font-bold' : '' ?>">Каталог</a>
    </nav>
</header>

<main class="mx-auto <?= $theme['maxWidth'] ?> <?= $theme['padding'] ?? 'py-16' ?> <?= $theme['background'] ?> <?= $theme['rounded'] ?> <?= $theme['shadow'] ?>">
    <?php
    require_once __DIR__ . '/admin/includes/BlockManager.php';

    foreach ($blocks as $block) {
        BlockManager::renderFrontend($block);
    }
    ?>
</main>

<!-- Опциональный стеклянный блок -->
<?php if (!empty($theme['frostedGlass'])): ?>
    <div class="fixed bottom-4 right-4 bg-white/30 backdrop-blur-md p-4 rounded-xl shadow-lg">
        ✨ Опциональный блок с эффектом запотевшего стекла
    </div>
<?php endif; ?>

</body>
</html>
