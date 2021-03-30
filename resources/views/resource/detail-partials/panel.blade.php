<h3 class="panel-title">{!! $panel->title !!}</h3>

<div class="panel">
    @foreach($panel->fields as $field)
        <div class="panel-item">
            <div class="row">
                <div class="col-md-4">
                    <span class="text-secondary">{!! $field->title !!}</span>
                </div>

                <div class="col-md-8">
                    {!! $field->displayValue($object) !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
