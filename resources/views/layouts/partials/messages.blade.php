@if (session()->has('errors'))
    <div class="alert alert-danger" role="alert">
        {!! __('Form has errors.') !!}
    </div>
@endif

@foreach ((array) session()->pull('success') as $message)
    <div class="alert alert-success" role="alert">
        {!! $message !!}
    </div>
@endforeach

@foreach ((array) session()->pull('error') as $message)
    <div class="alert alert-danger" role="alert">
        {!! $message !!}
    </div>
@endforeach
