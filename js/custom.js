(function($) {
	"use strict";

		// Headhesive
		var options = {
		    offset: 600,
		    classes: {
		        clone:   'banner--clone',
		        stick:   'banner--stick',
		        unstick: 'banner--unstick'
		    }
		};

		// Initialise with options
		var banner = new Headhesive('.navbar', options);

		// Tabs
		$( "#tabs, #horz_tabs" ).tabs();

		// Accordion
		$( "#accordion" ).accordion();
		  
		// Page loading animation
		setTimeout(function() {
	        $('body').addClass('loaded');
	    }, 2000);

	    // Wrap body content
	    $("body").wrapInner( "<div class='wrapper'></div>");

	    // Equal height pricing
	    $('.price, .response > div').matchHeight();

	    // Set button width inside pricing tables
	    var width = $('.price').width();
		$('.price .btn').width(width - 60);

	    $(window).resize(function() {

		    var width = $('.price').width();
			$('.price .btn').width(width - 60);

		});

		// Replace logo
		$(".banner--clone .navbar-header a").replaceWith('<img src="images/logo-dark.png" alt="Evoke web app landing page">');

		// Animated dropdown menu
		$(".nav").bootstrapDropdownOnHover({
			mouseOutDelay: 100, // Number of milliseconds to wait before closing the menu on mouseleave
			responsiveThreshold: 992, // Pixel width where the menus should no-longer be activated by hover
		});

		// Google maps
		if ($("#map").length > 0){
			$('#map').gmap3({
				map: {
				    options:{
				        zoom:16,
				        center: [51.731805, 0.671448],
				        mapTypeId: google.maps.MapTypeId.MAP,
				        mapTypeControl: false,
				        mapTypeControlOptions: {
	           mapTypeIds: [google.maps.MapTypeId.ROADMAP, "style1"]
	        },
				        navigationControl: true,
				        scrollwheel: false,
				        streetViewControl: true,
				        disableDefaultUI: false
				    },
				    styledmaptype:{
				      id: "style1",
				      options:{
				        name: "Style 1"
				      },
				      styles: [
				        {
				          featureType: "all",
				          elementType: "all",
				          stylers: [
				            { saturation: -100 },
				          ]
				        }
				      ]
				    }
				},
				marker:{
				    latLng: [51.731805, 0.671448],
				    options: {
					    icon: new google.maps.MarkerImage(
					        "../template/images/pin.png", new google.maps.Size(35, 48, "px", "px")
					    )
				    }
				 }
				}

			);
		}

})(jQuery);