ace.define("ace/snippets/maze.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet >\ndescription assignment\nscope maze\n\t-> ${1}= ${2}\n\nsnippet >\ndescription if\nscope maze\n\t-> IF ${2:**} THEN %${3:L} ELSE %${4:R}\n";

});

ace.define("ace/snippets/maze",["require","exports","module","ace/snippets/maze.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./maze.snippets");
exports.scope = "maze";

});                (function() {
                    ace.require(["ace/snippets/maze"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            