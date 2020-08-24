@php($id = 'field__' . $field->name)

<input
        type="text"
        name="{!! $field->name !!}"
        value="{!! $value ? $value->format($field->format) : null !!}"
        placeholder="{!! $field->title !!}"
        class="panel-control"
        autocomplete="off"
        id="{!! $id !!}"
        @if ($field->disabled) disabled="disabled" @endif
/>


@push('scripts')
    <script>
        $(function () {
            var id = '{!! $id !!}';
            var format = '{!! $field->format !!}';
            var timepicker = '{!! $field->timepicker !!}';

            $('#' + id).datetimepicker({format: format, timepicker: timepicker});
        });
    </script>
@endpush
