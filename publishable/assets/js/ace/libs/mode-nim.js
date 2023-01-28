ace.define("ace/mode/nim_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"], function(require, exports, module){"use strict";
var oop = require("../lib/oop");
var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;
var NimHighlightRules = function () {
    var keywordMapper = this.createKeywordMapper({
        "variable": "var|let|const",
        "keyword": "assert|parallel|spawn|export|include|from|template|mixin|bind|import|concept|raise|defer|try|finally|except|converter|proc|func|macro|method|and|or|not|xor|shl|shr|div|mod|in|notin|is|isnot|of|static|if|elif|else|case|of|discard|when|return|yield|block|break|while|echo|continue|asm|using|cast|addr|unsafeAddr|type|ref|ptr|do|declared|defined|definedInScope|compiles|sizeOf|is|shallowCopy|getAst|astToStr|spawn|procCall|for|iterator|as",
        "storage.type": "newSeq|int|int8|int16|int32|int64|uint|uint8|uint16|uint32|uint64|float|char|bool|string|set|pointer|float32|float64|enum|object|cstring|array|seq|openArray|varargs|UncheckedArray|tuple|set|distinct|void|auto|openarray|range",
        "support.function": "lock|ze|toU8|toU16|toU32|ord|low|len|high|add|pop|contains|card|incl|excl|dealloc|inc",
        "constant.language": "nil|true|false"
    }, "identifier");
    var hexNumber = "(?:0[xX][\\dA-Fa-f][\\dA-Fa-f_]*)";
    var decNumber = "(?:[0-9][\\d_]*)";
    var octNumber = "(?:0o[0-7][0-7_]*)";
    var binNumber = "(?:0[bB][01][01_]*)";
    var intNumber = "(?:" + hexNumber + "|" + decNumber + "|" + octNumber + "|" + binNumber + ")(?:'?[iIuU](?:8|16|32|64)|u)?\\b";
    var exponent = "(?:[eE][+-]?[\\d][\\d_]*)";
    var floatNumber = "(?:[\\d][\\d_]*(?:[.][\\d](?:[\\d_]*)" + exponent + "?)|" + exponent + ")";
    var floatNumberExt = "(?:" + hexNumber + "(?:'(?:(?:[fF](?:32|64)?)|[dD])))|(?:" + floatNumber + "|" + decNumber + "|" + octNumber + "|" + binNumber + ")(?:'(?:(?:[fF](?:32|64)?)|[dD]))";
    var stringEscape = "\\\\([abeprcnlftv\\\"']|x[0-9A-Fa-f]{2}|[0-2][0-9]{2}|u[0-9A-Fa-f]{8}|u[0-9A-Fa-f]{4})";
    var identifier = '[a-zA-Z][a-zA-Z0-9_]*';
    this.$rules = {
        "start": [{
                token: ["identifier", "keyword.operator", "support.function"],
                regex: "(" + identifier + ")([.]{1})(" + identifier + ")(?=\\()"
            }, {
                token: "paren.lparen",
                regex: "(\\{\\.)",
                next: [{
                        token: "paren.rparen",
                        regex: '(\\.\\}|\\})',
                        next: "start"
                    }, {
                        include: "methods"
                    }, {
                        token: "identifier",
                        regex: identifier
                    }, {
                        token: "punctuation",
                        regex: /[,]/
                    }, {
                        token: "keyword.operator",
                        regex: /[=:.]/
                    }, {
                        token: "paren.lparen",
                        regex: /[[(]/
                    }, {
                        token: "paren.rparen",
                        regex: /[\])]/
                    }, {
                        include: "math"
                    }, {
                        include: "strings"
                    }, {
                        defaultToken: "text"
                    }]
            }, {
                token: "comment.doc.start",
                regex: /##\[(?!])/,
                push: "docBlockComment"
            }, {
                token: "comment.start",
                regex: /#\[(?!])/,
                push: "blockComment"
            }, {
                token: "comment.doc",
                regex: '##.*$'
            }, {
                token: "comment",
                regex: '#.*$'
            }, {
                include: "strings"
            }, {
                token: "string",
                regex: "'(?:\\\\(?:[abercnlftv]|x[0-9A-Fa-f]{2}|[0-2][0-9]{2}|u[0-9A-Fa-f]{8}|u[0-9A-Fa-f]{4})|.{1})?'"
            }, {
                include: "methods"
            }, {
                token: keywordMapper,
                regex: "[a-zA-Z][a-zA-Z0-9_]*\\b"
            }, {
                token: ["keyword.operator", "text", "storage.type"],
                regex: "([:])(\\s+)(" + identifier + ")(?=$|\\)|\\[|,|\\s+=|;|\\s+\\{)"
            }, {
                token: "paren.lparen",
                regex: /\[\.|{\||\(\.|\[:|[[({`]/
            }, {
                token: "paren.rparen",
                regex: /\.\)|\|}|\.]|[\])}]/
            }, {
                token: "keyword.operator",
                regex: /[=+\-*\/<>@$~&%|!?^.:\\]/
            }, {
                token: "punctuation",
                regex: /[,;]/
            }, {
                include: "math"
            }],
        blockComment: [{
                regex: /#\[]/,
                token: "comment"
            }, {
                regex: /#\[(?!])/,
                token: "comment.start",
                push: "blockComment"
            }, {
                regex: /]#/,
                token: "comment.end",
                next: "pop"
            }, {
                defaultToken: "comment"
            }],
        docBlockComment: [{
                regex: /##\[]/,
                token: "comment.doc"
            }, {
                regex: /##\[(?!])/,
                token: "comment.doc.start",
                push: "docBlockComment"
            }, {
                regex: /]##/,
                token: "comment.doc.end",
                next: "pop"
            }, {
                defaultToken: "comment.doc"
            }],
        math: [{
                token: "constant.float",
                regex: floatNumberExt
            }, {
                token: "constant.float",
                regex: floatNumber
            }, {
                token: "constant.integer",
                regex: intNumber
            }],
        methods: [{
                token: "support.function",
                regex: "(\\w+)(?=\\()"
            }],
        strings: [{
                token: "string",
                regex: '(\\b' + identifier + ')?"""',
                push: [{
                        token: "string",
                        regex: '"""',
                        next: "pop"
                    }, {
                        defaultToken: "string"
                    }]
            }, {
                token: "string",
                regex: "\\b" + identifier + '"(?=.)',
                push: [{
                        token: "string",
                        regex: '"|$',
                        next: "pop"
                    }, {
                        defaultToken: "string"
                    }]
            }, {
                token: "string",
                regex: '"',
                push: [{
                        token: "string",
                        regex: '"|$',
                        next: "pop"
                    }, {
                        token: "constant.language.escape",
                        regex: stringEscape
                    }, {
                        defaultToken: "string"
                    }]
            }]
    };
    this.normalizeRules();
};
oop.inherits(NimHighlightRules, TextHighlightRules);
exports.NimHighlightRules = NimHighlightRules;

});

ace.define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"], function(require, exports, module){"use strict";
var oop = require("../../lib/oop");
var Range = require("../../range").Range;
var BaseFoldMode = require("./fold_mode").FoldMode;
var FoldMode = exports.FoldMode = function (commentRegex) {
    if (commentRegex) {
        this.foldingStartMarker = new RegExp(this.foldingStartMarker.source.replace(/\|[^|]*?$/, "|" + commentRegex.start));
        this.foldingStopMarker = new RegExp(this.foldingStopMarker.source.replace(/\|[^|]*?$/, "|" + commentRegex.end));
    }
};
oop.inherits(FoldMode, BaseFoldMode);
(function () {
    this.foldingStartMarker = /([\{\[\(])[^\}\]\)]*$|^\s*(\/\*)/;
    this.foldingStopMarker = /^[^\[\{\(]*([\}\]\)])|^[\s\*]*(\*\/)/;
    this.singleLineBlockCommentRe = /^\s*(\/\*).*\*\/\s*$/;
    this.tripleStarBlockCommentRe = /^\s*(\/\*\*\*).*\*\/\s*$/;
    this.startRegionRe = /^\s*(\/\*|\/\/)#?region\b/;
    this._getFoldWidgetBase = this.getFoldWidget;
    this.getFoldWidget = function (session, foldStyle, row) {
        var line = session.getLine(row);
        if (this.singleLineBlockCommentRe.test(line)) {
            if (!this.startRegionRe.test(line) && !this.tripleStarBlockCommentRe.test(line))
                return "";
        }
        var fw = this._getFoldWidgetBase(session, foldStyle, row);
        if (!fw && this.startRegionRe.test(line))
            return "start"; // lineCommentRegionStart
        return fw;
    };
    this.getFoldWidgetRange = function (session, foldStyle, row, forceMultiline) {
        var line = session.getLine(row);
        if (this.startRegionRe.test(line))
            return this.getCommentRegionBlock(session, line, row);
        var match = line.match(this.foldingStartMarker);
        if (match) {
            var i = match.index;
            if (match[1])
                return this.openingBracketBlock(session, match[1], row, i);
            var range = session.getCommentFoldRange(row, i + match[0].length, 1);
            if (range && !range.isMultiLine()) {
                if (forceMultiline) {
                    range = this.getSectionRange(session, row);
                }
                else if (foldStyle != "all")
                    range = null;
            }
            return range;
        }
        if (foldStyle === "markbegin")
            return;
        var match = line.match(this.foldingStopMarker);
        if (match) {
            var i = match.index + match[0].length;
            if (match[1])
                return this.closingBracketBlock(session, match[1], row, i);
            return session.getCommentFoldRange(row, i, -1);
        }
    };
    this.getSectionRange = function (session, row) {
        var line = session.getLine(row);
        var startIndent = line.search(/\S/);
        var startRow = row;
        var startColumn = line.length;
        row = row + 1;
        var endRow = row;
        var maxRow = session.getLength();
        while (++row < maxRow) {
            line = session.getLine(row);
            var indent = line.search(/\S/);
            if (indent === -1)
                continue;
            if (startIndent > indent)
                break;
            var subRange = this.getFoldWidgetRange(session, "all", row);
            if (subRange) {
                if (subRange.start.row <= startRow) {
                    break;
                }
                else if (subRange.isMultiLine()) {
                    row = subRange.end.row;
                }
                else if (startIndent == indent) {
                    break;
                }
            }
            endRow = row;
        }
        return new Range(startRow, startColumn, endRow, session.getLine(endRow).length);
    };
    this.getCommentRegionBlock = function (session, line, row) {
        var startColumn = line.search(/\s*$/);
        var maxRow = session.getLength();
        var startRow = row;
        var re = /^\s*(?:\/\*|\/\/|--)#?(end)?region\b/;
        var depth = 1;
        while (++row < maxRow) {
            line = session.getLine(row);
            var m = re.exec(line);
            if (!m)
                continue;
            if (m[1])
                depth--;
            else
                depth++;
            if (!depth)
                break;
        }
        var endRow = row;
        if (endRow > startRow) {
            return new Range(startRow, startColumn, endRow, line.length);
        }
    };
}).call(FoldMode.prototype);

});

ace.define("ace/mode/nim",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/nim_highlight_rules","ace/mode/folding/cstyle"], function(require, exports, module){"use strict";
var oop = require("../lib/oop");
var TextMode = require("./text").Mode;
var NimHighlightRules = require("./nim_highlight_rules").NimHighlightRules;
var CStyleFoldMode = require("./folding/cstyle").FoldMode;
var Mode = function () {
    TextMode.call(this);
    this.HighlightRules = NimHighlightRules;
    this.foldingRules = new CStyleFoldMode();
    this.$behaviour = this.$defaultBehaviour;
};
oop.inherits(Mode, TextMode);
(function () {
    this.lineCommentStart = "#";
    this.blockComment = { start: "#[", end: "]#", nestable: true };
    this.$id = "ace/mode/nim";
}).call(Mode.prototype);
exports.Mode = Mode;

});                (function() {
                    ace.require(["ace/mode/nim"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            