<?php

class BlockManager
{
    public static function getAvailableTypes(): array
    {
        $blocksDir = __DIR__ . '/../blocks';
        $types = [];

        foreach (glob($blocksDir . '/*.php') as $file) {
            $name = basename($file, '.php');
            $definition = include $file;

            if (is_array($definition) && isset($definition['title'])) {
                $types[$name] = $definition['title'];
            }
        }

        return $types;
    }

    public static function renderFrontend(array $block): void
    {
        $type = $block['type'] ?? null;
        $data = $block['data'] ?? [];

        if (!$type) {
            echo "<div class='text-red-600'>⚠ Блок без типа</div>";
            return;
        }

        $file = __DIR__ . '/../blocks/' . $type . '.php';

        if (!file_exists($file)) {
            echo "<div class='text-red-600'>⚠ Блок <b>{$type}</b> не найден</div>";
            return;
        }

        $definition = include $file;

        if (!is_array($definition) || !is_callable($definition['render'] ?? null)) {
            echo "<div class='text-red-600'>⚠ Блок <b>{$type}</b> не содержит render-функции</div>";
            return;
        }

        echo "<!-- BLOCK: {$type} -->";
        $definition['render']($data);
    }

    public static function getAvailableBlocks(): array
    {
        $blocksDir = __DIR__ . '/../blocks';
        $blockFiles = glob($blocksDir . '/*.php');
        $blockList = [];

        foreach ($blockFiles as $file) {
            $basename = basename($file, '.php');
            $blockList[] = $basename;
        }

        return $blockList;
    }

    public static function getDefaultData(string $type): array
    {
        $file = __DIR__ . '/../blocks/' . $type . '.php';

        if (!file_exists($file)) return [];

        $definition = include $file;

        return $definition['defaults'] ?? [];
    }



    public static function isValidType(string $type): bool
    {
        return in_array($type, self::getAvailableBlocks(), true);
    }

    public static function renderAdminForm(int $i, array $block): string
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];

        $file = __DIR__ . '/../blocks/' . $type . '.php';
        if (!file_exists($file)) {
            return "<div class='text-red-600 p-4'>⚠ Блок <strong>{$type}</strong> не найден</div>";
        }

        $definition = include $file;
        if (!is_array($definition)) {
            return "<div class='text-red-600 p-4'>⚠ Неверный формат блока <strong>{$type}</strong></div>";
        }

        // Если задан renderAdmin — использовать его
        if (is_callable($definition['renderAdmin'] ?? null)) {
            return $definition['renderAdmin']($i, $data);
        }

        // Если renderAdmin нет — сгенерировать форму по полям
        ob_start();
        echo "<fieldset class='block-item border p-4 mb-4 rounded bg-white shadow'>";
        echo "<legend class='text-lg font-bold mb-2'>" . htmlspecialchars($definition['title'] ?? $type) . "</legend>";
        echo "<input type='hidden' name='blocks[{$i}][type]' value='" . htmlspecialchars($type) . "'>";
        echo "<div class='space-y-4'>";
        require __DIR__ . '/../blocks/form.php';

        require_once __DIR__ . '/helpers.php';
        renderFields("blocks[{$i}][data]", $definition['fields'] ?? [], $data);
        echo "</div>";
        echo "<button type='button' class='delete-block text-red-600 mt-2'>Удалить блок</button>";
        echo "</fieldset>";
        return ob_get_clean();
    }


}
