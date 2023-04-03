// author: C.A.D. BONDJE DOUE
// file: winui.js
// @date: 20230102 14:44:55
// @desc: 

'use strict';
(function() {
    var RZ_TIMEOUT = 200;
    const createNS = igk.system.createNS;
    const IGK_UNDEF = 'undefined';
    const { regEventContextByOwner } = igk;
    createNS("igk.winui", { // represent window screen utility namespace
        toString: function() { return "igk.winui"; },
        open: function(uri) {
            var frm = window.open(uri);
            frm.onload = function() {
                frm.close();
            };
        },
        screenSize: function() {
            return {
                height: window.innerHeight,
                width: window.innerWidth,
                toString: function() {
                    return "height:" + this.height + " ; width: " + this.width;
                }
            };
        },
        focus: function(id) { var q = document.getElementById(id); if (q) q.focus(); },
        fitfix2: function(node, parent, onw, onh) {
            var t = $igk(node);
            var l = $igk(parent);
            var h = onh == null ? !0 : onh;
            var w = onw == null ? !0 : onw;
            if (t) {
                t.setCss({
                    "position": "fixed"
                });
                if (w) {
                    t.setCss({ "right": (l.fn.hasVScrollBar() ? l.fn.vscrollWidth() : 0) + "px" });
                }
                if (h) {
                    t.setCss({ "bottom": ((l.fn.hasHScrollBar() ? l.fn.hscrollHeight() + 1 : 0)) + "px" });
                }
            }
        },
        // define static method
        GetScreenPosition: function(item) { // get position according to screen . without scrolling calculation
            var left = 0;
            var top = 0;
            if (item) {
                left += item.offsetLeft;
                top += item.offsetTop;
                while (!igk.isUndef(typeof(item.offsetParent)) && (item.offsetParent != null)) {
                    left += (item.clientLeft) ? item.clientLeft : 0;
                    top += (item.clientTop) ? item.clientTop : 0;
                    item = item.offsetParent;
                }
            }
            return new igk.math.vector2d(left, top);
        },
        GetScreenSize: function() {
            var x = window.innerWidth || 0;
            var y = window.innerHeight || 0;
            return new igk.math.vector2d(x, y);
        },
        getWidth: function() {
            return window.innerWidth || 0;
        },
        getHeight: function() {
            return window.innerHeight || 0;
        },
        // get the document location
        GetDocumentLocation: function() {
            var left = 0;
            var top = 0;
            left = window.pageXOffset ? -window.pageXOffset : 0;
            top = window.pageYOffset ? -window.pageYOffset : 0;
            return new igk.math.vector2d(left, top);
        },
        // get the real screen location of the item with scroll calculation
        GetRealScreenPosition: function(item) { // 
            // >item: DomNode
            var left = 0;
            var top = 0;
            left = window.pageXOffset ? -window.pageXOffset : 0;
            top = window.pageYOffset ? -window.pageYOffset : 0;
            if (item) {
                // left+=item.offsetLeft?item.offsetLeft:0;
                // top +=item.offsetTop?item.offsetTop:0;
                left += -igk.winui.GetScrollLeft(item) + ((item.offsetLeft) ? item.offsetLeft : 0);
                top += -igk.winui.GetScrollTop(item) + ((item.offsetTop) ? item.offsetTop : 0);
                while ((item.offsetParent != null)) {
                    item = item.offsetParent;
                    left += -igk.winui.GetScrollLeft(item) + ((item.offsetLeft) ? item.offsetLeft : 0);
                    top += -igk.winui.GetScrollTop(item) + ((item.offsetTop) ? item.offsetTop : 0);
                }
            }
            return new igk.math.vector2d(left, top);
        },
        GetScrollPosition: function(item, parent) {
            if (!parent)
                return new igk.math.vector2d(0, 0);
            var left = 0;
            var top = 0;
            left = window.pageXOffset ? -window.pageXOffset : 0;
            top = window.pageYOffset ? -window.pageYOffset : 0;
            if (item) {
                // left+=item.offsetLeft?item.offsetLeft:0;
                // top +=item.offsetTop?item.offsetTop:0;
                left += -igk.winui.GetScrollLeft(item) + ((item.offsetLeft) ? item.offsetLeft : 0);
                top += -igk.winui.GetScrollTop(item) + ((item.offsetTop) ? item.offsetTop : 0);
                while ((item.offsetParent != null) && (item.offsetParent != parent)) {
                    item = item.offsetParent;
                    left += -igk.winui.GetScrollLeft(item) + ((item.offsetLeft) ? item.offsetLeft : 0);
                    top += -igk.winui.GetScrollTop(item) + ((item.offsetTop) ? item.offsetTop : 0);
                }
                if (item.offsetParent != parent) {
                    return new igk.math.vector2d(-1, -1);
                }
            }
            return new igk.math.vector2d(left, top);
        },
        GetRealOffsetParent: function(item) {
            if (item) {
                var q = item.offsetParent;
                while (q != null) {
                    if (q.offsetParent == null)
                        break;
                    q = q.offsetParent;
                }
                return q;
            }
            return null;
        },
        GetRealScrollParent: function(item) { // get the current scroll parent
            if (item) {
                // note: offsetParent is only available for item with  display not equal to 'none'
                var q = item.offsetParent; //  || item.parentNode;
                var cq = 0;
                while ((q != null) && (q.tagName.toLowerCase() != 'body')) {
                    cq = $igk(q);
                    if (q.offsetParent == null) {
                        console.debug("no offset parent? " + (cq.fn.hasVScrollBar() || cq.fn.hasHScrollBar()));
                        break;
                    }
                    if (cq.fn.hasVScrollBar() || cq.fn.hasHScrollBar()) {
                        break;
                    }
                    q = q.offsetParent; // || q.parentNode;
                }
                return q;
            }
            return null;
        },
        GetMousePoint: function(evt) {
            return new igk.math.vector2d(evt.clientX, evt.clientY);
        },
        // >@@ get the child mouse location
        GetChildMouseLocation: function(item, evt) // return mouse location in child
            {
                if (evt == null) {
                    return new igk.math.vector2d(0, 0);
                }
                var loc = igk.winui.GetRealScreenPosition($igk(item).o);
                loc.x = evt.clientX - (isNaN(loc.x) ? 0 : loc.x);
                loc.y = evt.clientY - (isNaN(loc.y) ? 0 : loc.y);
                return loc;
            },
        GetChildTouchLocation: function(item, evt, index) {
            var touchv = {
                "touchstart": 1,
                "touchmove": 1,
                "toucancel": 1,
                "touchend": 1
            };
            if (!evt || !touchv[evt.type])
                return igk.math.vector2d(0, 0).clone();
            var i = index || 0;
            var s = ((evt.touches.length > 0) && (i < evt.touches.length)) ? evt.touches.item(i) : null;
            if (s == null)
                return igk.math.vector2d(0, 0).clone();
            var loc = igk.winui.GetRealScreenPosition($igk(item).o);
            loc.x = s.pageX - (isNaN(loc.x) ? 0 : loc.x);
            loc.y = s.pageY - (isNaN(loc.y) ? 0 : loc.y);
            return loc;
        },
        // >@@get if the child has a mouse input
        HasMouseInputInChild: function(item, evt) {
            var loc = igk.winui.GetChildMouseLocation(item, evt);
            return igk.winui.controlUtils.HasChildContainPoint(item, loc);
        },
        GetScrollLeft: function(item) {
            if (item == null) return 0;
            if (item.pageXOffset) {
                return item.pageXOffset;
            } else if (item.scrollLeft)
                return item.scrollLeft;
            return 0;
        },
        GetScrollTop: function(item) {
            if (item == null) return 0;
            if (item.pageYOffset) {
                return item.pageYOffset; // pageXOffset
            } else if (item.scrollTop)
                return item.scrollTop;
            return 0;
        },
        registerEventHandler: function(name, objListener) {
            // > register event handler list
            var th = name.split(" ");
            var n, d;;
            for (var i = 0; i < th.length; i++) {
                d = igk.winui.getEventHandler(th[i]);
                if (d) {
                    continue;
                }
                if (objListener) {
                    igk.winui.events.register.registerEvent(th[i], objListener);
                }
            }
        },
        reg_window_event: function(method, func, useCapture) {
            return igk.winui.reg_system_event(window, method, func, useCapture);
        },
        unreg_window_event: function(method, func) {
            return igk.winui.unreg_system_event(window, method, func);
        },

        reg_event: function(item, method, func, useCapture) { // global	
            var g = method.split(' ');
            var s = 0;
            var o = 1;
            while (o && (s = g.pop())) {
                var eventHandler = igk.winui.getEventHandler(s);
                if (eventHandler != null) {
                    o = o && eventHandler.reg_event(item, func, useCapture);
                } else o = o && igk.winui.reg_system_event(item, s, func, useCapture);
            }
        },
        unreg_system_event_object: function(item) { // unregister all event register for this item
            if (!item)
                return;
            igk.winui.getEventObjectManager().unregister(item);
        },
        unreg_system_event: function(item, method, func) {
            if ((item == null) ||
                (((method in item) && (item["on" + method] + "" == igk.constants.undef)) &&
                    (!item.removeEventListener) &&
                    (item.detachEvent)
                ))
                return !1;
            igk.winui.getEventObjectManager().unregister(item, method, func);
            if (item.removeEventListener) {
                item.removeEventListener(method, func, false);
            } else if (item.detachEvent) {
                item.detachEvent("on" + method, func);
            } else {
                var m = item["on" + method];
                if (typeof(m) == "function") {
                    item["on" + method] = null;
                } else {
                    item["on" + method] = null;
                }
            }
            return !0;
        },
        unreg_event: function(item, method, func) {
            var eventHandler = igk.winui.getEventHandler(method);
            if (eventHandler != null) {
                return eventHandler.unreg_event(item, func);
            }
            return igk.winui.unreg_system_event(item, method, func);
        },
        regwindow_event: function(method, func) {
            if (!igk.winui.reg_event(window, method, func))
                return igk.winui.reg_event(document, method, func);
            return !0;
        },
        unregwindow_event: function(method, func) {
            if (!igk.winui.unreg_event(window, method, func))
                return igk.winui.unreg_event(document, method, func);
            return !0;
        },
        RegEventContext: function(eventContextOwner, properties) { // o that will host and igk properties to binds
            // >@@ eventContextOwner: the context o
            // >@@ property used to register 
            if ((properties == null) || (eventContextOwner == null) || (regEventContextByOwner(eventContextOwner) != null)) {
                return null;
            }

            function __eventContextObject(eventContextOwner, properties) {
                var q = this;
                var m_eventContextOwner = eventContextOwner;
                var m_properties = properties;
                var m_col = new Array();
                // save unregEventContext function to target o
                function __regEventContextFunction() {
                    var v_ts = properties;
                    var v_ch = igk.regEventContextByOwner(v_ts, true, function() { return new chainUnreg(eventContextOwner, v_ts); });
                    if (!v_ch){
                        return;
                    }
                    if (!v_ts.unregEventContext) {
                        igk.appendProperties(v_ts, {
                            unregEventContext: function() {
                                // unreg single context								
                                q.clear();
                                v_ch.chain--;
                                if (v_ch.chain == 0) {
                                    if (!igk_unRegEventContext(v_ch)) {
                                        throw ("item not unregister ");
                                    }
                                }
                            }
                        });
                    } else {
                        var meth = v_ts.unregEventContext;
                        v_ts.unregEventContext = function() {
                            meth();
                            q.clear();
                            v_ch.chain--;
                            if (v_ch.chain == 0) {
                                if (!igk_unRegEventContext(v_ch)) {
                                    throw ("unreg -- items");
                                }
                            }
                        };
                        v_ch.chain++;
                    }
                };
                __regEventContextFunction();

                function chainUnreg(o, properties) {
                    this.o = o;
                    this.properties = properties;
                    this.chain = 1;
                    this.unregEventContext = function() { this.properties.unregEventContext(); };
                    this.toString = function() { return "chainUnreg[" + o + ":" + this.chain + "]"; };
                };

                function eventCibling(target, name, func) {
                    this.name = name;
                    this.target = target;
                    var q = this;
                    this.func = function() { func.apply(q.target, arguments); };
                    this.toString = function() { return "eventcibling:" + name; };
                };

                function __unregister(s) {
                    if (igk_is_array(s)) {
                        for (var i = 0; i < s.length; i++) {
                            __unregister(s[i]);
                        }
                    } else if (s instanceof eventCibling) {
                        // unregister event cibling
                        igk.winui.unreg_event(s.target, s.name, s.func);
                    }
                };
                // resizing function
                var m_resizefuncs = [];
                var m_resizetimeout = 0;
                var m_resizeevent = false;

                function __is_resized_event() {
                    return m_resizeevent;
                };

                function __resizing_push(func) {
                    if (func) {
                        m_resizefuncs.push(func);
                    }
                };

                function __resize_call_invoke(evt) {
                    for (var i = 0; i < m_resizefuncs.length; i++) {
                        m_resizefuncs[i].apply(window, arguments);
                    }
                }

                function __resize_call(evt) {
                    if (m_resizetimeout) {
                        igk.clearTimeout(m_resizetimeout);
                        m_resizetimeout = null;
                    }
                    // start a new time out
                    m_resizetimeout = setTimeout(function() { __resize_call_invoke(evt); }, RZ_TIMEOUT);
                };
                window.igk.appendProperties(this, {
                    clear: function() { // unegister all element
                        if (m_col) {
                            for (var i in m_col) {
                                var s = m_col[i];
                                __unregister(s);
                            }
                        }
                    },
                    reg_event: function(target, name, func) { // register cibling function
                        if ((target == null) || !name || (func == null)) {
                            return !1;
                        }
                        var v_cibling = new eventCibling(target, name, func);
                        igk.winui.reg_event(v_cibling.target, v_cibling.name, v_cibling.func);
                        var key = name + ":" + target;
                        if (typeof(m_col[key]) == igk.constants.undef)
                            m_col[key] = v_cibling;
                        else {
                            var tab = m_col[key];
                            var v_isarray = igk_is_array(tab);
                            if (v_isarray) {
                                tab.push(v_cibling);
                                m_col = tab;
                            } else {
                                var v_t = new Array();
                                v_t.push(tab);
                                v_t.push(v_cibling);
                                // replace
                                m_col[key] = v_t;
                            }
                        }
                        return !0;
                    },
                    reg_window: function(name, func) { // register window function
                        if (name == "resize") { // handle resize
                            __resizing_push(func);
                            if (!__is_resized_event()) {
                                this.reg_event(window, name, __resize_call);
                                m_resizeevent = !0;
                            }
                            return;
                        }
                        this.reg_event(window, name, func);
                    },
                    unreg_window: function(name, func){
                        igk.winui.unreg_window_event(name, func);
                    },
                    toString: function() {
                        return "RegEventContext";
                    }
                });
            }
            return new __eventContextObject(eventContextOwner, properties);
        },
        saveHistory: function(uri) {
            if (typeof history.pushState == "function") {
                history.pushState({}, document.title, uri + "&history=1");
            }
        },
        reg_keypress: function(func) {
            igk.winui.reg_event(document, "keypress", func);
        },
        unreg_keypress: function(func) {
            igk.winui.unreg_event(document, "keypress", func);
        },
        centerDialog: function(dialog, minw, minh) { // center the dialog
            if (dialog == null)
                return;
            dialog.style.top = "50%";
            dialog.style.left = "50%";
            // setup the size
            dialog.style.minWidth = minw ? minw + "px" : null;
            dialog.style.minHeight = minh ? minh + "px" : null;
            $igk(dialog).setCss({
                "position": "absolute",
                "marginLeft": -(dialog.clientWidth / 2) + "px",
                "marginTop": -(dialog.clientHeight / 2) + "px"
            });
            // lock the size 
            if (!dialog.style.minWidth)
                dialog.style.minWidth = dialog.clientWidth + "px";
            if (!dialog.style.minHeight)
                dialog.style.minHeight = dialog.clientHeight + "px";
            // update size for draggin
            var pt = igk.winui.GetRealScreenPosition(dialog);
            var adjusted = false;
            if (pt.x < 0) {
                pt.x = 0;
                adjusted = !0;
            }
            if (pt.y < 0) {
                pt.y = 0;
                adjusted = !0;
            }
            if (adjusted) {
                dialog.style.margin = "0"; // remove marging			
                dialog.style.left = pt.x + "px";
                dialog.style.top = pt.y + "px";
            }
        },
        dragFrameManager: { // used to drag frame box
            target: null,
            box: null, // box to move
            startpos: null,
            dragstart: false,
            oldposition: null,
            locToScreen: true,
            dragSize: new igk.math.vector2d(4, 4),
            toString: function() {
                return "igk.winui.dragmanager";
            },
            init: function(target, box) {
                if (target == null)
                    return null;

                function __construct(target, box) {
                    var self = this;
                    var m_c = 0;
                    this.target = target;
                    this.box = (box == null) ? target : box;
                    this.changelocation = igk.winui.dragFrameManager.changelocation;
                    var m_eventContext = igk.winui.RegEventContext({
                            box: this.box,
                            toString: function() { return "dragFrameManager.BoxOwner" },
                            properties: $igk(box)
                        },
                        $igk(this.box));
                    if (m_eventContext) {
                        m_eventContext.reg_event(document, "mousemove", function() { self.changelocation.apply(self, arguments); });
                        m_eventContext.reg_event(document, "mouseup", function() { if (self.dragstart) { self.dragstart = false; }; });
                        m_eventContext.reg_event(this.target,
                            "mousedown",
                            function(evt) {
                                if (!self.dragstart) {
                                    self.startpos = new igk.math.vector2d(evt.clientX, evt.clientY);
                                    self.oldposition = igk.winui.GetScreenPosition(self.box);
                                    self.dragstart = !0;
                                }
                            });
                        m_eventContext.reg_event(this.target, "mouseup", function(evt) { self.dragstart = false; });
                    }
                    return self;
                };
                return new __construct(target, box);
            },
            changelocation: function(evt) {
                if (this.dragstart) {
                    var left = 0;
                    var right = 0;
                    var pt = igk.math.vector2d(
                        this.oldposition.x + (evt.clientX - this.startpos.x),
                        this.oldposition.y + (evt.clientY - this.startpos.y)
                    );
                    var adjustedx = false;
                    var adjustedy = false;
                    if (this.locToScreen) {
                        if (pt.x < 0) {
                            pt.x = 0;
                            adjustedx = true;
                        }
                        if (pt.y < 0) {
                            pt.y = 0;
                            adjustedy = true;
                        }
                        if (this.box.offsetParent) {
                            if (!adjustedx)
                                if ((pt.x + this.box.clientWidth) >= this.box.offsetParent.clientWidth) {
                                    pt.x = this.box.offsetParent.clientWidth - this.box.clientWidth;
                                    adjustedx = !0;
                                }
                            if (!adjustedy)
                                if ((pt.y + this.box.clientHeight) >= this.box.offsetParent.clientHeight) {
                                    pt.y = this.box.offsetParent.clientHeight - this.box.clientHeight;
                                    adjustedy = !0;
                                }
                        }
                    }
                    this.box.style.margin = "0";
                    this.box.style.left = pt.x + "px";
                    this.box.style.top = pt.y + "px";
                    // this.box.style.right='auto';
                };
            }
        }
    });
    (function() {
        var _uiListener = 0;
        createNS("igk.winui", {
            createInput: function(id, type, opts) {
                if (_uiListener) {
                    return _uiListener.apply(this, arguments);
                }
                var i = null;
                switch (type) {
                    case "select":
                        i = igk.createNode("select");
                        i.setAttribute("id", id);
                        i.o['name'] = id;
                        if (opts && opts.data) {
                            var sl = opts.select || '';
                            for (var j = 0; j < opts.data.length; j++) {
                                var o = i.add("option");
                                o.setAttribute("value", opts.data[j]);
                                o.setHtml(opts.data[j]);
                                if (opts.data[i] == sl) {
                                    o.setAttribute("selected", 'true');
                                }
                            }
                        }
                        if (opts && opts.change) {
                            i.on("change", opts.change);
                        }
                        break;
                    default:
                        i = igk.createNode("input");
                        i.setAttribute("id", id);
                        i.o['name'] = id;
                        var _pl = (opts ? opts.placeholder : '') || '';
                        if (type) {
                            i.o['type'] = type;
                        }
                        i.setAttribute("placeholder", _pl);
                        if ((type == 'file') && opts && opts.accept) {
                            i.setAttribute("accept", opts.accept);
                        }
                        break;
                }
                return i;
            },
            setListener: function(b) {
                _uiListener = b;
            },
            getListener: function() {
                return _uiListener;
            }
        });
    })();

})();