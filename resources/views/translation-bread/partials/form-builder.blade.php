@php
    $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};
    $formBuilderFields = collect([]);;
@endphp
<!-- form start -->
<form role="form"
        class="form-edit-add"
        action="@if(isset($dataTypeContent->id)){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->id) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
        method="POST" enctype="multipart/form-data">
    <!-- PUT Method if we are editing -->
    @if(isset($dataTypeContent->id))
        {{ method_field("PUT") }}
    @endif

    <!-- CSRF TOKEN -->
    {{ csrf_field() }}

    @foreach($formBuilder as $key => $htmlRow)
        <div class="{{ $htmlRow->class }}">
            @foreach($htmlRow->panels as $key_panel => $panel)
                <div class="{{ isset($panel->class) ? $panel->class : '' }}">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon wb-image"></i> {{ isset($panel->title) ? __($panel->title) : '' }}</h3>
                        <div class="panel-actions">
                            <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        @php
                            $formBuilderFields->push($panel->fields);
                        @endphp

                        @foreach($dataTypeRows->whereIn('field', $panel->fields) as $row)
                            <!-- GET THE DISPLAY OPTIONS -->
                            @php
                                $options = json_decode($row->details);
                                $display_options = isset($options->display) ? $options->display : NULL;
                            @endphp

                            @if ($options && isset($options->formfields_custom))
                                @include('voyager::formfields.custom.' . $options->formfields_custom)
                            @else
                                <div class="form-group @if($row->type == 'hidden') hidden @endif @if(isset($display_options->width)){{ 'col-md-' . $display_options->width }}@else{{ 'col-md-12' }}@endif" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label for="name">{{ $row->display_name }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                </div>
                            @endif
                        @endforeach

                        @php
                            $dataTypeRowsTranslation = $dataTypeTranslation->{(isset($dataTypeContentTranslation->id) ? 'editRows' : 'addRows' )};
                        @endphp

                        @foreach($dataTypeRowsTranslation->whereIn('field', $panel->fields) as $row)
                            <!-- GET THE DISPLAY OPTIONS -->
                            @php
                                $options = json_decode($row->details);
                                $display_options = isset($options->display) ? $options->display : NULL;
                            @endphp

                            @if ($options && isset($options->formfields_custom))
                                @include('voyager::formfields.custom.' . $options->formfields_custom)
                            @else
                                <div class="form-group @if($row->type == 'hidden') hidden @endif @if(isset($display_options->width)){{ 'col-md-' . $display_options->width }}@else{{ 'col-md-12' }}@endif" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label for="name">{{ $row->display_name }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    {!! app('voyager')->formField($row, $dataTypeTranslation, $dataTypeContentTranslation) !!}


                                    @foreach (app('voyager')->afterFormFields($row, $dataTypeTranslation, $dataTypeContentTranslation) as $after)
                                        {!! $after->handle($row, $dataTypeTranslation, $dataTypeContentTranslation) !!}
                                    @endforeach
                                </div>
                            @endif
                        @endforeach

                    </div><!-- panel-body -->

                    @if(!$key && count($htmlRow->panels) == $key_panel+1)
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-primary save">{{ __('voyager.generic.save') }}</button>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    @php
        $formBuilderFields = $formBuilderFields->collapse();

        $notFormBuilderFields = $dataTypeRows->filter(function ($value, $key) use ($formBuilderFields) {
            return !$formBuilderFields->contains($value->field);
        });
    @endphp

    @if(count($notFormBuilderFields))
        <!-- this fields  -->
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    @php
                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};
                    @endphp

                    @foreach($notFormBuilderFields as $row)
                        <!-- GET THE DISPLAY OPTIONS -->
                        @php
                            $options = json_decode($row->details);
                            $display_options = isset($options->display) ? $options->display : NULL;
                        @endphp

                        <!-- online code -->
                        @if ($options && isset($options->formfields_custom))
                            @include('voyager::formfields.custom.' . $options->formfields_custom)
                        @else
                            <div class="form-group @if($row->type == 'hidden') hidden @endif @if(isset($display_options->width)){{ 'col-md-' . $display_options->width }}@else{{ 'col-md-12' }}@endif" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                {{ $row->slugify }}
                                <label for="name">{{ $row->display_name }}</label>
                                @include('voyager::multilingual.input-hidden-bread-edit-add')
                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}

                                @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                    {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div><!-- panel-body -->
            </div>
        </div>
    @endif
</form>

<iframe id="form_target" name="form_target" style="display:none"></iframe>
<form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
        enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
    <input name="image" id="upload_file" type="file"
             onchange="$('#my_form').submit();this.value='';">
    <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
    {{ csrf_field() }}
</form>