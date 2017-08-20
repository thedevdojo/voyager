<div class="col-md-12">
    <div class="panel panel-bordered">
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
                @foreach($dataTypeRows as $row)
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

                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}

                            @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                            @endforeach
                        </div>
                    @endif
                @endforeach

                <!-- Adding / Editing -->
            </div><!-- panel-body -->

            <div class="panel-footer">
                <button type="submit" class="btn btn-primary save">{{ __('voyager.generic.save') }}</button>
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