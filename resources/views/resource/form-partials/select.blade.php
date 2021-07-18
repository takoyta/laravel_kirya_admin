<select
        name="{!! $field->name !!}"
        class="js-select2"
        @if ($field->disabled) disabled="disabled" @endif
>
    @foreach($field->getOptions($object) as $option => $label)
        <option value="{!! $option !!}" @if($option == $value) selected="selected" @endif>{!! $label !!}</option>
    @endforeach
</select>
