<h3 class="panel-title">{!! __($panel->title) !!}</h3>

<div class="panel">
    @foreach($panel->fields as $field)
        <div class="panel-item">
            <div class="row">
                <div class="col-md-4 pt-1">
                    <span class="text-secondary">{!! $field->title !!}</span>
                </div>

                <div class="col-md-8">
                    {!! $field->displayForm($object) !!}

                    @if ($errors->has($field->name))
                        <div class="mt-1 js-form-error">
                            <small class="text-danger">{!! $errors->first($field->name) !!}</small>
                        </div>
                    @endif

                    @if($field->help)
                        <div class="mt-1">
                            <small class="text-secondary">{!! $field->help !!}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
