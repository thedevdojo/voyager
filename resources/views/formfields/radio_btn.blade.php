<?php $selected_value = (isset($dataTypeContent->{$row->field}) && !empty(old(
    $row->field,
                $dataTypeContent->{$row->field}
))) ? old(
                    $row->field,
        $dataTypeContent->{$row->field}
                ) : old($row->field); ?>
                                        <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : null; ?>
<ul class="radio">
    @if(isset($options->options))
        @foreach($options->options as $key => $option)
            <li>
                <input type="radio" id="option-{{ str_slug($row->field, '-') }}-{{ str_slug($key, '-') }}"
                       name="{{ $row->field }}"
                       data-name="{{ $row->display_name }}"
                       value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'checked' }}@endif @if($selected_value == $key){{ 'checked' }}@endif>
                <label for="option-{{ str_slug($row->field, '-') }}-{{ str_slug($key, '-') }}">{{ $option }}</label>
                <div class="check"></div>
            </li>
        @endforeach
    @endif
</ul>
