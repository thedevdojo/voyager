ace.define("ace/mode/latex_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"], function(require, exports, module){"use strict";
var oop = require("../lib/oop");
var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;
var LatexHighlightRules = function () {
    this.$rules = {
        "start": [{
                token: "comment",
                regex: "%.*$"
            }, {
                token: ["keyword", "lparen", "variable.parameter", "rparen", "lparen", "storage.type", "rparen"],
                regex: "(\\\\(?:documentclass|usepackage|input))(?:(\\[)([^\\]]*)(\\]))?({)([^}]*)(})"
            }, {
                token: ["keyword", "lparen", "variable.parameter", "rparen"],
                regex: "(\\\\(?:label|v?ref|cite(?:[^{]*)))(?:({)([^}]*)(}))?"
            }, {
                token: ["storage.type", "lparen", "variable.parameter", "rparen"],
                regex: "(\\\\begin)({)(verbatim)(})",
                next: "verbatim"
            }, {
                token: ["storage.type", "lparen", "variable.parameter", "rparen"],
                regex: "(\\\\begin)({)(lstlisting)(})",
                next: "lstlisting"
            }, {
                token: ["storage.type", "lparen", "variable.parameter", "rparen"],
                regex: "(\\\\(?:begin|end))({)([\\w*]*)(})"
            }, {
                token: "storage.type",
                regex: /\\verb\b\*?/,
                next: [{
                        token: ["keyword.operator", "string", "keyword.operator"],
                        regex: "(.)(.*?)(\\1|$)|",
                        next: "start"
                    }]
            }, {
                token: "storage.type",
                regex: "\\\\[a-zA-Z]+"
            }, {
                token: "lparen",
                regex: "[[({]"
            }, {
                token: "rparen",
                regex: "[\\])}]"
            }, {
                token: "constant.character.escape",
                regex: "\\\\[^a-zA-Z]?"
            }, {
                token: "string",
                regex: "\\${1,2}",
                next: "equation"
            }],
        "equation": [{
                token: "comment",
                regex: "%.*$"
            }, {
                token: "string",
                regex: "\\${1,2}",
                next: "start"
            }, {
                token: "constant.character.escape",
                regex: "\\\\(?:[^a-zA-Z]|[a-zA-Z]+)"
            }, {
                token: "error",
                regex: "^\\s*$",
                next: "start"
            }, {
                defaultToken: "string"
            }],
        "verbatim": [{
                token: ["storage.type", "lparen", "variable.parameter", "rparen"],
                regex: "(\\\\end)({)(verbatim)(})",
                next: "start"
            }, {
                defaultToken: "text"
            }],
        "lstlisting": [{
                token: ["storage.type", "lparen", "variable.parameter", "rparen"],
                regex: "(\\\\end)({)(lstlisting)(})",
                next: "start"
            }, {
                defaultToken: "text"
            }]
    };
    this.normalizeRules();
};
oop.inherits(LatexHighlightRules, TextHighlightRules);
exports.LatexHighlightRules = LatexHighlightRules;

});

