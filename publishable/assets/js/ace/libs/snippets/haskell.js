ace.define("ace/snippets/haskell.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet lang\n\t{-# LANGUAGE ${1:OverloadedStrings} #-}\nsnippet info\n\t-- |\n\t-- Module      :  ${1:Module.Namespace}\n\t-- Copyright   :  ${2:Author} ${3:2011-2012}\n\t-- License     :  ${4:BSD3}\n\t--\n\t-- Maintainer  :  ${5:email@something.com}\n\t-- Stability   :  ${6:experimental}\n\t-- Portability :  ${7:unknown}\n\t--\n\t-- ${8:Description}\n\t--\nsnippet import\n\timport           ${1:Data.Text}\nsnippet import2\n\timport           ${1:Data.Text} (${2:head})\nsnippet importq\n\timport qualified ${1:Data.Text} as ${2:T}\nsnippet inst\n\tinstance ${1:Monoid} ${2:Type} where\n\t\t${3}\nsnippet type\n\ttype ${1:Type} = ${2:Type}\nsnippet data\n\tdata ${1:Type} = ${2:$1} ${3:Int}\nsnippet newtype\n\tnewtype ${1:Type} = ${2:$1} ${3:Int}\nsnippet class\n\tclass ${1:Class} a where\n\t\t${2}\nsnippet module\n\tmodule `substitute(substitute(expand('%:r'), '[/\\\\]','.','g'),'^\\%(\\l*\\.\\)\\?','','')` (\n\t)\twhere\n\t`expand('%') =~ 'Main' ? \"\\n\\nmain = do\\n  print \\\"hello world\\\"\" : \"\"`\n\nsnippet const\n\t${1:name} :: ${2:a}\n\t$1 = ${3:undefined}\nsnippet fn\n\t${1:fn} :: ${2:a} -> ${3:a}\n\t$1 ${4} = ${5:undefined}\nsnippet fn2\n\t${1:fn} :: ${2:a} -> ${3:a} -> ${4:a}\n\t$1 ${5} = ${6:undefined}\nsnippet ap\n\t${1:map} ${2:fn} ${3:list}\nsnippet do\n\tdo\n\t\t\nsnippet \u03BB\n\t\\${1:x} -> ${2}\nsnippet \\\n\t\\${1:x} -> ${2}\nsnippet <-\n\t${1:a} <- ${2:m a}\nsnippet \u2190\n\t${1:a} <- ${2:m a}\nsnippet ->\n\t${1:m a} -> ${2:a}\nsnippet \u2192\n\t${1:m a} -> ${2:a}\nsnippet tup\n\t(${1:a}, ${2:b})\nsnippet tup2\n\t(${1:a}, ${2:b}, ${3:c})\nsnippet tup3\n\t(${1:a}, ${2:b}, ${3:c}, ${4:d})\nsnippet rec\n\t${1:Record} { ${2:recFieldA} = ${3:undefined}\n\t\t\t\t, ${4:recFieldB} = ${5:undefined}\n\t\t\t\t}\nsnippet case\n\tcase ${1:something} of\n\t\t${2} -> ${3}\nsnippet let\n\tlet ${1} = ${2}\n\tin ${3}\nsnippet where\n\twhere\n\t\t${1:fn} = ${2:undefined}\n";

});

ace.define("ace/snippets/haskell",["require","exports","module","ace/snippets/haskell.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./haskell.snippets");
exports.scope = "haskell";

});                (function() {
                    ace.require(["ace/snippets/haskell"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            