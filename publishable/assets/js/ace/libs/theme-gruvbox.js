ace.define("ace/theme/gruvbox.css",["require","exports","module"], function(require, exports, module){module.exports = ".ace-gruvbox .ace_gutter-active-line {\n  background-color: #3C3836;\n}\n\n.ace-gruvbox {\n  color: #EBDAB4;\n  background-color: #1D2021;\n}\n\n.ace-gruvbox .ace_invisible {\n  color: #504945;\n}\n\n.ace-gruvbox .ace_marker-layer .ace_selection {\n  background: rgba(179, 101, 57, 0.75)\n}\n\n.ace-gruvbox.ace_multiselect .ace_selection.ace_start {\n  box-shadow: 0 0 3px 0px #002240;\n}\n\n.ace-gruvbox .ace_keyword {\n  color: #8ec07c;\n}\n\n.ace-gruvbox .ace_comment {\n  font-style: italic;\n  color: #928375;\n}\n\n.ace-gruvbox .ace-statement {\n  color: red;\n}\n\n.ace-gruvbox .ace_variable {\n  color: #84A598;\n}\n\n.ace-gruvbox .ace_variable.ace_language {\n  color: #D2879B;\n}\n\n.ace-gruvbox .ace_constant {\n  color: #C2859A;\n}\n\n.ace-gruvbox .ace_constant.ace_language {\n  color: #C2859A;\n}\n\n.ace-gruvbox .ace_constant.ace_numeric {\n  color: #C2859A;\n}\n\n.ace-gruvbox .ace_string {\n  color: #B8BA37;\n}\n\n.ace-gruvbox .ace_support {\n  color: #F9BC41;\n}\n\n.ace-gruvbox .ace_support.ace_function {\n  color: #F84B3C;\n}\n\n.ace-gruvbox .ace_storage {\n  color: #8FBF7F;\n}\n\n.ace-gruvbox .ace_keyword.ace_operator {\n  color: #EBDAB4;\n}\n\n.ace-gruvbox .ace_punctuation.ace_operator {\n  color: yellow;\n}\n\n.ace-gruvbox .ace_marker-layer .ace_active-line {\n  background: #3C3836;\n}\n\n.ace-gruvbox .ace_marker-layer .ace_selected-word {\n  border-radius: 4px;\n  border: 8px solid #3f475d;\n}\n\n.ace-gruvbox .ace_print-margin {\n  width: 5px;\n  background: #3C3836;\n}\n\n.ace-gruvbox .ace_indent-guide {\n  background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAEklEQVQImWNQUFD4z6Crq/sfAAuYAuYl+7lfAAAAAElFTkSuQmCC\") right repeat-y;\n}\n\n.ace-gruvbox .ace_indent-guide-active {\n  background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAAZSURBVHjaYvj///9/hivKyv8BAAAA//8DACLqBhbvk+/eAAAAAElFTkSuQmCC\") right repeat-y;\n}\n";

});

ace.define("ace/theme/gruvbox",["require","exports","module","ace/theme/gruvbox.css","ace/lib/dom"], function(require, exports, module){exports.isDark = true;
exports.cssClass = "ace-gruvbox";
exports.cssText = require("./gruvbox.css");
var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass, false);

});                (function() {
                    ace.require(["ace/theme/gruvbox"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            