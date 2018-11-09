@if ($action == 'browse')

    @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
    <div class="readmore">{{ mb_strlen( $dataTypeContent->{$row->field} ) > 200 ? mb_substr($dataTypeContent->{$row->field}, 0, 200) . ' ...' : $dataTypeContent->{$row->field} }}</div>

@else

    <textarea @if($row->required == 1) required @endif class="form-control" name="{{ $row->field }}" rows="{{ isset($options->display->rows) ? $options->display->rows : 5 }}">@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</textarea>

@endif
