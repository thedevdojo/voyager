/*
    newT
        a JavaScript template library
    
    authors:  jeffrey tierney | https://twitter.com/jeffreytierney
              gregory tomlinson | https://twitter.com/gregory80
    
    project home: https://github.com/jeffreytierney/newT
    license: (see author)
    
    Usage:
        
        Create a new 'newT' -- a JS represention of DOM Template w/ event capabilities
            
            newT.save("id_name", function( data ) {
                // use the () for multiline return of DOM elements via newT.
                // the second param is the 'contents' of the element
                // this sample is a simple string derived from data when newT.render() is called
                return (
                    newT.div({clss : "my_css_class"}, data.foo)
                )
            });
        
        Converter syntax for Element CSS Class Attribute: "clss"
        
        Render DOM Elements
            @param string "id_name" template name
            @param (any) the data to pass to the method used in the second parameter of newT.save("name", func);
            newT.render("id_name", { foo : "hello world bars" } );
            
        
        Render DOM with options and scope
            
            newT.render("id_name", {}, {
                scope : obj_scope || this
                data : { } // will be overriden completely - not extended
                preData : func // execute w/ scope passed in
                pre : func // excuted w/ scope passed in
            })
            
        
        On Script Load
            On script load, the newT (or temp name is assigned) is initialized.
                This single instance allows convience referrals to complex structures through the
                newT iterface
        
        Use with innerHTML
            newT.renderToString("id_name", {
                foo : "I come back as a string, no a DOM node"
            });

        
        Iteration
            newT.each(["one", "two"], function( data, idx ) {
                console.log("data", data);
                console.log("index position", idx);
            })
        
        Innards:
            newT.templates.global will provide current "templates"

*/

