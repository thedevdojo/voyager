(function () {
var autosave = (function () {
    'use strict';

    var Cell = function (initial) {
      var value = initial;
      var get = function () {
        return value;
      };
      var set = function (v) {
        value = v;
      };
      var clone = function () {
        return Cell(get());
      };
      return {
        get: get,
        set: set,
        clone: clone
      };
    };

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.LocalStorage');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var fireRestoreDraft = function (editor) {
      return editor.fire('RestoreDraft');
    };
    var fireStoreDraft = function (editor) {
      return editor.fire('StoreDraft');
    };
    var fireRemoveDraft = function (editor) {
      return editor.fire('RemoveDraft');
    };

    var parse = function (timeString, defaultTime) {
      var multiples = {
        s: 1000,
        m: 60000
      };
      var toParse = timeString || defaultTime;
      var parsedTime = /^(\d+)([ms]?)$/.exec('' + toParse);
      return (parsedTime[2] ? multiples[parsedTime[2]] : 1) * parseInt(toParse, 10);
    };

    var shouldAskBeforeUnload = function (editor) {
      return editor.getParam('autosave_ask_before_unload', true);
    };
    var getAutoSavePrefix = function (editor) {
      var prefix = editor.getParam('autosave_prefix', 'tinymce-autosave-{path}{query}{hash}-{id}-');
      prefix = prefix.replace(/\{path\}/g, document.location.pathname);
      prefix = prefix.replace(/\{query\}/g, document.location.search);
      prefix = prefix.replace(/\{hash\}/g, document.location.hash);
      prefix = prefix.replace(/\{id\}/g, editor.id);
      return prefix;
    };
    var shouldRestoreWhenEmpty = function (editor) {
      return editor.getParam('autosave_restore_when_empty', false);
    };
    var getAutoSaveInterval = function (editor) {
      return parse(editor.settings.autosave_interval, '30s');
    };
    var getAutoSaveRetention = function (editor) {
      return parse(editor.settings.autosave_retention, '20m');
    };

    var isEmpty = function (editor, html) {
      var forcedRootBlockName = editor.settings.forced_root_block;
      html = global$2.trim(typeof html === 'undefined' ? editor.getBody().innerHTML : html);
      return html === '' || new RegExp('^<' + forcedRootBlockName + '[^>]*>((\xA0|&nbsp;|[ \t]|<br[^>]*>)+?|)</' + forcedRootBlockName + '>|<br>$', 'i').test(html);
    };
    var hasDraft = function (editor) {
      var time = parseInt(global$1.getItem(getAutoSavePrefix(editor) + 'time'), 10) || 0;
      if (new Date().getTime() - time > getAutoSaveRetention(editor)) {
        removeDraft(editor, false);
        return false;
      }
      return true;
    };
    var removeDraft = function (editor, fire) {
      var prefix = getAutoSavePrefix(editor);
      global$1.removeItem(prefix + 'draft');
      global$1.removeItem(prefix + 'time');
      if (fire !== false) {
        fireRemoveDraft(editor);
      }
    };
    var storeDraft = function (editor) {
      var prefix = getAutoSavePrefix(editor);
      if (!isEmpty(editor) && editor.isDirty()) {
        global$1.setItem(prefix + 'draft', editor.getContent({
          format: 'raw',
          no_events: true
        }));
        global$1.setItem(prefix + 'time', new Date().getTime().toString());
        fireStoreDraft(editor);
      }
    };
    var restoreDraft = function (editor) {
      var prefix = getAutoSavePrefix(editor);
      if (hasDraft(editor)) {
        editor.setContent(global$1.getItem(prefix + 'draft'), { format: 'raw' });
        fireRestoreDraft(editor);
      }
    };
    var startStoreDraft = function (editor, started) {
      var interval = getAutoSaveInterval(editor);
      if (!started.get()) {
        setInterval(function () {
          if (!editor.removed) {
            storeDraft(editor);
          }
        }, interval);
        started.set(true);
      }
    };
    var restoreLastDraft = function (editor) {
      editor.undoManager.transact(function () {
        restoreDraft(editor);
        removeDraft(editor);
      });
      editor.focus();
    };

    function curry(fn) {
      var initialArgs = [];
      for (var _i = 1; _i < arguments.length; _i++) {
        initialArgs[_i - 1] = arguments[_i];
      }
      return function () {
        var restArgs = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          restArgs[_i] = arguments[_i];
        }
        var all = initialArgs.concat(restArgs);
        return fn.apply(null, all);
      };
    }

    var get = function (editor) {
      return {
        hasDraft: curry(hasDraft, editor),
        storeDraft: curry(storeDraft, editor),
        restoreDraft: curry(restoreDraft, editor),
        removeDraft: curry(removeDraft, editor),
        isEmpty: curry(isEmpty, editor)
      };
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.EditorManager');

    global$3._beforeUnloadHandler = function () {
      var msg;
      global$2.each(global$3.get(), function (editor) {
        if (editor.plugins.autosave) {
          editor.plugins.autosave.storeDraft();
        }
        if (!msg && editor.isDirty() && shouldAskBeforeUnload(editor)) {
          msg = editor.translate('You have unsaved changes are you sure you want to navigate away?');
        }
      });
      return msg;
    };
    var setup = function (editor) {
      window.onbeforeunload = global$3._beforeUnloadHandler;
    };

    var postRender = function (editor, started) {
      return function (e) {
        var ctrl = e.control;
        ctrl.disabled(!hasDraft(editor));
        editor.on('StoreDraft RestoreDraft RemoveDraft', function () {
          ctrl.disabled(!hasDraft(editor));
        });
        startStoreDraft(editor, started);
      };
    };
    var register = function (editor, started) {
      editor.addButton('restoredraft', {
        title: 'Restore last draft',
        onclick: function () {
          restoreLastDraft(editor);
        },
        onPostRender: postRender(editor, started)
      });
      editor.addMenuItem('restoredraft', {
        text: 'Restore last draft',
        onclick: function () {
          restoreLastDraft(editor);
        },
        onPostRender: postRender(editor, started),
        context: 'file'
      });
    };

    global.add('autosave', function (editor) {
      var started = Cell(false);
      setup(editor);
      register(editor, started);
      editor.on('init', function () {
        if (shouldRestoreWhenEmpty(editor) && editor.dom.isEmpty(editor.getBody())) {
          restoreDraft(editor);
        }
      });
      return get(editor);
    });
    function Plugin () {
    }

    return Plugin;

}());
})();
