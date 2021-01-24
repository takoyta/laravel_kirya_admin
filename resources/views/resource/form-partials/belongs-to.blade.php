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

        @component('admin::resource.index-partials.filters', compact('filterProvider'))
        @endcomponent
    @endif
</div>



@push('scripts')
    <script>
        $(function () {
            const $select = $('#{!! $id !!}');
            const $context = $select.closest('.panel-item');
            const allowClear = @json($field->getAllowClear());
            const placeholder = @json($resource->actionLabel('Select'));
            const url = @json($ajaxSearchUrl);

            Select2AdminHelper.init($select, $context, allowClear, placeholder, url);
        });
    </script>
@endpush
