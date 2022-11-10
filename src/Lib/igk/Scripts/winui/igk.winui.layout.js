"use strict";
// layout management
(function(){
    igk.system.createNS("igk.winui.layout", {
        autofix_width(n){
            var self = n; 
            var t = n.o.parentNode;
            function update(){
                var x = p.o.offsetWidth - p.o.clientWidth;
                t.style.width = 'calc(100% - '+x+'px)';
            };
            var p = $igk(n.o.parentNode).select('^.igk-parentscroll').first();
            if (p){
                p.on("DOMChanged", function(){
                    update.apply(self);
                });
                update.apply(self);
            }
            n.remove();
        }
    });
})();