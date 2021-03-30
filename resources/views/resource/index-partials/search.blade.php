{{-- USING ONLY AS COMPONENT --}}



<div class="d-flex">
    {!! $filterProvider->searchField->displayForm($filterProvider->virtualModel) !!}

    <div class="pr-1"></div>

    <div>
        <button type="submit" class="panel-button  text-nowrap"><i class="fa fa-search"></i></button>
    </div>
</div>
