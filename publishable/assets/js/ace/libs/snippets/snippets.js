ace.define("ace/snippets/snippets.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# snippets for making snippets :)\nsnippet snip\n\tsnippet ${1:trigger}\n\t\t${2}\nsnippet msnip\n\tsnippet ${1:trigger} ${2:description}\n\t\t${3}\nsnippet v\n\t{VISUAL}\n";

});

ace.define("ace/snippets/snippets",["require","exports","module","ace/snippets/snippets.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./snippets.snippets");
exports.scope = "snippets";

});                (function() {
                    ace.require(["ace/snippets/snippets"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            