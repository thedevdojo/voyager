@include('voyager::multilingual.input-hidden-bread-browse')
@php
    $action = new \TCG\Voyager\Actions\ViewAction($dataType, $data);

    $shouldLink = $action->shouldActionDisplayOnDataType();
    $shouldLink = $shouldLink ? Auth::user()->can($action->getPolicy(), $data) : false;
@endphp

@if ($shouldLink)
    <a href="{{ $action->getRoute($dataType->name) }}" title="{{ $action->getTitle() }}">
        {{ $data->{$row->field} }}
    </a>
@else
    <span>{{ $data->{$row->field} }}</span>
@endif