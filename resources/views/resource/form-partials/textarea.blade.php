<textarea
        name="{!! $field->name !!}"
        placeholder="{!! $field->title !!}"
        class="panel-control"
        rows="{!! $field->rows !!}"
        @if ($field->disabled) disabled="disabled" @endif
>{!! $value !!}</textarea>
