igk.ctrl.registerAttribManager("igk-js-fix-loc-scroll-width", { desc: "register element to be fixed with scroll width" });
igk.ctrl.registerAttribManager("igk-js-fix-loc-scroll-height", { desc: "register element to be fixed with scroll height" });
igk.ctrl.registerAttribManager("igk-fix-loc-target", { desc: "defined target parent #id. that will content the parent class. if not defined parent with element parentNode or igk-parentscroll class" });
igk.ctrl.bindAttribManager("igk-js-fix-loc-scroll-width", function() {
    function _fixManager(target) {
        var animinfo = {
            duration: 200,
            interval: 10,
            animtype: "timeout",
            context: "fix-scroll-context",
            effect: "circ",
            effectmode: "easeinout"
        };
        function __update() {
            var s = this.target.getAttribute("igk-js-fix-loc-scroll-width");
            var h = this.target.getAttribute("igk-js-fix-loc-scroll-height");
            var v_target = this.target;
            var p = this.target.getComputedStyle("position");
            if ((p == "fixed")) {
                var m = this.target.getAttribute("igk-fix-loc-target");
                var g = null;
                if (m == null) {
                    m = igk.qselect(".igk-parentscroll").first();
                    if (m == null) {
                        m = this.target.o.parentNode;
                    }
                }
                g = $igk(m);
                if (g == null) { // no parent
                    return;
                }
                var animprop = {};
                if (s == 1) {
                    igk.appendProperties(animprop, { "right": "0px" });
                    if (g.fn.hasVScrollBar()) {
                        animprop.right = g.fn.vscrollWidth() + "px";
                    }
                }
                if (h == 1) {
                    igk.appendProperties(animprop, { "bottom": "0px" });
                    if (g.fn.hasHScrollBar()) {
                        animprop.bottom = g.fn.hscrollHeight() + "px";
                    }
                }
                this.target.animate(animprop, animinfo);
            }
        };
        igk.appendProperties(this, {
            target: target,
            update: __update
        });
        var self = this;
        var m_eventContext = igk.winui.RegEventContext(this, $igk(this));
        if (m_eventContext) {
            m_eventContext.reg_window("resize", function() { self.update(); });
        }
        this.update();
        return this;
    }
    var s = this.getAttribute("igk-js-fix-loc-scroll-width");
    if ((s == 1) && (this.getComputedStyle("position") == "fixed")) {
        var v_fm = new _fixManager(this);
    }
    return !1;
});