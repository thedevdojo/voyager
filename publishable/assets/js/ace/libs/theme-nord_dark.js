ace.define("ace/theme/nord_dark.css",["require","exports","module"], function(require, exports, module){module.exports = ".ace-nord-dark .ace_gutter {\n  color: #616e88;\n}\n\n.ace-nord-dark .ace_print-margin {\n  width: 1px;\n  background: #4c566a;\n}\n\n.ace-nord-dark {\n  background-color: #2e3440;\n  color: #d8dee9;\n}\n\n.ace-nord-dark .ace_entity.ace_other.ace_attribute-name,\n.ace-nord-dark .ace_storage {\n  color: #d8dee9;\n}\n\n.ace-nord-dark .ace_cursor {\n  color: #d8dee9;\n}\n\n.ace-nord-dark .ace_string.ace_regexp {\n  color: #bf616a;\n}\n\n.ace-nord-dark .ace_marker-layer .ace_active-line {\n  background: #434c5ecc;\n}\n.ace-nord-dark .ace_marker-layer .ace_selection {\n  background: #434c5ecc;\n}\n\n.ace-nord-dark.ace_multiselect .ace_selection.ace_start {\n  box-shadow: 0 0 3px 0px #2e3440;\n}\n\n.ace-nord-dark .ace_marker-layer .ace_step {\n  background: #ebcb8b;\n}\n\n.ace-nord-dark .ace_marker-layer .ace_bracket {\n  margin: -1px 0 0 -1px;\n  border: 1px solid #88c0d066;\n}\n\n.ace-nord-dark .ace_gutter-active-line {\n  background-color: #434c5ecc;\n}\n\n.ace-nord-dark .ace_marker-layer .ace_selected-word {\n  border: 1px solid #88c0d066;\n}\n\n.ace-nord-dark .ace_invisible {\n  color: #4c566a;\n}\n\n.ace-nord-dark .ace_keyword,\n.ace-nord-dark .ace_meta,\n.ace-nord-dark .ace_support.ace_class,\n.ace-nord-dark .ace_support.ace_type {\n  color: #81a1c1;\n}\n\n.ace-nord-dark .ace_constant.ace_character,\n.ace-nord-dark .ace_constant.ace_other {\n  color: #d8dee9;\n}\n\n.ace-nord-dark .ace_constant.ace_language {\n  color: #5e81ac;\n}\n\n.ace-nord-dark .ace_constant.ace_escape {\n  color: #ebcB8b;\n}\n\n.ace-nord-dark .ace_constant.ace_numeric {\n  color: #b48ead;\n}\n\n.ace-nord-dark .ace_fold {\n  background-color: #4c566a;\n  border-color: #d8dee9;\n}\n\n.ace-nord-dark .ace_entity.ace_name.ace_function,\n.ace-nord-dark .ace_entity.ace_name.ace_tag,\n.ace-nord-dark .ace_support.ace_function,\n.ace-nord-dark .ace_variable,\n.ace-nord-dark .ace_variable.ace_language {\n  color: #8fbcbb;\n}\n\n.ace-nord-dark .ace_string {\n  color: #a3be8c;\n}\n\n.ace-nord-dark .ace_comment {\n  color: #616e88;\n}\n\n.ace-nord-dark .ace_indent-guide {\n  box-shadow: inset -1px 0 0 0 #434c5eb3;\n}\n\n.ace-nord-dark .ace_indent-guide-active {\n  box-shadow: inset -1px 0 0 0 #8395b8b3;\n}\n";

});

ace.define("ace/theme/nord_dark",["require","exports","module","ace/theme/nord_dark.css","ace/lib/dom"], function(require, exports, module){exports.isDark = true;
exports.cssClass = "ace-nord-dark";
exports.cssText = require("./nord_dark.css");
exports.$selectionColorConflict = true;
var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass, false);

});                (function() {
                    ace.require(["ace/theme/nord_dark"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            