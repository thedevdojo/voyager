@section('css')

    @include('voyager::formfields.includes.tag_styles')

@stop
@section('javascript')

    @include('voyager::formfields.includes.tag_scripts')

@stop
@endsection
<input type="text" class="form-control" name="{{ $row->field }}" data-name="{{ $row->display_name }}"  data-role="tagsinput" @if($row->required == 1) required @endif placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}" value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif"> 