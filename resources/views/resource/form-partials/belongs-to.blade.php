@php($id = 'field__' . $field->name)
@php($resource = $field->relatedResource)


<select
        name="{!! $field->name !!}"
        id="{!! $id !!}"
        @if ($field->disabled) disabled="disabled" @endif
>
    @if ($value)
        <option value="{!! $value !!}" selected>{!! $resource->title($value) !!}</option>
    @endif
</select>


@push('scripts')
    <script>
        $(function () {
            var id = '{!! $id !!}';

            var $select = $('#' + id);

            $select.select2({
                width: null,
                allowClear: @json($field->getAllowClear()),
                ajax: {
                    url: '{!! $ajax_search_url !!}',
                    delay: 250,
                    minimumInputLength: 2,
                    dataType: 'json',
                    data: function (params) {
                        /** query url data */
                        return { term: params.term, page: params.page };
                    },
                },
                placeholder: '{!! $resource->actionLabel('Select') !!}'
            });
        });
    </script>
@endpush