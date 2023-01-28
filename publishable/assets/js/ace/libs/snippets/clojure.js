ace.define("ace/snippets/clojure.snippets",["require","exports","module"], function(require, exports, module){module.exports = "snippet comm\n\t(comment\n\t  ${1}\n\t  )\nsnippet condp\n\t(condp ${1:pred} ${2:expr}\n\t  ${3})\nsnippet def\n\t(def ${1})\nsnippet defm\n\t(defmethod ${1:multifn} \"${2:doc-string}\" ${3:dispatch-val} [${4:args}]\n\t  ${5})\nsnippet defmm\n\t(defmulti ${1:name} \"${2:doc-string}\" ${3:dispatch-fn})\nsnippet defma\n\t(defmacro ${1:name} \"${2:doc-string}\" ${3:dispatch-fn})\nsnippet defn\n\t(defn ${1:name} \"${2:doc-string}\" [${3:arg-list}]\n\t  ${4})\nsnippet defp\n\t(defprotocol ${1:name}\n\t  ${2})\nsnippet defr\n\t(defrecord ${1:name} [${2:fields}]\n\t  ${3:protocol}\n\t  ${4})\nsnippet deft\n\t(deftest ${1:name}\n\t    (is (= ${2:assertion})))\n\t  ${3})\nsnippet is\n\t(is (= ${1} ${2}))\nsnippet defty\n\t(deftype ${1:Name} [${2:fields}]\n\t  ${3:Protocol}\n\t  ${4})\nsnippet doseq\n\t(doseq [${1:elem} ${2:coll}]\n\t  ${3})\nsnippet fn\n\t(fn [${1:arg-list}] ${2})\nsnippet if\n\t(if ${1:test-expr}\n\t  ${2:then-expr}\n\t  ${3:else-expr})\nsnippet if-let \n\t(if-let [${1:result} ${2:test-expr}]\n\t\t(${3:then-expr} $1)\n\t\t(${4:else-expr}))\nsnippet imp\n\t(:import [${1:package}])\n\t& {:keys [${1:keys}] :or {${2:defaults}}}\nsnippet let\n\t(let [${1:name} ${2:expr}]\n\t\t${3})\nsnippet letfn\n\t(letfn [(${1:name) [${2:args}]\n\t          ${3})])\nsnippet map\n\t(map ${1:func} ${2:coll})\nsnippet mapl\n\t(map #(${1:lambda}) ${2:coll})\nsnippet met\n\t(${1:name} [${2:this} ${3:args}]\n\t  ${4})\nsnippet ns\n\t(ns ${1:name}\n\t  ${2})\nsnippet dotimes\n\t(dotimes [_ 10]\n\t  (time\n\t    (dotimes [_ ${1:times}]\n\t      ${2})))\nsnippet pmethod\n\t(${1:name} [${2:this} ${3:args}])\nsnippet refer\n\t(:refer-clojure :exclude [${1}])\nsnippet require\n\t(:require [${1:namespace} :as [${2}]])\nsnippet use\n\t(:use [${1:namespace} :only [${2}]])\nsnippet print\n\t(println ${1})\nsnippet reduce\n\t(reduce ${1:(fn [p n] ${3})} ${2})\nsnippet when\n\t(when ${1:test} ${2:body})\nsnippet when-let\n\t(when-let [${1:result} ${2:test}]\n\t\t${3:body})\n";

});

ace.define("ace/snippets/clojure",["require","exports","module","ace/snippets/clojure.snippets"], function(require, exports, module){"use strict";
exports.snippetText = require("./clojure.snippets");
exports.scope = "clojure";

});                (function() {
                    ace.require(["ace/snippets/clojure"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            