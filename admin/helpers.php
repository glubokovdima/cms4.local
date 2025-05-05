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
                echo renderImageField($fullName, $value, $label);
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

            // другие типы можно добавить здесь
        }

        echo "</div>"; // .field-wrapper
    }
}


function renderImageField($name, $value = '', $label = 'Изображение')
{
    $preview = '';
    if ($value) {
        $preview = (str_starts_with($value, '/uploads/') || str_starts_with($value, 'http'))
            ? $value
            : '/uploads/' . ltrim($value, '/');
    }

    $imgTag = $preview
        ? '<img src="' . $preview . '" class="image-preview w-32 h-32 object-cover border mb-2 rounded">'
        : '<div class="image-preview w-32 h-32 border flex items-center justify-center text-gray-400 mb-2">Нет изображения</div>';

    return '
    <div class="image-field">
        <label>' . $label . '</label><br>
        <div class="image-preview-wrapper">' . $imgTag . '</div>
        <input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" class="image-input">
        <div class="flex gap-2">
            <button type="button" class="upload-btn px-3 py-1 bg-blue-500 text-white rounded text-sm" data-input-name="' . $name . '">Загрузить</button>
            <button type="button" class="choose-btn px-3 py-1 bg-gray-500 text-white rounded text-sm" data-input-name="' . $name . '">Выбрать</button>
            <button type="button" class="remove-btn px-3 py-1 bg-red-500 text-white rounded text-sm" data-input-name="' . $name . '">Удалить</button>
        </div>
    </div>';
}
