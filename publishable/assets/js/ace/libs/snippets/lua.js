ace.define("ace/snippets/lua.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet #!\n\t#!/usr/bin/env lua\n\t$1\nsnippet local\n\tlocal ${1:x} = ${2:1}\nsnippet fun\n\tfunction ${1:fname}(${2:...})\n\t\t${3:-- body}\n\tend\nsnippet for\n\tfor ${1:i}=${2:1},${3:10} do\n\t\t${4:print(i)}\n\tend\nsnippet forp\n\tfor ${1:i},${2:v} in pairs(${3:table_name}) do\n\t   ${4:-- body}\n\tend\nsnippet fori\n\tfor ${1:i},${2:v} in ipairs(${3:table_name}) do\n\t   ${4:-- body}\n\tend\n";

});

ace.define("ace/snippets/lua",["require","exports","module","ace/snippets/lua.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./lua.snippets");
exports.scope = "lua";

});                (function() {
                    ace.require(["ace/snippets/lua"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            