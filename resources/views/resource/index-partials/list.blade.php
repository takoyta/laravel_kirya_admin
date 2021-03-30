{{-- USING ONLY AS COMPONENT --}}



@php($panelId = $filterProvider->prefixed('panel'))

@php($showSearchForm = ! empty($filterProvider->searchField))
@php($showFilterForm = ! empty($filterProvider->fields))
@php($showActions    = ! empty($actions))


<div class="panel p-0" id="{!! $panelId !!}">

    @if ($showSearchForm || $showFilterForm || $showActions)
        <div class="panel-header d-flex justify-content-between">
            <form action="" method="GET" class="d-flex js-filter-form">
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
                @php($id = $object->getkey())
                @php($checkboxId = 'checkbox_'.$filterProvider->prefixed($i))
                <tr>
                    <!--checkbox-->
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input js-check-index-resource" id="{!! $checkboxId !!}" data-id="{!! $id !!}">
                            <label class="custom-control-label" for="{!! $checkboxId !!}"></label>
                        </div>
                    </td>

                    @foreach($fields as $field)
                        <td>
                            {!! $field->displayValue($object) !!}
                        </td>
                    @endforeach

                </tr>
            @endforeach
        </tbody>
    </table>


    <div class="panel-footer pb-0 d-flex justify-content-between align-items-center">
        <div>{!! $paginator->links() !!}</div>
        <p>{!! __('Total: :total', ['total' => $paginator->total()]) !!} <span class="js-checked-counter"></span></p>
    </div>

</div>




@push('scripts')
    <script>
        $(function () {
            /**
             * This code has UI fixes - remove extra params from forms & reset forms.
             */
            const $panel = $('#{!! $panelId !!}');
            const $form = $panel.find('.js-filter-form');
            const $counter = $panel.find('.js-checked-counter');
            const $actionLinks = $panel.find('a[href*="/action/"]');

            const reloadWithData = function (data = []) {
                window.location =
                    window.location.origin
                    + window.location.pathname
                    + (data.length > 0 ? '?' : '')
                    + $.param(data);
            }

            $form.submit(function() {
                reloadWithData(
                    $form
                        .serializeArray()
                        .filter(function (field) {
                            return '' !== field.value && '0' !== field.value;
                        })
                );

                return false;
            });

            $form.find('.js-reset-btn').click(function () {
                reloadWithData();
            });

            const $checkboxes = $panel.find('.js-check-index-resource');

            $checkboxes.change(function () {
                const ids = $checkboxes
                    .filter(':checked')
                    .map(function (i, checkbox) { return checkbox.dataset.id; })
                    .toArray();

                if (ids.length > 0)
                    $actionLinks.attr('data-counter', ids.length);
                else
                    $actionLinks.removeAttr('data-counter');

                $actionLinks.each(function (i, link) {
                    if (! link.originalHref) link.originalHref = link.href;

                    if (ids.length > 0)
                        link.href = link.originalHref
                            + (link.originalHref.includes('?') ? '&' : '?')
                            + 'ids=' + ids.join(',');
                    else
                        link.href = link.originalHref;
                });
            });

        });
    </script>
@endpush