ace.define("ace/snippets/coffee.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# Closure loop\nsnippet forindo\n\tfor ${1:name} in ${2:array}\n\t\tdo ($1) ->\n\t\t\t${3:// body}\n# Array comprehension\nsnippet fora\n\tfor ${1:name} in ${2:array}\n\t\t${3:// body...}\n# Object comprehension\nsnippet foro\n\tfor ${1:key}, ${2:value} of ${3:object}\n\t\t${4:// body...}\n# Range comprehension (inclusive)\nsnippet forr\n\tfor ${1:name} in [${2:start}..${3:finish}]\n\t\t${4:// body...}\nsnippet forrb\n\tfor ${1:name} in [${2:start}..${3:finish}] by ${4:step}\n\t\t${5:// body...}\n# Range comprehension (exclusive)\nsnippet forrex\n\tfor ${1:name} in [${2:start}...${3:finish}]\n\t\t${4:// body...}\nsnippet forrexb\n\tfor ${1:name} in [${2:start}...${3:finish}] by ${4:step}\n\t\t${5:// body...}\n# Function\nsnippet fun\n\t(${1:args}) ->\n\t\t${2:// body...}\n# Function (bound)\nsnippet bfun\n\t(${1:args}) =>\n\t\t${2:// body...}\n# Class\nsnippet cla class ..\n\tclass ${1:`substitute(Filename(), '\\(_\\|^\\)\\(.\\)', '\\u\\2', 'g')`}\n\t\t${2}\nsnippet cla class .. constructor: ..\n\tclass ${1:`substitute(Filename(), '\\(_\\|^\\)\\(.\\)', '\\u\\2', 'g')`}\n\t\tconstructor: (${2:args}) ->\n\t\t\t${3}\n\n\t\t${4}\nsnippet cla class .. extends ..\n\tclass ${1:`substitute(Filename(), '\\(_\\|^\\)\\(.\\)', '\\u\\2', 'g')`} extends ${2:ParentClass}\n\t\t${3}\nsnippet cla class .. extends .. constructor: ..\n\tclass ${1:`substitute(Filename(), '\\(_\\|^\\)\\(.\\)', '\\u\\2', 'g')`} extends ${2:ParentClass}\n\t\tconstructor: (${3:args}) ->\n\t\t\t${4}\n\n\t\t${5}\n# If\nsnippet if\n\tif ${1:condition}\n\t\t${2:// body...}\n# If __ Else\nsnippet ife\n\tif ${1:condition}\n\t\t${2:// body...}\n\telse\n\t\t${3:// body...}\n# Else if\nsnippet elif\n\telse if ${1:condition}\n\t\t${2:// body...}\n# Ternary If\nsnippet ifte\n\tif ${1:condition} then ${2:value} else ${3:other}\n# Unless\nsnippet unl\n\t${1:action} unless ${2:condition}\n# Switch\nsnippet swi\n\tswitch ${1:object}\n\t\twhen ${2:value}\n\t\t\t${3:// body...}\n\n# Log\nsnippet log\n\tconsole.log ${1}\n# Try __ Catch\nsnippet try\n\ttry\n\t\t${1}\n\tcatch ${2:error}\n\t\t${3}\n# Require\nsnippet req\n\t${2:$1} = require '${1:sys}'${3}\n# Export\nsnippet exp\n\t${1:root} = exports ? this\n";

});

ace.define("ace/snippets/coffee",["require","exports","module","ace/snippets/coffee.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./coffee.snippets");
exports.scope = "coffee";

});                (function() {
                    ace.require(["ace/snippets/coffee"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            