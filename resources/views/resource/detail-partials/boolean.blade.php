@if(null === $value)
    &nbsp;&nbsp;
@else
    <span class="boolean-circle @if($value) boolean-circle--true @else boolean-circle--false @endif"></span>
@endif

<span>{!! $label !!}</span>
