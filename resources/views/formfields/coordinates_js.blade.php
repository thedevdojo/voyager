<style>
    .map {
        height: 400px;
        width: 100%;
    }
</style>
<script type="application/javascript">
    function initMap() {
        var map = center = marker = [];
        @foreach(getCoordinates($dataTypeContent) as $column => $point)
            @if($point)
                center['{{$column}}'] = {lat: {{ $point['lat'] }}, lng: {{ $point['lng'] }}};
            @else
                center['{{$column}}'] = {lat: {{ config('voyager.googlemaps.center.lat') }}, lng: {{ config('voyager.googlemaps.center.lng') }}};
            @endif
            map['{{$column}}'] = new google.maps.Map(document.getElementById('map_{{$column}}'), {
                zoom: {{ config('voyager.googlemaps.zoom') }},
                center: center['{{$column}}']
            });
            marker['{{$column}}'] = new google.maps.Marker({
                position: map['{{$column}}'].getCenter(),
                map: map['{{$column}}'],
                draggable: {{ !empty($drag_marker) && $drag_marker ? 'true' : 'false' }}
            });
            marker['{{$column}}'].addListener('dragend',function() {
                document.getElementById('{{ $column.'_lat' }}').value = this.position.lat();
                document.getElementById('{{ $column.'_lng' }}').value = this.position.lng();
            });
        @endforeach
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('voyager.googlemaps.key') }}&callback=initMap"></script>