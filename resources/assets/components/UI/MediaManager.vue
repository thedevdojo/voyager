<template>
    <div class="min-h-64 w-full media-manager border rounded-lg p-4 mb-4">
        <input class="hidden" type="file" :multiple="multiple" @change="addUploadFiles($event.target.files)" :accept="accept" ref="upload_input">
        <div class="w-full mb-2" v-if="showToolbar">
            <div class="inline-block">
                <button class="button green" @click="upload()" :disabled="filesToUpload.length == 0">
                    <icon icon="upload"></icon>
                    {{ __('voyager::media.upload') }}
                </button>
                <button class="button blue" @click="selectFilesToUpload()">
                    <icon icon="check-square"></icon>
                    {{ __('voyager::media.select_files') }}
                </button>
                <button class="button blue" @click="loadFiles()">
                    <icon icon="sync"></icon>
                    {{ __('voyager::generic.reload') }}
                </button>
                <button class="button blue" @click="createFolder()">
                    <icon icon="folder-plus"></icon>
                    {{ __('voyager::media.create_folder') }}
                </button>
                <button class="button red" @click="deleteSelected()" v-if="selectedFiles.length > 0">
                    <icon icon="trash"></icon>
                    {{ trans_choice('voyager::media.delete_files', selectedFiles.length) }}
                </button>
            </div>
        </div>
        <div class="w-full mb-2 rounded-md breadcrumbs">
            <div class="button-group">
                <span v-for="(path, i) in pathSegments" :key="'path-'+i" class="flex inline-block items-center">
                    <button class="button py-0">
                        <a href="#" @click.prevent.stop="openPath(path, i)">
                            <icon v-if="path == ''" icon="home"></icon>
                            <span v-else>{{ path }}</span>
                        </a>
                    </button>
                    <button class="button cursor-default px-0 py-0 icon-only" v-if="pathSegments.length !== (i+1)">
                        <icon icon="angle-double-right" class="text-gray-700 dark:text-gray-300"></icon>
                    </button>
                </span>
            </div>
        </div>
        <div class="flex w-full min-h-64">
            <!-- Add max-h-256 overflow-y-scroll to limit the height -->
            <div class="relative flex-grow grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 " @click="selectedFiles = []"  ref="wrapper">
                <div class="absolute flex items-center w-full text-center h-full opacity-75 dragdrop" v-if="((filesToUpload.length == 0 && files.length == 0) || dragging) && !loadingFiles">
                    <h4 class="text-center w-full">{{ dragging ? dropText : dragText }}</h4>
                </div>
                <div class="absolute flex items-center w-full text-center h-full opacity-75 loader" v-if="loadingFiles">
                    <h4 class="text-center w-full">{{ __('voyager::generic.loading') }}</h4>
                </div>
                <div
                    class="item rounded-md border cursor-pointer select-none"
                    v-for="(file, i) in combinedFiles"
                    :key="i"
                    :class="[fileSelected(file) ? 'selected' : '']"
                    v-on:click.prevent.stop="selectFile(file, $event)"
                    v-on:dblclick.prevent.stop="openFile(file)">
                    <div class="flex p-3">
                        <div class="flex-none">
                            <div class="w-full flex justify-center">
                                <img :src="file.preview" class="rounded object-contain h-24 max-w-full" v-if="file.preview" />
                                <img :src="file.file.url" class="rounded object-contain h-24 max-w-full" v-else-if="mimeMatch(file.file.type, 'image/*')" />
                                <div v-else class="w-full flex justify-center h-24">
                                    <icon :icon="getFileIcon(file.file.type)" size="24"></icon>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow ml-3 overflow-hidden">
                            <div class="flex flex-col h-full">
                                <div class="flex-none">
                                    <p class="whitespace-no-wrap" v-tooltip="file.file.name">{{ file.file.name }}</p>
                                    <p class="text-xs" v-if="file.file.type !== 'dir'">{{ readableFileSize(file.file.size) }}</p>
                                </div>
                                <div class="flex items-end justify-end flex-grow">
                                    <button @click.stop="deleteUpload(file)" v-if="file.is_upload">
                                        <icon icon="times" :size="4"></icon>
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div
                        class="flex-none h-1 bg-blue-500 rounded-b-md"
                        v-if="file.status == Status.Uploading"
                        :style="{ width: file.progress+'%' }">
                    </div>
                    <div
                        class="flex-none h-1 w-full bg-green-500 rounded-b-md"
                        v-if="file.status == Status.Finished">
                    </div>
                    <div
                        class="flex-none h-1 w-full bg-red-500 rounded-b-md"
                        v-if="file.status == Status.Failed">
                    </div>
                </div>
            </div>
            <slide-x-right-transition class="flex-none">
                <div class="sidebar h-full border rounded-md p-2 ml-3 max-w-xs" v-if="selectedFiles.length > 0">
                    <div class="w-full flex justify-center">
                        <div v-if="selectedFiles.length > 1" class="w-full flex justify-center h-32">
                            <icon icon="copy" size="32"></icon>
                        </div>
                        <img :src="selectedFiles[0].preview" class="rounded object-contain h-32 max-w-full" v-else-if="selectedFiles[0].preview" />
                        <img :src="selectedFiles[0].file.url" class="rounded object-contain h-32 max-w-full" v-else-if="mimeMatch(selectedFiles[0].file.type, 'image/*')" />
                        <div v-else class="w-full flex justify-center h-32">
                            <icon :icon="getFileIcon(selectedFiles[0].file.type)" size="32"></icon>
                        </div>
                    </div>
                    <div class="w-full flex justify-center">
                        <div v-if="selectedFiles.length == 1">
                            <p>{{ selectedFiles[0].file.name }}</p>
                            <p>{{ __('voyager::media.size') }}: {{ readableFileSize(selectedFiles[0].file.size) }}</p>
                        </div>
                        <div v-else>
                            <p>{{ __('voyager::media.files_selected', { num: selectedFiles.length }) }}</p>
                            <p>{{ __('voyager::generic.approximately') }} {{ readableFileSize(selectedFilesSize) }}</p>
                        </div>
                    </div>
                    
                </div>
            </slide-x-right-transition>
        </div>
    </div>
