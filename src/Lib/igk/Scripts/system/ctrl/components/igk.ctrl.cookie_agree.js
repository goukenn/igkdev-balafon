"use strict";
(function() {
    function _hide(t) {
        t.on("transitionend", function() {
            t.remove();
        }).addClass("hide");
    };
    igk.system.createNS("igk.ctrl.cookie_agree", {
        agree(type, t, n) {
            if (t) {
                if (t = $igk(t).first()) {
                    _hide(t);
                }
            }
            igk.cookies.set(n, 1);
            igk.cookies.set("agree-type", type);
        },
        /**
         * init cookie aggree 
         * @param {*} t target selector
         * @param {*} n cookie name
         */
        init(t, n) {
            // in case os is mobile just hide it
            if (t) {
                if (t = $igk(t).first()) {
                    let _mobile = igk.navigator.isAndroid() || igk.navigator.isIOS();
                    let _g = igk.web.getcookies(n);
                    if (_g || _mobile) {
                        _hide(t);
                    }
                }
            }
        }
    })
})();