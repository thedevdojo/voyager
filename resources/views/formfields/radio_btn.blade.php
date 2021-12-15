<?php $selected_value = old($row->field, $dataTypeContent->{$row->field} ?? $options->default ?? NULL) ?>
<ul class="radio">
    @if(isset($options->options))
        @foreach($options->options as $key => $option)
            <li>
                <input type="radio" id="option-{{ \Illuminate\Support\Str::slug($row->field, '-') }}-{{ \Illuminate\Support\Str::slug($key, '-') }}"
                       name="{{ $row->field }}"
                       value="{{ $key }}" @if($selected_value == $key) checked @endif>
                <label for="option-{{ \Illuminate\Support\Str::slug($row->field, '-') }}-{{ \Illuminate\Support\Str::slug($key, '-') }}">{{ $option }}</label>
                <div class="check"></div>
            </li>
        @endforeach
    @endif
</ul>
