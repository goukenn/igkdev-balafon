// author: C.A.D. BONDJE DOUE
// file: notifydialog.js
// @date: 20230102 14:20:32
// @desc:  notification dialog
'use strict';
(function() {
    const createNS = igk.system.createNS;
    const is_string = igk.is_string;
    const IGK_FUNC = 'function';
    const IGK_UNDEF = 'undefined';
    const IGK_CLASS = 'class';
    // notify visible
    var m_nv = false;
    var bdiv = null; // shared bdiv
    var notify_frames = [];
    igk.publisher.register("system/bodychange", function() {
        if (m_nv) {
            // item was visible
            if (bdiv != null) {
                igk.dom.body().appendChild(bdiv.o);
            }
        }
    });
    // .STATIC notify objcet
    igk.winui.notify = function() { // notification class .atomic
        if (this.type && (this.type == IGK_CLASS)) {
            if (typeof(this.notify.getInstance) == IGK_UNDEF) {
                this.notify.getInstance = (function() {
                    var _instance = new igk.winui.notify();
                    return function() {
                        return _instance;
                    };
                })();
            }
            return this.notify.getInstance();
        }
        if (typeof(igk.winui.notify.getInstance) == IGK_UNDEF) {
            igk.appendProperties(this, {
                name: "igk-notifybox",
                toString: function() {
                    return this.name;
                }
            });
            igk.appendProperties(this, igk.winui.notify);
            igk.winui.notify.sm_instance = this;
        } else
            return igk.winui.notify.getInstance();
    };
    var defStyle = " google-Roboto";
    // merge with notify access.
    createNS("igk.winui.notify", {
        setFilterClass: function(f) {
            if (bdiv != null)
                bdiv.filters = f;
        },
        // >msg:message
        // >type: type of notify
        // >nc: noclose,
        closeAlls: function() {
            if (notify_frames && notify_frames.length > 0) {
                for (var i = 0; i < notify_frames.length; i++) {
                    notify_frames[i].close();
                }
                notify_frames = []; // reset frames
            }
        },
        showMsg: function(msg, type, nc, settings) {
            // msg : content message
            // type : of the content notification
            // nc: close button
            if (typeof(this) == IGK_FUNC) { // static object
                igk.winui.notify().showMsg(msg, type, nc, settings);
                return;
            }
            var div = null;
            var m_hide = false;
            var dial = null;
            var _sm = igk.winui.notify;
            // create shared data
            if (!this.initialize) {
                bdiv = igk.createNode("div");
                this.target = bdiv;
                this.initialize = !0;
                bdiv.filters = "igk-bgfilter-blur"; // default filter class
            }
            bdiv = this.target;
            dial = this;
            if (!bdiv)
                return;
            // setup new content
            bdiv.setHtml(""); // clear
            bdiv.addClass("igk-js-notify-box");
            var div = bdiv.add("div");
            var cl = "igk-js-notify ";
            if (type) {
                cl += type;
            }
            div.addClass(cl).setOpacity(1);
            div.setAttribute("igk-js-fix-loc-scroll-width", "1");
            div.setHtml(""); // clear
            if (typeof(msg) == "string") {
                div.setHtml(msg); // clear content with message
            } else {
                div.appendChild(msg);
            }
            var fc = igk.winui.events.fn.handleKeypressExit({
                target: this,
                complete: _g_close_notify
            });
            // append close button
            if (!nc) {
                var close = igk.createNode("div");
                close.addClass("dispb posab loc_r loc_t");
                close.add("a", {
                    "onclick": _g_close_notify
                }).setHtml("close").addClass("igk-notify-btn-close");
                div.appendChild(close);
            }
            // select all and close for cancel button
            div.qselect("input[type='button'][data-type='cancel']").each_all(function() {
                this.on("click", _g_close_notify);
            });
            function _g_close_notify() {
                _close_notify(null);
            };
            function _setupview() {
                if (!div.parentNode){
                    return;
                }
                div.setCss({
                    "height": "auto" // auto by default
                });
                var p = -(div.getHeight() / 2.0);
                var pn = div.getParentNode();
                var t = 0; // to get half position
                var h = div.getHeight() + "px";
                var oflow = false;
                if (pn) {
                    t = pn.getHeight() / 2.0;
                } else {
                    t = div.getTop();
                }
                if ((t + p) < 0) {
                    p = -t;
                    if (pn.getHeight() < div.getHeight()) {
                        h = pn.getHeight() + "px";
                        oflow = true;
                    }
                }
                div.setCss({
                    "marginTop": p + "px"
                });
                if (h != div.getComputedStyle('height')) {
                    // ========				
                    div.animate({ height: h }, {
                        duration: 200,
                        interval: 10,
                        animtype: "timeout",
                        context: "notify-height-context",
                        effect: "circ",
                        effectmode: "easeinout",
                        complete: function() {
                            if (!oflow) {
                                $igk(div).setCss({ height: "auto", overflowY: "hidden" });
                            } else {
                                $igk(div).setCss({ overflowY: "auto" });
                            }
                        }
                    });
                }
            };
            function _setupview_callback() {
                _setupview();
                igk.winui.unreg_event(window, "load", _setupview_callback);
            };
            // append static method to current instance
            _sm.Close = _close_notify;
            _sm.UpdateView = _setupview;
            _sm.settings = settings;
            var __closing = false;
            function _close_notify(callback) {
                if (__closing)
                    return;
                __closing = !0;
                igk.animation.fadeout(div.o, 20, 200, 1.0, function() {
                    var pn = bdiv.o.parentNode;
                    if (pn) {
                        pn.removeChild(bdiv.o);
                        if (bdiv.filters)
                            $igk(pn).rmClass(bdiv.filters);
                    }
                    bdiv.unregister();
                    __closing = false;
                    m_nv = false;
                    if (callback) {
                        callback.apply(document);
                    }
                    if (settings && settings.close) {
                        settings.close.apply(_sm);
                    }
                });
                // unregister key press
                igk.winui.unreg_event(document, "keypress", fc);
                if (m_eventContext) {
                    m_eventContext.unreg_window("resize", _setupview);
                }
                m_hide = !0;
                dial.hide = !0;
            }
            igk.winui.reg_event(window, "load", __show);
            if (!nc) {
                if (igk.navigator.isFirefox()) {
                    igk.winui.reg_event(document, "keypress", fc);
                } else {
                    igk.winui.reg_event(document, "keydown", fc);
                }
            }
            var m_eventContext = igk.winui.RegEventContext(this, $igk(this));
            if (m_eventContext) {
                m_eventContext.reg_window("resize", _setupview);
            }
            notify_frames.push({
                close: _g_close_notify
            });
            function __show() {
                // because of the ready call you must call only when showMsg
                if (m_hide)
                    return;
                igk.dom.body().addClass(bdiv.filters).appendChild(bdiv.o);
                if (!igk.is_readyRegister(__show)) {
                    _setupview();
                }
                // + | default filter properties
                div.setCss({
                    "zIndex": "800", // set to top index
                    "top": "50%",
                    "overflowY": "auto",
                    "overflowX": "hidden",
                    "transform": "TranslateY(-50%)",
                    "minHeight":"300px"
                });
                igk.ctrl.callBindAttribData(div.o);
                bdiv.addClass("igk-show");
                m_nv = true;
                // igk.animation.fadein(bdiv.o,20,200,{form:0.0,to:0.8});
            };
            igk.ready(__show);
        },
        showMsBox: function(t, m, cln, nc) { // nc : no close button
            var settings = null;
            if (typeof(t) == 'object') {
                var b = t;
                t = b.title;
                m = b.content;
                cln = b.type;
                nc = b.closeButton;
                settings = b.settings;
            }
            var q = igk.createNode("div")
                .addClass("igk-notify");
            if (cln) {
                q.addClass(cln);
            }
            q = q.add("div").addClass("igk-container");
            var content = q.add("div");
            content.addClass("content-z"); // content zone
            var box = content.add("div");
            box.addClass("title igk-title-4");
            if (is_string(t)) {
                box.setHtml(t);
            } else {
                box.add(t);
            }
            if (is_string(m)) {
                content.add("div").addClass("igk-panel igk-notify-panel").setHtml(m);
            } else {
                content.add("div").addClass("igk-panel").o.appendChild($igk(m).o);
            }
            igk.winui.notify.showMsg(q, cln, nc, settings);
        },
        showError: function(msg) {
            var q = igk.createNode("div")
                .addClass("igk-notify igk-notify-danger" + defStyle)
                .setHtml(msg);
            igk.winui.notify.showMsg(q.o.outerHTML, "igk-danger");
        },
        showErrorInfo: function(title, msg) {
            var q = igk.createNode("div")
                .addClass("igk-notify igk-notify-danger" + defStyle);
            q.add("div").addClass("igk-title-4").setHtml(title);
            q.add("div").addClass("igk-panel igk-notify-panel").setHtml(msg);
            var msg = q.o.outerHTML.split("\n").join("<br />");
            igk.winui.notify.showMsg(msg, "igk-danger");
        },
        close: function() {
            // close the top notification dialog
            if (notify_frames.length > 0) {
                var f = notify_frames.pop();
                f.close();
            }
        },
        visible: function() {
            return m_nv;
        },
        init: function() { // initialize a notification controler
            // init this current node as a message box 
            var q = $igk(igk.getParentScript());
            if (q && (q.o.parentNode != null)) {
                igk.ready(function() {
                    var t = q.select('.title').first().getHtml();
                    var m = q.select('.msg').first();
                    var i = (q.o.parentNode != null);
                    // remove data
                    q.select("^.igk-notify-box").remove();
                    if (i)
                        igk.winui.notify.showMsBox(t, m, q.o.className);
                });
            }
        },
        /**
         * notify dialog
         * @param {*} t target
         * @param string defStyle extra def classes
         */
        showDialog(t, defStyle, type) {
            let m = $igk(t).first();
            if (!m) {
                return;
            }
            defStyle = defStyle || "";
            type = type || "";
            let q = igk.createNode("div")
                .addClass("igk-notify " + defStyle)
                .setHtml(m.o.outerHTML);
            $igk(q.o.firstChild).toggleClass("dispn");
            igk.winui.notify.showMsg(q.o.outerHTML, type);
        }
    });
})();