ace.define("ace/mode/rdoc_highlight_rules",["require","exports","module","ace/lib/oop","ace/lib/lang","ace/mode/text_highlight_rules","ace/mode/latex_highlight_rules"], function(require, exports, module){/*
 * rdoc_highlight_rules.js
 *
 * Copyright (C) 2009-11 by RStudio, Inc.
 *
 * Distributed under the BSD license:
 *
 * Copyright (c) 2010, Ajax.org B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Ajax.org B.V. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL AJAX.ORG B.V. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 *
 */
"use strict";
var oop = require("../lib/oop");
var lang = require("../lib/lang");
var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;
var LaTeXHighlightRules = require("./latex_highlight_rules");
var RDocHighlightRules = function () {
    this.$rules = {
        "start": [
            {
                token: "comment",
                regex: "%.*$"
            }, {
                token: "text",
                regex: "\\\\[$&%#\\{\\}]"
            }, {
                token: "keyword",
                regex: "\\\\(?:name|alias|method|S3method|S4method|item|code|preformatted|kbd|pkg|var|env|option|command|author|email|url|source|cite|acronym|href|code|preformatted|link|eqn|deqn|keyword|usage|examples|dontrun|dontshow|figure|if|ifelse|Sexpr|RdOpts|inputencoding|usepackage)\\b",
                next: "nospell"
            }, {
                token: "keyword",
                regex: "\\\\(?:[a-zA-Z0-9]+|[^a-zA-Z0-9])"
            }, {
                token: "paren.keyword.operator",
                regex: "[[({]"
            }, {
                token: "paren.keyword.operator",
                regex: "[\\])}]"
            }, {
                token: "text",
                regex: "\\s+"
            }
        ],
        "nospell": [
            {
                token: "comment",
                regex: "%.*$",
                next: "start"
            }, {
                token: "nospell.text",
                regex: "\\\\[$&%#\\{\\}]"
            }, {
                token: "keyword",
                regex: "\\\\(?:name|alias|method|S3method|S4method|item|code|preformatted|kbd|pkg|var|env|option|command|author|email|url|source|cite|acronym|href|code|preformatted|link|eqn|deqn|keyword|usage|examples|dontrun|dontshow|figure|if|ifelse|Sexpr|RdOpts|inputencoding|usepackage)\\b"
            }, {
                token: "keyword",
                regex: "\\\\(?:[a-zA-Z0-9]+|[^a-zA-Z0-9])",
                next: "start"
            }, {
                token: "paren.keyword.operator",
                regex: "[[({]"
            }, {
                token: "paren.keyword.operator",
                regex: "[\\])]"
            }, {
                token: "paren.keyword.operator",
                regex: "}",
                next: "start"
            }, {
                token: "nospell.text",
                regex: "\\s+"
            }, {
                token: "nospell.text",
                regex: "\\w+"
            }
        ]
    };
};
oop.inherits(RDocHighlightRules, TextHighlightRules);
exports.RDocHighlightRules = RDocHighlightRules;

});

ace.define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"], function(require, exports, module){"use strict";
var Range = require("../range").Range;
var MatchingBraceOutdent = function () { };
(function () {
    this.checkOutdent = function (line, input) {
        if (!/^\s+$/.test(line))
            return false;
        return /^\s*\}/.test(input);
    };
    this.autoOutdent = function (doc, row) {
        var line = doc.getLine(row);
        var match = line.match(/^(\s*\})/);
        if (!match)
            return 0;
        var column = match[1].length;
        var openBracePos = doc.findMatchingBracket({ row: row, column: column });
        if (!openBracePos || openBracePos.row == row)
            return 0;
        var indent = this.$getIndent(doc.getLine(openBracePos.row));
        doc.replace(new Range(row, 0, row, column - 1), indent);
    };
    this.$getIndent = function (line) {
        return line.match(/^\s*/)[0];
    };
}).call(MatchingBraceOutdent.prototype);
exports.MatchingBraceOutdent = MatchingBraceOutdent;

});

ace.define("ace/mode/rdoc",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/rdoc_highlight_rules","ace/mode/matching_brace_outdent"], function(require, exports, module){/*
 * rdoc.js
 *
 * Copyright (C) 2009-11 by RStudio, Inc.
 *
 * The Initial Developer of the Original Code is
 * Ajax.org B.V.
 * Portions created by the Initial Developer are Copyright (C) 2010
 * the Initial Developer. All Rights Reserved.
 *
 * Distributed under the BSD license:
 *
 * Copyright (c) 2010, Ajax.org B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Ajax.org B.V. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL AJAX.ORG B.V. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 *
 */
"use strict";
var oop = require("../lib/oop");
var TextMode = require("./text").Mode;
var RDocHighlightRules = require("./rdoc_highlight_rules").RDocHighlightRules;
var MatchingBraceOutdent = require("./matching_brace_outdent").MatchingBraceOutdent;
var Mode = function (suppressHighlighting) {
    this.HighlightRules = RDocHighlightRules;
    this.$outdent = new MatchingBraceOutdent();
    this.$behaviour = this.$defaultBehaviour;
};
oop.inherits(Mode, TextMode);
(function () {
    this.getNextLineIndent = function (state, line, tab) {
        return this.$getIndent(line);
    };
    this.$id = "ace/mode/rdoc";
}).call(Mode.prototype);
exports.Mode = Mode;

});                (function() {
                    ace.require(["ace/mode/rdoc"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            