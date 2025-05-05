<?php

function renderFields(string $prefix, array $fields, array $data = []): void
{
    foreach ($fields as $field) {
        $name = $field['name'];
        $type = $field['type'];
        $label = $field['label'] ?? $name;
        $value = $data[$name] ?? '';
        $fullName = $prefix . '[' . $name . ']';

        // Атрибуты для showIf
        $showIf = $field['showIf'] ?? null;
        $wrapperAttrs = '';
        if ($showIf && is_array($showIf)) {
            $wrapperAttrs .= ' data-show-if=\'' . json_encode($showIf, JSON_UNESCAPED_UNICODE) . '\'';
        }

        echo "<div class='field-wrapper mb-4'$wrapperAttrs>";
        echo "<label class='block mb-1 font-medium'>{$label}</label>";

        switch ($type) {
            case 'text':
                echo "<input type='text' name='{$fullName}' value='" . htmlspecialchars($value) . "' class='w-full border px-2 py-1 rounded'>";
                break;

            case 'textarea':
                echo "<textarea name='{$fullName}' class='w-full border px-2 py-1 rounded'>" . htmlspecialchars($value) . "</textarea>";
                break;

            case 'select':
                echo "<select name='{$fullName}' class='w-full border px-2 py-1 rounded'>";
                foreach ($field['options'] ?? [] as $optValue => $optLabel) {
                    $selected = ($value == $optValue) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($optValue) . "' $selected>" . htmlspecialchars($optLabel) . "</option>";
                }
                echo "</select>";
                break;

            case 'image':
                echo "<input type='text' name='{$fullName}' value='" . htmlspecialchars($value) . "' class='w-full border px-2 py-1 rounded'>";
                break;

            case 'repeater':
                $subFields = $field['fields'] ?? [];
                $items = is_array($value) ? $value : [];

                echo "<div class='repeater' data-name='{$fullName}'>";

                // Существующие элементы
                foreach ($items as $j => $itemData) {
                    echo "<div class='repeater-item border p-3 mb-2 rounded bg-gray-50'>";
                    renderFields("{$fullName}[{$j}]", $subFields, $itemData);
                    echo "<button type='button' class='delete-repeater-item text-red-600 mt-2'>Удалить</button>";
                    echo "</div>";
                }

                // Шаблон для новых элементов
                echo "<template class='repeater-template'>";
                echo "<div class='repeater-item border p-3 mb-2 rounded bg-gray-50'>";
                renderFields("__NAME__[%INDEX%]", $subFields, []);
                echo "<button type='button' class='delete-repeater-item text-red-600 mt-2'>Удалить</button>";
                echo "</div>";
                echo "</template>";

                echo "<button type='button' class='add-repeater-item mt-2 text-blue-600'>➕ Добавить</button>";
                echo "</div>";
                break;

            // TODO: добавить другие типы, если нужно
        }

        echo "</div>"; // .field-wrapper
    }
}
