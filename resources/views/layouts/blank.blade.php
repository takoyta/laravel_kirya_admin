<!DOCTYPE HTML>
<html>
<head>
    <title>@yield('title', __('Home')) | {!! config('app.name', 'Admin Laravel') !!}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{!! csrf_token() !!}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- Styles -->
    @stack('stylesheets')
</head>
<body>
@yield('body')

@stack('scripts')
</body>
</html>