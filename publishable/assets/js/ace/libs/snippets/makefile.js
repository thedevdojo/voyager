ace.define("ace/snippets/makefile.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet ifeq\n\tifeq (${1:cond0},${2:cond1})\n\t\t${3:code}\n\tendif\n";

});

ace.define("ace/snippets/makefile",["require","exports","module","ace/snippets/makefile.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./makefile.snippets");
exports.scope = "makefile";

});                (function() {
                    ace.require(["ace/snippets/makefile"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            