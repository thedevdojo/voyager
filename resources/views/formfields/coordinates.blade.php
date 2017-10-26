<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>
@forelse($dataTypeContent->getCoordinates() as $point)
    <input type="hidden"  data-name="{{ $row->display_name }}" name="{{ $row->field }}[lat]" value="{{ $point['lat'] }}" id="lat"/>
    <input type="hidden"  data-name="{{ $row->display_name }}" name="{{ $row->field }}[lng]" value="{{ $point['lng'] }}" id="lng"/>
@empty
    <input type="hidden"  data-name="{{ $row->display_name }}" name="{{ $row->field }}[lat]" value="{{ config('voyager.googlemaps.center.lat') }}" id="lat"/>
    <input type="hidden"  data-name="{{ $row->display_name }}" name="{{ $row->field }}[lng]" value="{{ config('voyager.googlemaps.center.lng') }}" id="lng"/>
@endforelse

<script type="application/javascript">
    function initMap() {
        @forelse($dataTypeContent->getCoordinates() as $point)
            var center = {lat: {{ $point['lat'] }}, lng: {{ $point['lng'] }}};
        @empty
            var center = {lat: {{ config('voyager.googlemaps.center.lat') }}, lng: {{ config('voyager.googlemaps.center.lng') }}};
        @endforelse
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: {{ config('voyager.googlemaps.zoom') }},
            center: center
        });
        var markers = [];
        @forelse($dataTypeContent->getCoordinates() as $point)
            var marker = new google.maps.Marker({
                    position: {lat: {{ $point['lat'] }}, lng: {{ $point['lng'] }}},
                    map: map,
                    draggable: true
                });
            markers.push(marker);
        @empty
            var marker = new google.maps.Marker({
                    position: center,
                    map: map,
                    draggable: true
                });
        @endforelse

        google.maps.event.addListener(marker,'dragend',function(event) {
            document.getElementById('lat').value = this.position.lat();
            document.getElementById('lng').value = this.position.lng();
        });
    }
</script>
<div id="map"/>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('voyager.googlemaps.key') }}&callback=initMap"></script>
