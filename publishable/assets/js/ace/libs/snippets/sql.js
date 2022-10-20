ace.define("ace/snippets/sql.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet tbl\n\tcreate table ${1:table} (\n\t\t${2:columns}\n\t);\nsnippet col\n\t${1:name}\t${2:type}\t${3:default ''}\t${4:not null}\nsnippet ccol\n\t${1:name}\tvarchar2(${2:size})\t${3:default ''}\t${4:not null}\nsnippet ncol\n\t${1:name}\tnumber\t${3:default 0}\t${4:not null}\nsnippet dcol\n\t${1:name}\tdate\t${3:default sysdate}\t${4:not null}\nsnippet ind\n\tcreate index ${3:$1_$2} on ${1:table}(${2:column});\nsnippet uind\n\tcreate unique index ${1:name} on ${2:table}(${3:column});\nsnippet tblcom\n\tcomment on table ${1:table} is '${2:comment}';\nsnippet colcom\n\tcomment on column ${1:table}.${2:column} is '${3:comment}';\nsnippet addcol\n\talter table ${1:table} add (${2:column} ${3:type});\nsnippet seq\n\tcreate sequence ${1:name} start with ${2:1} increment by ${3:1} minvalue ${4:1};\nsnippet s*\n\tselect * from ${1:table}\n";

});

ace.define("ace/snippets/sql",["require","exports","module","ace/snippets/sql.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./sql.snippets");
exports.scope = "sql";

});                (function() {
                    ace.require(["ace/snippets/sql"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            