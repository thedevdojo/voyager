ace.define("ace/ext/menu_tools/get_editor_keyboard_shortcuts",["require","exports","module","ace/lib/keys"], function(require, exports, module){/*jslint indent: 4, maxerr: 50, white: true, browser: true, vars: true*/
"use strict";
var keys = require("../../lib/keys");
module.exports.getEditorKeybordShortcuts = function (editor) {
    var KEY_MODS = keys.KEY_MODS;
    var keybindings = [];
    var commandMap = {};
    editor.keyBinding.$handlers.forEach(function (handler) {
        var ckb = handler.commandKeyBinding;
        for (var i in ckb) {
            var key = i.replace(/(^|-)\w/g, function (x) { return x.toUpperCase(); });
            var commands = ckb[i];
            if (!Array.isArray(commands))
                commands = [commands];
            commands.forEach(function (command) {
                if (typeof command != "string")
                    command = command.name;
                if (commandMap[command]) {
                    commandMap[command].key += "|" + key;
                }
                else {
                    commandMap[command] = { key: key, command: command };
                    keybindings.push(commandMap[command]);
                }
            });
        }
    });
    return keybindings;
};

});

ace.define("ace/autocomplete/popup",["require","exports","module","ace/virtual_renderer","ace/editor","ace/range","ace/lib/event","ace/lib/lang","ace/lib/dom"], function(require, exports, module){"use strict";
var Renderer = require("../virtual_renderer").VirtualRenderer;
var Editor = require("../editor").Editor;
var Range = require("../range").Range;
var event = require("../lib/event");
var lang = require("../lib/lang");
var dom = require("../lib/dom");
var $singleLineEditor = function (el) {
    var renderer = new Renderer(el);
    renderer.$maxLines = 4;
    var editor = new Editor(renderer);
    editor.setHighlightActiveLine(false);
    editor.setShowPrintMargin(false);
    editor.renderer.setShowGutter(false);
    editor.renderer.setHighlightGutterLine(false);
    editor.$mouseHandler.$focusTimeout = 0;
    editor.$highlightTagPending = true;
    return editor;
};
var AcePopup = function (parentNode) {
    var el = dom.createElement("div");
    var popup = new $singleLineEditor(el);
    if (parentNode)
        parentNode.appendChild(el);
    el.style.display = "none";
    popup.renderer.content.style.cursor = "default";
    popup.renderer.setStyle("ace_autocomplete");
    popup.setOption("displayIndentGuides", false);
    popup.setOption("dragDelay", 150);
    var noop = function () { };
    popup.focus = noop;
    popup.$isFocused = true;
    popup.renderer.$cursorLayer.restartTimer = noop;
    popup.renderer.$cursorLayer.element.style.opacity = 0;
    popup.renderer.$maxLines = 8;
    popup.renderer.$keepTextAreaAtCursor = false;
    popup.setHighlightActiveLine(false);
    popup.session.highlight("");
    popup.session.$searchHighlight.clazz = "ace_highlight-marker";
    popup.on("mousedown", function (e) {
        var pos = e.getDocumentPosition();
        popup.selection.moveToPosition(pos);
        selectionMarker.start.row = selectionMarker.end.row = pos.row;
        e.stop();
    });
    var lastMouseEvent;
    var hoverMarker = new Range(-1, 0, -1, Infinity);
    var selectionMarker = new Range(-1, 0, -1, Infinity);
    selectionMarker.id = popup.session.addMarker(selectionMarker, "ace_active-line", "fullLine");
    popup.setSelectOnHover = function (val) {
        if (!val) {
            hoverMarker.id = popup.session.addMarker(hoverMarker, "ace_line-hover", "fullLine");
        }
        else if (hoverMarker.id) {
            popup.session.removeMarker(hoverMarker.id);
            hoverMarker.id = null;
        }
    };
    popup.setSelectOnHover(false);
    popup.on("mousemove", function (e) {
        if (!lastMouseEvent) {
            lastMouseEvent = e;
            return;
        }
        if (lastMouseEvent.x == e.x && lastMouseEvent.y == e.y) {
            return;
        }
        lastMouseEvent = e;
        lastMouseEvent.scrollTop = popup.renderer.scrollTop;
        var row = lastMouseEvent.getDocumentPosition().row;
        if (hoverMarker.start.row != row) {
            if (!hoverMarker.id)
                popup.setRow(row);
            setHoverMarker(row);
        }
    });
    popup.renderer.on("beforeRender", function () {
        if (lastMouseEvent && hoverMarker.start.row != -1) {
            lastMouseEvent.$pos = null;
            var row = lastMouseEvent.getDocumentPosition().row;
            if (!hoverMarker.id)
                popup.setRow(row);
            setHoverMarker(row, true);
        }
    });
    popup.renderer.on("afterRender", function () {
        var row = popup.getRow();
        var t = popup.renderer.$textLayer;
        var selected = t.element.childNodes[row - t.config.firstRow];
        if (selected !== t.selectedNode && t.selectedNode)
            dom.removeCssClass(t.selectedNode, "ace_selected");
        t.selectedNode = selected;
        if (selected)
            dom.addCssClass(selected, "ace_selected");
    });
    var hideHoverMarker = function () { setHoverMarker(-1); };
    var setHoverMarker = function (row, suppressRedraw) {
        if (row !== hoverMarker.start.row) {
            hoverMarker.start.row = hoverMarker.end.row = row;
            if (!suppressRedraw)
                popup.session._emit("changeBackMarker");
            popup._emit("changeHoverMarker");
        }
    };
    popup.getHoveredRow = function () {
        return hoverMarker.start.row;
    };
    event.addListener(popup.container, "mouseout", hideHoverMarker);
    popup.on("hide", hideHoverMarker);
    popup.on("changeSelection", hideHoverMarker);
    popup.session.doc.getLength = function () {
        return popup.data.length;
    };
    popup.session.doc.getLine = function (i) {
        var data = popup.data[i];
        if (typeof data == "string")
            return data;
        return (data && data.value) || "";
    };
    var bgTokenizer = popup.session.bgTokenizer;
    bgTokenizer.$tokenizeRow = function (row) {
        var data = popup.data[row];
        var tokens = [];
        if (!data)
            return tokens;
        if (typeof data == "string")
            data = { value: data };
        var caption = data.caption || data.value || data.name;
        function addToken(value, className) {
            value && tokens.push({
                type: (data.className || "") + (className || ""),
                value: value
            });
        }
        var lower = caption.toLowerCase();
        var filterText = (popup.filterText || "").toLowerCase();
        var lastIndex = 0;
        var lastI = 0;
        for (var i = 0; i <= filterText.length; i++) {
            if (i != lastI && (data.matchMask & (1 << i) || i == filterText.length)) {
                var sub = filterText.slice(lastI, i);
                lastI = i;
                var index = lower.indexOf(sub, lastIndex);
                if (index == -1)
                    continue;
                addToken(caption.slice(lastIndex, index), "");
                lastIndex = index + sub.length;
                addToken(caption.slice(index, lastIndex), "completion-highlight");
            }
        }
        addToken(caption.slice(lastIndex, caption.length), "");
        if (data.meta)
            tokens.push({ type: "completion-meta", value: data.meta });
        if (data.message)
            tokens.push({ type: "completion-message", value: data.message });
        return tokens;
    };
    bgTokenizer.$updateOnChange = noop;
    bgTokenizer.start = noop;
    popup.session.$computeWidth = function () {
        return this.screenWidth = 0;
    };
    popup.isOpen = false;
    popup.isTopdown = false;
    popup.autoSelect = true;
    popup.filterText = "";
    popup.data = [];
    popup.setData = function (list, filterText) {
        popup.filterText = filterText || "";
        popup.setValue(lang.stringRepeat("\n", list.length), -1);
        popup.data = list || [];
        popup.setRow(0);
    };
    popup.getData = function (row) {
        return popup.data[row];
    };
    popup.getRow = function () {
        return selectionMarker.start.row;
    };
    popup.setRow = function (line) {
        line = Math.max(this.autoSelect ? 0 : -1, Math.min(this.data.length, line));
        if (selectionMarker.start.row != line) {
            popup.selection.clearSelection();
            selectionMarker.start.row = selectionMarker.end.row = line || 0;
            popup.session._emit("changeBackMarker");
            popup.moveCursorTo(line || 0, 0);
            if (popup.isOpen)
                popup._signal("select");
        }
    };
    popup.on("changeSelection", function () {
        if (popup.isOpen)
            popup.setRow(popup.selection.lead.row);
        popup.renderer.scrollCursorIntoView();
    });
    popup.hide = function () {
        this.container.style.display = "none";
        this._signal("hide");
        popup.isOpen = false;
    };
    popup.show = function (pos, lineHeight, topdownOnly) {
        var el = this.container;
        var screenHeight = window.innerHeight;
        var screenWidth = window.innerWidth;
        var renderer = this.renderer;
        var maxH = renderer.$maxLines * lineHeight * 1.4;
        var top = pos.top + this.$borderSize;
        var allowTopdown = top > screenHeight / 2 && !topdownOnly;
        if (allowTopdown && top + lineHeight + maxH > screenHeight) {
            renderer.$maxPixelHeight = top - 2 * this.$borderSize;
            el.style.top = "";
            el.style.bottom = screenHeight - top + "px";
            popup.isTopdown = false;
        }
        else {
            top += lineHeight;
            renderer.$maxPixelHeight = screenHeight - top - 0.2 * lineHeight;
            el.style.top = top + "px";
            el.style.bottom = "";
            popup.isTopdown = true;
        }
        el.style.display = "";
        var left = pos.left;
        if (left + el.offsetWidth > screenWidth)
            left = screenWidth - el.offsetWidth;
        el.style.left = left + "px";
        this._signal("show");
        lastMouseEvent = null;
        popup.isOpen = true;
    };
    popup.goTo = function (where) {
        var row = this.getRow();
        var max = this.session.getLength() - 1;
        switch (where) {
            case "up":
                row = row <= 0 ? max : row - 1;
                break;
            case "down":
                row = row >= max ? -1 : row + 1;
                break;
            case "start":
                row = 0;
                break;
            case "end":
                row = max;
                break;
        }
        this.setRow(row);
    };
    popup.getTextLeftOffset = function () {
        return this.$borderSize + this.renderer.$padding + this.$imageSize;
    };
    popup.$imageSize = 0;
    popup.$borderSize = 1;
    return popup;
};
dom.importCssString("\n.ace_editor.ace_autocomplete .ace_marker-layer .ace_active-line {\n    background-color: #CAD6FA;\n    z-index: 1;\n}\n.ace_dark.ace_editor.ace_autocomplete .ace_marker-layer .ace_active-line {\n    background-color: #3a674e;\n}\n.ace_editor.ace_autocomplete .ace_line-hover {\n    border: 1px solid #abbffe;\n    margin-top: -1px;\n    background: rgba(233,233,253,0.4);\n    position: absolute;\n    z-index: 2;\n}\n.ace_dark.ace_editor.ace_autocomplete .ace_line-hover {\n    border: 1px solid rgba(109, 150, 13, 0.8);\n    background: rgba(58, 103, 78, 0.62);\n}\n.ace_completion-meta {\n    opacity: 0.5;\n    margin: 0.9em;\n}\n.ace_completion-message {\n    color: blue;\n}\n.ace_editor.ace_autocomplete .ace_completion-highlight{\n    color: #2d69c7;\n}\n.ace_dark.ace_editor.ace_autocomplete .ace_completion-highlight{\n    color: #93ca12;\n}\n.ace_editor.ace_autocomplete {\n    width: 300px;\n    z-index: 200000;\n    border: 1px lightgray solid;\n    position: fixed;\n    box-shadow: 2px 3px 5px rgba(0,0,0,.2);\n    line-height: 1.4;\n    background: #fefefe;\n    color: #111;\n}\n.ace_dark.ace_editor.ace_autocomplete {\n    border: 1px #484747 solid;\n    box-shadow: 2px 3px 5px rgba(0, 0, 0, 0.51);\n    line-height: 1.4;\n    background: #25282c;\n    color: #c1c1c1;\n}", "autocompletion.css", false);
exports.AcePopup = AcePopup;
exports.$singleLineEditor = $singleLineEditor;

});

