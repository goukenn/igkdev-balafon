'use strict';
(function() {
    igk.system.createNS("igk.uri", {
        getQueryArg: function(q) { // retrieve query argument from the query uri q
            return new(function() {
                var nb = [];
                var i = q.indexOf("?");
                if (i != -1) {
                    var e = q.substr(i + 1);
                    var ln = e.length,
                        pos = 0;
                    var c = 0;
                    var t = 0; // read mode type
                    var n = ""; // name
                    var v = ""; // value
                    while (pos < ln) {
                        c = e[pos];
                        switch (c) {
                            case "=":
                                t = 1;
                                break;
                            case "&":
                                nb[n] = v;
                                nb.push(v);
                                t = 0;
                                n = "";
                                v = "";
                                break;
                            default:
                                if (t == 0) {
                                    n += c;
                                } else
                                    v += c;
                                break;
                        }
                        pos++;
                    }
                    if (t == 1) {
                        nb[n] = v;
                        nb.push(v);
                    }
                    console.debug(nb);
                }
                igk.appendProperties(this, {
                    get: function(n) {
                        return nb[n];
                    }
                })
            })();
        },
        addquery: function(s, t) {
            if (s.length > 0)
                s += "&" + t;
            else
                s += "?" + t;
            return s;
        },
        getquery: function(ctrlname, funcName, params) {
            var s = "";
            if (ctrlname)
                s = igk.uri.addquery(s, "c=" + ctrlname);
            if (funcName)
                s = igk.uri.addquery(s, "f=" + funcName);
            if (params)
                s = igk.uri.addquery(s, params);
            return s;
        }
    });
})();