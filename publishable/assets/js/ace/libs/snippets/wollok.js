ace.define("ace/snippets/wollok.snippets",["require","exports","module"], function(require, exports, module){module.exports = "##\n## Basic Java packages and import\nsnippet im\n\timport\nsnippet w.l\n\twollok.lang\nsnippet w.i\n\twollok.lib\n\n## Class and object\nsnippet cl\n\tclass ${1:`Filename(\"\", \"untitled\")`} ${2}\nsnippet obj\n\tobject ${1:`Filename(\"\", \"untitled\")`} ${2:inherits Parent}${3}\nsnippet te\n\ttest ${1:`Filename(\"\", \"untitled\")`}\n\n##\n## Enhancements\nsnippet inh\n\tinherits\n\n##\n## Comments\nsnippet /*\n\t/*\n\t * ${1}\n\t */\n\n##\n## Control Statements\nsnippet el\n\telse\nsnippet if\n\tif (${1}) ${2}\n\n##\n## Create a Method\nsnippet m\n\tmethod ${1:method}(${2}) ${5}\n\n##  \n## Tests\nsnippet as\n\tassert.equals(${1:expected}, ${2:actual})\n\n##\n## Exceptions\nsnippet ca\n\tcatch ${1:e} : (${2:Exception} ) ${3}\nsnippet thr\n\tthrow\nsnippet try\n\ttry {\n\t\t${3}\n\t} catch ${1:e} : ${2:Exception} {\n\t}\n\n##\n## Javadocs\nsnippet /**\n\t/**\n\t * ${1}\n\t */\n\n##\n## Print Methods\nsnippet print\n\tconsole.println(\"${1:Message}\")\n\n##\n## Setter and Getter Methods\nsnippet set\n\tmethod set${1:}(${2:}) {\n\t\t$1 = $2\n\t}\nsnippet get\n\tmethod get${1:}() {\n\t\treturn ${1:};\n\t}\n\n##\n## Terminate Methods or Loops\nsnippet re\n\treturn";

});

ace.define("ace/snippets/wollok",["require","exports","module","ace/snippets/wollok.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./wollok.snippets");
exports.scope = "wollok";

});                (function() {
                    ace.require(["ace/snippets/wollok"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            