ace.define("ace/autocomplete/util",["require","exports","module"], function(require, exports, module){"use strict";
exports.parForEach = function (array, fn, callback) {
    var completed = 0;
    var arLength = array.length;
    if (arLength === 0)
        callback();
    for (var i = 0; i < arLength; i++) {
        fn(array[i], function (result, err) {
            completed++;
            if (completed === arLength)
                callback(result, err);
        });
    }
};
var ID_REGEX = /[a-zA-Z_0-9\$\-\u00A2-\u2000\u2070-\uFFFF]/;
exports.retrievePrecedingIdentifier = function (text, pos, regex) {
    regex = regex || ID_REGEX;
    var buf = [];
    for (var i = pos - 1; i >= 0; i--) {
        if (regex.test(text[i]))
            buf.push(text[i]);
        else
            break;
    }
    return buf.reverse().join("");
};
exports.retrieveFollowingIdentifier = function (text, pos, regex) {
    regex = regex || ID_REGEX;
    var buf = [];
    for (var i = pos; i < text.length; i++) {
        if (regex.test(text[i]))
            buf.push(text[i]);
        else
            break;
    }
    return buf;
};
exports.getCompletionPrefix = function (editor) {
    var pos = editor.getCursorPosition();
    var line = editor.session.getLine(pos.row);
    var prefix;
    editor.completers.forEach(function (completer) {
        if (completer.identifierRegexps) {
            completer.identifierRegexps.forEach(function (identifierRegex) {
                if (!prefix && identifierRegex)
                    prefix = this.retrievePrecedingIdentifier(line, pos.column, identifierRegex);
            }.bind(this));
        }
    }.bind(this));
    return prefix || this.retrievePrecedingIdentifier(line, pos.column);
};

});

