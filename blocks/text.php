<?php

return [
    'title' => 'Текстовый блок',
    'fields' => [
        ['name' => 'content', 'type' => 'textarea', 'label' => 'Текст']
    ],
    'render' => function ($data) {
        echo "<div class='max-w-2xl mx-auto py-8'>";
        echo "<div class='prose'>" . nl2br(htmlspecialchars($data['content'] ?? '')) . "</div>";
        echo "</div>";
    }
];
