@extends('voyager::master')

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> Viewing {{ ucfirst($dataType->display_name_singular) }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">

                    <!-- /.box-header -->
                    <!-- form start -->


                    @foreach($dataType->readRows as $row)

                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">{{ $row->display_name }}</h3>
                        </div>

                        <div class="panel-body" style="padding-top:0;">
                            @if($row->type == "image")
                                <img style="max-width:640px"
                                     src="{!! Voyager::image($dataTypeContent->{$row->field}) !!}">
                            @elseif($row->type == 'date')
                            {{ \Carbon\Carbon::parse($dataTypeContent->{$row->field})->format('F jS, Y h:i A') }}
                            @else
                                <p>{{ $dataTypeContent->{$row->field} }}</p>
                            @endif
                        </div><!-- panel-body -->
                        @if(!$loop->last)
                            <hr style="margin:0;">
                        @endif
                    @endforeach

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">User Role</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{$dataTypeContent->role->display_name}}</p>
                    </div>


                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')

@stop
