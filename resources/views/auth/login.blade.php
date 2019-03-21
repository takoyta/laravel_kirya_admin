@extends('admin::layouts.auth')

@section('title', __('Login'))

@section('content')

    @include('admin::layouts.partials.messages')

    <div class="panel">
        <form action="" method="POST">
            @csrf

            <div class="panel-item">
                <label for="email" class="text-secondary">{!! __('E-Mail') !!}</label>
                <input type="email" name="email" id="email" class="panel-control w-100" placeholder="vasya-pupkin@example.com">

                @if($errors->has('email'))
                    <small class="text-danger">{!! $errors->first('email') !!}</small>
                @endif
            </div>

            <div class="panel-item">
                <label for="password" class="text-secondary">{!! __('Password') !!}</label>
                <input type="password" name="password" id="password" class="panel-control w-100" placeholder="*****">
            </div>

            <div class="panel-item">
                <button type="submit" class="panel-button w-100">{!! __('Enter') !!}</button>
            </div>
        </form>
    </div>

@endsection
