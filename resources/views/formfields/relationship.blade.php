@if(isset($options->model) && isset($options->type))
	
	@if(class_exists($options->model))

		@php $relationshipField = $row->field; @endphp

		@if($options->type == 'belongsTo')

			@if(isset($view) && ($view == 'browse' || $view == 'read'))

				@php 
					$relationshipData = (isset($data)) ? $data : $dataTypeContent;
					$model = app($options->model);
					if (method_exists($model, 'getRelationship')) {
						$query = $model::getRelationship($relationshipData->{$options->column});
					} else {
						$query = $model::find($relationshipData->{$options->column});
					}
            	@endphp

            	@if(isset($query))
					<p>{{ $query->{$options->label} }}</p>
				@else
					<p>{{__('voyager::generic.no_results')}}</p>
				@endif

			@else
			
				<select class="form-control select2 select2ajax" name="{{ $options->column }}" 
					data-model="{{ $options->model }}" data-key="{{ $options->key }}" data-label="{{ $options->label }}" data-perpage="20">
					@php 
						$model = app($options->model);
						$selected_value = $model->where($options->key, $dataTypeContent->{$options->column})->pluck($options->label, $options->key);
					@endphp

					@if($row->required === 0)
						<option value="">{{__('voyager::generic.none')}}</option>
					@endif
					
					@if($selected_value)
						<option value="{{ $dataTypeContent->{$options->column} }}" selected="selected">{{ $selected_value[$dataTypeContent->{$options->column}] }}</option>
					@endif
				</select>

			@endif
		
		@elseif($options->type == 'hasOne')

			@php 

				$relationshipData = (isset($data)) ? $data : $dataTypeContent;
			
				$model = app($options->model);
        		$query = $model::where($options->column, '=', $relationshipData->id)->first();
			
			@endphp

			@if(isset($query))
				<p>{{ $query->{$options->label} }}</p>
			@else
				<p>None results</p>
			@endif

		@elseif($options->type == 'hasMany')

			@if(isset($view) && ($view == 'browse' || $view == 'read'))

				@php
					$relationshipData = (isset($data)) ? $data : $dataTypeContent;
					$model = app($options->model);
            		$selected_values = $model::where($options->column, '=', $relationshipData->id)->pluck($options->label)->all();
				@endphp

	            @if($view == 'browse')
	            	@php
	            		$string_values = implode(", ", $selected_values); 
	            		if(strlen($string_values) > 25){ $string_values = substr($string_values, 0, 25) . '...'; } 
	            	@endphp
	            	@if(empty($selected_values))
		            	<p>{{__('voyager::generic.no_results')}}</p>
		            @else
	            		<p>{{ $string_values }}</p>
	            	@endif
	            @else
	            	@if(empty($selected_values))
		            	<p>{{__('voyager::generic.no_results')}}</p>
		            @else
		            	<ul>
			            	@foreach($selected_values as $selected_value)
			            		<li>{{ $selected_value }}</li>
			            	@endforeach
			            </ul>
			        @endif
	            @endif

			@else

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
					<p>{{__('voyager::generic.no_results')}}</p>
				@endif

			@endif

		@elseif($options->type == 'belongsToMany')

			@if(isset($view) && ($view == 'browse' || $view == 'read'))

				@php
					$relationshipData = (isset($data)) ? $data : $dataTypeContent;
	            	$selected_values = isset($relationshipData) ? $relationshipData->belongsToMany($options->model, $options->pivot_table)->pluck($options->label)->all() : array();
	            @endphp

	            @if($view == 'browse')
	            	@php
	            		$string_values = implode(", ", $selected_values); 
	            		if(strlen($string_values) > 25){ $string_values = substr($string_values, 0, 25) . '...'; } 
	            	@endphp
	            	@if(empty($selected_values))
		            	<p>{{__('voyager::generic.no_results')}}</p>
		            @else
	            		<p>{{ $string_values }}</p>
	            	@endif
	            @else
	            	@if(empty($selected_values))
		            	<p>{{__('voyager::generic.no_results')}}</p>
		            @else
		            	<ul>
			            	@foreach($selected_values as $selected_value)
			            		<li>{{ $selected_value }}</li>
			            	@endforeach
			            </ul>
			        @endif
	            @endif

			@else
				<select
					class="form-control select2ajax @if(isset($options->taggable) && $options->taggable == 'on') select2-taggable @else select2 @endif" 
					name="{{ $relationshipField }}[]" multiple
					data-model="{{ $options->model }}" data-key="{{ $options->key }}" data-label="{{ $options->label }}" data-perpage="20"
					@if(isset($options->taggable) && $options->taggable == 'on')
						data-route="{{ route('voyager.'.str_slug($options->table).'.store') }}"
						data-label="{{$options->label}}"
						data-error-message="{{__('voyager::bread.error_tagging')}}"
					@endif
				>
					
						@php 
							// Loads the pre-selected values (key/value pairs)
							$selected_values = isset($dataTypeContent) ? $dataTypeContent->belongsToMany($options->model, $options->pivot_table)->pluck($options->table.'.'.$options->label, $options->table.'.'.$options->key)->all() : array();
							Log::debug($selected_values);
						@endphp
						
						@if($row->required === 0)
							<option value="">{{__('voyager::generic.none')}}</option>
						@endif

			            @foreach($selected_values as $key => $value)
			                <option value="{{ $key }}" selected="selected">{{ $value }}</option>
			            @endforeach

				</select>

			@endif

		@endif

	@else

		cannot make relationship because {{ $options->model }} does not exist.

	@endif

@endif

@section('javascript')
<script>
$(document).ready(function () {
	$('.select2ajax').select2({
		ajax: {
			url: '{{route("select2ajax")}}',
			dataType: 'json',
			delay: 500,
			data: function (params) {
				var query = {
					search: params.term,
					page: params.page || 1,
					model: $(this).data('model'),
					key: $(this).data('key'),
					label: $(this).data('label'),
					perpage: $(this).data('perpage'),
				}
				return query;
			}
		}
	});
});
</script>
@endsection
