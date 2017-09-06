jQuery(document).ready(function() {
  jQuery('#gifs').masonry({
    itemSelector: '#gifs li',
    columnWidth: 145,
    gutter: 10,
    transitionDuration: '0.2s',
    isFitWidth: true
  });

  // init giphy
  GiphySearch.init();

  // init the CMS extension app
  GiphyCMSExt.init();

  // start the default search
  GiphySearch.search("giphytrending", 100, true);
});
