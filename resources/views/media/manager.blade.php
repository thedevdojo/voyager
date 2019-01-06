@section('media-manager')
<div>
    <div id="toolbar">

    </div>
    <div id="content">
        <div class="breadcrumb-container">
            <ol class="breadcrumb filemanager">
                <li class="media_breadcrumb" data-folder="/" data-index="0">
                    <span class="arrow"></span>
                    <strong>{{ __('voyager::media.library') }}</strong>
                </li>
                <template v-for="i in 10">
                    <li><span class="arrow"></span>@{{ i }}</li>
                </template>
            </ol>
        </div>
        <div class="flex">
            <div id="left">
                <ul id="files">
                    <li v-for="(file) in files">

                    </li>
                </ul>
                <div id="file_loader" v-if="is_loading">
                    <?php $admin_loader_img = Voyager::setting('admin.loader', ''); ?>
                    @if($admin_loader_img == '')
                    <img src="{{ voyager_asset('images/logo-icon.png') }}" alt="Voyager Loader">
                    @else
                    <img src="{{ Voyager::image($admin_loader_img) }}" alt="Voyager Loader">
                    @endif
                    <p>{{ __('voyager::media.loading') }}</p>
                </div>

                <div id="no_files" v-if="files.length == 0">
                    <h3><i class="voyager-meh"></i> {{ __('voyager::media.no_files_in_folder') }}</h3>
                </div>
            </div>
            <div id="right">
                <div class="right_details">
                    <div class="detail_img">
                        Hello
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    Vue.component('media-manager', {
        template: `@yield('media-manager')`,
        props: {
            basePath: {
                type: String,
                default: '/'
            },
        },
        data: function() {
            return {
		  		selected_files: [],
                files: [],
		  		is_loading: true,
            };
        },
        methods: {
            getFiles: function() {
                this.is_loading = false;
            },
            selectFile: function(file) {

            },
            openFile: function(file) {

            },
            selectedFileIs: function(file, type) {

			},
            bytesToSize: function(bytes) {
				var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
				if (bytes == 0) return '0 Bytes';
				var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
				return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
			}
        },
        mounted: function() {
            this.getFiles();
        },
    });
</script>
