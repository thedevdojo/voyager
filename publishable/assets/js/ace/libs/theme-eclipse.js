ace.define("ace/theme/eclipse.css",["require","exports","module"], function(require, exports, module){module.exports = ".ace-eclipse .ace_gutter {\n  background: #ebebeb;\n  border-right: 1px solid rgb(159, 159, 159);\n  color: rgb(136, 136, 136);\n}\n\n.ace-eclipse .ace_print-margin {\n  width: 1px;\n  background: #ebebeb;\n}\n\n.ace-eclipse {\n  background-color: #FFFFFF;\n  color: black;\n}\n\n.ace-eclipse .ace_fold {\n    background-color: rgb(60, 76, 114);\n}\n\n.ace-eclipse .ace_cursor {\n  color: black;\n}\n\n.ace-eclipse .ace_storage,\n.ace-eclipse .ace_keyword,\n.ace-eclipse .ace_variable {\n  color: rgb(127, 0, 85);\n}\n\n.ace-eclipse .ace_constant.ace_buildin {\n  color: rgb(88, 72, 246);\n}\n\n.ace-eclipse .ace_constant.ace_library {\n  color: rgb(6, 150, 14);\n}\n\n.ace-eclipse .ace_function {\n  color: rgb(60, 76, 114);\n}\n\n.ace-eclipse .ace_string {\n  color: rgb(42, 0, 255);\n}\n\n.ace-eclipse .ace_comment {\n  color: rgb(113, 150, 130);\n}\n\n.ace-eclipse .ace_comment.ace_doc {\n  color: rgb(63, 95, 191);\n}\n\n.ace-eclipse .ace_comment.ace_doc.ace_tag {\n  color: rgb(127, 159, 191);\n}\n\n.ace-eclipse .ace_constant.ace_numeric {\n  color: darkblue;\n}\n\n.ace-eclipse .ace_tag {\n  color: rgb(25, 118, 116);\n}\n\n.ace-eclipse .ace_type {\n  color: rgb(127, 0, 127);\n}\n\n.ace-eclipse .ace_xml-pe {\n  color: rgb(104, 104, 91);\n}\n\n.ace-eclipse .ace_marker-layer .ace_selection {\n  background: rgb(181, 213, 255);\n}\n\n.ace-eclipse .ace_marker-layer .ace_bracket {\n  margin: -1px 0 0 -1px;\n  border: 1px solid rgb(192, 192, 192);\n}\n\n.ace-eclipse .ace_meta.ace_tag {\n  color:rgb(25, 118, 116);\n}\n\n.ace-eclipse .ace_invisible {\n  color: #ddd;\n}\n\n.ace-eclipse .ace_entity.ace_other.ace_attribute-name {\n  color:rgb(127, 0, 127);\n}\n.ace-eclipse .ace_marker-layer .ace_step {\n  background: rgb(255, 255, 0);\n}\n\n.ace-eclipse .ace_active-line {\n  background: rgb(232, 242, 254);\n}\n\n.ace-eclipse .ace_gutter-active-line {\n  background-color : #DADADA;\n}\n\n.ace-eclipse .ace_marker-layer .ace_selected-word {\n  border: 1px solid rgb(181, 213, 255);\n}\n\n.ace-eclipse .ace_indent-guide {\n  background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bLly//BwAmVgd1/w11/gAAAABJRU5ErkJggg==\") right repeat-y;\n}\n\n.ace-eclipse .ace_indent-guide-active {\n  background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAAZSURBVHjaYvj///9/hivKyv8BAAAA//8DACLqBhbvk+/eAAAAAElFTkSuQmCC\") right repeat-y;\n} \n";

});

ace.define("ace/theme/eclipse",["require","exports","module","ace/theme/eclipse.css","ace/lib/dom"], function(require, exports, module){"use strict";
exports.isDark = false;
exports.cssText = require("./eclipse.css");
exports.cssClass = "ace-eclipse";
var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass, false);

});                (function() {
                    ace.require(["ace/theme/eclipse"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            