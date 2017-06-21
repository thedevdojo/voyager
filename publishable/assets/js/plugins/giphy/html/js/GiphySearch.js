/***
    
    

    THIS IS SHARED CODE BASE

        it NEEDS TESTS!

    Implemented:
        mobile web
        chrome ext
        firefox ext
        safari ext


*/

var GiphySearch = {
    MAX_TAGS: 5,
    PLATFORM_COPY_PREPEND_TEXT:"",
    MAX_GIF_WIDTH: 145,
    SEARCH_PAGE_SIZE: 100,
    INFINITE_SCROLL_PX_OFFSET: 100,
    INFINITE_SCROLL_PAGE_SIZE: 50,
    ANIMATE_SCROLL_PX_OFFSET: 200,
    STILL_SCROLL_PAGES: 1,
    SCROLLTIMER_DELAY: 250,
    SCROLL_MOMENTUM_THRESHOLD: 100,
    ALLOW_URL_UPDATES:true, // enable history pushstate via add_history method
    API_KEY:"G46lZIryTGCUU", 
    //API_KEY: parent.tinyMCE.activeEditor.getParam('api_key'),
    location:{}, // for chrome ext

    // navigation vars
    curPage: "gifs",

    // scrolling vars
    curScrollPos: 0,
    prevScrollPos: 0,
    isRendering: false,

    // event buffer timers
    scrollTimer: 0,
    searchTimer: 0,
    renderTimer: 0,
    // infinite scroll vars
    curGifsNum: 0, // for offset to API server
    curResponse: null,
    prevResponse: null,

    // search vars
    curSearchTerm: "",
    prevSearchTerm: "",

    init: function(data) {
        //console.log("init()");
        if(GiphySearch.is_ext()) {
            //GiphySearch.init_chrome_extension();    
        } else {
            // fix me! target FF only somehow
            //GiphySearch.init_firefox_extension();
        }
        
        GiphySearch.bind_events();
        
    },
    bind_events:function() {
        //console.log("bind_events");
        jQuery(window).on('popstate', function(event) {
            GiphySearch.handleBrowserNavigation.call(this,event);
        });

        // set the container height for scrolling
        // container.height(jQuery(window).height());
        var $container = jQuery("#container");
    

        // watch scroll events for desktop
        $container.on("scroll", function(event) {
            //console.log("scroll event");
            GiphySearch.scroll.call(this,event);
        }).on("click", ".tag", function(event) {
            GiphySearch.handleTag.call(this,event);
        }).on("click", ".gif_drag_cover", function(event) {
       	    //console.log("handle gif drag cover");
    	    GiphySearch.handleGifDetailByCover.call(this,event);
        }).on("dragstart", ".giphy_gif_li", function(event) {
            //GiphySearch.handleGIFDragFFFix.call(this, event);
        }).on("dragstart", "#gif-detail-gif", function(event){
            //GiphySearch.handleGIFDrag.call(this, event);
        }).on("dragstart", ".gif-detail-cover", function(event){
            //GiphySearch.handleGIFDragDetailFFFix.call(this, event);            
        }).on("click", "#categories .popular_tag", function(event) {
            GiphySearch.handleTag.call(this,event);
        });

        jQuery("#app").on("click", ".logo-text", function(event) {
            GiphySearch.handleTrendingHome.call(this,event);
        }).on("click", ".logo-image", function(event) {
            GiphySearch.handleTrendingHome.call(this,event);
        });

        // address 'home' via logo tag
        jQuery("#header").on("click", function(event) {

        });

        // search input handler
        jQuery("#searchbar-input").keypress(function(event) {
            ////console.log("search bar input");
            if(event.keyCode == 13) {
                GiphySearch.handleSearch.call(this,event);
            }
        });

        // search button handler
        jQuery("#search-button").on("click", function(event) {
            //console.log("search button");
            GiphySearch.handleSearch.call(this,event);
        });

        // categories handler
        jQuery("#categories-button").on("click", function(event) {
            //console.log("categories button");
            GiphySearch.handleCategories.call(this,event);
        });

        // back button handler
        jQuery("#back-button").on("click", function(event) {
            GiphySearch.handleBack.call(this,event);
        });

        //console.log("end bind events");
        //console.log(jQuery("#categories-button"));
    },
    is_ext:function() {
        return !!(window.chrome && chrome.contextMenus);
    },
    init_firefox_extension:function() {
        GiphySearch.STILL_SCROLL_PAGES = 3;
        //console.log("init_firefox_extension();");
        jQuery(".ff_window_close_btn").bind("click", function(e) {
            window.close();
        });
    },
    init_chrome_extension:function() {
        
        GiphySearch.PLATFORM_COPY_PREPEND_TEXT = "Copy to clipboard: ";
        // GiphySearch.search("giphytrending", 100, true);
        GiphySearch.ALLOW_URL_UPDATES = false;
        GiphySearch.STILL_SCROLL_PAGES = 3;


        window.unonload = function() {
            //console.log("closed!");
            // chrome.contextMenus.removeAll();
        }

        jQuery("#container").on("click", "#gif-detail-link", function(event) {
            // copy to clipboard..
            var $this = jQuery(this);
            
            var _input = newT.input({
                type:"text", 
                id:"giphy_copy_box",
                value:$this.data("shortlink")
            });
            jQuery("#giphy_copy_box").remove();
            jQuery(document.body).append(_input);
            document.getElementById("giphy_copy_box").select();
            document.execCommand('Copy', false, null);                
        });
        var protocol_exp = /([https|http|ftp]+:)\/\//;
        var hostname_exp = /\/\/([^\/]+)\/?/;
        function urlparse(_url) {
          var params = {};

          var m1 = _url.match(protocol_exp);
          if(m1 && m1.length > 1) {
            params.scheme = m1[1];
          }

          var m2 = _url.match(hostname_exp);
          if(m2 && m2.length >1) {
            params.hostname = m2[1];
          }
          return params;
        }        
        GiphySearch.render_completed = function() {
            // //console.log("foo!");
            jQuery("#searchbar-input").focus();
        }

        // load chrome!
        chrome.contextMenus.create({
          "id":"giphy_context_menu",
          "title" : "Copy Link address",
          "type" : "normal",
          "contexts" : ["image"] //the context which item appear

        }); 
        chrome.contextMenus.create({
          "id":"giphy_img_src_menu",
          "title" : "Copy Full Size GIF Image",
          "type" : "normal",
          "contexts" : ["image"] //the context which item appear

        });         
        chrome.windows.getCurrent(function(_win) {
          chrome.tabs.query({ currentWindow: true, active: true }, function (tabs) {
            GiphySearch.location = urlparse(tabs[0].url);
          });

        });

        // setup the right click to work...
        chrome.contextMenus.onClicked.addListener(function(info, tab) {
            // lookup the context
            // //console.log("context menu!", info, info.srcUrl, GiphySearch.curResponse, GiphySearch.find_by_src(info.srcUrl));
            // TODO POSSIBLE ERROR here
            var gif_obj = GiphySearch.find_by_src(info.srcUrl);
          
          if(!gif_obj) {
            //console.log("NO SUCH GIF!");
            gif_obj = GiphySearch.curResponse.data[0];
            // return; // BAIL out
          } 

          var elem_params = {
            type:"text", 
            id:"giphy_copy_box",
            value:info.srcUrl
          };
          // //console.log("info.menuItemId", info.menuItemId);
          if(info.menuItemId === "giphy_img_src_menu") {
            // //console.log("Using", gif_obj.images.original.url);
            elem_params.value = gif_obj.images.original.url;            
          } else {
            // //console.log("Using", gif_obj.bitly_gif_url);
            elem_params.value = gif_obj.bitly_gif_url;
          }
          var div = newT.input(elem_params);          
          // jQuery("#giphy_copy_box").val( elem_params.value ).select()
          // jQuery("#giphy_copy_box").val( info.srcUrl );
          
          jQuery("#giphy_copy_box").remove(); // kill existing
          jQuery(document.body).append(div);
          document.getElementById("giphy_copy_box").select();
          document.execCommand('Copy', false, null);    
          
          setTimeout(function() {
              
          },0);

        });
    },
    find_by_src:function(src_url) {
        var all_gifs = [];
        var selected = null;
        if(GiphySearch.prevResponse) {
            all_gifs.push.apply(all_gifs,GiphySearch.prevResponse.data);    
        }

        if(GiphySearch.curResponse) {
            all_gifs.push.apply(all_gifs,GiphySearch.curResponse.data);    
        }
        
        // var data = GiphySearch.curResponse;
        for(var i=0, len=all_gifs.length; i<len; i++) {
            if(all_gifs[i].images.fixed_width.url === src_url) {
                selected = all_gifs[i];
                break;
            }
        }
        all_gifs = [];
        return selected;
    },
   
    format:function(str,params) {
        return str.replace(/%\((\w+)\)s/g, function(m, key) {
            return params[key] || ""
        });
    },   

    /**
        Handlers definitions
            handle all the events

            this is a hodge-podge
            some of the events call sub methods, others
            are defined top level
    */ 
    handleGIFDragDetailFFFix:function(e) {
        //console.log("Handle FF drag");
        var dt = e.originalEvent.dataTransfer;
        var $this = jQuery(this).parent("div").find("img");
        // var _id = $this.data("id");
        var _hostname = GiphySearch.location.hostname;
        var gif_obj = GiphySearch.find_by_src( $this.attr("src") );
        // //console.log("found gif!", gif_obj);
        if(!gif_obj) {
            //console.log("no such GIF", gif_obj);
            return;
        }
        dt.dropEffect = "copy";
        dt.effectAllowed = "copy";  
        // dt.dropEffect= 'move';  
        // dt.effectAllowed = "copyMove"
        var htmlstr = GiphySearch.gmail_template( {
            src:gif_obj.images.original.url, 
            url:gif_obj.bitly_gif_url 
        });

        // dt.effectAllowed = "move";
        // dt.dropEffect = "move";

        dt.setData("text/html", htmlstr);
        dt.setData("text", htmlstr);    

        // dt.setData("text/html", "foobar");
        // dt.setData("text", "foobar");    

        //console.log("setting data", dt.getData("text"));

        if(_hostname === "twitter.com") {
            dt.setData("text/html", gif_obj.bitly_gif_url + " via @giphy"  );
        }
        if(_hostname === "www.facebook.com") {
            dt.effectAllowed = "linkMove";
            dt.dropEffect = "linkMove";
            // FB pukes on our short link. use full url for better user experience
            dt.setData("text/html", "http://giphy.com/gifs/" + gif_obj.id  );
            dt.setData("text", "http://giphy.com/gifs/" + gif_obj.id  );            
        }
        if(/.*\.hipchat\.com/.test(_hostname)) {
            dt.setData("text/html", gif_obj.images.original.url);
            dt.setData("text", gif_obj.images.original.url);           
        }  
    },
    handleGIFDragFFFix:function(e) {
        //console.log("Handle FF drag");
        var dt = e.originalEvent.dataTransfer;
        var $this = jQuery(this).find("img");
        // var _id = $this.data("id");
        var _hostname = GiphySearch.location.hostname;
        var gif_obj = GiphySearch.find_by_src( $this.attr("src") );
        // //console.log("found gif!", gif_obj);
        if(!gif_obj) {
            //console.log("no such GIF", gif_obj);
            return;
        }
        dt.dropEffect = "copy";
        dt.effectAllowed = "copy";  
        // dt.dropEffect= 'move';  
        // dt.effectAllowed = "copyMove"
        var htmlstr = GiphySearch.gmail_template( {
            src:gif_obj.images.original.url, 
            url:gif_obj.bitly_gif_url 
        });

        // dt.effectAllowed = "move";
        // dt.dropEffect = "move";

        dt.setData("text/html", htmlstr);
        dt.setData("text", htmlstr);    

        // dt.setData("text/html", "foobar");
        // dt.setData("text", "foobar");    

        //console.log("setting data", dt.getData("text"));

        if(_hostname === "twitter.com") {
            dt.setData("text/html", gif_obj.bitly_gif_url + " via @giphy"  );
        }
        if(_hostname === "www.facebook.com") {
            dt.effectAllowed = "linkMove";
            dt.dropEffect = "linkMove";
            // FB pukes on our short link. use full url for better user experience
            dt.setData("text/html", "http://giphy.com/gifs/" + gif_obj.id  );
            dt.setData("text", "http://giphy.com/gifs/" + gif_obj.id  );            
        }
        if(/.*\.hipchat\.com/.test(_hostname)) {
            dt.setData("text/html", gif_obj.images.original.url);
            dt.setData("text", gif_obj.images.original.url);           
        }          
    },
    handleGIFDrag:function(e) {
        //console.log("I am dragging!", e);
        // e.preventDefault();
        // e.stopPropagation();
        // jQuery('.dropzone').hide().removeClass('dropzone-hilight');
        var dt = e.originalEvent.dataTransfer;
        var $this = jQuery(this);
        // var _id = $this.data("id");
        var _hostname = GiphySearch.location.hostname;
        var gif_obj = GiphySearch.find_by_src( $this.attr("src") );
        // //console.log("found gif!", gif_obj);
        if(!gif_obj) {
            //console.log("no such GIF", gif_obj);
            return;
        }
        dt.dropEffect = "copy";
        dt.effectAllowed = "copy";  
        // dt.dropEffect= 'move';  
        // dt.effectAllowed = "copyMove"
        var htmlstr = GiphySearch.gmail_template( {
            src:gif_obj.images.original.url, 
            url:gif_obj.bitly_gif_url 
        });

        // dt.effectAllowed = "move";
        // dt.dropEffect = "move";

        dt.setData("text/html", htmlstr);
        dt.setData("text", htmlstr);    

        // dt.setData("text/html", "foobar");
        // dt.setData("text", "foobar");    

        //console.log("setting data", dt.getData("text"));

        if(_hostname === "twitter.com") {
            dt.setData("text/html", gif_obj.bitly_gif_url + " via @giphy"  );
        }
        if(_hostname === "www.facebook.com") {
            dt.effectAllowed = "linkMove";
            dt.dropEffect = "linkMove";
            // FB pukes on our short link. use full url for better user experience
            dt.setData("text/html", "http://giphy.com/gifs/" + gif_obj.id  );
            dt.setData("text", "http://giphy.com/gifs/" + gif_obj.id  );            
        }
        if(/.*\.hipchat\.com/.test(_hostname)) {
            dt.setData("text/html", gif_obj.images.original.url);
            dt.setData("text", gif_obj.images.original.url);           
        }   
    },

    handleSearch: function(event) {
        ////console.log("handleSearch()");

        // get the tag to search
        var tag = jQuery("#searchbar-input").val();
        if(tag == "") return;

        // don't reset the typed in input!
        GiphySearch.scrollTimer = 0;
        GiphySearch.searchTimer = 0;
        GiphySearch.curY = 0;
        GiphySearch.curOffset = 0;
        // reset the scroll and page vars
        // GiphySearch.resetSearch();

        // make the new search
        GiphySearch.show_preloader();
        GiphySearch.search(tag, GiphySearch.SEARCH_PAGE_SIZE, true);
        GiphySearch.navigate("gifs");
    },
    handleTrendingHome:function(event) {
        GiphySearch.show_preloader();
        GiphySearch.resetSearch();
        GiphySearch.search("giphytrending", 100, true);
        GiphySearch.navigate("gifs");
        // GiphySearch.add_history( "Giphy", "/" );       
    },
    handleTag: function(event) {
        ////console.log("handleTag()");

        // get the tag
        var tag = jQuery(event.target).text();
        if(tag == '') return;

        GiphySearch.show_preloader();
        GiphySearch.resetViewport();
        GiphySearch.updateSearch( tag );
        
        // reset the scroll and page vars
        // GiphySearch.resetSearch();
        
        // make the new search
        GiphySearch.search(tag, GiphySearch.SEARCH_PAGE_SIZE, true);
        GiphySearch.navigate("gifs");
        // isolate all these,
        // restrict to a flag
        // GiphySearch.add_history( "Giphy Gif Search", "/search/" + tag.replace(/\s+/g, '-') );
        
    },
    handleGifDetailByCover:function(event) {
        var gif = jQuery(event.target).parent(".giphy_gif_li").find("img");
        GiphySearch._opendetail( gif );
    },

    handleGifDetail: function(event) {
        //console.log("handleGifDetail()");

        // get the fullsize gif src
        var gif = jQuery(event.target);
        GiphySearch._opendetail( gif );

    },
    _opendetail:function(gif) {
        //jQuery("html, body").animate({ scrollTop: 0 }, "fast");
        window.scrollTo(0, 0);
        jQuery("#container").css("overflow", "hidden");
      
        var gifEl = jQuery("#gif-detail-gif");
        // var loader = jQuery("#loader");
        var animatedLink = gif.attr("data-animated");
        var staticLink =  gif.attr("data-still");

        gifEl.attr("src", staticLink);
        gifEl.attr("data-id", gif.attr("data-id"));
        gifEl.attr("data-width", "500");
        gifEl.attr("data-height", Math.floor(gif.attr("height") * 2.5));
        
        // show the loader
        // loader.css("display","block");
        GiphySearch.show_preloader();

        jQuery("<img />").attr("src", animatedLink).load(function(e){
            // //console.log("load event", this.naturalHeight, this.clientWidth)
            GiphySearch.hide_preloader();
            // loader.css("display","none");
            gifEl.attr("src", animatedLink);
            // height:gif.attr("original_height")
            jQuery(".gif-detail-cover").css({
               height:jQuery("#gif-detail-gif").height()
            }).attr("draggable", true);
        });

        var linkHTML = "<span class='gif-link-info'>" + GiphySearch.PLATFORM_COPY_PREPEND_TEXT+""+ gif.attr("data-shortlink")+"</span>";
        var tags = gif.attr("data-tags").split(',');
        var tagsHTML = "";
        jQuery(tags).each(function(idx, tag){
            if(tag !== ""){
                tagsHTML += "<span class='gif-detail-tag'>"+tag+"</span>"; //USE ACTUAL ENCODDed?
            }
        });

        
        jQuery("#gif-detail-link").html(linkHTML).attr({
            "data-shortlink":gif.attr("data-shortlink") // we should call this data the same name as the server does
        });
        
        jQuery("#gif-detail-tags").html(tagsHTML);

        jQuery(".gif-detail-tag").on("click", function(event) {
            GiphySearch.handleTag(event);
        });

        // GiphySearch.add_history( "Giphy", "/gifs/"+gif.attr("data-id") );
        GiphySearch.navigate("gif-detail");
    },
    handleCategories: function(event) {
        ////console.log("handleCategories()");
        event.preventDefault();
        GiphySearch.navigate("categories");
    },


    handleBrowserNavigation: function(event){
        /*
         * UPDATE SO TO NOT MAKE NEW SEARCH CALLS WHen
         */
        var pathHash = window.location.pathname.split('/');
        if(pathHash[1] != "") {
            if(pathHash[1] == "gifs"){
                GiphySearch.navigate("gif-detail", pathHash[2]);
            }
            if(pathHash[1] == "search"){
                GiphySearch.search(pathHash[2], 100, true);
                GiphySearch.navigate("gifs");
            }
        } else {
            GiphySearch.search("giphytrending", 100, true);
            GiphySearch.navigate("gifs");
        }
    },

    handleBack: function(event) {
        jQuery("#container").css("overflow", "auto");
      
        // no back on the gifs page
        if(GiphySearch.curPage == "gifs") { return; }

        // back to the gif page
        if(GiphySearch.curPage == "categories" ||
            GiphySearch.curPage == "gif-detail") {
            // GiphySearch.add_history("Giphy", "/");
            
            GiphySearch.navigate("gifs");
        }
    },

    navigate: function(page, data) {
        //console.log("navigate(" + page + "," + data + ")");

        // set the current page
        GiphySearch.curPage = page;

        // hide everything
        jQuery("#gifs,#gif-detail,#share-menu,#categories,#category,#back-button").hide();
        // show the footer... it goes away on the gif-detail
        jQuery("#footer").show();

        // gifs
        if(page == "gifs") {
            jQuery("#gifs").show();
        }

        // gif detail
        if(page == "gif-detail") {
            jQuery("#gif-detail,#back-button,#share-menu").show();
            jQuery("#footer").hide();
        }

        // categories
        if(page == "categories") {
          //console.log("showing back button");
          jQuery("html, body").animate({ scrollTop: 0 }, "fast");
          jQuery("#categories,#back-button").show();
        }

        // category
        if(page == "category") {
            jQuery("#category").show();
        }
    },


    orientationchange: function(event) {
        //console.log("orientationchange()");
    },

    scroll: function(event) {
        ////console.log("scroll()");

        // only scroll on gifs page
        if(GiphySearch.curPage != "gifs") return;

        // set the current scroll pos
        GiphySearch.prevScrollPos = GiphySearch.curScrollPos;
        GiphySearch.curScrollPos = jQuery(event.target).scrollTop() + jQuery(window).height();

        // infinite scroll
        if(GiphySearch.curScrollPos + GiphySearch.INFINITE_SCROLL_PX_OFFSET > jQuery("#gifs").height()) {

            // start the infinite scroll after the last scroll event
            clearTimeout(GiphySearch.searchTimer);
            GiphySearch.searchTimer = setTimeout(function(event) {
                GiphySearch.search(GiphySearch.curSearchTerm, GiphySearch.INFINITE_SCROLL_PAGE_SIZE, false);
            }, 250);
        }

        // compenstate for a double scroll end event being triggered
        clearTimeout(GiphySearch.scrollTimer);
        GiphySearch.scrollTimer = setTimeout(function() {
            GiphySearch.scrollend(event);
        }, GiphySearch.SCROLLTIMER_DELAY);
    },

    scrollstart: function(event) {
        ////console.log("scrollstart()");
    },

    scrollend: function(event) {

        if(GiphySearch.renderTimer) { clearTimeout(GiphySearch.renderTimer); }
        GiphySearch.renderTimer = setTimeout(function() {
            GiphySearch.render();
        }, 250);
    },
    hide_preloader:function() {
        //console.log("hide preloader");
        jQuery(".loading_icon_box,.loading_icon").css("display","none");
    },
    show_preloader:function() {
        //console.log("show preloader");
        jQuery(".loading_icon_box,.loading_icon").css("display","block");
    },    
    // THIS IS POORLY NAMED, it doesn't render, it displays..
    // renders (aka added to DOM happens WAY earlier)
    render: function() {

        
        if(GiphySearch.isRendering) return;
        GiphySearch.isRendering = true;

        
        //console.log("*** render() ***");
        //console.log("*** display() ***");

        // get all the gifs
        /**
            NOTE:
                lis ONLY has a length
                when there are ALREADY rendered items
                on the page

                this is related to using setTimeout
                when adding images to masonry / DOM


        */        
        var lis = jQuery("#gifs li");
        // calculate the window boundaries        
        var windowTop = jQuery(window).scrollTop(); 
        var windowBottom = windowTop + jQuery(window).height();
        var windowHeight = jQuery(window).height();

        // sliding window of animated, still, and off
        ////console.log("existing li : ", lis);
        ////console.log("rendering " + lis.length + " num lis");
        for(var i=0; i<lis.length; i++) {

            // get the gif

            var li = jQuery(lis.get(i));

            // try cooperative multitasking to let the graphics render have a moment
            // this seems super innefficient b/c we access the DOM a LOT
            (function($li, _pos) {
                setTimeout(function() {
                // need to calculate the window offsets and some emperical padding numbers
                var liTop = $li.offset().top;
                var liBottom = liTop + $li.height();
                var img = $li.find("img");
                var liHeightOffset = GiphySearch.ANIMATE_SCROLL_PX_OFFSET;
                var stillPagesOffset = GiphySearch.STILL_SCROLL_PAGES;
                
                ////console.log("GIF ON " , windowTop, liHeightOffset, liBottom, windowBottom);

                // turn on the gifs that are in view... we pad with an offset to get the edge gifs
                if((liTop >= windowTop - liHeightOffset) && (liBottom <= windowBottom + liHeightOffset)) {
                // if((liTop >= windowTop - liHeightOffset) && (liBottom <= windowBottom + liHeightOffset)) {
                    ////console.log("GIF ON " , windowTop, liHeightOffset, liBottom, windowBottom);
                    

                    // buffer the animated gifs with a page above and below of stills...
                    // pad these a big with multiples of the window height
                    jQuery(img).attr("src", jQuery(img).attr("data-animated"));
                    // jQuery(img).attr("src", $img.attr("data-downsampled"));

                } else if((liTop >= windowTop - windowHeight*stillPagesOffset) &&
                          (liBottom <= windowBottom + windowHeight*stillPagesOffset)) {
                    ////console.log("GIF STILL");

                    // still these gifs
                    jQuery(img).attr("src", jQuery(img).attr("data-still"));

                } else {
                    ////console.log("GIF OFF");

                    // clear the rest of the gifs

                    if(GiphySearch.is_ext()) {
                        jQuery(img).attr("src", jQuery(img).attr("data-still") );
                    } else {
                        ////console.log("setting img src to clear");
                        jQuery(img).attr("src", "img/clear.gif");    
                    }
                    
                }
                
                if(lis.length-1 === _pos) {
                    GiphySearch.render_completed();
                    // //console.log(i, "current possition",  lis.length) 
                } 
            }, 0)})( jQuery(li), i  );

        }

        // reset rendering
        GiphySearch.isRendering = false; 
        GiphySearch.hide_preloader();  
        //console.log("rendering completed", "is rendering", GiphySearch.isRendering, lis.length);
    },
    gmail_template:function(params) {
        // we paste this 'template' into the dragdrop datatranser object
        return GiphySearch.format( '<a href="%(url)s"><img src="%(src)s" border="0" /></a><br />via <a href="%(url)s">giphy.com</a>', params );
    },
    render_completed:function() {
        //console.log("done rendering now!");
        
    },
    updateSearch:function(txt) {
        jQuery("#searchbar-input").val(txt);
    },
    resetViewport:function() {
        GiphySearch.scrollTimer = 0;
        GiphySearch.searchTimer = 0;
        GiphySearch.curY = 0;
        GiphySearch.curOffset = 0;
    },
    resetSearch: function() {
        ////console.log("resetSearch()");

        // reset the search box
        // jQuery("#searchbar-input").blur();
        jQuery("#searchbar-input").val("");
        // reset the scroll params
        GiphySearch.resetViewport();
    },
    process_search_response:function(response) {
        //console.log("fetched API data", response)
        // set the current search term
        // parse the gifs
        var gifs = response.data;
        var elem_array = [];
        
        
        
        var _frag = document.createDocumentFragment();
        //console.log("process search response ", _frag);
        //console.log("gifs length = " + gifs.length);
        
        for(var i=0; i<gifs.length; i++) {
            ////console.log("i = " + i);
            var gif = gifs[i];
            var tags = gif.tags || [];
            var gifTags = newT.frag();
            var _dataTags = [];
            // TODO: make this a function
            if(tags) {
                for(var j=0; j<tags.length && j<GiphySearch.MAX_TAGS; j++) {
                    if(tags[j].indexOf('giphy') == -1){
                        gifTags.appendChild(newT.span({
                            clss:"tag"
                        }, tags[j]));
                        _dataTags.push( tags[j] );
                    }
                }
            }
            
            var dataTags = _dataTags.join(",")
            var gif_height = Math.floor((gif.images.fixed_width.height * GiphySearch.MAX_GIF_WIDTH / gif.images.fixed_width.width));
            var _li = newT.li({
                        clss:"giphy_gif_li",
                        draggable:true
                    },
                    newT.img({
                        // draggable:true,
                        clss:"gif giphy_gif",
                        height:gif_height,
                        original_height:gif.images.fixed_width.height,
                        "data-id":gif.id,
                        "data-animated":gif.images.fixed_width.url,
                        "data-downsampled":gif.images.fixed_height_downsampled.url,
                        "data-still":gif.images.fixed_width_still.url,
                        "data-tags":dataTags,
                        "data-shortlink":gif.bitly_gif_url,
                        src:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                    }),
                    newT.div({
                        clss:"tags"
                    }, newT.div({
                        clss:"tags_inner"
                        },gifTags)),
                    newT.div({
                        clss:"actions"
                    },newT.a({
                        href:"#"
                    })),
                    newT.div({
                        clss:"gif_drag_cover",
                        style:"height:" + gif_height + "px;"
                    })
                );

            ////console.log(_li);    
                
            _frag.appendChild(_li);
            elem_array.push(_li)
            
            // increment the num gifs
            GiphySearch.curGifsNum++; // why? really seriously why?
        }
        
        //console.log("element array = ", elem_array.length); 
        // when I call settimout, the page no longer loads - WHY? no lis..
        
        //var gifs = document.getElementById("gifs");
        var gifs = jQuery('ul#gifs');
        document.getElementById("gifs").appendChild(_frag);
        
        //console.log('calling masonry with ', elem_array.length, gifs);
        gifs.masonry('appended', elem_array, true);
        
        
        //console.log("post append");
        
    },
    search: function(q, limit, reset) {
        //console.log("search : " + q + " limit = " + limit + " reset = " + reset);
        // if we are searching, bail on scroll
        // are we a new search vs infinite scroll then reset the gif count
        if(reset) {
            GiphySearch.curGifsNum = 0;
            jQuery('#gifs').empty();
        }
        GiphySearch.show_preloader();

        // save the current and previous search terms
        GiphySearch.prevSearchTerm = GiphySearch.curSearchTerm;
        GiphySearch.curSearchTerm = q;

        // giphy search api url
        var url = "http://api.giphy.com/v1/gifs/search?api_key=" + GiphySearch.API_KEY +
            "&q=" + q +
            //"&type=min" +
            "&limit=" + limit +
            "&offset=" + GiphySearch.curGifsNum;

        // make the ajax call
        var xhr = jQuery.ajax({
            dataType: "json",
            url: url
        });
        xhr.done(function(resp) {
            ////console.log("xhr done " + resp);
            // skip prev responses
            if(GiphySearch.curResponse == resp) { return; }           
            GiphySearch.curSearchTerm = q;
            GiphySearch.curLimit = limit;
            // set the previous response to keep out old data
            GiphySearch.prevResponse = GiphySearch.curResponse;
            GiphySearch.curResponse = resp;

            // if this is reset then swap ou
            if(reset) {
                jQuery("#gifs").empty();
                jQuery("#gifs").masonry();
            } 
            setTimeout(function() {
                GiphySearch.process_search_response(resp); 
                GiphySearch.render();   
            },0)           
            
        })
        .fail(function(resp) {          
          alert( "error communicating with giphy api! try again later." );
        });
        return xhr;
    }
}
