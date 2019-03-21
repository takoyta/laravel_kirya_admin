@php($id = 'field__' . $field->name)

<div class="custom-control custom-checkbox">
    <input type="hidden" name="{!! $field->name !!}" value="0">
    <input
            type="checkbox"
            name="{!! $field->name !!}"
            value="1"
            @if ($value) checked="checked" @endif
            class="custom-control-input"
            id="{!! $id !!}"
            @if ($field->disabled) disabled="disabled" @endif
    />
    <label for="{!! $id !!}" class="custom-control-label"></label>
</div>
