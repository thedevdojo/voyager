/*
 *  Multilingual System
 *
 *  Version Alpha
 *  First version: 18 Dec 2016
 *  Author Bruno Torrinha
 *
 *  MIT License
 *
 *  Provides a mechanism for easily manage multi languages on a single page.
 *  It was created for the BREAD system of Voyager, but can be implemented
 *  with any html structure.
 *  For each translatable field, the system requires an hidden input containing
 *  all translations.
 *  This is an alpha version and the final version may change, there are a few
 *  considerations to take from here.
 *  Every time a translatable model is opened, all languages are being pulled,
 *  while this may work well on websites with just a few languages, in case
 *  of 20+, it may gets odd, it requires testing.
 *
 *  Features
 *  * For setting up fields it's easy, go to the BREAD Edit, and add to the
 *    Options Details: {"translate":true}
 *  * Configuration options available:
 *    - locale (default system locale);
 *    - languages (array with available languages);
 *    - translatable (array modules with multi language system);
 *  * Language Selector, triggering the content update. It will be hidden, case
 *    model is not translatable.
 *  * Blade views created and included in: browse.blade and edit-add.blade.
 *
 *  TO-DO
 *  * Google Translator, triggered by a link placed on the top right of the input.
 *  * Option for showing the original version of the field, under the input.
 *  * Global page administrator, listing all the translated fields on the system.
 *
 */
;( function( $, window, document, undefined ) {

    "use strict";

        var pluginName = "multilingual",
            defaults = {
                form:           '.form-edit-add',
                langInputs:     'input[data-multilingual=true]',
                langSelector:   '> .language-selector input',
                locale:         '',    // Selected Locale, starts with the active language on button selector.
                editing:        false, // Editing or View
                copyFromLocale: true   // Duplicate default locate on empty fields, if false
            };

        function Plugin ( element, options ) {
            this.element = $(element);

            this.settings = $.extend( {}, defaults, options );
            this._defaults = defaults;
            this._name = pluginName;
            this.init();
        }

        // Avoid Plugin.prototype conflicts
        $.extend( Plugin.prototype, {
            init: function() {
                this.form         = this.element.find(this.settings.form);
                this.langInputs   = $(this.settings.langInputs);
                this.langSelector = this.element.find(this.settings.langSelector);
                if (this.langInputs.length === 0 || this.langSelector === 0) {
                    return false;
                }
                this.locale = this.returnLocale();
                this.setup();
                this.refresh();
            },

            setup: function() {
                var _this = this;
                /**
                 * Setup Language Selector
                 */
                this.langSelector.each(function(i, btn) {
                    $(btn).change($.proxy(_this.selectLanguage, _this));
                });

                /**
                 * Save data before submit
                 */
                if (this.settings.editing) {
                    $(this.form).on('submit', function(e) {
                        e.preventDefault();
                        _this.updateFieldsCache();
                        $(_this.form)[0].submit();
                    });
                }
            },

            /**
             * Refresh plugin data, available for dynamic calls or AJAX
             */
            refresh: function() {
                var _this   = this;
                this.locale = this.returnLocale();
                /**
                 * Setup the Translated Inputs
                 */
                this.langInputs.each(function(i, inp) {
                    var _inp   = $(inp),     // Input hidden
                        inpUsr = _inp.next(_this.settings.editing ? '.form-control' : '');

                    inpUsr.data("inp", _inp);
                    _inp.data("inpUsr", inpUsr);

                    // Save data in input hidden
                    var $_data = _this.loadJsonField(_inp.val());
                    if (_this.settings.editing) {
                        _inp.val( JSON.stringify($_data));
                    }

                    // Save each language in a different memory key
                    _this.langSelector.each(function(i, btn) {
                        _inp.data(btn.id, $_data[btn.id]);
                    });
                });

                // console.log(this.form);
                // console.log(this.langSelector);
                // console.log(this.langInputs);
            },


            loadJsonField: function(str) {
                var $_data = {};
                if (this.isJsonValid(str)) {
                    return JSON.parse(str);
                }
                /**
                 * JSON is invalid, we have to create a new object for this field.
                 * This happens after a field is defined has translatable, at the BREAD manager.
                 * This should be moved to php, and always load a JSON type.
                 */
                else {
                    var _this = this,
                        _str  = str;
                    this.langSelector.each(function(i, btn) {  // loop languages
                        $_data[btn.id] = (_this.locale == btn.id || _this.settings.copyFromLocale)
                                         ? _str
                                         : '';
                    });
                }
                return $_data;
            },

            /**
             * Return Locale for a given Button Group Selector
             *
             * @return string    The locale.
             */
            returnLocale: function() {
                var btn = this.langSelector.filter(function() {
                    return $(this).parent().hasClass('active');
                });
                return btn.prop('id');
            },

            isJsonValid: function(str) {
                try {
                    JSON.parse(str);
                } catch (ex) {
                    return false;
                }
                return true;
            },

            selectLanguage: function(e) {
                var _this = this,
                    lang  = e.target.id;

                this.langInputs.each(function(i, inp) {
                    if (_this.settings.editing) {
                        _this.updateFieldCache($(inp));
                    }
                    _this.loadLang($(inp), lang);
                });
                this.locale = lang;
            },

            /**
             * Update Cache for All Inputs
             */
            updateFieldsCache: function() {
                var _this = this;
                this.langInputs.each(function(i, inp) {
                    _this.updateFieldCache($(inp));
                });
            },

            /**
             * Update Cache for a Single Input
             */
            updateFieldCache: function(inp) {
                var _this  = this,
                    inpUsr = inp.data('inpUsr'),
                    $_val  = $(inpUsr).val(),
                    $_data = {};  // Create new data

                if (inpUsr.hasClass('richTextBox')) {
                    var $_mce = tinymce.get('richtext'+inpUsr.prop('name'));
                    $_val = $_mce.getContent();
                }

                this.langSelector.each(function(i, btn) {
                    var lang = btn.id;
                    $_data[lang] = (_this.locale == lang) ? $_val : inp.data(lang);
                });

                inp.val(JSON.stringify($_data));
                inp.data(this.locale, $_val);     // Update single key Mem
            },

            /**
             * Load input field
             */
            loadLang: function(inp, lang) {
                var inpUsr = inp.data("inpUsr"),
                    _val   = inp.data(lang);

                if (!this.settings.editing) {
                    inpUsr.text(_val);
                } else {
                    if (inpUsr.hasClass('richTextBox')) {
                        var $_mce = tinymce.get('richtext'+inpUsr.prop('name'));
                        $_mce.setContent(_val);
                    } else {
                        inpUsr.val(_val);
                    }
                }
            }
        });

        $.fn[ pluginName ] = function( options ) {
            return this.each( function() {
                if ( !$.data( this, pluginName ) ) {
                    $.data( this, pluginName, new Plugin(this, options) );
                }
            } );
        };

} )( jQuery, window, document );
