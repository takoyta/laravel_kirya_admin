@extends('admin::layouts.app')


@section('title', __($resource->label()) . ' ' . $resource->title($object))


@section('after-title')
    @foreach($actions as $action) {!! $action->display($object) !!} @endforeach
@endsection


@section('content')
    @foreach($panels as $panel) {!! $panel->display($resource, $object) !!} @endforeach
@endsection
