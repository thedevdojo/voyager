/**
 * =====================================================
 *  jQTubeUtil - jQuery YouTube Search Utility
 * =====================================================
 *  Version: 0.9.0 (11th September 2010)
 *  Author: Nirvana Tikku (ntikku@gmail.com)
 *
 *  Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 *  Documentation:
 *    http://www.tikku.com/jquery-jQTubeUtil-util
 * =====================================================
 *
 *
 *	Rebuild by ceasar feijen 2014
 */

;jQTubeUtil = (function($){ /* singleton */

	var f = function(){};
	var p = f.prototype;

	// Constants, Private Scope
	var SuggestURL = "https://suggestqueries.google.com/complete/search";

	// The Feed URL structure _requires_ these
	var CoreDefaults = {
		"callback": "?"
	};

	// The Autocomplete utility _requires_ these
	var SuggestDefaults = {
		hl: "en",
		ds: "yt",
		client: "youtube",
		hjson: "t",
		cp: 1
	};

	/**
	 * Initialize the jQTubeUtil utility
	 */
	p.init = function(options){
		if(options.lang)
			SuggestDefaults.hl = options.lang;
	};

	/**
	 * Autocomplete utility returns array of suggestions
	 * @param input - string
	 * @param callback - function
	 */
	p.suggest = function(input, callback){
		var opts = {q: encodeURIComponent(input)};
		var url = _buildURL(SuggestURL,
			$.extend({}, SuggestDefaults, opts)
		);
        //console.log(url);
		$.ajax({
			type: "GET",
			dataType: "json",
			url: url,
			success: function(xhr){
				var suggestions = [], res = {};
				for(entry in xhr[1]){
					suggestions.push(xhr[1][entry][0]);
				}
				res.suggestions = suggestions;
				res.searchURL = url;
				if(typeof(callback) == "function"){
					callback(res);
					return;
				}
			}
		});
	};


	/**
	 * This method builds the url utilizing a JSON
	 * object as the request param names and values
	 */
	function _buildURL(root, options){
		var ret = "?", k, v, first=true;
		var opts = $.extend({}, options, CoreDefaults);
		for(o in opts){
			k = o;	v = opts[o];
			ret += (first?"":"&")+k+"="+v;
			first=false;
		}
		return root + ret;
	};

	return new f();

})(jQuery);
