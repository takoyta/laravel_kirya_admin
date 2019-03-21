@extends('admin::layouts.app')

@section('title', __('Error'))

@section('content')

    <div class="alert alert-danger">
        {!! __('Whoops! Something went wrong.') !!}
    </div>

@endsection
