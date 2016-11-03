@extends('voyager::master')

@section('page_header')
	<h1 class="page-title">
		<i class="voyager-list-add"></i> {{ $dataType->display_name_plural }} <a href="/admin/{{ $dataType->slug }}/create" class="btn btn-success"><i class="fa fa-plus-circle"></i> Add New</a>
	</h1>
@stop

@section('page_header_actions')
	
@stop

@section('content')

	<div class="container-fluid">
        <div class="alert alert-info">
            <strong>How To Use:</strong>
            <p>You can output a menu anywhere on your site by calling <code>Menu::display('name')</code></p>
        </div>
    </div>

	<div class="page-content container-fluid">	
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-bordered">
					<div class="panel-body">
					<table id="dataTable" class="table table-hover">
					    <thead>
					      <tr>
					      	@foreach($dataType->browseRows as $rows)
					      		<th>{{ $rows->field }}</th>
					      	@endforeach
					      	<th class="actions">Actions</th>
					      </tr>
					    </thead>
					    <tbody>
					   		@foreach($dataTypeContent as $data)
					      		<tr>
					      			@foreach($dataType->browseRows as $row)
					      				<td>

					      					@if($row->type == 'image')
					      						<img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
					      					@else
					      						{{ $data->{$row->field} }}
					      					@endif
					      				</td>
					      			@endforeach
					      			<td class="no-sort no-click"><div class="btn-sm btn-danger pull-right delete" data-id="{{ $data->id }}"><i class="fa fa-trash"></i> Delete</div><a href="/admin/{{ $dataType->slug }}/{{ $data->id }}/edit" class="btn-sm btn-primary pull-right edit"><i class="fa fa-edit"></i> Edit</a> <a href="/admin/{{ $dataType->slug }}/{{ $data->id }}/builder" class="btn-sm btn-success pull-right"><i class="fa fa-list"></i> Builder</a></td>
					      		</tr>
					      	@endforeach
					    </tbody>
					</table>
					</div>
				</div>
			</div>
	    </div>
	</div>
	</div>
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title"><i class="fa fa-trash-o"></i> Are you sure you want to delete this {{ $dataType->display_name_singular }}?</h4>
	      </div>
	      <div class="modal-footer">
            <form action="/admin/{{ $dataType->slug }}" id="delete_form" method="POST">
            	<input type="hidden" name="_method" value="DELETE">
            	<input type="hidden" name="_token" value="{{ csrf_token() }}">
            	<input type="submit" class="btn btn-danger pull-right delete-confirm" value="Yes, Delete This {{ $dataType->display_name_singular }}">
          	</form>
          	<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
          </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 
@stop

@section('javascript')
	<!-- DataTables -->
    

    <script>

      $(document).ready(function(){
        $('#dataTable').DataTable();

      });

      $('td').on('click', '.delete', function(e){
      	id = $(e.target).data('id');
      
      	$('#delete_form').attr('action', '/admin/' + '{{ $dataType->slug }}' + '/' + id);

      	$('#delete_modal').modal('show');
      });

     
    </script>
@stop