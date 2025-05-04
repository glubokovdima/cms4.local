<?php
require_once 'includes/functions.php';

$themePath = dirname(__DIR__) . '/theme.json';

$theme = file_exists($themePath)
    ? json_decode(file_get_contents($themePath), true)
    : [
        'background' => 'bg-white',
        'accent' => 'text-indigo-600',
        'rounded' => '',
        'shadow' => '',
        'maxWidth' => 'max-w-7xl'
    ];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = [
        'background' => trim($_POST['background'] ?? ''),
        'accent' => trim($_POST['accent'] ?? ''),
        'rounded' => trim($_POST['rounded'] ?? ''),
        'shadow' => trim($_POST['shadow'] ?? ''),
        'maxWidth' => trim($_POST['maxWidth'] ?? '')
    ];

    file_put_contents($themePath, json_encode($theme, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $message = '✔ Настройки темы сохранены!';
}

renderAdminLayout('Настройки темы (ручной ввод)', function () use ($theme, $message) {
    ?>
    <?php if (!empty($message)): ?>
        <div class="mb-4 p-3 bg-green-500 text-white rounded"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-6 max-w-xl">
        <div>
            <label class="block font-semibold mb-1">Фоновый цвет (Tailwind):</label>
            <input type="text" name="background" value="<?= htmlspecialchars($theme['background']) ?>" class="w-full border p-2 rounded" placeholder="например: bg-white">
        </div>

        <div>
            <label class="block font-semibold mb-1">Акцентный цвет (Tailwind):</label>
            <input type="text" name="accent" value="<?= htmlspecialchars($theme['accent']) ?>" class="w-full border p-2 rounded" placeholder="например: text-indigo-600">
        </div>

        <div>
            <label class="block font-semibold mb-1">Скругление блоков (Tailwind):</label>
            <input type="text" name="rounded" value="<?= htmlspecialchars($theme['rounded']) ?>" class="w-full border p-2 rounded" placeholder="например: rounded-xl или оставить пустым">
        </div>

        <div>
            <label class="block font-semibold mb-1">Тень блоков (Tailwind):</label>
            <input type="text" name="shadow" value="<?= htmlspecialchars($theme['shadow']) ?>" class="w-full border p-2 rounded" placeholder="например: shadow-lg или оставить пустым">
        </div>

        <div>
            <label class="block font-semibold mb-1">Максимальная ширина:</label>
            <input type="text" name="maxWidth" value="<?= htmlspecialchars($theme['maxWidth']) ?>" class="w-full border p-2 rounded" placeholder="например: max-w-7xl">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">💾 Сохранить</button>
    </form>
    <?php
});
