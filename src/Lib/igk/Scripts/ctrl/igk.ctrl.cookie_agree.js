"use strict";

(function(){
    function _hide(t){
        t.on("transitionend", function(){
            t.remove();
        }).addClass("hide");
    };
    igk.system.createNS("igk.ctrl", {
        cookie_agree(type, t){
            if (t){
                if (t = $igk(t).first()){
                 _hide(t);
                }
            }
            igk.web.setcookies("agree", 1);
            igk.web.setcookies("agree-type", type);
        },
        init(t){
            // in case os is mobile just hide it
            if (t){
                if (t = $igk(t).first()){
                    let _mobile = igk.navigator.isAndroid() || igk.navigator.isIOS();
                    if (_mobile){
                        _hide(t);
                    }
                }
            }
        }
    })
})();