let blockIndex = $('#blocks-container .block-item').length;

// Добавление нового блока
$('#add-block').off('click').on('click', function () {
    const type = $('#block-type').val();
    if (!type) return alert('Выберите тип блока');

    $.post('includes/ajax-render-block.php', { type, index: blockIndex }, function (html) {
        $('#blocks-container').append(html);
        blockIndex++;
        updateConditionalFields();
        bindRepeaterLogic();
    });
});

// Удаление блока
$(document).on('click', '.delete-block', function () {
    $(this).closest('.block-item').remove();
});

// Условные поля
function updateConditionalFields() {
    $('.field-group[data-show-if]').each(function () {
        const cond = $(this).data('show-if');
        let show = true;
        for (const field in cond) {
            const val = $(`[name$="[${field}]"]`).val();
            if (val !== cond[field]) show = false;
        }
        $(this).toggle(show);
    });
}
$(document).on('change', 'select, input', updateConditionalFields);
$(document).ready(updateConditionalFields);

// Repeater
function bindRepeaterLogic() {
    $('.repeater').each(function () {
        const repeater = $(this);
        const name = repeater.data('name');
        const template = repeater.find('.repeater-item').first().clone();

        repeater.find('.add-repeater-item').off().on('click', function () {
            const index = repeater.find('.repeater-item').length;
            const newItem = template.clone();

            newItem.find('input, select, textarea').each(function () {
                const oldName = $(this).attr('name');
                const newName = oldName.replace(/\[\d+]/g, `[${index}]`);
                $(this).attr('name', newName).val('');
            });

            newItem.appendTo(repeater.find('.add-repeater-item').parent());
        });

        repeater.on('click', '.remove-repeater-item', function () {
            $(this).closest('.repeater-item').remove();
        });
    });
}
$(document).ready(bindRepeaterLogic);

// Загрузка изображений
$(document).on('change', '.upload-image-input', function () {
    const wrapper = $(this).closest('.image-uploader');
    const input = wrapper.find('input[type=hidden]');
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
        }
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
