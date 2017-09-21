/**
 * Youtube search - a TinyMCE youtube search and place plugin
 * youtube/js/youtube.js
 *
 * This is not free software
 *
 * Plugin info: http://www.cfconsultancy.nl/
 * Author: Ceasar Feijen
 *
 * Version: 2.0 released 14/08/2014
 */

	//Set output to 'iframe' or 'placeholder'
	var youtput = 'iframe';
	//Set to http or https
	var secure = 'https';
	//Max results per request
	var max = 30;
	//Api key 3 version (https://console.developers.google.com/project)
    var key = 'AIzaSyBQ2COy7Wdn8gfx-vavH8tMHPCmjE3rfWA';
	//use Slider or inputfields
	var slider = true;
	//Set order
    //date – Resources are sorted in reverse chronological order based on the date they were created.
    //rating – Resources are sorted from highest to lowest rating.
    //relevance – Resources are sorted based on their relevance to the search query. This is the default value for this parameter.
    //title – Resources are sorted alphabetically by title.
    //viewCount – Resources are sorted from highest to lowest number of views.
	var order = 'relevance';
    //Use suggest search words
    var suggest = true;
    //Set default width and height
    var width = 560;
    var height = 315;

	//Langs. To add or adjust the lang files are in the lang dir
	var data = {
		"youtubeurl": parent.tinymce.util.I18n.translate('Youtube URL'),
		"youtubeSkin": parent.tinymce.util.I18n.translate('Skin'),
		"youtubeSkinD": parent.tinymce.util.I18n.translate('dark'),
		"youtubeSkinL": parent.tinymce.util.I18n.translate('light'),
		"youtubeWidth": parent.tinymce.util.I18n.translate('width'),
		"youtubeHeight": parent.tinymce.util.I18n.translate('height'),
		"youtubeSearch": parent.tinymce.util.I18n.translate('Search'),
		"youtubeTitle": parent.tinymce.util.I18n.translate('Title'),
		"youtubeADDclose": parent.tinymce.util.I18n.translate('Insert and Close'),
		"youtubeADD": parent.tinymce.util.I18n.translate('Insert'),
		"youtubeLOAD": parent.tinymce.util.I18n.translate('Load More'),
		//Don't change from here
		"width": width,
		"height": height
	};

	function youtubesearch() {

	$(function () {

		YTDataV3.init({
			key: key,
            order: order
		});

        if (suggest) {
          $('#inpKeywords').keyup(function () {
  			var val = $(this).val();
  			 jQTubeUtil.suggest(val, function (response) {

  			 	var html = '';
  			 	for (s in response.suggestions) {
  			 		var sug = response.suggestions[s];
  			 		html += '<li><a href="#">' + sug + '</a></li>';
  			 	}
  			 	if (response.suggestions.length)
  			 		$('.autocomplete').html(html).fadeIn(500);
  			 	else
  			 		$('.autocomplete').fadeOut(500);
  			 });
  		  });
        }

		$('#btnSearch').click(function () {
			$('#inpKeywords, #btnSearch').blur();
			show_videos();
			$('.autocomplete').fadeOut(500);
			return false;
		});

		$(document).on('click', '.autocomplete a', function () {
			var text = $(this).text();
			$('#inpKeywords').val(text);
			$('.autocomplete').fadeOut(500);
			show_videos();
			return false;
		});

		function show_videos() {
			$('#hidPage').val(1); /*Reset Paging*/

			var val = $('#inpKeywords').val();
			var parametersObject = {
				"q": val,
				"start-index": document.getElementById("hidPage").value,
				"max-results": max,
                "order": order
			}

			$('.videos').addClass('preloader').html('');
			YTDataV3.search(parametersObject, function(response) {
				if (response.totalResults == 0) {
				   $(".videos").show().text(' No results !');
				   $('#load_more').hide();
				   return false;
				}
				if (response.totalResults < max) {
				   $('#load_more').hide();
				}

				var html = '';
				for (v in response.videos) {
					html+=template(response.videos[v]);
				}
				$('.videos').removeClass('preloader').html(html);
			});
		$('#load_more').show(500);
		}

	});
	};

	function convertQuotes(string){
		return string.replace(/["']/g, "");
	}

	function template(video) {
		html = '';
		html += '<li>';
		html += '<div class="row listbox"><div class="col-xs-5"><a href="javascript:selectVideo(\'' + video.id.videoId + '\',\'' + convertQuotes(video.snippet.title) + '\')">';
		html += '<img src="' + video.snippet.thumbnails.medium.url + '" class="img-rounded" alt="' + video.snippet.title + '" title="' + video.snippet.title + '" />';
		html += '</a></div>';
		html += '<div class="col-sx-7 listboxText"><a href="javascript:selectVideo(\'' + video.id.videoId + '\',\'' + convertQuotes(video.snippet.title) + '\')">' + video.snippet.title + '</a>';
		html += '<small>' + video.snippet.description + '</small>';
		html += '</div></div>';
		html += '</li>';

		return html;
	}

	function loadmore() {
		$('#hidPage').val($('#hidPage').val() * 1 + 1);
		var start = ($('#hidPage').val() * max + 1 - max);

		var val = $('.form-horizontal').find('#inpKeywords').val();
			var parametersObject = {
				'q': val,
				'max-results': max,
                'order': order,
				'next_page': true
			}
			YTDataV3.search(parametersObject, function(response) {
				// return;
				var html = '';
				for (v in response.videos) {
					html+=template(response.videos[v]);
				}
				$('.videos').removeClass('preloader').append(html);
			});

	}

	function selectVideo(Id,title) {
		var sUrl = secure + '://www.youtube.com/watch?v=' + Id;
		$('#inpURL').val(sUrl);
		$('#titleURL').val(title);
		$('#preview').html(get_video_iframe());
	}

	function I_InsertHTML(sHTML) {
		if (getVideoId($('#inpURL').val()) == '') {
		return false;
		}
		parent.tinymce.activeEditor.insertContent(sHTML);
	}

	function I_Close() {
		parent.tinymce.activeEditor.windowManager.close();
	}

	function get_video_iframe() {
		var sEmbedUrl = secure + '://www.youtube.com/embed/' + getVideoId($('#inpURL').val());
		var sHTML = '<iframe title="' + $('#titleURL').val() + '" width="290" height="230" src="' + sEmbedUrl + '?wmode=opaque&modestbranding=1&theme=' + $('#skinURL').val() + '" frameborder="0" allowfullscreen></iframe>';
		return sHTML;
	}
	function I_Insert() {
		var sEmbedUrl = secure + '://www.youtube.com/embed/' + getVideoId($('#inpURL').val());

		/* Link Title */
		var sTitle = $("#titleURL").val().substring(0,125);

        /* Here the output. change to your own needs */
        if (youtput == 'placeholder')
			var sHTML = '<img src="' + secure + '://img.youtube.com/vi/' + getVideoId($('#inpURL').val()) + '/0.jpg" alt="' + sTitle + '" width="' + $('#widthURL').val() + '" height="' + $('#heightURL').val() + '" data-video="youtube" data-skin="' + $('#skinURL').val() + '" data-id="' + getVideoId($('#inpURL').val()) + '">';
		else
			var sHTML = '<iframe title="' + sTitle + '" width="' + $('#widthURL').val() + '" height="' + $('#heightURL').val() + '" src="' + sEmbedUrl + '?wmode=opaque&theme=' + $('#skinURL').val() + '" frameborder="0" allowfullscreen></iframe>';
		I_InsertHTML(sHTML);
	}

	function getVideoId(url) {
		return url.replace(/^.*((v\/)|(embed\/)|(watch\?))\??v?=?([^\&\?]*).*/, '$5');
	}

	//Use jQuery's get method to retrieve the contents of our template file, then render the template.
	$.get('template/forms.html' , function (template) {
		filled = Mustache.render( template, data );
		$('#template-container').append(filled);

		if (slider) {

		$('#widthURL').slider
		({
		  formater: function(value) {
			return data.youtubeWidth+': '+value;
		  }
		});
		$('#heightURL').slider
		({
		  formater: function(value) {
			return data.youtubeHeight+': '+value;
		  }
		});

			$('#widthURL').on('slideStop', function(slideEvt) {
				var valueHeight = Math.round((slideEvt.value/16)*9);
				$('#heightURL').slider('setValue', valueHeight);
				$('#heightURL').val(valueHeight);
			});
			$('#heightURL').on('slideStop', function(slideEvt) {
				var valueWidtht = Math.round((slideEvt.value/9)*16);
				$('#widthURL').slider('setValue', valueWidtht);
				$('#widthURL').val(valueWidtht);
			});

		}

		 youtubesearch();
	});