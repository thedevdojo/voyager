(function() {
  $(function() {
    var HtmlMode, editor;
    HtmlMode = ace.require("ace/mode/html").Mode;
    editor = ace.edit("code-preview-list");
    editor.getSession().setMode(new HtmlMode());
    editor.setTheme("ace/theme/github");
  });

}).call(this);
