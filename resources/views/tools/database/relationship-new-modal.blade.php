<!-- <div class="modal fade modal-danger modal-relationships" id="new_relationship_modal">
	<div class="modal-dialog relationship-panel">
	    <div class="model-content">
	        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-heart"></i> {{ str_singular(ucfirst($table)) }} Relationships</h4>
            </div>

	        <div class="modal-body">
		        <div class="row">

		            <div class="col-md-12 relationship_details">
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
		                <span><input type="text" class="form-control" name="relationship_model[]" placeholder="Namespace" value="@if(isset($relationshipDetails->model)){{ $relationshipDetails->model }}@endif"></span>
		            </div>
		            <div class="col-md-12 relationship_details_more">
		                <div class="well">
		                    <label>Selection Details</label>
		                    <p><strong>Display the <span class="label_table_name"></span>: </strong>
		                        <select name="relationship_label[]" class="rowDrop select2" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->label)){{ $relationshipDetails->label }}@endif">
		                        </select>
		                    </p>
		                    <p><strong>Store the <span class="label_table_name"></span>: </strong>
		                        <select name="relationship_key[]" class="rowDrop select2" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->key)){{ $relationshipDetails->key }}@endif">
		                        </select>
		                    </p>
		                </div>
		            </div>
		        
		        </div>
		    </div>
		    <div class="modal-footer">
		    	<div class="relationship-btn-container">
                    <div class="btn btn-default btn-relationship"><i class="voyager-plus"></i> <span>Add New relationship</span></div>
                </div>
		    </div>
	    </div>
	</div>
</div> -->