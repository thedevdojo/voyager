module.exports = function(){
	if(document.getElementById('filemanager')){
		var manager = new Vue({
			el: '#filemanager',
			data: {
		  		files: '',
		  		folders: [],
		  		selected_file: '',
		  		directories: [],
			},
			methods: {
				selectedFileIs: function(val){
					if(typeof(this.selected_file.type) != 'undefined' && this.selected_file.type.includes(val)){
						return true;
					}
					return false;
				},
				imgIcon: function(path){
					return 'background-size: cover; background-image: url("' + path + '"); background-repeat:no-repeat; background-position:center center;display:inline-block; width:100%; height:100%;';
				},
				dateFilter: function(date){
					if(!date){
						return null;
					}
					var date = new Date(date * 1000);

					var month = "0" + (date.getMonth() + 1);
					var minutes = "0" + date.getMinutes();
					var seconds = "0" + date.getSeconds();
					
					var dateForamted = date.getFullYear() + '-' + month.substr(-2) + '-' + date.getDate() + ' ' + date.getHours() + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
					
					return dateForamted;
				}
			}
		});


		CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

		var VoyagerMedia = function(o){
			var files = $('#files');
			var defaults = {
				baseUrl: "/admin"
			};
			var options = $.extend(true, defaults, o);
			this.init = function(){
				$("#upload").dropzone({
					url: options.baseUrl+"/media/upload",
					previewsContainer: "#uploadPreview",
					totaluploadprogress: function(uploadProgress, totalBytes, totalBytesSent){
						$('#uploadProgress .progress-bar').css('width', uploadProgress + '%');
						if(uploadProgress == 100){
							$('#uploadProgress').delay(1500).slideUp(function(){
								$('#uploadProgress .progress-bar').css('width', '0%');
							});

						}
					},
					processing: function(){
						$('#uploadProgress').fadeIn();
					},
					sending: function(file, xhr, formData) {
						formData.append("_token", CSRF_TOKEN);
						formData.append("upload_path", manager.files.path);
					},
					success: function(e, res){
						if(res.success){
							toastr.success(res.message, "Sweet Success!");
						} else {
							toastr.error(res.message, "Whoopsie!");
						}
					},
					error: function(e, res, xhr){
						toastr.error(res, "Whoopsie");
					},
					queuecomplete: function(){
						getFiles(manager.folders);
					}
				});

				getFiles('/');


				files.on("dblclick", "li .file_link", function(){
					var type = manager.selected_file.type;

					var imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
					
					if (imageMimeTypes.indexOf(type) > -1) {
						$('#imagemodal').modal('show');   
						return false;
					}
					
					if (type !== "folder") {
						return false;
					}
					
					manager.folders.push(manager.selected_file.name);
					getFiles(manager.folders);
				});

				files.on("click", "li", function(e){
					var clicked = e.target;
					if(!$(clicked).hasClass('file_link')){
						clicked = $(e.target).closest('.file_link');
					}
					setCurrentSelected(clicked);
				});

				$('.breadcrumb').on("click", "li.media_breadcrumb", function(){
					var index = $(this).data('index');
					
					manager.folders = manager.folders.splice(0, index);
					getFiles(manager.folders);
				});

				$('.breadcrumb-container .toggle').click(function(){
					$('.flex #right').toggle();
					var toggle_text = $('.breadcrumb-container .toggle span').text();
					$('.breadcrumb-container .toggle span').text(toggle_text == "Close" ? "Open" : "Close");
					$('.breadcrumb-container .toggle .icon').toggleClass('fa-toggle-right').toggleClass('fa-toggle-left');
				});

				
				//********** Add Keypress Functionality **********//
				var isBrowsingFiles = null,
				fileBrowserActive = function(el){
					el = el instanceof jQuery ? el : $(el);
					if ($.contains(files.parent()[0], el[0])) {
						return true;
					} else {
						//$(document).off('click');
						return false;
					}
				},
				handleFileBrowserStatus = function (target) {
					isBrowsingFiles = fileBrowserActive(target);
				};

				files.on('click', function (event) {
					if (! isBrowsingFiles) {
						files.on('click', function (e) {
							handleFileBrowserStatus(e.target);
						});
					} else {
						handleFileBrowserStatus(event.target);
					}
				});

				$(document).keydown(function(e) {
					if(!$('.modal').hasClass('in')){
						var isKeyControl = e.which >= 37 && e.which <= 40;
						if (! isBrowsingFiles && isKeyControl) {
							return false;
						} else if (isKeyControl && isBrowsingFiles) {
							e.preventDefault();
						}
						var curSelected = $('#files li .selected').data('index');
						// left key
						if( (e.which == 37 || e.which == 38) && parseInt(curSelected)) {
							newSelected = parseInt(curSelected)-1;
							setCurrentSelected( $('*[data-index="'+ newSelected + '"]') );
						}
						// right key
						if( (e.which == 39 || e.which == 40) && parseInt(curSelected) < manager.files.items.length-1 ) {
							newSelected = parseInt(curSelected)+1;
							setCurrentSelected( $('*[data-index="'+ newSelected + '"]') );
						}
						// enter key
						if(e.which == 13) {
							if (!$('#new_folder_modal').is(':visible') && !$('#move_file_modal').is(':visible') && !$('#confirm_delete_modal').is(':visible') ) {
								var type = manager.selected_file.type;

								var imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
						
								if (imageMimeTypes.indexOf(type) > -1) {
									$('#imagemodal').modal('show');   
									return false;
								}
								
								if (type !== "folder") {
									return false;
								}
								
								manager.folders.push(manager.selected_file.name);
								getFiles(manager.folders);
							}
							if($('#confirm_delete_modal').is(':visible')){
								$('#confirm_delete').trigger('click');
							}
						}
						// backspace key
						if(e.which == 8) {
							var index =  manager.folders.length - 1;
							manager.folders = manager.folders.splice(0, index);
							getFiles(manager.folders);
						}
					}
				});
				
				//********** End Keypress Functionality **********//


				/********** TOOLBAR BUTTONS **********/
				$('#refresh').click(function(){
					getFiles(manager.folders);
				});

				$('#new_folder_modal').on('shown.bs.modal', function() {
					$("#new_folder_name").focus();
				});

				$('#new_folder_name').keydown(function(e) {
					if(e.which == 13) {
						$('#new_folder_submit').trigger('click');
					}
				});

				$('#move_file_modal').on('hidden.bs.modal', function () {
					$("#s2id_move_folder_dropdown").select2("close");
				});

				$('#new_folder_submit').click(function(){
					new_folder_path = manager.files.path + '/' + $('#new_folder_name').val();
					$.post(options.baseUrl+'/media/new_folder', { new_folder: new_folder_path, _token: CSRF_TOKEN }, function(data){
						if(data.success == true){
							toastr.success('successfully created ' + $('#new_folder_name').val(), "Sweet Success!");
							getFiles(manager.folders);
						} else {
							toastr.error(data.error, "Whoops!");
						}
						$('#new_folder_name').val('');
						$('#new_folder_modal').modal('hide');
					});
				});

				$('#delete').click(function(){
					if(manager.selected_file.type == 'directory'){
						$('.folder_warning').show();
					} else {
						$('.folder_warning').hide();
					}
					$('.confirm_delete_name').text(manager.selected_file.name);
					$('#confirm_delete_modal').modal('show');
				});

				$('#confirm_delete').click(function(){

					$.post(options.baseUrl+'/media/delete_file_folder', { folder_location: manager.folders, file_folder: manager.selected_file.name, type: manager.selected_file.type, _token: CSRF_TOKEN }, function(data){
						if(data.success == true){
							toastr.success('successfully deleted ' + manager.selected_file.name, "Sweet Success!");
							getFiles(manager.folders);
							$('#confirm_delete_modal').modal('hide');
						} else {
							toastr.error(data.error, "Whoops!");
						}
					});
				});

				$('#move').click(function(){
					$('#move_file_modal').modal('show');
				});

				$('#rename').click(function(){
					if(typeof(manager.selected_file) !== 'undefined'){
						$('#rename_file').val(manager.selected_file.name);
					}
					$('#rename_file_modal').modal('show');
				});

				$('#move_folder_dropdown').keydown(function(e) {
					if(e.which == 13) {
						$('#move_btn').trigger('click');
					}
				});

				$('#move_btn').click(function(){
					source = manager.selected_file.name;
					destination = $('#move_folder_dropdown').val() + '/' + manager.selected_file.name;
					$('#move_file_modal').modal('hide');
					$.post(options.baseUrl+'/media/move_file', { folder_location: manager.folders, source: source, destination: destination, _token: CSRF_TOKEN }, function(data){
						if(data.success == true){
							toastr.success('Successfully moved file/folder', "Sweet Success!");
							getFiles(manager.folders);
						} else {
							toastr.error(data.error, "Whoops!");
						}
					});
				});

				$('#rename_btn').click(function(){
					source = manager.selected_file.path;
					filename = manager.selected_file.name;
					new_filename = $('#new_filename').val();
					$('#rename_file_modal').modal('hide');
					$.post(options.baseUrl+'/media/rename_file', { folder_location: manager.folders, filename: filename, new_filename: new_filename, _token: CSRF_TOKEN }, function(data){
						if(data.success == true){
							toastr.success('Successfully renamed file/folder', "Sweet Success!");
							getFiles(manager.folders);
						} else {
							toastr.error(data.error, "Whoops!");
						}
					});
				});
				
				// Crop Image
				$('#crop').click(function(){
					// Cleanup the previous cropper
					if (typeof cropper !== 'undefined' && cropper instanceof Cropper) {
						cropper.destroy();
					}
					$('#confirm_crop_modal').modal('show');
				});

				// Cropper must init after the modal shown
				$('#confirm_crop_modal').on('shown.bs.modal', function (e) {
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

				$('#crop_btn').click(function(){
					if (window.confirm($(this).data('confirm'))) {
						cropImage(false);
					}
				});

				$('#crop_and_create_btn').click(function(){
					cropImage(true);
				});

				// $('#upload').click(function(){
				// 	$('#upload_files_modal').modal('show');
				// });
				/********** END TOOLBAR BUTTONS **********/

				manager.$watch('files', function (newVal, oldVal) {
					setCurrentSelected( $('*[data-index="0"]') );
					$('#filemanager #content #files').hide();
					$('#filemanager #content #files').fadeIn('fast');
					$('#filemanager .loader').fadeOut(function(){

						$('#filemanager #content').fadeIn();
					});

					if(newVal.items.length < 1){
						$('#no_files').show();
					} else {
						$('#no_files').hide();
					}
				});

				manager.$watch('directories', function (newVal, oldVal) {
					if($("#move_folder_dropdown").select2()){
						$("#move_folder_dropdown").select2('destroy');
					}
					$("#move_folder_dropdown").select2();
				});

				manager.$watch('selected_file', function (newVal, oldVal) {
					if(typeof(newVal) == 'undefined'){
						$('.right_details').hide();
						$('.right_none_selected').show();
						$('#move').attr('disabled', true);
						$('#delete').attr('disabled', true);
					} else {
						$('.right_details').show();
						$('.right_none_selected').hide();
						$('#move').removeAttr("disabled");
						$('#delete').removeAttr("disabled");
					}
				});

				function getFiles(folders){
					if(folders != '/'){
						var folder_location = '/' + folders.join('/');
					} else {
						var folder_location = '/';
					}
					$('#file_loader').fadeIn();
					$.post(options.baseUrl+'/media/files', { folder:folder_location, _token: CSRF_TOKEN, _token: CSRF_TOKEN }, function(data) {
						$('#file_loader').hide();
						manager.files = data;
						files.trigger('click');
						for(var i=0; i < manager.files.items.length; i++){
							if(typeof(manager.files.items[i].size) != undefined){
								manager.files.items[i].size = bytesToSize(manager.files.items[i].size);
							}
						}
					});

					// Add the latest files to the folder dropdown
					var all_folders = '';
					$.post(options.baseUrl+'/media/directories', { folder_location:manager.folders, _token: CSRF_TOKEN }, function(data){
						//console.log(data);
						manager.directories = data;
					});

				}

				function setCurrentSelected(cur){
					$('#files li .selected').removeClass('selected');
					$(cur).addClass('selected');
					manager.selected_file = manager.files.items[$(cur).data('index')];
				}

				function bytesToSize(bytes) {
					var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
					if (bytes == 0) return '0 Bytes';
					var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
					return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
				}
				
				function cropImage(createMode) {
					croppedData.originImageName = manager.selected_file.name
					croppedData.upload_path = '/' + manager.folders.join('/')
					croppedData.createMode = createMode
	
					var postData = Object.assign(croppedData, {_token: CSRF_TOKEN})
					$.post(options.baseUrl+'/media/crop', postData, function(data){
						console.log(data)
						if(data.success == true){
							toastr.success(data.message)
							getFiles(manager.folders);
							$('#confirm_crop_modal').modal('hide');
						} else {
							toastr.error(data.error, "Whoops!");
						}
					});
				}
				
				
			}
		};

		var media = new VoyagerMedia({
		    baseUrl: document.getElementById('base_url').value
		});
		$(function () {
		    media.init();
		});

	}
}