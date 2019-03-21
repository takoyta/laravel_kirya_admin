@extends('admin::layouts.app')


@if ($object->exists)
    @section('title', $resource->actionLabel('Edit') . ' ' . $resource->title($object))
@else
    @section('title', $resource->actionLabel('Create'))
@endif


@section('content')
    <form method="POST" enctype="multipart/form-data" action="" id="form">
        @csrf

        @php([$key, $value] = $retrivedAt)
        <input type="hidden" name="{!! $key !!}" value="{!! $value !!}">

        @foreach($panels as $panel) {!! $panel->displayForm($resource, $object) !!} @endforeach

        <div class="panel">
            <div class="panel-item">
                <div class="row">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="panel-button">{!! __($object->exists ? 'Update' : 'Create') !!}</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection


@if($message = $resource->getConfirmationMessage($object->exists ? 'update' : 'create'))
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
