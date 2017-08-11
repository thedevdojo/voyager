@foreach($dataTypeContent as $data)
@php
    $dataId = $data->id;
@endphp

<tr>
    @foreach($dataType->browseRows as $row)
        <td>
            <?php $options = json_decode($row->details); ?>
            @if($row->type == 'image')
                <img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
            @elseif($row->type == 'select_multiple')
                @if(property_exists($options, 'relationship'))

                    @foreach($data->{$row->field} as $item)
                        @if($item->{$row->field . '_page_slug'})
                        <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field} }}</a>@if(!$loop->last), @endif
                        @else
                        {{ $item->{$row->field} }}
                        @endif
                    @endforeach

                    {{-- $data->{$row->field}->implode($options->relationship->label, ', ') --}}
                @elseif(property_exists($options, 'options'))
                    @foreach($data->{$row->field} as $item)
                     {{ $options->options->{$item} . (!$loop->last ? ', ' : '') }}
                    @endforeach
                @endif

            @elseif($row->type == 'select_dropdown' && property_exists($options, 'options'))

                @if($data->{$row->field . '_page_slug'})
                    <a href="{{ $data->{$row->field . '_page_slug'} }}">{!! $options->options->{$data->{$row->field}} !!}</a>
                @else
                    {!! $options->options->{$data->{$row->field}} !!}
                @endif


            @elseif($row->type == 'select_dropdown' && $data->{$row->field . '_page_slug'})
                <a href="{{ $data->{$row->field . '_page_slug'} }}">{{ $data->{$row->field} }}</a>
            @elseif($row->type == 'date')
            {{ $options && property_exists($options, 'format') ? \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($options->format) : $data->{$row->field} }}
            @elseif($row->type == 'checkbox')
                @if($options && property_exists($options, 'on') && property_exists($options, 'off'))
                    @if($data->{$row->field})
                    <span class="label label-info">{{ $options->on }}</span>
                    @else
                    <span class="label label-primary">{{ $options->off }}</span>
                    @endif
                @else
                {{ $data->{$row->field} }}
                @endif
            @elseif($row->type == 'color')
                <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
            @elseif($row->type == 'text')
                @include('voyager::multilingual.input-hidden-bread-browse')
                <div class="readmore">{{ strlen( $data->{$row->field} ) > 200 ? substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
            @elseif($row->type == 'text_area')
                @include('voyager::multilingual.input-hidden-bread-browse')
                <div class="readmore">{{ strlen( $data->{$row->field} ) > 200 ? substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
            @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                @include('voyager::multilingual.input-hidden-bread-browse')
                @if(json_decode($data->{$row->field}))
                    @foreach(json_decode($data->{$row->field}) as $file)
                        <a href="/storage/{{ $file->download_link or '' }}">
                            {{ $file->original_name or '' }}
                        </a>
                        <br/>
                    @endforeach
                @else
                    <a href="/storage/{{ $data->{$row->field} }}">
                        Download
                    </a>
                @endif
            @elseif($row->type == 'rich_text_box')
                @include('voyager::multilingual.input-hidden-bread-browse')
                <div class="readmore">{{ strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
            @elseif($row->type == 'coordinates')
                @include('voyager::partials.coordinates-static-image')
            @else
                @include('voyager::multilingual.input-hidden-bread-browse')
                <span>{{ $data->{$row->field} }}</span>
            @endif
        </td>
    @endforeach
    @foreach($data->translations as $data)
        @foreach($dataTypeTranslation->browseRows as $row)
            <td>
                <?php $options = json_decode($row->details); ?>
                @if($row->type == 'image')
                    <img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                @elseif($row->type == 'select_multiple')
                    @if(property_exists($options, 'relationship'))

                        @foreach($data->{$row->field} as $item)
                            @if($item->{$row->field . '_page_slug'})
                            <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field} }}</a>@if(!$loop->last), @endif
                            @else
                            {{ $item->{$row->field} }}
                            @endif
                        @endforeach

                        {{-- $data->{$row->field}->implode($options->relationship->label, ', ') --}}
                    @elseif(property_exists($options, 'options'))
                        @foreach($data->{$row->field} as $item)
                         {{ $options->options->{$item} . (!$loop->last ? ', ' : '') }}
                        @endforeach
                    @endif

                @elseif($row->type == 'select_dropdown' && property_exists($options, 'options'))

                    @if($data->{$row->field . '_page_slug'})
                        <a href="{{ $data->{$row->field . '_page_slug'} }}">{!! $options->options->{$data->{$row->field}} !!}</a>
                    @else
                        {!! $options->options->{$data->{$row->field}} !!}
                    @endif


                @elseif($row->type == 'select_dropdown' && $data->{$row->field . '_page_slug'})
                    <a href="{{ $data->{$row->field . '_page_slug'} }}">{{ $data->{$row->field} }}</a>
                @elseif($row->type == 'date')
                {{ $options && property_exists($options, 'format') ? \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($options->format) : $data->{$row->field} }}
                @elseif($row->type == 'checkbox')
                    @if($options && property_exists($options, 'on') && property_exists($options, 'off'))
                        @if($data->{$row->field})
                        <span class="label label-info">{{ $options->on }}</span>
                        @else
                        <span class="label label-primary">{{ $options->off }}</span>
                        @endif
                    @else
                    {{ $data->{$row->field} }}
                    @endif
                @elseif($row->type == 'color')
                    <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                @elseif($row->type == 'text')
                    @include('voyager::multilingual.input-hidden-bread-browse')
                    <div class="readmore">{{ strlen( $data->{$row->field} ) > 200 ? substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                @elseif($row->type == 'text_area')
                    @include('voyager::multilingual.input-hidden-bread-browse')
                    <div class="readmore">{{ strlen( $data->{$row->field} ) > 200 ? substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                    @include('voyager::multilingual.input-hidden-bread-browse')
                    @if(json_decode($data->{$row->field}))
                        @foreach(json_decode($data->{$row->field}) as $file)
                            <a href="/storage/{{ $file->download_link or '' }}">
                                {{ $file->original_name or '' }}
                            </a>
                            <br/>
                        @endforeach
                    @else
                        <a href="/storage/{{ $data->{$row->field} }}">
                            Download
                        </a>
                    @endif
                @elseif($row->type == 'rich_text_box')
                    @include('voyager::multilingual.input-hidden-bread-browse')
                    <div class="readmore">{{ strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                @elseif($row->type == 'coordinates')
                    @include('voyager::partials.coordinates-static-image')
                @else
                    @include('voyager::multilingual.input-hidden-bread-browse')
                    <span>{{ $data->{$row->field} }}</span>
                @endif
            </td>
        @endforeach
    @endforeach

    <td class="no-sort no-click" id="bread-actions">
        @if (Voyager::can('delete_'.$dataType->name))
            <a href="javascript:;" title="{{ __('voyager.generic.delete') }}" class="btn btn-sm btn-danger pull-right delete" data-id="{{ $dataId }}" id="delete-{{ $dataId }}">
                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.delete') }}</span>
            </a>
        @endif
        @if (Voyager::can('edit_'.$dataType->name))
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataId) }}" title="{{ __('voyager.generic.edit') }}" class="btn btn-sm btn-primary pull-right edit">
                <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.edit') }}</span>
            </a>
        @endif
        @if (Voyager::can('read_'.$dataType->name))
            <a href="{{ route('voyager.'.$dataType->slug.'.show', $dataId) }}" title="{{ __('voyager.generic.view') }}" class="btn btn-sm btn-warning pull-right">
                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.view') }}</span>
            </a>
        @endif
    </td>
</tr>
@endforeach