<?php
// Логика + роутинг
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pagesFile = 'pages.json';
$pages = json_decode(file_get_contents($pagesFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Ошибка JSON: ' . json_last_error_msg());
}

$path = $_GET['path'] ?? '';
$segments = explode('/', trim($path, '/'));
$slug = $segments[0] ?: 'home';
$subslug = $segments[1] ?? null;

// === Подстраница товара ===
if ($slug === 'catalog' && $subslug) {
    $products = $pages['catalog']['blocks'][0]['data']['products'] ?? [];
    foreach ($products as $product) {
        if (slugify($product['title']) === $subslug) {
            $title = "Товар: {$product['title']}";
            $blocks = [function () use ($product) {
                ?>
                <div class="p-8">
                    <h1 class="text-3xl font-bold"><?= htmlspecialchars($product['title']) ?></h1>
                    <img src="/assets/images/<?= htmlspecialchars($product['image']) ?>" class="w-96 mb-4">
                    <p class="text-xl text-green-600"><?= htmlspecialchars($product['price']) ?></p>
                    <a href="/catalog" class="text-blue-600 hover:underline block mt-4">← Назад в каталог</a>
                </div>
                <?php
            }];
            $pageSlug = 'catalog';
            include 'layout.php';
            exit;
        }
    }
    redirect404();
}

// === Обычная страница ===
if (!isset($pages[$slug])) {
    redirect404();
}

$page = $pages[$slug];
$title = $page['title'] ?? 'Без названия';
$blocks = $page['blocks'] ?? [];
$pageSlug = $slug;

include 'layout.php';
exit;

// === Функции ===

function redirect404() {
    header("Location: /404");
    exit;
}

function slugify($str) {
    return strtolower(preg_replace('/[^a-z0-9]+/u', '-', translit($str)));
}

function translit($str) {
    $cyr = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я'];
    $lat = ['a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya'];
    return str_replace($cyr, $lat, mb_strtolower($str));
}
