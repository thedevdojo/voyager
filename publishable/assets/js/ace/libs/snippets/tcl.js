ace.define("ace/snippets/tcl.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# #!/usr/bin/env tclsh\nsnippet #!\n\t#!/usr/bin/env tclsh\n\t\n# Process\nsnippet pro\n\tproc ${1:function_name} {${2:args}} {\n\t\t${3:#body ...}\n\t}\n#xif\nsnippet xif\n\t${1:expr}? ${2:true} : ${3:false}\n# Conditional\nsnippet if\n\tif {${1}} {\n\t\t${2:# body...}\n\t}\n# Conditional if..else\nsnippet ife\n\tif {${1}} {\n\t\t${2:# body...}\n\t} else {\n\t\t${3:# else...}\n\t}\n# Conditional if..elsif..else\nsnippet ifee\n\tif {${1}} {\n\t\t${2:# body...}\n\t} elseif {${3}} {\n\t\t${4:# elsif...}\n\t} else {\n\t\t${5:# else...}\n\t}\n# If catch then\nsnippet ifc\n\tif { [catch {${1:#do something...}} ${2:err}] } {\n\t\t${3:# handle failure...}\n\t}\n# Catch\nsnippet catch\n\tcatch {${1}} ${2:err} ${3:options}\n# While Loop\nsnippet wh\n\twhile {${1}} {\n\t\t${2:# body...}\n\t}\n# For Loop\nsnippet for\n\tfor {set ${2:var} 0} {$$2 < ${1:count}} {${3:incr} $2} {\n\t\t${4:# body...}\n\t}\n# Foreach Loop\nsnippet fore\n\tforeach ${1:x} {${2:#list}} {\n\t\t${3:# body...}\n\t}\n# after ms script...\nsnippet af\n\tafter ${1:ms} ${2:#do something}\n# after cancel id\nsnippet afc\n\tafter cancel ${1:id or script}\n# after idle\nsnippet afi\n\tafter idle ${1:script}\n# after info id\nsnippet afin\n\tafter info ${1:id}\n# Expr\nsnippet exp\n\texpr {${1:#expression here}}\n# Switch\nsnippet sw\n\tswitch ${1:var} {\n\t\t${3:pattern 1} {\n\t\t\t${4:#do something}\n\t\t}\n\t\tdefault {\n\t\t\t${2:#do something}\n\t\t}\n\t}\n# Case\nsnippet ca\n\t${1:pattern} {\n\t\t${2:#do something}\n\t}${3}\n# Namespace eval\nsnippet ns\n\tnamespace eval ${1:path} {${2:#script...}}\n# Namespace current\nsnippet nsc\n\tnamespace current\n";

});

ace.define("ace/snippets/tcl",["require","exports","module","ace/snippets/tcl.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./tcl.snippets");
exports.scope = "tcl";

});                (function() {
                    ace.require(["ace/snippets/tcl"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            