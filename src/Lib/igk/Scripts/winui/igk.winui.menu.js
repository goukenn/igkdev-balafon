"use strict";
(function() {
    var selectors = {};
    var _ids = {};
    function uid() {
        return (performance.now().toString(36) + Math.random().toString(36)).replace(/\./g, "");
    };
    function _id_css(s) {
        id = s.o.getAttribute("id");
        if (!id) {
            if (!s.fn.ids) {
                var id = "css" + uid();
                s.addClass(id);
                s.fn.ids = id;
            }
        }
        return s.getCssSelector();
    };
    function _addcss(id) {
        var c = igk.dom.body().add("style");
        c.o["type"] = "text/css";
        c.o["id"] = id;
        return c;
    };
    function get_auto_height(q) {
        var s = q.o.getAttribute("style");
        var o = q.getComputedStyle('height');
        q.setCss({ height: "auto" });
        q.o.blur();
        var r = q.getComputedStyle('height');
        if (s)
            q.o.setAttribute("style", s);
        else
            q.o.removeAttribute("style");
        r = (r == "0px") ? q.o.scrollHeight + "px" : r;
        return r;
    };
    function _change_a(a, sub) {
        a.addClass({
            "expand": sub.supportClass("expand"),
        });
    };
    igk.system.createNS("igk.winui.menu.accordeonMenu", {
        /**
         * init accordeonMenu accordeons menu
         * @param {*} p target node 
         * @param {*} options setting
         */
        init(p, options) {
            var q = $igk(p);
            var expand_item = null;
            var expand_item_a = null;
            var type = (options ? options.type : null) || 0;
            function _toggle_single(_id, sub, a) {
                if (!(_id in selectors)) {
                    var mh = get_auto_height(sub);
                    _addcss(_id).o.sheet.addRule(_id + ".expand", "height:" + mh);
                    selectors[_id] = { 'maxHeight': mh };
                }
                // console.debug( "max: ", sub.getComputedStyle("height") , sub.o.offsetHeight, sub.o, _id); 
                sub.toggleClass("expand");
                if (sub != expand_item) {
                    if (expand_item) {
                        // close expanded item
                        expand_item.toggleClass("expand");
                    }
                    if (expand_item_a && expand_item) {
                        _change_a(expand_item_a, expand_item);
                    }
                    expand_item = sub;
                } else {
                    expand_item = null;
                }
                //if (sub.supportClass("expand")){
                var g = a.select(".expand-icon").first();
                if (g) {
                    _change_a(g, sub);
                    expand_item_a = g;
                    // console.debug('set class ', g.o.className, "support ", sub.supportClass("expand"));
                }
                //}
                // console.debug('change');
            };
            q.qselect('a').each_all(function(a) {
                var a = this;
                a.on("click", function(e) {
                    var sub = a.o.nextSibling || $igk(a.o.parentNode).select('ul').first();
                    if (sub) {
                        sub = $igk(sub);
                        var _id = _id_css(sub).replace(/\.expand( |$)?/, "");
                        switch (type) {
                            case 0:
                            default:
                                _toggle_single(_id, sub, a);
                                break;
                        }
                    }
                });
            });
        }
    });
})();