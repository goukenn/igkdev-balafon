//-------------------------------------------------------------------------------------------
// @id: igk-top-nav-bar
// @desc: 
// - used to managed top navigation bar
// - mark with attribute igk-top-nav-bar a div that will be used for top navigation bar
//-------------------------------------------------------------------------------------------
"use strict";
// @attr-component: igk-top-nav-bar
(function() {
    var cibling = "igk-top-nav-bar";
    igk.system.createNS("igk.ctrl.topnavbar", {
        init: function(target) {
            var q = $igk(target);
            var s = null;
            var _t = q.getAttribute("igk-nav-bar-target") || "^.igk-parentscroll";
            var _target = q.getAttribute('target');
            var _opts = igk.initObj(igk.JSON.parse(q.getAttribute("igk-nav-bar-options")), {
                offset: 0,
                'offset-target': undefined,
                'target':_target || undefined
            });
            s = q.select(_t);
            var offp = s.getItemAt(0);
            const v_r = _opts["offset-target"] ? $igk(_opts["offset-target"]): null;
            var tg_id = (tg_id = (v_r || null) ? tg_id.getItemAt(0) : null);
            q.o.removeAttribute('target');
            (function({target}){
                // + | copy target definition 
                target = target ? $igk(target).first() : null;
                if(!target){
                    return;
                }
                q.setHtml(target.o.outerHTML).init();                
            })(_opts);
            if (tg_id) {
                var g = igk.winui.GetScreenPosition(tg_id.o);
                _opts.offset = g.y + tg_id.o.scrollHeight;
            }
            function __bind(p) {
                var dx = p.scrollTop - _opts.offset;
                if ((dx) > 0) {
                    igk.winui.fitfix2(q, p, true, false);
                    q.addClass("igk-show");
                } else {
                    q.rmClass("igk-show");				
                }
            }
            function parentOffset(i) {
                var st = i.parentNode;
                //var g = null;
                while (st && (st.parentNode != null) && (st.parentNode != document.body)) {
                    st = st.parentNode;
                }
                return st;
            }
            igk.load(function() {
                if (offp)
                    var p = null;
                if (offp)
                    p = offp.o;
                else
                    p = parentOffset(target.o); 
                if (p) {
                    ns_igk.winui.reg_event(p, "scroll", function(evt) {                        
                        __bind(p);
                    });
                    __bind(p);
                } else {
                    console.debug("there is no parent offset");
                }
            });
        }
    });
    if (!igk.ctrl.isAttribManagerRegistrated(cibling)){
        igk.ctrl.registerAttribManager(cibling, { n: "js", desc: "register top nav bar" }); 
    } 
    igk.ctrl.bindAttribManager(cibling, function() { 
        var q = this;
        var source = igk.system.convert.parseToBool(this.getAttribute(cibling));
        if (source) {
            igk.ctrl.topnavbar.init(q);
        }
    });
})();