<select
        name="{!! $field->name . ($field->isMultiple ? '[]' : '') !!}"
        class="js-select2"
        @if ($field->isMultiple) multiple="multiple" @endif
        @if ($field->disabled) disabled="disabled" @endif
>
    @foreach($field->getOptions($object) as $option => $label)
        <option value="{!! $option !!}" @if(in_array($option, (array) $value)) selected="selected" @endif>{!! $label !!}</option>
    @endforeach
</select>
