@extends('admin::layouts.app')

@section('title', __($title))

@section('content')
<form method="POST" enctype="multipart/form-data" action="" id="form">
    @csrf
    <input type="hidden" name="_previous_url" value="{{ $previousUrl }}">

    @foreach($panels as $panel) {!! $panel->displayForm($virtualModel) !!} @endforeach

    <div class="panel">
        <div class="panel-item">
            <div class="row">
                <div class="col-md-4 pt-1">
                    <span class="text-secondary">{!! __('You are sure?') !!}</span>
                </div>

                <div class="col-md-8">
                    <button class="panel-button bg-danger">{!! __('Confirm') !!}</button>

                    <a class="panel-button" href="{{ $previousUrl }}">{!! __('Cancel') !!}</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
