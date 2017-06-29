tinymce.PluginManager.add('giphy', function(editor, url) {
    // Add a button that opens a window
    editor.addButton('giphy', {
        title: 'Giphy GIF Search',
        icon: true,
        image: tinyMCE.baseURL + '/plugins/giphy/html/img/giphy_icon_16.png',
        onclick : function(ev) {
            var modalw = 480;
            var modalh = 548;

            editor.windowManager.open({
                title : "Giphy Search",
                file: url + '/html/giphy.html',
                width : modalw,
                height : modalh,
                inline : true,
                resizable: true,
                scrollbars: true
            }, {
                plugin_url : url, // Plugin absolute URL
                api_key : 'dc6zaTOxFJmzC', // the API key
                api_host : 'http://api.giphy.com' // the API host
            });
        }
    });
});