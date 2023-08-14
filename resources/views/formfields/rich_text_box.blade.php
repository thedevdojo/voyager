<textarea class="form-control richTextBox" name="{{ $row->field }}" id="richtext{{ $row->field }}">
    {{ old($row->field, $dataTypeContent->{$row->field} ?? '') }}
</textarea>

@push('javascript')
    {{-- <script src="{{gereric::voyager_assets('image_manager.min.js')}}"></script> --}}
    <script>
        $(document).ready(function() {
            //     var additionalConfig = {
            //         selector: 'textarea.richTextBox[name="{{ $row->field }}"]',
            //     }

            //     $.extend(additionalConfig, {!! json_encode($options->tinymceOptions ?? (object) []) !!})

            //     tinymce.init(window.voyagerTinyMCE.getConfig(additionalConfig));


            new FroalaEditor('#richtext{{ $row->field }}', {
                // Set a preloader.
                // imageManagerPreloader: "/images/loader.gif",

                // Set page size.
                imageManagerPageSize: 20,

                // Set a scroll offset (value in pixels).
                imageManagerScrollOffset: 10,

                // Set the load images request URL.
                imageManagerLoadURL: "{{ route('images') }}",
                imageManagerLoadMethod: "GET",
                imageManagerDeleteURL: '{{ route('image.delete', ['image' => '__image__']) }}',
                imageManagerDeleteMethod: 'DELETE',
                imageManagerDeleteParams: {
                    _token: "{{ csrf_token() }}"
                },
                events: {
                    'imageManager.error': function(error, response) {
                        console.log('Image Manager Error:');
                        console.log('Error:', error);
                        console.log('Response:', response);
                    },


                    'imageManager.beforeDeleteImage': function($img) {
                        // Do something before deleting an image from the image manager.
                        alert('Image will be deleted.');
                        console.log($img.attr('src'));
                        // console.log($img.);

                    },

                    'imageManager.imageDeleted': function(image) {

                        console.log(image);

                    }
                }
            })
        });
    </script>
@endpush
