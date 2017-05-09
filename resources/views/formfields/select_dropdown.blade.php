@if(isset($options->relationship))

    {{-- If this is a relationship and the method does not exist, show a warning message --}}
    @if( !method_exists( $dataType->model_name, camel_case($row->field) ) )
        <p class="label label-warning"><i class="voyager-warning"></i> Make sure to setup the appropriate relationship in the {{ camel_case($row->field) . '()' }} method of the {{ $dataType->model_name }} class.</p>
    @endif

    @if( method_exists( $dataType->model_name, camel_case($row->field) ) )
        @if(isset($dataTypeContent->{$row->field}) && !is_null(old($row->field, $dataTypeContent->{$row->field})))
            <?php $selected_value = old($row->field, $dataTypeContent->{$row->field}); ?>
        @else
            <?php $selected_value = old($row->field); ?>
        @endif

        <select class="form-control select2" name="{{ $row->field }}">
            <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : NULL; ?>

            @if(isset($options->options))
                <optgroup label="Custom">
                @foreach($options->options as $key => $option)
                    <option value="{{ ($key == '_empty_' ? '' : $key) }}" @if($default == $key && $selected_value === NULL){{ 'selected="selected"' }}@endif @if((string)$selected_value == (string)$key){{ 'selected="selected"' }}@endif>{{ $option }}</option>
                @endforeach
                </optgroup>
            @endif
            {{-- Populate all options from relationship --}}
            <?php
            $relationshipListMethod = camel_case($row->field) . 'List';
            if (method_exists($dataTypeContent, $relationshipListMethod)) {
                $relationshipOptions = $dataTypeContent->$relationshipListMethod();
            } else {
                $relationshipClass = $dataTypeContent->{camel_case($row->field)}()->getRelated();
                $relationshipOptions = $relationshipClass::all();
            }

            // Try to get default value for the relationship
            // when default is a callable function (ClassName@methodName)
            if ($default != NULL) {
                $comps = explode('@', $default);
                if (count($comps) == 2 && method_exists($comps[0], $comps[1])) {
                    $default = call_user_func([$comps[0], $comps[1]]);
                }
            }
            ?>

            <optgroup label="Relationship">
            @foreach($relationshipOptions as $relationshipOption)
                <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if($default == $relationshipOption->{$options->relationship->key} && $selected_value === NULL){{ 'selected="selected"' }}@endif @if($selected_value == $relationshipOption->{$options->relationship->key}){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
            @endforeach
            </optgroup>
        </select>
    @else
        <select class="form-control select2" name="{{ $row->field }}"></select>
    @endif
@else
    <?php $selected_value = (isset($dataTypeContent->{$row->field}) && !is_null(old($row->field, $dataTypeContent->{$row->field}))) ? old($row->field, $dataTypeContent->{$row->field}) : old($row->field); ?>
    <select class="form-control select2" name="{{ $row->field }}">
        <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : NULL; ?>
        @if(isset($options->options))
            @foreach($options->options as $key => $option)
                <option value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'selected="selected"' }}@endif @if($selected_value == $key){{ 'selected="selected"' }}@endif>{{ $option }}</option>
            @endforeach
        @endif
    </select>
@endif