ace.define("ace/snippets",["require","exports","module","ace/lib/dom","ace/lib/oop","ace/lib/event_emitter","ace/lib/lang","ace/range","ace/range_list","ace/keyboard/hash_handler","ace/tokenizer","ace/clipboard","ace/editor"], function(require, exports, module){"use strict";
var dom = require("./lib/dom");
var oop = require("./lib/oop");
var EventEmitter = require("./lib/event_emitter").EventEmitter;
var lang = require("./lib/lang");
var Range = require("./range").Range;
var RangeList = require("./range_list").RangeList;
var HashHandler = require("./keyboard/hash_handler").HashHandler;
var Tokenizer = require("./tokenizer").Tokenizer;
var clipboard = require("./clipboard");
var VARIABLES = {
    CURRENT_WORD: function (editor) {
        return editor.session.getTextRange(editor.session.getWordRange());
    },
    SELECTION: function (editor, name, indentation) {
        var text = editor.session.getTextRange();
        if (indentation)
            return text.replace(/\n\r?([ \t]*\S)/g, "\n" + indentation + "$1");
        return text;
    },
    CURRENT_LINE: function (editor) {
        return editor.session.getLine(editor.getCursorPosition().row);
    },
    PREV_LINE: function (editor) {
        return editor.session.getLine(editor.getCursorPosition().row - 1);
    },
    LINE_INDEX: function (editor) {
        return editor.getCursorPosition().row;
    },
    LINE_NUMBER: function (editor) {
        return editor.getCursorPosition().row + 1;
    },
    SOFT_TABS: function (editor) {
        return editor.session.getUseSoftTabs() ? "YES" : "NO";
    },
    TAB_SIZE: function (editor) {
        return editor.session.getTabSize();
    },
    CLIPBOARD: function (editor) {
        return clipboard.getText && clipboard.getText();
    },
    FILENAME: function (editor) {
        return /[^/\\]*$/.exec(this.FILEPATH(editor))[0];
    },
    FILENAME_BASE: function (editor) {
        return /[^/\\]*$/.exec(this.FILEPATH(editor))[0].replace(/\.[^.]*$/, "");
    },
    DIRECTORY: function (editor) {
        return this.FILEPATH(editor).replace(/[^/\\]*$/, "");
    },
    FILEPATH: function (editor) { return "/not implemented.txt"; },
    WORKSPACE_NAME: function () { return "Unknown"; },
    FULLNAME: function () { return "Unknown"; },
    BLOCK_COMMENT_START: function (editor) {
        var mode = editor.session.$mode || {};
        return mode.blockComment && mode.blockComment.start || "";
    },
    BLOCK_COMMENT_END: function (editor) {
        var mode = editor.session.$mode || {};
        return mode.blockComment && mode.blockComment.end || "";
    },
    LINE_COMMENT: function (editor) {
        var mode = editor.session.$mode || {};
        return mode.lineCommentStart || "";
    },
    CURRENT_YEAR: date.bind(null, { year: "numeric" }),
    CURRENT_YEAR_SHORT: date.bind(null, { year: "2-digit" }),
    CURRENT_MONTH: date.bind(null, { month: "numeric" }),
    CURRENT_MONTH_NAME: date.bind(null, { month: "long" }),
    CURRENT_MONTH_NAME_SHORT: date.bind(null, { month: "short" }),
    CURRENT_DATE: date.bind(null, { day: "2-digit" }),
    CURRENT_DAY_NAME: date.bind(null, { weekday: "long" }),
    CURRENT_DAY_NAME_SHORT: date.bind(null, { weekday: "short" }),
    CURRENT_HOUR: date.bind(null, { hour: "2-digit", hour12: false }),
    CURRENT_MINUTE: date.bind(null, { minute: "2-digit" }),
    CURRENT_SECOND: date.bind(null, { second: "2-digit" })
};
VARIABLES.SELECTED_TEXT = VARIABLES.SELECTION;
function date(dateFormat) {
    var str = new Date().toLocaleString("en-us", dateFormat);
    return str.length == 1 ? "0" + str : str;
}
var SnippetManager = function () {
    this.snippetMap = {};
    this.snippetNameMap = {};
};
(function () {
    oop.implement(this, EventEmitter);
    this.getTokenizer = function () {
        return SnippetManager.$tokenizer || this.createTokenizer();
    };
    this.createTokenizer = function () {
        function TabstopToken(str) {
            str = str.substr(1);
            if (/^\d+$/.test(str))
                return [{ tabstopId: parseInt(str, 10) }];
            return [{ text: str }];
        }
        function escape(ch) {
            return "(?:[^\\\\" + ch + "]|\\\\.)";
        }
        var formatMatcher = {
            regex: "/(" + escape("/") + "+)/",
            onMatch: function (val, state, stack) {
                var ts = stack[0];
                ts.fmtString = true;
                ts.guard = val.slice(1, -1);
                ts.flag = "";
                return "";
            },
            next: "formatString"
        };
        SnippetManager.$tokenizer = new Tokenizer({
            start: [
                { regex: /\\./, onMatch: function (val, state, stack) {
                        var ch = val[1];
                        if (ch == "}" && stack.length) {
                            val = ch;
                        }
                        else if ("`$\\".indexOf(ch) != -1) {
                            val = ch;
                        }
                        return [val];
                    } },
                { regex: /}/, onMatch: function (val, state, stack) {
                        return [stack.length ? stack.shift() : val];
                    } },
                { regex: /\$(?:\d+|\w+)/, onMatch: TabstopToken },
                { regex: /\$\{[\dA-Z_a-z]+/, onMatch: function (str, state, stack) {
                        var t = TabstopToken(str.substr(1));
                        stack.unshift(t[0]);
                        return t;
                    }, next: "snippetVar" },
                { regex: /\n/, token: "newline", merge: false }
            ],
            snippetVar: [
                { regex: "\\|" + escape("\\|") + "*\\|", onMatch: function (val, state, stack) {
                        var choices = val.slice(1, -1).replace(/\\[,|\\]|,/g, function (operator) {
                            return operator.length == 2 ? operator[1] : "\x00";
                        }).split("\x00").map(function (value) {
                            return { value: value };
                        });
                        stack[0].choices = choices;
                        return [choices[0]];
                    }, next: "start" },
                formatMatcher,
                { regex: "([^:}\\\\]|\\\\.)*:?", token: "", next: "start" }
            ],
            formatString: [
                { regex: /:/, onMatch: function (val, state, stack) {
                        if (stack.length && stack[0].expectElse) {
                            stack[0].expectElse = false;
                            stack[0].ifEnd = { elseEnd: stack[0] };
                            return [stack[0].ifEnd];
                        }
                        return ":";
                    } },
                { regex: /\\./, onMatch: function (val, state, stack) {
                        var ch = val[1];
                        if (ch == "}" && stack.length)
                            val = ch;
                        else if ("`$\\".indexOf(ch) != -1)
                            val = ch;
                        else if (ch == "n")
                            val = "\n";
                        else if (ch == "t")
                            val = "\t";
                        else if ("ulULE".indexOf(ch) != -1)
                            val = { changeCase: ch, local: ch > "a" };
                        return [val];
                    } },
                { regex: "/\\w*}", onMatch: function (val, state, stack) {
                        var next = stack.shift();
                        if (next)
                            next.flag = val.slice(1, -1);
                        this.next = next && next.tabstopId ? "start" : "";
                        return [next || val];
                    }, next: "start" },
                { regex: /\$(?:\d+|\w+)/, onMatch: function (val, state, stack) {
                        return [{ text: val.slice(1) }];
                    } },
                { regex: /\${\w+/, onMatch: function (val, state, stack) {
                        var token = { text: val.slice(2) };
                        stack.unshift(token);
                        return [token];
                    }, next: "formatStringVar" },
                { regex: /\n/, token: "newline", merge: false },
                { regex: /}/, onMatch: function (val, state, stack) {
                        var next = stack.shift();
                        this.next = next && next.tabstopId ? "start" : "";
                        return [next || val];
                    }, next: "start" }
            ],
            formatStringVar: [
                { regex: /:\/\w+}/, onMatch: function (val, state, stack) {
                        var ts = stack[0];
                        ts.formatFunction = val.slice(2, -1);
                        return [stack.shift()];
                    }, next: "formatString" },
                formatMatcher,
                { regex: /:[\?\-+]?/, onMatch: function (val, state, stack) {
                        if (val[1] == "+")
                            stack[0].ifEnd = stack[0];
                        if (val[1] == "?")
                            stack[0].expectElse = true;
                    }, next: "formatString" },
                { regex: "([^:}\\\\]|\\\\.)*:?", token: "", next: "formatString" }
            ]
        });
        return SnippetManager.$tokenizer;
    };
    this.tokenizeTmSnippet = function (str, startState) {
        return this.getTokenizer().getLineTokens(str, startState).tokens.map(function (x) {
            return x.value || x;
        });
    };
    this.getVariableValue = function (editor, name, indentation) {
        if (/^\d+$/.test(name))
            return (this.variables.__ || {})[name] || "";
        if (/^[A-Z]\d+$/.test(name))
            return (this.variables[name[0] + "__"] || {})[name.substr(1)] || "";
        name = name.replace(/^TM_/, "");
        if (!this.variables.hasOwnProperty(name))
            return "";
        var value = this.variables[name];
        if (typeof value == "function")
            value = this.variables[name](editor, name, indentation);
        return value == null ? "" : value;
    };
    this.variables = VARIABLES;
    this.tmStrFormat = function (str, ch, editor) {
        if (!ch.fmt)
            return str;
        var flag = ch.flag || "";
        var re = ch.guard;
        re = new RegExp(re, flag.replace(/[^gim]/g, ""));
        var fmtTokens = typeof ch.fmt == "string" ? this.tokenizeTmSnippet(ch.fmt, "formatString") : ch.fmt;
        var _self = this;
        var formatted = str.replace(re, function () {
            var oldArgs = _self.variables.__;
            _self.variables.__ = [].slice.call(arguments);
            var fmtParts = _self.resolveVariables(fmtTokens, editor);
            var gChangeCase = "E";
            for (var i = 0; i < fmtParts.length; i++) {
                var ch = fmtParts[i];
                if (typeof ch == "object") {
                    fmtParts[i] = "";
                    if (ch.changeCase && ch.local) {
                        var next = fmtParts[i + 1];
                        if (next && typeof next == "string") {
                            if (ch.changeCase == "u")
                                fmtParts[i] = next[0].toUpperCase();
                            else
                                fmtParts[i] = next[0].toLowerCase();
                            fmtParts[i + 1] = next.substr(1);
                        }
                    }
                    else if (ch.changeCase) {
                        gChangeCase = ch.changeCase;
                    }
                }
                else if (gChangeCase == "U") {
                    fmtParts[i] = ch.toUpperCase();
                }
                else if (gChangeCase == "L") {
                    fmtParts[i] = ch.toLowerCase();
                }
            }
            _self.variables.__ = oldArgs;
            return fmtParts.join("");
        });
        return formatted;
    };
    this.tmFormatFunction = function (str, ch, editor) {
        if (ch.formatFunction == "upcase")
            return str.toUpperCase();
        if (ch.formatFunction == "downcase")
            return str.toLowerCase();
        return str;
    };
    this.resolveVariables = function (snippet, editor) {
        var result = [];
        var indentation = "";
        var afterNewLine = true;
        for (var i = 0; i < snippet.length; i++) {
            var ch = snippet[i];
            if (typeof ch == "string") {
                result.push(ch);
                if (ch == "\n") {
                    afterNewLine = true;
                    indentation = "";
                }
                else if (afterNewLine) {
                    indentation = /^\t*/.exec(ch)[0];
                    afterNewLine = /\S/.test(ch);
                }
                continue;
            }
            if (!ch)
                continue;
            afterNewLine = false;
            if (ch.fmtString) {
                var j = snippet.indexOf(ch, i + 1);
                if (j == -1)
                    j = snippet.length;
                ch.fmt = snippet.slice(i + 1, j);
                i = j;
            }
            if (ch.text) {
                var value = this.getVariableValue(editor, ch.text, indentation) + "";
                if (ch.fmtString)
                    value = this.tmStrFormat(value, ch, editor);
                if (ch.formatFunction)
                    value = this.tmFormatFunction(value, ch, editor);
                if (value && !ch.ifEnd) {
                    result.push(value);
                    gotoNext(ch);
                }
                else if (!value && ch.ifEnd) {
                    gotoNext(ch.ifEnd);
                }
            }
            else if (ch.elseEnd) {
                gotoNext(ch.elseEnd);
            }
            else if (ch.tabstopId != null) {
                result.push(ch);
            }
            else if (ch.changeCase != null) {
                result.push(ch);
            }
        }
        function gotoNext(ch) {
            var i1 = snippet.indexOf(ch, i + 1);
            if (i1 != -1)
                i = i1;
        }
        return result;
    };
    this.insertSnippetForSelection = function (editor, snippetText) {
        var cursor = editor.getCursorPosition();
        var line = editor.session.getLine(cursor.row);
        var tabString = editor.session.getTabString();
        var indentString = line.match(/^\s*/)[0];
        if (cursor.column < indentString.length)
            indentString = indentString.slice(0, cursor.column);
        snippetText = snippetText.replace(/\r/g, "");
        var tokens = this.tokenizeTmSnippet(snippetText);
        tokens = this.resolveVariables(tokens, editor);
        tokens = tokens.map(function (x) {
            if (x == "\n")
                return x + indentString;
            if (typeof x == "string")
                return x.replace(/\t/g, tabString);
            return x;
        });
        var tabstops = [];
        tokens.forEach(function (p, i) {
            if (typeof p != "object")
                return;
            var id = p.tabstopId;
            var ts = tabstops[id];
            if (!ts) {
                ts = tabstops[id] = [];
                ts.index = id;
                ts.value = "";
                ts.parents = {};
            }
            if (ts.indexOf(p) !== -1)
                return;
            if (p.choices && !ts.choices)
                ts.choices = p.choices;
            ts.push(p);
            var i1 = tokens.indexOf(p, i + 1);
            if (i1 === -1)
                return;
            var value = tokens.slice(i + 1, i1);
            var isNested = value.some(function (t) { return typeof t === "object"; });
            if (isNested && !ts.value) {
                ts.value = value;
            }
            else if (value.length && (!ts.value || typeof ts.value !== "string")) {
                ts.value = value.join("");
            }
        });
        tabstops.forEach(function (ts) { ts.length = 0; });
        var expanding = {};
        function copyValue(val) {
            var copy = [];
            for (var i = 0; i < val.length; i++) {
                var p = val[i];
                if (typeof p == "object") {
                    if (expanding[p.tabstopId])
                        continue;
                    var j = val.lastIndexOf(p, i - 1);
                    p = copy[j] || { tabstopId: p.tabstopId };
                }
                copy[i] = p;
            }
            return copy;
        }
        for (var i = 0; i < tokens.length; i++) {
            var p = tokens[i];
            if (typeof p != "object")
                continue;
            var id = p.tabstopId;
            var ts = tabstops[id];
            var i1 = tokens.indexOf(p, i + 1);
            if (expanding[id]) {
                if (expanding[id] === p) {
                    delete expanding[id];
                    Object.keys(expanding).forEach(function (parentId) {
                        ts.parents[parentId] = true;
                    });
                }
                continue;
            }
            expanding[id] = p;
            var value = ts.value;
            if (typeof value !== "string")
                value = copyValue(value);
            else if (p.fmt)
                value = this.tmStrFormat(value, p, editor);
            tokens.splice.apply(tokens, [i + 1, Math.max(0, i1 - i)].concat(value, p));
            if (ts.indexOf(p) === -1)
                ts.push(p);
        }
        var row = 0, column = 0;
        var text = "";
        tokens.forEach(function (t) {
            if (typeof t === "string") {
                var lines = t.split("\n");
                if (lines.length > 1) {
                    column = lines[lines.length - 1].length;
                    row += lines.length - 1;
                }
                else
                    column += t.length;
                text += t;
            }
            else if (t) {
                if (!t.start)
                    t.start = { row: row, column: column };
                else
                    t.end = { row: row, column: column };
            }
        });
        var range = editor.getSelectionRange();
        var end = editor.session.replace(range, text);
        var tabstopManager = new TabstopManager(editor);
        var selectionId = editor.inVirtualSelectionMode && editor.selection.index;
        tabstopManager.addTabstops(tabstops, range.start, end, selectionId);
    };
    this.insertSnippet = function (editor, snippetText) {
        var self = this;
        if (editor.inVirtualSelectionMode)
            return self.insertSnippetForSelection(editor, snippetText);
        editor.forEachSelection(function () {
            self.insertSnippetForSelection(editor, snippetText);
        }, null, { keepOrder: true });
        if (editor.tabstopManager)
            editor.tabstopManager.tabNext();
    };
    this.$getScope = function (editor) {
        var scope = editor.session.$mode.$id || "";
        scope = scope.split("/").pop();
        if (scope === "html" || scope === "php") {
            if (scope === "php" && !editor.session.$mode.inlinePhp)
                scope = "html";
            var c = editor.getCursorPosition();
            var state = editor.session.getState(c.row);
            if (typeof state === "object") {
                state = state[0];
            }
            if (state.substring) {
                if (state.substring(0, 3) == "js-")
                    scope = "javascript";
                else if (state.substring(0, 4) == "css-")
                    scope = "css";
                else if (state.substring(0, 4) == "php-")
                    scope = "php";
            }
        }
        return scope;
    };
    this.getActiveScopes = function (editor) {
        var scope = this.$getScope(editor);
        var scopes = [scope];
        var snippetMap = this.snippetMap;
        if (snippetMap[scope] && snippetMap[scope].includeScopes) {
            scopes.push.apply(scopes, snippetMap[scope].includeScopes);
        }
        scopes.push("_");
        return scopes;
    };
    this.expandWithTab = function (editor, options) {
        var self = this;
        var result = editor.forEachSelection(function () {
            return self.expandSnippetForSelection(editor, options);
        }, null, { keepOrder: true });
        if (result && editor.tabstopManager)
            editor.tabstopManager.tabNext();
        return result;
    };
    this.expandSnippetForSelection = function (editor, options) {
        var cursor = editor.getCursorPosition();
        var line = editor.session.getLine(cursor.row);
        var before = line.substring(0, cursor.column);
        var after = line.substr(cursor.column);
        var snippetMap = this.snippetMap;
        var snippet;
        this.getActiveScopes(editor).some(function (scope) {
            var snippets = snippetMap[scope];
            if (snippets)
                snippet = this.findMatchingSnippet(snippets, before, after);
            return !!snippet;
        }, this);
        if (!snippet)
            return false;
        if (options && options.dryRun)
            return true;
        editor.session.doc.removeInLine(cursor.row, cursor.column - snippet.replaceBefore.length, cursor.column + snippet.replaceAfter.length);
        this.variables.M__ = snippet.matchBefore;
        this.variables.T__ = snippet.matchAfter;
        this.insertSnippetForSelection(editor, snippet.content);
        this.variables.M__ = this.variables.T__ = null;
        return true;
    };
    this.findMatchingSnippet = function (snippetList, before, after) {
        for (var i = snippetList.length; i--;) {
            var s = snippetList[i];
            if (s.startRe && !s.startRe.test(before))
                continue;
            if (s.endRe && !s.endRe.test(after))
                continue;
            if (!s.startRe && !s.endRe)
                continue;
            s.matchBefore = s.startRe ? s.startRe.exec(before) : [""];
            s.matchAfter = s.endRe ? s.endRe.exec(after) : [""];
            s.replaceBefore = s.triggerRe ? s.triggerRe.exec(before)[0] : "";
            s.replaceAfter = s.endTriggerRe ? s.endTriggerRe.exec(after)[0] : "";
            return s;
        }
    };
    this.snippetMap = {};
    this.snippetNameMap = {};
    this.register = function (snippets, scope) {
        var snippetMap = this.snippetMap;
        var snippetNameMap = this.snippetNameMap;
        var self = this;
        if (!snippets)
            snippets = [];
        function wrapRegexp(src) {
            if (src && !/^\^?\(.*\)\$?$|^\\b$/.test(src))
                src = "(?:" + src + ")";
            return src || "";
        }
        function guardedRegexp(re, guard, opening) {
            re = wrapRegexp(re);
            guard = wrapRegexp(guard);
            if (opening) {
                re = guard + re;
                if (re && re[re.length - 1] != "$")
                    re = re + "$";
            }
            else {
                re = re + guard;
                if (re && re[0] != "^")
                    re = "^" + re;
            }
            return new RegExp(re);
        }
        function addSnippet(s) {
            if (!s.scope)
                s.scope = scope || "_";
            scope = s.scope;
            if (!snippetMap[scope]) {
                snippetMap[scope] = [];
                snippetNameMap[scope] = {};
            }
            var map = snippetNameMap[scope];
            if (s.name) {
                var old = map[s.name];
                if (old)
                    self.unregister(old);
                map[s.name] = s;
            }
            snippetMap[scope].push(s);
            if (s.prefix)
                s.tabTrigger = s.prefix;
            if (!s.content && s.body)
                s.content = Array.isArray(s.body) ? s.body.join("\n") : s.body;
            if (s.tabTrigger && !s.trigger) {
                if (!s.guard && /^\w/.test(s.tabTrigger))
                    s.guard = "\\b";
                s.trigger = lang.escapeRegExp(s.tabTrigger);
            }
            if (!s.trigger && !s.guard && !s.endTrigger && !s.endGuard)
                return;
            s.startRe = guardedRegexp(s.trigger, s.guard, true);
            s.triggerRe = new RegExp(s.trigger);
            s.endRe = guardedRegexp(s.endTrigger, s.endGuard, true);
            s.endTriggerRe = new RegExp(s.endTrigger);
        }
        if (Array.isArray(snippets)) {
            snippets.forEach(addSnippet);
        }
        else {
            Object.keys(snippets).forEach(function (key) {
                addSnippet(snippets[key]);
            });
        }
        this._signal("registerSnippets", { scope: scope });
    };
    this.unregister = function (snippets, scope) {
        var snippetMap = this.snippetMap;
        var snippetNameMap = this.snippetNameMap;
        function removeSnippet(s) {
            var nameMap = snippetNameMap[s.scope || scope];
            if (nameMap && nameMap[s.name]) {
                delete nameMap[s.name];
                var map = snippetMap[s.scope || scope];
                var i = map && map.indexOf(s);
                if (i >= 0)
                    map.splice(i, 1);
            }
        }
        if (snippets.content)
            removeSnippet(snippets);
        else if (Array.isArray(snippets))
            snippets.forEach(removeSnippet);
    };
    this.parseSnippetFile = function (str) {
        str = str.replace(/\r/g, "");
        var list = [], snippet = {};
        var re = /^#.*|^({[\s\S]*})\s*$|^(\S+) (.*)$|^((?:\n*\t.*)+)/gm;
        var m;
        while (m = re.exec(str)) {
            if (m[1]) {
                try {
                    snippet = JSON.parse(m[1]);
                    list.push(snippet);
                }
                catch (e) { }
            }
            if (m[4]) {
                snippet.content = m[4].replace(/^\t/gm, "");
                list.push(snippet);
                snippet = {};
            }
            else {
                var key = m[2], val = m[3];
                if (key == "regex") {
                    var guardRe = /\/((?:[^\/\\]|\\.)*)|$/g;
                    snippet.guard = guardRe.exec(val)[1];
                    snippet.trigger = guardRe.exec(val)[1];
                    snippet.endTrigger = guardRe.exec(val)[1];
                    snippet.endGuard = guardRe.exec(val)[1];
                }
                else if (key == "snippet") {
                    snippet.tabTrigger = val.match(/^\S*/)[0];
                    if (!snippet.name)
                        snippet.name = val;
                }
                else if (key) {
                    snippet[key] = val;
                }
            }
        }
        return list;
    };
    this.getSnippetByName = function (name, editor) {
        var snippetMap = this.snippetNameMap;
        var snippet;
        this.getActiveScopes(editor).some(function (scope) {
            var snippets = snippetMap[scope];
            if (snippets)
                snippet = snippets[name];
            return !!snippet;
        }, this);
        return snippet;
    };
}).call(SnippetManager.prototype);
var TabstopManager = function (editor) {
    if (editor.tabstopManager)
        return editor.tabstopManager;
    editor.tabstopManager = this;
    this.$onChange = this.onChange.bind(this);
    this.$onChangeSelection = lang.delayedCall(this.onChangeSelection.bind(this)).schedule;
    this.$onChangeSession = this.onChangeSession.bind(this);
    this.$onAfterExec = this.onAfterExec.bind(this);
    this.attach(editor);
};
(function () {
    this.attach = function (editor) {
        this.index = 0;
        this.ranges = [];
        this.tabstops = [];
        this.$openTabstops = null;
        this.selectedTabstop = null;
        this.editor = editor;
        this.editor.on("change", this.$onChange);
        this.editor.on("changeSelection", this.$onChangeSelection);
        this.editor.on("changeSession", this.$onChangeSession);
        this.editor.commands.on("afterExec", this.$onAfterExec);
        this.editor.keyBinding.addKeyboardHandler(this.keyboardHandler);
    };
    this.detach = function () {
        this.tabstops.forEach(this.removeTabstopMarkers, this);
        this.ranges = null;
        this.tabstops = null;
        this.selectedTabstop = null;
        this.editor.removeListener("change", this.$onChange);
        this.editor.removeListener("changeSelection", this.$onChangeSelection);
        this.editor.removeListener("changeSession", this.$onChangeSession);
        this.editor.commands.removeListener("afterExec", this.$onAfterExec);
        this.editor.keyBinding.removeKeyboardHandler(this.keyboardHandler);
        this.editor.tabstopManager = null;
        this.editor = null;
    };
    this.onChange = function (delta) {
        var isRemove = delta.action[0] == "r";
        var selectedTabstop = this.selectedTabstop || {};
        var parents = selectedTabstop.parents || {};
        var tabstops = (this.tabstops || []).slice();
        for (var i = 0; i < tabstops.length; i++) {
            var ts = tabstops[i];
            var active = ts == selectedTabstop || parents[ts.index];
            ts.rangeList.$bias = active ? 0 : 1;
            if (delta.action == "remove" && ts !== selectedTabstop) {
                var parentActive = ts.parents && ts.parents[selectedTabstop.index];
                var startIndex = ts.rangeList.pointIndex(delta.start, parentActive);
                startIndex = startIndex < 0 ? -startIndex - 1 : startIndex + 1;
                var endIndex = ts.rangeList.pointIndex(delta.end, parentActive);
                endIndex = endIndex < 0 ? -endIndex - 1 : endIndex - 1;
                var toRemove = ts.rangeList.ranges.slice(startIndex, endIndex);
                for (var j = 0; j < toRemove.length; j++)
                    this.removeRange(toRemove[j]);
            }
            ts.rangeList.$onChange(delta);
        }
        var session = this.editor.session;
        if (!this.$inChange && isRemove && session.getLength() == 1 && !session.getValue())
            this.detach();
    };
    this.updateLinkedFields = function () {
        var ts = this.selectedTabstop;
        if (!ts || !ts.hasLinkedRanges || !ts.firstNonLinked)
            return;
        this.$inChange = true;
        var session = this.editor.session;
        var text = session.getTextRange(ts.firstNonLinked);
        for (var i = 0; i < ts.length; i++) {
            var range = ts[i];
            if (!range.linked)
                continue;
            var original = range.original;
            var fmt = exports.snippetManager.tmStrFormat(text, original, this.editor);
            session.replace(range, fmt);
        }
        this.$inChange = false;
    };
    this.onAfterExec = function (e) {
        if (e.command && !e.command.readOnly)
            this.updateLinkedFields();
    };
    this.onChangeSelection = function () {
        if (!this.editor)
            return;
        var lead = this.editor.selection.lead;
        var anchor = this.editor.selection.anchor;
        var isEmpty = this.editor.selection.isEmpty();
        for (var i = 0; i < this.ranges.length; i++) {
            if (this.ranges[i].linked)
                continue;
            var containsLead = this.ranges[i].contains(lead.row, lead.column);
            var containsAnchor = isEmpty || this.ranges[i].contains(anchor.row, anchor.column);
            if (containsLead && containsAnchor)
                return;
        }
        this.detach();
    };
    this.onChangeSession = function () {
        this.detach();
    };
    this.tabNext = function (dir) {
        var max = this.tabstops.length;
        var index = this.index + (dir || 1);
        index = Math.min(Math.max(index, 1), max);
        if (index == max)
            index = 0;
        this.selectTabstop(index);
        if (index === 0)
            this.detach();
    };
    this.selectTabstop = function (index) {
        this.$openTabstops = null;
        var ts = this.tabstops[this.index];
        if (ts)
            this.addTabstopMarkers(ts);
        this.index = index;
        ts = this.tabstops[this.index];
        if (!ts || !ts.length)
            return;
        this.selectedTabstop = ts;
        var range = ts.firstNonLinked || ts;
        if (ts.choices)
            range.cursor = range.start;
        if (!this.editor.inVirtualSelectionMode) {
            var sel = this.editor.multiSelect;
            sel.toSingleRange(range);
            for (var i = 0; i < ts.length; i++) {
                if (ts.hasLinkedRanges && ts[i].linked)
                    continue;
                sel.addRange(ts[i].clone(), true);
            }
        }
        else {
            this.editor.selection.fromOrientedRange(range);
        }
        this.editor.keyBinding.addKeyboardHandler(this.keyboardHandler);
        if (this.selectedTabstop && this.selectedTabstop.choices)
            this.editor.execCommand("startAutocomplete", { matches: this.selectedTabstop.choices });
    };
    this.addTabstops = function (tabstops, start, end) {
        var useLink = this.useLink || !this.editor.getOption("enableMultiselect");
        if (!this.$openTabstops)
            this.$openTabstops = [];
        if (!tabstops[0]) {
            var p = Range.fromPoints(end, end);
            moveRelative(p.start, start);
            moveRelative(p.end, start);
            tabstops[0] = [p];
            tabstops[0].index = 0;
        }
        var i = this.index;
        var arg = [i + 1, 0];
        var ranges = this.ranges;
        tabstops.forEach(function (ts, index) {
            var dest = this.$openTabstops[index] || ts;
            for (var i = 0; i < ts.length; i++) {
                var p = ts[i];
                var range = Range.fromPoints(p.start, p.end || p.start);
                movePoint(range.start, start);
                movePoint(range.end, start);
                range.original = p;
                range.tabstop = dest;
                ranges.push(range);
                if (dest != ts)
                    dest.unshift(range);
                else
                    dest[i] = range;
                if (p.fmtString || (dest.firstNonLinked && useLink)) {
                    range.linked = true;
                    dest.hasLinkedRanges = true;
                }
                else if (!dest.firstNonLinked)
                    dest.firstNonLinked = range;
            }
            if (!dest.firstNonLinked)
                dest.hasLinkedRanges = false;
            if (dest === ts) {
                arg.push(dest);
                this.$openTabstops[index] = dest;
            }
            this.addTabstopMarkers(dest);
            dest.rangeList = dest.rangeList || new RangeList();
            dest.rangeList.$bias = 0;
            dest.rangeList.addList(dest);
        }, this);
        if (arg.length > 2) {
            if (this.tabstops.length)
                arg.push(arg.splice(2, 1)[0]);
            this.tabstops.splice.apply(this.tabstops, arg);
        }
    };
    this.addTabstopMarkers = function (ts) {
        var session = this.editor.session;
        ts.forEach(function (range) {
            if (!range.markerId)
                range.markerId = session.addMarker(range, "ace_snippet-marker", "text");
        });
    };
    this.removeTabstopMarkers = function (ts) {
        var session = this.editor.session;
        ts.forEach(function (range) {
            session.removeMarker(range.markerId);
            range.markerId = null;
        });
    };
    this.removeRange = function (range) {
        var i = range.tabstop.indexOf(range);
        if (i != -1)
            range.tabstop.splice(i, 1);
        i = this.ranges.indexOf(range);
        if (i != -1)
            this.ranges.splice(i, 1);
        i = range.tabstop.rangeList.ranges.indexOf(range);
        if (i != -1)
            range.tabstop.splice(i, 1);
        this.editor.session.removeMarker(range.markerId);
        if (!range.tabstop.length) {
            i = this.tabstops.indexOf(range.tabstop);
            if (i != -1)
                this.tabstops.splice(i, 1);
            if (!this.tabstops.length)
                this.detach();
        }
    };
    this.keyboardHandler = new HashHandler();
    this.keyboardHandler.bindKeys({
        "Tab": function (editor) {
            if (exports.snippetManager && exports.snippetManager.expandWithTab(editor))
                return;
            editor.tabstopManager.tabNext(1);
            editor.renderer.scrollCursorIntoView();
        },
        "Shift-Tab": function (editor) {
            editor.tabstopManager.tabNext(-1);
            editor.renderer.scrollCursorIntoView();
        },
        "Esc": function (editor) {
            editor.tabstopManager.detach();
        }
    });
}).call(TabstopManager.prototype);
var movePoint = function (point, diff) {
    if (point.row == 0)
        point.column += diff.column;
    point.row += diff.row;
};
var moveRelative = function (point, start) {
    if (point.row == start.row)
        point.column -= start.column;
    point.row -= start.row;
};
dom.importCssString("\n.ace_snippet-marker {\n    -moz-box-sizing: border-box;\n    box-sizing: border-box;\n    background: rgba(194, 193, 208, 0.09);\n    border: 1px dotted rgba(211, 208, 235, 0.62);\n    position: absolute;\n}", "snippets.css", false);
exports.snippetManager = new SnippetManager();
var Editor = require("./editor").Editor;
(function () {
    this.insertSnippet = function (content, options) {
        return exports.snippetManager.insertSnippet(this, content, options);
    };
    this.expandSnippet = function (options) {
        return exports.snippetManager.expandWithTab(this, options);
    };
}).call(Editor.prototype);

});

