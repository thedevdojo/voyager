<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>

<div id="coordinates-formfield">
    <coordinates
        inline-template
        ref="coordinates"
        api-key="{{ config('voyager.googlemaps.key') }}"
        :points='@json($dataTypeContent->getCoordinates() && count($dataTypeContent->getCoordinates()) ? $dataTypeContent->getCoordinates() : [[ 'lat' => config('voyager.googlemaps.center.lat'), 'lng' => config('voyager.googlemaps.center.lng') ]])'
        :zoom={{ config('voyager.googlemaps.zoom') }}
    >
        <div>
            <div class="form-group">
                <div class="col-md-5">
                    <label class="control-label">Find by Place</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="742 Evergreen Terrace"
                        id="places-autocomplete"
                        v-on:keypress="onPlaceKeypress($event)"
                    />
                 </div>
                <div class="col-md-2">
                    <label class="control-label">Latitude</label>
                    <input
                        class="form-control"
                        type="number"
                        step="any"
                        name="{{ $row->field }}[lat]"
                        placeholder="19.6400"
                        v-model="lat"
                        @change="onLatLngInputChange"
                    />
                </div>
                <div class="col-md-2">
                    <label class="control-label">Longitude</label>
                    <input
                        class="form-control"
                        type="number"
                        step="any"
                        name="{{ $row->field }}[lng]"
                        placeholder="-155.9969"
                        v-model="lng"
                        @change="onLatLngInputChange"
                    />
                </div>

                <div class="clearfix"></div>
            </div>

            <div id="map"></div>
        </div>
    </coordinates>
</div>

@push('javascript')
    <script>
        Vue.component('coordinates', {
            props: {
                apiKey: {
                    type: String,
                    required: true,
                },
                points: {
                    type: Array,
                    required: true,
                },
                zoom: {
                    type: Number,
                    required: true,
                }
            },
            data() {
                return {
                    autocomplete: null,
                    lat: '',
                    lng: '',
                    map: null,
                    marker: null,
                };
            },
            mounted() {
                // Load Google Maps script
                let gMapScript = document.createElement('script');
                gMapScript.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?key='+this.apiKey+'&callback=gMapVm.$refs.coordinates.initMap&libraries=places');
                document.head.appendChild(gMapScript);
            },
            methods: {
                initMap: function() {
                    var vm = this;

                    var center = vm.points[vm.points.length - 1];

                    // Set initial LatLng
                    this.setLatLng(center.lat, center.lng);

                    // Create map
                    vm.map = new google.maps.Map(document.getElementById('map'), {
                        zoom: vm.zoom,
                        center: new google.maps.LatLng(center.lat, center.lng)
                    });

                    // Create marker
                    vm.marker = new google.maps.Marker({
                        position: new google.maps.LatLng(center.lat, center.lng),
                        map: vm.map,
                        draggable: true
                    });

                    // Listen to map drag events
                    google.maps.event.addListener(vm.marker, 'drag', vm.onMapDrag);

                    // Setup places Autocomplete
                    vm.autocomplete = new google.maps.places.Autocomplete(document.getElementById('places-autocomplete'));
                    places = new google.maps.places.PlacesService(vm.map);
                    vm.autocomplete.addListener('place_changed', vm.onPlaceChange);
                },

                setLatLng: function(lat, lng) {
                    this.lat = lat;
                    this.lng = lng;
                },

                moveMapAndMarker: function(lat, lng) {
                    this.marker.setPosition(new google.maps.LatLng(lat, lng));
                    this.map.panTo(new google.maps.LatLng(lat, lng));
                },

                onMapDrag: function(event) {
                    this.setLatLng(event.latLng.lat(), event.latLng.lng());
                },

                onPlaceKeypress: function(event) {
                    if (event.which === 13) {
                        event.preventDefault()
                    }
                },

                onPlaceChange: function() {
                    var place = this.autocomplete.getPlace();
                    if (place.geometry) {
                        this.setLatLng(place.geometry.location.lat(), place.geometry.location.lng());
                        this.moveMapAndMarker(place.geometry.location.lat(), place.geometry.location.lng());
                    }
                },

                onLatLngInputChange: function(event) {
                    this.moveMapAndMarker(this.lat, this.lng);
                },
            }
        });

        var gMapVm = new Vue({ el: '#coordinates-formfield' });
    </script>
@endpush
