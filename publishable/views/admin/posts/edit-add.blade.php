@extends('voyager::master')

@section('css')
<style>

  .panel .mce-panel{
    border-left-color: #fff;
    border-right-color: #fff;
}

.panel .mce-toolbar, .panel .mce-statusbar {
  padding-left: 20px;
}

.panel .mce-edit-area, .panel .mce-edit-area iframe, .panel .mce-edit-area iframe html{
    padding:0px 10px;
    min-height:350px;
}

.mce-content-body {
    color:#555;
    font-size:14px;
}

.panel.is-fullscreen .mce-statusbar {
    position:absolute;
    bottom:0px;
    width:100%;
}

.panel.is-fullscreen .mce-tinymce{
    height:100%;
}

.panel.is-fullscreen .mce-edit-area, .panel.is-fullscreen .mce-edit-area iframe, .panel.is-fullscreen .mce-edit-area iframe html{
    height: 100%;
    position: absolute;
    width: 95%;
    overflow-y: scroll;
    overflow-x: hidden;
    min-height:100%;

}

</style>
@stop

@section('page_header')
<h1 class="page-title">
    <i class="{{ $dataType->icon }}"></i> @if(isset($dataTypeContent->id)){{ 'Edit' }}@else{{ 'New' }}@endif {{ $dataType->display_name_singular }}
</h1>
@stop

@section('content')
<div class="page-content container-fluid">

    <form role="form" action="@if(isset($dataTypeContent->id)){{ '/admin/posts/' . $dataTypeContent->id }}@else{{ '/admin/posts' }}@endif" method="POST" enctype="multipart/form-data">

        <div class="row">

            <div class="col-md-8">

                <!-- ### TITLE ### -->
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-character"></i> Post Title
                            <span class="panel-desc"> The title for your post</span>
                        </h3>
                        <div class="panel-actions">
                            <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <input type="text" class="form-control" name="title" placeholder="Title" value="@if(isset($dataTypeContent->title)){{ $dataTypeContent->title }}@endif">
                    </div>
                </div><!-- .panel -->

                <!-- ### CONTENT ### -->
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon wb-book"></i> Post Content</h3>
                        <div class="panel-actions">
                            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                        </div>
                    </div>
                    <textarea class="richTextBox" name="body" style="border:0px;">@if(isset($dataTypeContent->body)){{ $dataTypeContent->body }}@endif</textarea>
                </div><!-- .panel -->

                <!-- ### EXCERPT ### -->
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Excerpt <small>Small description of this post</small></h3>
                        <div class="panel-actions">
                            <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                      <textarea class="form-control" name="excerpt">@if(isset($dataTypeContent->excerpt)){{ $dataTypeContent->excerpt }}@endif</textarea>
                  </div>
              </div><!-- .panel -->

          </div>


          <div class="col-md-4">

                <!-- ### DETAILS ### -->
                <div class="panel panel panel-bordered panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon wb-clipboard"></i> Post Details</h3>
                        <div class="panel-actions">
                            <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="name">URL slug</label>
                            <input type="text" class="form-control" name="slug" placeholder="slug" value="@if(isset($dataTypeContent->slug)){{ $dataTypeContent->slug }}@endif">
                        </div>
                        <div class="form-group">
                            <label for="name">Post Status</label>
                            <select class="form-control" name="status">
                                <option value="PUBLISHED" @if(isset($dataTypeContent->status) && $dataTypeContent->status == 'PUBLISHED'){{ 'selected="selected"' }}@endif>published</option>
                                <option value="DRAFT" @if(isset($dataTypeContent->status) && $dataTypeContent->status == 'DRAFT'){{ 'selected="selected"' }}@endif>draft</option>
                                <option value="PENDING" @if(isset($dataTypeContent->status) && $dataTypeContent->status == 'PENDING'){{ 'selected="selected"' }}@endif>pending</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Post Category</label>
                            <select class="form-control" name="category_id">
                                @foreach(TCG\Voyager\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" @if(isset($dataTypeContent->category_id) && $dataTypeContent->category_id == $category->id){{ 'selected="selected"' }}@endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Featured</label>
                            <input type="checkbox" name="featured" @if(isset($dataTypeContent->featured) && $dataTypeContent->featured){{ 'checked="checked"' }}@endif>
                        </div>
                    </div>
                </div><!-- .panel -->


                <!-- ### IMAGE ### -->
                <div class="panel panel-bordered panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon wb-image"></i> Post Image</h3>
                        <div class="panel-actions">
                            <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if(isset($dataTypeContent->image))
                        <img src="{{ Voyager::image( $dataTypeContent->image ) }}" style="width:100%" />
                        @endif
                        <input type="file" name="image">
                    </div>
                </div><!-- .panel -->

                <!-- ### SEO CONTENT ### -->
                <div class="panel panel-bordered panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="icon wb-search"></i> SEO Content</h3>
                        <div class="panel-actions">
                            <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="name">Meta Description</label>
                            <textarea class="form-control" name="meta_description">@if(isset($dataTypeContent->meta_description)){{ $dataTypeContent->meta_description }}@endif</textarea>
                        </div>
                        <div class="form-group">
                            <label for="name">Meta Keywords</label>
                            <textarea class="form-control" name="meta_keywords">@if(isset($dataTypeContent->meta_keywords)){{ $dataTypeContent->meta_keywords }}@endif</textarea>
                        </div>
                        <div class="form-group">
                            <label for="name">SEO Title</label>
                            <input type="text" class="form-control" name="seo_title" placeholder="SEO Title" value="@if(isset($dataTypeContent->seo_title)){{ $dataTypeContent->seo_title }}@endif">
                        </div>
                    </div>
                </div><!-- .panel -->

            </div><!-- .col-md-4 -->
        </div><!-- .row -->

        <!-- PUT Method if we are editing -->
        @if(isset($dataTypeContent->id))
        <input type="hidden" name="_method" value="PUT">
        @endif
        <!-- PUT Method if we are editing -->

        <!-- CSRF TOKEN -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <button type="submit" class="btn btn-primary pull-right">@if(isset($dataTypeContent->id)){{ 'Update Post' }}@else<?= '<i class="icon wb-plus-circle"></i> Create New Post'; ?>@endif</button>

    </form>

    <iframe id="form_target" name="form_target" style="display:none"></iframe>
    <form id="my_form" action="/admin/upload" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
        <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>

</div><!-- .container-fluid -->
@stop

@section('javascript')
<script src="{{ config('voyager.assets_path') }}/lib/js/tinymce/tinymce.min.js"></script>
<script src="{{ config('voyager.assets_path') }}/js/voyager_tinymce.js"></script>
@stop