$(function() {
  var HtmlMode, editor;
  HtmlMode = ace.require("ace/mode/html").Mode;
  editor = ace.edit("code-preview-card");
  editor.getSession().setMode(new HtmlMode());
  editor.setTheme("ace/theme/github");
  editor = ace.edit("code-preview-card-profile");
  editor.getSession().setMode(new HtmlMode());
  editor.setTheme("ace/theme/github");
  editor = ace.edit("code-preview-card-banner");
  editor.getSession().setMode(new HtmlMode());
  editor.setTheme("ace/theme/github");
  editor = ace.edit("code-preview-card-jumbotron");
  editor.getSession().setMode(new HtmlMode());
  return editor.setTheme("ace/theme/github");
});
