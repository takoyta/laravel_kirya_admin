@extends('admin::layouts.blank')

@include('admin::layouts.partials.assets')

@section('body')

    <div class="d-flex min-vh-100">
        @include('admin::layouts.partials.sidebar')

        <div class="col px-0">
            @include('admin::layouts.partials.nav')

            <div class="content-wrapper mt-5 px-5">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="font-weight-normal mb-0"> @yield('title') </h1>

                    <div class="text-nowrap"> @yield('after-title') </div>
                </div>

                @include('admin::layouts.partials.messages')

                @yield('content')

                @include('admin::layouts.partials.copy')
            </div>

        </div>
    </div>

@endsection
