<?php
// $title — заголовок страницы
// $blocks — массив блоков (или callable)
// $pageSlug — текущий slug
$themePath = __DIR__ . '/theme.json';
$theme = file_exists($themePath) ? json_decode(file_get_contents($themePath), true) : [];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="<?= $theme['background'] ?> <?= $theme['rounded'] ? 'rounded-xl' : '' ?> <?= $theme['shadow'] ? 'shadow-md' : '' ?>">

<header class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <div class="text-xl font-bold text-indigo-700">Мой сайт</div>
        <nav class="space-x-6">
            <a href="/" class="<?= $pageSlug === 'home' ? 'font-bold text-indigo-600' : 'text-gray-700 hover:text-indigo-500' ?>">Главная</a>
            <a href="../index.php" class="<?= $pageSlug === 'catalog' ? 'font-bold text-indigo-600' : 'text-gray-700 hover:text-indigo-500' ?>">Каталог</a>
        </nav>
    </div>
</header>

<main class="pt-24">
    <?php
    require_once __DIR__ . '/admin/includes/BlockManager.php';

    if (is_callable($blocks)) {
        $blocks(); // возможность передать замыкание вместо массива
    } elseif (is_array($blocks)) {
        foreach ($blocks as $block) {
            BlockManager::renderFrontend($block);
        }
    }
    ?>
</main>

</body>
</html>
