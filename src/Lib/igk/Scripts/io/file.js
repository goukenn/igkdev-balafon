'use strict';
(function() {
    igk.system.createNS("igk.io.file", {
        // / TODO::check to download data builded with javascript
        download: function(t, n, v) {
            // t: mime-type image/png
            // n: name
            // v: value 
            var a = igk.createNode("a");
            var data = new Blob([v], { "type": t });
            igk.dom.body().appendChild(a.o); // not require in IE 
            a.o.download = n || "file.data"; // f
            a.o.href = URL.createObjectURL(data);
            a.o.type = t;
            a.o.click();
            a.remove();
            return 1;
        },
        /**
         * download the current data
         * */
        downloadData: function(name, data) {
            var a = igk.createNode("a");
            igk.dom.body().appendChild(a.o); // not require in IE 
            a.o.download = name;
            a.o.href = data;
            a.o.click();
            a.remove();
        },
        // load a html or text document,
        // >u: uri
        // >f: callback function on document recieve
        load: function(u, f) { //load file argument
            if (!f)
                throw ("argument f require");
            var eCb = f.error || null; // error callback
            var eComplete = typeof(f) == "function" ? f : f.complete;
            f = eComplete;
            function getNodeData(n) {
                var dummy = igk.createNode("dummy");
                var c = n.childNodes.length;
                var i = 0;
                var m = null;
                // copy node
                while (c > 0) {
                    m = n.childNodes[i];
                    switch (m.nodeType) {
                        case 8: // comment
                        case 3: // empty text node
                        case 11:
                        case 10:
                            i++;
                            break;
                        default:
                            dummy.o.appendChild(n.childNodes[i]);
                            break;
                    }
                    c--;
                }
                var v_d = dummy.getHtml(); // ob.o.contentDocument.documentElement.getElementsByTagName("body")[0].innerHTML;		
                if (v_d == null) {
                    return null;
                }
                return v_d;
            };
            var __loaded = 0;
            var ob = igk.createNode('object');
            ob.addClass('no-visible posab').setCss({ width: '1px', height: '1px' });
            // !important  for ie prepend data		
            ob.o.type = igk.navigator.isXBoxOne() ? "text/html" : "text/plain"; // on xbox one error		
            // ob.o.type="text/html";
            // alert(ob.o.type);		
            igk.dom.body().prepend(ob);
            ob.reg_event("error", function(evt) {
                if (eCb) {
                    eCb.apply(ob);
                    eCb = null;
                }
                if (__loaded || (igk.navigator.isFirefox() && igk.navigator.getFirefoxVersion() >= 50)) {
                    return;
                }
                f.apply(document, [{
                    error: 1,
                    data: evt,
                    msg: "/!\\error: " + u
                }]);
                ob.remove();
            });
            ob.reg_event("load", function(evt) {
                // alert(ob.o.contentDocument.readyState);
                try {
                    if (!ob.o.contentDocument || ob.o.contentDocument.readyState != "complete")
                        return;
                    var s = ob.o.contentDocument;
                    var d = getNodeData(s);
                    f.apply(document, [{
                        data: d,
                        document: s,
                        uri: u
                    }]);
                    __loaded = 1;
                } catch (e) {
                    console.error("Exception arrived : " + e);
                } finally {
                    setTimeout(function() { ob.remove(); }, 0);
                }
            });
            // load data
            // cause error for visual studio webBrowser
            // igk.show_prop(igk.getSettings());
            // ob.o.type  = "";// text/html";
            // alert("setting request ? " +u+" "+igk.getSettings().nosymbol);
            ob.o.data = u;
        }
    });
})();