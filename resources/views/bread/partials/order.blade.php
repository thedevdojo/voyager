<ol class="dd-list">
    @foreach ($items as $result)
        @if(!empty($nestable) && $result->{$result->getParentField()} != $parentId)
            @continue
        @endif
        <li class="dd-item" data-id="{{ $result->getKey() }}">
            <div class="dd-handle" style="height:inherit">
                @if (isset($dataRow->details->view))
                    @include($dataRow->details->view, ['row' => $dataRow, 'dataType' => $dataType,
                        'dataTypeContent' => $result, 'content' => $result->{$display_column}, 'action' => 'order'])
                @elseif($dataRow->type == 'image')
                    <span>
                        <img src="@if( !filter_var($result->{$display_column}, FILTER_VALIDATE_URL))
                            {{ Voyager::image( $result->{$display_column} ) }}
                            @else{{ $result->{$display_column} }}@endif" style="height: 100px">
                    </span>
                @else
                    <span>{{ $result->{$display_column} }}</span>
                @endif
            </div>
            @if(!empty($nestable) && !$result->nestableChildren->isEmpty())
                @include('voyager::bread.partials.order', ['items' => $result->nestableChildren,
                    'parentId' => $result->getKey()])
            @endif
        </li>
    @endforeach
</ol>
