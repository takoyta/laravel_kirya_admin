@php($fieldId = 'field__' . $field->name)
@php($types = $field->getTypes())


<select
        class="js-select2"
        id="{!! $fieldId !!}"
        @if ($field->disabled) disabled="disabled" @endif
>
    <option value="">—</option>

    @foreach($types as $type => $params)
        <option value="{!! $type !!}" @isset($value[$type]) selected @endif >{!! $params['label'] !!}</option>
    @endforeach
</select>


<div class="mt-3"></div>

<select
        id="{!! $fieldId !!}__id"
        class="js-select2"
        disabled
>
    @if($value)
        <option value="{!! $id = $value[$key = key($value)] !!}" selected>{!! app('admin.core')->resourceByKey($key)->title($id) !!}</option>
    @endif
</select>


@push('scripts')
    <script>
        $(function () {
            var fieldId = '{!! $fieldId !!}';

            var types = JSON.parse('@json($types)');

            var $type = $('#' + fieldId);
            var $id = $('#' + fieldId + '__id');

            var type = $type.val();
            if (type) {
                updateSelectId();
            }

            $type.change(function () {
                type = $type.val();

                $id.prop('disabled', true).empty();
                if (type) {
                    updateSelectId();
                    $id.select2('open');
                }
            });

            function updateSelectId() {
                var params = types[type];

                $id.attr('name', '{!! $field->name !!}[' + type + ']');

                $id.select2({
                    width: null,
                    allowClear: false,
                    ajax: {
                        url: params.ajaxSearchUrl,
                        delay: 250,
                        minimumInputLength: 2,
                        dataType: 'json',
                        data: function (params) {
                            /** query url data */
                            return { search: params.term, page: params.page };
                        },
                    },
                    placeholder: params.placeholder,
                });

                $id.prop('disabled', $type.prop('disabled')); // Enable select id if enabled type select
            }
        });
    </script>
@endpush
