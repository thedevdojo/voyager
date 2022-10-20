ace.define("ace/snippets/velocity.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# macro\nsnippet #macro\n\t#macro ( ${1:macroName} ${2:\\$var1, [\\$var2, ...]} )\n\t\t${3:## macro code}\n\t#end\n# foreach\nsnippet #foreach\n\t#foreach ( ${1:\\$item} in ${2:\\$collection} )\n\t\t${3:## foreach code}\n\t#end\n# if\nsnippet #if\n\t#if ( ${1:true} )\n\t\t${0}\n\t#end\n# if ... else\nsnippet #ife\n\t#if ( ${1:true} )\n\t\t${2}\n\t#else\n\t\t${0}\n\t#end\n#import\nsnippet #import\n\t#import ( \"${1:path/to/velocity/format}\" )\n# set\nsnippet #set\n\t#set ( $${1:var} = ${0} )\n";

});

ace.define("ace/snippets/velocity",["require","exports","module","ace/snippets/velocity.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./velocity.snippets");
exports.scope = "velocity";
exports.includeScopes = ["html", "javascript", "css"];

});                (function() {
                    ace.require(["ace/snippets/velocity"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            