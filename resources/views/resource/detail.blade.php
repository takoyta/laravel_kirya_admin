@extends('admin::layouts.app')


@section('title', $resource->labeledTitle($object))


@section('after-title')
    @foreach($actions as $action) {!! $action->display($object) !!} @endforeach
@endsection


@section('content')
    @foreach($panels as $panel) {!! $panel->displayValue($object) !!} @endforeach
@endsection
