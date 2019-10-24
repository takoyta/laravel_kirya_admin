<h3 class="panel-title">{!! __($title) !!}</h3>


@component('admin::resource.index-partials.list', compact('fields', 'filterProvider', 'actions', 'paginator'))
@endcomponent


@push('scripts')
    <script>
        $(function () {
            var $attachBtn = $('.js-attach-related');

            var ajaxUrl = $attachBtn.attr('href');
            $attachBtn.attr('href', '#')

            $attachBtn.click(function () {
                $attachBtn.hide();

                $.get(ajaxUrl).then(function (data) {
                    var options = data.data;

                    var $options = $.map(options, function (text, value) {
                        return $('<option/>').attr('value', value).text(text);
                    });

                    $('<select>')
                        .append($('<option/>').text($attachBtn.attr('title') + '...'))
                        .append($options)
                        .change(function () {
                            var value = $(this).val();

                            $.post(ajaxUrl, {related_id: value}).then(function () {
                                window.location.reload();
                            });
                        })
                        .insertAfter($attachBtn)
                        .select2({width: null})
                        .select2('open');
                });
                return false;
            });
        });
    </script>
@endpush