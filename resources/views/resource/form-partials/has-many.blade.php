<h3 class="panel-title">{!! __($title) !!}</h3>


@component('admin::resource.index-partials.list', compact('fields', 'filterProvider', 'paginator'))
    @slot('actionSlot')
        {!! $addAction->display($object) !!}
    @endslot
@endcomponent
