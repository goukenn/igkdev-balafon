"use strict";
(function(){
function init_dialog(){
    let m_show = 0;
    this.show = function(){
        if (this.showDialog)
            return;
        this.showDialog = 1;
        m_show = 1;
        this.rmClass("dispn");
        var q = this;
        var next = q.o.nextCibling;
        var parent = q.o.parentNode;
        igk.winui.notify.showMsBox({
            title:q.getAttribute("igk:title") || "Dialog",
            type: q.getAttribute("igk:dialogtype") || "default",
            content: q.o,
            closeButton:0,
            settings:
                 {close:function(){
                    q.hide();  
                    if (next){
                        next.parentNode.insertBefore(next, q.o);
                    }else{
                        parent.appendChild(q.o);
                    }
                q.showDialog = null;  
            }}});
        };
    this.hide = function(){
        this.addClass("dispn");
    };
};
//on ready 
igk.winui.initClassControl("igk-dialog", init_dialog);
})();