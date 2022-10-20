ace.define("ace/mode/kotlin_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"], function(require, exports, module){"use strict";
var oop = require("../lib/oop");
var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;
var KotlinHighlightRules = function () {
    var keywordMapper = this.$keywords = this.createKeywordMapper({
        "storage.modifier.kotlin": "var|val|public|private|protected|abstract|final|enum|open|attribute|"
            + "annotation|override|inline|var|val|vararg|lazy|in|out|internal|data|tailrec|operator|infix|const|"
            + "yield|typealias|typeof|sealed|inner|value|lateinit|external|suspend|noinline|crossinline|reified|"
            + "expect|actual",
        "keyword": "companion|class|object|interface|namespace|type|fun|constructor|if|else|while|for|do|return|when|"
            + "where|break|continue|try|catch|finally|throw|in|is|as|assert|constructor",
        "constant.language.kotlin": "true|false|null|this|super",
        "entity.name.function.kotlin": "get|set"
    }, "identifier");
    this.$rules = {
        start: [{
                include: "#comments"
            }, {
                token: [
                    "text",
                    "keyword.other.kotlin",
                    "text",
                    "entity.name.package.kotlin",
                    "text"
                ],
                regex: /^(\s*)(package)\b(?:(\s*)([^ ;$]+)(\s*))?/
            }, {
                token: "comment",
                regex: /^\s*#!.*$/
            }, {
                include: "#imports"
            }, {
                include: "#expressions"
            }, {
                token: "string",
                regex: /@[a-zA-Z][a-zA-Z:]*\b/
            }, {
                token: ["keyword.other.kotlin", "text", "entity.name.variable.kotlin"],
                regex: /\b(var|val)(\s+)([a-zA-Z_][\w]*)\b/
            }, {
                token: ["keyword.other.kotlin", "text", "entity.name.variable.kotlin", "paren.lparen"],
                regex: /(fun)(\s+)(\w+)(\()/,
                push: [{
                        token: ["variable.parameter.function.kotlin", "text", "keyword.operator"],
                        regex: /(\w+)(\s*)(:)/
                    }, {
                        token: "paren.rparen",
                        regex: /\)/,
                        next: "pop"
                    }, {
                        include: "#comments"
                    }, {
                        include: "#types"
                    }, {
                        include: "#expressions"
                    }]
            }, {
                token: ["text", "keyword", "text", "identifier"],
                regex: /^(\s*)(class)(\s*)([a-zA-Z]+)/,
                next: "#classes"
            }, {
                token: ["identifier", "punctuaction"],
                regex: /([a-zA-Z_][\w]*)(<)/,
                push: [{
                        include: "#generics"
                    }, {
                        include: "#defaultTypes"
                    }, {
                        token: "punctuation",
                        regex: />/,
                        next: "pop"
                    }]
            }, {
                token: keywordMapper,
                regex: /[a-zA-Z_][\w]*\b/
            }, {
                token: "paren.lparen",
                regex: /[{(\[]/
            }, {
                token: "paren.rparen",
                regex: /[})\]]/
            }],
        "#comments": [{
                token: "comment",
                regex: /\/\*/,
                push: [{
                        token: "comment",
                        regex: /\*\//,
                        next: "pop"
                    }, {
                        defaultToken: "comment"
                    }]
            }, {
                token: [
                    "text",
                    "comment"
                ],
                regex: /(\s*)(\/\/.*$)/
            }],
        "#constants": [{
                token: "constant.numeric.kotlin",
                regex: /\b(?:0(?:x|X)[0-9a-fA-F]*|(?:[0-9]+\.?[0-9]*|\.[0-9]+)(?:(?:e|E)(?:\+|-)?[0-9]+)?)(?:[LlFfUuDd]|UL|ul)?\b/
            }, {
                token: "constant.other.kotlin",
                regex: /\b[A-Z][A-Z0-9_]+\b/
            }],
        "#expressions": [{
                include: "#strings"
            }, {
                include: "#constants"
            }, {
                include: "#keywords"
            }],
        "#imports": [{
                token: [
                    "text",
                    "keyword.other.kotlin",
                    "text",
                    "keyword.other.kotlin"
                ],
                regex: /^(\s*)(import)(\s+[^ $]+\s+)((?:as)?)/
            }],
        "#generics": [{
                token: "punctuation",
                regex: /</,
                push: [{
                        token: "punctuation",
                        regex: />/,
                        next: "pop"
                    }, {
                        token: "storage.type.generic.kotlin",
                        regex: /\w+/
                    }, {
                        token: "keyword.operator",
                        regex: /:/
                    }, {
                        token: "punctuation",
                        regex: /,/
                    }, {
                        include: "#generics"
                    }]
            }],
        "#classes": [{
                include: "#generics"
            }, {
                token: "keyword",
                regex: /public|private|constructor/
            }, {
                token: "string",
                regex: /@[a-zA-Z][a-zA-Z:]*\b/
            }, {
                token: "text",
                regex: /(?=$|\(|{)/,
                next: "start"
            }],
        "#keywords": [{
                token: "keyword.operator.kotlin",
                regex: /==|!=|===|!==|<=|>=|<|>|=>|->|::|\?:/
            }, {
                token: "keyword.operator.assignment.kotlin",
                regex: /=/
            }, {
                token: "keyword.operator.declaration.kotlin",
                regex: /:/,
                push: [{
                        token: "text",
                        regex: /(?=$|{|=|,)/,
                        next: "pop"
                    }, {
                        include: "#types"
                    }]
            }, {
                token: "keyword.operator.dot.kotlin",
                regex: /\./
            }, {
                token: "keyword.operator.increment-decrement.kotlin",
                regex: /\-\-|\+\+/
            }, {
                token: "keyword.operator.arithmetic.kotlin",
                regex: /\-|\+|\*|\/|%/
            }, {
                token: "keyword.operator.arithmetic.assign.kotlin",
                regex: /\+=|\-=|\*=|\/=/
            }, {
                token: "keyword.operator.logical.kotlin",
                regex: /!|&&|\|\|/
            }, {
                token: "keyword.operator.range.kotlin",
                regex: /\.\./
            }, {
                token: "punctuation.kotlin",
                regex: /[;,]/
            }],
        "#types": [{
                include: "#defaultTypes"
            }, {
                token: "paren.lparen",
                regex: /\(/,
                push: [{
                        token: "paren.rparen",
                        regex: /\)/,
                        next: "pop"
                    }, {
                        include: "#defaultTypes"
                    }, {
                        token: "punctuation",
                        regex: /,/
                    }]
            }, {
                include: "#generics"
            }, {
                token: "keyword.operator.declaration.kotlin",
                regex: /->/
            }, {
                token: "paren.rparen",
                regex: /\)/
            }, {
                token: "keyword.operator.declaration.kotlin",
                regex: /:/,
                push: [{
                        token: "text",
                        regex: /(?=$|{|=|,)/,
                        next: "pop"
                    }, {
                        include: "#types"
                    }]
            }],
        "#defaultTypes": [{
                token: "storage.type.buildin.kotlin",
                regex: /\b(Any|Unit|String|Int|Boolean|Char|Long|Double|Float|Short|Byte|dynamic|IntArray|BooleanArray|CharArray|LongArray|DoubleArray|FloatArray|ShortArray|ByteArray|Array|List|Map|Nothing|Enum|Throwable|Comparable)\b/
            }],
        "#strings": [{
                token: "string",
                regex: /"""/,
                push: [{
                        token: "string",
                        regex: /"""/,
                        next: "pop"
                    }, {
                        token: "variable.parameter.template.kotlin",
                        regex: /\$\w+|\${[^}]+}/
                    }, {
                        token: "constant.character.escape.kotlin",
                        regex: /\\./
                    }, {
                        defaultToken: "string"
                    }]
            }, {
                token: "string",
                regex: /"/,
                push: [{
                        token: "string",
                        regex: /"/,
                        next: "pop"
                    }, {
                        token: "variable.parameter.template.kotlin",
                        regex: /\$\w+|\$\{[^\}]+\}/
                    }, {
                        token: "constant.character.escape.kotlin",
                        regex: /\\./
                    }, {
                        defaultToken: "string"
                    }]
            }, {
                token: "string",
                regex: /'/,
                push: [{
                        token: "string",
                        regex: /'/,
                        next: "pop"
                    }, {
                        token: "constant.character.escape.kotlin",
                        regex: /\\./
                    }, {
                        defaultToken: "string"
                    }]
            }, {
                token: "string",
                regex: /`/,
                push: [{
                        token: "string",
                        regex: /`/,
                        next: "pop"
                    }, {
                        defaultToken: "string"
                    }]
            }]
    };
    this.normalizeRules();
};
KotlinHighlightRules.metaData = {
    fileTypes: ["kt", "kts"],
    name: "Kotlin",
    scopeName: "source.Kotlin"
};
oop.inherits(KotlinHighlightRules, TextHighlightRules);
exports.KotlinHighlightRules = KotlinHighlightRules;

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

ace.define("ace/mode/kotlin",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/kotlin_highlight_rules","ace/mode/behaviour/cstyle","ace/mode/folding/cstyle"], function(require, exports, module){/*
  THIS FILE WAS AUTOGENERATED BY mode.tmpl.js
*/
"use strict";
var oop = require("../lib/oop");
var TextMode = require("./text").Mode;
var KotlinHighlightRules = require("./kotlin_highlight_rules").KotlinHighlightRules;
var CstyleBehaviour = require("./behaviour/cstyle").CstyleBehaviour;
var FoldMode = require("./folding/cstyle").FoldMode;
var Mode = function () {
    this.HighlightRules = KotlinHighlightRules;
    this.foldingRules = new FoldMode();
    this.$behaviour = new CstyleBehaviour();
};
oop.inherits(Mode, TextMode);
(function () {
    this.lineCommentStart = "//";
    this.blockComment = { start: "/*", end: "*/" };
    this.$id = "ace/mode/kotlin";
}).call(Mode.prototype);
exports.Mode = Mode;

});                (function() {
                    ace.require(["ace/mode/kotlin"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            