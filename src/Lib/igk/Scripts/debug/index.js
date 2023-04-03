"use strict";
(function(){ 
    const is_debug = igk.ENVIRONMENT.DEV;
    let p = {};
    for(let i of ['log','warn','debug','info']){
        p[i] = function(){
            // console.log('call = '+i, ...arguments);
            if (is_debug){                 
                console[i].apply(null, [...arguments]);
            }
        };
    }
    igk.system.createNS("igk.debug", p);
})();