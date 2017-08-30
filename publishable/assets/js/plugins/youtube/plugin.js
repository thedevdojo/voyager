/**
 * Youtube search - a TinyMCE youtube search and place plugin
 * youtube/plugin.js
 *
 * This is not free software
 *
 * Plugin info: http://www.cfconsultancy.nl/
 * Author: Ceasar Feijen
 *
 * Version: 2.0 released 14/08/2014
 */
tinymce.PluginManager.requireLangPack('youtube', 'en,nl,de');
tinymce.PluginManager.add('youtube', function(editor) {

    function openmanager() {
        win = editor.windowManager.open({
            title: 'Choose YouTube Video',
            file: tinyMCE.baseURL + '/plugins/youtube/youtube.html',
            filetype: 'video',
	    	width: 785,
            height: 560,
            inline: 1,
            buttons: [{
                text: 'cancel',
                onclick: function() {
                    this.parent()
                        .parent()
                        .close();
                }
            }]
        });

    }
	editor.addButton('youtube', {
		icon: true,
		image: tinyMCE.baseURL + '/plugins/youtube/icon.png',
		tooltip: 'Insert Youtube video',
		shortcut: 'Ctrl+Q',
		onclick: openmanager
	});

	editor.addShortcut('Ctrl+Q', '', openmanager);

	editor.addMenuItem('youtube', {
		icon:'media',
		text: 'Insert Youtube video',
		shortcut: 'Ctrl+Q',
		onclick: openmanager,
		context: 'insert'
	});
});
