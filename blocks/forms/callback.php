<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['phone'])) {
    $name = trim(strip_tags($_POST['name']));
    $phone = trim(strip_tags($_POST['phone']));
    $messageText = trim(strip_tags($_POST['message'] ?? ''));

    // Здесь можно отправить на email, Telegram, сохранить в лог и т.д.
    // mail(...), file_put_contents(...), curl для Telegram и т.п.

    $success = true;
}

if (!empty($success)): ?>
    <div class="p-4 bg-green-100 text-green-800 rounded">✅ Спасибо! Мы свяжемся с вами.</div>
<?php else: ?>
    <form method="post" class="space-y-4">
        <input type="text" name="name" placeholder="Ваше имя" required class="w-full border rounded px-3 py-2" />
        <input type="tel" name="phone" placeholder="Телефон" required class="w-full border rounded px-3 py-2" />
        <textarea name="message" placeholder="Ваше сообщение" class="w-full border rounded px-3 py-2"></textarea>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
            📩 Отправить
        </button>
    </form>
<?php endif; ?>
