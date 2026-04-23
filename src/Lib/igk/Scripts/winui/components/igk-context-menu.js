'use strict';
(function () {
    // controller setting up
    var m_contextMenu;
    function __ctr() {
        var m_target = igk.createNode("ul");
        m_target.addClass("igk-context-menu");
        var q = this;
        m_target.addClass("posfix");
        function __click(evt) {
            q.close();
            // evt.stopPropagation();
            // evt.preventDefault();
        }
        function __scroll(evt) {
            var b = $igk(q.contextCibling).getScreenLocation();
            var p = q.ciblingpos;
            if ((p.x != b.x) || (p.y != b.y)) {
                q.close();
            }
        }
        function __loadItemTo(n, t) {
            t = $igk(t);
            for (var i = 0; i < t.getChildCount(); i++) {
                __loadItem(n.add("li"), t.o.childNodes[i]);
            }
        };
        function __loadItem(n, t) {
            if (!t.tagName)
                return;
            switch (t.tagName.toLowerCase()) {
                case "li":
                    if (t.childNodes.length == 1) {
                        if (igk.html.isTextNode(t.childNodes[0])) {
                            var ajx = t.getAttribute("ajx") == 1;
                            var complete = "ns_igk.ajx.fn.replace_content(this.igk.contextMenu.contextCibling)";
                            var c = t.getAttribute("complete");
                            if (c) {
                                complete = c;
                            }
                            var g = t.getAttribute("class");
                            if (g != null) n.setAttribute("class", g);
                            n.add("a")
                                .setAttribute("href", t.getAttribute("uri"))
                                .appendProperties({ "contextMenu": q })
                                .setAttributeAssert(ajx, "onclick", "javascript: ns_igk.ajx.post(this.href,null," + complete + "); return !1;")
                                .setHtml(t.innerHTML);
                        } else {
                            __loadItemTo(n.add("ul").addClass("igk-context-sub"), t.childNodes[0]);
                        }
                    } else if (t.childNodes.length > 1) {
                        var ul = n.add("ul").addClass("igk-context-sub");
                        __loadItemTo(ul, t);
                    }
                    break;
                case "sep":
                    n.addClass("igk-context-menu-sep");
                    break;
            }
        }
        igk.appendProperties(this, {
            contextTarget: null,
            contextCibling: null,
            getTarget: function () { return m_target; },
            load: function (d) {
                m_target.setHtml(null);
                var dummy = igk.createNode("div");
                dummy.setHtml(d);
                __loadItemTo(m_target, dummy);
            },
            close: function () {
                // unreg event
                igk.winui.unreg_event(document, "click", __click);
                igk.qselect(".overflow-y-a").unreg_event("scroll", __scroll);
                var q = m_target;
                m_target.addClass("igk-trans-all-200ms").setCss({ "opacity": 0.0 }).timeOut(400,
                    function () {
                        m_target.rmClass("igk-trans-all-200ms igk-show");
                        q.o.parentNode.removeChild(q.o);
                        q.clearTimeOut();
                    }
                );
            },
            show: function (t, c, l) {
                // t:context target
                // c:cibling		
                // l:location			
                this.contextTarget = t;
                this.contextCibling = c;
                this.pos = l;
                this.ciblingpos = $igk(c).getScreenLocation();
                igk.dom.body().appendChild(m_target.o);
                m_target.addClass("posfix igk-show").setCss({
                    left: l.x + "px",
                    top: l.y + "px"
                }).addClass("igk-trans-all-200ms").setCss({ opacity: 1 }).timeOut(400, function () {
                    m_target.rmClass("igk-trans-all-200ms");
                });
                // register click 
                igk_winui_reg_event(document, "click", __click);
                // register scroll
                igk.qselect(".overflow-y-a").reg_event("scroll", __scroll);
            },
            toString: function () {
                return "igk.winui.contextmenu";
            }
        });
    };
    // init global ctx menu
    m_contextMenu = new __ctr();
    // define global context menu property
    igk.defineProperty(igk.winui, 'contextMenu', {
        get: function () { return m_contextMenu; },
        nopropfunc: function () { this.contextMenu = m_contextMenu; }
    });
    igk.winui.initClassControl("igk-context-menu", function () {
        // init all system class menu
        var id = $igk(this.getAttribute("igk:for"));
        if (!id)
            return;
        var q = this;
        var v = 0;
        q.close = function () {
            igk.winui.unreg_event(document, "click", __q_click);
            igk.qselect(".overflow-y-a").unreg_event("scroll", __q_scroll);
            q.addClass("igk-trans-all-200ms").setCss({ "opacity": 0.0 }).timeOut(400,
                function () {
                    q.rmClass("igk-trans-all-200ms igk-show");
                    // q.o.parentNode.removeChild(q.o);
                    q.clearTimeOut();
                }
            );
            v = 0;
        };
        function __q_click(evt) {
            q.close();
        };
        function __q_scroll(evt) {
            q.close();
        };
        q.show = function () {
            q.addClass("posfix igk-show").addClass("igk-trans-all-200ms").setCss({ opacity: 1 }).timeOut(400, function () {
                q.rmClass("igk-trans-all-200ms");
            });
            // reg event
            igk_winui_reg_event(document, "click", __q_click);
            igk.qselect(".overflow-y-a").reg_event("scroll", __q_scroll);
        };
        $igk(id).reg_event("click", function (evt) {
            if (v == 0) {
                q.show();
                evt.preventDefault();
                evt.stopPropagation();
                v = 1;
            }
        }).setCss({ "cursor": "pointer" });
    }, { desc: "igk context menu" });
})();