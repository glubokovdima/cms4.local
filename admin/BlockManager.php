<?php

class BlockManager
{
    protected static $cache = [];

    public static function load(string $type, bool $force = false): ?array
    {
        if (!$force && isset(self::$cache[$type])) return self::$cache[$type];

        $path = dirname(__DIR__, 2) . "/blocks/{$type}.php";
        if (!file_exists($path)) return null;

        $definition = include $path;

        // Автозаполнение форм (если блок форм)
        if ($type === 'form' && isset($definition['fields'])) {
            require_once __DIR__ . '/functions.php';
            foreach ($definition['fields'] as &$field) {
                if ($field['name'] === 'form_id' && $field['type'] === 'select') {
                    $field['options'] = getAvailableForms();
                }
            }
        }

        self::$cache[$type] = $definition;
        return $definition;
    }

    public static function renderFrontend(array $block)
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];

        $def = self::load($type);
        if (!$def || !is_callable($def['render'] ?? null)) {
            echo "<div class='text-red-600'>⚠ Шаблон блока '{$type}' не найден</div>";
            return;
        }

        $def['render']($data);
    }

    public static function renderAdminForm(int $index, array $block): string
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];

        $def = self::load($type);
        if (!$def) return "<div class='text-red-600'>❌ Блок '{$type}' не найден</div>";

        ob_start();
        echo "<div class=\"border p-4 rounded mb-4 bg-gray-50 block-item\">";
        echo "<div class=\"flex justify-between items-center mb-2\">";
        echo "<strong>Блок " . ($index + 1) . ": " . htmlspecialchars($type) . "</strong>";
        echo "<button type=\"button\" class=\"text-red-600 delete-block\">Удалить</button>";
        echo "</div>";
        echo "<input type=\"hidden\" name=\"blocks[{$index}][type]\" value=\"" . htmlspecialchars($type) . "\">";

        foreach ($def['fields'] ?? [] as $field) {
            $name = $field['name'];
            $value = $data[$name] ?? '';
            $label = $field['label'] ?? $name;
            $inputName = "blocks[{$index}][data][{$name}]";

            $showIfAttr = '';
            if (!empty($field['showIf']) && is_array($field['showIf'])) {
                $json = htmlspecialchars(json_encode($field['showIf']));
                $showIfAttr = " data-show-if='{$json}'";
            }

            echo "<div class=\"field-group mb-4\" {$showIfAttr}>";

            switch ($field['type']) {
                case 'text':
                    echo "<label class='block mb-1 text-sm font-medium'>{$label}</label>";
                    echo "<input type='text' name='{$inputName}' value='" . htmlspecialchars($value) . "' class='w-full border rounded px-2 py-1'>";
                    break;

                case 'textarea':
                    echo "<label class='block mb-1 text-sm font-medium'>{$label}</label>";
                    echo "<textarea name='{$inputName}' class='w-full border rounded px-2 py-1'>" . htmlspecialchars($value) . "</textarea>";
                    break;

                case 'select':
                    echo "<label class='block mb-1 text-sm font-medium'>{$label}</label>";
                    echo "<select name='{$inputName}' class='w-full border rounded px-2 py-1'>";
                    foreach ($field['options'] ?? [] as $optVal => $optLabel) {
                        $selected = $optVal == $value ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($optVal) . "' $selected>" . htmlspecialchars($optLabel) . "</option>";
                    }
                    echo "</select>";
                    break;

                case 'image':
                    echo "<label class='block mb-1 text-sm font-medium'>{$label}</label>";
                    echo "<div class='image-uploader' data-name='{$inputName}'>";
                    echo "<input type='hidden' name='{$inputName}' value='" . htmlspecialchars($value) . "'>";
                    if (!empty($value)) {
                        echo "<img src='{$value}' class='h-20 preview mb-2'>";
                    }
                    echo "<div class='flex gap-2 mt-1'>";
                    echo "<button type='button' class='replace-image text-blue-600 text-sm underline'>Заменить</button>";
                    echo "<button type='button' class='remove-image text-red-600 text-sm underline'>Удалить</button>";
                    echo "<button type='button' class='choose-from-uploaded text-gray-700 text-sm underline'>Выбрать</button>";
                    echo "</div><div class='uploaded-images hidden mt-2'></div></div>";
                    break;

                case 'repeater':
                    echo "<label class='block mb-1 text-sm font-medium'>{$label}</label>";
                    $items = is_array($value) ? $value : [];
                    echo "<div class='repeater border rounded p-2 space-y-2' data-name='{$inputName}'>";
                    foreach ($items as $i => $item) {
                        echo "<div class='repeater-item border p-2 rounded bg-white'>";
                        foreach ($field['fields'] as $subField) {
                            $subName = $subField['name'];
                            $subLabel = $subField['label'] ?? $subName;
                            $subValue = $item[$subName] ?? '';
                            $subInput = "{$inputName}[{$i}][{$subName}]";

                            echo "<label class='block text-sm font-medium'>{$subLabel}</label>";
                            if ($subField['type'] === 'select') {
                                echo "<select name='{$subInput}' class='w-full border px-2 py-1 rounded mb-2'>";
                                foreach ($subField['options'] as $val => $lbl) {
                                    $sel = $val == $subValue ? 'selected' : '';
                                    echo "<option value='{$val}' $sel>" . htmlspecialchars($lbl) . "</option>";
                                }
                                echo "</select>";
                            } else {
                                echo "<input type='text' name='{$subInput}' value='" . htmlspecialchars($subValue) . "' class='w-full border px-2 py-1 rounded mb-2'>";
                            }
                        }
                        echo "<button type='button' class='remove-repeater-item text-sm text-red-600'>Удалить</button>";
                        echo "</div>";
                    }
                    echo "<button type='button' class='add-repeater-item mt-2 bg-gray-200 px-2 py-1 rounded'>+ Добавить</button>";
                    echo "</div>";
                    break;

                default:
                    echo "<div class='text-red-600'>⚠ Неизвестный тип поля: {$field['type']}</div>";
            }

            echo "</div>"; // .field-group
        }

        echo "</div>"; // .block-item
        return ob_get_clean();
    }

    public static function getAvailableTypes(): array
    {
        $dir = dirname(__DIR__, 2) . '/blocks';
        $types = [];

        foreach (glob($dir . '/*.php') as $file) {
            $type = basename($file, '.php');
            $block = include $file;
            $title = $block['title'] ?? $type;
            $types[$type] = $title;
        }

        return $types;
    }
}
