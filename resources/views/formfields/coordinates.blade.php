@if($point)
    <input type="hidden" name="{{ $row->field }}[lat]" value="{{ $point['lat'] }}" id="{{ $row->field.'_lat' }}"/>
    <input type="hidden" name="{{ $row->field }}[lng]" value="{{ $point['lng'] }}" id="{{ $row->field.'_lng' }}"/>
@else
    <input type="hidden" name="{{ $row->field }}[lat]" value="{{ config('voyager.googlemaps.center.lat') }}" id="{{ $row->field.'_lat' }}"/>
    <input type="hidden" name="{{ $row->field }}[lng]" value="{{ config('voyager.googlemaps.center.lng') }}" id="{{ $row->field.'_lng' }}"/>
@endif

<div class="map" id="map_{{$row->field}}"></div>

