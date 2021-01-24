$(function () {
    /** Here enable frontend plugins */

    $.ajaxSetup({
        beforeSend: function (xhr, type) {
            if (!type.crossDomain) {
                xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
            }
        },
    });

    $('.js-select2').select2({width: null, minimumResultsForSearch: Infinity});

    $('.js-summernote').summernote({
        fonts: false,
        height: 300,
        styleTags: ['h2', 'h3', 'p', 'blockquote'],
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']], // fixme: picture insert as bas64 encoded
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
    });

    /** Prevent close dropdown on click inside */
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });

    Select2AdminHelper = {
        init: function ($select, $context, allowClear, placeholder, url) {
            const $fields = $context.find('.dropdown').find('input, select');
            const $applied = $context.find('.js-filter-applied');

            // For avoid conficts with main form add prefix
            const FIELD_PREFIX = '$__';
            $fields.each(function (i, field) {
                field.name = FIELD_PREFIX + field.name;
            });

            // On change
            $fields.change(function () {
                let params = Select2AdminHelper
                    .filterChanged($fields)
                    .map(function (i, field) {
                        return {name: field.name.replace(FIELD_PREFIX, ''), value: field.value};
                    });

                $applied.text(params.length || '');
                Select2AdminHelper.initSelect2($select, allowClear, placeholder, url, params);
            });

            // On reset
            $context.find('.js-reset-btn').click(function () {
                $fields.filter('input[type=checkbox], input[type=radio]').prop('checked', false);
                $fields.filter('select, input[type=input], input[type=date]').val('');

                $fields.trigger('change');
            });

            // Remove Apply button(apply on change)
            $context.find('.js-apply-btn').remove();

            Select2AdminHelper.initSelect2($select, allowClear, placeholder, url, []);
        },

        initSelect2: function ($select, allowClear, placeholder, url, queryParams) {
            $select.select2({
                width: null,
                allowClear: allowClear,
                ajax: {
                    url: url + '?' + $.param(queryParams),
                    delay: 250,
                    minimumInputLength: 2,
                    dataType: 'json',
                    data: function (params) {
                        /** query url data */
                        return {search: params.term, page: params.page};
                    },
                },
                placeholder: placeholder
            });
        },

        filterChanged: function ($fields) {
            return $fields.filter(function (o, field) {
                switch (field.tagName + '_' + field.type) {
                    case 'SELECT_select-one':
                        return field.value !== ''; // here is selected option value
                    case 'INPUT_radio':
                    case 'INPUT_checkbox':
                        return field.value !== '' && field.checked;
                    case 'INPUT_date':
                    case 'INPUT_input':
                        return field.value.trim() !== '';
                    case 'INPUT_hidden':
                    default:
                        return false;
                }
            });
        },
    };
});
