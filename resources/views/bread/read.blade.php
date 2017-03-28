@extends('voyager::master')

@section('page_title','View '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> Viewing {{ ucfirst($dataType->display_name_singular) }} &nbsp;

        @if (Voyager::can('edit_'.$dataType->name))
        <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;
            Edit
        </a>
        @endif
        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Return to List
        </a>        
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">

                    <!-- /.box-header -->
                    <!-- form start -->

                    @foreach($dataType->readRows as $row)
                        @php $rowDetails = json_decode($row->details); @endphp

                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">{{ $row->display_name }}</h3>
                        </div>

                        <div class="panel-body" style="padding-top:0;">
                            @if($row->type == "image")
                                <img class="img-responsive"
                                     src="{{ Voyager::image($dataTypeContent->{$row->field}) }}">
                            @elseif($row->type == 'select_dropdown' && property_exists($rowDetails, 'options') &&
                                    !empty($rowDetails->options->{$dataTypeContent->{$row->field}})
                            )

                                <?php echo $rowDetails->options->{$dataTypeContent->{$row->field}};?>
                            @elseif($row->type == 'select_dropdown' && $dataTypeContent->{$row->field . '_page_slug'})
                                <a href="{{ $dataTypeContent->{$row->field . '_page_slug'} }}">{{ $dataTypeContent->{$row->field}  }}</a>
                            @elseif($row->type == 'select_multiple')
                                @if(property_exists($rowDetails, 'relationship'))

                                    @foreach($dataTypeContent->{$row->field} as $item)
                                        @if($item->{$row->field . '_page_slug'})
                                        <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field}  }}</a>@if(!$loop->last), @endif
                                        @else
                                        {{ $item->{$row->field}  }}
                                        @endif
                                    @endforeach

                                @elseif(property_exists($rowDetails, 'options'))
                                    @foreach($dataTypeContent->{$row->field} as $item)
                                     {{ $rowDetails->options->{$item} . (!$loop->last ? ', ' : '') }}
                                    @endforeach
                                @endif
                            @elseif($row->type == 'date')
                                {{ $rowDetails && property_exists($rowDetails, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($rowDetails->format) : $dataTypeContent->{$row->field} }}
                            @elseif($row->type == 'checkbox')
                                @if($rowDetails && property_exists($rowDetails, 'on') && property_exists($rowDetails, 'off'))
                                    @if($dataTypeContent->{$row->field})
                                    <span class="label label-info">{{ $rowDetails->on }}</span>
                                    @else
                                    <span class="label label-primary">{{ $rowDetails->off }}</span>
                                    @endif
                                @else
                                {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'rich_text_box')
                                @include('voyager::multilingual.input-hidden-bread-read')
                                <p>{{ strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') }}</p>
                            @else
                                @include('voyager::multilingual.input-hidden-bread-read')
                                <p>{{ $dataTypeContent->{$row->field} }}</p>
                            @endif
                        </div><!-- panel-body -->
                        @if(!$loop->last)
                            <hr style="margin:0;">
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    @if ($isModelTranslatable)
    <script>
        $(document).ready(function () {
            $('.side-body').multilingual();
        });
    </script>
    <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
@stop
