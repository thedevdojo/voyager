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
                <input type="radio" id="option-{{ \Illuminate\Support\Str::slug($row->field, '-') }}-{{ \Illuminate\Support\Str::slug($key, '-') }}"
                       name="{{ $row->field }}"
                       value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'checked' }}@endif @if($selected_value == $key){{ 'checked' }}@endif>
                <label for="option-{{ \Illuminate\Support\Str::slug($row->field, '-') }}-{{ \Illuminate\Support\Str::slug($key, '-') }}">{{ $option }}</label>
                <div class="check"></div>
            </li>
        @endforeach
    @endif
</ul>
