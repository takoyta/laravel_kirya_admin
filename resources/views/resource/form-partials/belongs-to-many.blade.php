<h3 class="panel-title">{!! __($title) !!}</h3>


@php($id = 'attach-'.$relatedResource->uriKey())


@component('admin::resource.index-partials.list', compact('fields', 'filterProvider', 'paginator'))
    @if ($ajaxUrl)
        @slot('actionSlot')
            <a href="#{!! $id !!}" id="{!! $id !!}" class="panel-button">{!! $relatedResource->actionLabel('Attach') !!}</a>
        @endslot
    @endif
@endcomponent


@push('scripts')
    <script>
        $(function () {
            var ajaxUrl = '{!! $ajaxUrl !!}';

            var $attachBtn = $('#{!! $id !!}');

            $attachBtn.click(function () {
                $attachBtn.hide();
                
                $.get(ajaxUrl).then(function (data) {
                    var options = data.data;

                    var $options = $.map(options, function (text, value) {
                        return $('<option/>').attr('value', value).text(text);
                    });

                    $('<select>')
                        .append($('<option/>').text("{!! $relatedResource->actionLabel('Select') !!}..."))
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