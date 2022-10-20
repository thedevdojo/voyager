ace.define("ace/snippets/graphqlschema.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# Type Snippet\ntrigger type\nsnippet type\n\ttype ${1:type_name} {\n\t\t${2:type_siblings}\n\t}\n\n# Input Snippet\ntrigger input\nsnippet input\n\tinput ${1:input_name} {\n\t\t${2:input_siblings}\n\t}\n\n# Interface Snippet\ntrigger interface\nsnippet interface\n\tinterface ${1:interface_name} {\n\t\t${2:interface_siblings}\n\t}\n\n# Interface Snippet\ntrigger union\nsnippet union\n\tunion ${1:union_name} = ${2:type} | ${3: type}\n\n# Enum Snippet\ntrigger enum\nsnippet enum\n\tenum ${1:enum_name} {\n\t\t${2:enum_siblings}\n\t}\n";

});

ace.define("ace/snippets/graphqlschema",["require","exports","module","ace/snippets/graphqlschema.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./graphqlschema.snippets");
exports.scope = "graphqlschema";

});                (function() {
                    ace.require(["ace/snippets/graphqlschema"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            