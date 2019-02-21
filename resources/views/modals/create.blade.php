<div class="modal fade create-{{ $dataType->slug }}-{{ $rand }}" id="modal-create-{{ $dataType->slug }}" style="display: none;">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add {{ $dataType->slug }}</h4>
            </div>

            <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            id="form-create-{{ $dataType->slug }}"
                            action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if(!is_null($dataTypeContent->getKey()))
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

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{(!is_null($dataTypeContent->getKey()) ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = isset($row->details->display) ? $row->details->display : NULL;
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{isset($row->details->legend->align) ? $row->details->legend->align : 'center'}}" style="background-color: {{isset($row->details->legend->bgcolor) ? $row->details->legend->bgcolor : '#f0f0f0'}};padding: 5px;">{{$row->details->legend->text}}</legend>
                                @endif
                                @if (isset($row->details->formfields_custom))
                                    @include('voyager::formfields.custom.' . $row->details->formfields_custom)
                                @else
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ isset($display_options->width) ? $display_options->width : 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        {{ $row->slugify }}
                                        <label for="name">{{ $row->display_name }}</label>
                                        @include('voyager::multilingual.input-hidden-bread-edit-add')
                                        @if($row->type == 'relationship')
                                            @include('voyager::formfields.relationship', ['options' => $row->details])
                                        @else
                                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                        @endif

                                        @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                        {!! Form::button('<span class="fa fa-save"></span> &nbsp;' . __('voyager::generic.save'), ['type' => 'button', 'id' =>'button-create-'.$dataType->slug, 'class' => 'btn btn-success button-submit', 'data-loading-text' => trans('general.loading')]) !!}
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

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }}').modal('show');
        $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} #currency_code").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
        });
    });
    $(document).on('click', '.create-{{ $dataType->slug }}-{{ $rand }} #button-create-{{ $dataType->slug }}', function (e) {
        $('.create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} .modal-header').before('<span id="span-loading" style="position: absolute; height: 100%; width: 100%; z-index: 99; background: #6da252; opacity: 0.4;"><i class="fa fa-spinner fa-spin" style="font-size: 16em !important;margin-left: 35%;margin-top: 8%;"></i></span>');
        $.ajax({
            url: '{{ url("admin/".$dataType->slug) }}',
            type: 'POST',
            dataType: 'JSON',
            data: $(".create-{{ $dataType->slug }}-{{ $rand }} #form-create-{{ $dataType->slug }}").serialize(),
            beforeSend: function () {
                $('.create-{{ $dataType->slug }}-{{ $rand }} #button-create-{{ $dataType->slug }}').button('loading');
                $(".create-{{ $dataType->slug }}-{{ $rand }} .form-group").removeClass("has-error");
                $(".create-{{ $dataType->slug }}-{{ $rand }} .help-block").remove();
            },
            complete: function() {
                $('.create-{{ $dataType->slug }}-{{ $rand }} #button-create-{{ $dataType->slug }}').button('reset');
            },
            success: function(json) {
                var data = json['data'];
                $('.create-{{ $dataType->slug }}-{{ $rand }} #span-loading').remove();
                $('.create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }}').modal('hide');
                $('#name').append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                $('#name').trigger('change');
                $('#name').select2('refresh');
                @if ($dataType->slug)
                $('{{ $dataType->slug }}').append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                $('{{ $dataType->slug }}').trigger('change');
                $('{{ $dataType->slug }}').select2('refresh');
                @endif
            },
            error: function(error, textStatus, errorThrown) {
                $('.create-{{ $dataType->slug }}-{{ $rand }} #span-loading').remove();
                if (error.responseJSON.name) {
                    $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} input[name='name']").parent().parent().addClass('has-error');
                    $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} input[name='name']").parent().after('<p class="help-block">' + error.responseJSON.name + '</p>');
                }
                if (error.responseJSON.email) {
                    $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} input[name='email']").parent().parent().addClass('has-error');
                    $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} input[name='email']").parent().after('<p class="help-block">' + error.responseJSON.email + '</p>');
                }
                if (error.responseJSON.currency_code) {
                    $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} select[name='currency_code']").parent().parent().addClass('has-error');
                    $(".create-{{ $dataType->slug }}-{{ $rand }}#modal-create-{{ $dataType->slug }} select[name='currency_code']").parent().after('<p class="help-block">' + error.responseJSON.currency_code + '</p>');
                }
            }
        });
    });
</script>