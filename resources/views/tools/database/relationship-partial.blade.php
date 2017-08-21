@php $relationshipDetails = json_decode($relationship['details']); @endphp
<div class="row row-dd row-dd-relationship">
    <div class="col-xs-2">

        <h4><i class="voyager-heart"></i><strong>{{ $relationship->display_name }}</strong></h4>
        <div class="handler voyager-handle"></div>
        <strong>{{ __('voyager.database.type') }}:</strong> <span>Relationship</span>
        <input class="row_order" type="hidden" value="3">
    </div>
    <div class="col-xs-2">
        <input type="checkbox" name="relationship_browse[]" @if(isset($relationship->browse) && $relationship->browse){{ 'checked="checked"' }}@elseif(!isset($relationship->browse)){{ 'checked="checked"' }}@endif>
        <label for="relationship_browse[]"> Browse</label><br>
        <input type="checkbox" name="relationship_read[]" @if(isset($relationship->read) && $relationship->read){{ 'checked="checked"' }}@elseif(!isset($relationship->read)){{ 'checked="checked"' }}@endif>
        <label for="relationship_read[]"> Read</label><br>
        <input type="checkbox" name="relationship_edit[]" @if(isset($relationship->edit) && $relationship->edit){{ 'checked="checked"' }}@elseif(!isset($relationship->edit)){{ 'checked="checked"' }}@endif>
        <label for="relationship_edit[]"> Edit</label><br>
        <input type="checkbox" name="relationship_add[]" @if(isset($relationship->add) && $relationship->add){{ 'checked="checked"' }}@elseif(!isset($relationship->add)){{ 'checked="checked"' }}@endif>
        <label for="relationship_add[]"> Add</label><br>
        <input type="checkbox" name="relationship_delete[]" @if(isset($relationship->delete) && $relationship->delete){{ 'checked="checked"' }}@elseif(!isset($relationship->delete)){{ 'checked="checked"' }}@endif>
        <label for="relationship_delete[]"> Delete</label><br>
    </div>
    <div class="col-xs-2">
        <p>Relationship</p>
    </div>
    <div class="col-xs-2">
        <input type="text" name="relationship_display_name[]" class="form-control relationship_display_name" value="{{ $relationship['display_name'] }}">
    </div>
    <div class="col-xs-4">
        <div class="voyager-relationship-details-btn">
            <i class="voyager-angle-down"></i><i class="voyager-angle-up"></i> <span class="open_text">Open</span><span class="close_text">Close</span> Relationship Details
        </div>
    </div>
    <div class="col-md-12 voyager-relationship-details">
        <div class="delete_relationship"><i class="voyager-trash"></i> Delete</div>
        <div class="relationship_details_content">
            <p class="relationship_table_select">{{ str_singular(ucfirst($table)) }}
                <select name="relationship_field[]">
                @foreach($fieldOptions as $data)
                    <option value="{{ $data['field'] }}_relationship" @if($relationship->relationshipField() == $data['field']){{ 'selected="selected"' }}@endif>{{ $data['field'] }}</option>
                @endforeach
                <option value="__pivot__">PIVOT</option>
            </select>
            </p>
            <select class="relationship_type select2" name="relationship_type[]">
                <option value="hasOne" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasOne'){{ 'selected="selected"' }}@endif>Has One</option>
                <option value="hasMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasMany'){{ 'selected="selected"' }}@endif>Has Many</option>
                <option value="belongsTo" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsTo'){{ 'selected="selected"' }}@endif>Belongs To</option>
                <option value="belongsToMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsToMany'){{ 'selected="selected"' }}@endif>Belongs To Many</option>
            </select>
            <select class="relationship_table select2" name="relationship_table[]">
                @foreach($tables as $table)
                    <option value="{{ $table }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $table){{ 'selected="selected"' }}@endif>{{ ucfirst($table) }}</option>
                @endforeach
            </select>
            <span><input type="text" class="form-control" name="relationship_model[]" placeholder="Model Namespace (ex. App\Category)" value="@if(isset($relationshipDetails->model)){{ $relationshipDetails->model }}@endif"></span>
        </div>
        <div class="relationship_details_content margin_top">
            <label>Display the <span class="label_table_name"></span></label>
            <select name="relationship_label[]" class="rowDrop select2" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->label)){{ $relationshipDetails->label }}@endif">
            </select>
            <label>Store the <span class="label_table_name"></span></label>
            <select name="relationship_key[]" class="rowDrop select2" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->key)){{ $relationshipDetails->key }}@endif">
            </select>
        </div>
    </div>
</div>