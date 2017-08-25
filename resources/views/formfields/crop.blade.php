@php 
    $id = 'input_' . $row->field;

    if( strpos($dataTypeContent->{$row->field}, 'http://') === false && strpos($dataTypeContent->{$row->field}, 'https://') === false) {
        $image = Voyager::image( $dataTypeContent->{$row->field} );
    } else {
        $image = $dataTypeContent->{$row->field};
    }
@endphp

<input id="{{ $id }}" type="hidden" name="{{ $row->field }}"
    value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif">

@foreach($options->crop as $photoParams)
    <input type="hidden" name="{{ $row->field . '_' . $photoParams->name }}"
       value="{{ old($row->field . '_' . $photoParams->name ) }}">
@endforeach

<button type="button" class="btn btn-success" id="upload-{{ $id }}">
    <i class="voyager-upload"></i> {{ __('voyager.generic.upload') }}
</button>

@if($image)
    <a type="button" class="btn btn-primary" id="download-{{ $id }}" href="{{ $image }}" download="{{ $image }}" target="_blank">
        <i class="voyager-download"></i> {{ __('voyager.generic.download') }}
    </a>

    <button type="button" class="btn btn-warning" id="edit-{{ $id }}" >
        <i class="voyager-edit"></i> {{ __('voyager.generic.edit') }}
    </button>
@endif
<div id="uploadPreview" style="display:none;"></div>

<div id="dropzone-{{ $id }}" class="dropzone-block disabled">
    @foreach($options->crop as $photoParams)
        <div class="foto-send">
            <div class="photo-block {{ $photoParams->name }}">
                <div class="cropMain"></div>
                <div class="cropSlider"></div>
            </div>
        </div>
    @endforeach
</div>

<style>
    @foreach($options->crop as $photoParams)
        .{{ $photoParams->name }} .cropMain {
            width: {{ $photoParams->size->width / 2 }}px;
            height: {{ $photoParams->size->height / 2 }}px;
        }
        .{{ $photoParams->name }} .cropSlider {
            width: {{ $photoParams->size->width / 2 }}px;
        }
    @endforeach
</style>

@section('javascript')
    @parent
    <script>
    $(document).ready(function() {
        @foreach($options->crop as $photoParams)
            var {{ $photoParams->name }} = new Image.crop();
            {{ $photoParams->name }}.init(".{{ $photoParams->name }}");

            @if ( old($row->field) )
                {{ $photoParams->name }}.loadImg("{{ storage_url(old($row->field)) }}?{{ time() }}");
            @elseif ( isset($dataTypeContent->{$row->field}) )
                {{ $photoParams->name }}.loadImg("{{ $dataTypeContent->getCroppedPhoto($photoParams->name, $photoParams->size->name) }}?{{ time() }}");
            @endif
        @endforeach
    });

    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $("#upload-{{ $id }}, #dropzone-{{ $id }}").dropzone({
        url: "{{ route('voyager.media.upload') }}",
        previewsContainer: "#uploadPreview",

        sending: function(file, xhr, formData) {
            formData.append("_token", CSRF_TOKEN);
            formData.append("upload_path", "{{ $dataType->slug.'/'.date('F').date('Y') }}");
        },
        success: function(e, res){
            if (res.success){
                uploadImage(res.path)
                toastr.success(res.message, "Sweet Success!");
            } else {
                toastr.error(res.message, "Whoopsie!");
            }
        },
        error: function(e, res, xhr){
            toastr.error(res, "Whoopsie");
        }
    });

    $('#edit-{{ $id }}').click(function(){
        uploadImage("{{ $dataTypeContent->{$row->field} }}")
    });

    function uploadImage(imagePath){
        $("#dropzone-{{ $id }}").find(".crop-container").remove();
        $("#dropzone-{{ $id }}").find(".noUi-base").remove();

        $('#{{ $id }}').val(imagePath);
        $('.dropzone-block').removeClass('disabled');

        @foreach($options->crop as $photoParams)
            var {{ $photoParams->name }} = new Image.crop();
            {{ $photoParams->name }}.init(".{{ $photoParams->name }}");
            {{ $photoParams->name }}.loadImg("/{{ basename(storage_url('')) }}/" + imagePath);

            $("button:submit").click(function() {
                $('input[name={{ $row->field.'_'.$photoParams->name }}]').val(
                    JSON.stringify(coordinates({{ $photoParams->name }}))
                );
            })
        @endforeach
    }
    </script>
@endsection