(function (temp) {
  // Default global var name will be newT
  // can be overridden by passing something different
  // into the self executing wrapper function
  temp = temp || "newT";
  
  // internally refer to it as T for brevity sake
  var T = function(options) {
    this.init(options || {});
  }, regex_pattern=/\<[^\>]+\>|\&[^ ]+;/,
  el_cache = {},
  slice = Array.prototype.slice;
  
  T.prototype = {
    constructor: T.prototype.constructor,
    version : "1.1.2",
    init: function(options) {
      this.options = {
        if_attr: "when",
        local_safe: "_safe",
        safe_mode: true
      };
      for(var opt in options) { this.options[opt] = options[opt]; }
      this.templates = {};
      this.__createMethods();
      return this;
    },
    // simple function to save the passed in template
    save: function(name, template) {
      var name_parts = name.split("."),
          ns = "global";
      name = name_parts[0];
      if(name_parts.length > 1) {
        ns = name_parts[1];
      }
      if(!this.templates.hasOwnProperty(ns)) { this.templates[ns] = {}; }
      this.templates[ns][name] = template;
      return this;
    },
    // create the elements for the template
    // and if an exisiting root el was passed in, append it to that root
    // either way, return the newly created element(s)
    render: function(name, data, opts) {
      var name_parts = name.split("."),
          ns = "global",
          new_el,
          ret,
          _new_el, i;
      name = name_parts[0];
      if(name_parts.length > 1) {
        ns = name_parts[1];
      }
      
      opts = opts || {};
      opts.scope = opts.scope || null;
      opts.data = data;
      
      // if a preprocessing function is specified in the options, call it
      // use either the specified scope, or the default of null (set earlier)
      // params
      if (opts.preData) { opts.data = opts.preData.call(opts.scope, opts.data); }
      if (opts.pre) { ret = opts.pre.call(opts.scope, opts.data); }
      
      this.cur_options = opts;
      
      new_el = this.templates[ns][name](opts.data, opts._i, opts._idx);
      if(typeof new_el === "object" && new_el.constructor === [].constructor) {
        _new_el=new_el;
        new_el=document.createDocumentFragment();
        for(i in _new_el) {
            new_el.appendChild( _new_el[i] );
        }
      }
      
      if(opts.el) {
        opts.el.appendChild(new_el);
      }
      
      // if a posprocessing function is specified in the options, call it
      // use either the specified scope, or the default of null (set earlier)
      if (opts.post) { opts.post.call(opts.scope, new_el, opts.data); }
      
      this.cur_options = null;
      delete opts;
      return new_el;
    },
    renderToString: function(name, data, opts) {
      opts = opts || {};
      delete opts.el;
      
      var el = document.createElement("div");
      el.appendChild(this.render(name, data, opts));
      
      return el.innerHTML;
    
    },
    // function to iterate over a collection and render a previously saved template
    // for each item in the collection
    // uses a document fragment to collect each element and pass it back
    eachRender: function(data, template_name, opts) {
      // dont set cur_options here because that happens in render
      opts = opts || {};
      if(!this.checkRender(opts)) { return ""; }
      var frag = document.createDocumentFragment(), idx=0, i;
      opts.el = frag;
      for(i in data) {
        if(data.hasOwnProperty(i)) {
          opts["_i"] = i;
          opts["_idx"] = idx++;
          this.render(template_name, data[i], opts);
        }
      }
      delete opts;
      return frag;
    },
    // more free form iterator function that allows passing an ad-hoc
    // rendering function to be evaluated for each item in the collection
    // uses a document fragment to collect each element and pass it back
    each: function(data, func, opts) {
      opts = opts || {};
      if(!this.checkRender(opts)) { return ""; }
      this.cur_options = opts;
      var frag = document.createDocumentFragment(), child, idx=0, i;
      for(i in data) {
        if(data.hasOwnProperty(i)) {
          child = func(data[i], i, idx);
          if(child) {
            frag.appendChild(child);
          }
          idx+=1;
        }
      }
      this.cur_options = null;
      return frag;
    },
    checkRender: function(opts) {
      if(this.options.if_attr in opts && !opts[this.options.if_attr]) { return false; }
      return true;
    },
    // function that gets called in initializing the class... loops through
    // list of allowed html elements, and creates a helper function on the prototype
    // to allow easy creation of that element simply by calling its name as a function
    __createMethods: function() {
      //var el_list = "a abbr acronym address applet area b base basefont bdo bgsound big blockquote body br button caption center cite code col colgroup comment custom dd del dfn dir div dl dt em embed fieldset font form frame frameset head h1 h2 h3 h4 h5 h6 hn hr html i iframe img input input ins isindex kbd label legend li link listing map marquee menu meta nobr noframes noscript object ol optgroup option p param plaintext pre q rt ruby s samp script select small span strike strong style sub sup table tbody td textarea tfoot th thead title tr tt u ul var wbr xml xmp video audio";
      var el_list = "a abbr address area article aside audio b base bdi bdo blockquote body br button canvas caption cite code col colgroup command datalist dd del details device dfn div dl dt em embed fieldset figcaption figure footer form h1 h2 h3 h4 h5 h6 head header hgroup hr html i iframe img input ins kbd keygen label legend li link map mark menu meta meter nav noscript object ol optgroup option output p param pre progress q rp rt ruby s samp script section select small source span strong style sub summary sup table tbody td textarea tfoot th thead time title tr track ul var video wbr",
          els = el_list.split(" "),
          prefix = this.options.prefix || "", _this = this;
      
      // extra helper for just grouping a bunch together without a specific parent
      els.push("frag");
      
      this.addEls(els, true);
      
      return this;
    },
    _createEl: function(type) {
      if (type in el_cache && el_cache[type].cloneNode) { return el_cache[type].cloneNode(false);}
      el_cache[type] = document.createElement(type);
      return el_cache[type].cloneNode(false);
    },
    // generic version of the function used to build the element specific creation functions
    // type -> name of element to create
    // attributes (optional) -> object with key/value pairs for attributes to be added to the element
    //                          to avoid silliness with using class as an object key
    //                          you must use "clss" to set class.  yuck
    // content (optional) -> arbitrarily many pieces of content to be added within the element
    //                       can be strings, domElements, or anything that evaluates to either of those
    element: function(type, attributes, content) {
      var args = slice.call(arguments, 1),
          el = (type==="frag" ? document.createDocumentFragment() : this._createEl(type));
      if(args.length) {
        content = args;
      }
      else {
        return el;
      }
      
      if (args[0] && args[0].toString() === "[object Object]") {
        attributes = content.shift();
      }
      else {
        attributes = null;
      }
      
      if(attributes) {
        // when is not an attribute... but can accept a test case that can be used for conditional rendering
        // if it evaluates to true, the node will be rendered... if not, rendering will be short-circuited and an empty string will be returned
        // when is now just the default value for if_attr... this can be overridden using setOption()
        var _local_safe_mode;
        if(!this.checkRender(attributes)){ el = null; return "";}
        delete attributes[this.options.if_attr];
        
        if(this.options.local_safe in attributes) {
          _local_safe_mode = !!attributes[this.options.local_safe];
          delete attributes[this.options.local_safe];
        }
        
        
        for(attr in attributes) {
          switch(attr.toLowerCase()) {
            case "clss":
            case "classname":
              el.className = (attributes[attr].join ? attributes[attr].join(" ") : attributes[attr]);
              break;
            case "style":
              el.cssText = el.style.cssText = attributes[attr];
              break;
            default:
              if(attr.charAt(0) === "_") {
                var attr_name = attr.substring(1);
                if(attributes[attr]) {
                  el.setAttribute(attr_name, attr_name);
                }
              }
              else{
                el.setAttribute(attr, attributes[attr]);
              }
          }
        }
      }
      
      var c;
      for(var i=0, len=content.length; i<len; i++) {
        // if the content is a string, create a Text Node to hold it and append
        // unless (for now) there are html tags or entities in it... then just innerHTML it
        c = content[i];
        switch(typeof c) {
            case "string":
                this.addText(el, c, _local_safe_mode);
            break;
            
            case "number":
                el.appendChild(document.createTextNode(c));
            break;
            case "function":
                var result = c();
                if(typeof result == "string") {
                  this.addText(el, result, _local_safe_mode);
                }
                else {
                  el.appendChild(result);
                }
            break;
            case "undefined":
                break;
            default:
                try{ el.appendChild(c); } catch(ex) { el.appendChild(document.createTextNode(c)); }
            break;
        
        }
      }
      return el;
    },
    addText: function(el, text, _local_safe_mode) {
      if(!this.isSafeMode(_local_safe_mode) && text.match(regex_pattern)) {
        el.innerHTML = text;
      }
      else {
        el.appendChild(document.createTextNode(text));
      }
      return this;
    },
    setOption: function(key, val){
      if (typeof key === "object") {
        for (var _key in key) { this.options[_key] = key[_key]; }
      }
      else {
        this.options[key] = val;
      }
      return this;
    },
    // when safe mode is set to on, any strings
    safeMode: function(on) {
      if(typeof on == "undefined") { on = true; }
      this.options["safe_mode"] = !!on;
      return this;
    },
    isSafeMode: function(_local_safe_mode) {
      if(typeof _local_safe_mode != "undefined") { return !!_local_safe_mode; }
      if (this.cur_options && "safe_mode" in this.cur_options) { return !!this.cur_options.safe_mode; }
      return !!this.options.safe_mode
    },
    // If you want another separate instance of newT, perhaps to keep its own template management
    // call newT.clone() and it will return another freshly initialized copy (with a clear templates object)
    clone: function(options) {
      return new T(options);
    },
    // want to write plugin elements that can do more than just render dom elements?
    // such as dom elements that have some extra processing or ajax requests related to their rendering
    // extend the core newT prototype with this method.
    extend: function(name, func, force, local) {
      if(local) {
        if(!this.hasOwnProperty(name) || force) {
          this[name] = func;
          return true;
        }
      }
      else {
        if(!(name in T.prototype) || force) {
          T.prototype[name] = func;
          return true;
        }
      }
      return false;
    },
    addEls: function(els, force, local) {
      if(typeof els === "string") { els = els.split(" "); }
      var _this = this, args, p_elem=T.prototype.element;
      for(var i=0, len=els.length; i<len; i++) (function(el) {
        _this.extend(el, function() {
              args = slice.call(arguments);
              args.unshift(el);
              return p_elem.apply(_this, args);
        }, force, local);
      })(els[i]);

      delete _this;

      return this;
    },
    noConflict: function(new_name) {
      new_name = new_name || "__newT";
      try{delete window[_last_name];}catch(ex){window[_last_name] = undefined;}
      window[temp] = _old_newt;
      window[new_name] = _temp;
      _last_name = new_name;
      return this;
    }
  }
  
  if (typeof module !== "undefined" && module.exports != null) {
    module.exports = new T();
  } else {
    var _old_newt = window[temp], _temp, _last_name = temp;
    window[temp] = _temp = new T();
  }
  
})();
