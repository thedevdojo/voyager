<div class="form-group">
    <label for="name">{{ __('translation.details') }}</label>

    <textarea name="{{ $row->field }}" id="{{ $row->field }}_textarea" data-editor="json">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</textarea>
</div>

@section('javascript')
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>

    <script>
        window.invalidEditors = [];
        //var validationAlerts = $('.validation-error');
        //validationAlerts.hide();
        $(function () {

            /**
             * Reorder items
             */
            $('#bread-items').sortable({
                handle: '.handler',
                update: function (e, ui) {
                    var _rows = $('#bread-items').find('.row_order'),
                        _size = _rows.length;

                    for (var i = 0; i < _size; i++) {
                        $(_rows[i]).val(i+1);
                    }
                }
            });

            $('#bread-items').disableSelection();

            $('[data-toggle="tooltip"]').tooltip();

            $('.toggleswitch').bootstrapToggle();

            $('textarea[data-editor]').each(function () {
                var textarea = $(this),
                mode = textarea.data('editor'),

                editDiv = $('<div>').insertBefore(textarea),
                editor = ace.edit(editDiv[0]),
                _session = editor.getSession(),
                valid = false;
                textarea.hide();

                // Validate JSON
                _session.on("changeAnnotation", function(){
                    valid = _session.getAnnotations().length ? false : true;
                    if (!valid) {
                        if (window.invalidEditors.indexOf(textarea.attr('id')) < 0) {
                            window.invalidEditors.push(textarea.attr('id'));
                        }
                    } else {
                        for(var i = window.invalidEditors.length - 1; i >= 0; i--) {
                            if(window.invalidEditors[i] == textarea.attr('id')) {
                               window.invalidEditors.splice(i, 1);
                            }
                        }
                    }
                });

                // Use workers only when needed
                editor.on('focus', function () {
                    _session.setUseWorker(true);
                });
                editor.on('blur', function () {
                    if (valid) {
                        textarea.siblings('.validation-error').hide();
                        _session.setUseWorker(false);
                    } else {
                        textarea.siblings('.validation-error').show();
                    }
                });

                _session.setUseWorker(false);

                editor.setAutoScrollEditorIntoView(true);
                editor.$blockScrolling = Infinity;
                editor.setOption("maxLines", 100);
                editor.setOption("minLines", 40);
                editor.setOption("showLineNumbers", false);
                editor.setTheme("ace/theme/github");
                _session.setMode("ace/mode/json");
                if (textarea.val()) {
                    _session.setValue(JSON.stringify(JSON.parse(textarea.val()), null, 4));
                }

                _session.setMode("ace/mode/" + mode);

                // copy back to textarea on form submit...
                textarea.closest('form').on('submit', function (ev) {
                    if (window.invalidEditors.length) {
                        ev.preventDefault();
                        ev.stopPropagation();
                        validationAlerts.hide();
                        for (var i = window.invalidEditors.length - 1; i >= 0; i--) {
                            $('#'+window.invalidEditors[i]).siblings('.validation-error').show();
                        }
                        toastr.error('{{ __('voyager.json.invalid_message') }}', '{{ __('voyager.json.validation_errors') }}', {"preventDuplicates": true, "preventOpenDuplicates": true});
                    } else {
                        if (_session.getValue()) {
                            // uglify JSON object and update textarea for submit purposes
                            textarea.val(JSON.stringify(JSON.parse(_session.getValue())));
                        }
                    }
                });
            });
        });
    </script>
@stop
