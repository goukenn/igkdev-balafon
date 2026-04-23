"use strict";
(function(){
    igk.system.createNS("igk", {
        ClipBoard:{
            writeText(text){
                if (navigator.clipboard){
                    navigator.clipboard.writeText(text).then(()=>{
                        igk.DEBUG && console.log("data writed to clipboard");
                    }).catch ((e)=>{
                        igk.DEBUG && console.error("failed to write to  clipbard", e);
                    })
                }
            }
        }
    })
})();