ace.define("ace/autocomplete",["require","exports","module","ace/keyboard/hash_handler","ace/autocomplete/popup","ace/autocomplete/util","ace/lib/lang","ace/lib/dom","ace/snippets","ace/config"], function(require, exports, module){"use strict";
var HashHandler = require("./keyboard/hash_handler").HashHandler;
var AcePopup = require("./autocomplete/popup").AcePopup;
var util = require("./autocomplete/util");
var lang = require("./lib/lang");
var dom = require("./lib/dom");
var snippetManager = require("./snippets").snippetManager;
var config = require("./config");
var Autocomplete = function () {
    this.autoInsert = false;
    this.autoSelect = true;
    this.exactMatch = false;
    this.gatherCompletionsId = 0;
    this.keyboardHandler = new HashHandler();
    this.keyboardHandler.bindKeys(this.commands);
    this.blurListener = this.blurListener.bind(this);
    this.changeListener = this.changeListener.bind(this);
    this.mousedownListener = this.mousedownListener.bind(this);
    this.mousewheelListener = this.mousewheelListener.bind(this);
    this.changeTimer = lang.delayedCall(function () {
        this.updateCompletions(true);
    }.bind(this));
    this.tooltipTimer = lang.delayedCall(this.updateDocTooltip.bind(this), 50);
};
(function () {
    this.$init = function () {
        this.popup = new AcePopup(document.body || document.documentElement);
        this.popup.on("click", function (e) {
            this.insertMatch();
            e.stop();
        }.bind(this));
        this.popup.focus = this.editor.focus.bind(this.editor);
        this.popup.on("show", this.tooltipTimer.bind(null, null));
        this.popup.on("select", this.tooltipTimer.bind(null, null));
        this.popup.on("changeHoverMarker", this.tooltipTimer.bind(null, null));
        return this.popup;
    };
    this.getPopup = function () {
        return this.popup || this.$init();
    };
    this.openPopup = function (editor, prefix, keepPopupPosition) {
        if (!this.popup)
            this.$init();
        this.popup.autoSelect = this.autoSelect;
        this.popup.setData(this.completions.filtered, this.completions.filterText);
        editor.keyBinding.addKeyboardHandler(this.keyboardHandler);
        var renderer = editor.renderer;
        this.popup.setRow(this.autoSelect ? 0 : -1);
        if (!keepPopupPosition) {
            this.popup.setTheme(editor.getTheme());
            this.popup.setFontSize(editor.getFontSize());
            var lineHeight = renderer.layerConfig.lineHeight;
            var pos = renderer.$cursorLayer.getPixelPosition(this.base, true);
            pos.left -= this.popup.getTextLeftOffset();
            var rect = editor.container.getBoundingClientRect();
            pos.top += rect.top - renderer.layerConfig.offset;
            pos.left += rect.left - editor.renderer.scrollLeft;
            pos.left += renderer.gutterWidth;
            this.popup.show(pos, lineHeight);
        }
        else if (keepPopupPosition && !prefix) {
            this.detach();
        }
        this.changeTimer.cancel();
    };
    this.detach = function () {
        this.editor.keyBinding.removeKeyboardHandler(this.keyboardHandler);
        this.editor.off("changeSelection", this.changeListener);
        this.editor.off("blur", this.blurListener);
        this.editor.off("mousedown", this.mousedownListener);
        this.editor.off("mousewheel", this.mousewheelListener);
        this.changeTimer.cancel();
        this.hideDocTooltip();
        this.gatherCompletionsId += 1;
        if (this.popup && this.popup.isOpen)
            this.popup.hide();
        if (this.base)
            this.base.detach();
        this.activated = false;
        this.completions = this.base = null;
    };
    this.changeListener = function (e) {
        var cursor = this.editor.selection.lead;
        if (cursor.row != this.base.row || cursor.column < this.base.column) {
            this.detach();
        }
        if (this.activated)
            this.changeTimer.schedule();
        else
            this.detach();
    };
    this.blurListener = function (e) {
        var el = document.activeElement;
        var text = this.editor.textInput.getElement();
        var fromTooltip = e.relatedTarget && this.tooltipNode && this.tooltipNode.contains(e.relatedTarget);
        var container = this.popup && this.popup.container;
        if (el != text && el.parentNode != container && !fromTooltip
            && el != this.tooltipNode && e.relatedTarget != text) {
            this.detach();
        }
    };
    this.mousedownListener = function (e) {
        this.detach();
    };
    this.mousewheelListener = function (e) {
        this.detach();
    };
    this.goTo = function (where) {
        this.popup.goTo(where);
    };
    this.insertMatch = function (data, options) {
        if (!data)
            data = this.popup.getData(this.popup.getRow());
        if (!data)
            return false;
        var completions = this.completions;
        this.editor.startOperation({ command: { name: "insertMatch" } });
        if (data.completer && data.completer.insertMatch) {
            data.completer.insertMatch(this.editor, data);
        }
        else {
            if (!completions)
                return false;
            if (completions.filterText) {
                var ranges = this.editor.selection.getAllRanges();
                for (var i = 0, range; range = ranges[i]; i++) {
                    range.start.column -= completions.filterText.length;
                    this.editor.session.remove(range);
                }
            }
            if (data.snippet)
                snippetManager.insertSnippet(this.editor, data.snippet);
            else
                this.editor.execCommand("insertstring", data.value || data);
        }
        if (this.completions == completions)
            this.detach();
        this.editor.endOperation();
    };
    this.commands = {
        "Up": function (editor) { editor.completer.goTo("up"); },
        "Down": function (editor) { editor.completer.goTo("down"); },
        "Ctrl-Up|Ctrl-Home": function (editor) { editor.completer.goTo("start"); },
        "Ctrl-Down|Ctrl-End": function (editor) { editor.completer.goTo("end"); },
        "Esc": function (editor) { editor.completer.detach(); },
        "Return": function (editor) { return editor.completer.insertMatch(); },
        "Shift-Return": function (editor) { editor.completer.insertMatch(null, { deleteSuffix: true }); },
        "Tab": function (editor) {
            var result = editor.completer.insertMatch();
            if (!result && !editor.tabstopManager)
                editor.completer.goTo("down");
            else
                return result;
        },
        "PageUp": function (editor) { editor.completer.popup.gotoPageUp(); },
        "PageDown": function (editor) { editor.completer.popup.gotoPageDown(); }
    };
    this.gatherCompletions = function (editor, callback) {
        var session = editor.getSession();
        var pos = editor.getCursorPosition();
        var prefix = util.getCompletionPrefix(editor);
        this.base = session.doc.createAnchor(pos.row, pos.column - prefix.length);
        this.base.$insertRight = true;
        var matches = [];
        var total = editor.completers.length;
        editor.completers.forEach(function (completer, i) {
            completer.getCompletions(editor, session, pos, prefix, function (err, results) {
                if (!err && results)
                    matches = matches.concat(results);
                callback(null, {
                    prefix: util.getCompletionPrefix(editor),
                    matches: matches,
                    finished: (--total === 0)
                });
            });
        });
        return true;
    };
    this.showPopup = function (editor, options) {
        if (this.editor)
            this.detach();
        this.activated = true;
        this.editor = editor;
        if (editor.completer != this) {
            if (editor.completer)
                editor.completer.detach();
            editor.completer = this;
        }
        editor.on("changeSelection", this.changeListener);
        editor.on("blur", this.blurListener);
        editor.on("mousedown", this.mousedownListener);
        editor.on("mousewheel", this.mousewheelListener);
        this.updateCompletions(false, options);
    };
    this.updateCompletions = function (keepPopupPosition, options) {
        if (keepPopupPosition && this.base && this.completions) {
            var pos = this.editor.getCursorPosition();
            var prefix = this.editor.session.getTextRange({ start: this.base, end: pos });
            if (prefix == this.completions.filterText)
                return;
            this.completions.setFilter(prefix);
            if (!this.completions.filtered.length)
                return this.detach();
            if (this.completions.filtered.length == 1
                && this.completions.filtered[0].value == prefix
                && !this.completions.filtered[0].snippet)
                return this.detach();
            this.openPopup(this.editor, prefix, keepPopupPosition);
            return;
        }
        if (options && options.matches) {
            var pos = this.editor.getSelectionRange().start;
            this.base = this.editor.session.doc.createAnchor(pos.row, pos.column);
            this.base.$insertRight = true;
            this.completions = new FilteredList(options.matches);
            return this.openPopup(this.editor, "", keepPopupPosition);
        }
        var _id = this.gatherCompletionsId;
        var detachIfFinished = function (results) {
            if (!results.finished)
                return;
            return this.detach();
        }.bind(this);
        var processResults = function (results) {
            var prefix = results.prefix;
            var matches = results.matches;
            this.completions = new FilteredList(matches);
            if (this.exactMatch)
                this.completions.exactMatch = true;
            this.completions.setFilter(prefix);
            var filtered = this.completions.filtered;
            if (!filtered.length)
                return detachIfFinished(results);
            if (filtered.length == 1 && filtered[0].value == prefix && !filtered[0].snippet)
                return detachIfFinished(results);
            if (this.autoInsert && filtered.length == 1 && results.finished)
                return this.insertMatch(filtered[0]);
            this.openPopup(this.editor, prefix, keepPopupPosition);
        }.bind(this);
        var isImmediate = true;
        var immediateResults = null;
        this.gatherCompletions(this.editor, function (err, results) {
            var prefix = results.prefix;
            var matches = results && results.matches;
            if (!matches || !matches.length)
                return detachIfFinished(results);
            if (prefix.indexOf(results.prefix) !== 0 || _id != this.gatherCompletionsId)
                return;
            if (isImmediate) {
                immediateResults = results;
                return;
            }
            processResults(results);
        }.bind(this));
        isImmediate = false;
        if (immediateResults) {
            var results = immediateResults;
            immediateResults = null;
            processResults(results);
        }
    };
    this.cancelContextMenu = function () {
        this.editor.$mouseHandler.cancelContextMenu();
    };
    this.updateDocTooltip = function () {
        var popup = this.popup;
        var all = popup.data;
        var selected = all && (all[popup.getHoveredRow()] || all[popup.getRow()]);
        var doc = null;
        if (!selected || !this.editor || !this.popup.isOpen)
            return this.hideDocTooltip();
        this.editor.completers.some(function (completer) {
            if (completer.getDocTooltip)
                doc = completer.getDocTooltip(selected);
            return doc;
        });
        if (!doc && typeof selected != "string")
            doc = selected;
        if (typeof doc == "string")
            doc = { docText: doc };
        if (!doc || !(doc.docHTML || doc.docText))
            return this.hideDocTooltip();
        this.showDocTooltip(doc);
    };
    this.showDocTooltip = function (item) {
        if (!this.tooltipNode) {
            this.tooltipNode = dom.createElement("div");
            this.tooltipNode.className = "ace_tooltip ace_doc-tooltip";
            this.tooltipNode.style.margin = 0;
            this.tooltipNode.style.pointerEvents = "auto";
            this.tooltipNode.tabIndex = -1;
            this.tooltipNode.onblur = this.blurListener.bind(this);
            this.tooltipNode.onclick = this.onTooltipClick.bind(this);
        }
        var tooltipNode = this.tooltipNode;
        if (item.docHTML) {
            tooltipNode.innerHTML = item.docHTML;
        }
        else if (item.docText) {
            tooltipNode.textContent = item.docText;
        }
        if (!tooltipNode.parentNode)
            document.body.appendChild(tooltipNode);
        var popup = this.popup;
        var rect = popup.container.getBoundingClientRect();
        tooltipNode.style.top = popup.container.style.top;
        tooltipNode.style.bottom = popup.container.style.bottom;
        tooltipNode.style.display = "block";
        if (window.innerWidth - rect.right < 320) {
            if (rect.left < 320) {
                if (popup.isTopdown) {
                    tooltipNode.style.top = rect.bottom + "px";
                    tooltipNode.style.left = rect.left + "px";
                    tooltipNode.style.right = "";
                    tooltipNode.style.bottom = "";
                }
                else {
                    tooltipNode.style.top = popup.container.offsetTop - tooltipNode.offsetHeight + "px";
                    tooltipNode.style.left = rect.left + "px";
                    tooltipNode.style.right = "";
                    tooltipNode.style.bottom = "";
                }
            }
            else {
                tooltipNode.style.right = window.innerWidth - rect.left + "px";
                tooltipNode.style.left = "";
            }
        }
        else {
            tooltipNode.style.left = (rect.right + 1) + "px";
            tooltipNode.style.right = "";
        }
    };
    this.hideDocTooltip = function () {
        this.tooltipTimer.cancel();
        if (!this.tooltipNode)
            return;
        var el = this.tooltipNode;
        if (!this.editor.isFocused() && document.activeElement == el)
            this.editor.focus();
        this.tooltipNode = null;
        if (el.parentNode)
            el.parentNode.removeChild(el);
    };
    this.onTooltipClick = function (e) {
        var a = e.target;
        while (a && a != this.tooltipNode) {
            if (a.nodeName == "A" && a.href) {
                a.rel = "noreferrer";
                a.target = "_blank";
                break;
            }
            a = a.parentNode;
        }
    };
    this.destroy = function () {
        this.detach();
        if (this.popup) {
            this.popup.destroy();
            var el = this.popup.container;
            if (el && el.parentNode)
                el.parentNode.removeChild(el);
        }
        if (this.editor && this.editor.completer == this)
            this.editor.completer == null;
        this.popup = null;
    };
}).call(Autocomplete.prototype);
Autocomplete.for = function (editor) {
    if (editor.completer) {
        return editor.completer;
    }
    if (config.get("sharedPopups")) {
        if (!Autocomplete.$shared)
            Autocomplete.$sharedInstance = new Autocomplete();
        editor.completer = Autocomplete.$sharedInstance;
    }
    else {
        editor.completer = new Autocomplete();
        editor.once("destroy", function (e, editor) {
            editor.completer.destroy();
        });
    }
    return editor.completer;
};
Autocomplete.startCommand = {
    name: "startAutocomplete",
    exec: function (editor, options) {
        var completer = Autocomplete.for(editor);
        completer.autoInsert = false;
        completer.autoSelect = true;
        completer.showPopup(editor, options);
        completer.cancelContextMenu();
    },
    bindKey: "Ctrl-Space|Ctrl-Shift-Space|Alt-Space"
};
var FilteredList = function (array, filterText) {
    this.all = array;
    this.filtered = array;
    this.filterText = filterText || "";
    this.exactMatch = false;
};
(function () {
    this.setFilter = function (str) {
        if (str.length > this.filterText && str.lastIndexOf(this.filterText, 0) === 0)
            var matches = this.filtered;
        else
            var matches = this.all;
        this.filterText = str;
        matches = this.filterCompletions(matches, this.filterText);
        matches = matches.sort(function (a, b) {
            return b.exactMatch - a.exactMatch || b.$score - a.$score
                || (a.caption || a.value).localeCompare(b.caption || b.value);
        });
        var prev = null;
        matches = matches.filter(function (item) {
            var caption = item.snippet || item.caption || item.value;
            if (caption === prev)
                return false;
            prev = caption;
            return true;
        });
        this.filtered = matches;
    };
    this.filterCompletions = function (items, needle) {
        var results = [];
        var upper = needle.toUpperCase();
        var lower = needle.toLowerCase();
        loop: for (var i = 0, item; item = items[i]; i++) {
            var caption = item.caption || item.value || item.snippet;
            if (!caption)
                continue;
            var lastIndex = -1;
            var matchMask = 0;
            var penalty = 0;
            var index, distance;
            if (this.exactMatch) {
                if (needle !== caption.substr(0, needle.length))
                    continue loop;
            }
            else {
                var fullMatchIndex = caption.toLowerCase().indexOf(lower);
                if (fullMatchIndex > -1) {
                    penalty = fullMatchIndex;
                }
                else {
                    for (var j = 0; j < needle.length; j++) {
                        var i1 = caption.indexOf(lower[j], lastIndex + 1);
                        var i2 = caption.indexOf(upper[j], lastIndex + 1);
                        index = (i1 >= 0) ? ((i2 < 0 || i1 < i2) ? i1 : i2) : i2;
                        if (index < 0)
                            continue loop;
                        distance = index - lastIndex - 1;
                        if (distance > 0) {
                            if (lastIndex === -1)
                                penalty += 10;
                            penalty += distance;
                            matchMask = matchMask | (1 << j);
                        }
                        lastIndex = index;
                    }
                }
            }
            item.matchMask = matchMask;
            item.exactMatch = penalty ? 0 : 1;
            item.$score = (item.score || 0) - penalty;
            results.push(item);
        }
        return results;
    };
}).call(FilteredList.prototype);
exports.Autocomplete = Autocomplete;
exports.FilteredList = FilteredList;

});

