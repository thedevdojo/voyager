<?php $options = json_decode($row->details); ?>
@if(isset($options->relationship))
    {{-- If this is a relationship and the method does not exist, show a warning message --}}
    @if( !method_exists( $dataType->model_name, $row->field ) )
        <p class="label label-warning"><i class="voyager-warning"></i> Make sure to setup the appropriate relationship in the {{ $row->field . '()' }} method of the {{ $dataType->model_name }} class.</p>
    @endif

    @if( method_exists( $dataType->model_name, $row->field ) )
        <?php $selected_value = (isset($dataTypeContent->{$row->field}) && !is_null(old($row->field, $dataTypeContent->{$row->field}))) ? old($row->field, $dataTypeContent->{$row->field}) : old($row->field); ?>
        <select class="form-control select2" name="{{ $row->field }}">

            <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : NULL; ?>

            @if(isset($options->options))
                <optgroup label="Custom">
                    @foreach($options->options as $key => $option)
                        <option value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'selected="selected"' }}@endif @if((string)$selected_value == (string)$key){{ 'selected="selected"' }}@endif>{{ $option }}</option>
                    @endforeach
                </optgroup>
            @endif

            <?php $relationshipClass = get_class(app($dataType->model_name)->{$row->field}()->getRelated()); ?>
            <?php $relationshipOptions = $relationshipClass::all(); ?>
            <optgroup label="Relationship">
                @foreach($relationshipOptions as $relationshipOption)
                    <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if($selected_value == $relationshipOption->{$options->relationship->key}){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
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
