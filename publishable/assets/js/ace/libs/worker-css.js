"no use strict";
!(function(window) {
if (typeof window.window != "undefined" && window.document)
    return;
if (window.require && window.define)
    return;

if (!window.console) {
    window.console = function() {
        var msgs = Array.prototype.slice.call(arguments, 0);
        postMessage({type: "log", data: msgs});
    };
    window.console.error =
    window.console.warn = 
    window.console.log =
    window.console.trace = window.console;
}
window.window = window;
window.ace = window;

window.onerror = function(message, file, line, col, err) {
    postMessage({type: "error", data: {
        message: message,
        data: err && err.data,
        file: file,
        line: line, 
        col: col,
        stack: err && err.stack
    }});
};

window.normalizeModule = function(parentId, moduleName) {
    // normalize plugin requires
    if (moduleName.indexOf("!") !== -1) {
        var chunks = moduleName.split("!");
        return window.normalizeModule(parentId, chunks[0]) + "!" + window.normalizeModule(parentId, chunks[1]);
    }
    // normalize relative requires
    if (moduleName.charAt(0) == ".") {
        var base = parentId.split("/").slice(0, -1).join("/");
        moduleName = (base ? base + "/" : "") + moduleName;
        
        while (moduleName.indexOf(".") !== -1 && previous != moduleName) {
            var previous = moduleName;
            moduleName = moduleName.replace(/^\.\//, "").replace(/\/\.\//, "/").replace(/[^\/]+\/\.\.\//, "");
        }
    }
    
    return moduleName;
};

window.require = function require(parentId, id) {
    if (!id) {
        id = parentId;
        parentId = null;
    }
    if (!id.charAt)
        throw new Error("worker.js require() accepts only (parentId, id) as arguments");

    id = window.normalizeModule(parentId, id);

    var module = window.require.modules[id];
    if (module) {
        if (!module.initialized) {
            module.initialized = true;
            module.exports = module.factory().exports;
        }
        return module.exports;
    }
   
    if (!window.require.tlns)
        return console.log("unable to load " + id);
    
    var path = resolveModuleId(id, window.require.tlns);
    if (path.slice(-3) != ".js") path += ".js";
    
    window.require.id = id;
    window.require.modules[id] = {}; // prevent infinite loop on broken modules
    importScripts(path);
    return window.require(parentId, id);
};
function resolveModuleId(id, paths) {
    var testPath = id, tail = "";
    while (testPath) {
        var alias = paths[testPath];
        if (typeof alias == "string") {
            return alias + tail;
        } else if (alias) {
            return  alias.location.replace(/\/*$/, "/") + (tail || alias.main || alias.name);
        } else if (alias === false) {
            return "";
        }
        var i = testPath.lastIndexOf("/");
        if (i === -1) break;
        tail = testPath.substr(i) + tail;
        testPath = testPath.slice(0, i);
    }
    return id;
}
window.require.modules = {};
window.require.tlns = {};

window.define = function(id, deps, factory) {
    if (arguments.length == 2) {
        factory = deps;
        if (typeof id != "string") {
            deps = id;
            id = window.require.id;
        }
    } else if (arguments.length == 1) {
        factory = id;
        deps = [];
        id = window.require.id;
    }
    
    if (typeof factory != "function") {
        window.require.modules[id] = {
            exports: factory,
            initialized: true
        };
        return;
    }

    if (!deps.length)
        // If there is no dependencies, we inject "require", "exports" and
        // "module" as dependencies, to provide CommonJS compatibility.
        deps = ["require", "exports", "module"];

    var req = function(childId) {
        return window.require(id, childId);
    };

    window.require.modules[id] = {
        exports: {},
        factory: function() {
            var module = this;
            var returnExports = factory.apply(this, deps.slice(0, factory.length).map(function(dep) {
                switch (dep) {
                    // Because "require", "exports" and "module" aren't actual
                    // dependencies, we must handle them seperately.
                    case "require": return req;
                    case "exports": return module.exports;
                    case "module":  return module;
                    // But for all other dependencies, we can just go ahead and
                    // require them.
                    default:        return req(dep);
                }
            }));
            if (returnExports)
                module.exports = returnExports;
            return module;
        }
    };
};
window.define.amd = {};
window.require.tlns = {};
window.initBaseUrls  = function initBaseUrls(topLevelNamespaces) {
    for (var i in topLevelNamespaces)
        this.require.tlns[i] = topLevelNamespaces[i];
};

window.initSender = function initSender() {

    var EventEmitter = window.require("ace/lib/event_emitter").EventEmitter;
    var oop = window.require("ace/lib/oop");
    
    var Sender = function() {};
    
    (function() {
        
        oop.implement(this, EventEmitter);
                
        this.callback = function(data, callbackId) {
            postMessage({
                type: "call",
                id: callbackId,
                data: data
            });
        };
    
        this.emit = function(name, data) {
            postMessage({
                type: "event",
                name: name,
                data: data
            });
        };
        
    }).call(Sender.prototype);
    
    return new Sender();
};

var main = window.main = null;
var sender = window.sender = null;

window.onmessage = function(e) {
    var msg = e.data;
    if (msg.event && sender) {
        sender._signal(msg.event, msg.data);
    }
    else if (msg.command) {
        if (main[msg.command])
            main[msg.command].apply(main, msg.args);
        else if (window[msg.command])
            window[msg.command].apply(window, msg.args);
        else
            throw new Error("Unknown command:" + msg.command);
    }
    else if (msg.init) {
        window.initBaseUrls(msg.tlns);
        sender = window.sender = window.initSender();
        var clazz = this.require(msg.module)[msg.classname];
        main = window.main = new clazz(sender);
    }
};
})(this);

ace.define("ace/lib/oop",[], function(require, exports, module){"use strict";
exports.inherits = function (ctor, superCtor) {
    ctor.super_ = superCtor;
    ctor.prototype = Object.create(superCtor.prototype, {
        constructor: {
            value: ctor,
            enumerable: false,
            writable: true,
            configurable: true
        }
    });
};
exports.mixin = function (obj, mixin) {
    for (var key in mixin) {
        obj[key] = mixin[key];
    }
    return obj;
};
exports.implement = function (proto, mixin) {
    exports.mixin(proto, mixin);
};

});

ace.define("ace/lib/lang",[], function(require, exports, module){"use strict";
exports.last = function (a) {
    return a[a.length - 1];
};
exports.stringReverse = function (string) {
    return string.split("").reverse().join("");
};
exports.stringRepeat = function (string, count) {
    var result = '';
    while (count > 0) {
        if (count & 1)
            result += string;
        if (count >>= 1)
            string += string;
    }
    return result;
};
var trimBeginRegexp = /^\s\s*/;
var trimEndRegexp = /\s\s*$/;
exports.stringTrimLeft = function (string) {
    return string.replace(trimBeginRegexp, '');
};
exports.stringTrimRight = function (string) {
    return string.replace(trimEndRegexp, '');
};
exports.copyObject = function (obj) {
    var copy = {};
    for (var key in obj) {
        copy[key] = obj[key];
    }
    return copy;
};
exports.copyArray = function (array) {
    var copy = [];
    for (var i = 0, l = array.length; i < l; i++) {
        if (array[i] && typeof array[i] == "object")
            copy[i] = this.copyObject(array[i]);
        else
            copy[i] = array[i];
    }
    return copy;
};
exports.deepCopy = function deepCopy(obj) {
    if (typeof obj !== "object" || !obj)
        return obj;
    var copy;
    if (Array.isArray(obj)) {
        copy = [];
        for (var key = 0; key < obj.length; key++) {
            copy[key] = deepCopy(obj[key]);
        }
        return copy;
    }
    if (Object.prototype.toString.call(obj) !== "[object Object]")
        return obj;
    copy = {};
    for (var key in obj)
        copy[key] = deepCopy(obj[key]);
    return copy;
};
exports.arrayToMap = function (arr) {
    var map = {};
    for (var i = 0; i < arr.length; i++) {
        map[arr[i]] = 1;
    }
    return map;
};
exports.createMap = function (props) {
    var map = Object.create(null);
    for (var i in props) {
        map[i] = props[i];
    }
    return map;
};
exports.arrayRemove = function (array, value) {
    for (var i = 0; i <= array.length; i++) {
        if (value === array[i]) {
            array.splice(i, 1);
        }
    }
};
exports.escapeRegExp = function (str) {
    return str.replace(/([.*+?^${}()|[\]\/\\])/g, '\\$1');
};
exports.escapeHTML = function (str) {
    return ("" + str).replace(/&/g, "&#38;").replace(/"/g, "&#34;").replace(/'/g, "&#39;").replace(/</g, "&#60;");
};
exports.getMatchOffsets = function (string, regExp) {
    var matches = [];
    string.replace(regExp, function (str) {
        matches.push({
            offset: arguments[arguments.length - 2],
            length: str.length
        });
    });
    return matches;
};
exports.deferredCall = function (fcn) {
    var timer = null;
    var callback = function () {
        timer = null;
        fcn();
    };
    var deferred = function (timeout) {
        deferred.cancel();
        timer = setTimeout(callback, timeout || 0);
        return deferred;
    };
    deferred.schedule = deferred;
    deferred.call = function () {
        this.cancel();
        fcn();
        return deferred;
    };
    deferred.cancel = function () {
        clearTimeout(timer);
        timer = null;
        return deferred;
    };
    deferred.isPending = function () {
        return timer;
    };
    return deferred;
};
exports.delayedCall = function (fcn, defaultTimeout) {
    var timer = null;
    var callback = function () {
        timer = null;
        fcn();
    };
    var _self = function (timeout) {
        if (timer == null)
            timer = setTimeout(callback, timeout || defaultTimeout);
    };
    _self.delay = function (timeout) {
        timer && clearTimeout(timer);
        timer = setTimeout(callback, timeout || defaultTimeout);
    };
    _self.schedule = _self;
    _self.call = function () {
        this.cancel();
        fcn();
    };
    _self.cancel = function () {
        timer && clearTimeout(timer);
        timer = null;
    };
    _self.isPending = function () {
        return timer;
    };
    return _self;
};

});

ace.define("ace/apply_delta",[], function(require, exports, module){"use strict";
function throwDeltaError(delta, errorText) {
    console.log("Invalid Delta:", delta);
    throw "Invalid Delta: " + errorText;
}
function positionInDocument(docLines, position) {
    return position.row >= 0 && position.row < docLines.length &&
        position.column >= 0 && position.column <= docLines[position.row].length;
}
function validateDelta(docLines, delta) {
    if (delta.action != "insert" && delta.action != "remove")
        throwDeltaError(delta, "delta.action must be 'insert' or 'remove'");
    if (!(delta.lines instanceof Array))
        throwDeltaError(delta, "delta.lines must be an Array");
    if (!delta.start || !delta.end)
        throwDeltaError(delta, "delta.start/end must be an present");
    var start = delta.start;
    if (!positionInDocument(docLines, delta.start))
        throwDeltaError(delta, "delta.start must be contained in document");
    var end = delta.end;
    if (delta.action == "remove" && !positionInDocument(docLines, end))
        throwDeltaError(delta, "delta.end must contained in document for 'remove' actions");
    var numRangeRows = end.row - start.row;
    var numRangeLastLineChars = (end.column - (numRangeRows == 0 ? start.column : 0));
    if (numRangeRows != delta.lines.length - 1 || delta.lines[numRangeRows].length != numRangeLastLineChars)
        throwDeltaError(delta, "delta.range must match delta lines");
}
exports.applyDelta = function (docLines, delta, doNotValidate) {
    var row = delta.start.row;
    var startColumn = delta.start.column;
    var line = docLines[row] || "";
    switch (delta.action) {
        case "insert":
            var lines = delta.lines;
            if (lines.length === 1) {
                docLines[row] = line.substring(0, startColumn) + delta.lines[0] + line.substring(startColumn);
            }
            else {
                var args = [row, 1].concat(delta.lines);
                docLines.splice.apply(docLines, args);
                docLines[row] = line.substring(0, startColumn) + docLines[row];
                docLines[row + delta.lines.length - 1] += line.substring(startColumn);
            }
            break;
        case "remove":
            var endColumn = delta.end.column;
            var endRow = delta.end.row;
            if (row === endRow) {
                docLines[row] = line.substring(0, startColumn) + line.substring(endColumn);
            }
            else {
                docLines.splice(row, endRow - row + 1, line.substring(0, startColumn) + docLines[endRow].substring(endColumn));
            }
            break;
    }
};

});

ace.define("ace/lib/event_emitter",[], function(require, exports, module){"use strict";
var EventEmitter = {};
var stopPropagation = function () { this.propagationStopped = true; };
var preventDefault = function () { this.defaultPrevented = true; };
EventEmitter._emit =
    EventEmitter._dispatchEvent = function (eventName, e) {
        this._eventRegistry || (this._eventRegistry = {});
        this._defaultHandlers || (this._defaultHandlers = {});
        var listeners = this._eventRegistry[eventName] || [];
        var defaultHandler = this._defaultHandlers[eventName];
        if (!listeners.length && !defaultHandler)
            return;
        if (typeof e != "object" || !e)
            e = {};
        if (!e.type)
            e.type = eventName;
        if (!e.stopPropagation)
            e.stopPropagation = stopPropagation;
        if (!e.preventDefault)
            e.preventDefault = preventDefault;
        listeners = listeners.slice();
        for (var i = 0; i < listeners.length; i++) {
            listeners[i](e, this);
            if (e.propagationStopped)
                break;
        }
        if (defaultHandler && !e.defaultPrevented)
            return defaultHandler(e, this);
    };
EventEmitter._signal = function (eventName, e) {
    var listeners = (this._eventRegistry || {})[eventName];
    if (!listeners)
        return;
    listeners = listeners.slice();
    for (var i = 0; i < listeners.length; i++)
        listeners[i](e, this);
};
EventEmitter.once = function (eventName, callback) {
    var _self = this;
    this.on(eventName, function newCallback() {
        _self.off(eventName, newCallback);
        callback.apply(null, arguments);
    });
    if (!callback) {
        return new Promise(function (resolve) {
            callback = resolve;
        });
    }
};
EventEmitter.setDefaultHandler = function (eventName, callback) {
    var handlers = this._defaultHandlers;
    if (!handlers)
        handlers = this._defaultHandlers = { _disabled_: {} };
    if (handlers[eventName]) {
        var old = handlers[eventName];
        var disabled = handlers._disabled_[eventName];
        if (!disabled)
            handlers._disabled_[eventName] = disabled = [];
        disabled.push(old);
        var i = disabled.indexOf(callback);
        if (i != -1)
            disabled.splice(i, 1);
    }
    handlers[eventName] = callback;
};
EventEmitter.removeDefaultHandler = function (eventName, callback) {
    var handlers = this._defaultHandlers;
    if (!handlers)
        return;
    var disabled = handlers._disabled_[eventName];
    if (handlers[eventName] == callback) {
        if (disabled)
            this.setDefaultHandler(eventName, disabled.pop());
    }
    else if (disabled) {
        var i = disabled.indexOf(callback);
        if (i != -1)
            disabled.splice(i, 1);
    }
};
EventEmitter.on =
    EventEmitter.addEventListener = function (eventName, callback, capturing) {
        this._eventRegistry = this._eventRegistry || {};
        var listeners = this._eventRegistry[eventName];
        if (!listeners)
            listeners = this._eventRegistry[eventName] = [];
        if (listeners.indexOf(callback) == -1)
            listeners[capturing ? "unshift" : "push"](callback);
        return callback;
    };
EventEmitter.off =
    EventEmitter.removeListener =
        EventEmitter.removeEventListener = function (eventName, callback) {
            this._eventRegistry = this._eventRegistry || {};
            var listeners = this._eventRegistry[eventName];
            if (!listeners)
                return;
            var index = listeners.indexOf(callback);
            if (index !== -1)
                listeners.splice(index, 1);
        };
EventEmitter.removeAllListeners = function (eventName) {
    if (!eventName)
        this._eventRegistry = this._defaultHandlers = undefined;
    if (this._eventRegistry)
        this._eventRegistry[eventName] = undefined;
    if (this._defaultHandlers)
        this._defaultHandlers[eventName] = undefined;
};
exports.EventEmitter = EventEmitter;

});

ace.define("ace/range",[], function(require, exports, module){"use strict";
var comparePoints = function (p1, p2) {
    return p1.row - p2.row || p1.column - p2.column;
};
var Range = function (startRow, startColumn, endRow, endColumn) {
    this.start = {
        row: startRow,
        column: startColumn
    };
    this.end = {
        row: endRow,
        column: endColumn
    };
};
(function () {
    this.isEqual = function (range) {
        return this.start.row === range.start.row &&
            this.end.row === range.end.row &&
            this.start.column === range.start.column &&
            this.end.column === range.end.column;
    };
    this.toString = function () {
        return ("Range: [" + this.start.row + "/" + this.start.column +
            "] -> [" + this.end.row + "/" + this.end.column + "]");
    };
    this.contains = function (row, column) {
        return this.compare(row, column) == 0;
    };
    this.compareRange = function (range) {
        var cmp, end = range.end, start = range.start;
        cmp = this.compare(end.row, end.column);
        if (cmp == 1) {
            cmp = this.compare(start.row, start.column);
            if (cmp == 1) {
                return 2;
            }
            else if (cmp == 0) {
                return 1;
            }
            else {
                return 0;
            }
        }
        else if (cmp == -1) {
            return -2;
        }
        else {
            cmp = this.compare(start.row, start.column);
            if (cmp == -1) {
                return -1;
            }
            else if (cmp == 1) {
                return 42;
            }
            else {
                return 0;
            }
        }
    };
    this.comparePoint = function (p) {
        return this.compare(p.row, p.column);
    };
    this.containsRange = function (range) {
        return this.comparePoint(range.start) == 0 && this.comparePoint(range.end) == 0;
    };
    this.intersects = function (range) {
        var cmp = this.compareRange(range);
        return (cmp == -1 || cmp == 0 || cmp == 1);
    };
    this.isEnd = function (row, column) {
        return this.end.row == row && this.end.column == column;
    };
    this.isStart = function (row, column) {
        return this.start.row == row && this.start.column == column;
    };
    this.setStart = function (row, column) {
        if (typeof row == "object") {
            this.start.column = row.column;
            this.start.row = row.row;
        }
        else {
            this.start.row = row;
            this.start.column = column;
        }
    };
    this.setEnd = function (row, column) {
        if (typeof row == "object") {
            this.end.column = row.column;
            this.end.row = row.row;
        }
        else {
            this.end.row = row;
            this.end.column = column;
        }
    };
    this.inside = function (row, column) {
        if (this.compare(row, column) == 0) {
            if (this.isEnd(row, column) || this.isStart(row, column)) {
                return false;
            }
            else {
                return true;
            }
        }
        return false;
    };
    this.insideStart = function (row, column) {
        if (this.compare(row, column) == 0) {
            if (this.isEnd(row, column)) {
                return false;
            }
            else {
                return true;
            }
        }
        return false;
    };
    this.insideEnd = function (row, column) {
        if (this.compare(row, column) == 0) {
            if (this.isStart(row, column)) {
                return false;
            }
            else {
                return true;
            }
        }
        return false;
    };
    this.compare = function (row, column) {
        if (!this.isMultiLine()) {
            if (row === this.start.row) {
                return column < this.start.column ? -1 : (column > this.end.column ? 1 : 0);
            }
        }
        if (row < this.start.row)
            return -1;
        if (row > this.end.row)
            return 1;
        if (this.start.row === row)
            return column >= this.start.column ? 0 : -1;
        if (this.end.row === row)
            return column <= this.end.column ? 0 : 1;
        return 0;
    };
    this.compareStart = function (row, column) {
        if (this.start.row == row && this.start.column == column) {
            return -1;
        }
        else {
            return this.compare(row, column);
        }
    };
    this.compareEnd = function (row, column) {
        if (this.end.row == row && this.end.column == column) {
            return 1;
        }
        else {
            return this.compare(row, column);
        }
    };
    this.compareInside = function (row, column) {
        if (this.end.row == row && this.end.column == column) {
            return 1;
        }
        else if (this.start.row == row && this.start.column == column) {
            return -1;
        }
        else {
            return this.compare(row, column);
        }
    };
    this.clipRows = function (firstRow, lastRow) {
        if (this.end.row > lastRow)
            var end = { row: lastRow + 1, column: 0 };
        else if (this.end.row < firstRow)
            var end = { row: firstRow, column: 0 };
        if (this.start.row > lastRow)
            var start = { row: lastRow + 1, column: 0 };
        else if (this.start.row < firstRow)
            var start = { row: firstRow, column: 0 };
        return Range.fromPoints(start || this.start, end || this.end);
    };
    this.extend = function (row, column) {
        var cmp = this.compare(row, column);
        if (cmp == 0)
            return this;
        else if (cmp == -1)
            var start = { row: row, column: column };
        else
            var end = { row: row, column: column };
        return Range.fromPoints(start || this.start, end || this.end);
    };
    this.isEmpty = function () {
        return (this.start.row === this.end.row && this.start.column === this.end.column);
    };
    this.isMultiLine = function () {
        return (this.start.row !== this.end.row);
    };
    this.clone = function () {
        return Range.fromPoints(this.start, this.end);
    };
    this.collapseRows = function () {
        if (this.end.column == 0)
            return new Range(this.start.row, 0, Math.max(this.start.row, this.end.row - 1), 0);
        else
            return new Range(this.start.row, 0, this.end.row, 0);
    };
    this.toScreenRange = function (session) {
        var screenPosStart = session.documentToScreenPosition(this.start);
        var screenPosEnd = session.documentToScreenPosition(this.end);
        return new Range(screenPosStart.row, screenPosStart.column, screenPosEnd.row, screenPosEnd.column);
    };
    this.moveBy = function (row, column) {
        this.start.row += row;
        this.start.column += column;
        this.end.row += row;
        this.end.column += column;
    };
}).call(Range.prototype);
Range.fromPoints = function (start, end) {
    return new Range(start.row, start.column, end.row, end.column);
};
Range.comparePoints = comparePoints;
Range.comparePoints = function (p1, p2) {
    return p1.row - p2.row || p1.column - p2.column;
};
exports.Range = Range;

});

ace.define("ace/anchor",[], function(require, exports, module){"use strict";
var oop = require("./lib/oop");
var EventEmitter = require("./lib/event_emitter").EventEmitter;
var Anchor = exports.Anchor = function (doc, row, column) {
    this.$onChange = this.onChange.bind(this);
    this.attach(doc);
    if (typeof column == "undefined")
        this.setPosition(row.row, row.column);
    else
        this.setPosition(row, column);
};
(function () {
    oop.implement(this, EventEmitter);
    this.getPosition = function () {
        return this.$clipPositionToDocument(this.row, this.column);
    };
    this.getDocument = function () {
        return this.document;
    };
    this.$insertRight = false;
    this.onChange = function (delta) {
        if (delta.start.row == delta.end.row && delta.start.row != this.row)
            return;
        if (delta.start.row > this.row)
            return;
        var point = $getTransformedPoint(delta, { row: this.row, column: this.column }, this.$insertRight);
        this.setPosition(point.row, point.column, true);
    };
    function $pointsInOrder(point1, point2, equalPointsInOrder) {
        var bColIsAfter = equalPointsInOrder ? point1.column <= point2.column : point1.column < point2.column;
        return (point1.row < point2.row) || (point1.row == point2.row && bColIsAfter);
    }
    function $getTransformedPoint(delta, point, moveIfEqual) {
        var deltaIsInsert = delta.action == "insert";
        var deltaRowShift = (deltaIsInsert ? 1 : -1) * (delta.end.row - delta.start.row);
        var deltaColShift = (deltaIsInsert ? 1 : -1) * (delta.end.column - delta.start.column);
        var deltaStart = delta.start;
        var deltaEnd = deltaIsInsert ? deltaStart : delta.end; // Collapse insert range.
        if ($pointsInOrder(point, deltaStart, moveIfEqual)) {
            return {
                row: point.row,
                column: point.column
            };
        }
        if ($pointsInOrder(deltaEnd, point, !moveIfEqual)) {
            return {
                row: point.row + deltaRowShift,
                column: point.column + (point.row == deltaEnd.row ? deltaColShift : 0)
            };
        }
        return {
            row: deltaStart.row,
            column: deltaStart.column
        };
    }
    this.setPosition = function (row, column, noClip) {
        var pos;
        if (noClip) {
            pos = {
                row: row,
                column: column
            };
        }
        else {
            pos = this.$clipPositionToDocument(row, column);
        }
        if (this.row == pos.row && this.column == pos.column)
            return;
        var old = {
            row: this.row,
            column: this.column
        };
        this.row = pos.row;
        this.column = pos.column;
        this._signal("change", {
            old: old,
            value: pos
        });
    };
    this.detach = function () {
        this.document.off("change", this.$onChange);
    };
    this.attach = function (doc) {
        this.document = doc || this.document;
        this.document.on("change", this.$onChange);
    };
    this.$clipPositionToDocument = function (row, column) {
        var pos = {};
        if (row >= this.document.getLength()) {
            pos.row = Math.max(0, this.document.getLength() - 1);
            pos.column = this.document.getLine(pos.row).length;
        }
        else if (row < 0) {
            pos.row = 0;
            pos.column = 0;
        }
        else {
            pos.row = row;
            pos.column = Math.min(this.document.getLine(pos.row).length, Math.max(0, column));
        }
        if (column < 0)
            pos.column = 0;
        return pos;
    };
}).call(Anchor.prototype);

});

ace.define("ace/document",[], function(require, exports, module){"use strict";
var oop = require("./lib/oop");
var applyDelta = require("./apply_delta").applyDelta;
var EventEmitter = require("./lib/event_emitter").EventEmitter;
var Range = require("./range").Range;
var Anchor = require("./anchor").Anchor;
var Document = function (textOrLines) {
    this.$lines = [""];
    if (textOrLines.length === 0) {
        this.$lines = [""];
    }
    else if (Array.isArray(textOrLines)) {
        this.insertMergedLines({ row: 0, column: 0 }, textOrLines);
    }
    else {
        this.insert({ row: 0, column: 0 }, textOrLines);
    }
};
(function () {
    oop.implement(this, EventEmitter);
    this.setValue = function (text) {
        var len = this.getLength() - 1;
        this.remove(new Range(0, 0, len, this.getLine(len).length));
        this.insert({ row: 0, column: 0 }, text || "");
    };
    this.getValue = function () {
        return this.getAllLines().join(this.getNewLineCharacter());
    };
    this.createAnchor = function (row, column) {
        return new Anchor(this, row, column);
    };
    if ("aaa".split(/a/).length === 0) {
        this.$split = function (text) {
            return text.replace(/\r\n|\r/g, "\n").split("\n");
        };
    }
    else {
        this.$split = function (text) {
            return text.split(/\r\n|\r|\n/);
        };
    }
    this.$detectNewLine = function (text) {
        var match = text.match(/^.*?(\r\n|\r|\n)/m);
        this.$autoNewLine = match ? match[1] : "\n";
        this._signal("changeNewLineMode");
    };
    this.getNewLineCharacter = function () {
        switch (this.$newLineMode) {
            case "windows":
                return "\r\n";
            case "unix":
                return "\n";
            default:
                return this.$autoNewLine || "\n";
        }
    };
    this.$autoNewLine = "";
    this.$newLineMode = "auto";
    this.setNewLineMode = function (newLineMode) {
        if (this.$newLineMode === newLineMode)
            return;
        this.$newLineMode = newLineMode;
        this._signal("changeNewLineMode");
    };
    this.getNewLineMode = function () {
        return this.$newLineMode;
    };
    this.isNewLine = function (text) {
        return (text == "\r\n" || text == "\r" || text == "\n");
    };
    this.getLine = function (row) {
        return this.$lines[row] || "";
    };
    this.getLines = function (firstRow, lastRow) {
        return this.$lines.slice(firstRow, lastRow + 1);
    };
    this.getAllLines = function () {
        return this.getLines(0, this.getLength());
    };
    this.getLength = function () {
        return this.$lines.length;
    };
    this.getTextRange = function (range) {
        return this.getLinesForRange(range).join(this.getNewLineCharacter());
    };
    this.getLinesForRange = function (range) {
        var lines;
        if (range.start.row === range.end.row) {
            lines = [this.getLine(range.start.row).substring(range.start.column, range.end.column)];
        }
        else {
            lines = this.getLines(range.start.row, range.end.row);
            lines[0] = (lines[0] || "").substring(range.start.column);
            var l = lines.length - 1;
            if (range.end.row - range.start.row == l)
                lines[l] = lines[l].substring(0, range.end.column);
        }
        return lines;
    };
    this.insertLines = function (row, lines) {
        console.warn("Use of document.insertLines is deprecated. Use the insertFullLines method instead.");
        return this.insertFullLines(row, lines);
    };
    this.removeLines = function (firstRow, lastRow) {
        console.warn("Use of document.removeLines is deprecated. Use the removeFullLines method instead.");
        return this.removeFullLines(firstRow, lastRow);
    };
    this.insertNewLine = function (position) {
        console.warn("Use of document.insertNewLine is deprecated. Use insertMergedLines(position, ['', '']) instead.");
        return this.insertMergedLines(position, ["", ""]);
    };
    this.insert = function (position, text) {
        if (this.getLength() <= 1)
            this.$detectNewLine(text);
        return this.insertMergedLines(position, this.$split(text));
    };
    this.insertInLine = function (position, text) {
        var start = this.clippedPos(position.row, position.column);
        var end = this.pos(position.row, position.column + text.length);
        this.applyDelta({
            start: start,
            end: end,
            action: "insert",
            lines: [text]
        }, true);
        return this.clonePos(end);
    };
    this.clippedPos = function (row, column) {
        var length = this.getLength();
        if (row === undefined) {
            row = length;
        }
        else if (row < 0) {
            row = 0;
        }
        else if (row >= length) {
            row = length - 1;
            column = undefined;
        }
        var line = this.getLine(row);
        if (column == undefined)
            column = line.length;
        column = Math.min(Math.max(column, 0), line.length);
        return { row: row, column: column };
    };
    this.clonePos = function (pos) {
        return { row: pos.row, column: pos.column };
    };
    this.pos = function (row, column) {
        return { row: row, column: column };
    };
    this.$clipPosition = function (position) {
        var length = this.getLength();
        if (position.row >= length) {
            position.row = Math.max(0, length - 1);
            position.column = this.getLine(length - 1).length;
        }
        else {
            position.row = Math.max(0, position.row);
            position.column = Math.min(Math.max(position.column, 0), this.getLine(position.row).length);
        }
        return position;
    };
    this.insertFullLines = function (row, lines) {
        row = Math.min(Math.max(row, 0), this.getLength());
        var column = 0;
        if (row < this.getLength()) {
            lines = lines.concat([""]);
            column = 0;
        }
        else {
            lines = [""].concat(lines);
            row--;
            column = this.$lines[row].length;
        }
        this.insertMergedLines({ row: row, column: column }, lines);
    };
    this.insertMergedLines = function (position, lines) {
        var start = this.clippedPos(position.row, position.column);
        var end = {
            row: start.row + lines.length - 1,
            column: (lines.length == 1 ? start.column : 0) + lines[lines.length - 1].length
        };
        this.applyDelta({
            start: start,
            end: end,
            action: "insert",
            lines: lines
        });
        return this.clonePos(end);
    };
    this.remove = function (range) {
        var start = this.clippedPos(range.start.row, range.start.column);
        var end = this.clippedPos(range.end.row, range.end.column);
        this.applyDelta({
            start: start,
            end: end,
            action: "remove",
            lines: this.getLinesForRange({ start: start, end: end })
        });
        return this.clonePos(start);
    };
    this.removeInLine = function (row, startColumn, endColumn) {
        var start = this.clippedPos(row, startColumn);
        var end = this.clippedPos(row, endColumn);
        this.applyDelta({
            start: start,
            end: end,
            action: "remove",
            lines: this.getLinesForRange({ start: start, end: end })
        }, true);
        return this.clonePos(start);
    };
    this.removeFullLines = function (firstRow, lastRow) {
        firstRow = Math.min(Math.max(0, firstRow), this.getLength() - 1);
        lastRow = Math.min(Math.max(0, lastRow), this.getLength() - 1);
        var deleteFirstNewLine = lastRow == this.getLength() - 1 && firstRow > 0;
        var deleteLastNewLine = lastRow < this.getLength() - 1;
        var startRow = (deleteFirstNewLine ? firstRow - 1 : firstRow);
        var startCol = (deleteFirstNewLine ? this.getLine(startRow).length : 0);
        var endRow = (deleteLastNewLine ? lastRow + 1 : lastRow);
        var endCol = (deleteLastNewLine ? 0 : this.getLine(endRow).length);
        var range = new Range(startRow, startCol, endRow, endCol);
        var deletedLines = this.$lines.slice(firstRow, lastRow + 1);
        this.applyDelta({
            start: range.start,
            end: range.end,
            action: "remove",
            lines: this.getLinesForRange(range)
        });
        return deletedLines;
    };
    this.removeNewLine = function (row) {
        if (row < this.getLength() - 1 && row >= 0) {
            this.applyDelta({
                start: this.pos(row, this.getLine(row).length),
                end: this.pos(row + 1, 0),
                action: "remove",
                lines: ["", ""]
            });
        }
    };
    this.replace = function (range, text) {
        if (!(range instanceof Range))
            range = Range.fromPoints(range.start, range.end);
        if (text.length === 0 && range.isEmpty())
            return range.start;
        if (text == this.getTextRange(range))
            return range.end;
        this.remove(range);
        var end;
        if (text) {
            end = this.insert(range.start, text);
        }
        else {
            end = range.start;
        }
        return end;
    };
    this.applyDeltas = function (deltas) {
        for (var i = 0; i < deltas.length; i++) {
            this.applyDelta(deltas[i]);
        }
    };
    this.revertDeltas = function (deltas) {
        for (var i = deltas.length - 1; i >= 0; i--) {
            this.revertDelta(deltas[i]);
        }
    };
    this.applyDelta = function (delta, doNotValidate) {
        var isInsert = delta.action == "insert";
        if (isInsert ? delta.lines.length <= 1 && !delta.lines[0]
            : !Range.comparePoints(delta.start, delta.end)) {
            return;
        }
        if (isInsert && delta.lines.length > 20000) {
            this.$splitAndapplyLargeDelta(delta, 20000);
        }
        else {
            applyDelta(this.$lines, delta, doNotValidate);
            this._signal("change", delta);
        }
    };
    this.$safeApplyDelta = function (delta) {
        var docLength = this.$lines.length;
        if (delta.action == "remove" && delta.start.row < docLength && delta.end.row < docLength
            || delta.action == "insert" && delta.start.row <= docLength) {
            this.applyDelta(delta);
        }
    };
    this.$splitAndapplyLargeDelta = function (delta, MAX) {
        var lines = delta.lines;
        var l = lines.length - MAX + 1;
        var row = delta.start.row;
        var column = delta.start.column;
        for (var from = 0, to = 0; from < l; from = to) {
            to += MAX - 1;
            var chunk = lines.slice(from, to);
            chunk.push("");
            this.applyDelta({
                start: this.pos(row + from, column),
                end: this.pos(row + to, column = 0),
                action: delta.action,
                lines: chunk
            }, true);
        }
        delta.lines = lines.slice(from);
        delta.start.row = row + from;
        delta.start.column = column;
        this.applyDelta(delta, true);
    };
    this.revertDelta = function (delta) {
        this.$safeApplyDelta({
            start: this.clonePos(delta.start),
            end: this.clonePos(delta.end),
            action: (delta.action == "insert" ? "remove" : "insert"),
            lines: delta.lines.slice()
        });
    };
    this.indexToPosition = function (index, startRow) {
        var lines = this.$lines || this.getAllLines();
        var newlineLength = this.getNewLineCharacter().length;
        for (var i = startRow || 0, l = lines.length; i < l; i++) {
            index -= lines[i].length + newlineLength;
            if (index < 0)
                return { row: i, column: index + lines[i].length + newlineLength };
        }
        return { row: l - 1, column: index + lines[l - 1].length + newlineLength };
    };
    this.positionToIndex = function (pos, startRow) {
        var lines = this.$lines || this.getAllLines();
        var newlineLength = this.getNewLineCharacter().length;
        var index = 0;
        var row = Math.min(pos.row, lines.length);
        for (var i = startRow || 0; i < row; ++i)
            index += lines[i].length + newlineLength;
        return index + pos.column;
    };
}).call(Document.prototype);
exports.Document = Document;

});

ace.define("ace/worker/mirror",[], function(require, exports, module) {
"use strict";

var Document = require("../document").Document;
var lang = require("../lib/lang");
    
var Mirror = exports.Mirror = function(sender) {
    this.sender = sender;
    var doc = this.doc = new Document("");
    
    var deferredUpdate = this.deferredUpdate = lang.delayedCall(this.onUpdate.bind(this));
    
    var _self = this;
    sender.on("change", function(e) {
        var data = e.data;
        if (data[0].start) {
            doc.applyDeltas(data);
        } else {
            for (var i = 0; i < data.length; i += 2) {
                var d, err; 
                if (Array.isArray(data[i+1])) {
                    d = {action: "insert", start: data[i], lines: data[i+1]};
                } else {
                    d = {action: "remove", start: data[i], end: data[i+1]};
                }
                
                if ((d.action == "insert" ? d.start : d.end).row >= doc.$lines.length) {
                    err = new Error("Invalid delta");
                    err.data = {
                        path: _self.$path,
                        linesLength: doc.$lines.length,
                        start: d.start,
                        end: d.end
                    };
                    throw err;
                }

                doc.applyDelta(d, true);
            }
        }
        if (_self.$timeout)
            return deferredUpdate.schedule(_self.$timeout);
        _self.onUpdate();
    });
};

(function() {
    
    this.$timeout = 500;
    
    this.setTimeout = function(timeout) {
        this.$timeout = timeout;
    };
    
    this.setValue = function(value) {
        this.doc.setValue(value);
        this.deferredUpdate.schedule(this.$timeout);
    };
    
    this.getValue = function(callbackId) {
        this.sender.callback(this.doc.getValue(), callbackId);
    };
    
    this.onUpdate = function() {
    };
    
    this.isPending = function() {
        return this.deferredUpdate.isPending();
    };
    
}).call(Mirror.prototype);

});

ace.define("ace/mode/css/csslint",[], function(require, exports, module) {

var CSSLint = (function(){
  var module = module || {},
      exports = exports || {};
var parserlib = (function () {
var require;
require=(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){

"use strict";

var Colors = module.exports = {
    __proto__               : null,
    aliceblue               : "#f0f8ff",
    antiquewhite            : "#faebd7",
    aqua                    : "#00ffff",
    aquamarine              : "#7fffd4",
    azure                   : "#f0ffff",
    beige                   : "#f5f5dc",
    bisque                  : "#ffe4c4",
    black                   : "#000000",
    blanchedalmond          : "#ffebcd",
    blue                    : "#0000ff",
    blueviolet              : "#8a2be2",
    brown                   : "#a52a2a",
    burlywood               : "#deb887",
    cadetblue               : "#5f9ea0",
    chartreuse              : "#7fff00",
    chocolate               : "#d2691e",
    coral                   : "#ff7f50",
    cornflowerblue          : "#6495ed",
    cornsilk                : "#fff8dc",
    crimson                 : "#dc143c",
    cyan                    : "#00ffff",
    darkblue                : "#00008b",
    darkcyan                : "#008b8b",
    darkgoldenrod           : "#b8860b",
    darkgray                : "#a9a9a9",
    darkgreen               : "#006400",
    darkgrey                : "#a9a9a9",
    darkkhaki               : "#bdb76b",
    darkmagenta             : "#8b008b",
    darkolivegreen          : "#556b2f",
    darkorange              : "#ff8c00",
    darkorchid              : "#9932cc",
    darkred                 : "#8b0000",
    darksalmon              : "#e9967a",
    darkseagreen            : "#8fbc8f",
    darkslateblue           : "#483d8b",
    darkslategray           : "#2f4f4f",
    darkslategrey           : "#2f4f4f",
    darkturquoise           : "#00ced1",
    darkviolet              : "#9400d3",
    deeppink                : "#ff1493",
    deepskyblue             : "#00bfff",
    dimgray                 : "#696969",
    dimgrey                 : "#696969",
    dodgerblue              : "#1e90ff",
    firebrick               : "#b22222",
    floralwhite             : "#fffaf0",
    forestgreen             : "#228b22",
    fuchsia                 : "#ff00ff",
    gainsboro               : "#dcdcdc",
    ghostwhite              : "#f8f8ff",
    gold                    : "#ffd700",
    goldenrod               : "#daa520",
    gray                    : "#808080",
    green                   : "#008000",
    greenyellow             : "#adff2f",
    grey                    : "#808080",
    honeydew                : "#f0fff0",
    hotpink                 : "#ff69b4",
    indianred               : "#cd5c5c",
    indigo                  : "#4b0082",
    ivory                   : "#fffff0",
    khaki                   : "#f0e68c",
    lavender                : "#e6e6fa",
    lavenderblush           : "#fff0f5",
    lawngreen               : "#7cfc00",
    lemonchiffon            : "#fffacd",
    lightblue               : "#add8e6",
    lightcoral              : "#f08080",
    lightcyan               : "#e0ffff",
    lightgoldenrodyellow    : "#fafad2",
    lightgray               : "#d3d3d3",
    lightgreen              : "#90ee90",
    lightgrey               : "#d3d3d3",
    lightpink               : "#ffb6c1",
    lightsalmon             : "#ffa07a",
    lightseagreen           : "#20b2aa",
    lightskyblue            : "#87cefa",
    lightslategray          : "#778899",
    lightslategrey          : "#778899",
    lightsteelblue          : "#b0c4de",
    lightyellow             : "#ffffe0",
    lime                    : "#00ff00",
    limegreen               : "#32cd32",
    linen                   : "#faf0e6",
    magenta                 : "#ff00ff",
    maroon                  : "#800000",
    mediumaquamarine        : "#66cdaa",
    mediumblue              : "#0000cd",
    mediumorchid            : "#ba55d3",
    mediumpurple            : "#9370db",
    mediumseagreen          : "#3cb371",
    mediumslateblue         : "#7b68ee",
    mediumspringgreen       : "#00fa9a",
    mediumturquoise         : "#48d1cc",
    mediumvioletred         : "#c71585",
    midnightblue            : "#191970",
    mintcream               : "#f5fffa",
    mistyrose               : "#ffe4e1",
    moccasin                : "#ffe4b5",
    navajowhite             : "#ffdead",
    navy                    : "#000080",
    oldlace                 : "#fdf5e6",
    olive                   : "#808000",
    olivedrab               : "#6b8e23",
    orange                  : "#ffa500",
    orangered               : "#ff4500",
    orchid                  : "#da70d6",
    palegoldenrod           : "#eee8aa",
    palegreen               : "#98fb98",
    paleturquoise           : "#afeeee",
    palevioletred           : "#db7093",
    papayawhip              : "#ffefd5",
    peachpuff               : "#ffdab9",
    peru                    : "#cd853f",
    pink                    : "#ffc0cb",
    plum                    : "#dda0dd",
    powderblue              : "#b0e0e6",
    purple                  : "#800080",
    rebeccapurple           : "#663399",
    red                     : "#ff0000",
    rosybrown               : "#bc8f8f",
    royalblue               : "#4169e1",
    saddlebrown             : "#8b4513",
    salmon                  : "#fa8072",
    sandybrown              : "#f4a460",
    seagreen                : "#2e8b57",
    seashell                : "#fff5ee",
    sienna                  : "#a0522d",
    silver                  : "#c0c0c0",
    skyblue                 : "#87ceeb",
    slateblue               : "#6a5acd",
    slategray               : "#708090",
    slategrey               : "#708090",
    snow                    : "#fffafa",
    springgreen             : "#00ff7f",
    steelblue               : "#4682b4",
    tan                     : "#d2b48c",
    teal                    : "#008080",
    thistle                 : "#d8bfd8",
    tomato                  : "#ff6347",
    turquoise               : "#40e0d0",
    violet                  : "#ee82ee",
    wheat                   : "#f5deb3",
    white                   : "#ffffff",
    whitesmoke              : "#f5f5f5",
    yellow                  : "#ffff00",
    yellowgreen             : "#9acd32",
    currentColor            : "The value of the 'color' property.",
    activeborder            : "Active window border.",
    activecaption           : "Active window caption.",
    appworkspace            : "Background color of multiple document interface.",
    background              : "Desktop background.",
    buttonface              : "The face background color for 3-D elements that appear 3-D due to one layer of surrounding border.",
    buttonhighlight         : "The color of the border facing the light source for 3-D elements that appear 3-D due to one layer of surrounding border.",
    buttonshadow            : "The color of the border away from the light source for 3-D elements that appear 3-D due to one layer of surrounding border.",
    buttontext              : "Text on push buttons.",
    captiontext             : "Text in caption, size box, and scrollbar arrow box.",
    graytext                : "Grayed (disabled) text. This color is set to #000 if the current display driver does not support a solid gray color.",
    greytext                : "Greyed (disabled) text. This color is set to #000 if the current display driver does not support a solid grey color.",
    highlight               : "Item(s) selected in a control.",
    highlighttext           : "Text of item(s) selected in a control.",
    inactiveborder          : "Inactive window border.",
    inactivecaption         : "Inactive window caption.",
    inactivecaptiontext     : "Color of text in an inactive caption.",
    infobackground          : "Background color for tooltip controls.",
    infotext                : "Text color for tooltip controls.",
    menu                    : "Menu background.",
    menutext                : "Text in menus.",
    scrollbar               : "Scroll bar gray area.",
    threeddarkshadow        : "The color of the darker (generally outer) of the two borders away from the light source for 3-D elements that appear 3-D due to two concentric layers of surrounding border.",
    threedface              : "The face background color for 3-D elements that appear 3-D due to two concentric layers of surrounding border.",
    threedhighlight         : "The color of the lighter (generally outer) of the two borders facing the light source for 3-D elements that appear 3-D due to two concentric layers of surrounding border.",
    threedlightshadow       : "The color of the darker (generally inner) of the two borders facing the light source for 3-D elements that appear 3-D due to two concentric layers of surrounding border.",
    threedshadow            : "The color of the lighter (generally inner) of the two borders away from the light source for 3-D elements that appear 3-D due to two concentric layers of surrounding border.",
    window                  : "Window background.",
    windowframe             : "Window frame.",
    windowtext              : "Text in windows."
};

},{}],2:[function(require,module,exports){
"use strict";

module.exports = Combinator;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function Combinator(text, line, col) {

    SyntaxUnit.call(this, text, line, col, Parser.COMBINATOR_TYPE);
    this.type = "unknown";
    if (/^\s+$/.test(text)) {
        this.type = "descendant";
    } else if (text === ">") {
        this.type = "child";
    } else if (text === "+") {
        this.type = "adjacent-sibling";
    } else if (text === "~") {
        this.type = "sibling";
    }

}

Combinator.prototype = new SyntaxUnit();
Combinator.prototype.constructor = Combinator;


},{"../util/SyntaxUnit":26,"./Parser":6}],3:[function(require,module,exports){
"use strict";

module.exports = Matcher;

var StringReader = require("../util/StringReader");
var SyntaxError = require("../util/SyntaxError");
function Matcher(matchFunc, toString) {
    this.match = function(expression) {
        var result;
        expression.mark();
        result = matchFunc(expression);
        if (result) {
            expression.drop();
        } else {
            expression.restore();
        }
        return result;
    };
    this.toString = typeof toString === "function" ? toString : function() {
        return toString;
    };
}
Matcher.prec = {
    MOD:    5,
    SEQ:    4,
    ANDAND: 3,
    OROR:   2,
    ALT:    1
};
Matcher.parse = function(str) {
    var reader, eat, expr, oror, andand, seq, mod, term, result;
    reader = new StringReader(str);
    eat = function(matcher) {
        var result = reader.readMatch(matcher);
        if (result === null) {
            throw new SyntaxError(
                "Expected " + matcher, reader.getLine(), reader.getCol());
        }
        return result;
    };
    expr = function() {
        var m = [ oror() ];
        while (reader.readMatch(" | ") !== null) {
            m.push(oror());
        }
        return m.length === 1 ? m[0] : Matcher.alt.apply(Matcher, m);
    };
    oror = function() {
        var m = [ andand() ];
        while (reader.readMatch(" || ") !== null) {
            m.push(andand());
        }
        return m.length === 1 ? m[0] : Matcher.oror.apply(Matcher, m);
    };
    andand = function() {
        var m = [ seq() ];
        while (reader.readMatch(" && ") !== null) {
            m.push(seq());
        }
        return m.length === 1 ? m[0] : Matcher.andand.apply(Matcher, m);
    };
    seq = function() {
        var m = [ mod() ];
        while (reader.readMatch(/^ (?![&|\]])/) !== null) {
            m.push(mod());
        }
        return m.length === 1 ? m[0] : Matcher.seq.apply(Matcher, m);
    };
    mod = function() {
        var m = term();
        if (reader.readMatch("?") !== null) {
            return m.question();
        } else if (reader.readMatch("*") !== null) {
            return m.star();
        } else if (reader.readMatch("+") !== null) {
            return m.plus();
        } else if (reader.readMatch("#") !== null) {
            return m.hash();
        } else if (reader.readMatch(/^\{\s*/) !== null) {
            var min = eat(/^\d+/);
            eat(/^\s*,\s*/);
            var max = eat(/^\d+/);
            eat(/^\s*\}/);
            return m.braces(Number(min), Number(max));
        }
        return m;
    };
    term = function() {
        if (reader.readMatch("[ ") !== null) {
            var m = expr();
            eat(" ]");
            return m;
        }
        return Matcher.fromType(eat(/^[^ ?*+#{]+/));
    };
    result = expr();
    if (!reader.eof()) {
        throw new SyntaxError(
            "Expected end of string", reader.getLine(), reader.getCol());
    }
    return result;
};
Matcher.cast = function(m) {
    if (m instanceof Matcher) {
        return m;
    }
    return Matcher.parse(m);
};
Matcher.fromType = function(type) {
    var ValidationTypes = require("./ValidationTypes");
    return new Matcher(function(expression) {
        return expression.hasNext() && ValidationTypes.isType(expression, type);
    }, type);
};
Matcher.seq = function() {
    var ms = Array.prototype.slice.call(arguments).map(Matcher.cast);
    if (ms.length === 1) {
        return ms[0];
    }
    return new Matcher(function(expression) {
        var i, result = true;
        for (i = 0; result && i < ms.length; i++) {
            result = ms[i].match(expression);
        }
        return result;
    }, function(prec) {
        var p = Matcher.prec.SEQ;
        var s = ms.map(function(m) {
            return m.toString(p);
        }).join(" ");
        if (prec > p) {
            s = "[ " + s + " ]";
        }
        return s;
    });
};
Matcher.alt = function() {
    var ms = Array.prototype.slice.call(arguments).map(Matcher.cast);
    if (ms.length === 1) {
        return ms[0];
    }
    return new Matcher(function(expression) {
        var i, result = false;
        for (i = 0; !result && i < ms.length; i++) {
            result = ms[i].match(expression);
        }
        return result;
    }, function(prec) {
        var p = Matcher.prec.ALT;
        var s = ms.map(function(m) {
            return m.toString(p);
        }).join(" | ");
        if (prec > p) {
            s = "[ " + s + " ]";
        }
        return s;
    });
};
Matcher.many = function(required) {
    var ms = Array.prototype.slice.call(arguments, 1).reduce(function(acc, v) {
        if (v.expand) {
            var ValidationTypes = require("./ValidationTypes");
            acc.push.apply(acc, ValidationTypes.complex[v.expand].options);
        } else {
            acc.push(Matcher.cast(v));
        }
        return acc;
    }, []);

    if (required === true) {
        required = ms.map(function() {
            return true;
        });
    }

    var result = new Matcher(function(expression) {
        var seen = [], max = 0, pass = 0;
        var success = function(matchCount) {
            if (pass === 0) {
                max = Math.max(matchCount, max);
                return matchCount === ms.length;
            } else {
                return matchCount === max;
            }
        };
        var tryMatch = function(matchCount) {
            for (var i = 0; i < ms.length; i++) {
                if (seen[i]) {
                    continue;
                }
                expression.mark();
                if (ms[i].match(expression)) {
                    seen[i] = true;
                    if (tryMatch(matchCount + (required === false || required[i] ? 1 : 0))) {
                        expression.drop();
                        return true;
                    }
                    expression.restore();
                    seen[i] = false;
                } else {
                    expression.drop();
                }
            }
            return success(matchCount);
        };
        if (!tryMatch(0)) {
            pass++;
            tryMatch(0);
        }

        if (required === false) {
            return max > 0;
        }
        for (var i = 0; i < ms.length; i++) {
            if (required[i] && !seen[i]) {
                return false;
            }
        }
        return true;
    }, function(prec) {
        var p = required === false ? Matcher.prec.OROR : Matcher.prec.ANDAND;
        var s = ms.map(function(m, i) {
            if (required !== false && !required[i]) {
                return m.toString(Matcher.prec.MOD) + "?";
            }
            return m.toString(p);
        }).join(required === false ? " || " : " && ");
        if (prec > p) {
            s = "[ " + s + " ]";
        }
        return s;
    });
    result.options = ms;
    return result;
};
Matcher.andand = function() {
    var args = Array.prototype.slice.call(arguments);
    args.unshift(true);
    return Matcher.many.apply(Matcher, args);
};
Matcher.oror = function() {
    var args = Array.prototype.slice.call(arguments);
    args.unshift(false);
    return Matcher.many.apply(Matcher, args);
};
Matcher.prototype = {
    constructor: Matcher,
    match: function() {
        throw new Error("unimplemented");
    },
    toString: function() {
        throw new Error("unimplemented");
    },
    func: function() {
        return this.match.bind(this);
    },
    then: function(m) {
        return Matcher.seq(this, m);
    },
    or: function(m) {
        return Matcher.alt(this, m);
    },
    andand: function(m) {
        return Matcher.many(true, this, m);
    },
    oror: function(m) {
        return Matcher.many(false, this, m);
    },
    star: function() {
        return this.braces(0, Infinity, "*");
    },
    plus: function() {
        return this.braces(1, Infinity, "+");
    },
    question: function() {
        return this.braces(0, 1, "?");
    },
    hash: function() {
        return this.braces(1, Infinity, "#", Matcher.cast(","));
    },
    braces: function(min, max, marker, optSep) {
        var m1 = this, m2 = optSep ? optSep.then(this) : this;
        if (!marker) {
            marker = "{" + min + "," + max + "}";
        }
        return new Matcher(function(expression) {
            var result = true, i;
            for (i = 0; i < max; i++) {
                if (i > 0 && optSep) {
                    result = m2.match(expression);
                } else {
                    result = m1.match(expression);
                }
                if (!result) {
                    break;
                }
            }
            return i >= min;
        }, function() {
            return m1.toString(Matcher.prec.MOD) + marker;
        });
    }
};

},{"../util/StringReader":24,"../util/SyntaxError":25,"./ValidationTypes":21}],4:[function(require,module,exports){
"use strict";

module.exports = MediaFeature;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function MediaFeature(name, value) {

    SyntaxUnit.call(this, "(" + name + (value !== null ? ":" + value : "") + ")", name.startLine, name.startCol, Parser.MEDIA_FEATURE_TYPE);
    this.name = name;
    this.value = value;
}

MediaFeature.prototype = new SyntaxUnit();
MediaFeature.prototype.constructor = MediaFeature;


},{"../util/SyntaxUnit":26,"./Parser":6}],5:[function(require,module,exports){
"use strict";

module.exports = MediaQuery;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function MediaQuery(modifier, mediaType, features, line, col) {

    SyntaxUnit.call(this, (modifier ? modifier + " " : "") + (mediaType ? mediaType : "") + (mediaType && features.length > 0 ? " and " : "") + features.join(" and "), line, col, Parser.MEDIA_QUERY_TYPE);
    this.modifier = modifier;
    this.mediaType = mediaType;
    this.features = features;

}

MediaQuery.prototype = new SyntaxUnit();
MediaQuery.prototype.constructor = MediaQuery;


},{"../util/SyntaxUnit":26,"./Parser":6}],6:[function(require,module,exports){
"use strict";

module.exports = Parser;

var EventTarget = require("../util/EventTarget");
var SyntaxError = require("../util/SyntaxError");
var SyntaxUnit = require("../util/SyntaxUnit");

var Combinator = require("./Combinator");
var MediaFeature = require("./MediaFeature");
var MediaQuery = require("./MediaQuery");
var PropertyName = require("./PropertyName");
var PropertyValue = require("./PropertyValue");
var PropertyValuePart = require("./PropertyValuePart");
var Selector = require("./Selector");
var SelectorPart = require("./SelectorPart");
var SelectorSubPart = require("./SelectorSubPart");
var TokenStream = require("./TokenStream");
var Tokens = require("./Tokens");
var Validation = require("./Validation");
function Parser(options) {
    EventTarget.call(this);


    this.options = options || {};

    this._tokenStream = null;
}
Parser.DEFAULT_TYPE = 0;
Parser.COMBINATOR_TYPE = 1;
Parser.MEDIA_FEATURE_TYPE = 2;
Parser.MEDIA_QUERY_TYPE = 3;
Parser.PROPERTY_NAME_TYPE = 4;
Parser.PROPERTY_VALUE_TYPE = 5;
Parser.PROPERTY_VALUE_PART_TYPE = 6;
Parser.SELECTOR_TYPE = 7;
Parser.SELECTOR_PART_TYPE = 8;
Parser.SELECTOR_SUB_PART_TYPE = 9;

Parser.prototype = function() {

    var proto = new EventTarget(),  // new prototype
        prop,
        additions =  {
            __proto__: null,
            constructor: Parser,
            DEFAULT_TYPE : 0,
            COMBINATOR_TYPE : 1,
            MEDIA_FEATURE_TYPE : 2,
            MEDIA_QUERY_TYPE : 3,
            PROPERTY_NAME_TYPE : 4,
            PROPERTY_VALUE_TYPE : 5,
            PROPERTY_VALUE_PART_TYPE : 6,
            SELECTOR_TYPE : 7,
            SELECTOR_PART_TYPE : 8,
            SELECTOR_SUB_PART_TYPE : 9,

            _stylesheet: function() {

                var tokenStream = this._tokenStream,
                    count,
                    token,
                    tt;

                this.fire("startstylesheet");
                this._charset();

                this._skipCruft();
                while (tokenStream.peek() === Tokens.IMPORT_SYM) {
                    this._import();
                    this._skipCruft();
                }
                while (tokenStream.peek() === Tokens.NAMESPACE_SYM) {
                    this._namespace();
                    this._skipCruft();
                }
                tt = tokenStream.peek();
                while (tt > Tokens.EOF) {

                    try {

                        switch (tt) {
                            case Tokens.MEDIA_SYM:
                                this._media();
                                this._skipCruft();
                                break;
                            case Tokens.PAGE_SYM:
                                this._page();
                                this._skipCruft();
                                break;
                            case Tokens.FONT_FACE_SYM:
                                this._font_face();
                                this._skipCruft();
                                break;
                            case Tokens.KEYFRAMES_SYM:
                                this._keyframes();
                                this._skipCruft();
                                break;
                            case Tokens.VIEWPORT_SYM:
                                this._viewport();
                                this._skipCruft();
                                break;
                            case Tokens.DOCUMENT_SYM:
                                this._document();
                                this._skipCruft();
                                break;
                            case Tokens.SUPPORTS_SYM:
                                this._supports();
                                this._skipCruft();
                                break;
                            case Tokens.UNKNOWN_SYM:  // unknown @ rule
                                tokenStream.get();
                                if (!this.options.strict) {
                                    this.fire({
                                        type:       "error",
                                        error:      null,
                                        message:    "Unknown @ rule: " + tokenStream.LT(0).value + ".",
                                        line:       tokenStream.LT(0).startLine,
                                        col:        tokenStream.LT(0).startCol
                                    });
                                    count = 0;
                                    while (tokenStream.advance([Tokens.LBRACE, Tokens.RBRACE]) === Tokens.LBRACE) {
                                        count++;    // keep track of nesting depth
                                    }

                                    while (count) {
                                        tokenStream.advance([Tokens.RBRACE]);
                                        count--;
                                    }

                                } else {
                                    throw new SyntaxError("Unknown @ rule.", tokenStream.LT(0).startLine, tokenStream.LT(0).startCol);
                                }
                                break;
                            case Tokens.S:
                                this._readWhitespace();
                                break;
                            default:
                                if (!this._ruleset()) {
                                    switch (tt) {
                                        case Tokens.CHARSET_SYM:
                                            token = tokenStream.LT(1);
                                            this._charset(false);
                                            throw new SyntaxError("@charset not allowed here.", token.startLine, token.startCol);
                                        case Tokens.IMPORT_SYM:
                                            token = tokenStream.LT(1);
                                            this._import(false);
                                            throw new SyntaxError("@import not allowed here.", token.startLine, token.startCol);
                                        case Tokens.NAMESPACE_SYM:
                                            token = tokenStream.LT(1);
                                            this._namespace(false);
                                            throw new SyntaxError("@namespace not allowed here.", token.startLine, token.startCol);
                                        default:
                                            tokenStream.get();  // get the last token
                                            this._unexpectedToken(tokenStream.token());
                                    }

                                }
                        }
                    } catch (ex) {
                        if (ex instanceof SyntaxError && !this.options.strict) {
                            this.fire({
                                type:       "error",
                                error:      ex,
                                message:    ex.message,
                                line:       ex.line,
                                col:        ex.col
                            });
                        } else {
                            throw ex;
                        }
                    }

                    tt = tokenStream.peek();
                }

                if (tt !== Tokens.EOF) {
                    this._unexpectedToken(tokenStream.token());
                }

                this.fire("endstylesheet");
            },

            _charset: function(emit) {
                var tokenStream = this._tokenStream,
                    charset,
                    token,
                    line,
                    col;

                if (tokenStream.match(Tokens.CHARSET_SYM)) {
                    line = tokenStream.token().startLine;
                    col = tokenStream.token().startCol;

                    this._readWhitespace();
                    tokenStream.mustMatch(Tokens.STRING);

                    token = tokenStream.token();
                    charset = token.value;

                    this._readWhitespace();
                    tokenStream.mustMatch(Tokens.SEMICOLON);

                    if (emit !== false) {
                        this.fire({
                            type:   "charset",
                            charset:charset,
                            line:   line,
                            col:    col
                        });
                    }
                }
            },

            _import: function(emit) {

                var tokenStream = this._tokenStream,
                    uri,
                    importToken,
                    mediaList   = [];
                tokenStream.mustMatch(Tokens.IMPORT_SYM);
                importToken = tokenStream.token();
                this._readWhitespace();

                tokenStream.mustMatch([Tokens.STRING, Tokens.URI]);
                uri = tokenStream.token().value.replace(/^(?:url\()?["']?([^"']+?)["']?\)?$/, "$1");

                this._readWhitespace();

                mediaList = this._media_query_list();
                tokenStream.mustMatch(Tokens.SEMICOLON);
                this._readWhitespace();

                if (emit !== false) {
                    this.fire({
                        type:   "import",
                        uri:    uri,
                        media:  mediaList,
                        line:   importToken.startLine,
                        col:    importToken.startCol
                    });
                }

            },

            _namespace: function(emit) {

                var tokenStream = this._tokenStream,
                    line,
                    col,
                    prefix,
                    uri;
                tokenStream.mustMatch(Tokens.NAMESPACE_SYM);
                line = tokenStream.token().startLine;
                col = tokenStream.token().startCol;
                this._readWhitespace();
                if (tokenStream.match(Tokens.IDENT)) {
                    prefix = tokenStream.token().value;
                    this._readWhitespace();
                }

                tokenStream.mustMatch([Tokens.STRING, Tokens.URI]);
                uri = tokenStream.token().value.replace(/(?:url\()?["']([^"']+)["']\)?/, "$1");

                this._readWhitespace();
                tokenStream.mustMatch(Tokens.SEMICOLON);
                this._readWhitespace();

                if (emit !== false) {
                    this.fire({
                        type:   "namespace",
                        prefix: prefix,
                        uri:    uri,
                        line:   line,
                        col:    col
                    });
                }

            },

            _supports: function(emit) {
                var tokenStream = this._tokenStream,
                    line,
                    col;

                if (tokenStream.match(Tokens.SUPPORTS_SYM)) {
                    line = tokenStream.token().startLine;
                    col = tokenStream.token().startCol;

                    this._readWhitespace();
                    this._supports_condition();
                    this._readWhitespace();

                    tokenStream.mustMatch(Tokens.LBRACE);
                    this._readWhitespace();

                    if (emit !== false) {
                        this.fire({
                            type:   "startsupports",
                            line:   line,
                            col:    col
                        });
                    }

                    while (true) {
                        if (!this._ruleset()) {
                            break;
                        }
                    }

                    tokenStream.mustMatch(Tokens.RBRACE);
                    this._readWhitespace();

                    this.fire({
                        type:   "endsupports",
                        line:   line,
                        col:    col
                    });
                }
            },

            _supports_condition: function() {
                var tokenStream = this._tokenStream,
                    ident;

                if (tokenStream.match(Tokens.IDENT)) {
                    ident = tokenStream.token().value.toLowerCase();

                    if (ident === "not") {
                        tokenStream.mustMatch(Tokens.S);
                        this._supports_condition_in_parens();
                    } else {
                        tokenStream.unget();
                    }
                } else {
                    this._supports_condition_in_parens();
                    this._readWhitespace();

                    while (tokenStream.peek() === Tokens.IDENT) {
                        ident = tokenStream.LT(1).value.toLowerCase();
                        if (ident === "and" || ident === "or") {
                            tokenStream.mustMatch(Tokens.IDENT);
                            this._readWhitespace();
                            this._supports_condition_in_parens();
                            this._readWhitespace();
                        }
                    }
                }
            },

            _supports_condition_in_parens: function() {
                var tokenStream = this._tokenStream,
                    ident;

                if (tokenStream.match(Tokens.LPAREN)) {
                    this._readWhitespace();
                    if (tokenStream.match(Tokens.IDENT)) {
                        ident = tokenStream.token().value.toLowerCase();
                        if (ident === "not") {
                            this._readWhitespace();
                            this._supports_condition();
                            this._readWhitespace();
                            tokenStream.mustMatch(Tokens.RPAREN);
                        } else {
                            tokenStream.unget();
                            this._supports_declaration_condition(false);
                        }
                    } else {
                        this._supports_condition();
                        this._readWhitespace();
                        tokenStream.mustMatch(Tokens.RPAREN);
                    }
                } else {
                    this._supports_declaration_condition();
                }
            },

            _supports_declaration_condition: function(requireStartParen) {
                var tokenStream = this._tokenStream;

                if (requireStartParen !== false) {
                    tokenStream.mustMatch(Tokens.LPAREN);
                }
                this._readWhitespace();
                this._declaration();
                tokenStream.mustMatch(Tokens.RPAREN);
            },

            _media: function() {
                var tokenStream     = this._tokenStream,
                    line,
                    col,
                    mediaList;  // = [];
                tokenStream.mustMatch(Tokens.MEDIA_SYM);
                line = tokenStream.token().startLine;
                col = tokenStream.token().startCol;

                this._readWhitespace();

                mediaList = this._media_query_list();

                tokenStream.mustMatch(Tokens.LBRACE);
                this._readWhitespace();

                this.fire({
                    type:   "startmedia",
                    media:  mediaList,
                    line:   line,
                    col:    col
                });

                while (true) {
                    if (tokenStream.peek() === Tokens.PAGE_SYM) {
                        this._page();
                    } else if (tokenStream.peek() === Tokens.FONT_FACE_SYM) {
                        this._font_face();
                    } else if (tokenStream.peek() === Tokens.VIEWPORT_SYM) {
                        this._viewport();
                    } else if (tokenStream.peek() === Tokens.DOCUMENT_SYM) {
                        this._document();
                    } else if (tokenStream.peek() === Tokens.SUPPORTS_SYM) {
                        this._supports();
                    } else if (tokenStream.peek() === Tokens.MEDIA_SYM) {
                        this._media();
                    } else if (!this._ruleset()) {
                        break;
                    }
                }

                tokenStream.mustMatch(Tokens.RBRACE);
                this._readWhitespace();

                this.fire({
                    type:   "endmedia",
                    media:  mediaList,
                    line:   line,
                    col:    col
                });
            },
            _media_query_list: function() {
                var tokenStream = this._tokenStream,
                    mediaList   = [];


                this._readWhitespace();

                if (tokenStream.peek() === Tokens.IDENT || tokenStream.peek() === Tokens.LPAREN) {
                    mediaList.push(this._media_query());
                }

                while (tokenStream.match(Tokens.COMMA)) {
                    this._readWhitespace();
                    mediaList.push(this._media_query());
                }

                return mediaList;
            },
            _media_query: function() {
                var tokenStream = this._tokenStream,
                    type        = null,
                    ident       = null,
                    token       = null,
                    expressions = [];

                if (tokenStream.match(Tokens.IDENT)) {
                    ident = tokenStream.token().value.toLowerCase();
                    if (ident !== "only" && ident !== "not") {
                        tokenStream.unget();
                        ident = null;
                    } else {
                        token = tokenStream.token();
                    }
                }

                this._readWhitespace();

                if (tokenStream.peek() === Tokens.IDENT) {
                    type = this._media_type();
                    if (token === null) {
                        token = tokenStream.token();
                    }
                } else if (tokenStream.peek() === Tokens.LPAREN) {
                    if (token === null) {
                        token = tokenStream.LT(1);
                    }
                    expressions.push(this._media_expression());
                }

                if (type === null && expressions.length === 0) {
                    return null;
                } else {
                    this._readWhitespace();
                    while (tokenStream.match(Tokens.IDENT)) {
                        if (tokenStream.token().value.toLowerCase() !== "and") {
                            this._unexpectedToken(tokenStream.token());
                        }

                        this._readWhitespace();
                        expressions.push(this._media_expression());
                    }
                }

                return new MediaQuery(ident, type, expressions, token.startLine, token.startCol);
            },
            _media_type: function() {
                return this._media_feature();
            },
            _media_expression: function() {
                var tokenStream = this._tokenStream,
                    feature     = null,
                    token,
                    expression  = null;

                tokenStream.mustMatch(Tokens.LPAREN);
                this._readWhitespace();

                feature = this._media_feature();
                this._readWhitespace();

                if (tokenStream.match(Tokens.COLON)) {
                    this._readWhitespace();
                    token = tokenStream.LT(1);
                    expression = this._expression();
                }

                tokenStream.mustMatch(Tokens.RPAREN);
                this._readWhitespace();

                return new MediaFeature(feature, expression ? new SyntaxUnit(expression, token.startLine, token.startCol) : null);
            },
            _media_feature: function() {
                var tokenStream = this._tokenStream;

                this._readWhitespace();

                tokenStream.mustMatch(Tokens.IDENT);

                return SyntaxUnit.fromToken(tokenStream.token());
            },
            _page: function() {
                var tokenStream = this._tokenStream,
                    line,
                    col,
                    identifier  = null,
                    pseudoPage  = null;
                tokenStream.mustMatch(Tokens.PAGE_SYM);
                line = tokenStream.token().startLine;
                col = tokenStream.token().startCol;

                this._readWhitespace();

                if (tokenStream.match(Tokens.IDENT)) {
                    identifier = tokenStream.token().value;
                    if (identifier.toLowerCase() === "auto") {
                        this._unexpectedToken(tokenStream.token());
                    }
                }
                if (tokenStream.peek() === Tokens.COLON) {
                    pseudoPage = this._pseudo_page();
                }

                this._readWhitespace();

                this.fire({
                    type:   "startpage",
                    id:     identifier,
                    pseudo: pseudoPage,
                    line:   line,
                    col:    col
                });

                this._readDeclarations(true, true);

                this.fire({
                    type:   "endpage",
                    id:     identifier,
                    pseudo: pseudoPage,
                    line:   line,
                    col:    col
                });

            },
            _margin: function() {
                var tokenStream = this._tokenStream,
                    line,
                    col,
                    marginSym   = this._margin_sym();

                if (marginSym) {
                    line = tokenStream.token().startLine;
                    col = tokenStream.token().startCol;

                    this.fire({
                        type: "startpagemargin",
                        margin: marginSym,
                        line:   line,
                        col:    col
                    });

                    this._readDeclarations(true);

                    this.fire({
                        type: "endpagemargin",
                        margin: marginSym,
                        line:   line,
                        col:    col
                    });
                    return true;
                } else {
                    return false;
                }
            },
            _margin_sym: function() {

                var tokenStream = this._tokenStream;

                if (tokenStream.match([Tokens.TOPLEFTCORNER_SYM, Tokens.TOPLEFT_SYM,
                    Tokens.TOPCENTER_SYM, Tokens.TOPRIGHT_SYM, Tokens.TOPRIGHTCORNER_SYM,
                    Tokens.BOTTOMLEFTCORNER_SYM, Tokens.BOTTOMLEFT_SYM,
                    Tokens.BOTTOMCENTER_SYM, Tokens.BOTTOMRIGHT_SYM,
                    Tokens.BOTTOMRIGHTCORNER_SYM, Tokens.LEFTTOP_SYM,
                    Tokens.LEFTMIDDLE_SYM, Tokens.LEFTBOTTOM_SYM, Tokens.RIGHTTOP_SYM,
                    Tokens.RIGHTMIDDLE_SYM, Tokens.RIGHTBOTTOM_SYM])) {
                    return SyntaxUnit.fromToken(tokenStream.token());
                } else {
                    return null;
                }

            },

            _pseudo_page: function() {

                var tokenStream = this._tokenStream;

                tokenStream.mustMatch(Tokens.COLON);
                tokenStream.mustMatch(Tokens.IDENT);

                return tokenStream.token().value;
            },

            _font_face: function() {
                var tokenStream = this._tokenStream,
                    line,
                    col;
                tokenStream.mustMatch(Tokens.FONT_FACE_SYM);
                line = tokenStream.token().startLine;
                col = tokenStream.token().startCol;

                this._readWhitespace();

                this.fire({
                    type:   "startfontface",
                    line:   line,
                    col:    col
                });

                this._readDeclarations(true);

                this.fire({
                    type:   "endfontface",
                    line:   line,
                    col:    col
                });
            },

            _viewport: function() {
                var tokenStream = this._tokenStream,
                    line,
                    col;

                tokenStream.mustMatch(Tokens.VIEWPORT_SYM);
                line = tokenStream.token().startLine;
                col = tokenStream.token().startCol;

                this._readWhitespace();

                this.fire({
                    type:   "startviewport",
                    line:   line,
                    col:    col
                });

                this._readDeclarations(true);

                this.fire({
                    type:   "endviewport",
                    line:   line,
                    col:    col
                });

            },

            _document: function() {

                var tokenStream = this._tokenStream,
                    token,
                    functions = [],
                    prefix = "";

                tokenStream.mustMatch(Tokens.DOCUMENT_SYM);
                token = tokenStream.token();
                if (/^@-([^-]+)-/.test(token.value)) {
                    prefix = RegExp.$1;
                }

                this._readWhitespace();
                functions.push(this._document_function());

                while (tokenStream.match(Tokens.COMMA)) {
                    this._readWhitespace();
                    functions.push(this._document_function());
                }

                tokenStream.mustMatch(Tokens.LBRACE);
                this._readWhitespace();

                this.fire({
                    type:      "startdocument",
                    functions: functions,
                    prefix:    prefix,
                    line:      token.startLine,
                    col:       token.startCol
                });

                var ok = true;
                while (ok) {
                    switch (tokenStream.peek()) {
                        case Tokens.PAGE_SYM:
                            this._page();
                            break;
                        case Tokens.FONT_FACE_SYM:
                            this._font_face();
                            break;
                        case Tokens.VIEWPORT_SYM:
                            this._viewport();
                            break;
                        case Tokens.MEDIA_SYM:
                            this._media();
                            break;
                        case Tokens.KEYFRAMES_SYM:
                            this._keyframes();
                            break;
                        case Tokens.DOCUMENT_SYM:
                            this._document();
                            break;
                        default:
                            ok = Boolean(this._ruleset());
                    }
                }

                tokenStream.mustMatch(Tokens.RBRACE);
                token = tokenStream.token();
                this._readWhitespace();

                this.fire({
                    type:      "enddocument",
                    functions: functions,
                    prefix:    prefix,
                    line:      token.startLine,
                    col:       token.startCol
                });
            },

            _document_function: function() {

                var tokenStream = this._tokenStream,
                    value;

                if (tokenStream.match(Tokens.URI)) {
                    value = tokenStream.token().value;
                    this._readWhitespace();
                } else {
                    value = this._function();
                }

                return value;
            },

            _operator: function(inFunction) {

                var tokenStream = this._tokenStream,
                    token       = null;

                if (tokenStream.match([Tokens.SLASH, Tokens.COMMA]) ||
                    inFunction && tokenStream.match([Tokens.PLUS, Tokens.STAR, Tokens.MINUS])) {
                    token =  tokenStream.token();
                    this._readWhitespace();
                }
                return token ? PropertyValuePart.fromToken(token) : null;

            },

            _combinator: function() {

                var tokenStream = this._tokenStream,
                    value       = null,
                    token;

                if (tokenStream.match([Tokens.PLUS, Tokens.GREATER, Tokens.TILDE])) {
                    token = tokenStream.token();
                    value = new Combinator(token.value, token.startLine, token.startCol);
                    this._readWhitespace();
                }

                return value;
            },

            _unary_operator: function() {

                var tokenStream = this._tokenStream;

                if (tokenStream.match([Tokens.MINUS, Tokens.PLUS])) {
                    return tokenStream.token().value;
                } else {
                    return null;
                }
            },

            _property: function() {

                var tokenStream  = this._tokenStream,
                    value        = null,
                    hack         = null,
                    propertyName = "",
                    token,
                    line,
                    col;
                if (tokenStream.peek() === Tokens.STAR && this.options.starHack) {
                    tokenStream.get();
                    token = tokenStream.token();
                    hack = token.value;
                    line = token.startLine;
                    col = token.startCol;
                }
                if (tokenStream.peek() === Tokens.MINUS) {
                    tokenStream.get();
                    token = tokenStream.token();
                    propertyName = token.value;
                    line = token.startLine;
                    col = token.startCol;
                }

                if (tokenStream.match(Tokens.IDENT)) {
                    token = tokenStream.token();
                    propertyName += token.value;
                    if (propertyName.charAt(0) === "_" && this.options.underscoreHack) {
                        hack = "_";
                        propertyName = propertyName.substring(1);
                    }

                    value = new PropertyName(propertyName, hack, line || token.startLine, col || token.startCol);
                    this._readWhitespace();
                } else {
                    var tt = tokenStream.peek();
                    if (tt !== Tokens.EOF && tt !== Tokens.RBRACE) {
                        this._unexpectedToken(tokenStream.LT(1));
                    }
                }

                return value;
            },
            _ruleset: function() {

                var tokenStream = this._tokenStream,
                    tt,
                    selectors;
                try {
                    selectors = this._selectors_group();
                } catch (ex) {
                    if (ex instanceof SyntaxError && !this.options.strict) {
                        this.fire({
                            type:       "error",
                            error:      ex,
                            message:    ex.message,
                            line:       ex.line,
                            col:        ex.col
                        });
                        tt = tokenStream.advance([Tokens.RBRACE]);
                        if (tt === Tokens.RBRACE) {
                        } else {
                            throw ex;
                        }

                    } else {
                        throw ex;
                    }
                    return true;
                }
                if (selectors) {

                    this.fire({
                        type:       "startrule",
                        selectors:  selectors,
                        line:       selectors[0].line,
                        col:        selectors[0].col
                    });

                    this._readDeclarations(true);

                    this.fire({
                        type:       "endrule",
                        selectors:  selectors,
                        line:       selectors[0].line,
                        col:        selectors[0].col
                    });

                }

                return selectors;

            },
            _selectors_group: function() {
                var tokenStream = this._tokenStream,
                    selectors   = [],
                    selector;

                selector = this._selector();
                if (selector !== null) {

                    selectors.push(selector);
                    while (tokenStream.match(Tokens.COMMA)) {
                        this._readWhitespace();
                        selector = this._selector();
                        if (selector !== null) {
                            selectors.push(selector);
                        } else {
                            this._unexpectedToken(tokenStream.LT(1));
                        }
                    }
                }

                return selectors.length ? selectors : null;
            },
            _selector: function() {

                var tokenStream = this._tokenStream,
                    selector    = [],
                    nextSelector = null,
                    combinator  = null,
                    ws          = null;
                nextSelector = this._simple_selector_sequence();
                if (nextSelector === null) {
                    return null;
                }

                selector.push(nextSelector);

                do {
                    combinator = this._combinator();

                    if (combinator !== null) {
                        selector.push(combinator);
                        nextSelector = this._simple_selector_sequence();
                        if (nextSelector === null) {
                            this._unexpectedToken(tokenStream.LT(1));
                        } else {
                            selector.push(nextSelector);
                        }
                    } else {
                        if (this._readWhitespace()) {
                            ws = new Combinator(tokenStream.token().value, tokenStream.token().startLine, tokenStream.token().startCol);
                            combinator = this._combinator();
                            nextSelector = this._simple_selector_sequence();
                            if (nextSelector === null) {
                                if (combinator !== null) {
                                    this._unexpectedToken(tokenStream.LT(1));
                                }
                            } else {

                                if (combinator !== null) {
                                    selector.push(combinator);
                                } else {
                                    selector.push(ws);
                                }

                                selector.push(nextSelector);
                            }
                        } else {
                            break;
                        }

                    }
                } while (true);

                return new Selector(selector, selector[0].line, selector[0].col);
            },
            _simple_selector_sequence: function() {

                var tokenStream = this._tokenStream,
                    elementName = null,
                    modifiers   = [],
                    selectorText = "",
                    components  = [
                        function() {
                            return tokenStream.match(Tokens.HASH) ?
                                    new SelectorSubPart(tokenStream.token().value, "id", tokenStream.token().startLine, tokenStream.token().startCol) :
                                    null;
                        },
                        this._class,
                        this._attrib,
                        this._pseudo,
                        this._negation
                    ],
                    i           = 0,
                    len         = components.length,
                    component   = null,
                    line,
                    col;
                line = tokenStream.LT(1).startLine;
                col = tokenStream.LT(1).startCol;

                elementName = this._type_selector();
                if (!elementName) {
                    elementName = this._universal();
                }

                if (elementName !== null) {
                    selectorText += elementName;
                }

                while (true) {
                    if (tokenStream.peek() === Tokens.S) {
                        break;
                    }
                    while (i < len && component === null) {
                        component = components[i++].call(this);
                    }

                    if (component === null) {
                        if (selectorText === "") {
                            return null;
                        } else {
                            break;
                        }
                    } else {
                        i = 0;
                        modifiers.push(component);
                        selectorText += component.toString();
                        component = null;
                    }
                }


                return selectorText !== "" ?
                        new SelectorPart(elementName, modifiers, selectorText, line, col) :
                        null;
            },
            _type_selector: function() {

                var tokenStream = this._tokenStream,
                    ns          = this._namespace_prefix(),
                    elementName = this._element_name();

                if (!elementName) {
                    if (ns) {
                        tokenStream.unget();
                        if (ns.length > 1) {
                            tokenStream.unget();
                        }
                    }

                    return null;
                } else {
                    if (ns) {
                        elementName.text = ns + elementName.text;
                        elementName.col -= ns.length;
                    }
                    return elementName;
                }
            },
            _class: function() {

                var tokenStream = this._tokenStream,
                    token;

                if (tokenStream.match(Tokens.DOT)) {
                    tokenStream.mustMatch(Tokens.IDENT);
                    token = tokenStream.token();
                    return new SelectorSubPart("." + token.value, "class", token.startLine, token.startCol - 1);
                } else {
                    return null;
                }

            },
            _element_name: function() {

                var tokenStream = this._tokenStream,
                    token;

                if (tokenStream.match(Tokens.IDENT)) {
                    token = tokenStream.token();
                    return new SelectorSubPart(token.value, "elementName", token.startLine, token.startCol);

                } else {
                    return null;
                }
            },
            _namespace_prefix: function() {
                var tokenStream = this._tokenStream,
                    value       = "";
                if (tokenStream.LA(1) === Tokens.PIPE || tokenStream.LA(2) === Tokens.PIPE) {

                    if (tokenStream.match([Tokens.IDENT, Tokens.STAR])) {
                        value += tokenStream.token().value;
                    }

                    tokenStream.mustMatch(Tokens.PIPE);
                    value += "|";

                }

                return value.length ? value : null;
            },
            _universal: function() {
                var tokenStream = this._tokenStream,
                    value       = "",
                    ns;

                ns = this._namespace_prefix();
                if (ns) {
                    value += ns;
                }

                if (tokenStream.match(Tokens.STAR)) {
                    value += "*";
                }

                return value.length ? value : null;

            },
            _attrib: function() {

                var tokenStream = this._tokenStream,
                    value       = null,
                    ns,
                    token;

                if (tokenStream.match(Tokens.LBRACKET)) {
                    token = tokenStream.token();
                    value = token.value;
                    value += this._readWhitespace();

                    ns = this._namespace_prefix();

                    if (ns) {
                        value += ns;
                    }

                    tokenStream.mustMatch(Tokens.IDENT);
                    value += tokenStream.token().value;
                    value += this._readWhitespace();

                    if (tokenStream.match([Tokens.PREFIXMATCH, Tokens.SUFFIXMATCH, Tokens.SUBSTRINGMATCH,
                        Tokens.EQUALS, Tokens.INCLUDES, Tokens.DASHMATCH])) {

                        value += tokenStream.token().value;
                        value += this._readWhitespace();

                        tokenStream.mustMatch([Tokens.IDENT, Tokens.STRING]);
                        value += tokenStream.token().value;
                        value += this._readWhitespace();
                    }

                    tokenStream.mustMatch(Tokens.RBRACKET);

                    return new SelectorSubPart(value + "]", "attribute", token.startLine, token.startCol);
                } else {
                    return null;
                }
            },
            _pseudo: function() {

                var tokenStream = this._tokenStream,
                    pseudo      = null,
                    colons      = ":",
                    line,
                    col;

                if (tokenStream.match(Tokens.COLON)) {

                    if (tokenStream.match(Tokens.COLON)) {
                        colons += ":";
                    }

                    if (tokenStream.match(Tokens.IDENT)) {
                        pseudo = tokenStream.token().value;
                        line = tokenStream.token().startLine;
                        col = tokenStream.token().startCol - colons.length;
                    } else if (tokenStream.peek() === Tokens.FUNCTION) {
                        line = tokenStream.LT(1).startLine;
                        col = tokenStream.LT(1).startCol - colons.length;
                        pseudo = this._functional_pseudo();
                    }

                    if (pseudo) {
                        pseudo = new SelectorSubPart(colons + pseudo, "pseudo", line, col);
                    } else {
                        var startLine = tokenStream.LT(1).startLine,
                            startCol  = tokenStream.LT(0).startCol;
                        throw new SyntaxError("Expected a `FUNCTION` or `IDENT` after colon at line " + startLine + ", col " + startCol + ".", startLine, startCol);
                    }
                }

                return pseudo;
            },
            _functional_pseudo: function() {

                var tokenStream = this._tokenStream,
                    value = null;

                if (tokenStream.match(Tokens.FUNCTION)) {
                    value = tokenStream.token().value;
                    value += this._readWhitespace();
                    value += this._expression();
                    tokenStream.mustMatch(Tokens.RPAREN);
                    value += ")";
                }

                return value;
            },
            _expression: function() {

                var tokenStream = this._tokenStream,
                    value       = "";

                while (tokenStream.match([Tokens.PLUS, Tokens.MINUS, Tokens.DIMENSION,
                    Tokens.NUMBER, Tokens.STRING, Tokens.IDENT, Tokens.LENGTH,
                    Tokens.FREQ, Tokens.ANGLE, Tokens.TIME,
                    Tokens.RESOLUTION, Tokens.SLASH])) {

                    value += tokenStream.token().value;
                    value += this._readWhitespace();
                }

                return value.length ? value : null;

            },
            _negation: function() {

                var tokenStream = this._tokenStream,
                    line,
                    col,
                    value       = "",
                    arg,
                    subpart     = null;

                if (tokenStream.match(Tokens.NOT)) {
                    value = tokenStream.token().value;
                    line = tokenStream.token().startLine;
                    col = tokenStream.token().startCol;
                    value += this._readWhitespace();
                    arg = this._negation_arg();
                    value += arg;
                    value += this._readWhitespace();
                    tokenStream.match(Tokens.RPAREN);
                    value += tokenStream.token().value;

                    subpart = new SelectorSubPart(value, "not", line, col);
                    subpart.args.push(arg);
                }

                return subpart;
            },
            _negation_arg: function() {

                var tokenStream = this._tokenStream,
                    args        = [
                        this._type_selector,
                        this._universal,
                        function() {
                            return tokenStream.match(Tokens.HASH) ?
                                    new SelectorSubPart(tokenStream.token().value, "id", tokenStream.token().startLine, tokenStream.token().startCol) :
                                    null;
                        },
                        this._class,
                        this._attrib,
                        this._pseudo
                    ],
                    arg         = null,
                    i           = 0,
                    len         = args.length,
                    line,
                    col,
                    part;

                line = tokenStream.LT(1).startLine;
                col = tokenStream.LT(1).startCol;

                while (i < len && arg === null) {

                    arg = args[i].call(this);
                    i++;
                }
                if (arg === null) {
                    this._unexpectedToken(tokenStream.LT(1));
                }
                if (arg.type === "elementName") {
                    part = new SelectorPart(arg, [], arg.toString(), line, col);
                } else {
                    part = new SelectorPart(null, [arg], arg.toString(), line, col);
                }

                return part;
            },

            _declaration: function() {

                var tokenStream  = this._tokenStream,
                    property     = null,
                    expr         = null,
                    prio         = null,
                    invalid      = null,
                    propertyName = "";

                property = this._property();
                if (property !== null) {

                    tokenStream.mustMatch(Tokens.COLON);
                    this._readWhitespace();

                    expr = this._expr();
                    if (!expr || expr.length === 0) {
                        this._unexpectedToken(tokenStream.LT(1));
                    }

                    prio = this._prio();
                    propertyName = property.toString();
                    if (this.options.starHack && property.hack === "*" ||
                            this.options.underscoreHack && property.hack === "_") {

                        propertyName = property.text;
                    }

                    try {
                        this._validateProperty(propertyName, expr);
                    } catch (ex) {
                        invalid = ex;
                    }

                    this.fire({
                        type:       "property",
                        property:   property,
                        value:      expr,
                        important:  prio,
                        line:       property.line,
                        col:        property.col,
                        invalid:    invalid
                    });

                    return true;
                } else {
                    return false;
                }
            },

            _prio: function() {

                var tokenStream = this._tokenStream,
                    result      = tokenStream.match(Tokens.IMPORTANT_SYM);

                this._readWhitespace();
                return result;
            },

            _expr: function(inFunction) {

                var values      = [],
                    value       = null,
                    operator    = null;

                value = this._term(inFunction);
                if (value !== null) {

                    values.push(value);

                    do {
                        operator = this._operator(inFunction);
                        if (operator) {
                            values.push(operator);
                        } /*else {
                            values.push(new PropertyValue(valueParts, valueParts[0].line, valueParts[0].col));
                            valueParts = [];
                        }*/

                        value = this._term(inFunction);

                        if (value === null) {
                            break;
                        } else {
                            values.push(value);
                        }
                    } while (true);
                }

                return values.length > 0 ? new PropertyValue(values, values[0].line, values[0].col) : null;
            },

            _term: function(inFunction) {

                var tokenStream = this._tokenStream,
                    unary       = null,
                    value       = null,
                    endChar     = null,
                    part        = null,
                    token,
                    line,
                    col;
                unary = this._unary_operator();
                if (unary !== null) {
                    line = tokenStream.token().startLine;
                    col = tokenStream.token().startCol;
                }
                if (tokenStream.peek() === Tokens.IE_FUNCTION && this.options.ieFilters) {

                    value = this._ie_function();
                    if (unary === null) {
                        line = tokenStream.token().startLine;
                        col = tokenStream.token().startCol;
                    }
                } else if (inFunction && tokenStream.match([Tokens.LPAREN, Tokens.LBRACE, Tokens.LBRACKET])) {

                    token = tokenStream.token();
                    endChar = token.endChar;
                    value = token.value + this._expr(inFunction).text;
                    if (unary === null) {
                        line = tokenStream.token().startLine;
                        col = tokenStream.token().startCol;
                    }
                    tokenStream.mustMatch(Tokens.type(endChar));
                    value += endChar;
                    this._readWhitespace();
                } else if (tokenStream.match([Tokens.NUMBER, Tokens.PERCENTAGE, Tokens.LENGTH,
                    Tokens.ANGLE, Tokens.TIME,
                    Tokens.FREQ, Tokens.STRING, Tokens.IDENT, Tokens.URI, Tokens.UNICODE_RANGE])) {

                    value = tokenStream.token().value;
                    if (unary === null) {
                        line = tokenStream.token().startLine;
                        col = tokenStream.token().startCol;
                        part = PropertyValuePart.fromToken(tokenStream.token());
                    }
                    this._readWhitespace();
                } else {
                    token = this._hexcolor();
                    if (token === null) {
                        if (unary === null) {
                            line = tokenStream.LT(1).startLine;
                            col = tokenStream.LT(1).startCol;
                        }
                        if (value === null) {
                            if (tokenStream.LA(3) === Tokens.EQUALS && this.options.ieFilters) {
                                value = this._ie_function();
                            } else {
                                value = this._function();
                            }
                        }

                    } else {
                        value = token.value;
                        if (unary === null) {
                            line = token.startLine;
                            col = token.startCol;
                        }
                    }

                }

                return part !== null ? part : value !== null ?
                        new PropertyValuePart(unary !== null ? unary + value : value, line, col) :
                        null;

            },

            _function: function() {

                var tokenStream = this._tokenStream,
                    functionText = null,
                    expr        = null,
                    lt;

                if (tokenStream.match(Tokens.FUNCTION)) {
                    functionText = tokenStream.token().value;
                    this._readWhitespace();
                    expr = this._expr(true);
                    functionText += expr;
                    if (this.options.ieFilters && tokenStream.peek() === Tokens.EQUALS) {
                        do {

                            if (this._readWhitespace()) {
                                functionText += tokenStream.token().value;
                            }
                            if (tokenStream.LA(0) === Tokens.COMMA) {
                                functionText += tokenStream.token().value;
                            }

                            tokenStream.match(Tokens.IDENT);
                            functionText += tokenStream.token().value;

                            tokenStream.match(Tokens.EQUALS);
                            functionText += tokenStream.token().value;
                            lt = tokenStream.peek();
                            while (lt !== Tokens.COMMA && lt !== Tokens.S && lt !== Tokens.RPAREN) {
                                tokenStream.get();
                                functionText += tokenStream.token().value;
                                lt = tokenStream.peek();
                            }
                        } while (tokenStream.match([Tokens.COMMA, Tokens.S]));
                    }

                    tokenStream.match(Tokens.RPAREN);
                    functionText += ")";
                    this._readWhitespace();
                }

                return functionText;
            },

            _ie_function: function() {

                var tokenStream = this._tokenStream,
                    functionText = null,
                    lt;
                if (tokenStream.match([Tokens.IE_FUNCTION, Tokens.FUNCTION])) {
                    functionText = tokenStream.token().value;

                    do {

                        if (this._readWhitespace()) {
                            functionText += tokenStream.token().value;
                        }
                        if (tokenStream.LA(0) === Tokens.COMMA) {
                            functionText += tokenStream.token().value;
                        }

                        tokenStream.match(Tokens.IDENT);
                        functionText += tokenStream.token().value;

                        tokenStream.match(Tokens.EQUALS);
                        functionText += tokenStream.token().value;
                        lt = tokenStream.peek();
                        while (lt !== Tokens.COMMA && lt !== Tokens.S && lt !== Tokens.RPAREN) {
                            tokenStream.get();
                            functionText += tokenStream.token().value;
                            lt = tokenStream.peek();
                        }
                    } while (tokenStream.match([Tokens.COMMA, Tokens.S]));

                    tokenStream.match(Tokens.RPAREN);
                    functionText += ")";
                    this._readWhitespace();
                }

                return functionText;
            },

            _hexcolor: function() {

                var tokenStream = this._tokenStream,
                    token = null,
                    color;

                if (tokenStream.match(Tokens.HASH)) {

                    token = tokenStream.token();
                    color = token.value;
                    if (!/#[a-f0-9]{3,6}/i.test(color)) {
                        throw new SyntaxError("Expected a hex color but found '" + color + "' at line " + token.startLine + ", col " + token.startCol + ".", token.startLine, token.startCol);
                    }
                    this._readWhitespace();
                }

                return token;
            },

            _keyframes: function() {
                var tokenStream = this._tokenStream,
                    token,
                    tt,
                    name,
                    prefix = "";

                tokenStream.mustMatch(Tokens.KEYFRAMES_SYM);
                token = tokenStream.token();
                if (/^@-([^-]+)-/.test(token.value)) {
                    prefix = RegExp.$1;
                }

                this._readWhitespace();
                name = this._keyframe_name();

                this._readWhitespace();
                tokenStream.mustMatch(Tokens.LBRACE);

                this.fire({
                    type:   "startkeyframes",
                    name:   name,
                    prefix: prefix,
                    line:   token.startLine,
                    col:    token.startCol
                });

                this._readWhitespace();
                tt = tokenStream.peek();
                while (tt === Tokens.IDENT || tt === Tokens.PERCENTAGE) {
                    this._keyframe_rule();
                    this._readWhitespace();
                    tt = tokenStream.peek();
                }

                this.fire({
                    type:   "endkeyframes",
                    name:   name,
                    prefix: prefix,
                    line:   token.startLine,
                    col:    token.startCol
                });

                this._readWhitespace();
                tokenStream.mustMatch(Tokens.RBRACE);
                this._readWhitespace();

            },

            _keyframe_name: function() {
                var tokenStream = this._tokenStream;

                tokenStream.mustMatch([Tokens.IDENT, Tokens.STRING]);
                return SyntaxUnit.fromToken(tokenStream.token());
            },

            _keyframe_rule: function() {
                var keyList = this._key_list();

                this.fire({
                    type:   "startkeyframerule",
                    keys:   keyList,
                    line:   keyList[0].line,
                    col:    keyList[0].col
                });

                this._readDeclarations(true);

                this.fire({
                    type:   "endkeyframerule",
                    keys:   keyList,
                    line:   keyList[0].line,
                    col:    keyList[0].col
                });

            },

            _key_list: function() {
                var tokenStream = this._tokenStream,
                    keyList = [];
                keyList.push(this._key());

                this._readWhitespace();

                while (tokenStream.match(Tokens.COMMA)) {
                    this._readWhitespace();
                    keyList.push(this._key());
                    this._readWhitespace();
                }

                return keyList;
            },

            _key: function() {

                var tokenStream = this._tokenStream,
                    token;

                if (tokenStream.match(Tokens.PERCENTAGE)) {
                    return SyntaxUnit.fromToken(tokenStream.token());
                } else if (tokenStream.match(Tokens.IDENT)) {
                    token = tokenStream.token();

                    if (/from|to/i.test(token.value)) {
                        return SyntaxUnit.fromToken(token);
                    }

                    tokenStream.unget();
                }
                this._unexpectedToken(tokenStream.LT(1));
            },
            _skipCruft: function() {
                while (this._tokenStream.match([Tokens.S, Tokens.CDO, Tokens.CDC])) {
                }
            },
            _readDeclarations: function(checkStart, readMargins) {
                var tokenStream = this._tokenStream,
                    tt;


                this._readWhitespace();

                if (checkStart) {
                    tokenStream.mustMatch(Tokens.LBRACE);
                }

                this._readWhitespace();

                try {

                    while (true) {

                        if (tokenStream.match(Tokens.SEMICOLON) || (readMargins && this._margin())) {
                        } else if (this._declaration()) {
                            if (!tokenStream.match(Tokens.SEMICOLON)) {
                                break;
                            }
                        } else {
                            break;
                        }
                        this._readWhitespace();
                    }

                    tokenStream.mustMatch(Tokens.RBRACE);
                    this._readWhitespace();

                } catch (ex) {
                    if (ex instanceof SyntaxError && !this.options.strict) {
                        this.fire({
                            type:       "error",
                            error:      ex,
                            message:    ex.message,
                            line:       ex.line,
                            col:        ex.col
                        });
                        tt = tokenStream.advance([Tokens.SEMICOLON, Tokens.RBRACE]);
                        if (tt === Tokens.SEMICOLON) {
                            this._readDeclarations(false, readMargins);
                        } else if (tt !== Tokens.EOF && tt !== Tokens.RBRACE) {
                            throw ex;
                        }

                    } else {
                        throw ex;
                    }
                }

            },
            _readWhitespace: function() {

                var tokenStream = this._tokenStream,
                    ws = "";

                while (tokenStream.match(Tokens.S)) {
                    ws += tokenStream.token().value;
                }

                return ws;
            },
            _unexpectedToken: function(token) {
                throw new SyntaxError("Unexpected token '" + token.value + "' at line " + token.startLine + ", col " + token.startCol + ".", token.startLine, token.startCol);
            },
            _verifyEnd: function() {
                if (this._tokenStream.LA(1) !== Tokens.EOF) {
                    this._unexpectedToken(this._tokenStream.LT(1));
                }
            },
            _validateProperty: function(property, value) {
                Validation.validate(property, value);
            },

            parse: function(input) {
                this._tokenStream = new TokenStream(input, Tokens);
                this._stylesheet();
            },

            parseStyleSheet: function(input) {
                return this.parse(input);
            },

            parseMediaQuery: function(input) {
                this._tokenStream = new TokenStream(input, Tokens);
                var result = this._media_query();
                this._verifyEnd();
                return result;
            },
            parsePropertyValue: function(input) {

                this._tokenStream = new TokenStream(input, Tokens);
                this._readWhitespace();

                var result = this._expr();
                this._readWhitespace();
                this._verifyEnd();
                return result;
            },
            parseRule: function(input) {
                this._tokenStream = new TokenStream(input, Tokens);
                this._readWhitespace();

                var result = this._ruleset();
                this._readWhitespace();
                this._verifyEnd();
                return result;
            },
            parseSelector: function(input) {

                this._tokenStream = new TokenStream(input, Tokens);
                this._readWhitespace();

                var result = this._selector();
                this._readWhitespace();
                this._verifyEnd();
                return result;
            },
            parseStyleAttribute: function(input) {
                input += "}";   // for error recovery in _readDeclarations()
                this._tokenStream = new TokenStream(input, Tokens);
                this._readDeclarations();
            }
        };
    for (prop in additions) {
        if (Object.prototype.hasOwnProperty.call(additions, prop)) {
            proto[prop] = additions[prop];
        }
    }

    return proto;
}();

},{"../util/EventTarget":23,"../util/SyntaxError":25,"../util/SyntaxUnit":26,"./Combinator":2,"./MediaFeature":4,"./MediaQuery":5,"./PropertyName":8,"./PropertyValue":9,"./PropertyValuePart":11,"./Selector":13,"./SelectorPart":14,"./SelectorSubPart":15,"./TokenStream":17,"./Tokens":18,"./Validation":19}],7:[function(require,module,exports){

"use strict";

var Properties = module.exports = {
    __proto__: null,
    "align-items"                       : "flex-start | flex-end | center | baseline | stretch",
    "align-content"                     : "flex-start | flex-end | center | space-between | space-around | stretch",
    "align-self"                        : "auto | flex-start | flex-end | center | baseline | stretch",
    "all"                               : "initial | inherit | unset",
    "-webkit-align-items"               : "flex-start | flex-end | center | baseline | stretch",
    "-webkit-align-content"             : "flex-start | flex-end | center | space-between | space-around | stretch",
    "-webkit-align-self"                : "auto | flex-start | flex-end | center | baseline | stretch",
    "alignment-adjust"                  : "auto | baseline | before-edge | text-before-edge | middle | central | after-edge | text-after-edge | ideographic | alphabetic | hanging | mathematical | <percentage> | <length>",
    "alignment-baseline"                : "auto | baseline | use-script | before-edge | text-before-edge | after-edge | text-after-edge | central | middle | ideographic | alphabetic | hanging | mathematical",
    "animation"                         : 1,
    "animation-delay"                   : "<time>#",
    "animation-direction"               : "<single-animation-direction>#",
    "animation-duration"                : "<time>#",
    "animation-fill-mode"               : "[ none | forwards | backwards | both ]#",
    "animation-iteration-count"         : "[ <number> | infinite ]#",
    "animation-name"                    : "[ none | <single-animation-name> ]#",
    "animation-play-state"              : "[ running | paused ]#",
    "animation-timing-function"         : 1,
    "-moz-animation-delay"              : "<time>#",
    "-moz-animation-direction"          : "[ normal | alternate ]#",
    "-moz-animation-duration"           : "<time>#",
    "-moz-animation-iteration-count"    : "[ <number> | infinite ]#",
    "-moz-animation-name"               : "[ none | <single-animation-name> ]#",
    "-moz-animation-play-state"         : "[ running | paused ]#",

    "-ms-animation-delay"               : "<time>#",
    "-ms-animation-direction"           : "[ normal | alternate ]#",
    "-ms-animation-duration"            : "<time>#",
    "-ms-animation-iteration-count"     : "[ <number> | infinite ]#",
    "-ms-animation-name"                : "[ none | <single-animation-name> ]#",
    "-ms-animation-play-state"          : "[ running | paused ]#",

    "-webkit-animation-delay"           : "<time>#",
    "-webkit-animation-direction"       : "[ normal | alternate ]#",
    "-webkit-animation-duration"        : "<time>#",
    "-webkit-animation-fill-mode"       : "[ none | forwards | backwards | both ]#",
    "-webkit-animation-iteration-count" : "[ <number> | infinite ]#",
    "-webkit-animation-name"            : "[ none | <single-animation-name> ]#",
    "-webkit-animation-play-state"      : "[ running | paused ]#",

    "-o-animation-delay"                : "<time>#",
    "-o-animation-direction"            : "[ normal | alternate ]#",
    "-o-animation-duration"             : "<time>#",
    "-o-animation-iteration-count"      : "[ <number> | infinite ]#",
    "-o-animation-name"                 : "[ none | <single-animation-name> ]#",
    "-o-animation-play-state"           : "[ running | paused ]#",

    "appearance"                        : "none | auto",
    "-moz-appearance"                   : "none | button | button-arrow-down | button-arrow-next | button-arrow-previous | button-arrow-up | button-bevel | button-focus | caret | checkbox | checkbox-container | checkbox-label | checkmenuitem | dualbutton | groupbox | listbox | listitem | menuarrow | menubar | menucheckbox | menuimage | menuitem | menuitemtext | menulist | menulist-button | menulist-text | menulist-textfield | menupopup | menuradio | menuseparator | meterbar | meterchunk | progressbar | progressbar-vertical | progresschunk | progresschunk-vertical | radio | radio-container | radio-label | radiomenuitem | range | range-thumb | resizer | resizerpanel | scale-horizontal | scalethumbend | scalethumb-horizontal | scalethumbstart | scalethumbtick | scalethumb-vertical | scale-vertical | scrollbarbutton-down | scrollbarbutton-left | scrollbarbutton-right | scrollbarbutton-up | scrollbarthumb-horizontal | scrollbarthumb-vertical | scrollbartrack-horizontal | scrollbartrack-vertical | searchfield | separator | sheet | spinner | spinner-downbutton | spinner-textfield | spinner-upbutton | splitter | statusbar | statusbarpanel | tab | tabpanel | tabpanels | tab-scroll-arrow-back | tab-scroll-arrow-forward | textfield | textfield-multiline | toolbar | toolbarbutton | toolbarbutton-dropdown | toolbargripper | toolbox | tooltip | treeheader | treeheadercell | treeheadersortarrow | treeitem | treeline | treetwisty | treetwistyopen | treeview | -moz-mac-unified-toolbar | -moz-win-borderless-glass | -moz-win-browsertabbar-toolbox | -moz-win-communicationstext | -moz-win-communications-toolbox | -moz-win-exclude-glass | -moz-win-glass | -moz-win-mediatext | -moz-win-media-toolbox | -moz-window-button-box | -moz-window-button-box-maximized | -moz-window-button-close | -moz-window-button-maximize | -moz-window-button-minimize | -moz-window-button-restore | -moz-window-frame-bottom | -moz-window-frame-left | -moz-window-frame-right | -moz-window-titlebar | -moz-window-titlebar-maximized",
    "-ms-appearance"                    : "none | icon | window | desktop | workspace | document | tooltip | dialog | button | push-button | hyperlink | radio | radio-button | checkbox | menu-item | tab | menu | menubar | pull-down-menu | pop-up-menu | list-menu | radio-group | checkbox-group | outline-tree | range | field | combo-box | signature | password | normal",
    "-webkit-appearance"                : "none | button | button-bevel | caps-lock-indicator | caret | checkbox | default-button | listbox | listitem | media-fullscreen-button | media-mute-button | media-play-button | media-seek-back-button | media-seek-forward-button | media-slider | media-sliderthumb | menulist | menulist-button | menulist-text | menulist-textfield | push-button | radio | searchfield | searchfield-cancel-button | searchfield-decoration | searchfield-results-button | searchfield-results-decoration | slider-horizontal | slider-vertical | sliderthumb-horizontal | sliderthumb-vertical | square-button | textarea | textfield | scrollbarbutton-down | scrollbarbutton-left | scrollbarbutton-right | scrollbarbutton-up | scrollbargripper-horizontal | scrollbargripper-vertical | scrollbarthumb-horizontal | scrollbarthumb-vertical | scrollbartrack-horizontal | scrollbartrack-vertical",
    "-o-appearance"                     : "none | window | desktop | workspace | document | tooltip | dialog | button | push-button | hyperlink | radio | radio-button | checkbox | menu-item | tab | menu | menubar | pull-down-menu | pop-up-menu | list-menu | radio-group | checkbox-group | outline-tree | range | field | combo-box | signature | password | normal",

    "azimuth"                           : "<azimuth>",
    "backface-visibility"               : "visible | hidden",
    "background"                        : 1,
    "background-attachment"             : "<attachment>#",
    "background-clip"                   : "<box>#",
    "background-color"                  : "<color>",
    "background-image"                  : "<bg-image>#",
    "background-origin"                 : "<box>#",
    "background-position"               : "<bg-position>",
    "background-repeat"                 : "<repeat-style>#",
    "background-size"                   : "<bg-size>#",
    "baseline-shift"                    : "baseline | sub | super | <percentage> | <length>",
    "behavior"                          : 1,
    "binding"                           : 1,
    "bleed"                             : "<length>",
    "bookmark-label"                    : "<content> | <attr> | <string>",
    "bookmark-level"                    : "none | <integer>",
    "bookmark-state"                    : "open | closed",
    "bookmark-target"                   : "none | <uri> | <attr>",
    "border"                            : "<border-width> || <border-style> || <color>",
    "border-bottom"                     : "<border-width> || <border-style> || <color>",
    "border-bottom-color"               : "<color>",
    "border-bottom-left-radius"         :  "<x-one-radius>",
    "border-bottom-right-radius"        :  "<x-one-radius>",
    "border-bottom-style"               : "<border-style>",
    "border-bottom-width"               : "<border-width>",
    "border-collapse"                   : "collapse | separate",
    "border-color"                      : "<color>{1,4}",
    "border-image"                      : 1,
    "border-image-outset"               : "[ <length> | <number> ]{1,4}",
    "border-image-repeat"               : "[ stretch | repeat | round | space ]{1,2}",
    "border-image-slice"                : "<border-image-slice>",
    "border-image-source"               : "<image> | none",
    "border-image-width"                : "[ <length> | <percentage> | <number> | auto ]{1,4}",
    "border-left"                       : "<border-width> || <border-style> || <color>",
    "border-left-color"                 : "<color>",
    "border-left-style"                 : "<border-style>",
    "border-left-width"                 : "<border-width>",
    "border-radius"                     : "<border-radius>",
    "border-right"                      : "<border-width> || <border-style> || <color>",
    "border-right-color"                : "<color>",
    "border-right-style"                : "<border-style>",
    "border-right-width"                : "<border-width>",
    "border-spacing"                    : "<length>{1,2}",
    "border-style"                      : "<border-style>{1,4}",
    "border-top"                        : "<border-width> || <border-style> || <color>",
    "border-top-color"                  : "<color>",
    "border-top-left-radius"            : "<x-one-radius>",
    "border-top-right-radius"           : "<x-one-radius>",
    "border-top-style"                  : "<border-style>",
    "border-top-width"                  : "<border-width>",
    "border-width"                      : "<border-width>{1,4}",
    "bottom"                            : "<margin-width>",
    "-moz-box-align"                    : "start | end | center | baseline | stretch",
    "-moz-box-decoration-break"         : "slice | clone",
    "-moz-box-direction"                : "normal | reverse",
    "-moz-box-flex"                     : "<number>",
    "-moz-box-flex-group"               : "<integer>",
    "-moz-box-lines"                    : "single | multiple",
    "-moz-box-ordinal-group"            : "<integer>",
    "-moz-box-orient"                   : "horizontal | vertical | inline-axis | block-axis",
    "-moz-box-pack"                     : "start | end | center | justify",
    "-o-box-decoration-break"           : "slice | clone",
    "-webkit-box-align"                 : "start | end | center | baseline | stretch",
    "-webkit-box-decoration-break"      : "slice | clone",
    "-webkit-box-direction"             : "normal | reverse",
    "-webkit-box-flex"                  : "<number>",
    "-webkit-box-flex-group"            : "<integer>",
    "-webkit-box-lines"                 : "single | multiple",
    "-webkit-box-ordinal-group"         : "<integer>",
    "-webkit-box-orient"                : "horizontal | vertical | inline-axis | block-axis",
    "-webkit-box-pack"                  : "start | end | center | justify",
    "box-decoration-break"              : "slice | clone",
    "box-shadow"                        : "<box-shadow>",
    "box-sizing"                        : "content-box | border-box",
    "break-after"                       : "auto | always | avoid | left | right | page | column | avoid-page | avoid-column",
    "break-before"                      : "auto | always | avoid | left | right | page | column | avoid-page | avoid-column",
    "break-inside"                      : "auto | avoid | avoid-page | avoid-column",
    "caption-side"                      : "top | bottom",
    "clear"                             : "none | right | left | both",
    "clip"                              : "<shape> | auto",
    "-webkit-clip-path"                 : "<clip-source> | <clip-path> | none",
    "clip-path"                         : "<clip-source> | <clip-path> | none",
    "clip-rule"                         : "nonzero | evenodd",
    "color"                             : "<color>",
    "color-interpolation"               : "auto | sRGB | linearRGB",
    "color-interpolation-filters"       : "auto | sRGB | linearRGB",
    "color-profile"                     : 1,
    "color-rendering"                   : "auto | optimizeSpeed | optimizeQuality",
    "column-count"                      : "<integer> | auto",                       // https    ://www.w3.org/TR/css3-multicol/
    "column-fill"                       : "auto | balance",
    "column-gap"                        : "<length> | normal",
    "column-rule"                       : "<border-width> || <border-style> || <color>",
    "column-rule-color"                 : "<color>",
    "column-rule-style"                 : "<border-style>",
    "column-rule-width"                 : "<border-width>",
    "column-span"                       : "none | all",
    "column-width"                      : "<length> | auto",
    "columns"                           : 1,
    "content"                           : 1,
    "counter-increment"                 : 1,
    "counter-reset"                     : 1,
    "crop"                              : "<shape> | auto",
    "cue"                               : "cue-after | cue-before",
    "cue-after"                         : 1,
    "cue-before"                        : 1,
    "cursor"                            : 1,
    "direction"                         : "ltr | rtl",
    "display"                           : "inline | block | list-item | inline-block | table | inline-table | table-row-group | table-header-group | table-footer-group | table-row | table-column-group | table-column | table-cell | table-caption | grid | inline-grid | run-in | ruby | ruby-base | ruby-text | ruby-base-container | ruby-text-container | contents | none | -moz-box | -moz-inline-block | -moz-inline-box | -moz-inline-grid | -moz-inline-stack | -moz-inline-table | -moz-grid | -moz-grid-group | -moz-grid-line | -moz-groupbox | -moz-deck | -moz-popup | -moz-stack | -moz-marker | -webkit-box | -webkit-inline-box | -ms-flexbox | -ms-inline-flexbox | flex | -webkit-flex | inline-flex | -webkit-inline-flex",
    "dominant-baseline"                 : "auto | use-script | no-change | reset-size | ideographic | alphabetic | hanging | mathematical | central | middle | text-after-edge | text-before-edge",
    "drop-initial-after-adjust"         : "central | middle | after-edge | text-after-edge | ideographic | alphabetic | mathematical | <percentage> | <length>",
    "drop-initial-after-align"          : "baseline | use-script | before-edge | text-before-edge | after-edge | text-after-edge | central | middle | ideographic | alphabetic | hanging | mathematical",
    "drop-initial-before-adjust"        : "before-edge | text-before-edge | central | middle | hanging | mathematical | <percentage> | <length>",
    "drop-initial-before-align"         : "caps-height | baseline | use-script | before-edge | text-before-edge | after-edge | text-after-edge | central | middle | ideographic | alphabetic | hanging | mathematical",
    "drop-initial-size"                 : "auto | line | <length> | <percentage>",
    "drop-initial-value"                : "<integer>",
    "elevation"                         : "<angle> | below | level | above | higher | lower",
    "empty-cells"                       : "show | hide",
    "enable-background"                 : 1,
    "fill"                              : "<paint>",
    "fill-opacity"                      : "<opacity-value>",
    "fill-rule"                         : "nonzero | evenodd",
    "filter"                            : "<filter-function-list> | none",
    "fit"                               : "fill | hidden | meet | slice",
    "fit-position"                      : 1,
    "flex"                              : "<flex>",
    "flex-basis"                        : "<width>",
    "flex-direction"                    : "row | row-reverse | column | column-reverse",
    "flex-flow"                         : "<flex-direction> || <flex-wrap>",
    "flex-grow"                         : "<number>",
    "flex-shrink"                       : "<number>",
    "flex-wrap"                         : "nowrap | wrap | wrap-reverse",
    "-webkit-flex"                      : "<flex>",
    "-webkit-flex-basis"                : "<width>",
    "-webkit-flex-direction"            : "row | row-reverse | column | column-reverse",
    "-webkit-flex-flow"                 : "<flex-direction> || <flex-wrap>",
    "-webkit-flex-grow"                 : "<number>",
    "-webkit-flex-shrink"               : "<number>",
    "-webkit-flex-wrap"                 : "nowrap | wrap | wrap-reverse",
    "-ms-flex"                          : "<flex>",
    "-ms-flex-align"                    : "start | end | center | stretch | baseline",
    "-ms-flex-direction"                : "row | row-reverse | column | column-reverse",
    "-ms-flex-order"                    : "<number>",
    "-ms-flex-pack"                     : "start | end | center | justify | distribute",
    "-ms-flex-wrap"                     : "nowrap | wrap | wrap-reverse",
    "float"                             : "left | right | none",
    "float-offset"                      : 1,
    "flood-color"                       : 1,
    "flood-opacity"                     : "<opacity-value>",
    "font"                              : "<font-shorthand> | caption | icon | menu | message-box | small-caption | status-bar",
    "font-family"                       : "<font-family>",
    "font-feature-settings"             : "<feature-tag-value> | normal",
    "font-kerning"                      : "auto | normal | none",
    "font-size"                         : "<font-size>",
    "font-size-adjust"                  : "<number> | none",
    "font-stretch"                      : "<font-stretch>",
    "font-style"                        : "<font-style>",
    "font-variant"                      : "<font-variant> | normal | none",
    "font-variant-alternates"           : "<font-variant-alternates> | normal",
    "font-variant-caps"                 : "<font-variant-caps> | normal",
    "font-variant-east-asian"           : "<font-variant-east-asian> | normal",
    "font-variant-ligatures"            : "<font-variant-ligatures> | normal | none",
    "font-variant-numeric"              : "<font-variant-numeric> | normal",
    "font-variant-position"             : "normal | sub | super",
    "font-weight"                       : "<font-weight>",
    "gap"                               : "[ <length> | <percentage> ]{1,2}",
    "glyph-orientation-horizontal"      : "<glyph-angle>",
    "glyph-orientation-vertical"        : "auto | <glyph-angle>",
    "grid"                              : 1,
    "grid-area"                         : 1,
    "grid-auto-columns"                 : 1,
    "grid-auto-flow"                    : 1,
    "grid-auto-position"                : 1,
    "grid-auto-rows"                    : 1,
    "grid-cell-stacking"                : "columns | rows | layer",
    "grid-column"                       : 1,
    "grid-columns"                      : 1,
    "grid-column-align"                 : "start | end | center | stretch",
    "grid-column-sizing"                : 1,
    "grid-column-start"                 : 1,
    "grid-column-end"                   : 1,
    "grid-column-span"                  : "<integer>",
    "grid-flow"                         : "none | rows | columns",
    "grid-gap"                          : "[ <length> | <percentage> ]{1,2}",
    "grid-layer"                        : "<integer>",
    "grid-row"                          : 1,
    "grid-rows"                         : 1,
    "grid-row-align"                    : "start | end | center | stretch",
    "grid-row-gap"                      : 1,
    "grid-row-start"                    : 1,
    "grid-row-end"                      : 1,
    "grid-row-span"                     : "<integer>",
    "grid-row-sizing"                   : 1,
    "grid-template"                     : 1,
    "grid-template-areas"               : 1,
    "grid-template-columns"             : 1,
    "grid-template-rows"                : 1,
    "hanging-punctuation"               : 1,
    "height"                            : "<margin-width> | <content-sizing>",
    "hyphenate-after"                   : "<integer> | auto",
    "hyphenate-before"                  : "<integer> | auto",
    "hyphenate-character"               : "<string> | auto",
    "hyphenate-lines"                   : "no-limit | <integer>",
    "hyphenate-resource"                : 1,
    "hyphens"                           : "none | manual | auto",
    "icon"                              : 1,
    "image-orientation"                 : "angle | auto",
    "image-rendering"                   : "auto | optimizeSpeed | optimizeQuality",
    "image-resolution"                  : 1,
    "ime-mode"                          : "auto | normal | active | inactive | disabled",
    "inline-box-align"                  : "last | <integer>",
    "justify-content"                   : "flex-start | flex-end | center | space-between | space-around | space-evenly | stretch",
    "-webkit-justify-content"           : "flex-start | flex-end | center | space-between | space-around | space-evenly | stretch",
    "kerning"                           : "auto | <length>",
    "left"                              : "<margin-width>",
    "letter-spacing"                    : "<length> | normal",
    "line-height"                       : "<line-height>",
    "line-break"                        : "auto | loose | normal | strict",
    "line-stacking"                     : 1,
    "line-stacking-ruby"                : "exclude-ruby | include-ruby",
    "line-stacking-shift"               : "consider-shifts | disregard-shifts",
    "line-stacking-strategy"            : "inline-line-height | block-line-height | max-height | grid-height",
    "list-style"                        : 1,
    "list-style-image"                  : "<uri> | none",
    "list-style-position"               : "inside | outside",
    "list-style-type"                   : "disc | circle | square | decimal | decimal-leading-zero | lower-roman | upper-roman | lower-greek | lower-latin | upper-latin | armenian | georgian | lower-alpha | upper-alpha | none",
    "margin"                            : "<margin-width>{1,4}",
    "margin-bottom"                     : "<margin-width>",
    "margin-left"                       : "<margin-width>",
    "margin-right"                      : "<margin-width>",
    "margin-top"                        : "<margin-width>",
    "mark"                              : 1,
    "mark-after"                        : 1,
    "mark-before"                       : 1,
    "marker"                            : 1,
    "marker-end"                        : 1,
    "marker-mid"                        : 1,
    "marker-start"                      : 1,
    "marks"                             : 1,
    "marquee-direction"                 : 1,
    "marquee-play-count"                : 1,
    "marquee-speed"                     : 1,
    "marquee-style"                     : 1,
    "mask"                              : 1,
    "max-height"                        : "<length> | <percentage> | <content-sizing> | none",
    "max-width"                         : "<length> | <percentage> | <content-sizing> | none",
    "min-height"                        : "<length> | <percentage> | <content-sizing> | contain-floats | -moz-contain-floats | -webkit-contain-floats",
    "min-width"                         : "<length> | <percentage> | <content-sizing> | contain-floats | -moz-contain-floats | -webkit-contain-floats",
    "mix-blend-mode"                    : "<blend-mode>",
    "move-to"                           : 1,
    "nav-down"                          : 1,
    "nav-index"                         : 1,
    "nav-left"                          : 1,
    "nav-right"                         : 1,
    "nav-up"                            : 1,
    "object-fit"                        : "fill | contain | cover | none | scale-down",
    "object-position"                   : "<position>",
    "opacity"                           : "<opacity-value>",
    "order"                             : "<integer>",
    "-webkit-order"                     : "<integer>",
    "orphans"                           : "<integer>",
    "outline"                           : 1,
    "outline-color"                     : "<color> | invert",
    "outline-offset"                    : 1,
    "outline-style"                     : "<border-style>",
    "outline-width"                     : "<border-width>",
    "overflow"                          : "visible | hidden | scroll | auto",
    "overflow-style"                    : 1,
    "overflow-wrap"                     : "normal | break-word",
    "overflow-x"                        : 1,
    "overflow-y"                        : 1,
    "padding"                           : "<padding-width>{1,4}",
    "padding-bottom"                    : "<padding-width>",
    "padding-left"                      : "<padding-width>",
    "padding-right"                     : "<padding-width>",
    "padding-top"                       : "<padding-width>",
    "page"                              : 1,
    "page-break-after"                  : "auto | always | avoid | left | right",
    "page-break-before"                 : "auto | always | avoid | left | right",
    "page-break-inside"                 : "auto | avoid",
    "page-policy"                       : 1,
    "pause"                             : 1,
    "pause-after"                       : 1,
    "pause-before"                      : 1,
    "perspective"                       : 1,
    "perspective-origin"                : 1,
    "phonemes"                          : 1,
    "pitch"                             : 1,
    "pitch-range"                       : 1,
    "play-during"                       : 1,
    "pointer-events"                    : "auto | none | visiblePainted | visibleFill | visibleStroke | visible | painted | fill | stroke | all",
    "position"                          : "static | relative | absolute | fixed | sticky | -webkit-sticky",
    "presentation-level"                : 1,
    "punctuation-trim"                  : 1,
    "quotes"                            : 1,
    "rendering-intent"                  : 1,
    "resize"                            : 1,
    "rest"                              : 1,
    "rest-after"                        : 1,
    "rest-before"                       : 1,
    "richness"                          : 1,
    "right"                             : "<margin-width>",
    "rotation"                          : 1,
    "rotation-point"                    : 1,
    "ruby-align"                        : 1,
    "ruby-overhang"                     : 1,
    "ruby-position"                     : 1,
    "ruby-span"                         : 1,
    "shape-rendering"                   : "auto | optimizeSpeed | crispEdges | geometricPrecision",
    "size"                              : 1,
    "speak"                             : "normal | none | spell-out",
    "speak-header"                      : "once | always",
    "speak-numeral"                     : "digits | continuous",
    "speak-punctuation"                 : "code | none",
    "speech-rate"                       : 1,
    "src"                               : 1,
    "stop-color"                        : 1,
    "stop-opacity"                      : "<opacity-value>",
    "stress"                            : 1,
    "string-set"                        : 1,
    "stroke"                            : "<paint>",
    "stroke-dasharray"                  : "none | <dasharray>",
    "stroke-dashoffset"                 : "<percentage> | <length>",
    "stroke-linecap"                    : "butt | round | square",
    "stroke-linejoin"                   : "miter | round | bevel",
    "stroke-miterlimit"                 : "<miterlimit>",
    "stroke-opacity"                    : "<opacity-value>",
    "stroke-width"                      : "<percentage> | <length>",

    "table-layout"                      : "auto | fixed",
    "tab-size"                          : "<integer> | <length>",
    "target"                            : 1,
    "target-name"                       : 1,
    "target-new"                        : 1,
    "target-position"                   : 1,
    "text-align"                        : "left | right | center | justify | match-parent | start | end",
    "text-align-last"                   : 1,
    "text-anchor"                       : "start | middle | end",
    "text-decoration"                   : "<text-decoration-line> || <text-decoration-style> || <text-decoration-color>",
    "text-decoration-color"             : "<text-decoration-color>",
    "text-decoration-line"              : "<text-decoration-line>",
    "text-decoration-style"             : "<text-decoration-style>",
    "text-decoration-skip"              : "none | [ objects || spaces || ink || edges || box-decoration ]",
    "-webkit-text-decoration-skip"      : "none | [ objects || spaces || ink || edges || box-decoration ]",
    "text-underline-position"           : "auto | [ under || [ left | right ] ]",
    "text-emphasis"                     : 1,
    "text-height"                       : 1,
    "text-indent"                       : "<length> | <percentage>",
    "text-justify"                      : "auto | none | inter-word | inter-ideograph | inter-cluster | distribute | kashida",
    "text-outline"                      : 1,
    "text-overflow"                     : 1,
    "text-rendering"                    : "auto | optimizeSpeed | optimizeLegibility | geometricPrecision",
    "text-shadow"                       : 1,
    "text-transform"                    : "capitalize | uppercase | lowercase | none",
    "text-wrap"                         : "normal | none | avoid",
    "top"                               : "<margin-width>",
    "-ms-touch-action"                  : "auto | none | pan-x | pan-y | pan-left | pan-right | pan-up | pan-down | manipulation",
    "touch-action"                      : "auto | none | pan-x | pan-y | pan-left | pan-right | pan-up | pan-down | manipulation",
    "transform"                         : 1,
    "transform-origin"                  : 1,
    "transform-style"                   : 1,
    "transition"                        : 1,
    "transition-delay"                  : 1,
    "transition-duration"               : 1,
    "transition-property"               : 1,
    "transition-timing-function"        : 1,
    "unicode-bidi"                      : "normal | embed | isolate | bidi-override | isolate-override | plaintext",
    "user-modify"                       : "read-only | read-write | write-only",
    "user-select"                       : "auto | text | none | contain | all",
    "vertical-align"                    : "auto | use-script | baseline | sub | super | top | text-top | central | middle | bottom | text-bottom | <percentage> | <length>",
    "visibility"                        : "visible | hidden | collapse",
    "voice-balance"                     : 1,
    "voice-duration"                    : 1,
    "voice-family"                      : 1,
    "voice-pitch"                       : 1,
    "voice-pitch-range"                 : 1,
    "voice-rate"                        : 1,
    "voice-stress"                      : 1,
    "voice-volume"                      : 1,
    "volume"                            : 1,
    "white-space"                       : "normal | pre | nowrap | pre-wrap | pre-line | -pre-wrap | -o-pre-wrap | -moz-pre-wrap | -hp-pre-wrap",   // https    ://perishablepress.com/wrapping-content/
    "white-space-collapse"              : 1,
    "widows"                            : "<integer>",
    "width"                             : "<length> | <percentage> | <content-sizing> | auto",
    "will-change"                       : "<will-change>",
    "word-break"                        : "normal | keep-all | break-all | break-word",
    "word-spacing"                      : "<length> | normal",
    "word-wrap"                         : "normal | break-word",
    "writing-mode"                      : "horizontal-tb | vertical-rl | vertical-lr | lr-tb | rl-tb | tb-rl | bt-rl | tb-lr | bt-lr | lr-bt | rl-bt | lr | rl | tb",
    "z-index"                           : "<integer> | auto",
    "zoom"                              : "<number> | <percentage> | normal"
};

},{}],8:[function(require,module,exports){
"use strict";

module.exports = PropertyName;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function PropertyName(text, hack, line, col) {

    SyntaxUnit.call(this, text, line, col, Parser.PROPERTY_NAME_TYPE);
    this.hack = hack;

}

PropertyName.prototype = new SyntaxUnit();
PropertyName.prototype.constructor = PropertyName;
PropertyName.prototype.toString = function() {
    return (this.hack ? this.hack : "") + this.text;
};

},{"../util/SyntaxUnit":26,"./Parser":6}],9:[function(require,module,exports){
"use strict";

module.exports = PropertyValue;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function PropertyValue(parts, line, col) {

    SyntaxUnit.call(this, parts.join(" "), line, col, Parser.PROPERTY_VALUE_TYPE);
    this.parts = parts;

}

PropertyValue.prototype = new SyntaxUnit();
PropertyValue.prototype.constructor = PropertyValue;


},{"../util/SyntaxUnit":26,"./Parser":6}],10:[function(require,module,exports){
"use strict";

module.exports = PropertyValueIterator;
function PropertyValueIterator(value) {
    this._i = 0;
    this._parts = value.parts;
    this._marks = [];
    this.value = value;

}
PropertyValueIterator.prototype.count = function() {
    return this._parts.length;
};
PropertyValueIterator.prototype.isFirst = function() {
    return this._i === 0;
};
PropertyValueIterator.prototype.hasNext = function() {
    return this._i < this._parts.length;
};
PropertyValueIterator.prototype.mark = function() {
    this._marks.push(this._i);
};
PropertyValueIterator.prototype.peek = function(count) {
    return this.hasNext() ? this._parts[this._i + (count || 0)] : null;
};
PropertyValueIterator.prototype.next = function() {
    return this.hasNext() ? this._parts[this._i++] : null;
};
PropertyValueIterator.prototype.previous = function() {
    return this._i > 0 ? this._parts[--this._i] : null;
};
PropertyValueIterator.prototype.restore = function() {
    if (this._marks.length) {
        this._i = this._marks.pop();
    }
};
PropertyValueIterator.prototype.drop = function() {
    this._marks.pop();
};

},{}],11:[function(require,module,exports){
"use strict";

module.exports = PropertyValuePart;

var SyntaxUnit = require("../util/SyntaxUnit");

var Colors = require("./Colors");
var Parser = require("./Parser");
var Tokens = require("./Tokens");
function PropertyValuePart(text, line, col, optionalHint) {
    var hint = optionalHint || {};

    SyntaxUnit.call(this, text, line, col, Parser.PROPERTY_VALUE_PART_TYPE);
    this.type = "unknown";

    var temp;
    if (/^([+-]?[\d.]+)([a-z]+)$/i.test(text)) {  // dimension
        this.type = "dimension";
        this.value = Number(RegExp.$1);
        this.units = RegExp.$2;
        switch (this.units.toLowerCase()) {

            case "em":
            case "rem":
            case "ex":
            case "px":
            case "cm":
            case "mm":
            case "in":
            case "pt":
            case "pc":
            case "ch":
            case "vh":
            case "vw":
            case "vmax":
            case "vmin":
                this.type = "length";
                break;

            case "fr":
                this.type = "grid";
                break;

            case "deg":
            case "rad":
            case "grad":
            case "turn":
                this.type = "angle";
                break;

            case "ms":
            case "s":
                this.type = "time";
                break;

            case "hz":
            case "khz":
                this.type = "frequency";
                break;

            case "dpi":
            case "dpcm":
                this.type = "resolution";
                break;

        }

    } else if (/^([+-]?[\d.]+)%$/i.test(text)) {  // percentage
        this.type = "percentage";
        this.value = Number(RegExp.$1);
    } else if (/^([+-]?\d+)$/i.test(text)) {  // integer
        this.type = "integer";
        this.value = Number(RegExp.$1);
    } else if (/^([+-]?[\d.]+)$/i.test(text)) {  // number
        this.type = "number";
        this.value = Number(RegExp.$1);

    } else if (/^#([a-f0-9]{3,6})/i.test(text)) {  // hexcolor
        this.type = "color";
        temp = RegExp.$1;
        if (temp.length === 3) {
            this.red    = parseInt(temp.charAt(0) + temp.charAt(0), 16);
            this.green  = parseInt(temp.charAt(1) + temp.charAt(1), 16);
            this.blue   = parseInt(temp.charAt(2) + temp.charAt(2), 16);
        } else {
            this.red    = parseInt(temp.substring(0, 2), 16);
            this.green  = parseInt(temp.substring(2, 4), 16);
            this.blue   = parseInt(temp.substring(4, 6), 16);
        }
    } else if (/^rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i.test(text)) { // rgb() color with absolute numbers
        this.type   = "color";
        this.red    = Number(RegExp.$1);
        this.green  = Number(RegExp.$2);
        this.blue   = Number(RegExp.$3);
    } else if (/^rgb\(\s*(\d+)%\s*,\s*(\d+)%\s*,\s*(\d+)%\s*\)/i.test(text)) { // rgb() color with percentages
        this.type   = "color";
        this.red    = Number(RegExp.$1) * 255 / 100;
        this.green  = Number(RegExp.$2) * 255 / 100;
        this.blue   = Number(RegExp.$3) * 255 / 100;
    } else if (/^rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([\d.]+)\s*\)/i.test(text)) { // rgba() color with absolute numbers
        this.type   = "color";
        this.red    = Number(RegExp.$1);
        this.green  = Number(RegExp.$2);
        this.blue   = Number(RegExp.$3);
        this.alpha  = Number(RegExp.$4);
    } else if (/^rgba\(\s*(\d+)%\s*,\s*(\d+)%\s*,\s*(\d+)%\s*,\s*([\d.]+)\s*\)/i.test(text)) { // rgba() color with percentages
        this.type   = "color";
        this.red    = Number(RegExp.$1) * 255 / 100;
        this.green  = Number(RegExp.$2) * 255 / 100;
        this.blue   = Number(RegExp.$3) * 255 / 100;
        this.alpha  = Number(RegExp.$4);
    } else if (/^hsl\(\s*(\d+)\s*,\s*(\d+)%\s*,\s*(\d+)%\s*\)/i.test(text)) { // hsl()
        this.type   = "color";
        this.hue    = Number(RegExp.$1);
        this.saturation = Number(RegExp.$2) / 100;
        this.lightness  = Number(RegExp.$3) / 100;
    } else if (/^hsla\(\s*(\d+)\s*,\s*(\d+)%\s*,\s*(\d+)%\s*,\s*([\d.]+)\s*\)/i.test(text)) { // hsla() color with percentages
        this.type   = "color";
        this.hue    = Number(RegExp.$1);
        this.saturation = Number(RegExp.$2) / 100;
        this.lightness  = Number(RegExp.$3) / 100;
        this.alpha  = Number(RegExp.$4);
    } else if (/^url\(("([^\\"]|\.)*")\)/i.test(text)) { // URI
        this.type   = "uri";
        this.uri    = PropertyValuePart.parseString(RegExp.$1);
    } else if (/^([^(]+)\(/i.test(text)) {
        this.type   = "function";
        this.name   = RegExp.$1;
        this.value  = text;
    } else if (/^"([^\n\r\f\\"]|\\\r\n|\\[^\r0-9a-f]|\\[0-9a-f]{1,6}(\r\n|[ \n\r\t\f])?)*"/i.test(text)) {    // double-quoted string
        this.type   = "string";
        this.value  = PropertyValuePart.parseString(text);
    } else if (/^'([^\n\r\f\\']|\\\r\n|\\[^\r0-9a-f]|\\[0-9a-f]{1,6}(\r\n|[ \n\r\t\f])?)*'/i.test(text)) {    // single-quoted string
        this.type   = "string";
        this.value  = PropertyValuePart.parseString(text);
    } else if (Colors[text.toLowerCase()]) {  // named color
        this.type   = "color";
        temp        = Colors[text.toLowerCase()].substring(1);
        this.red    = parseInt(temp.substring(0, 2), 16);
        this.green  = parseInt(temp.substring(2, 4), 16);
        this.blue   = parseInt(temp.substring(4, 6), 16);
    } else if (/^[,/]$/.test(text)) {
        this.type   = "operator";
        this.value  = text;
    } else if (/^-?[a-z_\u00A0-\uFFFF][a-z0-9\-_\u00A0-\uFFFF]*$/i.test(text)) {
        this.type   = "identifier";
        this.value  = text;
    }
    this.wasIdent = Boolean(hint.ident);

}

PropertyValuePart.prototype = new SyntaxUnit();
PropertyValuePart.prototype.constructor = PropertyValuePart;
PropertyValuePart.parseString = function(str) {
    str = str.slice(1, -1); // Strip surrounding single/double quotes
    var replacer = function(match, esc) {
        if (/^(\n|\r\n|\r|\f)$/.test(esc)) {
            return "";
        }
        var m = /^[0-9a-f]{1,6}/i.exec(esc);
        if (m) {
            var codePoint = parseInt(m[0], 16);
            if (String.fromCodePoint) {
                return String.fromCodePoint(codePoint);
            } else {
                return String.fromCharCode(codePoint);
            }
        }
        return esc;
    };
    return str.replace(/\\(\r\n|[^\r0-9a-f]|[0-9a-f]{1,6}(\r\n|[ \n\r\t\f])?)/ig,
                       replacer);
};
PropertyValuePart.serializeString = function(value) {
    var replacer = function(match, c) {
        if (c === "\"") {
            return "\\" + c;
        }
        var cp = String.codePointAt ? String.codePointAt(0) :
            String.charCodeAt(0);
        return "\\" + cp.toString(16) + " ";
    };
    return "\"" + value.replace(/["\r\n\f]/g, replacer) + "\"";
};
PropertyValuePart.fromToken = function(token) {
    var part = new PropertyValuePart(token.value, token.startLine, token.startCol, {
        ident: token.type === Tokens.IDENT
    });
    return part;
};

},{"../util/SyntaxUnit":26,"./Colors":1,"./Parser":6,"./Tokens":18}],12:[function(require,module,exports){
"use strict";

var Pseudos = module.exports = {
    __proto__:       null,
    ":first-letter": 1,
    ":first-line":   1,
    ":before":       1,
    ":after":        1
};

Pseudos.ELEMENT = 1;
Pseudos.CLASS = 2;

Pseudos.isElement = function(pseudo) {
    return pseudo.indexOf("::") === 0 || Pseudos[pseudo.toLowerCase()] === Pseudos.ELEMENT;
};

},{}],13:[function(require,module,exports){
"use strict";

module.exports = Selector;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
var Specificity = require("./Specificity");
function Selector(parts, line, col) {

    SyntaxUnit.call(this, parts.join(" "), line, col, Parser.SELECTOR_TYPE);
    this.parts = parts;
    this.specificity = Specificity.calculate(this);

}

Selector.prototype = new SyntaxUnit();
Selector.prototype.constructor = Selector;


},{"../util/SyntaxUnit":26,"./Parser":6,"./Specificity":16}],14:[function(require,module,exports){
"use strict";

module.exports = SelectorPart;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function SelectorPart(elementName, modifiers, text, line, col) {

    SyntaxUnit.call(this, text, line, col, Parser.SELECTOR_PART_TYPE);
    this.elementName = elementName;
    this.modifiers = modifiers;

}

SelectorPart.prototype = new SyntaxUnit();
SelectorPart.prototype.constructor = SelectorPart;


},{"../util/SyntaxUnit":26,"./Parser":6}],15:[function(require,module,exports){
"use strict";

module.exports = SelectorSubPart;

var SyntaxUnit = require("../util/SyntaxUnit");

var Parser = require("./Parser");
function SelectorSubPart(text, type, line, col) {

    SyntaxUnit.call(this, text, line, col, Parser.SELECTOR_SUB_PART_TYPE);
    this.type = type;
    this.args = [];

}

SelectorSubPart.prototype = new SyntaxUnit();
SelectorSubPart.prototype.constructor = SelectorSubPart;


},{"../util/SyntaxUnit":26,"./Parser":6}],16:[function(require,module,exports){
"use strict";

module.exports = Specificity;

var Pseudos = require("./Pseudos");
var SelectorPart = require("./SelectorPart");
function Specificity(a, b, c, d) {
    this.a = a;
    this.b = b;
    this.c = c;
    this.d = d;
}

Specificity.prototype = {
    constructor: Specificity,
    compare: function(other) {
        var comps = ["a", "b", "c", "d"],
            i, len;

        for (i = 0, len = comps.length; i < len; i++) {
            if (this[comps[i]] < other[comps[i]]) {
                return -1;
            } else if (this[comps[i]] > other[comps[i]]) {
                return 1;
            }
        }

        return 0;
    },
    valueOf: function() {
        return (this.a * 1000) + (this.b * 100) + (this.c * 10) + this.d;
    },
    toString: function() {
        return this.a + "," + this.b + "," + this.c + "," + this.d;
    }

};
Specificity.calculate = function(selector) {

    var i,
        len,
        part,
        b = 0,
        c = 0,
        d = 0;

    function updateValues(part) {

        var i, j, len, num,
            elementName = part.elementName ? part.elementName.text : "",
            modifier;

        if (elementName && elementName.charAt(elementName.length - 1) !== "*") {
            d++;
        }

        for (i = 0, len = part.modifiers.length; i < len; i++) {
            modifier = part.modifiers[i];
            switch (modifier.type) {
                case "class":
                case "attribute":
                    c++;
                    break;

                case "id":
                    b++;
                    break;

                case "pseudo":
                    if (Pseudos.isElement(modifier.text)) {
                        d++;
                    } else {
                        c++;
                    }
                    break;

                case "not":
                    for (j = 0, num = modifier.args.length; j < num; j++) {
                        updateValues(modifier.args[j]);
                    }
            }
        }
    }

    for (i = 0, len = selector.parts.length; i < len; i++) {
        part = selector.parts[i];

        if (part instanceof SelectorPart) {
            updateValues(part);
        }
    }

    return new Specificity(0, b, c, d);
};

},{"./Pseudos":12,"./SelectorPart":14}],17:[function(require,module,exports){
"use strict";

module.exports = TokenStream;

var TokenStreamBase = require("../util/TokenStreamBase");

var PropertyValuePart = require("./PropertyValuePart");
var Tokens = require("./Tokens");

var h = /^[0-9a-fA-F]$/,
    nonascii = /^[\u00A0-\uFFFF]$/,
    nl = /\n|\r\n|\r|\f/,
    whitespace = /\u0009|\u000a|\u000c|\u000d|\u0020/;


function isHexDigit(c) {
    return c != null && h.test(c);
}

function isDigit(c) {
    return c != null && /\d/.test(c);
}

function isWhitespace(c) {
    return c != null && whitespace.test(c);
}

function isNewLine(c) {
    return c != null && nl.test(c);
}

function isNameStart(c) {
    return c != null && /[a-z_\u00A0-\uFFFF\\]/i.test(c);
}

function isNameChar(c) {
    return c != null && (isNameStart(c) || /[0-9\-\\]/.test(c));
}

function isIdentStart(c) {
    return c != null && (isNameStart(c) || /-\\/.test(c));
}

function mix(receiver, supplier) {
    for (var prop in supplier) {
        if (Object.prototype.hasOwnProperty.call(supplier, prop)) {
            receiver[prop] = supplier[prop];
        }
    }
    return receiver;
}

function wouldStartIdent(twoCodePoints) {
    return typeof twoCodePoints === "string" &&
        (twoCodePoints[0] === "-" && isNameStart(twoCodePoints[1]) || isNameStart(twoCodePoints[0]));
}

function wouldStartUnsignedNumber(twoCodePoints) {
    return typeof twoCodePoints === "string" &&
        (isDigit(twoCodePoints[0]) || (twoCodePoints[0] === "." && isDigit(twoCodePoints[1])));
}
function TokenStream(input) {
    TokenStreamBase.call(this, input, Tokens);
}

TokenStream.prototype = mix(new TokenStreamBase(), {
    _getToken: function() {

        var c,
            reader = this._reader,
            token   = null,
            startLine   = reader.getLine(),
            startCol    = reader.getCol();

        c = reader.read();

        while (c) {
            switch (c) {
                case "/":

                    if (reader.peek() === "*") {
                        token = this.commentToken(c, startLine, startCol);
                    } else {
                        token = this.charToken(c, startLine, startCol);
                    }
                    break;
                case "|":
                case "~":
                case "^":
                case "$":
                case "*":
                    if (reader.peek() === "=") {
                        token = this.comparisonToken(c, startLine, startCol);
                    } else {
                        token = this.charToken(c, startLine, startCol);
                    }
                    break;
                case "\"":
                case "'":
                    token = this.stringToken(c, startLine, startCol);
                    break;
                case "#":
                    if (isNameChar(reader.peek())) {
                        token = this.hashToken(c, startLine, startCol);
                    } else {
                        token = this.charToken(c, startLine, startCol);
                    }
                    break;
                case ".":
                    if (isDigit(reader.peek())) {
                        token = this.numberToken(c, startLine, startCol);
                    } else {
                        token = this.charToken(c, startLine, startCol);
                    }
                    break;
                case "-":
                    if (wouldStartUnsignedNumber(reader.peekCount(2))) {
                        token = this.numberToken(c, startLine, startCol);
                        break;
                    } else if (reader.peekCount(2) === "->") {
                        token = this.htmlCommentEndToken(c, startLine, startCol);
                    } else {
                        token = this._getDefaultToken(c, startLine, startCol);
                    }
                    break;
                case "+":
                    if (wouldStartUnsignedNumber(reader.peekCount(2))) {
                        token = this.numberToken(c, startLine, startCol);
                    } else {
                        token = this.charToken(c, startLine, startCol);
                    }
                    break;
                case "!":
                    token = this.importantToken(c, startLine, startCol);
                    break;
                case "@":
                    token = this.atRuleToken(c, startLine, startCol);
                    break;
                case ":":
                    token = this.notToken(c, startLine, startCol);
                    break;
                case "<":
                    token = this.htmlCommentStartToken(c, startLine, startCol);
                    break;
                case "\\":
                    if (/[^\r\n\f]/.test(reader.peek())) {
                        token = this.identOrFunctionToken(this.readEscape(c, true), startLine, startCol);
                    } else {
                        token = this.charToken(c, startLine, startCol);
                    }
                    break;
                case "U":
                case "u":
                    if (reader.peek() === "+") {
                        token = this.unicodeRangeToken(c, startLine, startCol);
                    } else {
                        token = this._getDefaultToken(c, startLine, startCol);
                    }
                    break;

                default:
                    token = this._getDefaultToken(c, startLine, startCol);

            }
            break;
        }

        if (!token && c === null) {
            token = this.createToken(Tokens.EOF, null, startLine, startCol);
        }

        return token;
    },
    _getDefaultToken: function(c, startLine, startCol) {
        var reader = this._reader,
            token   = null;

        if (isDigit(c)) {
            token = this.numberToken(c, startLine, startCol);
        } else
        if (isWhitespace(c)) {
            token = this.whitespaceToken(c, startLine, startCol);
        } else
        if (wouldStartIdent(c + reader.peekCount(1))) {
            token = this.identOrFunctionToken(c, startLine, startCol);
        } else {
            token = this.charToken(c, startLine, startCol);
        }

        return token;
    },
    createToken: function(tt, value, startLine, startCol, options) {
        var reader = this._reader;
        options = options || {};

        return {
            value:      value,
            type:       tt,
            channel:    options.channel,
            endChar:    options.endChar,
            hide:       options.hide || false,
            startLine:  startLine,
            startCol:   startCol,
            endLine:    reader.getLine(),
            endCol:     reader.getCol()
        };
    },
    atRuleToken: function(first, startLine, startCol) {
        var rule    = first,
            reader  = this._reader,
            tt      = Tokens.CHAR,
            ident;
        reader.mark();
        ident = this.readName();
        rule = first + ident;
        tt = Tokens.type(rule.toLowerCase());
        if (tt === Tokens.CHAR || tt === Tokens.UNKNOWN) {
            if (rule.length > 1) {
                tt = Tokens.UNKNOWN_SYM;
            } else {
                tt = Tokens.CHAR;
                rule = first;
                reader.reset();
            }
        }

        return this.createToken(tt, rule, startLine, startCol);
    },
    charToken: function(c, startLine, startCol) {
        var tt = Tokens.type(c);
        var opts = {};

        if (tt === -1) {
            tt = Tokens.CHAR;
        } else {
            opts.endChar = Tokens[tt].endChar;
        }

        return this.createToken(tt, c, startLine, startCol, opts);
    },
    commentToken: function(first, startLine, startCol) {
        var comment = this.readComment(first);

        return this.createToken(Tokens.COMMENT, comment, startLine, startCol);
    },
    comparisonToken: function(c, startLine, startCol) {
        var reader  = this._reader,
            comparison  = c + reader.read(),
            tt      = Tokens.type(comparison) || Tokens.CHAR;

        return this.createToken(tt, comparison, startLine, startCol);
    },
    hashToken: function(first, startLine, startCol) {
        var name    = this.readName(first);

        return this.createToken(Tokens.HASH, name, startLine, startCol);
    },
    htmlCommentStartToken: function(first, startLine, startCol) {
        var reader      = this._reader,
            text        = first;

        reader.mark();
        text += reader.readCount(3);

        if (text === "<!--") {
            return this.createToken(Tokens.CDO, text, startLine, startCol);
        } else {
            reader.reset();
            return this.charToken(first, startLine, startCol);
        }
    },
    htmlCommentEndToken: function(first, startLine, startCol) {
        var reader      = this._reader,
            text        = first;

        reader.mark();
        text += reader.readCount(2);

        if (text === "-->") {
            return this.createToken(Tokens.CDC, text, startLine, startCol);
        } else {
            reader.reset();
            return this.charToken(first, startLine, startCol);
        }
    },
    identOrFunctionToken: function(first, startLine, startCol) {
        var reader  = this._reader,
            ident   = this.readName(first),
            tt      = Tokens.IDENT,
            uriFns  = ["url(", "url-prefix(", "domain("],
            uri;
        if (reader.peek() === "(") {
            ident += reader.read();
            if (uriFns.indexOf(ident.toLowerCase()) > -1) {
                reader.mark();
                uri = this.readURI(ident);
                if (uri === null) {
                    reader.reset();
                    tt = Tokens.FUNCTION;
                } else {
                    tt = Tokens.URI;
                    ident = uri;
                }
            } else {
                tt = Tokens.FUNCTION;
            }
        } else if (reader.peek() === ":") {  // might be an IE function
            if (ident.toLowerCase() === "progid") {
                ident += reader.readTo("(");
                tt = Tokens.IE_FUNCTION;
            }
        }

        return this.createToken(tt, ident, startLine, startCol);
    },
    importantToken: function(first, startLine, startCol) {
        var reader      = this._reader,
            important   = first,
            tt          = Tokens.CHAR,
            temp,
            c;

        reader.mark();
        c = reader.read();

        while (c) {
            if (c === "/") {
                if (reader.peek() !== "*") {
                    break;
                } else {
                    temp = this.readComment(c);
                    if (temp === "") {    // broken!
                        break;
                    }
                }
            } else if (isWhitespace(c)) {
                important += c + this.readWhitespace();
            } else if (/i/i.test(c)) {
                temp = reader.readCount(8);
                if (/mportant/i.test(temp)) {
                    important += c + temp;
                    tt = Tokens.IMPORTANT_SYM;

                }
                break;  // we're done
            } else {
                break;
            }

            c = reader.read();
        }

        if (tt === Tokens.CHAR) {
            reader.reset();
            return this.charToken(first, startLine, startCol);
        } else {
            return this.createToken(tt, important, startLine, startCol);
        }


    },
    notToken: function(first, startLine, startCol) {
        var reader      = this._reader,
            text        = first;

        reader.mark();
        text += reader.readCount(4);

        if (text.toLowerCase() === ":not(") {
            return this.createToken(Tokens.NOT, text, startLine, startCol);
        } else {
            reader.reset();
            return this.charToken(first, startLine, startCol);
        }
    },
    numberToken: function(first, startLine, startCol) {
        var reader  = this._reader,
            value   = this.readNumber(first),
            ident,
            tt      = Tokens.NUMBER,
            c       = reader.peek();

        if (isIdentStart(c)) {
            ident = this.readName(reader.read());
            value += ident;

            if (/^em$|^ex$|^px$|^gd$|^rem$|^vw$|^vh$|^fr$|^vmax$|^vmin$|^ch$|^cm$|^mm$|^in$|^pt$|^pc$/i.test(ident)) {
                tt = Tokens.LENGTH;
            } else if (/^deg|^rad$|^grad$|^turn$/i.test(ident)) {
                tt = Tokens.ANGLE;
            } else if (/^ms$|^s$/i.test(ident)) {
                tt = Tokens.TIME;
            } else if (/^hz$|^khz$/i.test(ident)) {
                tt = Tokens.FREQ;
            } else if (/^dpi$|^dpcm$/i.test(ident)) {
                tt = Tokens.RESOLUTION;
            } else {
                tt = Tokens.DIMENSION;
            }

        } else if (c === "%") {
            value += reader.read();
            tt = Tokens.PERCENTAGE;
        }

        return this.createToken(tt, value, startLine, startCol);
    },
    stringToken: function(first, startLine, startCol) {
        var delim   = first,
            string  = first,
            reader  = this._reader,
            tt      = Tokens.STRING,
            c       = reader.read(),
            i;

        while (c) {
            string += c;

            if (c === "\\") {
                c = reader.read();
                if (c === null) {
                    break;  // premature EOF after backslash
                } else if (/[^\r\n\f0-9a-f]/i.test(c)) {
                    string += c;
                } else {
                    for (i = 0; isHexDigit(c) && i < 6; i++) {
                        string += c;
                        c = reader.read();
                    }
                    if (c === "\r" && reader.peek() === "\n") {
                        string += c;
                        c = reader.read();
                    }
                    if (isWhitespace(c)) {
                        string += c;
                    } else {
                        continue;
                    }
                }
            } else if (c === delim) {
                break; // delimiter found.
            } else if (isNewLine(reader.peek())) {
                tt = Tokens.INVALID;
                break;
            }
            c = reader.read();
        }
        if (c === null) {
            tt = Tokens.INVALID;
        }

        return this.createToken(tt, string, startLine, startCol);
    },

    unicodeRangeToken: function(first, startLine, startCol) {
        var reader  = this._reader,
            value   = first,
            temp,
            tt      = Tokens.CHAR;
        if (reader.peek() === "+") {
            reader.mark();
            value += reader.read();
            value += this.readUnicodeRangePart(true);
            if (value.length === 2) {
                reader.reset();
            } else {

                tt = Tokens.UNICODE_RANGE;
                if (value.indexOf("?") === -1) {

                    if (reader.peek() === "-") {
                        reader.mark();
                        temp = reader.read();
                        temp += this.readUnicodeRangePart(false);
                        if (temp.length === 1) {
                            reader.reset();
                        } else {
                            value += temp;
                        }
                    }

                }
            }
        }

        return this.createToken(tt, value, startLine, startCol);
    },
    whitespaceToken: function(first, startLine, startCol) {
        var value   = first + this.readWhitespace();
        return this.createToken(Tokens.S, value, startLine, startCol);
    },

    readUnicodeRangePart: function(allowQuestionMark) {
        var reader  = this._reader,
            part = "",
            c       = reader.peek();
        while (isHexDigit(c) && part.length < 6) {
            reader.read();
            part += c;
            c = reader.peek();
        }
        if (allowQuestionMark) {
            while (c === "?" && part.length < 6) {
                reader.read();
                part += c;
                c = reader.peek();
            }
        }

        return part;
    },

    readWhitespace: function() {
        var reader  = this._reader,
            whitespace = "",
            c       = reader.peek();

        while (isWhitespace(c)) {
            reader.read();
            whitespace += c;
            c = reader.peek();
        }

        return whitespace;
    },
    readNumber: function(first) {
        var reader  = this._reader,
            number  = first,
            hasDot  = first === ".",
            c       = reader.peek();


        while (c) {
            if (isDigit(c)) {
                number += reader.read();
            } else if (c === ".") {
                if (hasDot) {
                    break;
                } else {
                    hasDot = true;
                    number += reader.read();
                }
            } else {
                break;
            }

            c = reader.peek();
        }

        return number;
    },
    readString: function() {
        var token = this.stringToken(this._reader.read(), 0, 0);
        return token.type === Tokens.INVALID ? null : token.value;
    },
    readURI: function(first) {
        var reader  = this._reader,
            uri     = first,
            inner   = "",
            c       = reader.peek();
        while (c && isWhitespace(c)) {
            reader.read();
            c = reader.peek();
        }
        if (c === "'" || c === "\"") {
            inner = this.readString();
            if (inner !== null) {
                inner = PropertyValuePart.parseString(inner);
            }
        } else {
            inner = this.readUnquotedURL();
        }

        c = reader.peek();
        while (c && isWhitespace(c)) {
            reader.read();
            c = reader.peek();
        }
        if (inner === null || c !== ")") {
            uri = null;
        } else {
            uri += PropertyValuePart.serializeString(inner) + reader.read();
        }

        return uri;
    },
    readUnquotedURL: function(first) {
        var reader  = this._reader,
            url     = first || "",
            c;

        for (c = reader.peek(); c; c = reader.peek()) {
            if (nonascii.test(c) || /^[-!#$%&*-[\]-~]$/.test(c)) {
                url += c;
                reader.read();
            } else if (c === "\\") {
                if (/^[^\r\n\f]$/.test(reader.peek(2))) {
                    url += this.readEscape(reader.read(), true);
                } else {
                    break; // bad escape sequence.
                }
            } else {
                break; // bad character
            }
        }

        return url;
    },

    readName: function(first) {
        var reader  = this._reader,
            ident   = first || "",
            c;

        for (c = reader.peek(); c; c = reader.peek()) {
            if (c === "\\") {
                if (/^[^\r\n\f]$/.test(reader.peek(2))) {
                    ident += this.readEscape(reader.read(), true);
                } else {
                    break;
                }
            } else if (isNameChar(c)) {
                ident += reader.read();
            } else {
                break;
            }
        }

        return ident;
    },

    readEscape: function(first, unescape) {
        var reader  = this._reader,
            cssEscape = first || "",
            i       = 0,
            c       = reader.peek();

        if (isHexDigit(c)) {
            do {
                cssEscape += reader.read();
                c = reader.peek();
            } while (c && isHexDigit(c) && ++i < 6);
        }

        if (cssEscape.length === 1) {
            if (/^[^\r\n\f0-9a-f]$/.test(c)) {
                reader.read();
                if (unescape) {
                    return c;
                }
            } else {
                throw new Error("Bad escape sequence.");
            }
        } else if (c === "\r") {
            reader.read();
            if (reader.peek() === "\n") {
                c += reader.read();
            }
        } else if (/^[ \t\n\f]$/.test(c)) {
            reader.read();
        } else {
            c = "";
        }

        if (unescape) {
            var cp = parseInt(cssEscape.slice(first.length), 16);
            return String.fromCodePoint ? String.fromCodePoint(cp) :
                String.fromCharCode(cp);
        }
        return cssEscape + c;
    },

    readComment: function(first) {
        var reader  = this._reader,
            comment = first || "",
            c       = reader.read();

        if (c === "*") {
            while (c) {
                comment += c;
                if (comment.length > 2 && c === "*" && reader.peek() === "/") {
                    comment += reader.read();
                    break;
                }

                c = reader.read();
            }

            return comment;
        } else {
            return "";
        }

    }
});

},{"../util/TokenStreamBase":27,"./PropertyValuePart":11,"./Tokens":18}],18:[function(require,module,exports){
"use strict";

var Tokens = module.exports = [
    { name: "CDO" },
    { name: "CDC" },
    { name: "S", whitespace: true/*, channel: "ws"*/ },
    { name: "COMMENT", comment: true, hide: true, channel: "comment" },
    { name: "INCLUDES", text: "~=" },
    { name: "DASHMATCH", text: "|=" },
    { name: "PREFIXMATCH", text: "^=" },
    { name: "SUFFIXMATCH", text: "$=" },
    { name: "SUBSTRINGMATCH", text: "*=" },
    { name: "STRING" },
    { name: "IDENT" },
    { name: "HASH" },
    { name: "IMPORT_SYM", text: "@import" },
    { name: "PAGE_SYM", text: "@page" },
    { name: "MEDIA_SYM", text: "@media" },
    { name: "FONT_FACE_SYM", text: "@font-face" },
    { name: "CHARSET_SYM", text: "@charset" },
    { name: "NAMESPACE_SYM", text: "@namespace" },
    { name: "SUPPORTS_SYM", text: "@supports" },
    { name: "VIEWPORT_SYM", text: ["@viewport", "@-ms-viewport", "@-o-viewport"] },
    { name: "DOCUMENT_SYM", text: ["@document", "@-moz-document"] },
    { name: "UNKNOWN_SYM" },
    { name: "KEYFRAMES_SYM", text: [ "@keyframes", "@-webkit-keyframes", "@-moz-keyframes", "@-o-keyframes" ] },
    { name: "IMPORTANT_SYM" },
    { name: "LENGTH" },
    { name: "ANGLE" },
    { name: "TIME" },
    { name: "FREQ" },
    { name: "DIMENSION" },
    { name: "PERCENTAGE" },
    { name: "NUMBER" },
    { name: "URI" },
    { name: "FUNCTION" },
    { name: "UNICODE_RANGE" },
    { name: "INVALID" },
    { name: "PLUS", text: "+" },
    { name: "GREATER", text: ">" },
    { name: "COMMA", text: "," },
    { name: "TILDE", text: "~" },
    { name: "NOT" },
    { name: "TOPLEFTCORNER_SYM", text: "@top-left-corner" },
    { name: "TOPLEFT_SYM", text: "@top-left" },
    { name: "TOPCENTER_SYM", text: "@top-center" },
    { name: "TOPRIGHT_SYM", text: "@top-right" },
    { name: "TOPRIGHTCORNER_SYM", text: "@top-right-corner" },
    { name: "BOTTOMLEFTCORNER_SYM", text: "@bottom-left-corner" },
    { name: "BOTTOMLEFT_SYM", text: "@bottom-left" },
    { name: "BOTTOMCENTER_SYM", text: "@bottom-center" },
    { name: "BOTTOMRIGHT_SYM", text: "@bottom-right" },
    { name: "BOTTOMRIGHTCORNER_SYM", text: "@bottom-right-corner" },
    { name: "LEFTTOP_SYM", text: "@left-top" },
    { name: "LEFTMIDDLE_SYM", text: "@left-middle" },
    { name: "LEFTBOTTOM_SYM", text: "@left-bottom" },
    { name: "RIGHTTOP_SYM", text: "@right-top" },
    { name: "RIGHTMIDDLE_SYM", text: "@right-middle" },
    { name: "RIGHTBOTTOM_SYM", text: "@right-bottom" },
    { name: "RESOLUTION", state: "media" },
    { name: "IE_FUNCTION" },
    { name: "CHAR" },
    {
        name: "PIPE",
        text: "|"
    },
    {
        name: "SLASH",
        text: "/"
    },
    {
        name: "MINUS",
        text: "-"
    },
    {
        name: "STAR",
        text: "*"
    },

    {
        name: "LBRACE",
        endChar: "}",
        text: "{"
    },
    {
        name: "RBRACE",
        text: "}"
    },
    {
        name: "LBRACKET",
        endChar: "]",
        text: "["
    },
    {
        name: "RBRACKET",
        text: "]"
    },
    {
        name: "EQUALS",
        text: "="
    },
    {
        name: "COLON",
        text: ":"
    },
    {
        name: "SEMICOLON",
        text: ";"
    },
    {
        name: "LPAREN",
        endChar: ")",
        text: "("
    },
    {
        name: "RPAREN",
        text: ")"
    },
    {
        name: "DOT",
        text: "."
    }
];

(function() {
    var nameMap = [],
        typeMap = Object.create(null);

    Tokens.UNKNOWN = -1;
    Tokens.unshift({ name:"EOF" });
    for (var i = 0, len = Tokens.length; i < len; i++) {
        nameMap.push(Tokens[i].name);
        Tokens[Tokens[i].name] = i;
        if (Tokens[i].text) {
            if (Tokens[i].text instanceof Array) {
                for (var j = 0; j < Tokens[i].text.length; j++) {
                    typeMap[Tokens[i].text[j]] = i;
                }
            } else {
                typeMap[Tokens[i].text] = i;
            }
        }
    }

    Tokens.name = function(tt) {
        return nameMap[tt];
    };

    Tokens.type = function(c) {
        return typeMap[c] || -1;
    };
})();

},{}],19:[function(require,module,exports){

"use strict";

var Matcher = require("./Matcher");
var Properties = require("./Properties");
var ValidationTypes = require("./ValidationTypes");
var ValidationError = require("./ValidationError");
var PropertyValueIterator = require("./PropertyValueIterator");

var Validation = module.exports = {

    validate: function(property, value) {
        var name        = property.toString().toLowerCase(),
            expression  = new PropertyValueIterator(value),
            spec        = Properties[name],
            part;

        if (!spec) {
            if (name.indexOf("-") !== 0) {    // vendor prefixed are ok
                throw new ValidationError("Unknown property '" + property + "'.", property.line, property.col);
            }
        } else if (typeof spec !== "number") {
            if (ValidationTypes.isAny(expression, "inherit | initial | unset")) {
                if (expression.hasNext()) {
                    part = expression.next();
                    throw new ValidationError("Expected end of value but found '" + part + "'.", part.line, part.col);
                }
                return;
            }
            this.singleProperty(spec, expression);

        }

    },

    singleProperty: function(types, expression) {

        var result      = false,
            value       = expression.value,
            part;

        result = Matcher.parse(types).match(expression);

        if (!result) {
            if (expression.hasNext() && !expression.isFirst()) {
                part = expression.peek();
                throw new ValidationError("Expected end of value but found '" + part + "'.", part.line, part.col);
            } else {
                throw new ValidationError("Expected (" + ValidationTypes.describe(types) + ") but found '" + value + "'.", value.line, value.col);
            }
        } else if (expression.hasNext()) {
            part = expression.next();
            throw new ValidationError("Expected end of value but found '" + part + "'.", part.line, part.col);
        }

    }

};

},{"./Matcher":3,"./Properties":7,"./PropertyValueIterator":10,"./ValidationError":20,"./ValidationTypes":21}],20:[function(require,module,exports){
"use strict";

module.exports = ValidationError;
function ValidationError(message, line, col) {
    this.col = col;
    this.line = line;
    this.message = message;

}
ValidationError.prototype = new Error();

},{}],21:[function(require,module,exports){
"use strict";

var ValidationTypes = module.exports;

var Matcher = require("./Matcher");

function copy(to, from) {
    Object.keys(from).forEach(function(prop) {
        to[prop] = from[prop];
    });
}
copy(ValidationTypes, {

    isLiteral: function (part, literals) {
        var text = part.text.toString().toLowerCase(),
            args = literals.split(" | "),
            i,
            len,
            found = false;

        for (i = 0, len = args.length; i < len && !found; i++) {
            if (args[i].charAt(0) === "<") {
                found = this.simple[args[i]](part);
            } else if (args[i].slice(-2) === "()") {
                found = (part.type === "function" &&
                         part.name === args[i].slice(0, -2));
            } else if (text === args[i].toLowerCase()) {
                found = true;
            }
        }

        return found;
    },

    isSimple: function(type) {
        return Boolean(this.simple[type]);
    },

    isComplex: function(type) {
        return Boolean(this.complex[type]);
    },

    describe: function(type) {
        if (this.complex[type] instanceof Matcher) {
            return this.complex[type].toString(0);
        }
        return type;
    },
    isAny: function (expression, types) {
        var args = types.split(" | "),
            i,
            len,
            found = false;

        for (i = 0, len = args.length; i < len && !found && expression.hasNext(); i++) {
            found = this.isType(expression, args[i]);
        }

        return found;
    },
    isAnyOfGroup: function(expression, types) {
        var args = types.split(" || "),
            i,
            len,
            found = false;

        for (i = 0, len = args.length; i < len && !found; i++) {
            found = this.isType(expression, args[i]);
        }

        return found ? args[i - 1] : false;
    },
    isType: function (expression, type) {
        var part = expression.peek(),
            result = false;

        if (type.charAt(0) !== "<") {
            result = this.isLiteral(part, type);
            if (result) {
                expression.next();
            }
        } else if (this.simple[type]) {
            result = this.simple[type](part);
            if (result) {
                expression.next();
            }
        } else if (this.complex[type] instanceof Matcher) {
            result = this.complex[type].match(expression);
        } else {
            result = this.complex[type](expression);
        }

        return result;
    },


    simple: {
        __proto__: null,

        "<absolute-size>":
            "xx-small | x-small | small | medium | large | x-large | xx-large",

        "<animateable-feature>":
            "scroll-position | contents | <animateable-feature-name>",

        "<animateable-feature-name>": function(part) {
            return this["<ident>"](part) &&
                !/^(unset|initial|inherit|will-change|auto|scroll-position|contents)$/i.test(part);
        },

        "<angle>": function(part) {
            return part.type === "angle";
        },

        "<attachment>": "scroll | fixed | local",

        "<attr>": "attr()",
        "<basic-shape>": "inset() | circle() | ellipse() | polygon()",

        "<bg-image>": "<image> | <gradient> | none",

        "<border-style>":
            "none | hidden | dotted | dashed | solid | double | groove | " +
            "ridge | inset | outset",

        "<border-width>": "<length> | thin | medium | thick",

        "<box>": "padding-box | border-box | content-box",

        "<clip-source>": "<uri>",

        "<color>": function(part) {
            return part.type === "color" || String(part) === "transparent" || String(part) === "currentColor";
        },
        "<color-svg>": function(part) {
            return part.type === "color";
        },

        "<content>": "content()",
        "<content-sizing>":
            "fill-available | -moz-available | -webkit-fill-available | " +
            "max-content | -moz-max-content | -webkit-max-content | " +
            "min-content | -moz-min-content | -webkit-min-content | " +
            "fit-content | -moz-fit-content | -webkit-fit-content",

        "<feature-tag-value>": function(part) {
            return part.type === "function" && /^[A-Z0-9]{4}$/i.test(part);
        },
        "<filter-function>":
            "blur() | brightness() | contrast() | custom() | " +
            "drop-shadow() | grayscale() | hue-rotate() | invert() | " +
            "opacity() | saturate() | sepia()",

        "<flex-basis>": "<width>",

        "<flex-direction>": "row | row-reverse | column | column-reverse",

        "<flex-grow>": "<number>",

        "<flex-shrink>": "<number>",

        "<flex-wrap>": "nowrap | wrap | wrap-reverse",

        "<font-size>":
            "<absolute-size> | <relative-size> | <length> | <percentage>",

        "<font-stretch>":
            "normal | ultra-condensed | extra-condensed | condensed | " +
            "semi-condensed | semi-expanded | expanded | extra-expanded | " +
            "ultra-expanded",

        "<font-style>": "normal | italic | oblique",

        "<font-variant-caps>":
            "small-caps | all-small-caps | petite-caps | all-petite-caps | " +
            "unicase | titling-caps",

        "<font-variant-css21>": "normal | small-caps",

        "<font-weight>":
            "normal | bold | bolder | lighter | " +
            "100 | 200 | 300 | 400 | 500 | 600 | 700 | 800 | 900",

        "<generic-family>":
            "serif | sans-serif | cursive | fantasy | monospace",

        "<geometry-box>": "<shape-box> | fill-box | stroke-box | view-box",

        "<glyph-angle>": function(part) {
            return part.type === "angle" && part.units === "deg";
        },

        "<gradient>": function(part) {
            return part.type === "function" && /^(?:-(?:ms|moz|o|webkit)-)?(?:repeating-)?(?:radial-|linear-)?gradient/i.test(part);
        },

        "<icccolor>":
            "cielab() | cielch() | cielchab() | " +
            "icc-color() | icc-named-color()",
        "<ident>": function(part) {
            return part.type === "identifier" || part.wasIdent;
        },

        "<ident-not-generic-family>": function(part) {
            return this["<ident>"](part) && !this["<generic-family>"](part);
        },

        "<image>": "<uri>",

        "<integer>": function(part) {
            return part.type === "integer";
        },

        "<length>": function(part) {
            if (part.type === "function" && /^(?:-(?:ms|moz|o|webkit)-)?calc/i.test(part)) {
                return true;
            } else {
                return part.type === "length" || part.type === "number" || part.type === "integer" || String(part) === "0";
            }
        },

        "<line>": function(part) {
            return part.type === "integer";
        },

        "<line-height>": "<number> | <length> | <percentage> | normal",

        "<margin-width>": "<length> | <percentage> | auto",

        "<miterlimit>": function(part) {
            return this["<number>"](part) && part.value >= 1;
        },

        "<nonnegative-length-or-percentage>": function(part) {
            return (this["<length>"](part) || this["<percentage>"](part)) &&
                (String(part) === "0" || part.type === "function" || (part.value) >= 0);
        },

        "<nonnegative-number-or-percentage>": function(part) {
            return (this["<number>"](part) || this["<percentage>"](part)) &&
                (String(part) === "0" || part.type === "function" || (part.value) >= 0);
        },

        "<number>": function(part) {
            return part.type === "number" || this["<integer>"](part);
        },

        "<opacity-value>": function(part) {
            return this["<number>"](part) && part.value >= 0 && part.value <= 1;
        },

        "<padding-width>": "<nonnegative-length-or-percentage>",

        "<percentage>": function(part) {
            return part.type === "percentage" || String(part) === "0";
        },

        "<relative-size>": "smaller | larger",

        "<shape>": "rect() | inset-rect()",

        "<shape-box>": "<box> | margin-box",

        "<single-animation-direction>":
            "normal | reverse | alternate | alternate-reverse",

        "<single-animation-name>": function(part) {
            return this["<ident>"](part) &&
                /^-?[a-z_][-a-z0-9_]+$/i.test(part) &&
                !/^(none|unset|initial|inherit)$/i.test(part);
        },

        "<string>": function(part) {
            return part.type === "string";
        },

        "<time>": function(part) {
            return part.type === "time";
        },

        "<uri>": function(part) {
            return part.type === "uri";
        },

        "<width>": "<margin-width>"
    },

    complex: {
        __proto__: null,

        "<azimuth>":
            "<angle>" +
            " | " +
            "[ [ left-side | far-left | left | center-left | center | " +
            "center-right | right | far-right | right-side ] || behind ]" +
            " | " +
            "leftwards | rightwards",

        "<bg-position>": "<position>#",

        "<bg-size>":
            "[ <length> | <percentage> | auto ]{1,2} | cover | contain",

        "<blend-mode>":
            "normal | multiply | screen | overlay | darken | lighten | color-dodge | " +
            "color-burn | hard-light | soft-light | difference | exclusion | hue | " +
            "saturation | color | luminosity",

        "<border-image-slice>":
        Matcher.many([true /* first element is required */],
                     Matcher.cast("<nonnegative-number-or-percentage>"),
                     Matcher.cast("<nonnegative-number-or-percentage>"),
                     Matcher.cast("<nonnegative-number-or-percentage>"),
                     Matcher.cast("<nonnegative-number-or-percentage>"),
                     "fill"),

        "<border-radius>":
            "<nonnegative-length-or-percentage>{1,4} " +
            "[ / <nonnegative-length-or-percentage>{1,4} ]?",

        "<box-shadow>": "none | <shadow>#",

        "<clip-path>": "<basic-shape> || <geometry-box>",

        "<dasharray>":
        Matcher.cast("<nonnegative-length-or-percentage>")
            .braces(1, Infinity, "#", Matcher.cast(",").question()),

        "<family-name>":
            "<string> | <ident-not-generic-family> <ident>*",

        "<filter-function-list>": "[ <filter-function> | <uri> ]+",
        "<flex>":
            "none | [ <flex-grow> <flex-shrink>? || <flex-basis> ]",

        "<font-family>": "[ <generic-family> | <family-name> ]#",

        "<font-shorthand>":
            "[ <font-style> || <font-variant-css21> || " +
            "<font-weight> || <font-stretch> ]? <font-size> " +
            "[ / <line-height> ]? <font-family>",

        "<font-variant-alternates>":
            "stylistic() || " +
            "historical-forms || " +
            "styleset() || " +
            "character-variant() || " +
            "swash() || " +
            "ornaments() || " +
            "annotation()",

        "<font-variant-ligatures>":
            "[ common-ligatures | no-common-ligatures ] || " +
            "[ discretionary-ligatures | no-discretionary-ligatures ] || " +
            "[ historical-ligatures | no-historical-ligatures ] || " +
            "[ contextual | no-contextual ]",

        "<font-variant-numeric>":
            "[ lining-nums | oldstyle-nums ] || " +
            "[ proportional-nums | tabular-nums ] || " +
            "[ diagonal-fractions | stacked-fractions ] || " +
            "ordinal || slashed-zero",

        "<font-variant-east-asian>":
            "[ jis78 | jis83 | jis90 | jis04 | simplified | traditional ] || " +
            "[ full-width | proportional-width ] || " +
            "ruby",
        "<paint>": "<paint-basic> | <uri> <paint-basic>?",
        "<paint-basic>": "none | currentColor | <color-svg> <icccolor>?",

        "<position>":
            "[ center | [ left | right ] [ <percentage> | <length> ]? ] && " +
            "[ center | [ top | bottom ] [ <percentage> | <length> ]? ]" +
            " | " +
            "[ left | center | right | <percentage> | <length> ] " +
            "[ top | center | bottom | <percentage> | <length> ]" +
            " | " +
            "[ left | center | right | top | bottom | <percentage> | <length> ]",

        "<repeat-style>":
            "repeat-x | repeat-y | [ repeat | space | round | no-repeat ]{1,2}",

        "<shadow>":
        Matcher.many([true /* length is required */],
                     Matcher.cast("<length>").braces(2, 4), "inset", "<color>"),

        "<text-decoration-color>":
           "<color>",

        "<text-decoration-line>":
            "none | [ underline || overline || line-through || blink ]",

        "<text-decoration-style>":
            "solid | double | dotted | dashed | wavy",

        "<will-change>":
            "auto | <animateable-feature>#",

        "<x-one-radius>":
            "[ <length> | <percentage> ]{1,2}"
    }
});

Object.keys(ValidationTypes.simple).forEach(function(nt) {
    var rule = ValidationTypes.simple[nt];
    if (typeof rule === "string") {
        ValidationTypes.simple[nt] = function(part) {
            return ValidationTypes.isLiteral(part, rule);
        };
    }
});

Object.keys(ValidationTypes.complex).forEach(function(nt) {
    var rule = ValidationTypes.complex[nt];
    if (typeof rule === "string") {
        ValidationTypes.complex[nt] = Matcher.parse(rule);
    }
});
ValidationTypes.complex["<font-variant>"] =
    Matcher.oror({ expand: "<font-variant-ligatures>" },
                 { expand: "<font-variant-alternates>" },
                 "<font-variant-caps>",
                 { expand: "<font-variant-numeric>" },
                 { expand: "<font-variant-east-asian>" });

},{"./Matcher":3}],22:[function(require,module,exports){
"use strict";

module.exports = {
    Colors            : require("./Colors"),
    Combinator        : require("./Combinator"),
    Parser            : require("./Parser"),
    PropertyName      : require("./PropertyName"),
    PropertyValue     : require("./PropertyValue"),
    PropertyValuePart : require("./PropertyValuePart"),
    Matcher           : require("./Matcher"),
    MediaFeature      : require("./MediaFeature"),
    MediaQuery        : require("./MediaQuery"),
    Selector          : require("./Selector"),
    SelectorPart      : require("./SelectorPart"),
    SelectorSubPart   : require("./SelectorSubPart"),
    Specificity       : require("./Specificity"),
    TokenStream       : require("./TokenStream"),
    Tokens            : require("./Tokens"),
    ValidationError   : require("./ValidationError")
};

},{"./Colors":1,"./Combinator":2,"./Matcher":3,"./MediaFeature":4,"./MediaQuery":5,"./Parser":6,"./PropertyName":8,"./PropertyValue":9,"./PropertyValuePart":11,"./Selector":13,"./SelectorPart":14,"./SelectorSubPart":15,"./Specificity":16,"./TokenStream":17,"./Tokens":18,"./ValidationError":20}],23:[function(require,module,exports){
"use strict";

module.exports = EventTarget;
function EventTarget() {
    this._listeners = Object.create(null);
}

EventTarget.prototype = {
    constructor: EventTarget,
    addListener: function(type, listener) {
        if (!this._listeners[type]) {
            this._listeners[type] = [];
        }

        this._listeners[type].push(listener);
    },
    fire: function(event) {
        if (typeof event === "string") {
            event = { type: event };
        }
        if (typeof event.target !== "undefined") {
            event.target = this;
        }

        if (typeof event.type === "undefined") {
            throw new Error("Event object missing 'type' property.");
        }

        if (this._listeners[event.type]) {
            var listeners = this._listeners[event.type].concat();
            for (var i = 0, len = listeners.length; i < len; i++) {
                listeners[i].call(this, event);
            }
        }
    },
    removeListener: function(type, listener) {
        if (this._listeners[type]) {
            var listeners = this._listeners[type];
            for (var i = 0, len = listeners.length; i < len; i++) {
                if (listeners[i] === listener) {
                    listeners.splice(i, 1);
                    break;
                }
            }


        }
    }
};

},{}],24:[function(require,module,exports){
"use strict";

module.exports = StringReader;
function StringReader(text) {
    this._input = text.replace(/(\r\n?|\n)/g, "\n");
    this._line = 1;
    this._col = 1;
    this._cursor = 0;
}

StringReader.prototype = {
    constructor: StringReader,
    getCol: function() {
        return this._col;
    },
    getLine: function() {
        return this._line;
    },
    eof: function() {
        return this._cursor === this._input.length;
    },
    peek: function(count) {
        var c = null;
        count = typeof count === "undefined" ? 1 : count;
        if (this._cursor < this._input.length) {
            c = this._input.charAt(this._cursor + count - 1);
        }

        return c;
    },
    read: function() {
        var c = null;
        if (this._cursor < this._input.length) {
            if (this._input.charAt(this._cursor) === "\n") {
                this._line++;
                this._col = 1;
            } else {
                this._col++;
            }
            c = this._input.charAt(this._cursor++);
        }

        return c;
    },
    mark: function() {
        this._bookmark = {
            cursor: this._cursor,
            line:   this._line,
            col:    this._col
        };
    },

    reset: function() {
        if (this._bookmark) {
            this._cursor = this._bookmark.cursor;
            this._line = this._bookmark.line;
            this._col = this._bookmark.col;
            delete this._bookmark;
        }
    },
    peekCount: function(count) {
        count = typeof count === "undefined" ? 1 : Math.max(count, 0);
        return this._input.substring(this._cursor, this._cursor + count);
    },
    readTo: function(pattern) {

        var buffer = "",
            c;
        while (buffer.length < pattern.length || buffer.lastIndexOf(pattern) !== buffer.length - pattern.length) {
            c = this.read();
            if (c) {
                buffer += c;
            } else {
                throw new Error("Expected \"" + pattern + "\" at line " + this._line  + ", col " + this._col + ".");
            }
        }

        return buffer;

    },
    readWhile: function(filter) {

        var buffer = "",
            c = this.peek();

        while (c !== null && filter(c)) {
            buffer += this.read();
            c = this.peek();
        }

        return buffer;

    },
    readMatch: function(matcher) {

        var source = this._input.substring(this._cursor),
            value = null;
        if (typeof matcher === "string") {
            if (source.slice(0, matcher.length) === matcher) {
                value = this.readCount(matcher.length);
            }
        } else if (matcher instanceof RegExp) {
            if (matcher.test(source)) {
                value = this.readCount(RegExp.lastMatch.length);
            }
        }

        return value;
    },
    readCount: function(count) {
        var buffer = "";

        while (count--) {
            buffer += this.read();
        }

        return buffer;
    }

};

},{}],25:[function(require,module,exports){
"use strict";

module.exports = SyntaxError;
function SyntaxError(message, line, col) {
    Error.call(this);
    this.name = this.constructor.name;
    this.col = col;
    this.line = line;
    this.message = message;

}
SyntaxError.prototype = Object.create(Error.prototype); // jshint ignore:line
SyntaxError.prototype.constructor = SyntaxError; // jshint ignore:line

},{}],26:[function(require,module,exports){
"use strict";

module.exports = SyntaxUnit;
function SyntaxUnit(text, line, col, type) {
    this.col = col;
    this.line = line;
    this.text = text;
    this.type = type;
}
SyntaxUnit.fromToken = function(token) {
    return new SyntaxUnit(token.value, token.startLine, token.startCol);
};

SyntaxUnit.prototype = {
    constructor: SyntaxUnit,
    valueOf: function() {
        return this.toString();
    },
    toString: function() {
        return this.text;
    }

};

},{}],27:[function(require,module,exports){
"use strict";

module.exports = TokenStreamBase;

var StringReader = require("./StringReader");
var SyntaxError = require("./SyntaxError");
function TokenStreamBase(input, tokenData) {
    this._reader = new StringReader(input ? input.toString() : "");
    this._token = null;
    this._tokenData = tokenData;
    this._lt = [];
    this._ltIndex = 0;

    this._ltIndexCache = [];
}
TokenStreamBase.createTokenData = function(tokens) {

    var nameMap     = [],
        typeMap     = Object.create(null),
        tokenData   = tokens.concat([]),
        i           = 0,
        len         = tokenData.length + 1;

    tokenData.UNKNOWN = -1;
    tokenData.unshift({ name:"EOF" });

    for (; i < len; i++) {
        nameMap.push(tokenData[i].name);
        tokenData[tokenData[i].name] = i;
        if (tokenData[i].text) {
            typeMap[tokenData[i].text] = i;
        }
    }

    tokenData.name = function(tt) {
        return nameMap[tt];
    };

    tokenData.type = function(c) {
        return typeMap[c];
    };

    return tokenData;
};

TokenStreamBase.prototype = {
    constructor: TokenStreamBase,
    match: function(tokenTypes, channel) {
        if (!(tokenTypes instanceof Array)) {
            tokenTypes = [tokenTypes];
        }

        var tt  = this.get(channel),
            i   = 0,
            len = tokenTypes.length;

        while (i < len) {
            if (tt === tokenTypes[i++]) {
                return true;
            }
        }
        this.unget();
        return false;
    },
    mustMatch: function(tokenTypes) {

        var token;
        if (!(tokenTypes instanceof Array)) {
            tokenTypes = [tokenTypes];
        }

        if (!this.match.apply(this, arguments)) {
            token = this.LT(1);
            throw new SyntaxError("Expected " + this._tokenData[tokenTypes[0]].name +
                " at line " + token.startLine + ", col " + token.startCol + ".", token.startLine, token.startCol);
        }
    },
    advance: function(tokenTypes, channel) {

        while (this.LA(0) !== 0 && !this.match(tokenTypes, channel)) {
            this.get();
        }

        return this.LA(0);
    },
    get: function(channel) {

        var tokenInfo = this._tokenData,
            i         = 0,
            token,
            info;
        if (this._lt.length && this._ltIndex >= 0 && this._ltIndex < this._lt.length) {

            i++;
            this._token = this._lt[this._ltIndex++];
            info = tokenInfo[this._token.type];
            while ((typeof info.channel !== "undefined" && channel !== info.channel) &&
                    this._ltIndex < this._lt.length) {
                this._token = this._lt[this._ltIndex++];
                info = tokenInfo[this._token.type];
                i++;
            }
            if ((typeof info.channel === "undefined" || channel === info.channel) &&
                    this._ltIndex <= this._lt.length) {
                this._ltIndexCache.push(i);
                return this._token.type;
            }
        }
        token = this._getToken();
        if (token.type > -1 && !tokenInfo[token.type].hide) {
            token.channel = tokenInfo[token.type].channel;
            this._token = token;
            this._lt.push(token);
            this._ltIndexCache.push(this._lt.length - this._ltIndex + i);
            if (this._lt.length > 5) {
                this._lt.shift();
            }
            if (this._ltIndexCache.length > 5) {
                this._ltIndexCache.shift();
            }
            this._ltIndex = this._lt.length;
        }
        info = tokenInfo[token.type];
        if (info &&
                (info.hide ||
                (typeof info.channel !== "undefined" && channel !== info.channel))) {
            return this.get(channel);
        } else {
            return token.type;
        }
    },
    LA: function(index) {
        var total = index,
            tt;
        if (index > 0) {
            if (index > 5) {
                throw new Error("Too much lookahead.");
            }
            while (total) {
                tt = this.get();
                total--;
            }
            while (total < index) {
                this.unget();
                total++;
            }
        } else if (index < 0) {

            if (this._lt[this._ltIndex + index]) {
                tt = this._lt[this._ltIndex + index].type;
            } else {
                throw new Error("Too much lookbehind.");
            }

        } else {
            tt = this._token.type;
        }

        return tt;

    },
    LT: function(index) {
        this.LA(index);
        return this._lt[this._ltIndex + index - 1];
    },
    peek: function() {
        return this.LA(1);
    },
    token: function() {
        return this._token;
    },
    tokenName: function(tokenType) {
        if (tokenType < 0 || tokenType > this._tokenData.length) {
            return "UNKNOWN_TOKEN";
        } else {
            return this._tokenData[tokenType].name;
        }
    },
    tokenType: function(tokenName) {
        return this._tokenData[tokenName] || -1;
    },
    unget: function() {
        if (this._ltIndexCache.length) {
            this._ltIndex -= this._ltIndexCache.pop();//--;
            this._token = this._lt[this._ltIndex - 1];
        } else {
            throw new Error("Too much lookahead.");
        }
    }

};


},{"./StringReader":24,"./SyntaxError":25}],28:[function(require,module,exports){
"use strict";

module.exports = {
    StringReader    : require("./StringReader"),
    SyntaxError     : require("./SyntaxError"),
    SyntaxUnit      : require("./SyntaxUnit"),
    EventTarget     : require("./EventTarget"),
    TokenStreamBase : require("./TokenStreamBase")
};

},{"./EventTarget":23,"./StringReader":24,"./SyntaxError":25,"./SyntaxUnit":26,"./TokenStreamBase":27}],"parserlib":[function(require,module,exports){
"use strict";

module.exports = {
    css  : require("./css"),
    util : require("./util")
};

},{"./css":22,"./util":28}]},{},[]);

return require('parserlib');
})();
var clone = (function() {
'use strict';

function _instanceof(obj, type) {
  return type != null && obj instanceof type;
}

var nativeMap;
try {
  nativeMap = Map;
} catch(_) {
  nativeMap = function() {};
}

var nativeSet;
try {
  nativeSet = Set;
} catch(_) {
  nativeSet = function() {};
}

var nativePromise;
try {
  nativePromise = Promise;
} catch(_) {
  nativePromise = function() {};
}
function clone(parent, circular, depth, prototype, includeNonEnumerable) {
  if (typeof circular === 'object') {
    depth = circular.depth;
    prototype = circular.prototype;
    includeNonEnumerable = circular.includeNonEnumerable;
    circular = circular.circular;
  }
  var allParents = [];
  var allChildren = [];

  var useBuffer = typeof Buffer != 'undefined';

  if (typeof circular == 'undefined')
    circular = true;

  if (typeof depth == 'undefined')
    depth = Infinity;
  function _clone(parent, depth) {
    if (parent === null)
      return null;

    if (depth === 0)
      return parent;

    var child;
    var proto;
    if (typeof parent != 'object') {
      return parent;
    }

    if (_instanceof(parent, nativeMap)) {
      child = new nativeMap();
    } else if (_instanceof(parent, nativeSet)) {
      child = new nativeSet();
    } else if (_instanceof(parent, nativePromise)) {
      child = new nativePromise(function (resolve, reject) {
        parent.then(function(value) {
          resolve(_clone(value, depth - 1));
        }, function(err) {
          reject(_clone(err, depth - 1));
        });
      });
    } else if (clone.__isArray(parent)) {
      child = [];
    } else if (clone.__isRegExp(parent)) {
      child = new RegExp(parent.source, __getRegExpFlags(parent));
      if (parent.lastIndex) child.lastIndex = parent.lastIndex;
    } else if (clone.__isDate(parent)) {
      child = new Date(parent.getTime());
    } else if (useBuffer && Buffer.isBuffer(parent)) {
      if (Buffer.allocUnsafe) {
        child = Buffer.allocUnsafe(parent.length);
      } else {
        child = new Buffer(parent.length);
      }
      parent.copy(child);
      return child;
    } else if (_instanceof(parent, Error)) {
      child = Object.create(parent);
    } else {
      if (typeof prototype == 'undefined') {
        proto = Object.getPrototypeOf(parent);
        child = Object.create(proto);
      }
      else {
        child = Object.create(prototype);
        proto = prototype;
      }
    }

    if (circular) {
      var index = allParents.indexOf(parent);

      if (index != -1) {
        return allChildren[index];
      }
      allParents.push(parent);
      allChildren.push(child);
    }

    if (_instanceof(parent, nativeMap)) {
      parent.forEach(function(value, key) {
        var keyChild = _clone(key, depth - 1);
        var valueChild = _clone(value, depth - 1);
        child.set(keyChild, valueChild);
      });
    }
    if (_instanceof(parent, nativeSet)) {
      parent.forEach(function(value) {
        var entryChild = _clone(value, depth - 1);
        child.add(entryChild);
      });
    }

    for (var i in parent) {
      var attrs;
      if (proto) {
        attrs = Object.getOwnPropertyDescriptor(proto, i);
      }

      if (attrs && attrs.set == null) {
        continue;
      }
      child[i] = _clone(parent[i], depth - 1);
    }

    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(parent);
      for (var i = 0; i < symbols.length; i++) {
        var symbol = symbols[i];
        var descriptor = Object.getOwnPropertyDescriptor(parent, symbol);
        if (descriptor && !descriptor.enumerable && !includeNonEnumerable) {
          continue;
        }
        child[symbol] = _clone(parent[symbol], depth - 1);
        if (!descriptor.enumerable) {
          Object.defineProperty(child, symbol, {
            enumerable: false
          });
        }
      }
    }

    if (includeNonEnumerable) {
      var allPropertyNames = Object.getOwnPropertyNames(parent);
      for (var i = 0; i < allPropertyNames.length; i++) {
        var propertyName = allPropertyNames[i];
        var descriptor = Object.getOwnPropertyDescriptor(parent, propertyName);
        if (descriptor && descriptor.enumerable) {
          continue;
        }
        child[propertyName] = _clone(parent[propertyName], depth - 1);
        Object.defineProperty(child, propertyName, {
          enumerable: false
        });
      }
    }

    return child;
  }

  return _clone(parent, depth);
}
clone.clonePrototype = function clonePrototype(parent) {
  if (parent === null)
    return null;

  var c = function () {};
  c.prototype = parent;
  return new c();
};

function __objToStr(o) {
  return Object.prototype.toString.call(o);
}
clone.__objToStr = __objToStr;

function __isDate(o) {
  return typeof o === 'object' && __objToStr(o) === '[object Date]';
}
clone.__isDate = __isDate;

function __isArray(o) {
  return typeof o === 'object' && __objToStr(o) === '[object Array]';
}
clone.__isArray = __isArray;

function __isRegExp(o) {
  return typeof o === 'object' && __objToStr(o) === '[object RegExp]';
}
clone.__isRegExp = __isRegExp;

function __getRegExpFlags(re) {
  var flags = '';
  if (re.global) flags += 'g';
  if (re.ignoreCase) flags += 'i';
  if (re.multiline) flags += 'm';
  return flags;
}
clone.__getRegExpFlags = __getRegExpFlags;

return clone;
})();

if (typeof module === 'object' && module.exports) {
  module.exports = clone;
}

var CSSLint = (function() {
    "use strict";

    var rules           = [],
        formatters      = [],
        embeddedRuleset = /\/\*\s*csslint([^\*]*)\*\//,
        api             = new parserlib.util.EventTarget();

    api.version = "1.0.5";
    api.addRule = function(rule) {
        rules.push(rule);
        rules[rule.id] = rule;
    };
    api.clearRules = function() {
        rules = [];
    };
    api.getRules = function() {
        return [].concat(rules).sort(function(a, b) {
            return a.id > b.id ? 1 : 0;
        });
    };
    api.getRuleset = function() {
        var ruleset = {},
            i = 0,
            len = rules.length;

        while (i < len) {
            ruleset[rules[i++].id] = 1;    // by default, everything is a warning
        }

        return ruleset;
    };
    function applyEmbeddedRuleset(text, ruleset) {
        var valueMap,
            embedded = text && text.match(embeddedRuleset),
            rules = embedded && embedded[1];

        if (rules) {
            valueMap = {
                "true": 2,  // true is error
                "": 1,      // blank is warning
                "false": 0, // false is ignore

                "2": 2,     // explicit error
                "1": 1,     // explicit warning
                "0": 0      // explicit ignore
            };

            rules.toLowerCase().split(",").forEach(function(rule) {
                var pair = rule.split(":"),
                    property = pair[0] || "",
                    value = pair[1] || "";

                ruleset[property.trim()] = valueMap[value.trim()];
            });
        }

        return ruleset;
    }
    api.addFormatter = function(formatter) {
        formatters[formatter.id] = formatter;
    };
    api.getFormatter = function(formatId) {
        return formatters[formatId];
    };
    api.format = function(results, filename, formatId, options) {
        var formatter = api.getFormatter(formatId),
            result = null;

        if (formatter) {
            result = formatter.startFormat();
            result += formatter.formatResults(results, filename, options || {});
            result += formatter.endFormat();
        }

        return result;
    };
    api.hasFormat = function(formatId) {
        return formatters.hasOwnProperty(formatId);
    };
    api.verify = function(text, ruleset) {

        var i = 0,
            reporter,
            lines,
            allow = {},
            ignore = [],
            report,
            parser = new parserlib.css.Parser({
                starHack: true,
                ieFilters: true,
                underscoreHack: true,
                strict: false
            });
        lines = text.replace(/\n\r?/g, "$split$").split("$split$");
        CSSLint.Util.forEach(lines, function (line, lineno) {
            var allowLine = line && line.match(/\/\*[ \t]*csslint[ \t]+allow:[ \t]*([^\*]*)\*\//i),
                allowRules = allowLine && allowLine[1],
                allowRuleset = {};

            if (allowRules) {
                allowRules.toLowerCase().split(",").forEach(function(allowRule) {
                    allowRuleset[allowRule.trim()] = true;
                });
                if (Object.keys(allowRuleset).length > 0) {
                    allow[lineno + 1] = allowRuleset;
                }
            }
        });

        var ignoreStart = null,
            ignoreEnd = null;
        CSSLint.Util.forEach(lines, function (line, lineno) {
            if (ignoreStart === null && line.match(/\/\*[ \t]*csslint[ \t]+ignore:start[ \t]*\*\//i)) {
                ignoreStart = lineno;
            }

            if (line.match(/\/\*[ \t]*csslint[ \t]+ignore:end[ \t]*\*\//i)) {
                ignoreEnd = lineno;
            }

            if (ignoreStart !== null && ignoreEnd !== null) {
                ignore.push([ignoreStart, ignoreEnd]);
                ignoreStart = ignoreEnd = null;
            }
        });
        if (ignoreStart !== null) {
            ignore.push([ignoreStart, lines.length]);
        }

        if (!ruleset) {
            ruleset = api.getRuleset();
        }

        if (embeddedRuleset.test(text)) {
            ruleset = clone(ruleset);
            ruleset = applyEmbeddedRuleset(text, ruleset);
        }

        reporter = new Reporter(lines, ruleset, allow, ignore);

        ruleset.errors = 2;       // always report parsing errors as errors
        for (i in ruleset) {
            if (ruleset.hasOwnProperty(i) && ruleset[i]) {
                if (rules[i]) {
                    rules[i].init(parser, reporter);
                }
            }
        }
        try {
            parser.parse(text);
        } catch (ex) {
            reporter.error("Fatal error, cannot continue: " + ex.message, ex.line, ex.col, {});
        }

        report = {
            messages    : reporter.messages,
            stats       : reporter.stats,
            ruleset     : reporter.ruleset,
            allow       : reporter.allow,
            ignore      : reporter.ignore
        };
        report.messages.sort(function (a, b) {
            if (a.rollup && !b.rollup) {
                return 1;
            } else if (!a.rollup && b.rollup) {
                return -1;
            } else {
                return a.line - b.line;
            }
        });

        return report;
    };

    return api;

})();
function Reporter(lines, ruleset, allow, ignore) {
    "use strict";
    this.messages = [];
    this.stats = [];
    this.lines = lines;
    this.ruleset = ruleset;
    this.allow = allow;
    if (!this.allow) {
        this.allow = {};
    }
    this.ignore = ignore;
    if (!this.ignore) {
        this.ignore = [];
    }
}

Reporter.prototype = {
    constructor: Reporter,
    error: function(message, line, col, rule) {
        "use strict";
        this.messages.push({
            type    : "error",
            line    : line,
            col     : col,
            message : message,
            evidence: this.lines[line-1],
            rule    : rule || {}
        });
    },
    warn: function(message, line, col, rule) {
        "use strict";
        this.report(message, line, col, rule);
    },
    report: function(message, line, col, rule) {
        "use strict";
        if (this.allow.hasOwnProperty(line) && this.allow[line].hasOwnProperty(rule.id)) {
            return;
        }

        if (this.isIgnored(line)) {
            return;
        }

        this.messages.push({
            type    : this.ruleset[rule.id] === 2 ? "error" : "warning",
            line    : line,
            col     : col,
            message : message,
            evidence: this.lines[line-1],
            rule    : rule
        });
    },
    info: function(message, line, col, rule) {
        "use strict";
        this.messages.push({
            type    : "info",
            line    : line,
            col     : col,
            message : message,
            evidence: this.lines[line-1],
            rule    : rule
        });
    },
    rollupError: function(message, rule) {
        "use strict";
        this.messages.push({
            type    : "error",
            rollup  : true,
            message : message,
            rule    : rule
        });
    },
    rollupWarn: function(message, rule) {
        "use strict";
        this.messages.push({
            type    : "warning",
            rollup  : true,
            message : message,
            rule    : rule
        });
    },
    stat: function(name, value) {
        "use strict";
        this.stats[name] = value;
    },
    isIgnored: function(line) {
        "use strict";
        var ignore = false;
        CSSLint.Util.forEach(this.ignore, function (range) {
            if (range[0] <= line && line <= range[1]) {
                ignore = true;
            }
        });
        return ignore;
    }
};
CSSLint._Reporter = Reporter;
CSSLint.Util = {
    mix: function(receiver, supplier) {
        "use strict";
        var prop;

        for (prop in supplier) {
            if (supplier.hasOwnProperty(prop)) {
                receiver[prop] = supplier[prop];
            }
        }

        return prop;
    },
    indexOf: function(values, value) {
        "use strict";
        if (values.indexOf) {
            return values.indexOf(value);
        } else {
            for (var i=0, len=values.length; i < len; i++) {
                if (values[i] === value) {
                    return i;
                }
            }
            return -1;
        }
    },
    forEach: function(values, func) {
        "use strict";
        if (values.forEach) {
            return values.forEach(func);
        } else {
            for (var i=0, len=values.length; i < len; i++) {
                func(values[i], i, values);
            }
        }
    }
};
CSSLint.addRule({
    id: "box-model",
    name: "Beware of broken box size",
    desc: "Don't use width or height when using padding or border.",
    url: "https://github.com/CSSLint/csslint/wiki/Beware-of-box-model-size",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            widthProperties = {
                border: 1,
                "border-left": 1,
                "border-right": 1,
                padding: 1,
                "padding-left": 1,
                "padding-right": 1
            },
            heightProperties = {
                border: 1,
                "border-bottom": 1,
                "border-top": 1,
                padding: 1,
                "padding-bottom": 1,
                "padding-top": 1
            },
            properties,
            boxSizing = false;

        function startRule() {
            properties = {};
            boxSizing = false;
        }

        function endRule() {
            var prop, value;

            if (!boxSizing) {
                if (properties.height) {
                    for (prop in heightProperties) {
                        if (heightProperties.hasOwnProperty(prop) && properties[prop]) {
                            value = properties[prop].value;
                            if (!(prop === "padding" && value.parts.length === 2 && value.parts[0].value === 0)) {
                                reporter.report("Using height with " + prop + " can sometimes make elements larger than you expect.", properties[prop].line, properties[prop].col, rule);
                            }
                        }
                    }
                }

                if (properties.width) {
                    for (prop in widthProperties) {
                        if (widthProperties.hasOwnProperty(prop) && properties[prop]) {
                            value = properties[prop].value;

                            if (!(prop === "padding" && value.parts.length === 2 && value.parts[1].value === 0)) {
                                reporter.report("Using width with " + prop + " can sometimes make elements larger than you expect.", properties[prop].line, properties[prop].col, rule);
                            }
                        }
                    }
                }
            }
        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("startpage", startRule);
        parser.addListener("startpagemargin", startRule);
        parser.addListener("startkeyframerule", startRule);
        parser.addListener("startviewport", startRule);

        parser.addListener("property", function(event) {
            var name = event.property.text.toLowerCase();

            if (heightProperties[name] || widthProperties[name]) {
                if (!/^0\S*$/.test(event.value) && !(name === "border" && event.value.toString() === "none")) {
                    properties[name] = {
                        line: event.property.line,
                        col: event.property.col,
                        value: event.value
                    };
                }
            } else {
                if (/^(width|height)/i.test(name) && /^(length|percentage)/.test(event.value.parts[0].type)) {
                    properties[name] = 1;
                } else if (name === "box-sizing") {
                    boxSizing = true;
                }
            }

        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);
        parser.addListener("endpage", endRule);
        parser.addListener("endpagemargin", endRule);
        parser.addListener("endkeyframerule", endRule);
        parser.addListener("endviewport", endRule);
    }

});

CSSLint.addRule({
    id: "bulletproof-font-face",
    name: "Use the bulletproof @font-face syntax",
    desc: "Use the bulletproof @font-face syntax to avoid 404's in old IE (http://www.fontspring.com/blog/the-new-bulletproof-font-face-syntax).",
    url: "https://github.com/CSSLint/csslint/wiki/Bulletproof-font-face",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            fontFaceRule = false,
            firstSrc = true,
            ruleFailed = false,
            line, col;
        parser.addListener("startfontface", function() {
            fontFaceRule = true;
        });

        parser.addListener("property", function(event) {
            if (!fontFaceRule) {
                return;
            }

            var propertyName = event.property.toString().toLowerCase(),
                value = event.value.toString();
            line = event.line;
            col = event.col;
            if (propertyName === "src") {
                var regex = /^\s?url\(['"].+\.eot\?.*['"]\)\s*format\(['"]embedded-opentype['"]\).*$/i;
                if (!value.match(regex) && firstSrc) {
                    ruleFailed = true;
                    firstSrc = false;
                } else if (value.match(regex) && !firstSrc) {
                    ruleFailed = false;
                }
            }


        });
        parser.addListener("endfontface", function() {
            fontFaceRule = false;

            if (ruleFailed) {
                reporter.report("@font-face declaration doesn't follow the fontspring bulletproof syntax.", line, col, rule);
            }
        });
    }
});

CSSLint.addRule({
    id: "compatible-vendor-prefixes",
    name: "Require compatible vendor prefixes",
    desc: "Include all compatible vendor prefixes to reach a wider range of users.",
    url: "https://github.com/CSSLint/csslint/wiki/Require-compatible-vendor-prefixes",
    browsers: "All",
    init: function (parser, reporter) {
        "use strict";
        var rule = this,
            compatiblePrefixes,
            properties,
            prop,
            variations,
            prefixed,
            i,
            len,
            inKeyFrame = false,
            arrayPush = Array.prototype.push,
            applyTo = [];
        compatiblePrefixes = {
            "animation"                  : "webkit",
            "animation-delay"            : "webkit",
            "animation-direction"        : "webkit",
            "animation-duration"         : "webkit",
            "animation-fill-mode"        : "webkit",
            "animation-iteration-count"  : "webkit",
            "animation-name"             : "webkit",
            "animation-play-state"       : "webkit",
            "animation-timing-function"  : "webkit",
            "appearance"                 : "webkit moz",
            "border-end"                 : "webkit moz",
            "border-end-color"           : "webkit moz",
            "border-end-style"           : "webkit moz",
            "border-end-width"           : "webkit moz",
            "border-image"               : "webkit moz o",
            "border-radius"              : "webkit",
            "border-start"               : "webkit moz",
            "border-start-color"         : "webkit moz",
            "border-start-style"         : "webkit moz",
            "border-start-width"         : "webkit moz",
            "box-align"                  : "webkit moz",
            "box-direction"              : "webkit moz",
            "box-flex"                   : "webkit moz",
            "box-lines"                  : "webkit",
            "box-ordinal-group"          : "webkit moz",
            "box-orient"                 : "webkit moz",
            "box-pack"                   : "webkit moz",
            "box-sizing"                 : "",
            "box-shadow"                 : "",
            "column-count"               : "webkit moz ms",
            "column-gap"                 : "webkit moz ms",
            "column-rule"                : "webkit moz ms",
            "column-rule-color"          : "webkit moz ms",
            "column-rule-style"          : "webkit moz ms",
            "column-rule-width"          : "webkit moz ms",
            "column-width"               : "webkit moz ms",
            "flex"                       : "webkit ms",
            "flex-basis"                 : "webkit",
            "flex-direction"             : "webkit ms",
            "flex-flow"                  : "webkit",
            "flex-grow"                  : "webkit",
            "flex-shrink"                : "webkit",
            "hyphens"                    : "epub moz",
            "line-break"                 : "webkit ms",
            "margin-end"                 : "webkit moz",
            "margin-start"               : "webkit moz",
            "marquee-speed"              : "webkit wap",
            "marquee-style"              : "webkit wap",
            "padding-end"                : "webkit moz",
            "padding-start"              : "webkit moz",
            "tab-size"                   : "moz o",
            "text-size-adjust"           : "webkit ms",
            "transform"                  : "webkit ms",
            "transform-origin"           : "webkit ms",
            "transition"                 : "",
            "transition-delay"           : "",
            "transition-duration"        : "",
            "transition-property"        : "",
            "transition-timing-function" : "",
            "user-modify"                : "webkit moz",
            "user-select"                : "webkit moz ms",
            "word-break"                 : "epub ms",
            "writing-mode"               : "epub ms"
        };


        for (prop in compatiblePrefixes) {
            if (compatiblePrefixes.hasOwnProperty(prop)) {
                variations = [];
                prefixed = compatiblePrefixes[prop].split(" ");
                for (i = 0, len = prefixed.length; i < len; i++) {
                    variations.push("-" + prefixed[i] + "-" + prop);
                }
                compatiblePrefixes[prop] = variations;
                arrayPush.apply(applyTo, variations);
            }
        }

        parser.addListener("startrule", function () {
            properties = [];
        });

        parser.addListener("startkeyframes", function (event) {
            inKeyFrame = event.prefix || true;
        });

        parser.addListener("endkeyframes", function () {
            inKeyFrame = false;
        });

        parser.addListener("property", function (event) {
            var name = event.property;
            if (CSSLint.Util.indexOf(applyTo, name.text) > -1) {
                if (!inKeyFrame || typeof inKeyFrame !== "string" ||
                        name.text.indexOf("-" + inKeyFrame + "-") !== 0) {
                    properties.push(name);
                }
            }
        });

        parser.addListener("endrule", function () {
            if (!properties.length) {
                return;
            }

            var propertyGroups = {},
                i,
                len,
                name,
                prop,
                variations,
                value,
                full,
                actual,
                item,
                propertiesSpecified;

            for (i = 0, len = properties.length; i < len; i++) {
                name = properties[i];

                for (prop in compatiblePrefixes) {
                    if (compatiblePrefixes.hasOwnProperty(prop)) {
                        variations = compatiblePrefixes[prop];
                        if (CSSLint.Util.indexOf(variations, name.text) > -1) {
                            if (!propertyGroups[prop]) {
                                propertyGroups[prop] = {
                                    full: variations.slice(0),
                                    actual: [],
                                    actualNodes: []
                                };
                            }
                            if (CSSLint.Util.indexOf(propertyGroups[prop].actual, name.text) === -1) {
                                propertyGroups[prop].actual.push(name.text);
                                propertyGroups[prop].actualNodes.push(name);
                            }
                        }
                    }
                }
            }

            for (prop in propertyGroups) {
                if (propertyGroups.hasOwnProperty(prop)) {
                    value = propertyGroups[prop];
                    full = value.full;
                    actual = value.actual;

                    if (full.length > actual.length) {
                        for (i = 0, len = full.length; i < len; i++) {
                            item = full[i];
                            if (CSSLint.Util.indexOf(actual, item) === -1) {
                                propertiesSpecified = (actual.length === 1) ? actual[0] : (actual.length === 2) ? actual.join(" and ") : actual.join(", ");
                                reporter.report("The property " + item + " is compatible with " + propertiesSpecified + " and should be included as well.", value.actualNodes[0].line, value.actualNodes[0].col, rule);
                            }
                        }

                    }
                }
            }
        });
    }
});

CSSLint.addRule({
    id: "display-property-grouping",
    name: "Require properties appropriate for display",
    desc: "Certain properties shouldn't be used with certain display property values.",
    url: "https://github.com/CSSLint/csslint/wiki/Require-properties-appropriate-for-display",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        var propertiesToCheck = {
                display: 1,
                "float": "none",
                height: 1,
                width: 1,
                margin: 1,
                "margin-left": 1,
                "margin-right": 1,
                "margin-bottom": 1,
                "margin-top": 1,
                padding: 1,
                "padding-left": 1,
                "padding-right": 1,
                "padding-bottom": 1,
                "padding-top": 1,
                "vertical-align": 1
            },
            properties;

        function reportProperty(name, display, msg) {
            if (properties[name]) {
                if (typeof propertiesToCheck[name] !== "string" || properties[name].value.toLowerCase() !== propertiesToCheck[name]) {
                    reporter.report(msg || name + " can't be used with display: " + display + ".", properties[name].line, properties[name].col, rule);
                }
            }
        }

        function startRule() {
            properties = {};
        }

        function endRule() {

            var display = properties.display ? properties.display.value : null;
            if (display) {
                switch (display) {

                    case "inline":
                        reportProperty("height", display);
                        reportProperty("width", display);
                        reportProperty("margin", display);
                        reportProperty("margin-top", display);
                        reportProperty("margin-bottom", display);
                        reportProperty("float", display, "display:inline has no effect on floated elements (but may be used to fix the IE6 double-margin bug).");
                        break;

                    case "block":
                        reportProperty("vertical-align", display);
                        break;

                    case "inline-block":
                        reportProperty("float", display);
                        break;

                    default:
                        if (display.indexOf("table-") === 0) {
                            reportProperty("margin", display);
                            reportProperty("margin-left", display);
                            reportProperty("margin-right", display);
                            reportProperty("margin-top", display);
                            reportProperty("margin-bottom", display);
                            reportProperty("float", display);
                        }
                }
            }

        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("startkeyframerule", startRule);
        parser.addListener("startpagemargin", startRule);
        parser.addListener("startpage", startRule);
        parser.addListener("startviewport", startRule);

        parser.addListener("property", function(event) {
            var name = event.property.text.toLowerCase();

            if (propertiesToCheck[name]) {
                properties[name] = {
                    value: event.value.text,
                    line: event.property.line,
                    col: event.property.col
                };
            }
        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);
        parser.addListener("endkeyframerule", endRule);
        parser.addListener("endpagemargin", endRule);
        parser.addListener("endpage", endRule);
        parser.addListener("endviewport", endRule);

    }

});

CSSLint.addRule({
    id: "duplicate-background-images",
    name: "Disallow duplicate background images",
    desc: "Every background-image should be unique. Use a common class for e.g. sprites.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-duplicate-background-images",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            stack = {};

        parser.addListener("property", function(event) {
            var name = event.property.text,
                value = event.value,
                i, len;

            if (name.match(/background/i)) {
                for (i=0, len=value.parts.length; i < len; i++) {
                    if (value.parts[i].type === "uri") {
                        if (typeof stack[value.parts[i].uri] === "undefined") {
                            stack[value.parts[i].uri] = event;
                        } else {
                            reporter.report("Background image '" + value.parts[i].uri + "' was used multiple times, first declared at line " + stack[value.parts[i].uri].line + ", col " + stack[value.parts[i].uri].col + ".", event.line, event.col, rule);
                        }
                    }
                }
            }
        });
    }
});

CSSLint.addRule({
    id: "duplicate-properties",
    name: "Disallow duplicate properties",
    desc: "Duplicate properties must appear one after the other.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-duplicate-properties",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            properties,
            lastProperty;

        function startRule() {
            properties = {};
        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("startpage", startRule);
        parser.addListener("startpagemargin", startRule);
        parser.addListener("startkeyframerule", startRule);
        parser.addListener("startviewport", startRule);

        parser.addListener("property", function(event) {
            var property = event.property,
                name = property.text.toLowerCase();

            if (properties[name] && (lastProperty !== name || properties[name] === event.value.text)) {
                reporter.report("Duplicate property '" + event.property + "' found.", event.line, event.col, rule);
            }

            properties[name] = event.value.text;
            lastProperty = name;

        });


    }

});

CSSLint.addRule({
    id: "empty-rules",
    name: "Disallow empty rules",
    desc: "Rules without any properties specified should be removed.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-empty-rules",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            count = 0;

        parser.addListener("startrule", function() {
            count=0;
        });

        parser.addListener("property", function() {
            count++;
        });

        parser.addListener("endrule", function(event) {
            var selectors = event.selectors;

            if (count === 0) {
                reporter.report("Rule is empty.", selectors[0].line, selectors[0].col, rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "errors",
    name: "Parsing Errors",
    desc: "This rule looks for recoverable syntax errors.",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        parser.addListener("error", function(event) {
            reporter.error(event.message, event.line, event.col, rule);
        });

    }

});

CSSLint.addRule({
    id: "floats",
    name: "Disallow too many floats",
    desc: "This rule tests if the float property is used too many times",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-too-many-floats",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;
        var count = 0;
        parser.addListener("property", function(event) {
            if (!reporter.isIgnored(event.property.line)) {
              if (event.property.text.toLowerCase() === "float" &&
                      event.value.text.toLowerCase() !== "none") {
                  count++;
              }
            }
        });
        parser.addListener("endstylesheet", function() {
            reporter.stat("floats", count);
            if (count >= 10) {
                reporter.rollupWarn("Too many floats (" + count + "), you're probably using them for layout. Consider using a grid system instead.", rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "font-faces",
    name: "Don't use too many web fonts",
    desc: "Too many different web fonts in the same stylesheet.",
    url: "https://github.com/CSSLint/csslint/wiki/Don%27t-use-too-many-web-fonts",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            count = 0;


        parser.addListener("startfontface", function(event) {
            if (!reporter.isIgnored(event.line)) {
                count++;
            }
        });

        parser.addListener("endstylesheet", function() {
            if (count > 5) {
                reporter.rollupWarn("Too many @font-face declarations (" + count + ").", rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "font-sizes",
    name: "Disallow too many font sizes",
    desc: "Checks the number of font-size declarations.",
    url: "https://github.com/CSSLint/csslint/wiki/Don%27t-use-too-many-font-size-declarations",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            count = 0;
        parser.addListener("property", function(event) {
            if (!reporter.isIgnored(event.property.line)) {
                if (event.property.toString() === "font-size") {
                    count++;
                }
            }
        });
        parser.addListener("endstylesheet", function() {
            reporter.stat("font-sizes", count);
            if (count >= 10) {
                reporter.rollupWarn("Too many font-size declarations (" + count + "), abstraction needed.", rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "gradients",
    name: "Require all gradient definitions",
    desc: "When using a vendor-prefixed gradient, make sure to use them all.",
    url: "https://github.com/CSSLint/csslint/wiki/Require-all-gradient-definitions",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            gradients;

        parser.addListener("startrule", function() {
            gradients = {
                moz: 0,
                webkit: 0,
                oldWebkit: 0,
                o: 0
            };
        });

        parser.addListener("property", function(event) {

            if (/\-(moz|o|webkit)(?:\-(?:linear|radial))\-gradient/i.test(event.value)) {
                gradients[RegExp.$1] = 1;
            } else if (/\-webkit\-gradient/i.test(event.value)) {
                gradients.oldWebkit = 1;
            }

        });

        parser.addListener("endrule", function(event) {
            var missing = [];

            if (!gradients.moz) {
                missing.push("Firefox 3.6+");
            }

            if (!gradients.webkit) {
                missing.push("Webkit (Safari 5+, Chrome)");
            }

            if (!gradients.oldWebkit) {
                missing.push("Old Webkit (Safari 4+, Chrome)");
            }

            if (!gradients.o) {
                missing.push("Opera 11.1+");
            }

            if (missing.length && missing.length < 4) {
                reporter.report("Missing vendor-prefixed CSS gradients for " + missing.join(", ") + ".", event.selectors[0].line, event.selectors[0].col, rule);
            }

        });

    }

});

CSSLint.addRule({
    id: "ids",
    name: "Disallow IDs in selectors",
    desc: "Selectors should not contain IDs.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-IDs-in-selectors",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;
        parser.addListener("startrule", function(event) {
            var selectors = event.selectors,
                selector,
                part,
                modifier,
                idCount,
                i, j, k;

            for (i=0; i < selectors.length; i++) {
                selector = selectors[i];
                idCount = 0;

                for (j=0; j < selector.parts.length; j++) {
                    part = selector.parts[j];
                    if (part.type === parser.SELECTOR_PART_TYPE) {
                        for (k=0; k < part.modifiers.length; k++) {
                            modifier = part.modifiers[k];
                            if (modifier.type === "id") {
                                idCount++;
                            }
                        }
                    }
                }

                if (idCount === 1) {
                    reporter.report("Don't use IDs in selectors.", selector.line, selector.col, rule);
                } else if (idCount > 1) {
                    reporter.report(idCount + " IDs in the selector, really?", selector.line, selector.col, rule);
                }
            }

        });
    }

});

CSSLint.addRule({
    id: "import-ie-limit",
    name: "@import limit on IE6-IE9",
    desc: "IE6-9 supports up to 31 @import per stylesheet",
    browsers: "IE6, IE7, IE8, IE9",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            MAX_IMPORT_COUNT = 31,
            count = 0;

        function startPage() {
            count = 0;
        }

        parser.addListener("startpage", startPage);

        parser.addListener("import", function() {
            count++;
        });

        parser.addListener("endstylesheet", function() {
            if (count > MAX_IMPORT_COUNT) {
                reporter.rollupError(
                    "Too many @import rules (" + count + "). IE6-9 supports up to 31 import per stylesheet.",
                    rule
                );
            }
        });
    }

});

CSSLint.addRule({
    id: "import",
    name: "Disallow @import",
    desc: "Don't use @import, use <link> instead.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-%40import",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        parser.addListener("import", function(event) {
            reporter.report("@import prevents parallel downloads, use <link> instead.", event.line, event.col, rule);
        });

    }

});

CSSLint.addRule({
    id: "important",
    name: "Disallow !important",
    desc: "Be careful when using !important declaration",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-%21important",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            count = 0;
        parser.addListener("property", function(event) {
            if (!reporter.isIgnored(event.line)) {
                if (event.important === true) {
                    count++;
                    reporter.report("Use of !important", event.line, event.col, rule);
                }
            }
        });
        parser.addListener("endstylesheet", function() {
            reporter.stat("important", count);
            if (count >= 10) {
                reporter.rollupWarn("Too many !important declarations (" + count + "), try to use less than 10 to avoid specificity issues.", rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "known-properties",
    name: "Require use of known properties",
    desc: "Properties should be known (listed in CSS3 specification) or be a vendor-prefixed property.",
    url: "https://github.com/CSSLint/csslint/wiki/Require-use-of-known-properties",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        parser.addListener("property", function(event) {
            if (event.invalid) {
                reporter.report(event.invalid.message, event.line, event.col, rule);
            }

        });
    }

});

CSSLint.addRule({
    id: "order-alphabetical",
    name: "Alphabetical order",
    desc: "Assure properties are in alphabetical order",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            properties;

        var startRule = function () {
            properties = [];
        };

        var endRule = function(event) {
            var currentProperties = properties.join(","),
                expectedProperties = properties.sort().join(",");

            if (currentProperties !== expectedProperties) {
                reporter.report("Rule doesn't have all its properties in alphabetical order.", event.line, event.col, rule);
            }
        };

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("startpage", startRule);
        parser.addListener("startpagemargin", startRule);
        parser.addListener("startkeyframerule", startRule);
        parser.addListener("startviewport", startRule);

        parser.addListener("property", function(event) {
            var name = event.property.text,
                lowerCasePrefixLessName = name.toLowerCase().replace(/^-.*?-/, "");

            properties.push(lowerCasePrefixLessName);
        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);
        parser.addListener("endpage", endRule);
        parser.addListener("endpagemargin", endRule);
        parser.addListener("endkeyframerule", endRule);
        parser.addListener("endviewport", endRule);
    }

});

CSSLint.addRule({
    id: "outline-none",
    name: "Disallow outline: none",
    desc: "Use of outline: none or outline: 0 should be limited to :focus rules.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-outline%3Anone",
    browsers: "All",
    tags: ["Accessibility"],
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            lastRule;

        function startRule(event) {
            if (event.selectors) {
                lastRule = {
                    line: event.line,
                    col: event.col,
                    selectors: event.selectors,
                    propCount: 0,
                    outline: false
                };
            } else {
                lastRule = null;
            }
        }

        function endRule() {
            if (lastRule) {
                if (lastRule.outline) {
                    if (lastRule.selectors.toString().toLowerCase().indexOf(":focus") === -1) {
                        reporter.report("Outlines should only be modified using :focus.", lastRule.line, lastRule.col, rule);
                    } else if (lastRule.propCount === 1) {
                        reporter.report("Outlines shouldn't be hidden unless other visual changes are made.", lastRule.line, lastRule.col, rule);
                    }
                }
            }
        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("startpage", startRule);
        parser.addListener("startpagemargin", startRule);
        parser.addListener("startkeyframerule", startRule);
        parser.addListener("startviewport", startRule);

        parser.addListener("property", function(event) {
            var name = event.property.text.toLowerCase(),
                value = event.value;

            if (lastRule) {
                lastRule.propCount++;
                if (name === "outline" && (value.toString() === "none" || value.toString() === "0")) {
                    lastRule.outline = true;
                }
            }

        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);
        parser.addListener("endpage", endRule);
        parser.addListener("endpagemargin", endRule);
        parser.addListener("endkeyframerule", endRule);
        parser.addListener("endviewport", endRule);

    }

});

CSSLint.addRule({
    id: "overqualified-elements",
    name: "Disallow overqualified elements",
    desc: "Don't use classes or IDs with elements (a.foo or a#foo).",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-overqualified-elements",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            classes = {};

        parser.addListener("startrule", function(event) {
            var selectors = event.selectors,
                selector,
                part,
                modifier,
                i, j, k;

            for (i=0; i < selectors.length; i++) {
                selector = selectors[i];

                for (j=0; j < selector.parts.length; j++) {
                    part = selector.parts[j];
                    if (part.type === parser.SELECTOR_PART_TYPE) {
                        for (k=0; k < part.modifiers.length; k++) {
                            modifier = part.modifiers[k];
                            if (part.elementName && modifier.type === "id") {
                                reporter.report("Element (" + part + ") is overqualified, just use " + modifier + " without element name.", part.line, part.col, rule);
                            } else if (modifier.type === "class") {

                                if (!classes[modifier]) {
                                    classes[modifier] = [];
                                }
                                classes[modifier].push({
                                    modifier: modifier,
                                    part: part
                                });
                            }
                        }
                    }
                }
            }
        });

        parser.addListener("endstylesheet", function() {

            var prop;
            for (prop in classes) {
                if (classes.hasOwnProperty(prop)) {
                    if (classes[prop].length === 1 && classes[prop][0].part.elementName) {
                        reporter.report("Element (" + classes[prop][0].part + ") is overqualified, just use " + classes[prop][0].modifier + " without element name.", classes[prop][0].part.line, classes[prop][0].part.col, rule);
                    }
                }
            }
        });
    }

});

CSSLint.addRule({
    id: "regex-selectors",
    name: "Disallow selectors that look like regexs",
    desc: "Selectors that look like regular expressions are slow and should be avoided.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-selectors-that-look-like-regular-expressions",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        parser.addListener("startrule", function(event) {
            var selectors = event.selectors,
                selector,
                part,
                modifier,
                i, j, k;

            for (i=0; i < selectors.length; i++) {
                selector = selectors[i];
                for (j=0; j < selector.parts.length; j++) {
                    part = selector.parts[j];
                    if (part.type === parser.SELECTOR_PART_TYPE) {
                        for (k=0; k < part.modifiers.length; k++) {
                            modifier = part.modifiers[k];
                            if (modifier.type === "attribute") {
                                if (/([~\|\^\$\*]=)/.test(modifier)) {
                                    reporter.report("Attribute selectors with " + RegExp.$1 + " are slow!", modifier.line, modifier.col, rule);
                                }
                            }

                        }
                    }
                }
            }
        });
    }

});

CSSLint.addRule({
    id: "rules-count",
    name: "Rules Count",
    desc: "Track how many rules there are.",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var count = 0;
        parser.addListener("startrule", function() {
            count++;
        });

        parser.addListener("endstylesheet", function() {
            reporter.stat("rule-count", count);
        });
    }

});

CSSLint.addRule({
    id: "selector-max-approaching",
    name: "Warn when approaching the 4095 selector limit for IE",
    desc: "Will warn when selector count is >= 3800 selectors.",
    browsers: "IE",
    init: function(parser, reporter) {
        "use strict";
        var rule = this, count = 0;

        parser.addListener("startrule", function(event) {
            count += event.selectors.length;
        });

        parser.addListener("endstylesheet", function() {
            if (count >= 3800) {
                reporter.report("You have " + count + " selectors. Internet Explorer supports a maximum of 4095 selectors per stylesheet. Consider refactoring.", 0, 0, rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "selector-max",
    name: "Error when past the 4095 selector limit for IE",
    desc: "Will error when selector count is > 4095.",
    browsers: "IE",
    init: function(parser, reporter) {
        "use strict";
        var rule = this, count = 0;

        parser.addListener("startrule", function(event) {
            count += event.selectors.length;
        });

        parser.addListener("endstylesheet", function() {
            if (count > 4095) {
                reporter.report("You have " + count + " selectors. Internet Explorer supports a maximum of 4095 selectors per stylesheet. Consider refactoring.", 0, 0, rule);
            }
        });
    }

});

CSSLint.addRule({
    id: "selector-newline",
    name: "Disallow new-line characters in selectors",
    desc: "New-line characters in selectors are usually a forgotten comma and not a descendant combinator.",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        function startRule(event) {
            var i, len, selector, p, n, pLen, part, part2, type, currentLine, nextLine,
                selectors = event.selectors;

            for (i = 0, len = selectors.length; i < len; i++) {
                selector = selectors[i];
                for (p = 0, pLen = selector.parts.length; p < pLen; p++) {
                    for (n = p + 1; n < pLen; n++) {
                        part = selector.parts[p];
                        part2 = selector.parts[n];
                        type = part.type;
                        currentLine = part.line;
                        nextLine = part2.line;

                        if (type === "descendant" && nextLine > currentLine) {
                            reporter.report("newline character found in selector (forgot a comma?)", currentLine, selectors[i].parts[0].col, rule);
                        }
                    }
                }

            }
        }

        parser.addListener("startrule", startRule);

    }
});

CSSLint.addRule({
    id: "shorthand",
    name: "Require shorthand properties",
    desc: "Use shorthand properties where possible.",
    url: "https://github.com/CSSLint/csslint/wiki/Require-shorthand-properties",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            prop, i, len,
            propertiesToCheck = {},
            properties,
            mapping = {
                "margin": [
                    "margin-top",
                    "margin-bottom",
                    "margin-left",
                    "margin-right"
                ],
                "padding": [
                    "padding-top",
                    "padding-bottom",
                    "padding-left",
                    "padding-right"
                ]
            };
        for (prop in mapping) {
            if (mapping.hasOwnProperty(prop)) {
                for (i=0, len=mapping[prop].length; i < len; i++) {
                    propertiesToCheck[mapping[prop][i]] = prop;
                }
            }
        }

        function startRule() {
            properties = {};
        }
        function endRule(event) {

            var prop, i, len, total;
            for (prop in mapping) {
                if (mapping.hasOwnProperty(prop)) {
                    total=0;

                    for (i=0, len=mapping[prop].length; i < len; i++) {
                        total += properties[mapping[prop][i]] ? 1 : 0;
                    }

                    if (total === mapping[prop].length) {
                        reporter.report("The properties " + mapping[prop].join(", ") + " can be replaced by " + prop + ".", event.line, event.col, rule);
                    }
                }
            }
        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("property", function(event) {
            var name = event.property.toString().toLowerCase();

            if (propertiesToCheck[name]) {
                properties[name] = 1;
            }
        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);

    }

});

CSSLint.addRule({
    id: "star-property-hack",
    name: "Disallow properties with a star prefix",
    desc: "Checks for the star property hack (targets IE6/7)",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-star-hack",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;
        parser.addListener("property", function(event) {
            var property = event.property;

            if (property.hack === "*") {
                reporter.report("Property with star prefix found.", event.property.line, event.property.col, rule);
            }
        });
    }
});

CSSLint.addRule({
    id: "text-indent",
    name: "Disallow negative text-indent",
    desc: "Checks for text indent less than -99px",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-negative-text-indent",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            textIndent,
            direction;


        function startRule() {
            textIndent = false;
            direction = "inherit";
        }
        function endRule() {
            if (textIndent && direction !== "ltr") {
                reporter.report("Negative text-indent doesn't work well with RTL. If you use text-indent for image replacement explicitly set direction for that item to ltr.", textIndent.line, textIndent.col, rule);
            }
        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("property", function(event) {
            var name = event.property.toString().toLowerCase(),
                value = event.value;

            if (name === "text-indent" && value.parts[0].value < -99) {
                textIndent = event.property;
            } else if (name === "direction" && value.toString() === "ltr") {
                direction = "ltr";
            }
        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);

    }

});

CSSLint.addRule({
    id: "underscore-property-hack",
    name: "Disallow properties with an underscore prefix",
    desc: "Checks for the underscore property hack (targets IE6)",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-underscore-hack",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;
        parser.addListener("property", function(event) {
            var property = event.property;

            if (property.hack === "_") {
                reporter.report("Property with underscore prefix found.", event.property.line, event.property.col, rule);
            }
        });
    }
});

CSSLint.addRule({
    id: "universal-selector",
    name: "Disallow universal selector",
    desc: "The universal selector (*) is known to be slow.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-universal-selector",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;

        parser.addListener("startrule", function(event) {
            var selectors = event.selectors,
                selector,
                part,
                i;

            for (i=0; i < selectors.length; i++) {
                selector = selectors[i];

                part = selector.parts[selector.parts.length-1];
                if (part.elementName === "*") {
                    reporter.report(rule.desc, part.line, part.col, rule);
                }
            }
        });
    }

});

CSSLint.addRule({
    id: "unqualified-attributes",
    name: "Disallow unqualified attribute selectors",
    desc: "Unqualified attribute selectors are known to be slow.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-unqualified-attribute-selectors",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";

        var rule = this;

        parser.addListener("startrule", function(event) {

            var selectors = event.selectors,
                selectorContainsClassOrId = false,
                selector,
                part,
                modifier,
                i, k;

            for (i=0; i < selectors.length; i++) {
                selector = selectors[i];

                part = selector.parts[selector.parts.length-1];
                if (part.type === parser.SELECTOR_PART_TYPE) {
                    for (k=0; k < part.modifiers.length; k++) {
                        modifier = part.modifiers[k];

                        if (modifier.type === "class" || modifier.type === "id") {
                            selectorContainsClassOrId = true;
                            break;
                        }
                    }

                    if (!selectorContainsClassOrId) {
                        for (k=0; k < part.modifiers.length; k++) {
                            modifier = part.modifiers[k];
                            if (modifier.type === "attribute" && (!part.elementName || part.elementName === "*")) {
                                reporter.report(rule.desc, part.line, part.col, rule);
                            }
                        }
                    }
                }

            }
        });
    }

});

CSSLint.addRule({
    id: "vendor-prefix",
    name: "Require standard property with vendor prefix",
    desc: "When using a vendor-prefixed property, make sure to include the standard one.",
    url: "https://github.com/CSSLint/csslint/wiki/Require-standard-property-with-vendor-prefix",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this,
            properties,
            num,
            propertiesToCheck = {
                "-webkit-border-radius": "border-radius",
                "-webkit-border-top-left-radius": "border-top-left-radius",
                "-webkit-border-top-right-radius": "border-top-right-radius",
                "-webkit-border-bottom-left-radius": "border-bottom-left-radius",
                "-webkit-border-bottom-right-radius": "border-bottom-right-radius",

                "-o-border-radius": "border-radius",
                "-o-border-top-left-radius": "border-top-left-radius",
                "-o-border-top-right-radius": "border-top-right-radius",
                "-o-border-bottom-left-radius": "border-bottom-left-radius",
                "-o-border-bottom-right-radius": "border-bottom-right-radius",

                "-moz-border-radius": "border-radius",
                "-moz-border-radius-topleft": "border-top-left-radius",
                "-moz-border-radius-topright": "border-top-right-radius",
                "-moz-border-radius-bottomleft": "border-bottom-left-radius",
                "-moz-border-radius-bottomright": "border-bottom-right-radius",

                "-moz-column-count": "column-count",
                "-webkit-column-count": "column-count",

                "-moz-column-gap": "column-gap",
                "-webkit-column-gap": "column-gap",

                "-moz-column-rule": "column-rule",
                "-webkit-column-rule": "column-rule",

                "-moz-column-rule-style": "column-rule-style",
                "-webkit-column-rule-style": "column-rule-style",

                "-moz-column-rule-color": "column-rule-color",
                "-webkit-column-rule-color": "column-rule-color",

                "-moz-column-rule-width": "column-rule-width",
                "-webkit-column-rule-width": "column-rule-width",

                "-moz-column-width": "column-width",
                "-webkit-column-width": "column-width",

                "-webkit-column-span": "column-span",
                "-webkit-columns": "columns",

                "-moz-box-shadow": "box-shadow",
                "-webkit-box-shadow": "box-shadow",

                "-moz-transform": "transform",
                "-webkit-transform": "transform",
                "-o-transform": "transform",
                "-ms-transform": "transform",

                "-moz-transform-origin": "transform-origin",
                "-webkit-transform-origin": "transform-origin",
                "-o-transform-origin": "transform-origin",
                "-ms-transform-origin": "transform-origin",

                "-moz-box-sizing": "box-sizing",
                "-webkit-box-sizing": "box-sizing"
            };
        function startRule() {
            properties = {};
            num = 1;
        }
        function endRule() {
            var prop,
                i,
                len,
                needed,
                actual,
                needsStandard = [];

            for (prop in properties) {
                if (propertiesToCheck[prop]) {
                    needsStandard.push({
                        actual: prop,
                        needed: propertiesToCheck[prop]
                    });
                }
            }

            for (i=0, len=needsStandard.length; i < len; i++) {
                needed = needsStandard[i].needed;
                actual = needsStandard[i].actual;

                if (!properties[needed]) {
                    reporter.report("Missing standard property '" + needed + "' to go along with '" + actual + "'.", properties[actual][0].name.line, properties[actual][0].name.col, rule);
                } else {
                    if (properties[needed][0].pos < properties[actual][0].pos) {
                        reporter.report("Standard property '" + needed + "' should come after vendor-prefixed property '" + actual + "'.", properties[actual][0].name.line, properties[actual][0].name.col, rule);
                    }
                }
            }

        }

        parser.addListener("startrule", startRule);
        parser.addListener("startfontface", startRule);
        parser.addListener("startpage", startRule);
        parser.addListener("startpagemargin", startRule);
        parser.addListener("startkeyframerule", startRule);
        parser.addListener("startviewport", startRule);

        parser.addListener("property", function(event) {
            var name = event.property.text.toLowerCase();

            if (!properties[name]) {
                properties[name] = [];
            }

            properties[name].push({
                name: event.property,
                value: event.value,
                pos: num++
            });
        });

        parser.addListener("endrule", endRule);
        parser.addListener("endfontface", endRule);
        parser.addListener("endpage", endRule);
        parser.addListener("endpagemargin", endRule);
        parser.addListener("endkeyframerule", endRule);
        parser.addListener("endviewport", endRule);
    }

});

CSSLint.addRule({
    id: "zero-units",
    name: "Disallow units for 0 values",
    desc: "You don't need to specify units when a value is 0.",
    url: "https://github.com/CSSLint/csslint/wiki/Disallow-units-for-zero-values",
    browsers: "All",
    init: function(parser, reporter) {
        "use strict";
        var rule = this;
        parser.addListener("property", function(event) {
            var parts = event.value.parts,
                i = 0,
                len = parts.length;

            while (i < len) {
                if ((parts[i].units || parts[i].type === "percentage") && parts[i].value === 0 && parts[i].type !== "time") {
                    reporter.report("Values of 0 shouldn't have units specified.", parts[i].line, parts[i].col, rule);
                }
                i++;
            }

        });

    }

});

(function() {
    "use strict";
    var xmlEscape = function(str) {
        if (!str || str.constructor !== String) {
            return "";
        }

        return str.replace(/["&><]/g, function(match) {
            switch (match) {
                case "\"":
                    return "&quot;";
                case "&":
                    return "&amp;";
                case "<":
                    return "&lt;";
                case ">":
                    return "&gt;";
            }
        });
    };

    CSSLint.addFormatter({
        id: "checkstyle-xml",
        name: "Checkstyle XML format",
        startFormat: function() {
            return "<?xml version=\"1.0\" encoding=\"utf-8\"?><checkstyle>";
        },
        endFormat: function() {
            return "</checkstyle>";
        },
        readError: function(filename, message) {
            return "<file name=\"" + xmlEscape(filename) + "\"><error line=\"0\" column=\"0\" severty=\"error\" message=\"" + xmlEscape(message) + "\"></error></file>";
        },
        formatResults: function(results, filename/*, options*/) {
            var messages = results.messages,
                output = [];
            var generateSource = function(rule) {
                if (!rule || !("name" in rule)) {
                    return "";
                }
                return "net.csslint." + rule.name.replace(/\s/g, "");
            };


            if (messages.length > 0) {
                output.push("<file name=\""+filename+"\">");
                CSSLint.Util.forEach(messages, function (message) {
                    if (!message.rollup) {
                        output.push("<error line=\"" + message.line + "\" column=\"" + message.col + "\" severity=\"" + message.type + "\"" +
                          " message=\"" + xmlEscape(message.message) + "\" source=\"" + generateSource(message.rule) +"\"/>");
                    }
                });
                output.push("</file>");
            }

            return output.join("");
        }
    });

}());

CSSLint.addFormatter({
    id: "compact",
    name: "Compact, 'porcelain' format",
    startFormat: function() {
        "use strict";
        return "";
    },
    endFormat: function() {
        "use strict";
        return "";
    },
    formatResults: function(results, filename, options) {
        "use strict";
        var messages = results.messages,
            output = "";
        options = options || {};
        var capitalize = function(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        };

        if (messages.length === 0) {
            return options.quiet ? "" : filename + ": Lint Free!";
        }

        CSSLint.Util.forEach(messages, function(message) {
            if (message.rollup) {
                output += filename + ": " + capitalize(message.type) + " - " + message.message + " (" + message.rule.id + ")\n";
            } else {
                output += filename + ": line " + message.line +
                    ", col " + message.col + ", " + capitalize(message.type) + " - " + message.message + " (" + message.rule.id + ")\n";
            }
        });

        return output;
    }
});

CSSLint.addFormatter({
    id: "csslint-xml",
    name: "CSSLint XML format",
    startFormat: function() {
        "use strict";
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?><csslint>";
    },
    endFormat: function() {
        "use strict";
        return "</csslint>";
    },
    formatResults: function(results, filename/*, options*/) {
        "use strict";
        var messages = results.messages,
            output = [];
        var escapeSpecialCharacters = function(str) {
            if (!str || str.constructor !== String) {
                return "";
            }
            return str.replace(/"/g, "'").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        };

        if (messages.length > 0) {
            output.push("<file name=\""+filename+"\">");
            CSSLint.Util.forEach(messages, function (message) {
                if (message.rollup) {
                    output.push("<issue severity=\"" + message.type + "\" reason=\"" + escapeSpecialCharacters(message.message) + "\" evidence=\"" + escapeSpecialCharacters(message.evidence) + "\"/>");
                } else {
                    output.push("<issue line=\"" + message.line + "\" char=\"" + message.col + "\" severity=\"" + message.type + "\"" +
                        " reason=\"" + escapeSpecialCharacters(message.message) + "\" evidence=\"" + escapeSpecialCharacters(message.evidence) + "\"/>");
                }
            });
            output.push("</file>");
        }

        return output.join("");
    }
});

CSSLint.addFormatter({
    id: "json",
    name: "JSON",
    startFormat: function() {
        "use strict";
        this.json = [];
        return "";
    },
    endFormat: function() {
        "use strict";
        var ret = "";
        if (this.json.length > 0) {
            if (this.json.length === 1) {
                ret = JSON.stringify(this.json[0]);
            } else {
                ret = JSON.stringify(this.json);
            }
        }
        return ret;
    },
    formatResults: function(results, filename, options) {
        "use strict";
        if (results.messages.length > 0 || !options.quiet) {
            this.json.push({
                filename: filename,
                messages: results.messages,
                stats: results.stats
            });
        }
        return "";
    }
});

CSSLint.addFormatter({
    id: "junit-xml",
    name: "JUNIT XML format",
    startFormat: function() {
        "use strict";
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?><testsuites>";
    },
    endFormat: function() {
        "use strict";
        return "</testsuites>";
    },
    formatResults: function(results, filename/*, options*/) {
        "use strict";

        var messages = results.messages,
            output = [],
            tests = {
                "error": 0,
                "failure": 0
            };
        var generateSource = function(rule) {
            if (!rule || !("name" in rule)) {
                return "";
            }
            return "net.csslint." + rule.name.replace(/\s/g, "");
        };
        var escapeSpecialCharacters = function(str) {

            if (!str || str.constructor !== String) {
                return "";
            }

            return str.replace(/"/g, "'").replace(/</g, "&lt;").replace(/>/g, "&gt;");

        };

        if (messages.length > 0) {

            messages.forEach(function (message) {
                var type = message.type === "warning" ? "error" : message.type;
                if (!message.rollup) {
                    output.push("<testcase time=\"0\" name=\"" + generateSource(message.rule) + "\">");
                    output.push("<" + type + " message=\"" + escapeSpecialCharacters(message.message) + "\"><![CDATA[" + message.line + ":" + message.col + ":" + escapeSpecialCharacters(message.evidence) + "]]></" + type + ">");
                    output.push("</testcase>");

                    tests[type] += 1;

                }

            });

            output.unshift("<testsuite time=\"0\" tests=\"" + messages.length + "\" skipped=\"0\" errors=\"" + tests.error + "\" failures=\"" + tests.failure + "\" package=\"net.csslint\" name=\"" + filename + "\">");
            output.push("</testsuite>");

        }

        return output.join("");

    }
});

CSSLint.addFormatter({
    id: "lint-xml",
    name: "Lint XML format",
    startFormat: function() {
        "use strict";
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?><lint>";
    },
    endFormat: function() {
        "use strict";
        return "</lint>";
    },
    formatResults: function(results, filename/*, options*/) {
        "use strict";
        var messages = results.messages,
            output = [];
        var escapeSpecialCharacters = function(str) {
            if (!str || str.constructor !== String) {
                return "";
            }
            return str.replace(/"/g, "'").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        };

        if (messages.length > 0) {

            output.push("<file name=\""+filename+"\">");
            CSSLint.Util.forEach(messages, function (message) {
                if (message.rollup) {
                    output.push("<issue severity=\"" + message.type + "\" reason=\"" + escapeSpecialCharacters(message.message) + "\" evidence=\"" + escapeSpecialCharacters(message.evidence) + "\"/>");
                } else {
                    var rule = "";
                    if (message.rule && message.rule.id) {
                        rule = "rule=\"" + escapeSpecialCharacters(message.rule.id) + "\" ";
                    }
                    output.push("<issue " + rule + "line=\"" + message.line + "\" char=\"" + message.col + "\" severity=\"" + message.type + "\"" +
                        " reason=\"" + escapeSpecialCharacters(message.message) + "\" evidence=\"" + escapeSpecialCharacters(message.evidence) + "\"/>");
                }
            });
            output.push("</file>");
        }

        return output.join("");
    }
});

CSSLint.addFormatter({
    id: "text",
    name: "Plain Text",
    startFormat: function() {
        "use strict";
        return "";
    },
    endFormat: function() {
        "use strict";
        return "";
    },
    formatResults: function(results, filename, options) {
        "use strict";
        var messages = results.messages,
            output = "";
        options = options || {};

        if (messages.length === 0) {
            return options.quiet ? "" : "\n\ncsslint: No errors in " + filename + ".";
        }

        output = "\n\ncsslint: There ";
        if (messages.length === 1) {
            output += "is 1 problem";
        } else {
            output += "are " + messages.length + " problems";
        }
        output += " in " + filename + ".";

        var pos = filename.lastIndexOf("/"),
            shortFilename = filename;

        if (pos === -1) {
            pos = filename.lastIndexOf("\\");
        }
        if (pos > -1) {
            shortFilename = filename.substring(pos+1);
        }

        CSSLint.Util.forEach(messages, function (message, i) {
            output = output + "\n\n" + shortFilename;
            if (message.rollup) {
                output += "\n" + (i+1) + ": " + message.type;
                output += "\n" + message.message;
            } else {
                output += "\n" + (i+1) + ": " + message.type + " at line " + message.line + ", col " + message.col;
                output += "\n" + message.message;
                output += "\n" + message.evidence;
            }
        });

        return output;
    }
});

return CSSLint;
})();


module.exports.CSSLint = CSSLint;

});

ace.define("ace/mode/css_worker",[], function(require, exports, module) {
"use strict";

var oop = require("../lib/oop");
var lang = require("../lib/lang");
var Mirror = require("../worker/mirror").Mirror;
var CSSLint = require("./css/csslint").CSSLint;

var Worker = exports.Worker = function(sender) {
    Mirror.call(this, sender);
    this.setTimeout(400);
    this.ruleset = null;
    this.setDisabledRules("ids|order-alphabetical");
    this.setInfoRules(
      "adjoining-classes|zero-units|gradients|box-model|" +
      "import|outline-none|vendor-prefix"
    );
};

oop.inherits(Worker, Mirror);

(function() {
    this.setInfoRules = function(ruleNames) {
        if (typeof ruleNames == "string")
            ruleNames = ruleNames.split("|");
        this.infoRules = lang.arrayToMap(ruleNames);
        this.doc.getValue() && this.deferredUpdate.schedule(100);
    };

    this.setDisabledRules = function(ruleNames) {
        if (!ruleNames) {
            this.ruleset = null;
        } else {
            if (typeof ruleNames == "string")
                ruleNames = ruleNames.split("|");
            var all = {};

            CSSLint.getRules().forEach(function(x){
                all[x.id] = true;
            });
            ruleNames.forEach(function(x) {
                delete all[x];
            });
            
            this.ruleset = all;
        }
        this.doc.getValue() && this.deferredUpdate.schedule(100);
    };

    this.onUpdate = function() {
        var value = this.doc.getValue();
        if (!value)
            return this.sender.emit("annotate", []);
        var infoRules = this.infoRules;

        var result = CSSLint.verify(value, this.ruleset);
        this.sender.emit("annotate", result.messages.map(function(msg) {
            return {
                row: msg.line - 1,
                column: msg.col - 1,
                text: msg.message,
                type: infoRules[msg.rule.id] ? "info" : msg.type,
                rule: msg.rule.name
            };
        }));
    };

}).call(Worker.prototype);

});
