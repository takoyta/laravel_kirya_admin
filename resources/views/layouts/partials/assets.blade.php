@prepend('stylesheets')
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css" rel="stylesheet" />

    <link href="{!! asset('vendor/admin/select2.css') !!}" rel="stylesheet" />

    <link href="{!! asset('vendor/admin/stylesheets.css') !!}" rel="stylesheet" />
@endprepend


@prepend('scripts')

    <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <!-- $.datetimepicker -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

    <!-- include summernote css/js (require twitter bootsrap) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.js"></script>

    <script>
        $(function() {
            /** Here enable frontend plugins */

            $.ajaxSetup({
                beforeSend: function(xhr, type) {
                    if (! type.crossDomain) {
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
            });

            $('.js-select2').select2({width: null,  minimumResultsForSearch: Infinity});

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
    </script>
@endprepend
