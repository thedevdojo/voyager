@section('media-manager')
<div>
    <div id="toolbar">

    </div>
    <div id="content">
        <div class="breadcrumb-container">
            <ol class="breadcrumb filemanager">
                <li class="media_breadcrumb" v-on:click="setCurrentPath(-1)">
                    <span class="arrow"></span>
                    <strong>{{ __('voyager::media.library') }}</strong>
                </li>
                <li v-for="(folder, i) in getCurrentPath()" v-on:click="setCurrentPath(i)">
                    <span class="arrow"></span>
                    @{{ folder }}
                </li>
            </ol>
        </div>
        <div class="flex">
            <div id="left">
                <ul id="files">
                    <li v-for="(file) in files" v-on:click="selectFile(file, $event)" v-on:dblclick="openFile(file, $event)">
                        <div :class="'file_link ' + (isFileSelected(file) ? 'selected' : '')">
                            <div class="link_icon">
                                <template v-if="fileIs(file, 'image')">
                                    <div class="img_icon" :style="imgIcon(file.path)"></div>
                                </template>
                                <template v-else-if="fileIs(file, 'video')">
                                    <i class="icon voyager-video"></i>
                                </template>
                                <template v-else-if="fileIs(file, 'audio')">
                                    <i class="icon voyager-music"></i>
                                </template>
                                <template v-else-if="fileIs(file, 'zip')">
                                    <i class="icon voyager-archive"></i>
                                </template>
                                <template v-else-if="fileIs(file,'folder')">
                                    <i class="icon voyager-folder"></i>
                                </template>
                                <template v-else>
                                    <i class="icon voyager-file-text"></i>
                                </template>
                            </div>
                        </div>
                        <div class="details">
                            <div>
                                <h4>@{{ file.name }}</h4>
                                <small>
                                    <span class="file_size" v-if="!fileIs(file, 'folder')">@{{ bytesToSize(file.size) }}</span>
                                    <span class="num_items" v-else>...</span>
                                </small>
                            </div>
                        </div>
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
    <!-- Image Modal -->
    <div class="modal fade" id="imagemodal" v-if="selectedFileIs('image')">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <img :src="selected_file.path" class="img img-responsive" style="margin: 0 auto;">
                </div>

                <div class="modal-footer text-left">
                    <small class="image-title">@{{ selected_file.name }}</small>
                </div>

            </div>
        </div>
    </div>
    <!-- End Image Modal -->

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
            allowMultiSelect: {
                type: Boolean,
                default: true
            }
        },
        data: function() {
            return {
                current_folder: this.basePath,
		  		selected_files: [],
                files: [],
		  		is_loading: true,
            };
        },
        computed: {
            selected_file: function() {
                
            }
        },
        methods: {
            getFiles: function() {
                var vm = this;
                vm.is_loading = true;
                $.post('{{ route('voyager.media.files') }}', { folder: vm.current_folder, _token: '{{ csrf_token() }}' }, function(data) {
                    vm.files = data.items;
                    vm.selected_files = [];
                    //Todo: Pre-select first file?
					vm.is_loading = false;
				});
            },
            selectFile: function(file, e) {
                if (!e.ctrlKey || !this.allowMultiSelect) {
                    this.selected_files = [];
                }

                this.selected_files.push(file);
            },
            openFile: function(file, e) {
                if (file.type == 'folder') {
                    this.current_folder += "/"+file.name;
                    this.getFiles();
                } else {
                    // ...
                }
            },
            isFileSelected: function(file) {
                return this.selected_files.includes(file);
            },
            fileIs: function(file, type) {
                return file.type.includes(type);
			},
            getCurrentPath: function() {
                var path = this.current_folder.replace(this.basePath, '').split('/').filter(function (el) {
                    return el != '';
                });

                return path;
            },
            setCurrentPath: function(i) {
                var path = this.getCurrentPath();
                path.length = i + 1;
                this.current_folder = '/' + path.join('/');

                this.getFiles();
            },
            bytesToSize: function(bytes) {
				var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
				if (bytes == 0) return '0 Bytes';
				var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
				return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
			},
            imgIcon: function(path) {
				return 'background-size: cover; background-image: url("' + path + '"); background-repeat:no-repeat; background-position:center center;display:inline-block; width:100%; height:100%;';
			},
        },
        mounted: function() {
            this.getFiles();
        },
    });
</script>
