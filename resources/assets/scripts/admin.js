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
});
