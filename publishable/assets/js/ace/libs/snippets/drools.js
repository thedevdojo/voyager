ace.define("ace/snippets/drools.snippets",["require","exports","module"], function(require, exports, module){module.exports = "\nsnippet rule\n\trule \"${1?:rule_name}\"\n\twhen\n\t\t${2:// when...} \n\tthen\n\t\t${3:// then...}\n\tend\n\nsnippet query\n\tquery ${1?:query_name}\n\t\t${2:// find} \n\tend\n\t\nsnippet declare\n\tdeclare ${1?:type_name}\n\t\t${2:// attributes} \n\tend\n\n";

});

ace.define("ace/snippets/drools",["require","exports","module","ace/snippets/drools.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./drools.snippets");
exports.scope = "drools";

});                (function() {
                    ace.require(["ace/snippets/drools"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            