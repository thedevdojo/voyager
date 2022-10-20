/*--------------------
|
| TinyMCE default config
|
--------------------*/

var getConfig = function(options) {

    var baseTinymceConfig = {
        menubar: false,
        selector: 'textarea.richTextBox',
        base_url: $('meta[name="assets-path"]').attr('content')+'?path=js/',
        skin: 'oxide',
        min_height: 600,
        resize: true,
        plugins: 'link image code table lists',
        extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
        relative_urls: false, // Necessary so uploaded images don't get a relative path but an URL instead.
        remove_script_host: false,
        file_picker_types: 'image',
        file_picker_callback: (callback, value, meta) => {
            if (meta.filetype == 'image') {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function () {
                    var formdata = new FormData();
                    formdata.append('image', this.files[0]);
                    formdata.append('type_slug', $('#upload_type_slug').val());
                    // Show loader
                    $('#voyager-loader').css('z-index', 10000);
                    $('#voyager-loader').fadeIn();
                    $.ajax({
                        type: 'post',
                        url: $('#upload_url').val(),
                        data: formdata,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        cache: false,
                    })
                    .done((result) => {
                        callback(result);
                    })
                    .always(() => {
                        $('#voyager-loader').fadeOut();
                        $('#voyager-loader').css('z-index', 99);
                    });
                }

                input.click();
            }
        },
        toolbar: 'styleselect | bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | link image table | code',
        image_caption: true,
        image_title: true,
        init_instance_callback: function (editor) {
            if (typeof tinymce_init_callback !== "undefined") {
                tinymce_init_callback(editor);
            }
        },
        setup: function (editor) {
            if (typeof tinymce_setup_callback !== "undefined") {
                tinymce_setup_callback(editor);
            }
        }
    };

    return $.extend({}, baseTinymceConfig, options);
}

exports.getConfig = getConfig;
