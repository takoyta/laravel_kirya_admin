@prepend('stylesheets')
    @foreach(app('admin.asset')->getAssets('stylesheets') as $href)
        <link href="{!! $href !!}" rel="stylesheet">
    @endforeach
@endprepend


@prepend('scripts')
    @foreach(app('admin.asset')->getAssets('scripts') as $src)
        <script src="{!! $src !!}"></script>
    @endforeach
@endprepend
