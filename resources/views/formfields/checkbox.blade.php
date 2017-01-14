<br>
<?php
$options = json_decode($row->details);
$checked = (isset($dataTypeContent->{$row->field}) && old($row->field, $dataTypeContent->{$row->field}) == 1)
    ? true
    : old($row->field);
?>
<input type="checkbox" name="{{ $row->field }}" class="toggleswitch"
    @if($checked) checked @endif
    @if(isset($options->on) && isset($options->off))
        data-on="{{ $options->on }}"
        data-off="{{ $options->off }}"
    @endif
/>