ace.define("ace/ext/menu_tools/settings_menu.css",["require","exports","module"], function(require, exports, module){module.exports = "#ace_settingsmenu, #kbshortcutmenu {\n    background-color: #F7F7F7;\n    color: black;\n    box-shadow: -5px 4px 5px rgba(126, 126, 126, 0.55);\n    padding: 1em 0.5em 2em 1em;\n    overflow: auto;\n    position: absolute;\n    margin: 0;\n    bottom: 0;\n    right: 0;\n    top: 0;\n    z-index: 9991;\n    cursor: default;\n}\n\n.ace_dark #ace_settingsmenu, .ace_dark #kbshortcutmenu {\n    box-shadow: -20px 10px 25px rgba(126, 126, 126, 0.25);\n    background-color: rgba(255, 255, 255, 0.6);\n    color: black;\n}\n\n.ace_optionsMenuEntry:hover {\n    background-color: rgba(100, 100, 100, 0.1);\n    transition: all 0.3s\n}\n\n.ace_closeButton {\n    background: rgba(245, 146, 146, 0.5);\n    border: 1px solid #F48A8A;\n    border-radius: 50%;\n    padding: 7px;\n    position: absolute;\n    right: -8px;\n    top: -8px;\n    z-index: 100000;\n}\n.ace_closeButton{\n    background: rgba(245, 146, 146, 0.9);\n}\n.ace_optionsMenuKey {\n    color: darkslateblue;\n    font-weight: bold;\n}\n.ace_optionsMenuCommand {\n    color: darkcyan;\n    font-weight: normal;\n}\n.ace_optionsMenuEntry input, .ace_optionsMenuEntry button {\n    vertical-align: middle;\n}\n\n.ace_optionsMenuEntry button[ace_selected_button=true] {\n    background: #e7e7e7;\n    box-shadow: 1px 0px 2px 0px #adadad inset;\n    border-color: #adadad;\n}\n.ace_optionsMenuEntry button {\n    background: white;\n    border: 1px solid lightgray;\n    margin: 0px;\n}\n.ace_optionsMenuEntry button:hover{\n    background: #f0f0f0;\n}";

});

