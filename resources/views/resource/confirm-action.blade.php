@extends('admin::layouts.app')

@section('title', __($title))

@section('content')
    <div class="panel">
        <div class="panel-item">

            <form method="POST" id="form">
                @csrf
                <div>
                    <span>{!! __('You are sure?') !!}</span>

                    <button class="panel-button">{!! __('Confirm') !!}</button>

                    <a class="panel-button" href="{{ $backUrl }}">{!! __('Cancel') !!}</a>
                </div>
            </form>

        </div>
    </div>
@endsection
