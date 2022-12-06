'use strict';
(function() {
    igk.system.createNS("igk.io.xml", {
        parseString: function(txt, type) {
            var doc = new DOMParser();
            var xml = doc.parseFromString(txt, type || "text/xml");
            if (xml.documentElement.nodeName == "parsererror") {
                // document.write("Error in XML<br><br>" + xml.documentElement.childNodes[0].nodeValue);
                // alert("Error in XML\n\n" + xml.documentElement.childNodes[0].nodeValue);
                return false;
            }
            return xml;
        },
        parseUri: function(uri) {
            var g = igk.ajx.get(uri, null, null, false);
            var s = g.xhr.responseText;
            return s;
        }
    });
})();