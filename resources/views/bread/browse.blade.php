@extends('voyager::app')

@section('content')
<bread-browse
    :bread="{{ json_encode($bread) }}"
    :layout="{{ json_encode($layout) }}"
    data-url="{{ route('voyager.'.$bread->slug.'.index') }}"
></bread-browse>
@endsection
