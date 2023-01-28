ace.define("ace/snippets/razor.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet if\n(${1} == ${2}) {\n\t${3}\n}";

});

ace.define("ace/snippets/razor",["require","exports","module","ace/snippets/razor.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./razor.snippets");
exports.scope = "razor";

});                (function() {
                    ace.require(["ace/snippets/razor"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            