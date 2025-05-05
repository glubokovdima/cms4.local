let blockIndex = $('#blocks-container .block-item').length;

// Добавление нового блока
$('#add-block').on('click', function () {
    const type = $('#block-type').val();
    if (!type) return;

    $.post('block-loader.php', { type }, function (html) {
        $('#blocks-container').append(html);
        blockIndex++;
        updateConditionalFields();
    });
});

// Удаление блока
$(document).on('click', '.delete-block', function () {
    $(this).closest('.block-item').remove();
});

// Условные поля (showIf)
function updateConditionalFields() {
    $('[data-show-if]').each(function () {
        const $wrapper = $(this);
        const condition = $wrapper.data('show-if');

        let visible = true;
        for (const field in condition) {
            const expected = condition[field];
            const $target = $(`[name*='[${field}]']`);
            const actual = $target.val();
            if (actual !== expected) {
                visible = false;
                break;
            }
        }

        $wrapper.toggle(visible);
    });
}
$(document).on('change', 'select, input', updateConditionalFields);
$(document).ready(updateConditionalFields);

// Repeater логика
$(document).on('click', '.add-repeater-item', function () {
    const $repeater = $(this).closest('.repeater');
    const name = $repeater.data('name');
    const index = $repeater.find('.repeater-item').length;
    const templateHtml = $repeater.find('template.repeater-template').html();

    const html = templateHtml
        .replaceAll('__NAME__', name)
        .replaceAll('%INDEX%', index);

    $(html).insertBefore($(this));
    updateConditionalFields();
});

$(document).on('click', '.delete-repeater-item', function () {
    $(this).closest('.repeater-item').remove();
});


// ===== Загрузка изображений =====


$(document).on('click', '.upload-btn', function () {
    const inputName = $(this).data('input-name');
    const input = $(`[name="${inputName}"]`);
    const wrapper = $(this).closest('.image-field');

    const uploader = $('<input type="file" accept="image/*" style="display:none">');
    $('body').append(uploader);

    uploader.on('change', function () {
        const file = this.files[0];
        const formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: 'upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success(res) {
                const data = JSON.parse(res);
                if (data.success) {
                    const url = '/uploads/' + data.file;
                    input.val(data.file);
                    wrapper.find('.image-preview-wrapper').html(`<img src="${url}" class="image-preview w-32 h-32 object-cover border mb-2 rounded">`);
                } else {
                    alert('Ошибка загрузки: ' + data.error);
                }
                uploader.remove();
            }
        });
    });

    uploader.trigger('click');
});

$(document).on('click', '.remove-btn', function () {
    const inputName = $(this).data('input-name');
    const input = $(`[name="${inputName}"]`);
    const wrapper = $(this).closest('.image-field');

    input.val('');
    wrapper.find('.image-preview-wrapper').html('<div class="image-preview w-32 h-32 border flex items-center justify-center text-gray-400 mb-2">Нет изображения</div>');
});


$(document).on('click', '.choose-btn', function () {
    const inputName = $(this).data('input-name');
    const input = $(`[name="${inputName}"]`);
    const wrapper = $(this).closest('.image-field');

    // Создание модального окна (один раз)
    let modal = $('#image-chooser-modal');
    if (modal.length === 0) {
        modal = $(`
            <div id="image-chooser-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white p-4 rounded shadow max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-bold mb-4">Выберите изображение</h2>
                    <div class="image-chooser-grid flex flex-wrap gap-2 justify-start"></div>
                    <button class="close-modal mt-4 px-4 py-2 bg-gray-600 text-white rounded">Закрыть</button>
                </div>
            </div>
        `);
        $('body').append(modal);

        modal.on('click', '.close-modal', () => modal.remove());
    }

    const grid = modal.find('.image-chooser-grid');
    grid.empty();
    modal.show();

    $.getJSON('list-images.php', function (images) {
        images.forEach(img => {
            const imgEl = $('<img>').attr('src', '/uploads/' + img).addClass('w-24 h-24 object-cover border cursor-pointer rounded');
            imgEl.on('click', () => {
                input.val(img);
                wrapper.find('.image-preview-wrapper').html(`<img src="/uploads/${img}" class="image-preview w-32 h-32 object-cover border mb-2 rounded">`);
                modal.remove();
            });
            grid.append(imgEl);
        });
    });
});


