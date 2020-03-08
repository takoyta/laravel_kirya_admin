@php($id = 'field__' . $field->name)


@if($value)
    <div class="mb-2">
        <img src="{!! $value !!}" alt="" class="img-fluid rounded" style="max-width: 400px;">
    </div>

    @if($field->nullable)

        <div class="mb-3 custom-control custom-checkbox">
            <input
                    type="checkbox"
                    name="{!! $field->name !!}"
                    value="unlink"
                    class="custom-control-input"
                    id="{!! $id !!}_delete"
            />
            <label for="{!! $id !!}_delete" class="custom-control-label text-secondary">{!! __('Delete') !!}</label>
        </div>
    @endif
@endif

@if(! $field->disabled)
    <div>
        <input
                type="file"
                accept="{!! $field->accept !!}"
                name="{!! $field->name !!}"
                class="d-none"
                id="{!! $id !!}"
        />
        <label for="{!! $id !!}" class="panel-button">{!! __('Browse') !!}</label>
        <span class="pt-1">{!! __('No file selected') !!}</span>
    </div>
@endif


@push('scripts')
    <script>
        $(function () {
            var id = '{!! $id !!}';

            function resolveFileSize(size) {
                var suffix = ['TB', 'GB', 'MB', 'KB', 'B']

                while (size > 1024) {
                    size = size / 1024;
                    suffix.pop();
                }

                return size.toFixed(1) + ' ' + suffix.pop();
            }

            /**
             * Change text in label on "filename [size suffix]"
             * */
            $('#' + id).change(function (e) {
                var file = e.target.files[0];

                $('label[for=' + id + '] + span').text(
                    file.name + ' [' + resolveFileSize(file.size) + ']'
                );
            })
        });
    </script>
@endpush
