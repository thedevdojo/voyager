ace.define("ace/snippets/robot.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# scope: robot\n### Sections\nsnippet settingssection\ndescription *** Settings *** section\n\t*** Settings ***\n\tLibrary    ${1}\n\nsnippet keywordssection\ndescription *** Keywords *** section\n\t*** Keywords ***\n\t${1:Keyword Name}\n\t    [Arguments]    \\${${2:Example Arg 1}}\n\t\nsnippet testcasessection\ndescription *** Test Cases *** section\n\t*** Test Cases ***\n\t${1:First Test Case}\n\t    ${2:Log    Example Arg}\n\nsnippet variablessection\ndescription *** Variables *** section\n\t*** Variables ***\n\t\\${${1:Variable Name}}=    ${2:Variable Value}\n\n### Helpful keywords\nsnippet testcase\ndescription A test case\n\t${1:Test Case Name}\n\t    ${2:Log    Example log message}\n\t\nsnippet keyword\ndescription A keyword\n\t${1:Example Keyword}\n\t    [Arguments]    \\${${2:Example Arg 1}}\n\n### Built Ins\nsnippet forinr\ndescription For In Range Loop\n\tFOR    \\${${1:Index}}    IN RANGE     \\${${2:10}}\n\t    Log     \\${${1:Index}}\n\tEND\n\nsnippet forin\ndescription For In Loop\n\tFOR    \\${${1:Item}}    IN     @{${2:List Variable}}\n\t    Log     \\${${1:Item}}\n\tEND\n\nsnippet if\ndescription If Statement\n\tIF    ${1:condition}\n\t    ${2:Do something}\n\tEND\n\nsnippet else\ndescription If Statement\n\tIF    ${1:Condition}\n\t    ${2:Do something}\n\tELSE\n\t    ${3:Otherwise do this}\n\tEND\n\nsnippet elif\ndescription Else-If Statement\n\tIF    ${1:Condition 1}\n\t    ${2:Do something}\n\tELSE IF    ${3:Condition 2}\n\t    ${4:Do something else}\n\tEND\n";

});

ace.define("ace/snippets/robot",["require","exports","module","ace/snippets/robot.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./robot.snippets");
exports.scope = "robot";

});                (function() {
                    ace.require(["ace/snippets/robot"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            