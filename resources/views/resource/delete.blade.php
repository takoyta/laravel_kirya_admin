@extends('admin::layouts.app')


@section('title', $resource->actionLabel('Delete') . ' ' . $resource->title($object))


@section('content')
    <div class="panel">
        <div class="panel-item">

            <form method="POST" id="form">
                @csrf
                <div>
                    <span>{!! __('You are sure?') !!}</span>
                    <button class="panel-button">{!! $resource->actionLabel('Yes, delete') !!}</button>

                    {!! $altAction->display($object) !!}
                </div>

            </form>

        </div>
    </div>
@endsection

@if($message = $resource->getConfirmationMessage('delete'))
    @push('scripts')
        <script>
            $(function () {
                $('#form').submit(function () {
                    return confirm('{!! $message !!}')
                })
            });
        </script>
    @endpush
@endif