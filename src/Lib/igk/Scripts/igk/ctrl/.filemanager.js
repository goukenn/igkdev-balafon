"use strict";

(function()
{
    var options={};
    igk.system.createNS("igk.ctrl.filemanager", {
        newFile:function(){            
            $igk("igk-dialog.new-file").first().show(); 
        },
        init:function(u){
            options = u;
        }
    }); 
})();