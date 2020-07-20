<h3 class="panel-title">{!! $fieldTitle !!}</h3>

<div class="panel px-0 py-1">
    @empty ($tableBody)
        <span>{{ __($noData) }}</span>
    @else()
        <table class="table table-bordered mb-0 {!! implode(' ', $tableClasses) !!}">
            <thead>
            <tr>
                @foreach($tableTitles as $tableTitle)
                    <th scope="col">{!! $tableTitle !!}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($tableBody as $tableRow)
                <tr>
                    @foreach($tableRow as $tableCell)
                        <td>{!! $tableCell !!}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
