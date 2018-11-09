@if ($action == 'edit' || $action == 'add')
    @section("formfield_edit_add")
        {{-- If this is a relationship and the method does not exist, show a warning message --}}
        @if(isset($options->relationship) && !method_exists( $dataType->model_name, camel_case($row->field) ) )
            <p class="label label-warning"><i class="voyager-warning"></i> {{ __('voyager::form.field_select_dd_relationship', ['method' => camel_case($row->field).'()', 'class' => $dataType->model_name]) }}</p>
        @endif
        @php
            $dataTypeContent->{$row->field} = json_decode($dataTypeContent->{$row->field})
        @endphp
        <select class="form-control select2" name="{{ $row->field }}[]" multiple>
            @if(isset($options->relationship))
                {{-- Check that the relationship method exists --}}
                @if( method_exists( $dataType->model_name, camel_case($row->field) ) )
                    <?php $selected_values = isset($dataTypeContent) ? $dataTypeContent->{camel_case($row->field)}()->pluck($options->relationship->key)->all() : []; ?>
                    <?php
                    $relationshipListMethod = camel_case($row->field) . 'List';
                    if (isset($dataTypeContent) && method_exists($dataTypeContent, $relationshipListMethod)) {
                        $relationshipOptions = $dataTypeContent->$relationshipListMethod();
                    } else {
                        $relationshipClass = get_class(app($dataType->model_name)->{camel_case($row->field)}()->getRelated());
                        $relationshipOptions = $relationshipClass::all();
                    }
                    ?>
                    @foreach($relationshipOptions as $relationshipOption)
                        <option value="{{ $relationshipOption->{$options->relationship->key} }}" @if(in_array($relationshipOption->{$options->relationship->key}, $selected_values)){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->relationship->label} }}</option>
                    @endforeach
                @endif
            @elseif(isset($options->options))
                @foreach($options->options as $key => $label)
                    <?php $selected = ''; ?>
                    @if(is_array($dataTypeContent->{$row->field}) && in_array($key, $dataTypeContent->{$row->field}))
                        <?php $selected = 'selected="selected"'; ?>
                    @elseif(!is_null(old($row->field)) && in_array($key, old($row->field)))
                        <?php $selected = 'selected="selected"'; ?>
                    @endif
                    <option value="{{ $key }}" {!! $selected !!}>
                        {{ $label }}
                    </option>
                @endforeach
            @endif
        </select>
    @overwrite
@endif

{{--  Render BREA[D] --}}

@if ($action == 'browse')
    @section("formfield_browse")
        @if(property_exists($row->details, 'relationship'))

            @foreach($dataTypeContent->{$row->field} as $item)
                @if($item->{$row->field . '_page_slug'})
                    <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field} }}</a>@if(!$loop->last), @endif
                @else
                    {{ $item->{$row->field} }}
                @endif
            @endforeach

        @elseif(property_exists($row->details, 'options'))
            @if (count(json_decode($dataTypeContent->{$row->field})) > 0)
                @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                    @if (@$row->details->options->{$item})
                        {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                    @endif
                @endforeach
            @else
                {{ __('voyager::generic.none') }}
            @endif
        @endif
    @overwrite
@endif

@if ($action == 'read')
    @section("formfield_read")
        @if(property_exists($row->details, 'relationship'))

            @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                @if($item->{$row->field . '_page_slug'})
                    <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field}  }}</a>@if(!$loop->last), @endif
                @else
                    {{ $item->{$row->field}  }}
                @endif
            @endforeach

        @elseif(property_exists($row->details, 'options'))
            @if (count(json_decode($dataTypeContent->{$row->field})) > 0)
                @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                    @if (@$row->details->options->{$item})
                        {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                    @endif
                @endforeach
            @else
                {{ __('voyager::generic.none') }}
            @endif
        @endif
    @overwrite
@endif

@if ($action == 'edit')
    @section("formfield_edit")
        @yield("formfield_edit_add")
    @overwrite
@endif

@if ($action == 'add')
    @section("formfield_add")
        @yield("formfield_edit_add")
    @overwrite
@endif

@yield("formfield_{$action}")