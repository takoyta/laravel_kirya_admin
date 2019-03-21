@extends('admin::layouts.blank')

@include('admin::layouts.partials.assets')


@section('body')

    <div class="d-flex justify-content-center align-items-center min-vh-100">

        <div class="width-350px mb-5 mt-1">
            <h1 class="font-weight-normal mb-3">
                @yield('title')
            </h1>

            @yield('content')

            @include('admin::layouts.partials.copy')
        </div>

    </div>

@endsection
