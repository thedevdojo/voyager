ace.define("ace/ext/searchbox.css",["require","exports","module"], function(require, exports, module){module.exports = "\n\n/* ------------------------------------------------------------------------------------------\n * Editor Search Form\n * --------------------------------------------------------------------------------------- */\n.ace_search {\n    background-color: #ddd;\n    color: #666;\n    border: 1px solid #cbcbcb;\n    border-top: 0 none;\n    overflow: hidden;\n    margin: 0;\n    padding: 4px 6px 0 4px;\n    position: absolute;\n    top: 0;\n    z-index: 99;\n    white-space: normal;\n}\n.ace_search.left {\n    border-left: 0 none;\n    border-radius: 0px 0px 5px 0px;\n    left: 0;\n}\n.ace_search.right {\n    border-radius: 0px 0px 0px 5px;\n    border-right: 0 none;\n    right: 0;\n}\n\n.ace_search_form, .ace_replace_form {\n    margin: 0 20px 4px 0;\n    overflow: hidden;\n    line-height: 1.9;\n}\n.ace_replace_form {\n    margin-right: 0;\n}\n.ace_search_form.ace_nomatch {\n    outline: 1px solid red;\n}\n\n.ace_search_field {\n    border-radius: 3px 0 0 3px;\n    background-color: white;\n    color: black;\n    border: 1px solid #cbcbcb;\n    border-right: 0 none;\n    outline: 0;\n    padding: 0;\n    font-size: inherit;\n    margin: 0;\n    line-height: inherit;\n    padding: 0 6px;\n    min-width: 17em;\n    vertical-align: top;\n    min-height: 1.8em;\n    box-sizing: content-box;\n}\n.ace_searchbtn {\n    border: 1px solid #cbcbcb;\n    line-height: inherit;\n    display: inline-block;\n    padding: 0 6px;\n    background: #fff;\n    border-right: 0 none;\n    border-left: 1px solid #dcdcdc;\n    cursor: pointer;\n    margin: 0;\n    position: relative;\n    color: #666;\n}\n.ace_searchbtn:last-child {\n    border-radius: 0 3px 3px 0;\n    border-right: 1px solid #cbcbcb;\n}\n.ace_searchbtn:disabled {\n    background: none;\n    cursor: default;\n}\n.ace_searchbtn:hover {\n    background-color: #eef1f6;\n}\n.ace_searchbtn.prev, .ace_searchbtn.next {\n     padding: 0px 0.7em\n}\n.ace_searchbtn.prev:after, .ace_searchbtn.next:after {\n     content: \"\";\n     border: solid 2px #888;\n     width: 0.5em;\n     height: 0.5em;\n     border-width:  2px 0 0 2px;\n     display:inline-block;\n     transform: rotate(-45deg);\n}\n.ace_searchbtn.next:after {\n     border-width: 0 2px 2px 0 ;\n}\n.ace_searchbtn_close {\n    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAcCAYAAABRVo5BAAAAZ0lEQVR42u2SUQrAMAhDvazn8OjZBilCkYVVxiis8H4CT0VrAJb4WHT3C5xU2a2IQZXJjiQIRMdkEoJ5Q2yMqpfDIo+XY4k6h+YXOyKqTIj5REaxloNAd0xiKmAtsTHqW8sR2W5f7gCu5nWFUpVjZwAAAABJRU5ErkJggg==) no-repeat 50% 0;\n    border-radius: 50%;\n    border: 0 none;\n    color: #656565;\n    cursor: pointer;\n    font: 16px/16px Arial;\n    padding: 0;\n    height: 14px;\n    width: 14px;\n    top: 9px;\n    right: 7px;\n    position: absolute;\n}\n.ace_searchbtn_close:hover {\n    background-color: #656565;\n    background-position: 50% 100%;\n    color: white;\n}\n\n.ace_button {\n    margin-left: 2px;\n    cursor: pointer;\n    -webkit-user-select: none;\n    -moz-user-select: none;\n    -o-user-select: none;\n    -ms-user-select: none;\n    user-select: none;\n    overflow: hidden;\n    opacity: 0.7;\n    border: 1px solid rgba(100,100,100,0.23);\n    padding: 1px;\n    box-sizing:    border-box!important;\n    color: black;\n}\n\n.ace_button:hover {\n    background-color: #eee;\n    opacity:1;\n}\n.ace_button:active {\n    background-color: #ddd;\n}\n\n.ace_button.checked {\n    border-color: #3399ff;\n    opacity:1;\n}\n\n.ace_search_options{\n    margin-bottom: 3px;\n    text-align: right;\n    -webkit-user-select: none;\n    -moz-user-select: none;\n    -o-user-select: none;\n    -ms-user-select: none;\n    user-select: none;\n    clear: both;\n}\n\n.ace_search_counter {\n    float: left;\n    font-family: arial;\n    padding: 0 8px;\n}";

});

