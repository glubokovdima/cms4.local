<?php

require_once 'includes/functions.php';
$pages = loadPages();
$message = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_slug'], $_POST['new_title'])) {
    $slug = trim($_POST['new_slug']);
    $title = trim($_POST['new_title']);

    if (!isset($pages[$slug])) {
        $pages[$slug] = ['title' => $title, 'blocks' => []];
        savePages($pages);
        header("Location: page.php?slug=$slug");
        exit;
    } else {
        $message = ['type' => 'error', 'text' => 'Страница с таким slug уже существует.'];
    }
}

renderAdminLayout('Страницы', function () use ($pages, $message) {
    if ($message): ?>
        <div class="mb-4 p-3 rounded text-white <?= $message['type'] === 'error' ? 'bg-red-500' : 'bg-green-500' ?>">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    <?php endif; ?>

    <ul class="list-disc pl-5 mb-6 space-y-1">
        <?php foreach ($pages as $slug => $page): ?>
            <li><a href="page.php?slug=<?= $slug ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($page['title']) ?></a></li>
        <?php endforeach; ?>
    </ul>

    <form method="post" class="space-y-4">
        <input type="text" name="new_slug" placeholder="slug" required class="border px-2 py-1 w-full">
        <input type="text" name="new_title" placeholder="Заголовок" required class="border px-2 py-1 w-full">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Создать</button>
    </form>
<?php });
