<script id="relationship-edit-add" type="text/x-jquery-template">
<tr>
    <td>
        <select class="form-control select2" name="{{ $row->field }}[]">
            @foreach($relationshipOptions as $relationshipOption)
                <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if(in_array($relationshipOption->{$options->relationship->key}, $selected_values)){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
            @endforeach
        </select>
    </td>
    @foreach ($options->relationship->editablePivotFields as $pivotField)
    <td><input type="text" name="pivot_{{$pivotField}}[]" class="form-control" placeholder="{{ $pivotField }}" value=""></td>
    @endforeach
    <td class="danger">
        <button type="button" class="close form-control btn-remove-bread-relationship" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></button>
    </td>
</tr>
</tr>
</script>
