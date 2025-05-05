<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/blocks.php';
require_once __DIR__ . '/BlockManager.php';

$pages = loadPages();
$message = null;

$slug = $_GET['slug'] ?? null;
if (!$slug || !isset($pages[$slug])) {
    exit("Страница не найдена.");
}

$page = $pages[$slug];

// Получаем список шаблонов из папки templates
$templateFiles = glob(__DIR__ . '/../templates/*.php');
$availableTemplates = array_map(function ($path) {
    return basename($path, '.php');
}, $templateFiles);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blocks = $_POST['blocks'] ?? [];
    if (!is_array($blocks)) $blocks = [];

    $page['title'] = trim($_POST['title'] ?? $page['title']);
    $page['template'] = $_POST['template'] ?? 'default';
    $page['blocks'] = array_values($blocks);
    $pages[$slug] = $page;

    if (savePages($pages)) {
        $message = ['type' => 'success', 'text' => '✔ Страница сохранена'];
    } else {
        $message = ['type' => 'error', 'text' => '❌ Ошибка при сохранении страницы'];
    }
}

renderAdminLayout("Редактирование: {$page['title']}", function () use ($page, $message, $availableTemplates) {
    ?>
    <?php if ($message): ?>
        <div class="mb-4 p-3 rounded text-white <?= $message['type'] === 'error' ? 'bg-red-500' : 'bg-green-500' ?>">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    <?php endif; ?>

    <form method="post" id="page-form">
        <label class="block mb-2">Заголовок:
            <input type="text" name="title" value="<?= htmlspecialchars($page['title']) ?>" class="w-full border px-2 py-1">
        </label>

        <label class="block mb-4">Шаблон:
            <select name="template" class="w-full border px-2 py-1">
                <?php foreach ($availableTemplates as $tpl): ?>
                    <option value="<?= htmlspecialchars($tpl) ?>" <?= $page['template'] === $tpl ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tpl) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <div id="blocks-container">
            <?php foreach ($page['blocks'] as $i => $block): ?>
                <?= BlockManager::renderAdminForm($i, $block) ?>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center space-x-2 mt-4 mb-6">
            <?php
            $blockTypes = BlockManager::getAvailableTypes();
            if (empty($blockTypes)) {
                echo "<div class='text-red-600'>❌ Блоки не найдены в /blocks</div>";
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
    <?php
});
