{{-- USING ONLY AS COMPONENT --}}



@php($formId = $filterProvider->prefixed('form'))

@php($showSearchForm = ! empty($filterProvider->searchField))
@php($showFilterForm = ! empty($filterProvider->fields))
@php($showActions    = ! empty($actions))


<div class="panel p-0">

    @if ($showSearchForm || $showFilterForm || $showActions)
        <div class="panel-header d-flex justify-content-between">
            <form action="" method="GET" class="d-flex" id="{!! $formId !!}">
                @if ($showFilterForm)
                    @component('admin::resource.index-partials.filters', compact('filterProvider'))
                    @endcomponent

                    <div class="pr-1"></div>
                @endif

                @if ($showSearchForm)
                    @component('admin::resource.index-partials.search', compact('filterProvider'))
                    @endcomponent
                @endif
            </form>

            <div class="d-flex">
                @foreach($actions as $action)
                    <div class="pr-1"></div>
                    {!! $action->display() !!}
                @endforeach
            </div>
        </div>
    @endif


    <table class="table mb-0">
        <thead>
        <tr>
            <th scope="col"><!--checkbox--></th>

            @foreach($fields as $field)
                <th>
                    <span>{!! $field->title !!}</span>

                    @if($field->sortable)
                        <span class="d-inline-flex flex-column align-middle pl-1">
                            <a href="{{ $paginator->orderUrl($field, 'asc') }}" class="sort-arrow sort-arrow--asc @if($paginator->isOrderedBy($field, 'asc')) active @endif"></a>

                            <a href="{{ $paginator->orderUrl($field, 'desc') }}" class="sort-arrow sort-arrow--desc @if($paginator->isOrderedBy($field, 'desc')) active @endif"></a>
                        </span>
                    @endif
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
            @foreach($paginator->items() as $i => $object)
                <tr>
                    <!--checkbox-->
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="resource-{!! $i !!}">
                            <label class="custom-control-label" for="resource-{!! $i !!}"></label>
                        </div>
                    </td>

                    @foreach($fields as $field)
                        <td>
                            {!! $field->display($object) !!}
                        </td>
                    @endforeach

                </tr>
            @endforeach
        </tbody>
    </table>


    <div class="panel-footer pb-0 d-flex justify-content-between align-items-center">
        <div>{!! $paginator->links() !!}</div>
        <p>{!! __('Total: :total', ['total' => $paginator->total()]) !!}</p>
    </div>

</div>




@push('scripts')
    <script>
        $(function () {
            /**
             * This code has UI fixes - remove extra params from forms & reset forms.
             */
            var $form = $('#{!! $formId !!}');

            $form.submit(function() {
                var data = $form
                    .serializeArray()
                    .filter(function (field) {
                        return '' !== field.value && '0' !== field.value;
                    });

                reloadWithData(data);

                return false;
            });

            $form.find('.js-reset-btn').click(function () {
                reloadWithData([]);
            });

            function reloadWithData(data) {
                window.location =
                    window.location.origin
                    + window.location.pathname
                    + (data.length > 0 ? '?' : '')
                    + $.param(data);
            }
        });
    </script>
@endpush