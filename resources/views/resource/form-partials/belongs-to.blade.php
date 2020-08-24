@php($id = 'field__' . $field->name)
@php($resource = $field->relatedResource)

<div class="d-flex">
    <select
        name="{!! $field->name !!}"
        id="{!! $id !!}"
        @if ($field->disabled) disabled="disabled" @endif
    >
        @if ($value)
            <option value="{!! $value !!}" selected>{!! $resource->title($value) !!}</option>
        @endif
    </select>

    @if(! $field->disabled && count($filterProvider->fields) > 0)
        <div class="pr-1"></div>

        <div class="dropdown ml-1">

            <button type="reset" class="panel-button item-heading text-nowrap dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span id="applied__{!! $id !!}"></span>
                <i class="fa fa-filter"></i>
            </button>

            <div class="dropdown-menu width-350px p-0">

                @foreach($filterProvider->fields as $filterField)
                    <div class="p-2 mt-1 item-heading">
                        {!! $filterField->title !!}
                    </div>
                    <div class="p-2">
                        {!! $filterField->formInputView($filterProvider->virtualModel) !!}
                    </div>
                @endforeach

                <button type="button" class="my-1 item-heading w-100 border-0 py-1 js-reset-btn">{!! __('Reset filters') !!}</button>
            </div>

        </div>
    @endif
</div>



@push('scripts')
    <script>
        $(function () {
            const id = '{!! $id !!}';
            const url = '{!! $ajaxSearchUrl !!}';

            const $select = $('#' + id);
            const $fields = $select.parent().find('.dropdown-menu').find('input, select');
            const $applied = $('#applied__' + id);

            const init = function(params = []) {
                $select.select2({
                    width: null,
                    allowClear: @json($field->getAllowClear()),
                    ajax: {
                        url: url + '?' + $.param(params),
                        delay: 250,
                        minimumInputLength: 2,
                        dataType: 'json',
                        data: function (params) {
                            /** query url data */
                            return {search: params.term, page: params.page};
                        },
                    },
                    placeholder: '{!! $resource->actionLabel('Select') !!}'
                });
            };

            const filled = function () {
                return $fields.filter(function(o, field) {
                    return field.type !== 'hidden'
                        && field.value !== ''
                        && ('checkbox' !== field.type || field.checked);
                })
            };

            init();

            // For prevent submiting field move them names into dataset
            $fields.each(function (i, field) {
                field.dataset.name = field.name;
                field.name = '';
            });

            // On change any field
            $fields.change(function () {
                $applied.text(filled().length || '');
                init(filled().map(function (i, field) {
                    return {name: field.dataset.name, value: field.value};
                }));
            });

            // On reset (Set all empty string and trigger update at first input)
            $select.parent().find('.js-reset-btn').click(function () {
                filled().filter('[type=checkbox]').prop('checked', false);
                filled().val('');
                $fields.trigger('change');
            })
        });
    </script>
@endpush
