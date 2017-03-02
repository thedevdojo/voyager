/**
 *  Multilingual System
 *
 *  Version Alpha
 *  Last version: 02 Mar 2017
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
 *  * Language Selector, triggering the content update. It will be hidden, case
 *    model is not translatable.
 *  * Blade partial views created and included in: [browse, read, edit-add].blade.
 *
 *  TO-DO
 *  * Google Translator, triggered by a link placed on the top right of the input.
 *  * Option for showing the fall-back version of the field, under the input.
 *    This would apply to text input only.
 *  * Global page administrator, listing all the translated fields on the system.
 *
 */
;( function( $, window, document, undefined ) {

    "use strict";

        var pluginName = "multilingual",
            defaults = {
                editing:       false,                       // Editing or View
                form:          '.form-edit-add',
                transInputs:   'input[data-i18n = true]',   // Hidden inputs holding translations
                langSelectors: '> .language-selector input' // Language selector inputs
            };

        function Plugin ( element, options ) {
            this.element   = $(element);
            this.settings  = $.extend( {}, defaults, options );
            this._defaults = defaults;
            this._name     = pluginName;
            this.init();
        }

        $.extend( Plugin.prototype, {
            init: function() {
                this.form          = this.element.find(this.settings.form);
                this.transInputs   = $(this.settings.transInputs);
                this.langSelectors = this.element.find(this.settings.langSelectors);

                if (this.transInputs.length === 0 || this.langSelectors === 0) {
                    return false;
                }
                this.setup();
            },


            setup: function() {
                var _this = this;

                this.localeDefaultBtn = this.returnLocale();
                this.locale = this.localeDefaultBtn.prop('id');

                /**
                 * Setup language selector
                 */
                this.langSelectors.each(function(i, btn) {
                    $(btn).change($.proxy(_this.selectLanguage, _this));
                });

                /**
                 * Setup translatable inputs
                 */
                this.transInputs.each(function(i, inp) {
                    var _inp   = $(inp),
                        inpUsr = _inp.next(_this.settings.editing ? '.form-control' : '');

                    inpUsr.data("inp", _inp);
                    _inp.data("inpUsr", inpUsr);

                    // Load and Save data in hidden input
                    var $_data = (_this.isJsonValid(_inp.val())) ? JSON.parse(_inp.val()) : {};

                    if (_this.settings.editing) {
                        _inp.val(JSON.stringify($_data));
                    }

                    // Save each language in a different key
                    _this.langSelectors.each(function(i, btn) {
                        _inp.data(btn.id, $_data[btn.id]);
                    });
                });

                /**
                 * Save data before submit
                 */
                if (this.settings.editing) {
                    $(this.form).on('submit', function(e) {
                        e.preventDefault();
                        _this.localeDefaultBtn.click();
                        _this.prepareData();
                        $(_this.form)[0].submit();
                    });
                }
            },

            isJsonValid: function(str) {
                try {
                    JSON.parse(str);
                } catch (ex) {
                    return false;
                }
                return true;
            },

            /**
             * Return Locale for a given Button Group Selector
             *
             * @return string The locale.
             */
            returnLocale: function() {
                return this.langSelectors.filter(function() {
                    return $(this).parent().hasClass('active');
                });
            },

            selectLanguage: function(e) {
                var _this = this,
                    lang  = e.target.id;

                this.transInputs.each(function(i, inp) {
                    if (_this.settings.editing) {
                        _this.updateInputCache($(inp));
                    }
                    _this.loadLang($(inp), lang);
                });

                this.locale = lang;
            },

            /**
             * Update cache for all inputs, and prepare form data for submit
             */
            prepareData: function() {
                var _this = this;
                this.transInputs.each(function(i, inp) {
                    _this.updateInputCache($(inp));
                });
            },

            /**
             * Update cache for a single input
             */
            updateInputCache: function(inp) {
                var _this  = this,
                    inpUsr = inp.data('inpUsr'),
                    $_val  = $(inpUsr).val(),
                    $_data = {};  // Create new data

                if (inpUsr.hasClass('richTextBox')) {
                    var $_mce = tinymce.get('richtext'+inpUsr.prop('name'));
                    $_val = $_mce.getContent();
                }

                this.langSelectors.each(function(i, btn) {
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