</template>
<script>
var Status = {
    Pending  : 1,
    Uploading: 2,
    Finished : 3,
    Failed   : 4,
};

Vue.prototype.Status = Status;

export default {
    props: {
        'uploadUrl': {
            type: String,
            required: true,
        },
        'listUrl': {
            type: String,
            required: true,
        },
        'instantUpload': {
            type: Boolean,
            default: true,
        },
        'multiple': {
            type: Boolean,
            default: true,
        },
        'maxSize': {
            type: Number,
            default: 0,
        },
        'multiSelect': {
            type: Boolean,
            default: true,
        },
        'dragText': {
            type: String,
            default: 'Drag your files here',
        },
        'dropText': {
            type: String,
            default: 'Drop it like its 🔥',
        },
        'accept': {
            type: String,
            default: '*/*'
        },
        'showToolbar': {
            type: Boolean,
            default: true,
        }
    },
    data: function () {
        return {
            filesToUpload: [],
            uploading: 0,
            files: [],
            selectedFiles: [],
            path: '',
            ddCapable: true,
            dragging: false,
            dragEnterCounter: 0,
            loadingFiles: false,
        };
    },
    methods: {
        addUploadFiles: function (files) {
            var vm = this;
            vm.filesToUpload = vm.filesToUpload.concat(Array.from(files).map(function (file) {
                // Validate size
                if (vm.maxSize > 0 && (file.size > vm.maxSize)) {
                    return null;
                }

                // Validate mime type
                var matcher = new vm.MimeMatcher(vm.accept);
                if (!matcher.match(file.type)) {
                    return null;
                }

                // Check if file already exists by name AND size
                var exists = false;
                vm.filesToUpload.forEach(function (ex_file) {
                    if (ex_file.file.name == file.name && ex_file.file.size == file.size) {
                        exists = true;
                    }
                });
                if (exists) {
                    return null;
                }

                var f = {
                    file: file,
                    is_upload: true,
                    status: Status.Pending,
                    progress: 0,
                    preview: null,

                }
                // Create FileReader if it is an image
                var matcher = new vm.MimeMatcher('image/*');
                if (matcher.match(file.type)) {
                    let reader  = new FileReader();
                    reader.addEventListener('load', function () {
                        f.preview = reader.result;
                    });
                    reader.readAsDataURL(file);
                }

                return f;
            }).filter(x => !!x));

            if (vm.instantUpload) {
                vm.upload();
            }
        },
        loadFiles: function () {
            var vm = this;
            vm.loadingFiles = true;
            vm.selectedFiles = [];
            axios.post(vm.listUrl, {
                path: vm.path
            })
            .then(function (response) {
                vm.files = response.data;
            })
            .catch(function (response) {
                
            })
            .then(function () {
                // When loaded, clear finished filesToUpload
                vm.loadingFiles = false;
            });
        },
        upload: function () {
            var vm = this;
            vm.filesToUpload.forEach(function (file) {
                vm.uploading++;
                if (file.status == Status.Uploading || file.status == Status.Finished) {
                    return;
                }
                let formData = new FormData();
                formData.append('file', file.file);
                formData.append('path', vm.path);
                axios.post(vm.uploadUrl, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(e) {
                        file.status = Status.Uploading;
                        file.progress = Math.round((e.loaded * 100) / e.total);
                    }
                })
                .then(function (response) {
                    file.status = Status.Finished;
                    file.progress = 100;

                    if (response.data.exists === true) {
                        vm.$notify.notify(
                            vm.__('voyager::media.file_exists', { file: file.file.name }),
                            null, 'red', 7500
                        );
                        file.status = Status.Failed;
                    } else {
                        if (response.data.success === false) {
                            vm.$notify.notify(
                                vm.__('voyager::media.file_upload_failed', { file: file.file.name }),
                                null, 'red', 7500
                            );
                            file.status = Status.Failed;
                        }
                    }
                })
                .catch(function (response) {
                    file.status = Status.Failed;
                    file.progress = 0;

                    if (response.response.status == 413) {
                        vm.$notify.notify(
                            vm.__('voyager::generic.upload_too_large', { file: file.file.name, size: vm.readableFileSize(file.file.size) }),
                            null, 'red', 7500
                        );
                    } else {
                        vm.$notify.notify(
                            vm.__('voyager::generic.upload_failed', { file: file.file.name }) + '<br>' + response.response.statusText,
                            null, 'red', 7500
                        );
                    }
                }).then(function () {
                    vm.uploading--;
                    if (vm.uploading == 0) {
                        vm.loadFiles();
                        vm.filesToUpload = vm.filesToUpload.filter(function (file) {
                            return file.status !== Status.Finished;
                        });
                    }
                });
            });
        },
        selectFilesToUpload: function () {
            this.$refs.upload_input.click();
        },
        selectFile: function (file, e) {
            if (!e.ctrlKey || !this.multiSelect) {
                this.selectedFiles = [];
            }
            this.selectedFiles.push(file);
        },
        fileSelected: function (file) {
            return this.selectedFiles.indexOf(file) >= 0;
        },
        openFile: function (file) {
            if (file.file.type == 'dir') {
                this.path = this.path + '/' + file.file.name;
                this.pushCurrentPathToUrl();
                this.loadFiles();
            }
        },
        deleteUpload: function (file) {
            this.filesToUpload.splice(this.filesToUpload.indexOf(file), 1);
        },
        getFileIcon: function (type) {
            if (type == 'dir') {
                return 'folder';
            } else if (this.mimeMatch(type, 'video/*')) {
                return 'video';
            } else if (this.mimeMatch(type, 'audio/*')) {
                return 'music';
            } else if (this.mimeMatch(type, 'image/*')) {
                return 'image';
            }

            return 'file-alt';
        },
        openPath: function (path, index) {
            this.path = this.pathSegments.slice(0, (index + 1)).join('/');

            // Push path to URL
            this.pushCurrentPathToUrl();

            this.loadFiles();
        },
        pushCurrentPathToUrl: function () {
            var url = window.location.href.split('?')[0];
            url = this.addParameterToUrl('path', this.path, url);
            this.pushToUrlHistory(url);
        },
        deleteSelected: function () {
            var vm = this;
            var files = [];
            vm.selectedFiles.forEach(function (file) {
                files.push(file.file.relative_path + file.file.name);
            });

            vm.$notify.confirm(
                vm.trans_choice('voyager::media.delete_files_confirm', files.length),
                function (response) {
                    if (response) {
                        axios.delete(vm.route('voyager.media.delete'), {
                            params: {
                                files: files,
                            }
                        })
                        .then(function (response) {
                            vm.$notify.notify(
                                vm.trans_choice('voyager::media.delete_files_success', files.length),
                                null,
                                'green',
                                5000
                            );
                        })
                        .catch(function (errors) {
                            //
                        })
                        .then(function () {
                            vm.loadFiles();
                        });
                    }
                },
                false,
                'red',
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            );
        },
        createFolder: function () {
            var vm = this;
            vm.$notify.prompt(vm.__('voyager::media.create_folder_prompt'), '', function (name) {
                axios.post(vm.route('voyager.media.create_folder'), {
                    path: vm.path,
                    name: name,
                })
                .then(function (response) {
                    vm.$notify.notify(
                        vm.__('voyager::media.create_folder_success', { name: name }),
                        null,
                        'green',
                        5000
                    );
                })
                .catch(function (errors) {
                    //
                })
                .then(function () {
                    vm.loadFiles();
                });
            }, 'blue', vm.__('voyager::generic.ok'), vm.__('voyager::generic.cancel'), false, 7500);
        }
    },
    computed: {
        combinedFiles: function () {
            return this.files.concat(this.filesToUpload);
        },
        selectedFilesSize: function () {
            var size = 0;

            this.selectedFiles.forEach(function (file) {
                size += file.file.size;
            });

            return size;
        },
        pathSegments: function () {
            return this.path.split('/');
        }
    },
    mounted: function () {
        var vm = this;

        var div = document.createElement('div');
        vm.ddCapable = (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;

        if (vm.ddCapable) {
            // Prevent browser opening a new tab
            ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(function (event) {
                vm.$refs.wrapper.addEventListener(event, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Indicates that we are dragging files over our wrapper
            ['drag', 'dragstart', 'dragover', 'dragenter'].forEach(function (event) {
                vm.$refs.wrapper.addEventListener(event, function (e) {
                    vm.dragEnterCounter++;
                    vm.dragging = true;
                });
            });

            // Indicates that we left our wrapper or dropped files
            ['dragend', 'dragleave', 'drop'].forEach(function (event) {
                vm.$refs.wrapper.addEventListener(event, function (e) {
                    vm.dragEnterCounter--;
                    if (vm.dragEnterCounter == 0 || vm.combinedFiles.length == 0) {
                        vm.dragging = false;
                    }
                });
            });

            vm.$refs.wrapper.addEventListener('drop', function (e) {
                vm.dragEnterCounter = 0;
                vm.dragging = false;
                vm.addUploadFiles(e.dataTransfer.files);
            });
        }

        var path = vm.getParameterFromUrl('path', '');
        if (path !== '/') {
            vm.path = path;
        }

        vm.loadFiles();
    }
};
</script>

<style scoped>

</style>