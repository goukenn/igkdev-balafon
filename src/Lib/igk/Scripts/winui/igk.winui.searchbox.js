"use strict";
(function(){
    igk.system.createNS("igk.winui.searchbox", {
        search(t, ii){
            var i = ii || $igk(t.o.parentNode).qselect('input').first();
            if (i){
                var u = t.getAttribute("igk:target-uri");
                var _id = t.getAttribute("igk:target-id") || "search";
                var tu = u.split("?");
                var q = tu.slice(1).join("");
                u = tu[0];
                var s = new URLSearchParams(q);
                s.delete(_id);
                if (i.o.value){
                    s.append(_id, i.o.value);  
                }
                igk.form.posturi(u+"?"+s.toString());
            }            
        }
    });
    igk.winui.initClassControl("igk-winui-searchbox", function(){
        this.on("keypress", function(e){
            if (e.keyCode == igk.winui.inputKeys.Enter){
                var p = $igk(this.parentNode).qselect(".igk-winui-searchbtn").first();
                if (p){
                    igk.winui.searchbox.search(p, $igk(e.target));
                }
            }
        });
    });
})();