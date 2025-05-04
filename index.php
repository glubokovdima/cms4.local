<?php
// index.php — основной вход в сайт (корень)
// Старт: включаем ошибки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Пути
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/config');
define('BLOCKS_PATH', BASE_PATH . '/blocks');
define('UPLOADS_PATH', BASE_PATH . '/public/uploads');
define('ASSETS_URL', '/assets/images/');
define('LAYOUT_FILE', BASE_PATH . '/templates/layout.php');

// Загрузка страниц
$pagesFile = CONFIG_PATH . '/pages.json';
if (!file_exists($pagesFile)) {
    die('❌ Файл pages.json не найден');
}

$pages = json_decode(file_get_contents($pagesFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Ошибка JSON: ' . json_last_error_msg());
}

// Роутинг
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
                    <img src="<?= ASSETS_URL . htmlspecialchars($product['image']) ?>" class="w-96 mb-4">
                    <p class="text-xl text-green-600"><?= htmlspecialchars($product['price']) ?></p>
                    <a href="/catalog" class="text-blue-600 hover:underline block mt-4">← Назад в каталог</a>
                </div>
                <?php
            }];
            $pageSlug = 'catalog';
            include LAYOUT_FILE;
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

include LAYOUT_FILE;
exit;


// === Вспомогательные функции ===
function redirect404() {
    header("HTTP/1.1 404 Not Found");
    echo "<h1>404 — страница не найдена</h1>";
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
