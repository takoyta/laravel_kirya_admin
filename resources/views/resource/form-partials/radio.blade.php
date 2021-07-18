@foreach($field->getOptions($object) as $option => $title)

    @php($id = $field->name . '__' . $option)

    <div class="custom-control custom-radio mb-2">
        <input
                type="radio"
                name="{!! $field->name !!}"
                value="{!! $option !!}"
                @if ($option == $value) checked="checked" @endif
                id="{!! $id !!}"
                class="custom-control-input"
                @if ($field->disabled) disabled="disabled" @endif
        />
        <label for="{!! $id !!}" class="custom-control-label">{!! $title !!}</label>
    </div>

@endforeach
