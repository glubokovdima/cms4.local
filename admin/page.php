<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'includes/functions.php';
require_once 'includes/blocks.php';

$pages = loadPages();
$message = null;


$slug = $_GET['slug'] ?? null;
if (!$slug || !isset($pages[$slug])) {
    exit("Страница не найдена.");
}

$page = $pages[$slug];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blocks = $_POST['blocks'] ?? [];
    $page['title'] = $_POST['title'] ?? $page['title'];
    $page['blocks'] = array_values($blocks);
    $pages[$slug] = $page;
    savePages($pages);
    $message = ['type' => 'success', 'text' => '✔ Страница сохранена'];
}

renderAdminLayout("Редактирование: {$page['title']}", function () use ($page, $message) {
    if ($message): ?>
        <div class="mb-4 p-3 bg-green-500 text-white rounded"><?= htmlspecialchars($message['text']) ?></div>
    <?php endif; ?>

    <form method="post" id="page-form">
        <label class="block mb-2">Заголовок:
            <input type="text" name="title" value="<?= htmlspecialchars($page['title']) ?>" class="w-full border px-2 py-1">
        </label>

        <?php
        require_once 'includes/BlockManager.php'; // подключаем
        ?>
        <div id="blocks-container">
            <?php
            foreach ($page['blocks'] as $i => $block) {
                echo BlockManager::renderAdminForm($i, $block);
            }
            ?>
        </div>


        <div class="flex items-center space-x-2 mt-4 mb-6">
            <?php
            $blockTypes = BlockManager::getAvailableTypes();
            if (empty($blockTypes)) {
                echo "<div style='color:red'>❌ Блоки не найдены в /blocks</div>";
            }
            ?>


            <select id="block-type" class="border rounded p-2">
                <option value="">➕ Добавить блок</option>
                <?php foreach ($blockTypes as $type => $title): ?>
                    <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($title) ?></option>
                <?php endforeach; ?>
            </select>



            <button type="button" id="add-block" class="bg-gray-200 px-3 py-2 rounded">Добавить</button>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">💾 Сохранить</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../assets/admin.js"></script>
<?php });
