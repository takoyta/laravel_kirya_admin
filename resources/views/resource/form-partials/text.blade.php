<input
        type="text"
        name="{!! $field->name !!}"
        value="{!! $value !!}"
        placeholder="{!! $field->title !!}"
        class="panel-control"
        @if ($field->disabled) disabled="disabled" @endif
/>
