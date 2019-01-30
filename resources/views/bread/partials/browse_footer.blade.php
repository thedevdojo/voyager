@if ($isServerSide)
    <div class="pull-left">
        <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
            'voyager::generic.showing_entries', $dataTypeContent->total(), [
                'from' => $dataTypeContent->firstItem(),
                'to' => $dataTypeContent->lastItem(),
                'all' => $dataTypeContent->total()
            ]) }}</div>
    </div>
    <div class="pull-right">
        {{ $dataTypeContent->appends([
            's' => $search->value,
            'filter' => $search->filter,
            'key' => $search->key,
            'order_by' => $orderBy,
            'sort_order' => $sortOrder
        ])->links() }}
    </div>
@endif