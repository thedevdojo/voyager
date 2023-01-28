ace.define("ace/theme/clouds.css",["require","exports","module"], function(require, exports, module){module.exports = ".ace-clouds .ace_gutter {\n  background: #ebebeb;\n  color: #333\n}\n\n.ace-clouds .ace_print-margin {\n  width: 1px;\n  background: #e8e8e8\n}\n\n.ace-clouds {\n  background-color: #FFFFFF;\n  color: #000000\n}\n\n.ace-clouds .ace_cursor {\n  color: #000000\n}\n\n.ace-clouds .ace_marker-layer .ace_selection {\n  background: #BDD5FC\n}\n\n.ace-clouds.ace_multiselect .ace_selection.ace_start {\n  box-shadow: 0 0 3px 0px #FFFFFF;\n}\n\n.ace-clouds .ace_marker-layer .ace_step {\n  background: rgb(255, 255, 0)\n}\n\n.ace-clouds .ace_marker-layer .ace_bracket {\n  margin: -1px 0 0 -1px;\n  border: 1px solid #BFBFBF\n}\n\n.ace-clouds .ace_marker-layer .ace_active-line {\n  background: #FFFBD1\n}\n\n.ace-clouds .ace_gutter-active-line {\n  background-color : #dcdcdc\n}\n\n.ace-clouds .ace_marker-layer .ace_selected-word {\n  border: 1px solid #BDD5FC\n}\n\n.ace-clouds .ace_invisible {\n  color: #BFBFBF\n}\n\n.ace-clouds .ace_keyword,\n.ace-clouds .ace_meta,\n.ace-clouds .ace_support.ace_constant.ace_property-value {\n  color: #AF956F\n}\n\n.ace-clouds .ace_keyword.ace_operator {\n  color: #484848\n}\n\n.ace-clouds .ace_keyword.ace_other.ace_unit {\n  color: #96DC5F\n}\n\n.ace-clouds .ace_constant.ace_language {\n  color: #39946A\n}\n\n.ace-clouds .ace_constant.ace_numeric {\n  color: #46A609\n}\n\n.ace-clouds .ace_constant.ace_character.ace_entity {\n  color: #BF78CC\n}\n\n.ace-clouds .ace_invalid {\n  background-color: #FF002A\n}\n\n.ace-clouds .ace_fold {\n  background-color: #AF956F;\n  border-color: #000000\n}\n\n.ace-clouds .ace_storage,\n.ace-clouds .ace_support.ace_class,\n.ace-clouds .ace_support.ace_function,\n.ace-clouds .ace_support.ace_other,\n.ace-clouds .ace_support.ace_type {\n  color: #C52727\n}\n\n.ace-clouds .ace_string {\n  color: #5D90CD\n}\n\n.ace-clouds .ace_comment {\n  color: #BCC8BA\n}\n\n.ace-clouds .ace_entity.ace_name.ace_tag,\n.ace-clouds .ace_entity.ace_other.ace_attribute-name {\n  color: #606060\n}\n\n.ace-clouds .ace_indent-guide {\n  background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bLly//BwAmVgd1/w11/gAAAABJRU5ErkJggg==\") right repeat-y\n}\n\n.ace-clouds .ace_indent-guide-active {\n  background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAAZSURBVHjaYvj///9/hivKyv8BAAAA//8DACLqBhbvk+/eAAAAAElFTkSuQmCC\") right repeat-y;\n} \n";

});

ace.define("ace/theme/clouds",["require","exports","module","ace/theme/clouds.css","ace/lib/dom"], function(require, exports, module){exports.isDark = false;
exports.cssClass = "ace-clouds";
exports.cssText = require("./clouds.css");
var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass, false);

});                (function() {
                    ace.require(["ace/theme/clouds"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            