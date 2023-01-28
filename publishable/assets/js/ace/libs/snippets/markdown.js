ace.define("ace/snippets/markdown.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# Markdown\n\n# Includes octopress (http://octopress.org/) snippets\n\nsnippet [\n\t[${1:text}](http://${2:address} \"${3:title}\")\nsnippet [*\n\t[${1:link}](${2:`@*`} \"${3:title}\")${4}\n\nsnippet [:\n\t[${1:id}]: http://${2:url} \"${3:title}\"\nsnippet [:*\n\t[${1:id}]: ${2:`@*`} \"${3:title}\"\n\nsnippet ![\n\t![${1:alttext}](${2:/images/image.jpg} \"${3:title}\")\nsnippet ![*\n\t![${1:alt}](${2:`@*`} \"${3:title}\")${4}\n\nsnippet ![:\n\t![${1:id}]: ${2:url} \"${3:title}\"\nsnippet ![:*\n\t![${1:id}]: ${2:`@*`} \"${3:title}\"\n\nsnippet ===\nregex /^/=+/=*//\n\t${PREV_LINE/./=/g}\n\t\n\t${0}\nsnippet ---\nregex /^/-+/-*//\n\t${PREV_LINE/./-/g}\n\t\n\t${0}\nsnippet blockquote\n\t{% blockquote %}\n\t${1:quote}\n\t{% endblockquote %}\n\nsnippet blockquote-author\n\t{% blockquote ${1:author}, ${2:title} %}\n\t${3:quote}\n\t{% endblockquote %}\n\nsnippet blockquote-link\n\t{% blockquote ${1:author} ${2:URL} ${3:link_text} %}\n\t${4:quote}\n\t{% endblockquote %}\n\nsnippet bt-codeblock-short\n\t```\n\t${1:code_snippet}\n\t```\n\nsnippet bt-codeblock-full\n\t``` ${1:language} ${2:title} ${3:URL} ${4:link_text}\n\t${5:code_snippet}\n\t```\n\nsnippet codeblock-short\n\t{% codeblock %}\n\t${1:code_snippet}\n\t{% endcodeblock %}\n\nsnippet codeblock-full\n\t{% codeblock ${1:title} lang:${2:language} ${3:URL} ${4:link_text} %}\n\t${5:code_snippet}\n\t{% endcodeblock %}\n\nsnippet gist-full\n\t{% gist ${1:gist_id} ${2:filename} %}\n\nsnippet gist-short\n\t{% gist ${1:gist_id} %}\n\nsnippet img\n\t{% img ${1:class} ${2:URL} ${3:width} ${4:height} ${5:title_text} ${6:alt_text} %}\n\nsnippet youtube\n\t{% youtube ${1:video_id} %}\n\n# The quote should appear only once in the text. It is inherently part of it.\n# See http://octopress.org/docs/plugins/pullquote/ for more info.\n\nsnippet pullquote\n\t{% pullquote %}\n\t${1:text} {\" ${2:quote} \"} ${3:text}\n\t{% endpullquote %}\n";

});

ace.define("ace/snippets/markdown",["require","exports","module","ace/snippets/markdown.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./markdown.snippets");
exports.scope = "markdown";

});                (function() {
                    ace.require(["ace/snippets/markdown"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            