// author: C.A.D. BONDJE DOUE
// desc: control to clone node
// date: 2022-03-14
// file: igk.winui.clonenode.js
"use script";
(function(){
    igk.winui.initClassControl("igk-winui-clonenode", function(){
        var t = this.getAttribute("igk:target"); 
        var c = this.getAttribute("igk:complete"); 
        if (t){
            t = $igk(t).first();
            if (t){
                var g = t.clone();
                g.rmClass("dispn");
                g.o.removeAttribute("id");
                g.o.removeAttribute("name");
                g.addClass("trans-opacity no-opacity");
                this.replaceBy(g);
                if (c){
                    var fc = new Function(c);
                    fc.apply(g);
                }
                setTimeout(() => {
                    g.rmClass("no-opacity");
                    g.on("transitionend", function(e){
                        if (e.target == g.o){
                            g.rmClass("trans-opacity");
                        }
                    });
                }, 200);
            }
        }else{
            this.remove();
        }
    });
})();