ace.define("ace/ext/menu_tools/overlay_page",["require","exports","module","ace/lib/dom","ace/ext/menu_tools/settings_menu.css"], function(require, exports, module){/*jslint indent: 4, maxerr: 50, white: true, browser: true, vars: true*/
'use strict';
var dom = require("../../lib/dom");
var cssText = require("./settings_menu.css");
dom.importCssString(cssText, "settings_menu.css", false);
module.exports.overlayPage = function overlayPage(editor, contentElement, callback) {
    var closer = document.createElement('div');
    var ignoreFocusOut = false;
    function documentEscListener(e) {
        if (e.keyCode === 27) {
            close();
        }
    }
    function close() {
        if (!closer)
            return;
        document.removeEventListener('keydown', documentEscListener);
        closer.parentNode.removeChild(closer);
        if (editor) {
            editor.focus();
        }
        closer = null;
        callback && callback();
    }
    function setIgnoreFocusOut(ignore) {
        ignoreFocusOut = ignore;
        if (ignore) {
            closer.style.pointerEvents = "none";
            contentElement.style.pointerEvents = "auto";
        }
    }
    closer.style.cssText = 'margin: 0; padding: 0; ' +
        'position: fixed; top:0; bottom:0; left:0; right:0;' +
        'z-index: 9990; ' +
        (editor ? 'background-color: rgba(0, 0, 0, 0.3);' : '');
    closer.addEventListener('click', function (e) {
        if (!ignoreFocusOut) {
            close();
        }
    });
    document.addEventListener('keydown', documentEscListener);
    contentElement.addEventListener('click', function (e) {
        e.stopPropagation();
    });
    closer.appendChild(contentElement);
    document.body.appendChild(closer);
    if (editor) {
        editor.blur();
    }
    return {
        close: close,
        setIgnoreFocusOut: setIgnoreFocusOut
    };
};

});

