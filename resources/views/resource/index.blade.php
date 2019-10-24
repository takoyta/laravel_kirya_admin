@extends('admin::layouts.app')

@section('title', __($resource->label() . ' List'))


@section('content')

    @component('admin::resource.index-partials.list', compact('fields', 'filterProvider', 'actions', 'paginator'))
    @endcomponent

@endsection
