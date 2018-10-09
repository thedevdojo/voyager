<br>
<?php $checked = false; ?>
@if(isset($options->options))
    @foreach($options->options as $key => $label)
        @if(isset($dataTypeContent->{$row->field}) || old($row->field))
            <?php $checked = old($row->field, $dataTypeContent->{$row->field}); ?>
        @else
            <?php $checked = isset($options->checked) && $options->checked ? true : false; ?>
        @endif

        <input type="checkbox" name="{{ $row->field }}[{{$key}}]"
               {!! $checked ? 'checked="checked"' : '' !!} value="{{$key}}" />
        {{$label}}
    @endforeach
@endif
