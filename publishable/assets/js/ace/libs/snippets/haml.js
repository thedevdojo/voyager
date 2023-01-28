ace.define("ace/snippets/haml.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet t\n\t%table\n\t\t%tr\n\t\t\t%th\n\t\t\t\t${1:headers}\n\t\t%tr\n\t\t\t%td\n\t\t\t\t${2:headers}\nsnippet ul\n\t%ul\n\t\t%li\n\t\t\t${1:item}\n\t\t%li\nsnippet =rp\n\t= render :partial => '${1:partial}'\nsnippet =rpl\n\t= render :partial => '${1:partial}', :locals => {}\nsnippet =rpc\n\t= render :partial => '${1:partial}', :collection => @$1\n\n";

});

ace.define("ace/snippets/haml",["require","exports","module","ace/snippets/haml.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./haml.snippets");
exports.scope = "haml";

});                (function() {
                    ace.require(["ace/snippets/haml"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            