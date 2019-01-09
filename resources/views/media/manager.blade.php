@section('media-manager')
<div>
    <div id="toolbar">
        <div class="btn-group offset-right">
            <button type="button" class="btn btn-primary" id="upload" v-if="allowUpload">
                <i class="voyager-upload"></i>
                {{ __('voyager::generic.upload') }}
            </button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create_dir_modal">
                <i class="voyager-folder"></i>
                {{ __('voyager::generic.add_folder') }}
            </button>
        </div>
        <button type="button" class="btn btn-default" v-on:click="getFiles()">
            <i class="voyager-refresh"></i>
        </button>
        <div class="btn-group offset-right">
            <button type="button" v-if="showFolders && allowMove" class="btn btn-default" data-toggle="modal" data-target="#move_files_modal">
                <i class="voyager-move"></i>
                {{ __('voyager::generic.move') }}
            </button>
            <button type="button" v-if="allowDelete" class="btn btn-default" data-toggle="modal" data-target="#confirm_delete_modal">
                <i class="voyager-trash"></i>
                {{ __('voyager::generic.delete') }}
            </button>
            <button v-if="allowCrop" :disabled="selected_files.length != 1 || !fileIs(selected_file, 'image')" type="button" class="btn btn-default" data-toggle="modal" data-target="#crop_modal">
                <i class="voyager-crop"></i>
                {{ __('voyager::media.crop') }}
            </button>
        </div>
    </div>
    <div id="uploadPreview" style="display:none;" v-if="allowUpload"></div>
    <div id="uploadProgress" class="progress active progress-striped" v-if="allowUpload">
        <div class="progress-bar progress-bar-success" style="width: 0"></div>
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
                    <li v-for="(file) in files" v-on:click="selectFile(file, $event)" v-on:dblclick="openFile(file)" v-if="filter(file)">
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
                                <template v-else-if="fileIs(file, 'folder')">
                                    <i class="icon voyager-folder"></i>
                                </template>
                                <template v-else>
                                    <i class="icon voyager-file-text"></i>
                                </template>
                            </div>
                            <div class="details">
                                <div :class="file.type">
                                    <h4>@{{ file.name }}</h4>
                                    <small v-if="!fileIs(file, 'folder')">
                                        <span class="file_size">@{{ bytesToSize(file.size) }}</span>
                                    </small>
                                </div>
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
                    <div v-if="selected_files.length > 1" class="right_none_selected">
                        <i class="voyager-list"></i>
                        <p>@{{ selected_files.length }} {{ __('voyager::media.files_selected') }}</p>
                    </div>
                    <div v-else-if="selected_files.length == 1" class="right_details">
                        <div class="detail_img">
                            <div v-if="fileIs(selected_file, 'image')">
                                <img :src="selected_file.path" />
                            </div>
                            <div v-else-if="fileIs(selected_file, 'video')">
                                <video width="100%" height="auto" ref="videoplayer" controls>
                                    <source :src="selected_file.path" type="video/mp4">
                                    <source :src="selected_file.path" type="video/ogg">
                                    <source :src="selected_file.path" type="video/webm">
                                    {{ __('voyager::media.browser_video_support') }}
                                </video>
                            </div>
                            <div v-else-if="fileIs(selected_file, 'audio')">
                                <i class="voyager-music"></i>
                                <audio controls style="width:100%; margin-top:5px;" ref="audioplayer">
                                    <source :src="selected_file.path" type="audio/ogg">
                                    <source :src="selected_file.path" type="audio/mpeg">
                                    {{ __('voyager::media.browser_audio_support') }}
                                </audio>
                            </div>
                            <div v-else-if="fileIs(selected_file, 'zip')">
                                <i class="voyager-archive"></i>
                            </div>
                            <div v-else-if="fileIs(selected_file, 'folder')">
                                <i class="voyager-folder"></i>
                            </div>
                            <div v-else>
                                <i class="voyager-file-text"></i>
                            </div>
                        </div>
                        <div class="detail_info">
                            <span>
                                <h4>{{ __('voyager::media.title') }}:</h4>
                                <input v-if="allowRename" type="text" class="form-control" :value="selected_file.name" @keyup.enter="renameFile">
                                <p v-else>@{{ selected_file.name }}</p>
                            </span>
                            <span>
                                <h4>{{ __('voyager::media.type') }}:</h4>
                                <p>@{{ selected_file.type }}</p>
                            </span>

                            <template v-if="!fileIs(selected_file, 'folder')">
                                <span>
                                    <h4>{{ __('voyager::media.size') }}:</h4>
                                    <p><span class="selected_file_size">@{{ bytesToSize(selected_file.size) }}</span></p>
                                </span>
                                <span>
                                    <h4>{{ __('voyager::media.public_url') }}:</h4>
                                    <p><a :href="selected_file.path" target="_blank">{{ __('voyager::generic.click_here') }}</a></p>
                                </span>
                                <span>
                                    <h4>{{ __('voyager::media.last_modified') }}:</h4>
                                    <p>@{{ dateFilter(selected_file.last_modified) }}</p>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div v-else class="right_none_selected">
                        <i class="voyager-cursor"></i>
                        <p>{{ __('voyager::media.nothing_selected') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imagemodal" v-if="selected_file && fileIs(selected_file, 'image')">
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

    <!-- New Folder Modal -->
    <div class="modal fade modal-info" id="create_dir_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-folder"></i> {{ __('voyager::media.add_new_folder') }}</h4>
                </div>

                <div class="modal-body">
                    <input name="new_folder_name" placeholder="{{ __('voyager::media.new_folder_name') }}" class="form-control" value=""/>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-info" v-on:click="createFolder">{{ __('voyager::media.create_new_folder') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End New Folder Modal -->

    <!-- Delete File Modal -->
    <div class="modal fade modal-danger" id="confirm_delete_modal" v-if="allowDelete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }}</h4>
                    <ul>
                        <li v-for="file in selected_files">@{{ file.name }}</li>
                    </ul>
                    <h5 class="folder_warning">
                        <i class="voyager-warning"></i> {{ __('voyager::media.delete_folder_question') }}
                    </h5>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" v-on:click="deleteFiles">{{ __('voyager::generic.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->

    <!-- Move Files Modal -->
    <div class="modal fade modal-warning" id="move_files_modal" v-if="allowMove">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-move"></i> {{ __('voyager::media.move_file_folder') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::media.destination_folder') }}</h4>
                    <select class="form-control">
                        <option v-if="current_folder != basePath && showFolders" value="/../">../</option>
                        <option v-for="file in files" v-if="file.type == 'folder' && !selected_files.includes(file)" :value="file.name">@{{ file.name }}</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-warning" v-on:click="moveFiles">{{ __('voyager::generic.move') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Move File Modal -->

    <!-- Crop Image Modal -->
    <div class="modal fade modal-warning" id="crop_modal" v-if="allowCrop">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::media.crop_image') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="crop-container">
                        <img id="cropping-image" v-if="selected_files.length == 1 && fileIs(selected_file, 'image')" class="img img-responsive" :src="selected_file.path + '?' + selected_file.last_modified" />
                    </div>
                    <div class="new-image-info">
                        {{ __('voyager::media.width') }} <span id="new-image-width"></span>, {{ __('voyager::media.height') }}<span id="new-image-height"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-warning" v-on:click="crop(false)">{{ __('voyager::media.crop') }}</button>
                    <button type="button" class="btn btn-warning" v-on:click="crop(true)">{{ __('voyager::media.crop_and_create') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Crop Image Modal -->
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
            },
            allowFolderSelect: {
                type: Boolean,
                default: true,
            },
            maxSelectedFiles: {
                type: Number,
                default: 0
            },
            showFolders: {
                type: Boolean,
                default: true
            },
            allowUpload: {
                type: Boolean,
                default: true
            },
            allowMove: {
                type: Boolean,
                default: true
            },
            allowDelete: {
                type: Boolean,
                default: true
            },
            allowCreateFolder: {
                type: Boolean,
                default: true
            },
            allowRename: {
                type: Boolean,
                default: true
            },
            allowCrop: {
                type: Boolean,
                default: true
            },
            allowCreateFolder: {
                type: Boolean,
                default: true
            },
            allowedTypes: {
                type: Array,
                default: function() {
                    return []
                }
            },
            preSelect: {
                type: Boolean,
                default: true,
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
                return this.selected_files[0];
            }
        },
        methods: {
            getFiles: function() {
                var vm = this;
                vm.is_loading = true;
                $.post('{{ route('voyager.media.files') }}', { folder: vm.current_folder, _token: '{{ csrf_token() }}' }, function(data) {
                    vm.files = [];
                    for (var i = 0, file; file = data[i]; i++) {
                        if (vm.filter(file)) {
                            vm.files.push(file);
                        }
                    }
                    vm.selected_files = [];
                    if (vm.preSelect && data.length > 0) {
                        vm.selected_files.push(data[0]);
                    }
					vm.is_loading = false;
				});
            },
            selectFile: function(file, e) {
                if ((!e.ctrlKey && !e.shiftKey) || !this.allowMultiSelect) {
                    this.selected_files = [];
                }

                if (e.shiftKey && this.allowMultiSelect && this.selected_files.length == 1) {
                    var start = 0;
                    for (var i = 0, cfile; cfile = this.files[i]; i++) {
                        if (cfile === this.selected_file) {
                            start = i;
                            break;
                        }
                    }

                    var end = 0;
                    for (var i = 0, cfile; cfile = this.files[i]; i++) {
                        if (cfile === file) {
                            end = i;
                            break;
                        }
                    }

                    for (var i = start; i < end; i++) {
                        if (this.maxSelectedFiles > 0 && this.selected_files.length > this.maxSelectedFiles) {
                            toastr.error("You can select a maximum of "+this.maxSelectedFiles+" files");
                            return;
                        }
                        if (!this.allowFolderSelect && this.files[i].type == 'folder') {
                            toastr.error("You can't select a folder");
                            return;
                        }
                        this.selected_files.push(this.files[i]);
                    }
                }

                if (this.maxSelectedFiles > 0 && this.selected_files.length >= this.maxSelectedFiles) {
                    toastr.error("You can select a maximum of "+this.maxSelectedFiles+" files");
                    return;
                }

                if (!this.allowFolderSelect && file.type == 'folder') {
                    toastr.error("You can't select a folder");
                    return;
                }

                this.selected_files.push(file);

                if (this.selected_files.length == 1) {
                    var vm = this;
                    Vue.nextTick(function () {
                        if (vm.fileIs(vm.selected_file, 'video')) {
                            vm.$refs.videoplayer.load();
                        } else if (vm.fileIs(vm.selected_file, 'audio')) {
                            vm.$refs.audioplayer.load();
                        }
                    });
                }
            },
            openFile: function(file) {
                if (file.type == 'folder') {
                    this.current_folder += "/"+file.name;
                    this.getFiles();
                } else if (this.fileIs(this.selected_file, 'image')) {
                    $('#imagemodal').modal('show');
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
                if (i == -1) {
                    this.current_folder = this.basePath;
                } else {
                    var path = this.getCurrentPath();
                    path.length = i + 1;
                    this.current_folder = this.basePath+path.join('/');
                }

                this.getFiles();
            },
            filter: function(file) {
                if (this.allowedTypes.length > 0) {
                    if (file.type != 'folder') {
                        for (var i = 0, type; type = this.allowedTypes[i]; i++) {
                            if (file.type.includes(type)) {
                                return true;
                            }
                        }
                    }
                }

                if (file.type == 'folder' && this.showFolders) {
                    return true;
                }

                if (this.allowedTypes.length == 0) {
                    return true;
                }

                return false;
            },
            renameFile: function(object) {
                if (!this.allowRename) {
                    return;
                }
                var vm = this;
                $.post('{{ route('voyager.media.rename') }}', {
                    folder_location: vm.getCurrentPath(),
                    filename: vm.selected_file.name,
                    new_filename: object.target.value,
                    _token: '{{ csrf_token() }}'
                }, function(data){
					if (data.success == true) {
						toastr.success('Successfully renamed file/folder', "Sweet Success!");
						vm.getFiles();
					} else {
						toastr.error(data.error, "Whoops!");
					}
				});
            },
            createFolder: function(e) {
                if (!this.allowCreateFolder) {
                    return;
                }
                var vm = this;
                var name = $(e.path).parent('.modal-content').find('input').first().val();
                $.post('{{ route('voyager.media.new_folder') }}', { new_folder: vm.current_folder+'/'+name, _token: '{{ csrf_token() }}' }, function(data) {
					if(data.success == true){
						toastr.success('Successfully created ' + name, "Sweet Success!");
						vm.getFiles();
					} else {
						toastr.error(data.error, "Whoops!");
					}
					$(e.path).parent('.modal-content').find('input').first().val('');
					$('#create_dir_modal').modal('hide');
				});
            },
            deleteFiles: function() {
                if (!this.allowDelete) {
                    return;
                }
                var vm = this;
                $.post('{{ route('voyager.media.delete') }}', {
                    path: vm.current_folder,
                    files: vm.selected_files,
                    _token: '{{ csrf_token() }}'
                }, function(data){
					if(data.success == true){
						toastr.success('', "Sweet Success!");
						vm.getFiles();
						$('#confirm_delete_modal').modal('hide');
					} else {
						toastr.error(data.error, "Whoops!");
                        vm.getFiles();
						$('#confirm_delete_modal').modal('hide');
					}
				});
            },
            moveFiles: function(e) {
                if (!this.allowMove) {
                    return;
                }
                var vm = this;
                var destination = $(e.path).parent('.modal-content').find('select').first().val();
                $('#move_files_modal').modal('hide');
				$.post('{{ route('voyager.media.move') }}', {
                    path: vm.current_folder,
                    files: vm.selected_files,
                    destination: destination,
                    _token: '{{ csrf_token() }}'
                }, function(data){
					if(data.success == true){
						toastr.success('Successfully moved file/folder', "Sweet Success!");
						vm.getFiles();
					} else {
						toastr.error(data.error, "Whoops!");
					}
				});
            },
            crop: function(mode) {
                if (!this.allowCrop) {
                    return;
                }
                if (!mode) {
                    if (!window.confirm('{{ __('voyager::media.crop_override_confirm') }}')) {
						return;
					}
                }

                croppedData.originImageName = this.selected_file.name;
				croppedData.upload_path = this.current_folder;
				croppedData.createMode = mode;

                var vm = this;
				var postData = Object.assign(croppedData, { _token: '{{ csrf_token() }}' })
				$.post('{{ route('voyager.media.crop') }}', postData, function(data) {
					if (data.success) {
						toastr.success(data.message);
						vm.getFiles();
						$('#crop_modal').modal('hide');
					} else {
						toastr.error(data.error, "Whoops!");
					}
				});
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
            dateFilter: function(date) {
                if (!date) {
                    return null;
                }
                var date = new Date(date * 1000);

                var month = "0" + (date.getMonth() + 1);
                var minutes = "0" + date.getMinutes();
                var seconds = "0" + date.getSeconds();

                var dateFormated = date.getFullYear() + '-' + month.substr(-2) + '-' + date.getDate() + ' ' + date.getHours() + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

                return dateFormated;
            },
        },
        mounted: function() {
            this.getFiles();
            var vm = this;

            //Key events
            window.onkeydown = function(evt) {
                evt = evt || window.event;
                if (evt.keyCode == 39) {
                    for (var i = 0, file; file = vm.files[i]; i++) {
                        if (file === vm.selected_file) {
                            i = i + 1; // increase i by one
                            i = i % vm.files.length;
                            vm.selectFile(vm.files[i], evt);
                            break;
                        }
                    }
                } else if (evt.keyCode == 37) {
                    for (var i = 0, file; file = vm.files[i]; i++) {
                        if (file === vm.selected_file) {
                            if (i === 0) {
                                i = vm.files.length;
                            }
                            i = i - 1;
                            vm.selectFile(vm.files[i], evt);
                            break;
                        }
                    }
                } else if (evt.keyCode == 13) {
                    vm.openFile(vm.selected_file, null);
                }
            };
            //Dropzone
            if (this.allowUpload) {
                $("#upload").dropzone({
                    timeout: 180000,
                    url: '{{ route('voyager.media.upload') }}',
                    previewsContainer: "#uploadPreview",
                    totaluploadprogress: function(uploadProgress, totalBytes, totalBytesSent) {
                        $('#uploadProgress .progress-bar').css('width', uploadProgress + '%');
    					if (uploadProgress == 100) {
    						$('#uploadProgress').delay(1500).slideUp(function(){
    							$('#uploadProgress .progress-bar').css('width', '0%');
    						});
    					}
                    },
                    processing: function(){
                        $('#uploadProgress').fadeIn();
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("_token", '{{ csrf_token() }}');
                        formData.append("upload_path", vm.getCurrentPath());
                    },
                    success: function(e, res) {
                        if (res.success) {
                            toastr.success(res.message, "Sweet Success!");
                        } else {
                            toastr.error(res.message, "Whoopsie!");
                        }
                    },
                    error: function(e, res, xhr) {
                        toastr.error(res, "Whoopsie");
                    },
                    queuecomplete: function() {
                        vm.getFiles();
                    }
                });
            }

            //Cropper
            if (this.allowCrop) {
                $('#crop_modal').on('shown.bs.modal', function (e) {
                    if (typeof cropper !== 'undefined' && cropper instanceof Cropper) {
    					cropper.destroy();
    				}
    				var croppingImage = document.getElementById('cropping-image');
    				cropper = new Cropper(croppingImage, {
    					crop: function(e) {
    						document.getElementById('new-image-width').innerText = Math.round(e.detail.width) + 'px';
    						document.getElementById('new-image-height').innerText = Math.round(e.detail.height) + 'px';
    						croppedData = {
    							x: Math.round(e.detail.x),
    							y: Math.round(e.detail.y),
    							height: Math.round(e.detail.height),
    							width: Math.round(e.detail.width)
    						};
    					}
    				});
    			});
            }
        },
    });
</script>