ace.define("ace/ext/modelist",["require","exports","module"], function(require, exports, module){"use strict";
var modes = [];
function getModeForPath(path) {
    var mode = modesByName.text;
    var fileName = path.split(/[\/\\]/).pop();
    for (var i = 0; i < modes.length; i++) {
        if (modes[i].supportsFile(fileName)) {
            mode = modes[i];
            break;
        }
    }
    return mode;
}
var Mode = function (name, caption, extensions) {
    this.name = name;
    this.caption = caption;
    this.mode = "ace/mode/" + name;
    this.extensions = extensions;
    var re;
    if (/\^/.test(extensions)) {
        re = extensions.replace(/\|(\^)?/g, function (a, b) {
            return "$|" + (b ? "^" : "^.*\\.");
        }) + "$";
    }
    else {
        re = "^.*\\.(" + extensions + ")$";
    }
    this.extRe = new RegExp(re, "gi");
};
Mode.prototype.supportsFile = function (filename) {
    return filename.match(this.extRe);
};
var supportedModes = {
    ABAP: ["abap"],
    ABC: ["abc"],
    ActionScript: ["as"],
    ADA: ["ada|adb"],
    Alda: ["alda"],
    Apache_Conf: ["^htaccess|^htgroups|^htpasswd|^conf|htaccess|htgroups|htpasswd"],
    Apex: ["apex|cls|trigger|tgr"],
    AQL: ["aql"],
    AsciiDoc: ["asciidoc|adoc"],
    ASL: ["dsl|asl|asl.json"],
    Assembly_x86: ["asm|a"],
    AutoHotKey: ["ahk"],
    BatchFile: ["bat|cmd"],
    BibTeX: ["bib"],
    C_Cpp: ["cpp|c|cc|cxx|h|hh|hpp|ino"],
    C9Search: ["c9search_results"],
    Cirru: ["cirru|cr"],
    Clojure: ["clj|cljs"],
    Cobol: ["CBL|COB"],
    coffee: ["coffee|cf|cson|^Cakefile"],
    ColdFusion: ["cfm"],
    Crystal: ["cr"],
    CSharp: ["cs"],
    Csound_Document: ["csd"],
    Csound_Orchestra: ["orc"],
    Csound_Score: ["sco"],
    CSS: ["css"],
    Curly: ["curly"],
    D: ["d|di"],
    Dart: ["dart"],
    Diff: ["diff|patch"],
    Dockerfile: ["^Dockerfile"],
    Dot: ["dot"],
    Drools: ["drl"],
    Edifact: ["edi"],
    Eiffel: ["e|ge"],
    EJS: ["ejs"],
    Elixir: ["ex|exs"],
    Elm: ["elm"],
    Erlang: ["erl|hrl"],
    Forth: ["frt|fs|ldr|fth|4th"],
    Fortran: ["f|f90"],
    FSharp: ["fsi|fs|ml|mli|fsx|fsscript"],
    FSL: ["fsl"],
    FTL: ["ftl"],
    Gcode: ["gcode"],
    Gherkin: ["feature"],
    Gitignore: ["^.gitignore"],
    Glsl: ["glsl|frag|vert"],
    Gobstones: ["gbs"],
    golang: ["go"],
    GraphQLSchema: ["gql"],
    Groovy: ["groovy"],
    HAML: ["haml"],
    Handlebars: ["hbs|handlebars|tpl|mustache"],
    Haskell: ["hs"],
    Haskell_Cabal: ["cabal"],
    haXe: ["hx"],
    Hjson: ["hjson"],
    HTML: ["html|htm|xhtml|vue|we|wpy"],
    HTML_Elixir: ["eex|html.eex"],
    HTML_Ruby: ["erb|rhtml|html.erb"],
    INI: ["ini|conf|cfg|prefs"],
    Io: ["io"],
    Ion: ["ion"],
    Jack: ["jack"],
    Jade: ["jade|pug"],
    Java: ["java"],
    JavaScript: ["js|jsm|jsx|cjs|mjs"],
    JSON: ["json"],
    JSON5: ["json5"],
    JSONiq: ["jq"],
    JSP: ["jsp"],
    JSSM: ["jssm|jssm_state"],
    JSX: ["jsx"],
    Julia: ["jl"],
    Kotlin: ["kt|kts"],
    LaTeX: ["tex|latex|ltx|bib"],
    Latte: ["latte"],
    LESS: ["less"],
    Liquid: ["liquid"],
    Lisp: ["lisp"],
    LiveScript: ["ls"],
    Log: ["log"],
    LogiQL: ["logic|lql"],
    LSL: ["lsl"],
    Lua: ["lua"],
    LuaPage: ["lp"],
    Lucene: ["lucene"],
    Makefile: ["^Makefile|^GNUmakefile|^makefile|^OCamlMakefile|make"],
    Markdown: ["md|markdown"],
    Mask: ["mask"],
    MATLAB: ["matlab"],
    Maze: ["mz"],
    MediaWiki: ["wiki|mediawiki"],
    MEL: ["mel"],
    MIPS: ["s|asm"],
    MIXAL: ["mixal"],
    MUSHCode: ["mc|mush"],
    MySQL: ["mysql"],
    Nginx: ["nginx|conf"],
    Nim: ["nim"],
    Nix: ["nix"],
    NSIS: ["nsi|nsh"],
    Nunjucks: ["nunjucks|nunjs|nj|njk"],
    ObjectiveC: ["m|mm"],
    OCaml: ["ml|mli"],
    PartiQL: ["partiql|pql"],
    Pascal: ["pas|p"],
    Perl: ["pl|pm"],
    pgSQL: ["pgsql"],
    PHP_Laravel_blade: ["blade.php"],
    PHP: ["php|inc|phtml|shtml|php3|php4|php5|phps|phpt|aw|ctp|module"],
    Pig: ["pig"],
    Powershell: ["ps1"],
    Praat: ["praat|praatscript|psc|proc"],
    Prisma: ["prisma"],
    Prolog: ["plg|prolog"],
    Properties: ["properties"],
    Protobuf: ["proto"],
    Puppet: ["epp|pp"],
    Python: ["py"],
    QML: ["qml"],
    R: ["r"],
    Raku: ["raku|rakumod|rakutest|p6|pl6|pm6"],
    Razor: ["cshtml|asp"],
    RDoc: ["Rd"],
    Red: ["red|reds"],
    RHTML: ["Rhtml"],
    Robot: ["robot|resource"],
    RST: ["rst"],
    Ruby: ["rb|ru|gemspec|rake|^Guardfile|^Rakefile|^Gemfile"],
    Rust: ["rs"],
    SaC: ["sac"],
    SASS: ["sass"],
    SCAD: ["scad"],
    Scala: ["scala|sbt"],
    Scheme: ["scm|sm|rkt|oak|scheme"],
    Scrypt: ["scrypt"],
    SCSS: ["scss"],
    SH: ["sh|bash|^.bashrc"],
    SJS: ["sjs"],
    Slim: ["slim|skim"],
    Smarty: ["smarty|tpl"],
    Smithy: ["smithy"],
    snippets: ["snippets"],
    Soy_Template: ["soy"],
    Space: ["space"],
    SQL: ["sql"],
    SQLServer: ["sqlserver"],
    Stylus: ["styl|stylus"],
    SVG: ["svg"],
    Swift: ["swift"],
    Tcl: ["tcl"],
    Terraform: ["tf", "tfvars", "terragrunt"],
    Tex: ["tex"],
    Text: ["txt"],
    Textile: ["textile"],
    Toml: ["toml"],
    TSX: ["tsx"],
    Twig: ["twig|swig"],
    Typescript: ["ts|typescript|str"],
    Vala: ["vala"],
    VBScript: ["vbs|vb"],
    Velocity: ["vm"],
    Verilog: ["v|vh|sv|svh"],
    VHDL: ["vhd|vhdl"],
    Visualforce: ["vfp|component|page"],
    Wollok: ["wlk|wpgm|wtest"],
    XML: ["xml|rdf|rss|wsdl|xslt|atom|mathml|mml|xul|xbl|xaml"],
    XQuery: ["xq"],
    YAML: ["yaml|yml"],
    Zeek: ["zeek|bro"],
    Django: ["html"]
};
var nameOverrides = {
    ObjectiveC: "Objective-C",
    CSharp: "C#",
    golang: "Go",
    C_Cpp: "C and C++",
    Csound_Document: "Csound Document",
    Csound_Orchestra: "Csound",
    Csound_Score: "Csound Score",
    coffee: "CoffeeScript",
    HTML_Ruby: "HTML (Ruby)",
    HTML_Elixir: "HTML (Elixir)",
    FTL: "FreeMarker",
    PHP_Laravel_blade: "PHP (Blade Template)",
    Perl6: "Perl 6",
    AutoHotKey: "AutoHotkey / AutoIt"
};
var modesByName = {};
for (var name in supportedModes) {
    var data = supportedModes[name];
    var displayName = (nameOverrides[name] || name).replace(/_/g, " ");
    var filename = name.toLowerCase();
    var mode = new Mode(filename, displayName, data[0]);
    modesByName[filename] = mode;
    modes.push(mode);
}
module.exports = {
    getModeForPath: getModeForPath,
    modes: modes,
    modesByName: modesByName
};

});

