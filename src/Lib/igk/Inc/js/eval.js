// @ts-nocheck
'use strict';

(function() {
    var i = document.scripts[document.scripts.length - 2];
    if (typeof(i.text) == "undefined") {
        return;
    }

    function loadXml(s) {
        var r = null;
        if ("DOMParser" in window) {
            var g = (new window.DOMParser()).parseFromString(s, "text/xml");
            r = g.firstChild;
            if (r && r.tagName.toLowerCase() == "parsererror") {
                return null;
            }
        } else {
            r = igk.dom.activeXDocument();
            r.load(s);
        }
        return r;
    }
    // after loading core script 
    function __initScript(i) {
        let r=null,s = i.text.trim().substring(2);        
        if (r = loadXml("<data>" + s + "</data>")) {
            if (r.lastChild) {
                var b = r.lastChild.textContent;
                try {
                    if (b && (b.length > 0)) {
                        (new Function(b)).apply();
                    }
                } catch (e) {
                    // + | handling error 
                    console.error('Error:igk-winui-balafon-js-inc : ' + e.message);
                    console.log('inline script: ', e.lineNumber + ":" + e.columnNumber);
                    window.__igk_var_script_error = b;                    
                    if (b){
                        console.log(b.substring(Math.min(0, e.columnNumber-100),200)); 
                    } 
                }
            }
        } else {
            console.debug("failed to parse core data")
        }
    }
    __initScript(i);
    if (typeof(igk) != 'undefined'){
        igk.system.createNS("igk.js", {
            initEmbededScript() {
                let i = document.scripts[document.scripts.length - 2];
                if (i)
                    __initScript(i)
            }
        });
    }
})();