@extends('voyager::app')
@section('page-title', __('voyager::generic.media'))
@section('content')
<card title="{{ __('voyager::generic.media') }}">
    <div class="mt-4">
        <media-manager
            :upload-url="route('voyager.media.upload')"
            :list-url="route('voyager.media.list')"
            :drag-text="__('voyager::media.drag_files_here')"
            :drop-text="__('voyager::media.drop_files')"
            ref="media">
        </media-manager>
    </div>
</card>
@endsection
