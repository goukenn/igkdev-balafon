(function() {
    const createNS = igk.system.createNS;
    createNS("igk.html", {
        string: function(text) {
            // string text
            text = text.replace(/\</g, "&lt;");
            text = text.replace(/\>/g, "&gt;");
            return text;
        },
        isTextNode: function(item) {
            return (item && (item.nodeType == 3));
        },
        closeEmptyTag: function(s, replacecallback) {
            function replacing(m) {
                var tag = m.trim().split(' ')[0].substring(1);
                m = m.replace("/>", "></" + tag + ">");
                return m;
            }
            var rg = new RegExp("((<)([^\/>])+(\/>))", "ig");
            return s.replace(rg, replacecallback || replacing);
        },
        appendQuery: function(uri, exp) {
            if (uri.indexOf('?') != -1) {
                uri = uri + "&" + exp;
            } else {
                uri = uri + "?" + exp;
            }
            return uri;
        },
        // getDefinition: $igk['global::']['igk_get_html_item_definition'],
        // getDefinitionValue: $igk['global::']['igk_get_html_item_definition_value'],
        addToHead: function(n) {
            if (document.head) {
                document.head.appendChild(n);
            } else { // internet explorer 8 no get head property defined for document
                igk.ready(function() {
                    // set the head
                    document.head = document.getElementsByTagName("head")[0];
                    document.head.appendChild(n);
                });
            }
        }
    });
})();