ace.define("ace/theme/textmate",["require","exports","module","ace/theme/textmate.css","ace/lib/dom"], function(require, exports, module){"use strict";
exports.isDark = false;
exports.cssClass = "ace-tm";
exports.cssText = require("./textmate.css");
exports.$id = "ace/theme/textmate";
var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass, false);

});                (function() {
                    ace.require(["ace/theme/textmate"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            