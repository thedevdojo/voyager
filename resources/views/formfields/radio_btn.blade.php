@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
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
                               value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'checked' }}@endif @if($selected_value == $key){{ 'checked' }}@endif>
                        <label for="option-{{ str_slug($row->field, '-') }}-{{ str_slug($key, '-') }}">{{ $option }}</label>
                        <div class="check"></div>
                    </li>
                @endforeach
            @endif
        </ul>
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        <span>{{ $dataTypeContent->{$row->field} }}</span>
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        <p>{{ $dataTypeContent->{$row->field} }}</p>
    @overwrite
@endif

@if ($action == 'edit')
    @section("formfield_edit")
        @yield("formfield_edit_add")
    @overwrite
@endif

@if ($action == 'add')
    @section("formfield_add")
        @yield("formfield_edit_add")
    @overwrite
@endif

@yield("formfield_{$action}")
