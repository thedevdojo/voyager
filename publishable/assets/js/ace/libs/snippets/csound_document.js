ace.define("ace/snippets/csound_document.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# <CsoundSynthesizer>\nsnippet synth\n\t<CsoundSynthesizer>\n\t<CsInstruments>\n\t${1}\n\t</CsInstruments>\n\t<CsScore>\n\te\n\t</CsScore>\n\t</CsoundSynthesizer>\n";

});

ace.define("ace/snippets/csound_document",["require","exports","module","ace/snippets/csound_document.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./csound_document.snippets");
exports.scope = "csound_document";

});                (function() {
                    ace.require(["ace/snippets/csound_document"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            