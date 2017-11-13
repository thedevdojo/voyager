/*
 * Slugify for Voyager v0.9.0
 *
 * Generates a slug for a given input element.
 * This script was created for Voyager, but works with any HTML structure.
 *
 * Default behavior is to auto generate a new slug, only if the input is empty.
 * If input isn't empty, the auto generation is disabled.
 * To force the auto generator, set the option "forceUpdate: true".
 *
 * Copyright 2017 Bruno Torrinha
 * License MIT
 *
 * Some credits:
 * Char map from: https://github.com/diegok/slugit-jquery
 */
;( function( $, window, document, undefined ) {

    "use strict";

        var pluginName = "slugify",
            defaults = {
                separator:   '-',
                input:       false, // The origin from where we generate the slug.
                forceUpdate: false, // Force update if input is not empty.
                map:         false  // Provide an extra character map translator.
            };

        function Plugin ( element, options ) {
            this.element   = $(element);  // The input where slug is placed.
            this.settings  = $.extend( {}, defaults, options );
            this._defaults = defaults;
            this.chars     = this._load_char_maps();
            if (!this.settings.map) {      // Load extra character map translator
                $.extend(this.chars, this.settings.map);
            }
            this.init();
        }

        // Avoid Plugin.prototype conflicts
        $.extend( Plugin.prototype, {
            init: function() {
                this.input = this.settings.input
                             || $(this.element).closest('form').find('input[name="' + this.element.attr("data-slug-origin") + '"]');

                this.forceUpdate = (this.element.data('slug-forceupdate')) ? true : false;
                this.input.on('keyup change', $.proxy(this.onChange, this));

                this.refresh();
            },


            refresh: function() {
                this.element.update = this.element.val() === '';
            },


            /**
             * When input changes
             */
            onChange: function(ev) {
                var code = ev.keyCode ? ev.keyCode : ev.which;

                if (code > 34 && code < 41) {
                    return;
                }

                var strOrigin = $(ev.target).val(),
                    strTarget = this.element.val();

                if (
                    this.element.update
                    || strTarget === ''
                    || (strTarget != '' && this.forceUpdate)
                ){
                    this.element.val(this.slug(strOrigin));
                    this.element.update = true;
                }
                return;
            },


            /**
             * Generate a slug
             */
            slug: function(str) {
                str = str
                    .toString()
                    .toLowerCase();

                var _slug = '',
                    _sep = this.settings.separator;

                // Replace Char Map
                //
                for (var i=0, l=str.length ; i<l ; i++) {
                    _slug += (this.chars[str.charAt(i)])
                             ? this.chars[str.charAt(i)]
                             : str.charAt(i);
                }

                str = _slug
                    .replace(/^\s+|\s+$/g, '')      // Trim
                    .replace(/[^-\u0600-۾\w\d\$\*\(\)\'\!\_]/g, _sep)   // Remove invalid chars
                    .replace(/\s+/g, _sep)          // Replace spaces with separator
                    .replace(/\-\-+/g, _sep);       // Replace multiple separators with single

                return str;
            },

            _load_char_maps: function() {
                return $.extend(
                            this._map_latin(),
                            this._map_greek(),
                            this._map_turkish(),
                            this._map_russian(),
                            this._map_ukranian(),
                            this._map_czech(),
                            this._map_polish(),
                            this._map_vietnam(),
                            this._map_latvian(),
                            this._map_currency(),
                            this._map_symbols()
                        );
            },
            _map_latin: function() {
                return {
                    'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ç':
                    'C', 'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I', 'Î': 'I',
                    'Ï': 'I', 'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö':
                    'O', 'Ő': 'O', 'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ű': 'U',
                    'Ý': 'Y', 'Þ': 'TH', 'ß': 'ss', 'à':'a', 'á':'a', 'â': 'a', 'ã': 'a', 'ä':
                    'a', 'å': 'a', 'æ': 'ae', 'ç': 'c', 'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
                    'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i', 'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó':
                    'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ő': 'o', 'ø': 'o', 'ù': 'u', 'ú': 'u',
                    'û': 'u', 'ü': 'u', 'ű': 'u', 'ý': 'y', 'þ': 'th', 'ÿ': 'y'
                };
            },
            _map_greek: function() {
                return {
                    'α':'a', 'β':'b', 'γ':'g', 'δ':'d', 'ε':'e', 'ζ':'z', 'η':'h', 'θ':'8',
                    'ι':'i', 'κ':'k', 'λ':'l', 'μ':'m', 'ν':'n', 'ξ':'3', 'ο':'o', 'π':'p',
                    'ρ':'r', 'σ':'s', 'τ':'t', 'υ':'y', 'φ':'f', 'χ':'x', 'ψ':'ps', 'ω':'w',
                    'ά':'a', 'έ':'e', 'ί':'i', 'ό':'o', 'ύ':'y', 'ή':'h', 'ώ':'w', 'ς':'s',
                    'ϊ':'i', 'ΰ':'y', 'ϋ':'y', 'ΐ':'i',
                    'Α':'A', 'Β':'B', 'Γ':'G', 'Δ':'D', 'Ε':'E', 'Ζ':'Z', 'Η':'H', 'Θ':'8',
                    'Ι':'I', 'Κ':'K', 'Λ':'L', 'Μ':'M', 'Ν':'N', 'Ξ':'3', 'Ο':'O', 'Π':'P',
                    'Ρ':'R', 'Σ':'S', 'Τ':'T', 'Υ':'Y', 'Φ':'F', 'Χ':'X', 'Ψ':'PS', 'Ω':'W',
                    'Ά':'A', 'Έ':'E', 'Ί':'I', 'Ό':'O', 'Ύ':'Y', 'Ή':'H', 'Ώ':'W', 'Ϊ':'I',
                    'Ϋ':'Y'
                };
            },
            _map_turkish: function() {
                return {
                    'ş':'s', 'Ş':'S', 'ı':'i', 'İ':'I', 'ç':'c', 'Ç':'C', 'ü':'u', 'Ü':'U',
                    'ö':'o', 'Ö':'O', 'ğ':'g', 'Ğ':'G'
                };
            },
            _map_russian: function() {
                return {
                    'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ё':'yo', 'ж':'zh',
                    'з':'z', 'и':'i', 'й':'j', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o',
                    'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'х':'h', 'ц':'c',
                    'ч':'ch', 'ш':'sh', 'щ':'sh', 'ъ':'', 'ы':'y', 'ь':'', 'э':'e', 'ю':'yu',
                    'я':'ya',
                    'А':'A', 'Б':'B', 'В':'V', 'Г':'G', 'Д':'D', 'Е':'E', 'Ё':'Yo', 'Ж':'Zh',
                    'З':'Z', 'И':'I', 'Й':'J', 'К':'K', 'Л':'L', 'М':'M', 'Н':'N', 'О':'O',
                    'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F', 'Х':'H', 'Ц':'C',
                    'Ч':'Ch', 'Ш':'Sh', 'Щ':'Sh', 'Ъ':'', 'Ы':'Y', 'Ь':'', 'Э':'E', 'Ю':'Yu',
                    'Я':'Ya'
                };
            },
            _map_ukranian: function() {
                return {
                    'Є':'Ye', 'І':'I', 'Ї':'Yi', 'Ґ':'G', 'є':'ye', 'і':'i', 'ї':'yi', 'ґ':'g'
                };
            },
            _map_czech: function() {
                return {
                    'č':'c', 'ď':'d', 'ě':'e', 'ň': 'n', 'ř':'r', 'š':'s', 'ť':'t', 'ů':'u',
                    'ž':'z', 'Č':'C', 'Ď':'D', 'Ě':'E', 'Ň': 'N', 'Ř':'R', 'Š':'S', 'Ť':'T',
                    'Ů':'U', 'Ž':'Z'
                };
            },
            _map_polish: function() {
                return {
                    'ą':'a', 'ć':'c', 'ę':'e', 'ł':'l', 'ń':'n', 'ó':'o', 'ś':'s', 'ź':'z',
                    'ż':'z', 'Ą':'A', 'Ć':'C', 'Ę':'e', 'Ł':'L', 'Ń':'N', 'Ó':'o', 'Ś':'S',
                    'Ź':'Z', 'Ż':'Z'
                };
            },
            _map_vietnam: function() {
                return {
                    'ạ': 'a','ả': 'a','ầ': 'a','ấ': 'a','ậ': 'a','ẩ': 'a','ẫ': 'a','ằ': 'a',
                    'ắ': 'a','ặ': 'a','ẳ': 'a','ẵ': 'a','ẹ': 'e','ẻ': 'e','ẽ': 'e','ề': 'e',
                    'ế': 'e','ệ': 'e','ể': 'e','ễ': 'e','ị': 'i','ỉ': 'i','ọ': 'o','ỏ': 'o',
                    'ồ': 'o','ố': 'o','ộ': 'o','ổ': 'o','ỗ': 'o','ờ': 'o','ớ': 'o','ợ': 'o',
                    'ở': 'o','ỡ': 'o','ụ': 'u','ủ': 'u','ừ': 'u','ứ': 'u','ự': 'u','ử': 'u',
                    'ữ': 'u','ỳ': 'y','ỵ': 'y','ỷ': 'y','ỹ': 'y','Ạ': 'A','Ả': 'A','Ầ': 'A',
                    'Ấ': 'A','Ậ': 'A','Ẩ': 'A','Ẫ': 'A','Ằ': 'A','Ắ': 'A','Ặ': 'A','Ẳ': 'A',
                    'Ẵ': 'A','Ẹ': 'E','Ẻ': 'E','Ẽ': 'E','Ề': 'E','Ế': 'E','Ệ': 'E','Ể': 'E',
                    'Ễ': 'E','Ị': 'I','Ỉ': 'I','Ọ': 'O','Ỏ': 'O','Ồ': 'O','Ố': 'O','Ộ': 'O',
                    'Ổ': 'O','Ỗ': 'O','Ờ': 'O','Ớ': 'O','Ợ': 'O','Ở': 'O','Ỡ': 'O','Ụ': 'U',
                    'Ủ': 'U','Ừ': 'U','Ứ': 'U','Ự': 'U','Ử': 'U','Ữ': 'U','Ỳ': 'Y','Ỵ': 'Y',
                    'đ': 'd','Đ': 'D','Ỷ': 'Y','Ỹ': 'Y'
                };
            },
            _map_latvian: function() {
                return {
                    'ā':'a', 'č':'c', 'ē':'e', 'ģ':'g', 'ī':'i', 'ķ':'k', 'ļ':'l', 'ņ':'n',
                    'š':'s', 'ū':'u', 'ž':'z', 'Ā':'A', 'Č':'C', 'Ē':'E', 'Ģ':'G', 'Ī':'i',
                    'Ķ':'k', 'Ļ':'L', 'Ņ':'N', 'Š':'S', 'Ū':'u', 'Ž':'Z'
                };
            },
            _map_currency: function() {
                return {
                    '€': 'euro', '$': 'dollar', '₢': 'cruzeiro', '₣': 'french franc', '£': 'pound',
                    '₤': 'lira', '₥': 'mill', '₦': 'naira', '₧': 'peseta', '₨': 'rupee',
                    '₩': 'won', '₪': 'new shequel', '₫': 'dong', '₭': 'kip', '₮': 'tugrik',
                    '₯': 'drachma', '₰': 'penny', '₱': 'peso', '₲': 'guarani', '₳': 'austral',
                    '₴': 'hryvnia', '₵': 'cedi', '¢': 'cent', '¥': 'yen', '元': 'yuan',
                    '円': 'yen', '﷼': 'rial', '₠': 'ecu', '¤': 'currency', '฿': 'baht'
                };
            },
            _map_symbols: function() {
                return {
                    '©':'(c)', 'œ': 'oe', 'Œ': 'OE', '∑': 'sum', '®': '(r)', '†': '+',
                    '“': '"', '”': '"', '‘': "'", '’': "'", '∂': 'd', 'ƒ': 'f', '™': 'tm',
                    '℠': 'sm', '…': '...', '˚': 'o', 'º': 'o', 'ª': 'a', '•': '*',
                    '∆': 'delta', '∞': 'infinity', '♥': 'love', '&': 'and'
                };
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
