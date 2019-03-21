<textarea
        name="{!! $field->name !!}"
        class="d-none js-summernote"
        @if ($field->disabled) disabled="disabled" @endif
>{!! $value !!}</textarea>
