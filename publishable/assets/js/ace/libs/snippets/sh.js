ace.define("ace/snippets/sh.snippets",["require","exports","module"], function(require, exports, module){module.exports = "# Shebang. Executing bash via /usr/bin/env makes scripts more portable.\nsnippet #!\n\t#!/usr/bin/env bash\n\t\nsnippet if\n\tif [[ ${1:condition} ]]; then\n\t\t${2:#statements}\n\tfi\nsnippet elif\n\telif [[ ${1:condition} ]]; then\n\t\t${2:#statements}\nsnippet for\n\tfor (( ${2:i} = 0; $2 < ${1:count}; $2++ )); do\n\t\t${3:#statements}\n\tdone\nsnippet fori\n\tfor ${1:needle} in ${2:haystack} ; do\n\t\t${3:#statements}\n\tdone\nsnippet wh\n\twhile [[ ${1:condition} ]]; do\n\t\t${2:#statements}\n\tdone\nsnippet until\n\tuntil [[ ${1:condition} ]]; do\n\t\t${2:#statements}\n\tdone\nsnippet case\n\tcase ${1:word} in\n\t\t${2:pattern})\n\t\t\t${3};;\n\tesac\nsnippet go \n\twhile getopts '${1:o}' ${2:opts} \n\tdo \n\t\tcase $$2 in\n\t\t${3:o0})\n\t\t\t${4:#staments};;\n\t\tesac\n\tdone\n# Set SCRIPT_DIR variable to directory script is located.\nsnippet sdir\n\tSCRIPT_DIR=\"$( cd \"$( dirname \"${BASH_SOURCE[0]}\" )\" && pwd )\"\n# getopt\nsnippet getopt\n\t__ScriptVersion=\"${1:version}\"\n\n\t#===  FUNCTION  ================================================================\n\t#         NAME:  usage\n\t#  DESCRIPTION:  Display usage information.\n\t#===============================================================================\n\tfunction usage ()\n\t{\n\t\t\tcat <<- EOT\n\n\t  Usage :  $${0:0} [options] [--] \n\n\t  Options: \n\t  -h|help       Display this message\n\t  -v|version    Display script version\n\n\tEOT\n\t}    # ----------  end of function usage  ----------\n\n\t#-----------------------------------------------------------------------\n\t#  Handle command line arguments\n\t#-----------------------------------------------------------------------\n\n\twhile getopts \":hv\" opt\n\tdo\n\t  case $opt in\n\n\t\th|help     )  usage; exit 0   ;;\n\n\t\tv|version  )  echo \"$${0:0} -- Version $__ScriptVersion\"; exit 0   ;;\n\n\t\t\\? )  echo -e \"\\n  Option does not exist : $OPTARG\\n\"\n\t\t\t  usage; exit 1   ;;\n\n\t  esac    # --- end of case ---\n\tdone\n\tshift $(($OPTIND-1))\n\n";

});

ace.define("ace/snippets/sh",["require","exports","module","ace/snippets/sh.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./sh.snippets");
exports.scope = "sh";

});                (function() {
                    ace.require(["ace/snippets/sh"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            