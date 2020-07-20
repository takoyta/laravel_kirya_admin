{{-- USING ONLY AS COMPONENT --}}



<div class="dropdown ml-1">

    <button type="reset" class="panel-button item-heading text-nowrap dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {!! $filterProvider->appliedFiltersCount ?: '' !!}
        <i class="fa fa-filter"></i>
    </button>

    <div class="dropdown-menu width-350px p-0">

        @foreach($filterProvider->fields as $field)
            <div class="p-2 mt-1 item-heading">
                {!! $field->title !!}
            </div>
            <div class="p-2">
                {!! $field->formInputView($filterProvider) !!}
            </div>
        @endforeach

        <button type="submit" class="mt-1 item-heading w-100 border-0 py-1">{!! __('Apply filters') !!}</button>

        <button type="button" class="my-1 item-heading w-100 border-0 py-1 js-reset-btn">{!! __('Reset filters') !!}</button>
    </div>

</div>
