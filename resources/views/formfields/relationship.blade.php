@if(isset($options->model) && isset($options->type))

    @if(class_exists($options->model))

        @php

            $relationshipField = $row->field;

            $slug = null;
            $dataType = \TCG\Voyager\Models\DataType::where(['name' => $options->table])->first();

            if ($dataType) {
                $slug = $dataType->slug;
            }

        @endphp

        @if($options->type == 'belongsTo')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $model = app($options->model);
                    $query = $model::find($relationshipData->{$options->column});
                @endphp

                @if(isset($query))
                    @if ($slug and isset($query->id))
                        <p>
                            <a href="{{ route('voyager.'. $slug .'.show', ['id' => $query->id]) }}" target="_blank">
                                {{ $query->{$options->label} }}
                            </a>
                        </p>
                    @else
                        <p>{{ $query->{$options->label} }}</p>
                    @endif
                @else
                    <p>{{ __('voyager::generic.no_results') }}</p>
                @endif

            @else

                <select class="form-control select2" name="{{ $options->column }}">
                    @php
                        $model = app($options->model);
                        $query = $model::all();
                    @endphp

                    @if(!$row->required)
                        <option value="">{{__('voyager::generic.none')}}</option>
                    @endif

                    @foreach($query as $relationshipData)
                        <option value="{{ $relationshipData->{$options->key} }}" @if($dataTypeContent->{$options->column} == $relationshipData->{$options->key}){{ 'selected="selected"' }}@endif>{{ $relationshipData->{$options->label} }}</option>
                    @endforeach
                </select>

            @endif

        @elseif($options->type == 'hasOne')

            @php

                $relationshipData = (isset($data)) ? $data : $dataTypeContent;

                $model = app($options->model);
                $query = $model::where($options->column, '=', $relationshipData->id)->first();

            @endphp

            @if(isset($query))
                @if ($slug and isset($query->id))
                    <p>
                        <a href="{{ route('voyager.'. $slug .'.show', ['id' => $query->id]) }}" target="_blank">
                            {{ $query->{$options->label} }}
                        </a>
                    </p>
                @else
                    <p>{{ $query->{$options->label} }}</p>
                @endif
            @else
                <p>{{ __('voyager::generic.no_results') }}</p>
            @endif

        @elseif($options->type == 'hasMany')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $model = app($options->model);
            		$selected_values = $model::where($options->column, '=', $relationshipData->id)->get()->all();
                @endphp

                @if($view == 'browse')
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        @foreach(array_slice($selected_values, 0, 4) as $value)
                            <span class="badge label label-default">
                                {{ $value->{$options->label} }}
                            </span>
                        @endforeach
                        @if (count($selected_values) > 4)
                            ...
                        @endif
                    @endif
                @else
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <div class="list-group">
                            @foreach($selected_values as $selected_value)
                                @if ($slug and isset($selected_value->id))
                                    <a href="{{ route('voyager.'. $slug .'.show', ['id' => $selected_value->id]) }}" class="list-group-item list-group-item-action" target="_blank">
                                        {{ $selected_value->{$options->label} }}
                                    </a>
                                @else
                                    <div class="list-group-item">{{ $selected_value }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif

            @else

                @php
                    $model = app($options->model);
                    $query = $model::where($options->column, '=', $dataTypeContent->id)->get();
                @endphp

                @if(isset($query))
                    <div class="list-group">
                        @foreach($query as $query_res)
                            <div class="list-group-item">{{ $query_res->{$options->label} }}</div>
                        @endforeach
                    </div>

                @else
                    <p>{{ __('voyager::generic.no_results') }}</p>
                @endif

            @endif

        @elseif($options->type == 'belongsToMany')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $selected_values = isset($relationshipData) ? $relationshipData->belongsToMany($options->model, $options->pivot_table)->get()->all() : array();
                @endphp

                @if($view == 'browse')
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        @foreach(array_slice($selected_values, 0, 4) as $value)
                            <span class="badge label label-default">
                                {{ $value->{$options->label} }}
                            </span>
                        @endforeach
                        @if (count($selected_values) > 4)
                            ...
                        @endif
                    @endif
                @else
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <div class="list-group">
                            @foreach($selected_values as $selected_value)
                                @if ($slug and isset($selected_value->id))
                                    <a href="{{ route('voyager.'. $slug .'.show', ['id' => $selected_value->id]) }}" class="list-group-item list-group-item-action" target="_blank">
                                        {{ $selected_value->{$options->label} }}
                                    </a>
                                @else
                                    <div class="list-group-item">{{ $selected_value }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif

            @else
                <select
                        class="form-control @if(isset($options->taggable) && $options->taggable == 'on') select2-taggable @else select2 @endif"
                        name="{{ $relationshipField }}[]" multiple
                        @if(isset($options->taggable) && $options->taggable == 'on')
                        data-route="{{ route('voyager.'.str_slug($options->table).'.store') }}"
                        data-label="{{$options->label}}"
                        data-error-message="{{__('voyager::bread.error_tagging')}}"
                        @endif
                >

                    @php
                        $selected_values = isset($dataTypeContent) ? $dataTypeContent->belongsToMany($options->model, $options->pivot_table)->get()->map(function ($item, $key) use ($options) {
                            return $item->{$options->key};
                        })->all() : array();
                        $relationshipOptions = app($options->model)->all();
                    @endphp

                    @if(!$row->required)
                        <option value="">{{__('voyager::generic.none')}}</option>
                    @endif

                    @foreach($relationshipOptions as $relationshipOption)
                        <option value="{{ $relationshipOption->{$options->key} }}" @if(in_array($relationshipOption->{$options->key}, $selected_values)){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->label} }}</option>
                    @endforeach

                </select>

            @endif

        @endif

    @else

        cannot make relationship because {{ $options->model }} does not exist.

    @endif

@endif
