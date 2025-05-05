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

// Новая картинка
$(document).on('change', '.upload-image-input', function () {
    const wrapper = $(this).closest('.image-uploader');
    const input = wrapper.find('input[type=hidden]');
    const file = this.files[0];

    uploadImage(file, (url) => {
        input.val(url);
        wrapper.find('img.preview').remove();
        wrapper.prepend(`<img src="${url}" class="h-20 preview mb-2">`);
    });
});

// Заменить изображение
$(document).on('click', '.replace-image', function () {
    const wrapper = $(this).closest('.image-uploader');
    const input = wrapper.find('input[type=hidden]');
    const uploader = $('<input type="file" accept="image/*" style="display:none">');
    $('body').append(uploader);

    uploader.on('change', function () {
        const file = this.files[0];
        const formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: 'includes/upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success(res) {
                const data = JSON.parse(res);
                if (data.success) {
                    input.val(data.file);
                    wrapper.find('img.preview').remove();
                    wrapper.prepend(`<img src="${data.file}" class="h-20 preview mb-2">`);
                }
                uploader.remove();
            }
        });
    });

    uploader.trigger('click');
});

// Удалить изображение
$(document).on('click', '.remove-image', function () {
    const wrapper = $(this).closest('.image-uploader');
    wrapper.find('input[type=hidden]').val('');
    wrapper.find('img.preview').remove();
});

// Выбрать из загруженных
$(document).on('click', '.choose-from-uploaded', function () {
    const wrapper = $(this).closest('.image-uploader');
    const container = wrapper.find('.uploaded-images');
    const input = wrapper.find('input[type=hidden]');
    container.toggle();

    if (container.children().length > 0) return;

    $.getJSON('includes/list-images.php', function (images) {
        images.forEach(img => {
            const imgEl = $('<img>').attr('src', img).addClass('h-16 mr-2 mb-2 cursor-pointer inline-block');
            imgEl.on('click', () => {
                input.val(img);
                wrapper.find('img.preview').remove();
                wrapper.prepend(`<img src="${img}" class="h-20 preview mb-2">`);
                container.hide();
            });
            container.append(imgEl);
        });
    });
});
