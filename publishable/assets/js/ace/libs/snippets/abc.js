ace.define("ace/snippets/abc.snippets",["require","exports","module"], function(require, exports, module){module.exports = "\nsnippet zupfnoter.print\n\t%%%%hn.print {\"startpos\": ${1:pos_y}, \"t\":\"${2:title}\", \"v\":[${3:voices}], \"s\":[[${4:syncvoices}1,2]], \"f\":[${5:flowlines}],  \"sf\":[${6:subflowlines}], \"j\":[${7:jumplines}]}\n\nsnippet zupfnoter.note\n\t%%%%hn.note {\"pos\": [${1:pos_x},${2:pos_y}], \"text\": \"${3:text}\", \"style\": \"${4:style}\"}\n\nsnippet zupfnoter.annotation\n\t%%%%hn.annotation {\"id\": \"${1:id}\", \"pos\": [${2:pos}], \"text\": \"${3:text}\"}\n\nsnippet zupfnoter.lyrics\n\t%%%%hn.lyrics {\"pos\": [${1:x_pos},${2:y_pos}]}\n\nsnippet zupfnoter.legend\n\t%%%%hn.legend {\"pos\": [${1:x_pos},${2:y_pos}]}\n\n\n\nsnippet zupfnoter.target\n\t\"^:${1:target}\"\n\nsnippet zupfnoter.goto\n\t\"^@${1:target}@${2:distance}\"\n\nsnippet zupfnoter.annotationref\n\t\"^#${1:target}\"\n\nsnippet zupfnoter.annotation\n\t\"^!${1:text}@${2:x_offset},${3:y_offset}\"\n\n\n";

});

ace.define("ace/snippets/abc",["require","exports","module","ace/snippets/abc.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./abc.snippets");
exports.scope = "abc";

});                (function() {
                    ace.require(["ace/snippets/abc"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            