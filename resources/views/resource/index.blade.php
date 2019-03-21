@extends('admin::layouts.app')

@section('title', __($resource->label() . ' List'))


@section('content')

    @component('admin::resource.index-partials.list', compact('fields', 'filterProvider', 'paginator'))
        @slot('actionSlot')
            @foreach($actions as $action)
                {!! $action->display() !!}
                <div class="pr-1"></div>
            @endforeach

            {!! $resource->makeActionLink('create')->display() !!}
        @endslot
    @endcomponent

@endsection
