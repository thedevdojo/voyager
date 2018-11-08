@php $images = json_decode($data->{$row->field}); @endphp
@if($images)
    @php $images = array_slice($images, 0, 3); @endphp
    @foreach($images as $image)
        <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
    @endforeach
@endif