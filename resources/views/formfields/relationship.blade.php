@if(isset($options->model) && isset($options->type))
	
	@if(class_exists($options->model))

		@php $relationshipField = @$options->column @endphp

		@if($options->type == 'belongsTo')
			
			<select class="form-control select2" name="{{ $relationshipField }}">
				@php 
					$model = app($options->model);
            		$query = $model::all();
            	@endphp
				@foreach($query as $relationshipData)
					<option value="{{ $relationshipData->{$options->key} }}" @if($dataTypeContent->{$relationshipField} == $relationshipData->{$options->key}){{ 'selected="selected"' }}@endif>{{ $relationshipData->{$options->label} }}</option>
				@endforeach
			</select>
		
		@elseif($options->type == 'hasOne')

				@php
					$model = app($options->model);
            		$query = $model::where($options->column, '=', $dataTypeContent->id)->first();
				@endphp

			@if(isset($query))
				<p>{{ $query->{$options->label} }}</p>
			@else
				<p>None found.</p>
			@endif

		@elseif($options->type == 'hasMany')

				@php
					$model = app($options->model);
            		$query = $model::where($options->column, '=', $dataTypeContent->id)->get();
				@endphp

			@if(isset($query))
				<ul>
					@foreach($query as $query_res)
						<li>{{ $query_res->{$options->label} }}</li>
					@endforeach
				</ul>
				
			@else
				<p>None found.</p>
			@endif

		@elseif($options->type == 'belongsToMany' || $options->type == 'hasMany')

			<select class="form-control select2" name="{{ $relationshipField }}[]" multiple>
				
		            <?php $selected_values = isset($dataTypeContent) ? $dataTypeContent->belongsToMany($options->model)->pluck($options->key)->all() : array(); //{camel_case($row->field)}()->pluck($options->relationship->key)->all() : array(); ?>
		            <?php
		            // $relationshipListMethod = camel_case($row->field) . 'List';
		            // if (isset($dataTypeContent) && method_exists($dataTypeContent, $relationshipListMethod)) {
		            //     $relationshipOptions = $dataTypeContent->$relationshipListMethod();
		            // } else {
		            	//$dataType = Voyager::model('DataType')->find($row->data_type_id);
		                //$relationshipClass = get_class(app($options->model)->getRelated());
		                $relationshipOptions = app($options->model)->all();
		            //}
		            ?>
		            @foreach($relationshipOptions as $relationshipOption)
		                <option value="{{ $relationshipOption->{$options->key} }}" @if(in_array($relationshipOption->{$options->key}, $selected_values)){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->label} }}</option>
		            @endforeach

		    </select>

		@endif

	@else

		cannot make relationship because {{ $options->model }} does not exist.

	@endif

@endif