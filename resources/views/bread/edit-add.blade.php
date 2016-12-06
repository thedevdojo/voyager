@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> @if(isset($dataTypeContent->id)){{ 'Edit' }}@else{{ 'New' }}@endif {{ $dataType->display_name_singular }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">

                    <div class="panel-heading">
                        <h3 class="panel-title">@if(isset($dataTypeContent->id)){{ 'Edit' }}@else{{ 'Add New' }}@endif {{ $dataType->display_name_singular }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form"
                          action="@if(isset($dataTypeContent->id)){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->id) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                          method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if(isset($dataTypeContent->id))
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- If we are editing -->
                            @if(isset($dataTypeContent->id))
                                <?php $dataTypeRows = $dataType->editRows; ?>
                            @else
                                <?php $dataTypeRows = $dataType->addRows; ?>
                            @endif

                            @foreach($dataTypeRows as $row)
                                <div class="form-group">
                                    <label for="name">{{ $row->display_name }}</label>

                                    @if($row->type == "text")
                                        <input type="text" class="form-control" name="{{ $row->field }}"
                                               placeholder="{{ $row->display_name }}"
                                               value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
                                    @elseif($row->type == "password")
                                        @if(isset($dataTypeContent->{$row->field}))
                                            <br>
                                            <small>Leave empty to keep the same</small>
                                        @endif
                                        <input type="password" class="form-control" name="{{ $row->field }}" value="">
                                    @elseif($row->type == "text_area")
                                        <textarea class="form-control"
                                                  name="{{ $row->field }}">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif</textarea>
                                    @elseif($row->type == "rich_text_box")
                                        <textarea class="form-control richTextBox"
                                                  name="{{ $row->field }}">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif</textarea>
                                    @elseif($row->type == "image" || $row->type == "file")
                                        @if($row->type == "image" && isset($dataTypeContent->{$row->field}))
                                            <img src="{{ Voyager::image( $dataTypeContent->{$row->field} ) }}"
                                                 style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;">
                                        @elseif($row->type == "file" && isset($dataTypeContent->{$row->field}))
                                            <div class="fileType">{{ $dataTypeContent->{$row->field} }}</div>
                                        @endif
                                        <input type="file" name="{{ $row->field }}">
                                    @elseif($row->type == "select_dropdown")
                                        <?php $options = json_decode($row->details); ?>
                                        @if(isset($options->relationship))
                                            {{-- If this is a relationship and the method does not exist, show a warning message --}}
                                            @if( !method_exists( $dataType->model_name, $row->field ) )
                                                <p class="label label-warning"><i class="voyager-warning"></i> Make sure to setup the appropriate relationship in the {{ $row->field . '()' }} method of the {{ $dataType->model_name }} class.</p>   
                                            @endif

                                            @if( method_exists( $dataType->model_name, $row->field ) )
                                                <?php $selected_value = (isset($dataTypeContent->{$row->field}) && !empty(old($row->field, $dataTypeContent->{$row->field}))) ? old($row->field, $dataTypeContent->{$row->field}) : old($row->field); ?>
                                                <select class="form-control select2" name="{{ $row->field }}">
                                                    <?php $relationshipClass = get_class(app($dataType->model_name)->{$row->field}()->getRelated()); ?>
                                                    <?php $relationshipOptions = $relationshipClass::all(); ?>
                                                    @foreach($relationshipOptions as $relationshipOption)
                                                        <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if($selected_value == $relationshipOption->{$options->relationship->key}){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="form-control select2" name="{{ $row->field }}"></select>
                                            @endif
                                        @else
                                            <?php $selected_value = (isset($dataTypeContent->{$row->field}) && !empty(old($row->field, $dataTypeContent->{$row->field}))) ? old($row->field, $dataTypeContent->{$row->field}) : old($row->field); ?>
                                            <select class="form-control select2" name="{{ $row->field }}">
                                                <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : NULL; ?>
                                                @if(isset($options->options))
                                                    @foreach($options->options as $key => $option)
                                                        <option value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'selected="selected"' }}@endif @if($selected_value == $key){{ 'selected="selected"' }}@endif>{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @endif

                                    @elseif($row->type == "select_multiple")
                                        <?php $options = json_decode($row->details); ?>
                                        {{-- If this is a relationship and the method does not exist, show a warning message --}}
                                        @if(isset($options->relationship) && !method_exists( $dataType->model_name, $row->field ) )
                                            <p class="label label-warning"><i class="voyager-warning"></i> Make sure to setup the appropriate relationship in the {{ $row->field . '()' }} method of the {{ $dataType->model_name }} class.</p>   
                                        @endif
                                        
                                        <select class="form-control select2" name="{{ $row->field }}[]" multiple>
                                            @if(isset($options->relationship))
                                                <!-- Check that the method relationship exists -->
                                                @if( method_exists( $dataType->model_name, $row->field ) )
                                                    <?php $selected_values = isset($dataTypeContent) ? $dataTypeContent->{$row->field}()->getRelated()->pluck($options->relationship->key)->all() : array(); ?>
                                                    <?php $relationshipClass = get_class(app($dataType->model_name)->{$row->field}()->getRelated()); ?>
                                                    <?php $relationshipOptions = $relationshipClass::all(); ?>
                                                    @foreach($relationshipOptions as $relationshipOption)
                                                        <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if(in_array($relationshipOption->{$options->relationship->key}, $selected_values)){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </select>

                                    @elseif($row->type == "radio_btn")
                                        <?php $options = json_decode($row->details); ?>
                                        <?php $selected_value = (isset($dataTypeContent->{$row->field}) && !empty(old($row->field,
                                                        $dataTypeContent->{$row->field}))) ? old($row->field,
                                                $dataTypeContent->{$row->field}) : old($row->field); ?>
                                        <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : NULL; ?>
                                        <ul class="radio">
                                            @if(isset($options->options))
                                                @foreach($options->options as $key => $option)
                                                    <li>
                                                        <input type="radio" id="option-{{ $key }}"
                                                               name="{{ $row->field }}"
                                                               value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'checked' }}@endif @if($selected_value == $key){{ 'checked' }}@endif>
                                                        <label for="option-{{ $key }}">{{ $option }}</label>
                                                        <div class="check"></div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>

                                    @elseif($row->type == "checkbox")

                                        <br>
                                        <?php $options = json_decode($row->details); ?>
                                        <?php $checked = (isset($dataTypeContent->{$row->field}) && old($row->field,
                                                        $dataTypeContent->{$row->field}) == 1) ? true : old($row->field); ?>
                                        @if(isset($options->on) && isset($options->off))
                                            <input type="checkbox" name="{{ $row->field }}" class="toggleswitch"
                                                   data-on="{{ $options->on }}" @if($checked) checked
                                                   @endif data-off="{{ $options->off }}">
                                        @else
                                            <input type="checkbox" name="{{ $row->field }}" class="toggleswitch"
                                                   @if($checked) checked @endif>
                                        @endif

                                    @endif

                                </div>
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                          enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                               onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();
        });
    </script>
    <script src="{{ config('voyager.assets_path') }}/lib/js/tinymce/tinymce.min.js"></script>
    <script src="{{ config('voyager.assets_path') }}/js/voyager_tinymce.js"></script>
@stop
