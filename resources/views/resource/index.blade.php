@extends('admin::layouts.app')

@section('title', $pageTitle)


@section('content')

    @component('admin::resource.index-partials.list', compact('fields', 'filterProvider', 'actions', 'paginator'))
    @endcomponent

@endsection
