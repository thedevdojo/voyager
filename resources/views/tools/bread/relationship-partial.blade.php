@php $relationshipDetails = $relationship['details']; @endphp
<div class="row row-dd row-dd-relationship">
    <div class="col-xs-2">
        <h4><i class="voyager-heart"></i><strong>{{ $relationship->display_name }}</strong></h4>
        <div class="handler voyager-handle"></div>
        <strong>{{ __('voyager::database.type') }}:</strong> <span>{{ __('voyager::database.relationship.relationship') }}</span>
        <div class="handler voyager-handle"></div>
        <input class="row_order" type="hidden" value="{{ $relationship['order'] }}" name="field_order_{{ $relationship['field'] }}">
    </div>
    <div class="col-xs-2">
        <input type="checkbox" name="field_browse_{{ $relationship['field'] }}" @if(isset($relationship->browse) && $relationship->browse){{ 'checked="checked"' }}@elseif(!isset($relationship->browse)){{ 'checked="checked"' }}@endif>
        <label for="field_browse_{{ $relationship['field'] }}"> {{ __('voyager::database.relationship.browse') }}</label><br>
        <input type="checkbox" name="field_read_{{ $relationship['field'] }}" @if(isset($relationship->read) && $relationship->read){{ 'checked="checked"' }}@elseif(!isset($relationship->read)){{ 'checked="checked"' }}@endif>
        <label for="field_read_{{ $relationship['field'] }}"> {{ __('voyager::database.relationship.read') }}</label><br>
        <input type="checkbox" name="field_edit_{{ $relationship['field'] }}" @if(isset($relationship->edit) && $relationship->edit){{ 'checked="checked"' }}@elseif(!isset($relationship->edit)){{ 'checked="checked"' }}@endif>
        <label for="field_edit_{{ $relationship['field'] }}"> {{ __('voyager::database.relationship.edit') }}</label><br>
        <input type="checkbox" name="field_add_{{ $relationship['field'] }}" @if(isset($relationship->add) && $relationship->add){{ 'checked="checked"' }}@elseif(!isset($relationship->add)){{ 'checked="checked"' }}@endif>
        <label for="field_add_{{ $relationship['field'] }}"> {{ __('voyager::database.relationship.add') }}</label><br>
        <input type="checkbox" name="field_delete_{{ $relationship['field'] }}" @if(isset($relationship->delete) && $relationship->delete){{ 'checked="checked"' }}@elseif(!isset($relationship->delete)){{ 'checked="checked"' }}@endif>
        <label for="field_delete_{{ $relationship['field'] }}"> {{ __('voyager::database.relationship.delete') }}</label><br>
    </div>
    <div class="col-xs-2">
        <p>{{ __('voyager::database.relationship.relationship') }}</p>
    </div>
    <div class="col-xs-2">
        <input type="text" name="field_display_name_{{ $relationship['field'] }}" class="form-control relationship_display_name" value="{{ $relationship['display_name'] }}">
    </div>
    <div class="col-xs-4">
        <div class="voyager-relationship-details-btn">
            <i class="voyager-angle-down"></i><i class="voyager-angle-up"></i> 
            <span class="open_text">{{ __('voyager::database.relationship.open') }}</span>
            <span class="close_text">{{ __('voyager::database.relationship.close') }}</span>
            {{ __('voyager::database.relationship.relationship_details') }}
        </div>
    </div>
    <div class="col-md-12 voyager-relationship-details">
        <a href="{{ route('voyager.bread.delete_relationship', $relationship['id']) }}" class="delete_relationship"><i class="voyager-trash"></i> {{ __('voyager::database.relationship.delete') }}</a>
        <div class="relationship_details_content">
            <p class="relationship_table_select">{{ str_singular(ucfirst($table)) }}</p>
            <select class="relationship_type select2" name="relationship_type_{{ $relationship['field'] }}">
                <option value="hasOne" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasOne'){{ 'selected="selected"' }}@endif>{{ __('voyager::database.relationship.has_one') }}</option>
                <option value="hasMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasMany'){{ 'selected="selected"' }}@endif>{{ __('voyager::database.relationship.has_many') }}</option>
                <option value="belongsTo" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsTo'){{ 'selected="selected"' }}@endif>{{ __('voyager::database.relationship.belongs_to') }}</option>
                <option value="belongsToMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsToMany'){{ 'selected="selected"' }}@endif>{{ __('voyager::database.relationship.belongs_to_many') }}</option>
            </select>
            <select class="relationship_table select2" name="relationship_table_{{ $relationship['field'] }}">
                @foreach($tables as $tablename)
                    <option value="{{ $tablename }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $tablename){{ 'selected="selected"' }}@endif>{{ ucfirst($tablename) }}</option>
                @endforeach
            </select>
            <span><input type="text" class="form-control" name="relationship_model_{{ $relationship['field'] }}" placeholder="{{ __('voyager::database.relationship.namespace') }}" value="@if(isset($relationshipDetails->model)){{ $relationshipDetails->model }}@endif"></span>
        </div>
            <div class="relationshipField">
                <div class="relationship_details_content margin_top belongsTo @if($relationshipDetails->type == 'belongsTo'){{ 'flexed' }}@endif">
                    <label>{{ __('voyager::database.relationship.which_column_from') }} <span>{{ str_singular(ucfirst($table)) }}</span> {{ __('voyager::database.relationship.is_used_to_reference') }} <span class="label_table_name"></span>?</label>
                    <select name="relationship_column_belongs_to_{{ $relationship['field'] }}" class="new_relationship_field select2">
                        @foreach($fieldOptions as $data)
                            <option value="{{ $data['field'] }}" @if($relationshipDetails->column == $data['field']){{ 'selected="selected"' }}@endif>{{ $data['field'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relationship_details_content margin_top hasOneMany @if($relationshipDetails->type == 'hasOne' || $relationshipDetails->type == 'hasMany'){{ 'flexed' }}@endif">
                    <label>{{ __('voyager::database.relationship.which_column_from') }} <span class="label_table_name"></span> {{ __('voyager::database.relationship.is_used_to_reference') }} <span>{{ str_singular(ucfirst($table)) }}</span>?</label>
                    <select name="relationship_column_{{ $relationship['field'] }}" class="new_relationship_field select2 rowDrop" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="{{ $relationshipDetails->column }}">
                    </select>
                </div>
            </div>
        <div class="relationship_details_content margin_top relationshipPivot @if($relationshipDetails->type == 'belongsToMany'){{ 'visible' }}@endif">
            <label>{{ __('voyager::database.relationship.pivot_table') }}:</label>
            <select name="relationship_pivot_table_{{ $relationship['field'] }}" class="select2">
                @foreach($tables as $tbl)
                    <option value="{{ $tbl }}" @if(isset($relationshipDetails->pivot_table) && $relationshipDetails->pivot_table == $tbl){{ 'selected="selected"' }}@endif>{{ str_singular(ucfirst($tbl)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="relationship_details_content margin_top">
            <label>{{ __('voyager::database.relationship.display_the') }} <span class="label_table_name"></span></label>
            <select name="relationship_label_{{ $relationship['field'] }}" class="rowDrop select2" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->label)){{ $relationshipDetails->label }}@endif">
            </select>
            <label class="relationship_key" style="@if($relationshipDetails->type == 'belongsTo' || $relationshipDetails->type == 'belongsToMany'){{ 'display:block' }}@endif">{{ __('voyager::database.relationship.store_the') }} <span class="label_table_name"></span></label>
            <select name="relationship_key_{{ $relationship['field'] }}" class="rowDrop select2 relationship_key" style="@if($relationshipDetails->type == 'belongsTo' || $relationshipDetails->type == 'belongsToMany'){{ 'display:block' }}@endif" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->key)){{ $relationshipDetails->key }}@endif">
            </select>
            <br>
            @isset($relationshipDetails->taggable)
                <label class="relationship_taggable" style="@if($relationshipDetails->type == 'belongsToMany'){{ 'display:block' }}@endif">
                    {{__('voyager::database.relationship.allow_tagging')}}
                </label>
                <span class="relationship_taggable" style="@if($relationshipDetails->type == 'belongsToMany'){{ 'display:block' }}@endif">
                    <input type="checkbox" name="relationship_taggable_{{ $relationship['field'] }}" class="toggleswitch" data-on="{{ __('voyager::generic.yes') }}" data-off="{{ __('voyager::generic.no') }}" {{$relationshipDetails->taggable == 'on' ? 'checked' : ''}}>
                </span>
            @endisset
        </div>
    </div>
    <input type="hidden" value="0" name="field_required_{{ $relationship['field'] }}" checked="checked">
    <input type="hidden" name="field_input_type_{{ $relationship['field'] }}" value="relationship">
    <input type="hidden" name="field_{{ $relationship['field'] }}" value="{{ $relationship['field'] }}">
    <input type="hidden" name="relationships[]" value="{{ $relationship['field'] }}">
</div>