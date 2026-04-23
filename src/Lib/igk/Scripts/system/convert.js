    // author: C.A.D. BONDJE DOUE
    // file: convert.js
    // @date: 20230102 14:43:14
    // @desc: 
    'use strict';
    (function() {
        // igk.system.convert namespace
        igk.system.createNS("igk.system.convert", {
            parseToInt: function(i) {
                var v = parseInt(i);
                if (Number.isNaN(v))
                    v = 0;
                return v;
            },
            parseToBool: function(i) {
                if (typeof(i) == "string") {
                    switch (i.toLowerCase()) {
                        case "true":
                        case "1":
                            return !0;
                    }
                    return !1;
                }
                if (i)
                    return !0;
                return !1;
            },
            HexP: function(r) {
                var g = (r >= 10) ? String.fromCharCode(parseInt("A".charCodeAt(0) + (r - 10))) : "" + r;
                return g;
            },
            ToBase: function(d, base, length) {
                if (typeof(length) == IGK_UNDEF)
                    length = -1;
                if (typeof(d) == IGK_UNDEF)
                    return "UX";
                if (Number.isNaN(d) || Number.isNaN(base))
                    return "UX";
                d = parseInt(d);
                var o = "";
                var hpex = igk.system.convert.HexP;
                var ToBase = igk.system.convert.ToBase;
                if (base > 0) {
                    var p = parseInt(d / base);
                    var r = d % base;
                    if (p < base) {
                        if (p != 0)
                            o = hpex(p) + "" + hpex(r);
                        else
                            o = hpex(r);
                    } else {
                        o = hpex(r) + o;
                        o = ToBase(p, base) + o;
                    }
                }
                if (length != -1) {
                    for (var i = o.length; i < length; i++) {
                        o = "0" + o;
                    }
                }
                return o;
            }
        });
    })();