<!-- !!! Add form action below -->
<form role="form" action="{{ route('voyager.database.bread.relationship') }}" method="POST">
	<div class="modal fade modal-danger modal-relationships" id="new_relationship_modal">
		<div class="modal-dialog relationship-panel">
		    <div class="model-content">
		        <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal"
	                        aria-hidden="true">&times;</button>
	                <h4 class="modal-title"><i class="voyager-heart"></i> {{ str_singular(ucfirst($table)) }} Relationships</h4>
	            </div>

		        <div class="modal-body">
			        <div class="row">

			        	@if(isset($dataType->id))
				            <div class="col-md-12 relationship_details">
				                <p class="relationship_table_select">{{ str_singular(ucfirst($table)) }}</p>
				                <select class="relationship_type select2" name="relationship_type">
				                    <option value="hasOne" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasOne'){{ 'selected="selected"' }}@endif>Has One</option>
				                    <option value="hasMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasMany'){{ 'selected="selected"' }}@endif>Has Many</option>
				                    <option value="belongsTo" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsTo'){{ 'selected="selected"' }}@endif>Belongs To</option>
				                    <option value="belongsToMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsToMany'){{ 'selected="selected"' }}@endif>Belongs To Many</option>
				                </select>
				                <select class="relationship_table select2" name="relationship_table">
				                    @foreach($tables as $tbl)
				                        <option value="{{ $tbl }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $tbl){{ 'selected="selected"' }}@endif>{{ str_singular(ucfirst($tbl)) }}</option>
				                    @endforeach
				                </select>
				                <span><input type="text" class="form-control" name="relationship_model" placeholder="Namespace" value="@if(isset($relationshipDetails->model)){{ $relationshipDetails->model }}@endif"></span>
				            </div>
				            <div class="col-md-12 relationship_details relationshipField">
				            	<div class="belongsTo">
				            		<label>Which column from the <span>{{ str_singular(ucfirst($table)) }}</span> is used to reference the <span class="label_table_name"></span>?</label>
				            		<select name="relationship_column_belongs_to" class="new_relationship_field select2">
				                    	@foreach($fieldOptions as $data)
				                        	<option value="{{ $data['field'] }}">{{ $data['field'] }}</option>
				                    	@endforeach
				               		</select>
				               	</div>
				               	<div class="hasOneMany flexed">
				            		<label>Which column from the <span class="label_table_name"></span> is used to reference the <span>{{ str_singular(ucfirst($table)) }}</span>?</label>
					                <select name="relationship_column" class="new_relationship_field select2 rowDrop" data-table="{{ $tables[0] }}" data-selected="">
					                </select>
					            </div>
				            </div>
				            <div class="col-md-12 relationship_details relationshipPivot">
				            	<label>Pivot Table:</label>
				            	<select name="relationship_pivot" class="select2">
		                        	@foreach($tables as $tbl)
				                        <option value="{{ $tbl }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $tbl){{ 'selected="selected"' }}@endif>{{ str_singular(ucfirst($tbl)) }}</option>
				                    @endforeach
		                        </select>
				            </div>
				            <div class="col-md-12 relationship_details_more">
				                <div class="well">
				                    <label>Selection Details</label>
				                    <p><strong>Display the <span class="label_table_name"></span>: </strong>
				                        <select name="relationship_label" class="rowDrop select2" data-table="{{ $tables[0] }}" data-selected="">
				                        </select>
				                    </p>
				                    <p class="relationship_key"><strong>Store the <span class="label_table_name"></span>: </strong>
				                        <select name="relationship_key" class="rowDrop select2" data-table="{{ $tables[0] }}" data-selected="">
				                        </select>
				                    </p>
				                </div>
				            </div>
				        @else
				        	<div class="col-md-12">
				        		<h5><i class="voyager-rum-1"></i> Easy there Captain</h5>
				        		<p class="relationship-warn">Before you can create a new relationship you will need to create the BREAD first.<br> Then, return back to edit the BREAD and you will be able to add relationships.<br> Thanks.</p>
				        	</div>
				        @endif
			        
			        </div>
			    </div>
			    <div class="modal-footer">
			    	<div class="relationship-btn-container">
			    		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	                    @if(isset($dataType->id))
	                    	<button class="btn btn-danger btn-relationship"><i class="voyager-plus"></i> <span>Add New relationship</span></button>
	                	@endif
	                </div>
			    </div>
		    </div>
		</div>
	</div>
	<input type="hidden" value="@if(isset($dataType->id)){{ $dataType->id }}@endif" name="data_type_id">
	{{ csrf_field() }}
</form>