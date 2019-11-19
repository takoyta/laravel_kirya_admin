<!-- Sidebar -->
<div class="sidebar-wrapper col col-250px px-0">
    <div class="sidebar-brand py-3 mb-3 px-4">
        <a href="{{ route('admin.index') }}" class="a">{!! config('app.name', 'Laravel') !!}</a>
    </div>

    @php($currentResourceUriKey = isset($resource) ? $resource->uriKey() : 'index')

    @foreach(app('admin')->menu() as $group => $resources)
        <div class="mb-3">
            <div class="sidebar-group mb-1 px-4">{!! __($group) !!}</div>

            @foreach($resources as $item)
                <div class="sidebar-resource mb-1 px-4" data-order="{!! $item['order'] !!}">
                    <a
                        href="{{ route('admin.list', $item['uriKey']) }}"
                        class="a @if($currentResourceUriKey === $item['uriKey']) text-success font-weight-bold @endif"
                    >{!! __($item['label']) !!}</a>
                </div>
            @endforeach
        </div>
    @endforeach
</div>