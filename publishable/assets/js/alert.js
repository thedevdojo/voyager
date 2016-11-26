$(function() {
  var HtmlMode, editor;
  HtmlMode = ace.require("ace/mode/html").Mode;
  editor = ace.edit("code-preview-alert");
  editor.getSession().setMode(new HtmlMode());
  return editor.setTheme("ace/theme/github");
});