ace.define("ace/ext/prompt",["require","exports","module","ace/range","ace/lib/dom","ace/ext/menu_tools/get_editor_keyboard_shortcuts","ace/autocomplete","ace/autocomplete/popup","ace/autocomplete/popup","ace/undomanager","ace/tokenizer","ace/ext/menu_tools/overlay_page","ace/ext/modelist"], function(require, exports, module){/**
 * Prompt plugin is used for getting input from user.
 *
 * @param {Object} editor                   Ouside editor related to this prompt. Will be blurred when prompt is open.
 * @param {String} message                  Predefined value of prompt input box.
 * @param {Object} options                  Cusomizable options for this prompt.
 * @param {String} options.name             Prompt name.
 * @param {String} options.$type            Use prompt of specific type (gotoLine|commands|modes or default if empty).
 * @param {[start, end]} options.selection  Defines which part of the predefined value should be highlited.
 * @param {Boolean} options.hasDescription  Set to true if prompt has description below input box.
 * @param {String} options.prompt           Description below input box.
 * @param {String} options.placeholder      Placeholder for value.
 * @param {Object} options.$rules           Specific rules for input like password or regexp.
 * @param {Boolean} options.ignoreFocusOut  Set to true to keep the prompt open when focus moves to another part of the editor.
 * @param {Function} options.getCompletions Function for defining list of options for value.
 * @param {Function} options.getPrefix      Function for defining current value prefix.
 * @param {Function} options.onAccept       Function called when Enter is pressed.
 * @param {Function} options.onInput        Function called when input is added to prompt input box.
 * @param {Function} options.onCancel       Function called when Esc|Shift-Esc is pressed.
 * @param {Function} callback               Function called after done.
 * */
"use strict";
var Range = require("../range").Range;
var dom = require("../lib/dom");
var shortcuts = require("../ext/menu_tools/get_editor_keyboard_shortcuts");
var FilteredList = require("../autocomplete").FilteredList;
var AcePopup = require('../autocomplete/popup').AcePopup;
var $singleLineEditor = require('../autocomplete/popup').$singleLineEditor;
var UndoManager = require("../undomanager").UndoManager;
var Tokenizer = require("../tokenizer").Tokenizer;
var overlayPage = require("./menu_tools/overlay_page").overlayPage;
var modelist = require("./modelist");
var openPrompt;
function prompt(editor, message, options, callback) {
    if (typeof message == "object") {
        return prompt(editor, "", message, options);
    }
    if (openPrompt) {
        var lastPrompt = openPrompt;
        editor = lastPrompt.editor;
        lastPrompt.close();
        if (lastPrompt.name && lastPrompt.name == options.name)
            return;
    }
    if (options.$type)
        return prompt[options.$type](editor, callback);
    var cmdLine = $singleLineEditor();
    cmdLine.session.setUndoManager(new UndoManager());
    var el = dom.buildDom(["div", { class: "ace_prompt_container" + (options.hasDescription ? " input-box-with-description" : "") }]);
    var overlay = overlayPage(editor, el, done);
    el.appendChild(cmdLine.container);
    if (editor) {
        editor.cmdLine = cmdLine;
        cmdLine.setOption("fontSize", editor.getOption("fontSize"));
    }
    if (message) {
        cmdLine.setValue(message, 1);
    }
    if (options.selection) {
        cmdLine.selection.setRange({
            start: cmdLine.session.doc.indexToPosition(options.selection[0]),
            end: cmdLine.session.doc.indexToPosition(options.selection[1])
        });
    }
    if (options.getCompletions) {
        var popup = new AcePopup();
        popup.renderer.setStyle("ace_autocomplete_inline");
        popup.container.style.display = "block";
        popup.container.style.maxWidth = "600px";
        popup.container.style.width = "100%";
        popup.container.style.marginTop = "3px";
        popup.renderer.setScrollMargin(2, 2, 0, 0);
        popup.autoSelect = false;
        popup.renderer.$maxLines = 15;
        popup.setRow(-1);
        popup.on("click", function (e) {
            var data = popup.getData(popup.getRow());
            if (!data.error) {
                cmdLine.setValue(data.value || data.name || data);
                accept();
                e.stop();
            }
        });
        el.appendChild(popup.container);
        updateCompletions();
    }
    if (options.$rules) {
        var tokenizer = new Tokenizer(options.$rules);
        cmdLine.session.bgTokenizer.setTokenizer(tokenizer);
    }
    if (options.placeholder) {
        cmdLine.setOption("placeholder", options.placeholder);
    }
    if (options.hasDescription) {
        var promptTextContainer = dom.buildDom(["div", { class: "ace_prompt_text_container" }]);
        dom.buildDom(options.prompt || "Press 'Enter' to confirm or 'Escape' to cancel", promptTextContainer);
        el.appendChild(promptTextContainer);
    }
    overlay.setIgnoreFocusOut(options.ignoreFocusOut);
    function accept() {
        var val;
        if (popup && popup.getCursorPosition().row > 0) {
            val = valueFromRecentList();
        }
        else {
            val = cmdLine.getValue();
        }
        var curData = popup ? popup.getData(popup.getRow()) : val;
        if (curData && !curData.error) {
            done();
            options.onAccept && options.onAccept({
                value: val,
                item: curData
            }, cmdLine);
        }
    }
    var keys = {
        "Enter": accept,
        "Esc|Shift-Esc": function () {
            options.onCancel && options.onCancel(cmdLine.getValue(), cmdLine);
            done();
        }
    };
    if (popup) {
        Object.assign(keys, {
            "Up": function (editor) { popup.goTo("up"); valueFromRecentList(); },
            "Down": function (editor) { popup.goTo("down"); valueFromRecentList(); },
            "Ctrl-Up|Ctrl-Home": function (editor) { popup.goTo("start"); valueFromRecentList(); },
            "Ctrl-Down|Ctrl-End": function (editor) { popup.goTo("end"); valueFromRecentList(); },
            "Tab": function (editor) {
                popup.goTo("down");
                valueFromRecentList();
            },
            "PageUp": function (editor) { popup.gotoPageUp(); valueFromRecentList(); },
            "PageDown": function (editor) { popup.gotoPageDown(); valueFromRecentList(); }
        });
    }
    cmdLine.commands.bindKeys(keys);
    function done() {
        overlay.close();
        callback && callback();
        openPrompt = null;
    }
    cmdLine.on("input", function () {
        options.onInput && options.onInput();
        updateCompletions();
    });
    function updateCompletions() {
        if (options.getCompletions) {
            var prefix;
            if (options.getPrefix) {
                prefix = options.getPrefix(cmdLine);
            }
            var completions = options.getCompletions(cmdLine);
            popup.setData(completions, prefix);
            popup.resize(true);
        }
    }
    function valueFromRecentList() {
        var current = popup.getData(popup.getRow());
        if (current && !current.error)
            return current.value || current.caption || current;
    }
    cmdLine.resize(true);
    if (popup) {
        popup.resize(true);
    }
    cmdLine.focus();
    openPrompt = {
        close: done,
        name: options.name,
        editor: editor
    };
}
prompt.gotoLine = function (editor, callback) {
    function stringifySelection(selection) {
        if (!Array.isArray(selection))
            selection = [selection];
        return selection.map(function (r) {
            var cursor = r.isBackwards ? r.start : r.end;
            var anchor = r.isBackwards ? r.end : r.start;
            var row = anchor.row;
            var s = (row + 1) + ":" + anchor.column;
            if (anchor.row == cursor.row) {
                if (anchor.column != cursor.column)
                    s += ">" + ":" + cursor.column;
            }
            else {
                s += ">" + (cursor.row + 1) + ":" + cursor.column;
            }
            return s;
        }).reverse().join(", ");
    }
    prompt(editor, ":" + stringifySelection(editor.selection.toJSON()), {
        name: "gotoLine",
        selection: [1, Number.MAX_VALUE],
        onAccept: function (data) {
            var value = data.value;
            var _history = prompt.gotoLine._history;
            if (!_history)
                prompt.gotoLine._history = _history = [];
            if (_history.indexOf(value) != -1)
                _history.splice(_history.indexOf(value), 1);
            _history.unshift(value);
            if (_history.length > 20)
                _history.length = 20;
            var pos = editor.getCursorPosition();
            var ranges = [];
            value.replace(/^:/, "").split(/,/).map(function (str) {
                var parts = str.split(/([<>:+-]|c?\d+)|[^c\d<>:+-]+/).filter(Boolean);
                var i = 0;
                function readPosition() {
                    var c = parts[i++];
                    if (!c)
                        return;
                    if (c[0] == "c") {
                        var index = parseInt(c.slice(1)) || 0;
                        return editor.session.doc.indexToPosition(index);
                    }
                    var row = pos.row;
                    var column = 0;
                    if (/\d/.test(c)) {
                        row = parseInt(c) - 1;
                        c = parts[i++];
                    }
                    if (c == ":") {
                        c = parts[i++];
                        if (/\d/.test(c)) {
                            column = parseInt(c) || 0;
                        }
                    }
                    return { row: row, column: column };
                }
                pos = readPosition();
                var range = Range.fromPoints(pos, pos);
                if (parts[i] == ">") {
                    i++;
                    range.end = readPosition();
                }
                else if (parts[i] == "<") {
                    i++;
                    range.start = readPosition();
                }
                ranges.unshift(range);
            });
            editor.selection.fromJSON(ranges);
            var scrollTop = editor.renderer.scrollTop;
            editor.renderer.scrollSelectionIntoView(editor.selection.anchor, editor.selection.cursor, 0.5);
            editor.renderer.animateScrolling(scrollTop);
        },
        history: function () {
            var undoManager = editor.session.getUndoManager();
            if (!prompt.gotoLine._history)
                return [];
            return prompt.gotoLine._history;
        },
        getCompletions: function (cmdLine) {
            var value = cmdLine.getValue();
            var m = value.replace(/^:/, "").split(":");
            var row = Math.min(parseInt(m[0]) || 1, editor.session.getLength()) - 1;
            var line = editor.session.getLine(row);
            var current = value + "  " + line;
            return [current].concat(this.history());
        },
        $rules: {
            start: [{
                    regex: /\d+/,
                    token: "string"
                }, {
                    regex: /[:,><+\-c]/,
                    token: "keyword"
                }]
        }
    });
};
prompt.commands = function (editor, callback) {
    function normalizeName(name) {
        return (name || "").replace(/^./, function (x) {
            return x.toUpperCase(x);
        }).replace(/[a-z][A-Z]/g, function (x) {
            return x[0] + " " + x[1].toLowerCase(x);
        });
    }
    function getEditorCommandsByName(excludeCommands) {
        var commandsByName = [];
        var commandMap = {};
        editor.keyBinding.$handlers.forEach(function (handler) {
            var platform = handler.platform;
            var cbn = handler.byName;
            for (var i in cbn) {
                var key = cbn[i].bindKey;
                if (typeof key !== "string") {
                    key = key && key[platform] || "";
                }
                var commands = cbn[i];
                var description = commands.description || normalizeName(commands.name);
                if (!Array.isArray(commands))
                    commands = [commands];
                commands.forEach(function (command) {
                    if (typeof command != "string")
                        command = command.name;
                    var needle = excludeCommands.find(function (el) {
                        return el === command;
                    });
                    if (!needle) {
                        if (commandMap[command]) {
                            commandMap[command].key += "|" + key;
                        }
                        else {
                            commandMap[command] = { key: key, command: command, description: description };
                            commandsByName.push(commandMap[command]);
                        }
                    }
                });
            }
        });
        return commandsByName;
    }
    var excludeCommandsList = ["insertstring", "inserttext", "setIndentation", "paste"];
    var shortcutsArray = getEditorCommandsByName(excludeCommandsList);
    shortcutsArray = shortcutsArray.map(function (item) {
        return { value: item.description, meta: item.key, command: item.command };
    });
    prompt(editor, "", {
        name: "commands",
        selection: [0, Number.MAX_VALUE],
        maxHistoryCount: 5,
        onAccept: function (data) {
            if (data.item) {
                var commandName = data.item.command;
                this.addToHistory(data.item);
                editor.execCommand(commandName);
            }
        },
        addToHistory: function (item) {
            var history = this.history();
            history.unshift(item);
            delete item.message;
            for (var i = 1; i < history.length; i++) {
                if (history[i]["command"] == item.command) {
                    history.splice(i, 1);
                    break;
                }
            }
            if (this.maxHistoryCount > 0 && history.length > this.maxHistoryCount) {
                history.splice(history.length - 1, 1);
            }
            prompt.commands.history = history;
        },
        history: function () {
            return prompt.commands.history || [];
        },
        getPrefix: function (cmdLine) {
            var currentPos = cmdLine.getCursorPosition();
            var filterValue = cmdLine.getValue();
            return filterValue.substring(0, currentPos.column);
        },
        getCompletions: function (cmdLine) {
            function getFilteredCompletions(commands, prefix) {
                var resultCommands = JSON.parse(JSON.stringify(commands));
                var filtered = new FilteredList(resultCommands);
                return filtered.filterCompletions(resultCommands, prefix);
            }
            function getUniqueCommandList(commands, usedCommands) {
                if (!usedCommands || !usedCommands.length) {
                    return commands;
                }
                var excludeCommands = [];
                usedCommands.forEach(function (item) {
                    excludeCommands.push(item.command);
                });
                var resultCommands = [];
                commands.forEach(function (item) {
                    if (excludeCommands.indexOf(item.command) === -1) {
                        resultCommands.push(item);
                    }
                });
                return resultCommands;
            }
            var prefix = this.getPrefix(cmdLine);
            var recentlyUsedCommands = getFilteredCompletions(this.history(), prefix);
            var otherCommands = getUniqueCommandList(shortcutsArray, recentlyUsedCommands);
            otherCommands = getFilteredCompletions(otherCommands, prefix);
            if (recentlyUsedCommands.length && otherCommands.length) {
                recentlyUsedCommands[0]["message"] = " Recently used";
                otherCommands[0]["message"] = " Other commands";
            }
            var completions = recentlyUsedCommands.concat(otherCommands);
            return completions.length > 0 ? completions : [{
                    value: "No matching commands",
                    error: 1
                }];
        }
    });
};
prompt.modes = function (editor, callback) {
    var modesArray = modelist.modes;
    modesArray = modesArray.map(function (item) {
        return { value: item.caption, mode: item.name };
    });
    prompt(editor, "", {
        name: "modes",
        selection: [0, Number.MAX_VALUE],
        onAccept: function (data) {
            if (data.item) {
                var modeName = "ace/mode/" + data.item.mode;
                editor.session.setMode(modeName);
            }
        },
        getPrefix: function (cmdLine) {
            var currentPos = cmdLine.getCursorPosition();
            var filterValue = cmdLine.getValue();
            return filterValue.substring(0, currentPos.column);
        },
        getCompletions: function (cmdLine) {
            function getFilteredCompletions(modes, prefix) {
                var resultCommands = JSON.parse(JSON.stringify(modes));
                var filtered = new FilteredList(resultCommands);
                return filtered.filterCompletions(resultCommands, prefix);
            }
            var prefix = this.getPrefix(cmdLine);
            var completions = getFilteredCompletions(modesArray, prefix);
            return completions.length > 0 ? completions : [{
                    "caption": "No mode matching",
                    "value": "No mode matching",
                    "error": 1
                }];
        }
    });
};
dom.importCssString(".ace_prompt_container {\n    max-width: 600px;\n    width: 100%;\n    margin: 20px auto;\n    padding: 3px;\n    background: white;\n    border-radius: 2px;\n    box-shadow: 0px 2px 3px 0px #555;\n}", "promtp.css", false);
exports.prompt = prompt;

});                (function() {
                    ace.require(["ace/ext/prompt"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            