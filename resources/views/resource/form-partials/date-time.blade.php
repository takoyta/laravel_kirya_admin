@php($id = 'field__' . $field->name)

<input
        type="text"
        name="{!! $field->name !!}"
        value="{!! $value instanceof \DateTimeInterface ? $value->format($field->format) : $value !!}"
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