ace.define("ace/ext/searchbox",["require","exports","module","ace/lib/dom","ace/lib/lang","ace/lib/event","ace/ext/searchbox.css","ace/keyboard/hash_handler","ace/lib/keys"], function(require, exports, module){"use strict";
var dom = require("../lib/dom");
var lang = require("../lib/lang");
var event = require("../lib/event");
var searchboxCss = require("./searchbox.css");
var HashHandler = require("../keyboard/hash_handler").HashHandler;
var keyUtil = require("../lib/keys");
var MAX_COUNT = 999;
dom.importCssString(searchboxCss, "ace_searchbox", false);
var SearchBox = function (editor, range, showReplaceForm) {
    var div = dom.createElement("div");
    dom.buildDom(["div", { class: "ace_search right" },
        ["span", { action: "hide", class: "ace_searchbtn_close" }],
        ["div", { class: "ace_search_form" },
            ["input", { class: "ace_search_field", placeholder: "Search for", spellcheck: "false" }],
            ["span", { action: "findPrev", class: "ace_searchbtn prev" }, "\u200b"],
            ["span", { action: "findNext", class: "ace_searchbtn next" }, "\u200b"],
            ["span", { action: "findAll", class: "ace_searchbtn", title: "Alt-Enter" }, "All"]
        ],
        ["div", { class: "ace_replace_form" },
            ["input", { class: "ace_search_field", placeholder: "Replace with", spellcheck: "false" }],
            ["span", { action: "replaceAndFindNext", class: "ace_searchbtn" }, "Replace"],
            ["span", { action: "replaceAll", class: "ace_searchbtn" }, "All"]
        ],
        ["div", { class: "ace_search_options" },
            ["span", { action: "toggleReplace", class: "ace_button", title: "Toggle Replace mode",
                    style: "float:left;margin-top:-2px;padding:0 5px;" }, "+"],
            ["span", { class: "ace_search_counter" }],
            ["span", { action: "toggleRegexpMode", class: "ace_button", title: "RegExp Search" }, ".*"],
            ["span", { action: "toggleCaseSensitive", class: "ace_button", title: "CaseSensitive Search" }, "Aa"],
            ["span", { action: "toggleWholeWords", class: "ace_button", title: "Whole Word Search" }, "\\b"],
            ["span", { action: "searchInSelection", class: "ace_button", title: "Search In Selection" }, "S"]
        ]
    ], div);
    this.element = div.firstChild;
    this.setSession = this.setSession.bind(this);
    this.$init();
    this.setEditor(editor);
    dom.importCssString(searchboxCss, "ace_searchbox", editor.container);
};
(function () {
    this.setEditor = function (editor) {
        editor.searchBox = this;
        editor.renderer.scroller.appendChild(this.element);
        this.editor = editor;
    };
    this.setSession = function (e) {
        this.searchRange = null;
        this.$syncOptions(true);
    };
    this.$initElements = function (sb) {
        this.searchBox = sb.querySelector(".ace_search_form");
        this.replaceBox = sb.querySelector(".ace_replace_form");
        this.searchOption = sb.querySelector("[action=searchInSelection]");
        this.replaceOption = sb.querySelector("[action=toggleReplace]");
        this.regExpOption = sb.querySelector("[action=toggleRegexpMode]");
        this.caseSensitiveOption = sb.querySelector("[action=toggleCaseSensitive]");
        this.wholeWordOption = sb.querySelector("[action=toggleWholeWords]");
        this.searchInput = this.searchBox.querySelector(".ace_search_field");
        this.replaceInput = this.replaceBox.querySelector(".ace_search_field");
        this.searchCounter = sb.querySelector(".ace_search_counter");
    };
    this.$init = function () {
        var sb = this.element;
        this.$initElements(sb);
        var _this = this;
        event.addListener(sb, "mousedown", function (e) {
            setTimeout(function () {
                _this.activeInput.focus();
            }, 0);
            event.stopPropagation(e);
        });
        event.addListener(sb, "click", function (e) {
            var t = e.target || e.srcElement;
            var action = t.getAttribute("action");
            if (action && _this[action])
                _this[action]();
            else if (_this.$searchBarKb.commands[action])
                _this.$searchBarKb.commands[action].exec(_this);
            event.stopPropagation(e);
        });
        event.addCommandKeyListener(sb, function (e, hashId, keyCode) {
            var keyString = keyUtil.keyCodeToString(keyCode);
            var command = _this.$searchBarKb.findKeyCommand(hashId, keyString);
            if (command && command.exec) {
                command.exec(_this);
                event.stopEvent(e);
            }
        });
        this.$onChange = lang.delayedCall(function () {
            _this.find(false, false);
        });
        event.addListener(this.searchInput, "input", function () {
            _this.$onChange.schedule(20);
        });
        event.addListener(this.searchInput, "focus", function () {
            _this.activeInput = _this.searchInput;
            _this.searchInput.value && _this.highlight();
        });
        event.addListener(this.replaceInput, "focus", function () {
            _this.activeInput = _this.replaceInput;
            _this.searchInput.value && _this.highlight();
        });
    };
    this.$closeSearchBarKb = new HashHandler([{
            bindKey: "Esc",
            name: "closeSearchBar",
            exec: function (editor) {
                editor.searchBox.hide();
            }
        }]);
    this.$searchBarKb = new HashHandler();
    this.$searchBarKb.bindKeys({
        "Ctrl-f|Command-f": function (sb) {
            var isReplace = sb.isReplace = !sb.isReplace;
            sb.replaceBox.style.display = isReplace ? "" : "none";
            sb.replaceOption.checked = false;
            sb.$syncOptions();
            sb.searchInput.focus();
        },
        "Ctrl-H|Command-Option-F": function (sb) {
            if (sb.editor.getReadOnly())
                return;
            sb.replaceOption.checked = true;
            sb.$syncOptions();
            sb.replaceInput.focus();
        },
        "Ctrl-G|Command-G": function (sb) {
            sb.findNext();
        },
        "Ctrl-Shift-G|Command-Shift-G": function (sb) {
            sb.findPrev();
        },
        "esc": function (sb) {
            setTimeout(function () { sb.hide(); });
        },
        "Return": function (sb) {
            if (sb.activeInput == sb.replaceInput)
                sb.replace();
            sb.findNext();
        },
        "Shift-Return": function (sb) {
            if (sb.activeInput == sb.replaceInput)
                sb.replace();
            sb.findPrev();
        },
        "Alt-Return": function (sb) {
            if (sb.activeInput == sb.replaceInput)
                sb.replaceAll();
            sb.findAll();
        },
        "Tab": function (sb) {
            (sb.activeInput == sb.replaceInput ? sb.searchInput : sb.replaceInput).focus();
        }
    });
    this.$searchBarKb.addCommands([{
            name: "toggleRegexpMode",
            bindKey: { win: "Alt-R|Alt-/", mac: "Ctrl-Alt-R|Ctrl-Alt-/" },
            exec: function (sb) {
                sb.regExpOption.checked = !sb.regExpOption.checked;
                sb.$syncOptions();
            }
        }, {
            name: "toggleCaseSensitive",
            bindKey: { win: "Alt-C|Alt-I", mac: "Ctrl-Alt-R|Ctrl-Alt-I" },
            exec: function (sb) {
                sb.caseSensitiveOption.checked = !sb.caseSensitiveOption.checked;
                sb.$syncOptions();
            }
        }, {
            name: "toggleWholeWords",
            bindKey: { win: "Alt-B|Alt-W", mac: "Ctrl-Alt-B|Ctrl-Alt-W" },
            exec: function (sb) {
                sb.wholeWordOption.checked = !sb.wholeWordOption.checked;
                sb.$syncOptions();
            }
        }, {
            name: "toggleReplace",
            exec: function (sb) {
                sb.replaceOption.checked = !sb.replaceOption.checked;
                sb.$syncOptions();
            }
        }, {
            name: "searchInSelection",
            exec: function (sb) {
                sb.searchOption.checked = !sb.searchRange;
                sb.setSearchRange(sb.searchOption.checked && sb.editor.getSelectionRange());
                sb.$syncOptions();
            }
        }]);
    this.setSearchRange = function (range) {
        this.searchRange = range;
        if (range) {
            this.searchRangeMarker = this.editor.session.addMarker(range, "ace_active-line");
        }
        else if (this.searchRangeMarker) {
            this.editor.session.removeMarker(this.searchRangeMarker);
            this.searchRangeMarker = null;
        }
    };
    this.$syncOptions = function (preventScroll) {
        dom.setCssClass(this.replaceOption, "checked", this.searchRange);
        dom.setCssClass(this.searchOption, "checked", this.searchOption.checked);
        this.replaceOption.textContent = this.replaceOption.checked ? "-" : "+";
        dom.setCssClass(this.regExpOption, "checked", this.regExpOption.checked);
        dom.setCssClass(this.wholeWordOption, "checked", this.wholeWordOption.checked);
        dom.setCssClass(this.caseSensitiveOption, "checked", this.caseSensitiveOption.checked);
        var readOnly = this.editor.getReadOnly();
        this.replaceOption.style.display = readOnly ? "none" : "";
        this.replaceBox.style.display = this.replaceOption.checked && !readOnly ? "" : "none";
        this.find(false, false, preventScroll);
    };
    this.highlight = function (re) {
        this.editor.session.highlight(re || this.editor.$search.$options.re);
        this.editor.renderer.updateBackMarkers();
    };
    this.find = function (skipCurrent, backwards, preventScroll) {
        var range = this.editor.find(this.searchInput.value, {
            skipCurrent: skipCurrent,
            backwards: backwards,
            wrap: true,
            regExp: this.regExpOption.checked,
            caseSensitive: this.caseSensitiveOption.checked,
            wholeWord: this.wholeWordOption.checked,
            preventScroll: preventScroll,
            range: this.searchRange
        });
        var noMatch = !range && this.searchInput.value;
        dom.setCssClass(this.searchBox, "ace_nomatch", noMatch);
        this.editor._emit("findSearchBox", { match: !noMatch });
        this.highlight();
        this.updateCounter();
    };
    this.updateCounter = function () {
        var editor = this.editor;
        var regex = editor.$search.$options.re;
        var all = 0;
        var before = 0;
        if (regex) {
            var value = this.searchRange
                ? editor.session.getTextRange(this.searchRange)
                : editor.getValue();
            var offset = editor.session.doc.positionToIndex(editor.selection.anchor);
            if (this.searchRange)
                offset -= editor.session.doc.positionToIndex(this.searchRange.start);
            var last = regex.lastIndex = 0;
            var m;
            while ((m = regex.exec(value))) {
                all++;
                last = m.index;
                if (last <= offset)
                    before++;
                if (all > MAX_COUNT)
                    break;
                if (!m[0]) {
                    regex.lastIndex = last += 1;
                    if (last >= value.length)
                        break;
                }
            }
        }
        this.searchCounter.textContent = before + " of " + (all > MAX_COUNT ? MAX_COUNT + "+" : all);
    };
    this.findNext = function () {
        this.find(true, false);
    };
    this.findPrev = function () {
        this.find(true, true);
    };
    this.findAll = function () {
        var range = this.editor.findAll(this.searchInput.value, {
            regExp: this.regExpOption.checked,
            caseSensitive: this.caseSensitiveOption.checked,
            wholeWord: this.wholeWordOption.checked
        });
        var noMatch = !range && this.searchInput.value;
        dom.setCssClass(this.searchBox, "ace_nomatch", noMatch);
        this.editor._emit("findSearchBox", { match: !noMatch });
        this.highlight();
        this.hide();
    };
    this.replace = function () {
        if (!this.editor.getReadOnly())
            this.editor.replace(this.replaceInput.value);
    };
    this.replaceAndFindNext = function () {
        if (!this.editor.getReadOnly()) {
            this.editor.replace(this.replaceInput.value);
            this.findNext();
        }
    };
    this.replaceAll = function () {
        if (!this.editor.getReadOnly())
            this.editor.replaceAll(this.replaceInput.value);
    };
    this.hide = function () {
        this.active = false;
        this.setSearchRange(null);
        this.editor.off("changeSession", this.setSession);
        this.element.style.display = "none";
        this.editor.keyBinding.removeKeyboardHandler(this.$closeSearchBarKb);
        this.editor.focus();
    };
    this.show = function (value, isReplace) {
        this.active = true;
        this.editor.on("changeSession", this.setSession);
        this.element.style.display = "";
        this.replaceOption.checked = isReplace;
        if (value)
            this.searchInput.value = value;
        this.searchInput.focus();
        this.searchInput.select();
        this.editor.keyBinding.addKeyboardHandler(this.$closeSearchBarKb);
        this.$syncOptions(true);
    };
    this.isFocused = function () {
        var el = document.activeElement;
        return el == this.searchInput || el == this.replaceInput;
    };
}).call(SearchBox.prototype);
exports.SearchBox = SearchBox;
exports.Search = function (editor, isReplace) {
    var sb = editor.searchBox || new SearchBox(editor);
    sb.show(editor.session.getTextRange(), isReplace);
};

});                (function() {
                    ace.require(["ace/ext/searchbox"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            