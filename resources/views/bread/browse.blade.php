@extends('voyager::app')

@section('content')
<bread-browse
    :bread="{{ json_encode($bread) }}"
    :accessors="{{ json_encode($bread->getComputedProperties()) }}"
    :layout="{{ json_encode($layout) }}"
    :actions="{{ $actions }}"
    :translatable="{{ json_encode($bread->getTranslatableFields()) }}"
    url="{{ Str::finish(route('voyager.'.$bread->slug.'.index'), '/') }}"
></bread-browse>
@endsection
