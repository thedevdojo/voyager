@extends('voyager::master')

@section('page_header')
    <div class="page-title">
        <i class="voyager-data"></i> @if(isset($dataType->id)){{ __('voyager.database.edit_bread_for_table', ['table' => @$dataType->name]) }}@elseif(isset($table)){{ __('voyager.database.create_bread_for_table', ['table' => $table]) }}@endif
    </div>
    @php
        $isModelTranslatable = (!isset($isModelTranslatable) || !isset($dataType)) ? false : $isModelTranslatable;
        if (isset($dataType->name)) {
            $table = $dataType->name;
        }
    @endphp
    @include('voyager::multilingual.language-selector')
@stop


@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <form action="@if(isset($dataType->id)){{ route('voyager.database.bread.update', $dataType->id) }}@else{{ route('voyager.database.bread.store') }}@endif"
                      method="POST" role="form">
                @if(isset($dataType->id))
                    <input type="hidden" value="{{ $dataType->id }}" name="id">
                    {{ method_field("PUT") }}
                @endif
                    <!-- CSRF TOKEN -->
                    {{ csrf_field() }}

                    <div class="panel panel-primary panel-bordered">

                        <div class="panel-heading">
                            <h3 class="panel-title">{{ ucfirst($table) }} {{ __('voyager.database.bread_info') }}</h3>
                        </div>

                        <div class="panel-body">
                            <div class="row clearfix">
                                <div class="col-md-6 form-group">
                                    <label for="name">{{ __('voyager.database.table_name') }}</label>
                                    <input type="text" class="form-control" readonly name="name" placeholder="{{ __('generic_name') }}"
                                           value="@if(isset($dataType->name)){{ $dataType->name }}@else{{ $table }}@endif">
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('voyager.database.display_name_singular') }}</label>
                                    @if($isModelTranslatable)
                                        @include('voyager::multilingual.input-hidden', [
                                            'isModelTranslatable' => true,
                                            '_field_name'         => 'display_name_singular',
                                            '_field_trans' => get_field_translations($dataType, 'display_name_singular')
                                        ])
                                    @endif
                                    <input type="text" class="form-control"
                                           name="display_name_singular"
                                           id="display_name_singular"
                                           placeholder="{{ __('voyager.database.display_name_singular') }}"
                                           value="@if(isset($dataType->display_name_singular)){{ $dataType->display_name_singular }}@else{{ $display_name }}@endif">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('voyager.database.display_name_plural') }}</label>
                                    @if($isModelTranslatable)
                                        @include('voyager::multilingual.input-hidden', [
                                            'isModelTranslatable' => true,
                                            '_field_name'         => 'display_name_plural',
                                            '_field_trans' => get_field_translations($dataType, 'display_name_plural')
                                        ])
                                    @endif
                                    <input type="text" class="form-control"
                                           name="display_name_plural"
                                           id="display_name_plural"
                                           placeholder="{{ __('voyager.database.display_name_plural') }}"
                                           value="@if(isset($dataType->display_name_plural)){{ $dataType->display_name_plural }}@else{{ $display_name_plural }}@endif">
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('voyager.database.url_slug') }}</label>
                                    <input type="text" class="form-control" name="slug" placeholder="{{ __('voyager.database.url_slug_ph') }}"
                                           value="@if(isset($dataType->slug)){{ $dataType->slug }}@else{{ $slug }}@endif">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('voyager.database.icon_hint') }} <a
                                                href="{{ voyager_asset('fonts/voyager/icons-reference.html') }}"
                                                target="_blank">{{ __('voyager.database.icon_hint2') }}</a></label>
                                    <input type="text" class="form-control" name="icon"
                                           placeholder="{{ __('voyager.database.icon_class') }}"
                                           value="@if(isset($dataType->icon)){{ $dataType->icon }}@endif">
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('voyager.database.model_name') }}</label>
                                    <span class="voyager-question"
                                        aria-hidden="true"
                                        data-toggle="tooltip"
                                        data-placement="right"
                                        title="{{ __('voyager.database.model_name_ph') }}"></span>
                                    <input type="text" class="form-control" name="model_name" placeholder="{{ __('voyager.database.model_class') }}"
                                           value="@if(isset($dataType->model_name)){{ $dataType->model_name }}@else{{ $model_name }}@endif">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('voyager.database.controller_name') }}</label>
                                    <span class="voyager-question"
                                        aria-hidden="true"
                                        data-toggle="tooltip"
                                        data-placement="right"
                                        title="{{ __('voyager.database.controller_name_hint') }}"></span>
                                    <input type="text" class="form-control" name="controller" placeholder="{{ __('voyager.database.controller_name') }}"
                                           value="@if(isset($dataType->controller)){{ $dataType->controller }}@endif">
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-md-6 form-group">
                                    <label for="generate_permissions">{{ __('voyager.database.generate_permissions') }}</label><br>
                                    <?php $checked = (isset($dataType->generate_permissions) && $dataType->generate_permissions == 1) ? true : (isset($generate_permissions) && $generate_permissions) ? true : false; ?>
                                    <input type="checkbox" name="generate_permissions" class="toggleswitch"
                                           @if($checked) checked @endif>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="server_side">{{ __('voyager.database.server_pagination') }}</label><br>
                                    <?php $checked = (isset($dataType->server_side) && $dataType->server_side == 1) ? true : (isset($server_side) && $server_side) ? true : false; ?>
                                    <input type="checkbox" name="server_side" class="toggleswitch"
                                           @if($checked) checked @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">{{ __('voyager.database.description') }}</label>
                                <textarea class="form-control" name="description"
                                          placeholder="{{ __('voyager.database.description') }}"
                                    >@if(isset($dataType->description)){{ $dataType->description }}@endif</textarea>
                            </div>
                        </div><!-- .panel-body -->
                    </div><!-- .panel -->

                    <div class="panel panel-primary panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ __('voyager.database.edit_rows', ['table' => $table]) }}:</h3>
                        </div>

                        <div class="panel-body">
                            <div class="row fake-table-hd">
                                <div class="col-xs-2">{{ __('voyager.database.field') }}</div>
                                <div class="col-xs-2">{{ __('voyager.database.visibility') }}</div>
                                <div class="col-xs-2">{{ __('voyager.database.input_type') }}</div>
                                <div class="col-xs-2">{{ __('voyager.database.display_name') }}</div>
                                <div class="col-xs-4">{{ __('voyager.database.optional_details') }}</div>
                            </div>

                            <div id="bread-items">
                            @php
                                $r_order = 0;
                            @endphp
                            @foreach($fieldOptions as $data)
                                @php
                                    $r_order += 1;
                                @endphp

                                @if(isset($dataType->id))
                                    <?php $dataRow = TCG\Voyager\Models\DataRow::where('data_type_id', '=',
                                            $dataType->id)->where('field', '=', $data['field'])->first(); ?>
                                @endif

                                <div class="row row-dd">
                                    <div class="col-xs-2">
                                        <h4><strong>{{ $data['field'] }}</strong></h4>
                                        <strong>{{ __('voyager.database.type') }}:</strong> <span>{{ $data['type'] }}</span><br/>
                                        <strong>{{ __('voyager.database.key') }}:</strong> <span>{{ $data['key'] }}</span><br/>
                                        <strong>{{ __('voyager.generic.required') }}:</strong>
                                        @if($data['null'] == "NO")
                                            <span>{{ __('voyager.generic.yes') }}</span>
                                            <input type="hidden" value="1" name="field_required_{{ $data['field'] }}"
                                                   checked="checked">
                                        @else
                                            <span>{{ __('voyager.generic.no') }}</span>
                                            <input type="hidden" value="0" name="field_required_{{ $data['field'] }}">
                                        @endif
                                        <div class="handler voyager-handle"></div>
                                        <input class="row_order" type="hidden" value="{{ $r_order }}" name="field_order_{{ $data['field'] }}">
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="checkbox"
                                               id="field_browse_{{ $data['field'] }}"
                                               name="field_browse_{{ $data['field'] }}"
                                               @if(isset($dataRow->browse) && $dataRow->browse)
                                                    {{ 'checked="checked"' }}
                                               @elseif($data['key'] == 'PRI')
                                               @elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')
                                               @elseif(!isset($dataRow->browse))
                                                    {{ 'checked="checked"' }}
                                               @endif>
                                        <label for="field_browse_{{ $data['field'] }}">{{ __('voyager.generic.browse') }}</label><br/>
                                        <input type="checkbox"
                                               id="field_read_{{ $data['field'] }}"
                                               name="field_read_{{ $data['field'] }}" @if(isset($dataRow->read) && $dataRow->read){{ 'checked="checked"' }}@elseif($data['key'] == 'PRI')@elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')@elseif(!isset($dataRow->read)){{ 'checked="checked"' }}@endif>
                                        <label for="field_read_{{ $data['field'] }}">{{ __('voyager.generic.read') }}</label><br/>
                                        <input type="checkbox"
                                               id="field_edit_{{ $data['field'] }}"
                                               name="field_edit_{{ $data['field'] }}" @if(isset($dataRow->edit) && $dataRow->edit){{ 'checked="checked"' }}@elseif($data['key'] == 'PRI')@elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')@elseif(!isset($dataRow->edit)){{ 'checked="checked"' }}@endif>
                                        <label for="field_edit_{{ $data['field'] }}">{{ __('voyager.generic.edit') }}</label><br/>
                                        <input type="checkbox"
                                               id="field_add_{{ $data['field'] }}"
                                               name="field_add_{{ $data['field'] }}" @if(isset($dataRow->add) && $dataRow->add){{ 'checked="checked"' }}@elseif($data['key'] == 'PRI')@elseif($data['type'] == 'timestamp' && $data['field'] == 'created_at')@elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')@elseif(!isset($dataRow->add)){{ 'checked="checked"' }}@endif>
                                            <label for="field_add_{{ $data['field'] }}">{{ __('voyager.generic.add') }}</label><br/>
                                        <input type="checkbox"
                                               id="field_delete_{{ $data['field'] }}"
                                               name="field_delete_{{ $data['field'] }}" @if(isset($dataRow->delete) && $dataRow->delete){{ 'checked="checked"' }}@elseif($data['key'] == 'PRI')@elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')@elseif(!isset($dataRow->delete)){{ 'checked="checked"' }}@endif>
                                                <label for="field_delete_{{ $data['field'] }}">{{ __('voyager.generic.add') }}</label><br/>
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="hidden" name="field_{{ $data['field'] }}" value="{{ $data['field'] }}">
                                        @if($data['type'] == 'timestamp')
                                            <p>{{ __('generic_timestamp') }}</p>
                                            <input type="hidden" value="timestamp"
                                                   name="field_input_type_{{ $data['field'] }}">
                                        @else
                                            <select name="field_input_type_{{ $data['field'] }}">
                                                @foreach (Voyager::formFields() as $formField)
                                                    <option value="{{ $formField->getCodename() }}" @if(isset($dataRow->type) && $dataRow->type == $formField->getCodename()){{ 'selected' }}@endif>
                                                        {{ $formField->getName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="text" class="form-control"
                                               value="@if(isset($dataRow->display_name)){{ $dataRow->display_name }}@else{{ ucwords(str_replace('_', ' ', $data['field'])) }}@endif"
                                               name="field_display_name_{{ $data['field'] }}">
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="alert alert-danger validation-error">
                                            {{ __('voyager.json.invalid') }}
                                        </div>
                                        <textarea id="json-input-{{ $data['field'] }}" class="resizable-editor" data-editor="json" name="field_details_{{ $data['field'] }}">@if(isset($dataRow->details)){{ $dataRow->details }}@endif</textarea>
                                    </div>
                                </div>

                            @endforeach
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">{{ __('voyager.generic.submit') }}</button>
                            </div>
                        </div><!-- .panel-body -->
                    </div><!-- .panel -->

                </form>
            </div><!-- .col-md-12 -->
        </div><!-- .row -->
    </div><!-- .page-content -->



@stop

@section('javascript')
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>

    <script>
        window.invalidEditors = [];
        var validationAlerts = $('.validation-error');
        validationAlerts.hide();
        $(function () {
            @if ($isModelTranslatable)
                /**
                 * Multilingual setup
                 */
                $('.side-body').multilingual({
                    "form":    'form',
                    "editing": true
                });
            @endif

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
                // editDiv = $('<div>', {
                //     position: 'absolute',
                //     width: 250,
                //     resize: 'vertical',
                //     class: textarea.attr('class')
                // }).insertBefore(textarea),
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
                editor.setOption("maxLines", 30);
                editor.setOption("minLines", 4);
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
