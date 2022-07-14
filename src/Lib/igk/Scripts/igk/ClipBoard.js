"use strict";

(function(){
    igk.system.createNS("igk", {
        ClipBoard:{
            writeText(text){
                if (navigator.clipboard){
                    navigator.clipboard.writeText(text).then(()=>{
                        console.log("data writed to clipboard");
                    }).catch ((e)=>{
                        console.error("failed to write to  clipbard", e);
                    })
                }
            }
        }
    })
})();