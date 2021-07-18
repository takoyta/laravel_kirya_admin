@foreach($field->getOptions($object) as $option => $title)

    @php($id = $field->name . '__' . $option)

    <div class="custom-control {!! $field->isMultiple ? 'custom-checkbox' : 'custom-radio' !!} mb-2">
        <input
                type="{!! $field->isMultiple ? 'checkbox' : 'radio' !!}"
                name="{!! $field->name . ($field->isMultiple ? '[]' : '') !!}"
                value="{!! $option !!}"
                @if(in_array($option, (array) $value)) checked="checked" @endif
                id="{!! $id !!}"
                class="custom-control-input"
                @if ($field->disabled) disabled="disabled" @endif
        />
        <label for="{!! $id !!}" class="custom-control-label">{!! $title !!}</label>
    </div>

@endforeach
