@if(isset($options->model) && isset($options->type))

	@if(class_exists($options->model))

		@if($options->type == 'hasOne')
			@php $relationshipField = rtrim($row->field, '_relationship') @endphp
			<select class="form-control select2" name="{{ $relationshipField }}">
				@php 
					$model = app($options->model);
            		$query = $model::all();
            	@endphp
				@foreach($query as $relationshipData)
					<option value="{{ $relationshipData->key }}" @if($row->{$relationshipField} == $relationshipData->id){{ 'selected="selected"' }}@endif>{{ $relationshipData->{$options->label} }}</option>
				@endforeach
			</select>
		@endif

	@else

		cannot make relationship because {{ $options->model }} does not exist.

	@endif

@endif