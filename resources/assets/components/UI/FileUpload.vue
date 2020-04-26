<template>
    <div class="flex min-h-64 w-full bg-gray-850 border-gray-700 border rounded-lg p-4 mb-4">
        <input class="hidden" type="file" :multiple="multiple" @change="addUploadFiles($event.target.files)" :accept="accept" ref="upload_input">
        <div class="flex w-full">
            <!-- Add max-h-256 overflow-y-scroll to limit the height -->
            <div class="relative flex-grow grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 " @click="selectedFiles = []"  ref="wrapper">
                <div class="absolute flex items-center w-full text-center h-full opacity-75 bg-gray-850" v-if="(filesToUpload.length == 0 && files.length == 0) || dragging">
                    <h4 class="text-center w-full opacity-100">{{ dragging ? dropText : dragText }}</h4>
                </div>
                <div
                    class="bg-gray-800 hover:bg-gray-750 rounded-md border-gray-700 border cursor-pointer"
                    v-for="(file, i) in combinedFiles"
                    :key="i"
                    :class="[fileSelected(file) ? 'border-blue-700' : '']"
                    @click="selectFile(file, $event)"
                    @dblclick="openFile(file, $event)">
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
                <div class="h-full border border-gray-700 rounded-md p-2 ml-3 max-w-xs" v-if="selectedFiles.length > 0">
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
                            <p>SIze: {{ readableFileSize(selectedFiles[0].file.size) }}</p>
                        </div>
                        <span v-else>
                            {{ selectedFiles.length }} files selected
                        </span>
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
            default: false,
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
    },
    data: function () {
        return {
            filesToUpload: [],
            files: [],
            selectedFiles: [],
            path: '',
            ddCapable: true,
            dragging: false,
            dragEnterCounter: 0,
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
            });
        },
        upload: function () {
            var vm = this;
            vm.filesToUpload.forEach(function (file) {
                if (file.status == Status.Uploading || file.status == Status.Finished) {
                    return;
                }
                let formData = new FormData();
                formData.append('file', file.file);
                axios.post(vm.uploadUrl, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(e) {
                        file.status = Status.Uploading;
                        file.progress = Math.round((e.loaded * 100) / e.total);
                    }
                })
                .then(function () {
                    file.status = Status.Finished;
                    file.progress = 100;
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
                });
            });
        },
        selectFilesToUpload: function () {
            this.$refs.upload_input.click();
        },
        selectFile: function (file, e) {
            e.preventDefault();
            e.stopPropagation();
            if (!e.ctrlKey || !this.multiSelect) {
                this.selectedFiles = [];
            }
            this.selectedFiles.push(file);
        },
        fileSelected: function (file) {
            return this.selectedFiles.indexOf(file) >= 0;
        },
        openFile: function (file, e) {
            e.preventDefault();
            e.stopPropagation();
            if (file.file.type == 'dir') {
                this.path = this.path + '/' + file.file.name;
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
        }
    },
    computed: {
        combinedFiles: function () {
            return this.files.concat(this.filesToUpload);
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
                    if (vm.dragEnterCounter == 0) {
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

        vm.loadFiles();
    }
};
</script>

<style scoped>

</style>