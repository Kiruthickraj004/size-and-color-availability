jQuery(function ($) {

    function rgbToHex(rgb) {
        const parts = rgb.split(',').map(v => parseInt(v.trim(), 10));
        if (parts.length !== 3) return null;
        return '#' + parts.map(v => v.toString(16).padStart(2, '0')).join('');
    }

    function syncPicker(row, hex) {
        row.find('.color-picker').val(hex);
        row.find('.hex-input').val(hex);
    }

    $('#add-custom-color').on('click', function () {

        const i = $('#custom-colors-wrapper .custom-color-row').length;

        $('#custom-colors-wrapper').append(`
            <div class="custom-color-row">
                <input type="text" name="_custom_colors[${i}][name]" placeholder="Color name" />
                <input type="color" class="color-picker" value="#000000" />
                <input type="text" class="hex-input" name="_custom_colors[${i}][hex]" value="#000000" />
                <input type="text" class="rgb-input" placeholder="255,0,0" />
                <button type="button" class="button remove-color">×</button>
            </div>
        `);
    });

    $(document).on('input', '.color-picker', function () {
        const row = $(this).closest('.custom-color-row');
        syncPicker(row, $(this).val());
    });

    $(document).on('input', '.hex-input', function () {
        const hex = $(this).val();
        if (/^#[0-9a-f]{6}$/i.test(hex)) {
            syncPicker($(this).closest('.custom-color-row'), hex);
        }
    });

    $(document).on('input', '.rgb-input', function () {
        const hex = rgbToHex($(this).val());
        if (hex) {
            syncPicker($(this).closest('.custom-color-row'), hex);
        }
    });

    $(document).on('click', '.remove-color', function () {
        $(this).closest('.custom-color-row').remove();
    });
});
