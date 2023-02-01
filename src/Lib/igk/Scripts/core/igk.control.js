// -----------------------------------------------------------------------------------------
// >namespace: igk.winui 
// -----------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------
// -----------------------------------EXTENSION--------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------

(function() {
    const BEFORESUBMIT_EVENT = 'igkFormBeforeSubmit';

    (function() {


        if (document.head) {
            var e = $igk(document.head).select("link");
            e.each(function() {
                if (this.getAttribute("rel") == "igkcss") {
                    // igk.show_prop(this.o);
                    igk.ajx.get(this.getAttribute("href"), null, function(xhr) {
                        if (this.isReady()) {
                            var s = igk.createNode("style");
                            s.setAttribute('igk-id', 'igkcss');
                            s.innerHTML = xhr.responseText;
                            document.head.appendChild(s);
                        }
                    }, false); // important to lock loading before the document continue loading
                }
                return !0;
            });
        }
    })();

    // $igk(igk.getParentScript()).select("link").each(function() {
    //     var s = (this.o.getAttribute("href") + "");
    //     if (s && (s.indexOf("R/Styles/base.php") != -1)) {
    //         igk.system.apps.link = this;
    //         igk.appendProperties(this, {
    //             basehref: this.o.href,
    //             counter: 0,
    //             reload: function() {
    //                 var bck = this.basehref;
    //                 this.o.href = bck + "?reload=" + this.counter;
    //                 this.counter++;
    //             }
    //         });
    //     }
    //     return !0;
    // });


    // 
    // controller utility presentation igk-new-lang-key
    // 
    (function() {
        var context_options = {};
        var m_items = {};

        function __reg_key(n, t) {
            if (m_items[n]) {
                var b = m_items[n];
                if (b.length) {
                    b.push(t);
                } else {
                    b = [];
                    b.push(m_items[n]);
                    b.push(t);
                    // replace
                    m_items[n] = b;
                }
            } else {
                m_items[n] = t;
            }
        }

        function __add_prop() {
            var q = this;
            var k = this.getHtml();
            __reg_key(k, q);
            // q.reg_context_options("modkeyv",k);
            // q.reg_context_options("delkeyv",k);
            // q.reg_event("click",function(evt){		
            // evt.preventDefault();
            // });
            // replace context menu
            q.reg_event("contextmenu", function(evt) {
                evt.preventDefault();
                var langctrl = igk.ctrl.getRegCtrl("c_l");
                if (langctrl) {
                    var p = q.getParentCtrl();
                    var k = q.getHtml();
                    var pid = p ? p.o.id : '';
                    igk.ajx.post(langctrl.getUri("mod_key&ajx=1"), "key=" + k + "&ctrl=" + pid,
                        igk.ajx.fn.replace_or_append_to_body,
                        true);
                } else {
                    if (!igk.winui.notify.visible()) {
                        var h = q.select(".error").first();
                        if (h) {
                            h = h.getHtml();
                            igk.winui.notify.showError(h); //"<div class='error-3'>/!\\ No language controller found !!! </div>");
                        }
                    }
                }
            });
        }

        function __register_lang_key(s) {
            if (s == null)
                return;
            s.setAttribute("igk-new-lang-key", 1)
                .appendProperties({
                    "igk-new-lang-key-setup": 1,
                    reg_context_options: function(k, uri) {
                        context_options[k] = uri;
                    },
                    show_context_options: function() {
                        var ul = igk.winui.contextMenu();
                        ul.setHtml(null);
                        var loc = this.getScreenLocation();
                        var size = { w: this.getWidth(), h: this.getHeight() + this.o.scrollHeight };
                        ul.addClass("posab").setCss({
                            zIndex: 10,
                            border: "1px solid black",
                            padding: "8px",
                            position: "absolute",
                            left: loc.x + "px",
                            minWidth: "200px",
                            top: (loc.y + size.h) + "px"
                        });
                        for (var i in context_options) {
                            ul.add("li").add("a")
                                .setHtml(i)
                                .setAttribute("href", "?mod=1&k=" + context_options[i]);
                        }
                        igk.dom.body().appendChild(ul);
                    }
                }, true);
            if (s.isSr()) {
                s.each_all(__add_prop);
            } else {
                __add_prop.apply(s);
            }
        }

        function __initglobalkey() {
            var s = igk.qselect(".igk-new-lang-key").each_all(function() {
                console.debug("register lang key");
                __register_lang_key(this);
            });
            igk.unready(__initglobalkey);
        }
        igk.ready(__initglobalkey);
        igk.ctrl.registerAttribManager("igk-new-lang-key", { desc: "register new keys language editor" });
        igk.ctrl.bindAttribManager("igk-new-lang-key", function(m, n) {
            var t = this["igk-new-lang-key-setup"];
            this.setCss({
                color: "red",
                cursor: "help"
            });
            if (t == null) {
                // init node key
                __register_lang_key($igk(this));
            }
        });
        // update language view 
        igk.system.createNS("igk.winui.langkey.fn", {
            update: function(n, v, d) {
                if (n != v) {
                    $igk('.igk-new-lang-key').each_all(function() {
                        if (this.getHtml() == n) {
                            // this.setHtml(v);
                            var idx = this.getAttribute("igk:data") || 0;
                            d && (v = d[idx]);
                            var t = igk.createText(v);
                            this.o.parentNode.replaceChild(t.o, this.o);
                        }
                    });
                }
                var s = igk.getCurrentScript();
                if (s) {
                    $igk(s).remove();
                }
            }
        });
    })();
    igk.system.createNS("igk.winui.navlink", {
        init: function(target, link) {
            if (typeof(this.classinit) == igk.constants.undef) {
                this.navitems = [];
                this.classinit = !0;
            }
            var navitems = this.navitems;
            target.reg_event("click", function(evt) {
                evt.preventDefault();
                var b = $igk("#" + link).first();
                if (b && !b.isSr()) {
                    // igk.dom.body()
                    var q = $igk(b).getscrollParent();
                    // igk.show_prop(b.o);					
                    if (q) {
                        q.scrollTo(b.o, {
                            duration: 500,
                            interval: 20,
                            effect: "linear",
                            effectmode: "easeinout"
                        }, null);
                    }
                } else {
                    console.debug('[IGK] - Element not found ' + link);
                }
            });
            navitems.push(target);
        }
    });
    igk.ctrl.registerAttribManager("igk-nav-link", { desc: "navigation link" });
    igk.ctrl.bindAttribManager("igk-nav-link", function(n, m) {
        if (!m)
            return;
        this.setAttribute("href", "!#" + m);
        var link = null;
        igk.winui.navlink.init(this, m);
    });

    function animationProperty(q, s) {
        // q:target
        // s:source
        if (s == null)
            throw "source is null";
        if (q == null)
            throw "item is null";
        var duration = '0.2s';
        var effect = "ease-in-out";
        var rmduration = '0.2s';
        var rmeffect = 'ease-in-out';
        var tq = igk.JSON.parse(s);
        var store = {};
        for (var m in tq.css) {
            store[m] = q.getComputedStyle(m);
            // for safari fix
            if (typeof(store[m]) == igk.constants.undef)
                store[m] = null;
        }
        if (tq.duration)
            duration = tq.duration;
        if (tq.effect)
            duration = tq.effect;
        igk.appendProperties(this, {
            bind: function() {
                if (q.isCssSupportAnimation()) {
                    if (tq.select) {
                        q.select(tq.select)
                            .setCss({ transition: 'all ' + duration + ' ' + effect })
                            .setCss(tq.css);
                    } else {
                        q.setCss({ transition: 'all ' + duration + ' ' + effect })
                            .setCss(tq.css);
                    }
                } else {
                    q.animate(tq.css, { duration: igk.datetime.timeToMs(duration) });
                }
            },
            unbind: function() {
                if (q.isCssSupportAnimation()) {
                    if (tq.select) {
                        q.select(tq.select)
                            .setCss({ transition: 'all ' + rmduration + ' ' + effect })
                            .setCss(store);
                    } else {
                        q.setCss({ transition: 'all ' + rmduration + ' ' + effect })
                            .setCss(store)
                            .timeOut(0, () => {});
                    }
                } else {
                    q.animate(store, { duration: igk.datetime.timeToMs(rmduration) });
                }
            }
        });
    }
    // translation animations igk-js-anim-msover
    igk.ctrl.registerAttribManager("igk-js-anim-msover", { desc: "globa animation style on mouse over" });
    igk.ctrl.bindAttribManager("igk-js-anim-msover", function() {
        var q = this;
        var source = this.getAttribute("igk-js-anim-msover");
        var store = {};
        var duration = '0.2s';
        var effect = "ease-in-out";
        var rmduration = '0.2s';
        var rmeffect = 'ease-in-out';
        var tq = null;
        if (!source) {
            return;
        }
        tq = igk.JSON.parse(source);
        for (var m in tq.css) {
            store[m] = q.getComputedStyle(m);
            // for safari fix
            if (typeof(store[m]) == igk.constants.undef)
                store[m] = null;
        }
        if (tq.duration)
            duration = tq.duration;
        if (tq.effect)
            duration = tq.effect;
        this.reg_event("mouseover", function(evt) {
            if (q.isCssSupportAnimation()) {
                if (tq.select) {
                    q.select(tq.select)
                        .setCss({ transition: 'all ' + duration + ' ' + effect })
                        .setCss(tq.css);
                } else {
                    q.setCss({ transition: 'all ' + duration + ' ' + effect })
                        .setCss(tq.css);
                }
            } else {
                q.animate(tq.css, { duration: igk.datetime.timeToMs(duration) });
            }
        });
        // mouseenter not supported for safari window
        // this.reg_event("mouseenter",function(){ 
        // });
        // mouseout vs mouseleave . safari error
        this.reg_event("mouseout", function() {
            // igk.show_prop(store);
            if (q.isCssSupportAnimation()) {
                if (tq.select) {
                    q.select(tq.select)
                        .setCss({ transition: 'all ' + rmduration + ' ' + effect })
                        .setCss(store);
                } else {
                    q.setCss({ transition: 'all ' + rmduration + ' ' + effect })
                        .setCss(store)
                        .timeOut(0, () => {});
                }
            } else {
                q.animate(store, { duration: igk.datetime.timeToMs(rmduration) });
            }
        });
    });
    igk.ctrl.bindAttribManager("igk-js-anim-focus", function() {
        var q = this;
        var source = this.getAttribute("igk-js-anim-focus");
        var store = {};
        if (!source) {
            return;
        }
        if (q.isCssSupportAnimation()) {
            var anim = new animationProperty(q, source);
            q.reg_event("focus", function() { anim.bind(); });
            q.reg_event("blur", function() { anim.unbind(); });
        } else {
            var t = eval("new Array(" + source + ")");
            for (var m in t[0]) {
                store[m] = q.getComputedStyle(m);
            }
            this.reg_event("focus", function() { eval("q.animate(" + source + ");"); });
            this.reg_event("blur", function() { q.animate(store, t[1]); });
        }
    });
    igk.ctrl.bindAttribManager("igk-js-bind-select-to", function(n, v) {
        // v : target id or json properties
        // 	: target must start with #
        // 	: if json properties allowed value is {id:target,}
        var s = null;
        var q = this;
        var qv = q.getAttribute('value');
        if (igk.system.string.startWith(v, "#")) {
            s = $igk(v);
            if (s) {
                s.select("option").each(function() {
                    // copy
                    q.appendChild(this.clone());
                    // continue execution
                    return !0;
                });
            }
        } else {
            var s = igk.JSON.parse(v);
            if (s && s.id) {
                if (s.allowempty) {
                    var opt = igk.createNode("option");
                    opt.setAttribute("value", typeof(s.emptyvalue) != igk.constants.undef ? s.emptyvalue : null);
                    q.appendChild(opt);
                }
                var select = s.selected;
                var tag = s.tag ? s.tag : 'option';
                var present = false;
                var v_sl = $igk(s.id).select(tag)
                    .each(function() {
                        // copy				
                        var r = null;
                        if (tag != 'option') {
                            r = igk.createNode("option");
                            r.copyAttributes(this);
                            r.setHtml(this.o.innerHTML);
                        } else
                            r = this.clone();
                        var vv = r.getAttribute('value');
                        if (vv == select) {
                            r.setAttribute('selected', 'true');
                            present = true;
                        }
                        // alert("tag is "+tag + " VS  "+r.o.tagName);
                        // if (tag != 'option'){
                        // r.o.tagName = "option";
                        // }
                        q.appendChild(r);
                        // continue execution
                        return !0;
                    });
                if (!present && s.usecustom) {
                    if ((select + "").length > 0) {
                        var r = igk.createNode("option");
                        r.setAttribute('value', select);
                        r.setAttribute('selected', 'selected');
                        r.setHtml(select);
                        q.appendChild(r);
                    }
                }
            }
        }
    });
    //
    //+ attrib : igk-js-autofix-item
    //
    (function() {
        igk.ctrl.registerAttribManager("igk-js-autofix-item", { 'ns': 'js', 'desc': 'start auto fix item to host. item must be added before being initialized' });
        igk.ctrl.bindAttribManager("igk-js-autofix-item", function(m, n) {
            // console.debug("auto fix item ");
            var o = igk.JSON.parse(n);
            var c = null;
            if (!o.target) c = this.select("^.igk-parentscroll").first();
            else c = this.select(o.target).first();
            c = c || $igk(document);
            c.reg_event("scroll", __autofix_check_scroll(this, c, o));
        });

        function __autofix_check_scroll(q, p, o) {
            var man = new _autofix_manager();
            man.q = q;
            man.p = p;
            man.o = o;

            function _hostbind(e) {
                man.check(e);
            };
            return _hostbind;
        };
        //create a class
        function _autofix_manager() {};
        //add method to class using prototype
        _autofix_manager.prototype.check = function() {
            var c = this.p;
            var offset = this.o.offset || 0;
            if (c.o.scrollTop > offset) {
                // console.debug("bind ... make it fixed");
                c.addClass("igk-js-autofix-host");
                this.q.addClass("fix");
            } else {
                // console.debug("unbind");
                c.rmClass("igk-js-autofix-host");
                this.q.rmClass("fix");
            }
        };
    })();
    // igk.ctrl.togglebutton management
    (function() {
        igk.system.createNS("igk.ctrl.togglebutton", {
            init: function(target) {
                if (!target)
                    return;
                var q = $igk(target);
                var s = q.getAttribute("igk-toggle-target");
                if (!s)
                    return null;
                if (this.namespace) {
                    return new this.init(target);
                }
                var e = $igk(s).first();
                var t = q.getAttribute("igk-toggle-class");
                var toggle = "visibiliy";
                // if(t){
                toggle = "class";
                // }
                this.updateState = function() {
                    var v_state = q.getAttribute("igk-toggle-state");
                    switch (v_state) {
                        case "expand":
                            {
                                if (e) e.setCss({ width: 200 / 3.0 + "%" });
                                q.setAttribute("igk-toggle-state", "collapse");
                            }
                            break;
                        case "collapse":
                            if (e) e.setCss({ width: 0 + "%" });
                            q.setAttribute("igk-toggle-state", "expand");
                            break;
                    }
                };
                var self = this;
                q.reg_event("click", function(evt) {
                    self.updateState();
                });
                this.updateState();
            }
        });
        if (!igk.ctrl.isAttribManagerRegistrated("igk-toggle-button")) {
            var k = igk.ctrl.registerAttribManager("igk-toggle-button", { n: "js", desc: "register toggle button" });
        }
        igk.ctrl.bindAttribManager("igk-toggle-button", function() {
            var v = this.getAttribute("igk-toggle-button");
            var q = this;
            var source = igk.system.convert.parseToBool(this.getAttribute("igk-toggle-button"));
            if (source) {
                igk.ctrl.togglebutton.init(q);
            }
        });
    })();
    // img.src="?vimg=warzone";
    // Q: javascript get current domain?
    // R: document.domain,window.location.hostname
    // Q: define constant in javascript
    // R: no const name for most navigator. use property expression
    // Q: async a post
    // R: ok true=async ,false=sync
    // Q: property in igk
    // R: by default properties is a set of [g/s]et[method_name]
    (function() {
        igk.system.createNS("igk.html", {
            addInput: function(t, p, n, v) {
                i = igk.createNode("input");
                i.o["type"] = n;
                i.o["class"] = "cl" + n;
                i.o["value"] = v;
                t.o.appendChild(i.o);
                return i;
            }
        });
    })();
    // --------------------------------------------
    // focus on the scrollable control
    // --------------------------------------------
    igk.ready(function() {
        var q = igk.qselect(".igk-parentscroll").first();
        if (q) {
            q.o.focus();
        }
    });
    // --------------------------------------------
    // slider button management
    // --------------------------------------------
    (function() {
        var contain_slider = false;
        var m_s_btn = [];
        var ct_sliderbtn = "winui/sliderbutton";
        var m_datas = {};

        function _slide_to() {
            var p = igk.JSON.parse($igk(this).getAttribute("igk-slider-data"));
            if (p) {
                p.scrollparent = $igk($igk(p.target).first().getscrollParent());
                p.cibling = $igk(this);
                ns_igk.winui.fn.navigateTo(p.target, p.property).apply(this, null);
                m_datas[p.scrollparent.getCssSelector()] = p;
                igk.web.storage.set("igk/slider", p.scrollparent.getCssSelector());
            }
        }

        function _slider_click(evt) {
            evt.preventDefault();
            _slide_to.apply(this);
        }

        function _view_size() {
            for (var i in m_datas) {
                var p = m_datas[i];
                ns_igk.winui.fn.navigateTo(p.target, p.property).apply(p.cibling, null);
            }
        }

        function _init_slider_button(q) {
            if (q.data.contains(ct_sliderbtn))
                return;
            if (q.istouchable()) {
                q.reg_event("touchend", _slider_click);
            } else
                q.reg_event("click", _slider_click);
            m_s_btn.push(q);
            q.data.add(ct_sliderbtn, 1);
            if (!contain_slider) {
                // register document size change
                igk.winui.reg_event(window, "resize", _view_size);
                // load slider from cookies
                var sl = igk.web.storage.get("igk/slider");
                contain_slider = !0;
            }
        }
        igk.ctrl.registerReady(function() {
            if (igk.system.regex.item_match_class("igk-slider-btn", this)) {
                _init_slider_button($igk(this));
            }
        });
        igk.ready(function() {
            igk.qselect(".igk-slider-btn").each_all(function() {
                _init_slider_button(this);
            });
        });
        igk.system.createNS("igk.winui.sliderbtn", {
            fn: {
                ajxslideToReady: function(q, p) {
                    // if(!q)return null;
                    return function(xhr) {
                        if (this.isReady()) {
                            $igk(q).setAttribute("igk-slider-data", p);
                            _init_slider_button($igk(q));
                            _slide_to.apply($igk(q));
                        }
                    }
                }
            }
        });
    })();
    // ---------------------------------------------------
    // form validation data
    // ---------------------------------------------------
    (function() {
        igk.ctrl.bindAttribManager("igk-form-validate", function(n, v) {
            if (!v) {
                return;
            }
            var q = this;
            // ------------------------------------------------------------
            // TODO: CHECK VALIDITY
            // ------------------------------------------------------------
            // alert(this.o.noValidate);
            // var q= this;
            // this.reg_event("invalid",function(evt){
            // });
            this.reg_event("submit", function(evt) {
                if ((typeof(q.o.checkValidity) != igk.constants.undef) && !q.o.checkValidity()) {
                    console.debug("data not valid");
                    evt.preventDefault();
                    return;
                }
                var _o = false;
                $igk(this).select("input").each(function() {
                    if (typeof(this.igkCheckIsInvalid) != igk.constants.undef) {
                        _o |= this.igkCheckIsInvalid();
                    }
                    return !0;
                });
                if (_o)
                    evt.preventDefault();
            });
        });
    })();
    // ---------------------------------------------------
    // android management system
    // ---------------------------------------------------
    (function() {
        // init android Namespace
        igk.system.createNS("igk.android", {});

        function __initAndroid() {
            if (!igk.navigator.isAndroid() && !igk.dom.body().supportClass("igk-android"))
                return;
            var m_actx = igk.createNode("android-ctx");
            var m_ectx = igk.winui.RegEventContext(m_actx, m_actx);
            if (m_ectx) {
                m_ectx.reg_window("resize", function() {
                    __setup_screen();
                });
                if (typeof(window.onorientationchange) != "undefined") {
                    m_ectx.reg_window("orientationchange", function(evt) {
                        if (window.orientation == 90) {
                            $igk('.igk-android').addClass("lnd-scape");
                        } else
                            $igk('.igk-android').rmClass("lnd-scape");
                    });
                }
            }
            var mt = igk.css.getMediaType();
            __setup_screen();
            igk.dom.body().addClass(mt);
            var mt = igk.css.getMediaType();
            igk.publisher.register(igk.publisher.events.mediachanged, function(e) {
                igk.dom.body().rmClass(mt).addClass(e.mediaType);
                mt = e.mediaType;
            });
            return m_actx;
        }

        function __setup_screen() {
            if (typeof(window.onorientationchange) == "undefined") {
                // 
                if (window.innerWidth > window.innerHeight) {
                    $igk('.igk-android').addClass("lnd-scape");
                } else {
                    $igk('.igk-android').rmClass("lnd-scape");
                }
            }
        }
        igk.ready(function() {
            __initAndroid();
        });
    })();
    // ----------------------------------------------------------
    // code view surface
    // ----------------------------------------------------------
    (function() {
        const _R = igk.resources.lang;
        var m_types = ['css', 'php', 'csharp', 'html', 'xml'];
        var m_codes = []; // store code object that currently been transformed
        var m_reg = (function(t) {
            var r = '';
            for (var i = 0; i < t.length; i++) {
                if (i > 0)
                    r += '|';
                r += t[i];
            }
            return new RegExp('((' + r + ')code)', 'i');
        })(m_types);

        function __init_code_area() {
            var q = this;
            if (!q) {
                return;
            }
            if (q.hightlight)
                return;
            q.hightlight = 1;
            m_codes.push(q);
            // return;
            // q.addClass("dispib");
            var c = '';
            var b = null;
            if ((b = m_reg.exec(q.o.className))) {
                c = b[2];
            } else {
                c = q.getAttribute("lang");
                if (!(c in m_types)) {
                    c = 'php';
                }
            }
            // q.addClass("code-php");
            var s = q.o.textContent.trim(); // .getHtml().trim();
            var t = s.split('\n');
            // return;
            // clear node
            q.setHtml("");
            q.getSource = function() {
                return s;
            };
            let copyable = q.getAttribute("igk-copyable");
            if (copyable) {
                let block = q.add("div");
                block.addClass("igk-copyable posab igk-svg-host svg-fill")
                    .setCss({
                        zIndex: 100,
                        position: 'absolute',
                        display: 'block',
                        width: '32px',
                        maxWidth: '32px',
                        overflow: 'hidden',
                        top: '0px',
                        right: '0px',
                        background: 'rgb(255 255 255 / 0.9)',
                        padding: '4px',
                        borderBottomLeftRadius: '5px',
                        cursor: 'pointer',
                    })
                    .on("click", function() {
                        igk.ClipBoard.writeText(s);

                        igk.winui.controls.toast.show(_R("item copied to clipboard"), "igk-success");
                        // let n = $igk(document.createElement('div'));
                        // n.addClass('igk-winui-toast');
                        // n.setHtml("item copied to clipboard");
                        // n.o.setAttribute("noHide", false);
                        // igk.dom.body().add(n);
                        // return n;
                    })
                    .add(igk.svg.useSvg('copy-outline'));
                q.on('scroll', function(e) {
                    let r = '-' + (q.o.scrollLeft) + 'px';
                    block.o.style.right = r;
                });
                q.init();
            }
            var m = null;
            if (c) {
                m = "igk.highlightjs." + c;
                m = eval("new " + m + "();");
            } else {
                m = new igk_e();
            }
            var l = "";
            for (var i = 0; i < t.length; i++) {
                l = t[i];
                var d = q.add("div");
                d.setHtml(m.evals(l));
            }
            var o = q.add('span').addClass("dispib").setHtml('_');
            var w = igk.getNumber(o.getComputedStyle('width'));
            o.remove();
            // delete(o); 
            w = (((m.getLines() + '').length * w) + 10) & 0xFFFA;
            igk.css.appendRule(q.getCssSelector() + " > div > span.ln {text-align:right; width:" + w + "px;}");
        };

        function igk_e() { // evaluator
            var l = 0;
            igk.appendProperties(this, {
                evals: function(m) {
                    l++;
                    return m;
                },
                getLines: function() { return l; }
            });
        };
        const inf = {
            ln: 0,
            mode: 0,
            pos: 0
        };

        function _readWord(s) {
            var w = "";
            var c = 0;
            var ch = "";
            while (inf.pos < inf.ln) {
                ch = inf.s[inf.pos];
                // TODO TRADITIONAL WAY
                // if (ch==("(")){
                // 	inf.pos--;
                // 	console.debug("breakk..............", inf.pos);
                // 	break;
                // }
                c = ch.toLowerCase().charCodeAt(0);
                if (((c >= 48) && (c <= 57)) || ((c >= 97) && (c <= 122)) || (ch == '_')) {
                    w += ch;
                } else {
                    break;
                }
                inf.pos++;
            }
            // delete(c);
            // if (w.length == 0){
            // }
            // 	inf.read = 0;
            return w;
        }

        function _readStringLitteral(ch) {
            var w = ch;
            var p = ch;
            while (inf.pos < inf.ln) {
                ch = inf.s[inf.pos];
                if (ch != p) {
                    w += ch;
                } else {
                    if (inf.s[inf.pos - 1] == "\\") {
                        // escaped
                        w += ch;
                    } else
                        break;
                }
                inf.pos++;
            }
            return w + p;
        };

        function _readPhpOperator(ch) {
            const op = ["||", "|", "&&", "&", ".", "->", "-", "/", "+", "*", "^", "~", "%", "()", "[]", "(", ")", "[", "]", "{", "}"];
            let c = "";
            while (inf.pos < inf.ln) {
                c = inf.s[inf.pos];
                if (op.indexOf(ch + c) == -1) {
                    return ch;
                }
                ch += c;
                inf.pos++;
            }
            return ch;
        };

        function igk_php_eval() { // php evaluation code
            igk_e.apply(this);
            var reserved = /((true|false)|\\$this|(a(bstract|nd|rray|s))|(c(a(llable|se|tch)|l(ass|one)|on(st|tinue)))|(d(e(clare|fault)|ie|o))|(e(cho|lse(if)?|mpty|nd(declare|for(each)?|if|switch|while)|val|x(it|tends)))|(f(inal|or(each)?|unction))|(g(lobal|oto))|(i(f|mplements|n(clude(_once)?|st(anceof|eadof)|terface)|sset))|(n(amespace|ew))|(p(r(i(nt|vate)|otected)|ublic))|(re(quire(_once)?|turn))|(s(tatic|witch))|(t(hrow|r(ait|y)))|(u(nset|se))|(__halt_compiler|break|list|(x)?or|var|while))$/;
            var w = 0;
            var l = 1; // line count
            var mode = 0;
            this.evals = function(s) {
                // read line
                s = s.trim().replace('<!--?php', '<span class="proc">&lt;?php</span>').replace('?-->', '<span class="proc">?&gt;</span>');
                var o = '';
                var m = 0;
                var tr = ''; // tempory read		
                o += '<span class=\'ln\'>' + l + '</span>'; // line number
                if (/(&lt;\?php|\?&gt;)/.test(s.trim())) {
                    o += "<span class='proc'>" + s + "</span>";
                } else {
                    if (s.length == 0)
                        o += "<br />";
                    else {
                        inf.ln = s.length;
                        inf.pos = 0;
                        inf.read = 1;
                        inf.s = s;
                        var sp = igk.createNode("span");
                        var ch = "";
                        while (inf.read && (inf.ln > inf.pos)) {
                            ch = s[inf.pos];
                            switch (ch) {
                                case ' ':
                                    sp.add("span").setHtml(" ");
                                    break;
                                case '\t':
                                    sp.add("span").addClass("t").setHtml(" ");
                                    break;
                                case '"':
                                case "'":
                                    if (inf.mode == 0) {
                                        inf.mode = 1; // string
                                        inf.pos++;
                                        w = _readStringLitteral(ch);
                                        sp.add("span").addClass("s").setHtml(w);
                                        inf.mode = 0;
                                    }
                                    break;
                                case "`":
                                    inf.mode = 1; // string
                                    inf.pos++;
                                    w = _readStringLitteral(ch);
                                    inf.mode = 0;
                                    sp.add('span').addClass("litteral").setHtml(w);
                                    break;
                                case '/': // for comment
                                    if ((inf.pos + 1 < inf.ln) && (inf.s[inf.pos + 1] == "/")) {
                                        sp.add("span").addClass("cm").setHtml(ch + inf.s.substr(inf.pos + 1));
                                        inf.read = 0;
                                    } else
                                        sp.add("span").addClass("pc").setHtml("/");
                                    break;
                                case "@":
                                    if ((inf.pos + 1 < inf.ln) && ("\"'".indexOf(inf.s[inf.pos + 1]) != -1)) {
                                        inf.pos++;
                                        w = _readStringLitteral(inf.s[inf.pos++]);
                                        sp.add("span").addClass("s").setHtml("@" + w);
                                        inf.mode = 0;
                                    } else {
                                        inf.pos++;
                                        w = _readStringLitteral(inf.s[inf.pos++]);
                                        sp.add("span").addClass("s").setHtml('@' + w);
                                    }
                                    break;
                                case '$': // read var
                                    inf.pos++;
                                    w = _readWord();
                                    if (w.length > 0) {
                                        sp.add("span").addClass("v r").setHtml("$" + w);
                                    } else {
                                        sp.add("span").setHtml(ch);
                                    }
                                    inf.pos--;
                                    // inf.read = 0;
                                    break;
                                    // case "&":
                                    // 	if (inf.mode == 0) {
                                    // 		inf.start = inf.pos;
                                    // 		inf.pos++;
                                    // 		while ((inf.pos < inf.ln) && (s[inf.pos] != ';')) {
                                    // 			inf.pos++;
                                    // 		}
                                    // 		if (s[inf.pos] == ';') {
                                    // 			m = sp.add("span").addClass("pc").setHtml(s.substr(inf.start, inf.pos - inf.start));
                                    // 		}
                                    // 		else
                                    // 			inf.read = 0;
                                    // 		delete (inf.start);
                                    // 	}
                                    // 	break;
                                default:
                                    if (inf.mode == 0) {
                                        if (",.?|()#[]-+{}\\/%*><;:=&".indexOf(ch) != -1) { // igk.char.isPonctuation(ch)){
                                            ch = _readPhpOperator(ch);
                                            m = sp.add("span").addClass("pc").setHtml(ch);
                                        } else {
                                            w = _readWord();
                                            var _cl = "w";
                                            if (/[0-9.]+/.test(w)) {
                                                _cl = "num";
                                            }
                                            m = sp.add("span").addClass(_cl).setHtml(w);
                                            if (reserved.test(w)) {
                                                m.addClass("r");
                                            }
                                            inf.pos--;
                                        }
                                        // inf.read = 0;
                                    }
                                    // sp.add("span").addClass("w").setHtml(w);
                                    break;
                            }
                            inf.pos++;
                        }
                        o += sp.getHtml();
                    }
                }
                l++;
                return o;
            };
            this.getLines = function() {
                return l;
            };
        }

        function igk_xml_eval() {
            throw new Error("Not implement");
        };
        igk.system.createNS("igk.highlightjs", {
            'php': igk_php_eval,
            'xml': igk_xml_eval
        });

        function __initCode() {
            $igk("code.igk-code").each_all(__init_code_area);
        };
        igk.ready(__initCode);
        igk.ctrl.registerReady(function() {
            if (this.tagName && this.tagName.toLowerCase() == "code" && this.getAttribute('igk-code')) {
                __init_code_area.apply($igk(this));
            }
        });
        igk.system.createNS("igk.winui.codes", {
            getCodes: function() {
                return m_codes || [];
            }
        });
    })();
    // ------------------------------
    // register media type 
    // ------------------------------
    (function() {
        const IGK_UNDEF = "undefined";
        const IGK_FUNC = "function";
        var props = {};
        var domProp = null;
        var vendors = ['webkit', 'ms', 'o'];
        var corecss = "balafon.css";
        var r = igk.createNode('div');
        var dev = igk.createNode('div');
        var dum = null;
        var rule = null;
        var m_chtheme = null; // will store the changed theme for dynamic theme changing purpose
        dev.addClass("igk-device");
        r.addClass('igk-media-type');
        // load dummy css style properties
        var xdum = igk.createNode("div", igk.namespaces.xhtml);
        var dum = xdum.o;
        var l = false;
        if (dum.style) {
            for (var i in dum.style) {
                if (typeof(dum.style[i]) != IGK_FUNC) {
                    switch (i) {
                        case 'cssText':
                        case 'length':
                        case 'parentRule':
                            continue;
                    }
                    // 
                    // firefox implement some property with - symbol ignore them
                    // 
                    if (i.indexOf('-') != -1)
                        continue;
                    props[(i + '').toLowerCase()] = i;
                    l = !0;
                }
            }
        }
        // delete(dum);
        // load css from dummy style resolving the safary error
        if (!l && window.getComputedStyle) {
            var txt = window.getComputedStyle(dum).cssText;
            if (txt) {
                var tab = txt.split(';');
                for (var i = 0; i < tab.length; i++) {
                    var s = tab[i].split(':')[0]; // first word
                    var d = getchars(s);
                    var index = 1;
                    if (s[0] == '-') {
                        index = 2;
                    } else {
                        // replace all next segment width uppercase layer
                    }
                    while (index > 0) {
                        index = s.indexOf('-', index);
                        if (index == -1)
                            break;
                        if (index + 1 < s.length) {
                            d[index + 1] = (s[index + 1] + '').toUpperCase();
                        }
                        index++;
                    }
                    s = getstring(d).replace(/( |\-)/g, "");
                    if (typeof(props[s.toLowerCase()]) == IGK_UNDEF)
                        props[s.toLowerCase()] = s;
                }
            }
        }
        // animation and transition
        var e = ['animation', 'transition'];
        var v = vendors;
        // checking global prop
        for (var i = 0; i < v.length; i++) {
            for (var j = 0; j < e.length; j++) {
                var s = (v[i] + e[j]).toLowerCase();
                if ((typeof(props[s]) == IGK_UNDEF) && props[s + "delay"]) {
                    props[s] = v[i] + e[j][0].toUpperCase() + e[j].substring(1);
                }
            }
        }
        // for chrome navigator require to register
        // if(igk.navigator.isChrome()){
        // igk.ready(function(){
        // // r.setCss({position:'absolute',zIndex:40});
        // // register media to bottom
        // igk.dom.body().add("div").setCss({position:'absolute',visibility:'hidden',overflow:'hidden','height':'0px', 'bottom':'0px'})
        // .addClass("igk-m-i")// media info
        // .add(r).
        // t.add(dev);		 
        // igk.css.appendRule(".igk-device:before{position:absolute;}");
        // igk.publisher.publish("sys://css/info",{});	
        // });
        // }
        function __getRule(f) {
            var m = null;
            var q = new RegExp("/" + f + "(.+)*");
            for (var i = 0; i < document.styleSheets.length; i++) {
                m = document.styleSheets[i];
                if (m.href && q.test(m.href + ""))
                    return m;
            }
            return null;
        }

        function _initTransitionProperties(t, list) {
            t = $igk(t);
            if (typeof(list) == 'string') {
                if (!t.autoTransition)
                    t.autoTransition = {};
                if (t.autoTransition[list]) {
                    return true;
                }
                if (list == 'all') {
                    list = ['width', 'height', 'marginLeft', 'marginRight', 'margin', 'padding', 'paddingLeft', 'paddingRight'];
                }
            }
            var g = t.getComputedStyle("transition");
            if (!g)
                return false;
            var mark = 0;

            function _initPropertyStyle(t, n) {
                if (!t.autoTransition)
                    t.autoTransition = {};
                if (t.autoTransition[n])
                    return;
                t.autoTransition[n] = 1;
                return new(function(n) {
                    var _os = t.o.style[n]; // old style
                    igk.defineProperty(t.o.style, n, {
                        get: function() {
                            if (mark)
                                return _os;
                            return t.getComputedStyle(n);
                        },
                        set: function(v) {
                            if (v == 'auto') {
                                t.o.style.setProperty(n, 'auto');
                                var r = t.getComputedStyle(n);
                                t.o.style.setProperty(n, _os);
                                setTimeout(function() {
                                    t.o.style.setProperty(n, r);
                                }, 1);
                                return;
                            }
                            _os = v;
                            t.o.style.setProperty(n, v);
                        }
                    });
                })(n);
            };
            var n = '';
            var d = '';
            for (var i = 0; i < list.length; i++) {
                n = list[i];
                mark = 1;
                var v = t.getComputedStyle(n);
                var s = t.o.style[n];
                mark = 0;
                if (s == '') {
                    // init width default property
                    t.o.style.setProperty(n, v);
                }
                _initPropertyStyle(t, n);
            }
            // restore transition properties
            t.setCss({
                transition: g
            });
        };
        delete(igk.css);
        //throw new Error(igk.css);
        function __getStyleValue(stylelist, n) {
            switch (n.toLowerCase()) {
                case "transition":
                    var s = stylelist[n];
                    if (!igk.isUndef(s) && s.length > 0) // you specify a transition. get by chrome
                        return s;
                    // other navigation join property style
                    var v_p = ['property', 'duration', 'timing-function', 'delay'];
                    var v_v = vendors;
                    var v_k = "";
                    var v_prop = {
                        toString: function() {
                            var t = v_prop.property;
                            var di = v_prop.duration;
                            var tf = v_prop["timing-function"];
                            var dl = v_prop.delay;
                            var s = "";
                            if (!igk.isUndef(t)) {
                                for (var i = 0; i < t.length; i++) {
                                    if (i > 0) {
                                        s += ',';
                                    }
                                    s += t[i] + " " + di[i] + " " +
                                        tf[i] + " " +
                                        dl[i];
                                }
                            }
                            return s;
                        }
                    };
                    var v_t = 0;
                    var v_splitcsss_pattern = "([^,(]+(\\(.+?\\))?)[\\s,]*";
                    // for standard
                    for (var i = 0; i < v_p.length; i++) {
                        v_k = n + "-" + v_p[i];
                        if ((i == 0) && (typeof(stylelist[v_k]) != igk.constants.undef))
                            v_t = 1;
                        if (!v_t)
                            break;
                        s += ((i > 0) ? " | " : "") + stylelist[v_k];
                        v_prop[v_p[i]] = igk.system.regex.split(v_splitcsss_pattern, stylelist[v_k]);
                    }
                    if (!v_t) {
                        // find througth specification
                        v_t = 0;
                        for (var j = 0; j < v_v.length; j++) {
                            v_prop[v_v[j]] = {};
                            if (v_t)
                                s += "|";
                            for (var i = 0; i < v_p.length; i++) {
                                v_k = v_v[j] + n + "-" + v_p[i];
                                s += stylelist[v_k];
                                if (!igk.isUndef(stylelist[v_k])) // style found ..
                                    v_prop[v_p[i]] = igk.system.regex.split(v_splitcsss_pattern, stylelist[v_k]);
                            }
                            v_t = 1;
                        }
                    }
                    return v_prop.toString();
                    break;
                default:
                    // console.error(n);
                    return stylelist[n];
            }
        };

        function __setProperty(item, properties) {
            var _navsupport = igk.navigator.isIE();
            if (item && item.style && properties) {
                for (var i in properties) {
                    try {
                        if (i.startsWith("--")) {
                            //ie 11 not supporting custom data on css								
                            item.style.setProperty(i, properties[i]);
                            if (!_navsupport)
                                continue;
                        } // else
                        item.style[i] = properties[i];
                    } catch (ex) {
                        // boxSizing cause error					
                    }
                }
            } else {
                console.debug('[BJS] -/!\v properties ' + item + ' not defined');
            }
        }

        function _get_html_theme() {
            var d = document.getElementsByTagName('html')[0];
            return d.getAttribute('data-theme');
        }
        var dynStyle = null;
        igk.system.createNS("igk.css", {
            changeDocumentTheme(n) {
                var d = document.getElementsByTagName('html')[0];
                if (typeof(n) == 'undefined') {
                    // toggle theme
                    n = 'dark';
                    if (d.getAttribute('data-theme') == n) {
                        n = 'light';
                    }
                }
                d.setAttribute('data-theme', n);
                igk.cookies.set('theme_name', n);
                igk.log(`>change cookie ${n}`);
                return n;
            },
            getStyleSelectorList(index) {
                if (this != igk.css) {
                    throw new Error("Func must be called staticly");
                }
                var rm = document.styleSheets[index];
                let r = null;
                if (rm) {
                    r = [];
                    for (var i = 0; i < rm.cssRules.length; i++) {
                        if (rm.cssRules[i].selectorText)
                            r.push(rm.cssRules[i].selectorText);
                    }
                    r.sort();
                }
                return r;
            },
            isItemSupport(names) {
                if (typeof(names) == 'string') {
                    var s = names.toLowerCase();
                    return s in props;
                }
                for (var i = 0; i < names.length; i++) {
                    if (typeof(props[names[i].toLowerCase()]) != IGK_UNDEF)
                        return !0;
                }
                var s = dum.style;
                if (s) {
                    // for safari
                    for (var i = 0; i < names.length; i++) {
                        if (typeof(s[names[i]]) != IGK_UNDEF)
                            return !0;
                    }
                }
                return !1;
            },
            setProperties(item, properties) {
                if ((item == null) || (!item.style)) {
                    return;
                }
                var k = {};
                var n = null;
                var v = null;
                for (var ni in properties) {
                    if (typeof(ni) != 'string')
                        continue;
                    if (ni.startsWith("--")) {
                        k[ni] = properties[ni];
                        continue;
                    }
                    v = properties[ni];
                    if (igk.css.isItemSupport(['webkit' + ni])) {
                        n = props[('webkit' + ni).toLowerCase()];
                        if (n)
                            k[n] = v;
                    } else if (igk.css.isItemSupport([ni])) {
                        n = props[ni.toLowerCase()];
                        if (n) {
                            k[n] = v;
                        }
                    }
                }
                // setting real value		
                __setProperty(item, k);
            },
            getStyleValue: function(stylelist, n) {
                // get css style list value
                // @stylelist: get width getComputedStyle function
                // @n : the name of the property to get
                return __getStyleValue(stylelist, n);
            },
            toggleClass(i, p) { // toggle class
                var q = $igk(i).first();
                if (q) {
                    q.toggleClass(p);
                    console.log(q.o.className);
                }
            },
            initAutoTransitionProperties: _initTransitionProperties,
            loadLinks(t) {
                // console.log('init font list ... ');
                for (var i = 0; i < t.length; i++) {
                    var c = document.createElement("link");
                    c.setAttribute("href", t[i]);
                    c.setAttribute("rel", "stylesheet");
                    igk.dom.body().add(c);
                    // console.log('font : ' + t[i]);
                }
            },
            getEmSize(c, t) {
                // return the em font size of this target element
                var s = igk.getNumber($igk(c).getComputedStyle("font-size"));
                var T = s;
                t = t || igk.dom.body().o;
                if (t == c)
                    return 1;
                while (c) {
                    c = c.o.parentNode;
                    if (c && (c != t)) {
                        c = $igk(c);
                        T = igk.getNumber($igk(c).getComputedStyle("font-size"));
                        if (T != s)
                            break;
                    } else {
                        c = null;
                    }
                }
                s = Math.round(s / T, 3);
                return s;
            },
            appendRule(c) { // append rule to balafon.css.php or css definition 
                rule = rule || __getRule(corecss);
                try {
                    if (rule)
                        rule.insertRule(c, rule.cssRules.length);
                } catch (e) {}
            },
            appendStyle(uri) {
                // plugin style to document
                if (!uri) {
                    return;
                }
                var e = document.createElement("link");
                $igk(e).setAttribute("href", uri)
                    .setAttribute("type", "text/css")
                    .setAttribute("rel", "stylesheet");
                document.head.appendChild(e);
                return document.styleSheets[document.styleSheets.length - 1];
            },
            getMediaType() {
                return (r.getComputedStyle('content', ':before') + "").replace(/\"/g, "");
            },
            getMediaIndex() {
                return (r.getComputedStyle('z-index', ':before') + "").replace(/\"/g, "");
            },
            getDevice() {
                return (dev.getComputedStyle('content', ':before') + "").replace(/\"/g, "");
            },
            changeTheme(uri, value) {
                igk.ajx.get(uri + "/" + value, null, function(xhr) {
                    if (this.isReady()) {
                        rule = rule || __getRule(corecss);
                        if (rule) {
                            // rule.remove();
                            while (rule.cssRules.length > 0) {
                                rule.deleteRule(0);
                            }
                            if (m_chtheme != null) {
                                $igk(m_chtheme).remove();
                            }
                            var s = document.createElement("style");
                            s["type"] = "text/css";
                            s.innerText = xhr.responseText;
                            document.head.appendChild(s);
                            m_chtheme = s;
                            // igk.css.appendRule(xhr.responseText, 0);
                        }
                    }
                });
            },
            selectStyle(rx, callback) {
                var d = document.styleSheets;
                if (d.length <= 0)
                    return 0;
                var match = [];
                var k = [];
                for (var i in rx) {
                    for (var j = 0; j < d.length; j++) {
                        if (!match[j] && rx[i].test(d[j][i])) {
                            callback(d[j]);
                            match[j] = 1;
                            k.push(d[j]);
                        }
                    }
                }
                return k;
            },
            getComputedClassStyle(c, p, j) {
                // @c:class name
                // @p:property
                // @j:speudo type
                if (dum == null) {
                    dum = igk.createNode('div');
                    if (igk.navigator.isChrome()) {
                        igk.ready(function() {
                            igk.dom.body().add(dum); // .setHtml("infof").add(r).t.add(dev);		
                        });
                    } else
                        $igk(h).add(dum);
                    dum.addClass(c);
                }
                var g = dum.getComputedStyle(p, j);
                dum.remove();
                dum = null;
                return g;
            },
            getComputedSrcStyle(h, c, p, j) {
                // h:host
                // 
                h = h || igk.dom.body().o;
                if (dum == null) {
                    dum = igk.createNode('div');
                    if (igk.navigator.isChrome()) {
                        igk.ready(function() {
                            $igk(h).add(dum); // .setHtml("infof").add(r).t.add(dev);		
                        });
                    } else
                        $igk(h).add(dum);
                    dum.addClass("dispn").addClass(c);
                }
                var g = dum.getComputedStyle(p, j);
                dum.remove();
                dum = null;
                return g;
            },
            appendTempStyle(c) {
                var q = $igk("select#tempsp").first();
                if (!q) {
                    q = igk.dom.body().add("style");
                    q.o["type"] = "text/css";
                }
                q.setHtml(c);
            },
            clearTempStyle() {
                var q = $igk("select#tempsp").first();
                if (q) {
                    q.setHtml('');
                }
            },
            isMatch(t, p) {
                // check if  t match pattern
                // @t:dom node
                // p:pattern
                if ((/^#[\w\-_]+$/.exec(p))) { // search parent by id
                    // exemple: ^#info
                    b = $igk(t).getParentById(p.substring(1));
                    if (b)
                        v_sl.push(b);
                } else if ((/^\./.exec(p))) { // search parent by class by class
                    p = p.substring(1);
                    var s = $igk(t).o;
                    var rx = new RegExp("(" + p + ")(\\s|$)", "i");
                    if (rx.exec("" + s.className)) {
                        return 1;
                    }
                } else {
                    if (item.tagName.toLowerCase() == pattern)
                        return 1;
                }
                return 0;
            },
            setProperty(item, name, value) {
                var k = {};
                var n = null;
                if (igk.css.isItemSupport(['webkit' + name])) {
                    n = props[('webkit' + name).toLowerCase()];
                    k[n] = value;
                    i
                } else if (igk.css.isItemSupport([name])) {
                    n = props[name.toLowerCase()];
                    k[n] = value;
                    // that notation work only for firefox
                    // item.setCss({[n]: value});						
                }
                // setting real value
                __setProperty(item, k);
            },
            setTransitionDuration(item, time) {
                igk.css.setProperty(item.o, 'transitionduration', time);
                return igk.css;
            },
            setTransitionDelay(item, time) {
                igk.css.setProperty(item.o, 'transitiondelay', time);
                return igk.css;
            },
            changeStyle(selectorText, style) { // change css style definition
                var theRules = new Array();
                if (document.styleSheets[0].cssRules) {
                    theRules = document.styleSheets[0].cssRules;
                } else if (document.styleSheets[0].rules) {
                    theRules = document.styleSheets[0].rules;
                }
                for (n in theRules) {
                    if (theRules[n].selectorText == selectorText) {
                        theRules[n].style = style;
                    }
                }
            },
            getProperties() {
                /**
                 * return css properties to check
                 */
                return {};
            },
            appendDynamicRule(rule, style) {
                dynStyle = dynStyle || ((document.styleSheets.length > 0) ? document.styleSheets[document.styleSheets.length - 1] : (function() {
                    q = igk.dom.body().add("style");
                    q.o["type"] = "text/css";
                    return q.o.style;
                })());

                dynStyle.addRule(rule, style);
            },
            rmDynamicRule() {
                if (dynStyle) {
                    //$igk(dynStyle).remove();
                    dynStyle = null;
                    console.log("remove dynStyle");
                }
            }
        });
        igk.defineProperty(igk.css, "rule", { get: function() { return rule; } });
        igk.defineProperty(igk.css, "vendors", {
            get: function() {
                return vendors;
            }
        });
        // iniitilial ie events
        igk.ready(function() {
            var B = igk.dom.body();
            if (igk.navigator.isFirefox() || igk.navigator.isChrome() || igk.navigator.isSafari()) {
                // to get content of css style item must be added to document
                B.add("div").setCss({ position: 'absolute', visibility: 'hidden', overflow: 'hidden', 'height': '0px', 'bottom': '0px' })
                    .addClass("igk-m-i") // media info
                    .add(r).
                t.add(dev);
                igk.css.appendRule(".igk-media-type:before{position:absolute;}");
                igk.css.appendRule(".igk-device:before{position:absolute;}");
            }
            var dev = igk.css.getDevice();
            var m_c = igk.css.getMediaType(); // current
            function __checkMedia() {
                var i = igk.css.getMediaType();
                var d = null;
                if (i != m_c) {
                    B.rmClass(m_c).addClass(i);
                    __raiseMedia(i);
                }
            };

            function __raiseMedia(i) {
                m_c = i;
                igk.publisher.publish(igk.publisher.events.mediachanged, {
                    mediaType: i,
                    mediaIndex: igk.css.getMediaIndex(),
                    device: igk.css.getDevice()
                });
            };
            // + | theme manage change theme detections
            // + | register to (prefers-color-scheme: dark)
            // + |
            function __checkMediaTheme() {
                const cTheme = _get_html_theme();
                let m = window.matchMedia('(prefers-color-scheme: dark)');
                if (!cTheme) {
                    igk.css.changeDocumentTheme(m.matches ? 'dark' : 'light');
                }
                m.addEventListener('change', (e) => {
                    igk.css.changeDocumentTheme(e.matches ? 'dark' : 'light');
                });
            };

            if (window.matchMedia) {
                __checkMediaTheme();
            }


            B.addClass(m_c);
            __raiseMedia(m_c);
            igk.winui.reg_event(window, 'resize', __checkMedia);
        });
        var e = {};
        e.mediachanged = 1;
        for (var s in e) {
            e[s] = 'sys://events/' + s;
        }
        igk.system.createNS("igk.publisher.events", e);
    })();
    (function() {
        function igk_str_padEnd(l, v) {
            var hl = this.length;
            var s = this.toString();
            while (hl < l) {
                s += '' + v;
                hl++;
            }
            return s;
        }

        function igk_str_startWith(c) {
            if (typeof(c) == 'undefined')
                return !1;
            var l = this.length;
            var x = c.length;
            var i = 0;
            if (l == 0)
                return !1;
            if (typeof(this[0]) == 'undefined') {
                // ie7 do not suport bracket operator
                while ((i < x) && (i < l) && (this.charAt(i) == c.charAt(i))) {
                    i++;
                }
            } else {
                while ((i < x) && (i < l) && (this[i] == c[i])) {
                    i++;
                }
            }
            return i == x;
        };
        if (!String.prototype.startsWith)
            String.prototype.startsWith = igk_str_startWith;
    })();
    //
    // canvas utility 
    //
    (function() {
        //filter list
        var prop = ['sepia', 'blur', 'contrast', 'brightness', 'drop-shadow', 'grayscale', 'hue-rotate', 'invert', 'saturate', 'opacity'];

        function igk_get_filter_exp(f) {
            if (f == null) return "none";
            var s = "";
            var m = "";
            for (var i in f) {
                m = i;
                switch (m) {
                    case "huerotate":
                        m = "hue-rotate";
                        break;
                    case "dropshadow":
                        m = "drop-shadow";
                        break;
                }
                if (s.length > 0)
                    s += " ";
                s += m + "(" + f[i] + ")";
            }
            return s;
        };

        function igk_getFilterProp(o) {
            var go = igk.JSON.parse(o.getAttribute("igk:filter"));
            if (go) {
                var r = "";
                for (var i = 0; i < prop.length; i++) {
                    if (igk_isdefine(go[prop[i]])) {
                        if (r.length > 0)
                            r += " ";
                        r += prop[i] + "(" + go[prop[i]] + ")";
                    }
                }
                // o.setCss({filter:'progid:DXImageTransform.Microsoft.Blur(PixelRadius="5")'});
                if (igk.navigator.isIEEdge()) {
                    o.setCss({ filter: r });
                    //return null;
                }
                o.setCss({ filter: r });
                return r;
            }
            return null;
        };
        var _g_canva = igk.createNode("canvas");
        var _g_ctx = _g_canva.o.getContext ? _g_canva.o.getContext("2d") : null;
        if (_g_ctx == null)
            return;
        igk.system.createNS("igk.canvas", {
            supportFilter: function() {
                return "filter" in _g_ctx;
            },
            getFilterExpression: function(c) {
                var f = null;
                // transform code string to canvas filter expression
                if (c != null) {
                    if (!String.prototype.padEnd)
                        String.prototype.padEnd = igk_str_padEnd;
                    var s = (c + "").padEnd(20, 0);
                    var u = [];
                    for (var i = 0; i < 10; i++) {
                        u[i] = "0x" + s.substring(i * 2, (i * 2) + 2);
                    }
                    var sfilter = {};
                    sfilter.grayscale = Math.round((eval(u[0]) / 100 / 255.0) * 10000) + "%"; //"0
                    sfilter.huerotate = Math.round((eval(u[1]) / 100 / 255.0) * 36000) + "deg"; // ("
                    sfilter.blur = eval(u[2]) + "px"; // ("0x
                    sfilter.sepia = Math.round((eval(u[3]) / 100 / 255.0) * 10000) + "%";
                    sfilter.saturate = (100 - Math.round((eval(u[4]) / 100 / 255.0) * 10000)) + "%";
                    sfilter.invert = Math.round((eval(u[5]) / 100 / 255.0) * 10000) + "%";
                    sfilter.opacity = (100 - Math.round((eval(u[6]) / 100 / 255.0) * 10000)) + "%";
                    sfilter.brightness = (100 - Math.round((eval(u[7]) / 100 / 255.0) * 20000)) + "%"; // scale from 0 - 200  / default is 100
                    sfilter.contrast = (100 - Math.round((eval(u[8]) / 100 / 255.0) * 10000)) + "%";
                    f = sfilter;
                }
                return igk_get_filter_exp(f);
            },
            getFilterString: igk_get_filter_exp,
            toStringExpression: function(v) {
                var r = "";
                for (var i = 0; i < prop.length; i++) {
                    var n = prop[i];
                    if (n in v) {
                        r += igk.system.convert.ToBase(Math.round((v[prop[i]] / 100) * 255), 16);
                    }
                }
                return r;
            }
        });
        var hprop = {};
        for (var i = 0; i < prop.length; i++) {
            hprop[prop[i]] = i;
        }
        var v_list = igk.defineEnum(null, hprop);
        igk.defineProperty(igk.canvas, "filters", {
            get: function() {
                return v_list;
            }
        });
    })();
    // horizontal menu manager
    (function() {
        // select all ul that have a igk-hmenu class 
        // select all 
        var items = [];

        function __hideSubmenu(q) {
            q.rmClass("igk-show");
        }

        function __showSubmenu(q) {
            q.addClass("igk-show");
        }

        function __initMenu(q) {
            if (!q || q.data["system/menu"])
                return;
            $igk(q.o.parentNode).reg_event("mouseover", function() {
                __showSubmenu(q);
            }).reg_event("mouseleave", function() { __hideSubmenu(q); });
            q.data["system/menu"] = 1;
        }

        function __init() {
            $igk("ul.igk-hmenu").select('li ul').each(function() {
                items.push(this);
                __initMenu(this);
                return !0;
            });
        }
        // ready menu function
        igk.ready(function() {
            __init();
            igk.ajx.fn.registerNodeReady(function() {
                __init();
            });
        });
    })();
    (function() {
        igk.system.createNS("igk.ctrl", {
            initMemoryUsage: function(uri) {
                var p = $igk(igk.getParentScript());
                var tout = null;

                function __getMemory() {
                    if (p.o.parentNode == null) {
                        return;
                    }
                    if (tout) {
                        clearTimeout(tout);
                        tout = null;
                    }
                    igk.ajx.get(uri, null, function(xhr) {
                        if (this.isReady()) {
                            var pt = igk.createNode("dummy");
                            pt.setHtml(xhr.responseText);
                            var q = $igk(pt.o.children[0]);
                            if (q) {
                                p.setHtml(q.getHtml());
                            }
                            setTimeout(__getMemory, 2000);
                        }
                    });
                }
                igk.ready(__getMemory);
            }
        });
    })();
    (function() {
        // if (igk.navigator.isIE() && igk.navigator.IEVersion()<10){
        // 	// not allow the creationg of this application
        // 	// ie 10 auto load and lock the file because unload
        // 	return;
        // }
        // file picker
        var s = 0;
        var _pic = 0;
        var re = 0; // recreate field
        function __getfile() {
            var s = ns_igk.createNode("input");
            s.setAttribute("type", "file");
            s.o.id = "clFile";
            s.o.name = "clFile";
            // for firefox
            // s.addClass("dispn");
            // for safari
            s.addClass("posab");
            s.setCss({ visibility: 'hidden' });
            return s;
        };
        igk.system.createNS("igk.system.io", {
            pickfile(uri, p, osrc) {
                s = s || __getfile();
                p = p || {};
                // reset the value
                s.o.value = null;
                if (p.accept) {
                    s.o.setAttribute("accept", p.accept);
                }
                // pick file. 
                // >uri: uri to send the picked file
                // >p: property to manage picking file
                // >osrc: source of the requesting
                igk.dom.body().prepend(s);

                function __change() {
                    if (uri == null) {
                        var complete = (p ? p.complete : null);
                        if (complete) {
                            complete(s.o.files[0]);
                        }
                    } else {
                        igk.ajx.uploadFile(osrc, s.o.files[0], uri, true,
                            p ? p.complete : null,
                            p ? p.start : null,
                            p ? p.progress : null,
                            p ? p.done : null
                        );
                    }
                    s.unreg_event('change', __change);
                    s.remove();
                    s.o.value = null;
                    if (re)
                        s = null;
                }
                // remove the reg change event
                s.unreg_event('change', __change);
                s.reg_event('change', __change);
                s.o.accept = null;
                if (p.accept) {
                    s.o.accept = p.accept;
                }
                try {
                    //try to set the files
                    s.o.files = null;
                } catch (e) {
                    //
                    re = 1; // need to recreate.
                }
                s.o.click();
            }
        });
        igk.system.io.pickfile.getSrc = function() { return _src; };
        igk.winui.initClassControl("igk-js-pickfile", function() {
            var q = this;
            var s = igk.JSON.parse(q.getAttribute("igk:data"));
            if (s && igk.is_object(s)) {
                this.reg_event("click", function() {
                    igk.system.io.pickfile(
                        s.uri,
                        s.options,
                        q);
                });
            }
        });
    })();
    // debugger manager
    (function() {
        igk.winui.initClassControl("igk-debuggernode",
            function() {
                var q = this;
                this.reg_event("click", function() {
                    q.setHtml("");
                });
            }, { desc: "debugger node" });
    })();
    // (function(){
    // igk.ready(function(){
    // $igk("*").each(
    // function(){
    // return !0;
    // }
    // );
    // })
    // })();
    // igk-tooltip
    (function() {
        igk.ctrl.registerAttribManager("igk-tooltip", { desc: "for tooltip component" });
        igk.ctrl.bindAttribManager("igk-tooltip", function(n, m) {
            var p = igk.JSON.parse(m);
            var q = this;
            var tip = null;
            this.reg_event("mouseover", function(evt) {
                if (tip == null)
                    tip = igk.winui.tooltip.show(q, p.data);
                else
                    tip.show();
            });
            // .reg_event("mouseout",function(evt){
            // if(tip)
            // tip.hide();
            // }).reg_event("mouseleave", function(){
            // if(tip)
            // tip.hide();
            // });
        });
        var _tip_offset = 200;
        var _tip_c = 1000;

        function _tip() {
            var t = igk.createNode("div");
            t.setOpacity(0.2)
                .addClass("igk-trans-all dispb posab pad-a-4")
                .setCss({
                    zIndex: _tip_offset + _tip_c,
                    padding: "4px",
                    backgroundColor: "white",
                    border: "1px solid black",
                    cursor: "pointer"
                })
                .setHtml(" ");
            // igk.ctrl.selectionmanagement.disable_selection(t.o);
            var _m_closing = false;
            var _m_t = "";
            igk.appendProperties(this, {
                show: function() {
                    if (_m_closing)
                        return;
                    _m_closing = 1;
                    t.setOpacity(1.0)
                        .setHtml(this.data);
                    var b = this.owner.getScreenBounds();
                    var loc = this.owner.getScreenLocation();
                    t.setCss({ left: loc.x + "px", top: (loc.y + (b.h / 2)) + "px" });
                    igk.dom.body().appendChild(t.o);
                },
                hide: function() {
                    if (_m_closing == 1) {
                        t.setOpacity(0.1);
                    }
                }
            });
            var self = this;
            t.reg_event("mouseover", function() {
                self.show();
            }).reg_event("mouseout", function(evt) {
                self.hide();
            }).reg_event("transitionend", function(evt) {
                if (evt.propertyName == "opacity")
                    if (_m_closing) {
                        t.remove();
                        _m_closing = 0;
                    }
            });
        };
        igk.winui.tooltip = function() {
            // tooltip constructor
        };
        igk.system.createNS("igk.winui.tooltip", {
            show: function(q, data) {
                var d = new _tip();
                d.data = data;
                d.owner = q;
                d.show();
                return d;
            }
        });
    })();
    // (function(){
    // if(!igk.math.rectangle.intersect){
    // igk.system.createNS("igk.math.rectangle",{
    // intersect: function(rc1,rc2){
    // var H=rc1.h + rc2.h;
    // var W=rc1.w + rc2.w;
    // var minx=Math.min(rc1.x,rc2.x);
    // var maxx=Math.max(rc1.x + rc1.w,rc2.x + rc2.w);
    // var miny=Math.min(rc1.y,rc2.y);
    // var maxy=Math.max(rc1.y + rc1.h,rc2.y + rc2.h);
    // var w1=W -(maxx - minx);
    // var h1=H -(maxy - miny);
    // if(
    // (w1 >=0)
    // &&
    // (h1 >=0)
    // )
    // {
    // double k=Math.Min(rc1.Right,rc2.Right) - w1;
    // double r=Math.Min(rc1.Bottom,rc2.Bottom) - h1;
    // return !0; // new Rectangled(k,r,w1,h1);
    // }
    // return !1; // Rectangled.Empty;
    // }
    // });
    // }
    // alert("ok 3");
    igk.ready(
        function() {
            function __load(j) {
                // var loc=j.getBoundingClientRect(); // j.o.getBoundingClientRect ? 
                // j.o.getBoundingClientRect(): {x:0,y:0};// \{x:j.getscrollLeft(),
                // y:j.getscrollTop()
                // \};// getLocation();
                // 
                // var size=igk.winui.screenSize();
                // get screen visibility
                // var vsb=((loc.x>=0) &&(loc.x<=size.width) &&(loc.y>=0) &&(loc.y<=size.height));
                // firefox : scrollHeight ok ... other not ok
                // iternet chrome
                // igk.show_prop(j.o);
                // var s=j.getSize();
                if (j.data["img-js.loaded"]) {
                    return !1;
                }
                if (j.getisVisible()) {
                    j.data["img-js.loaded"] = 1;
                    // load image
                    var i = document.createElement("img");
                    $igk(i).reg_event("load", function(evt) {
                        console.debug("complete ....");
                        igk.dom.copyAttributes(j.o, i, { 'data': 1 });
                        j.o.parentNode.replaceChild(i, j.o);
                    });
                    // set properties
                    i["src"] = j.getAttribute("data");
                    return !0;
                }
                return !1;
            };
            // var items=[];
            function __fcScroll(evt) {
                var tab = $igk(evt.target).data["img-js"].items; // items;// copy tab
                var _ctab = [];
                // view all item
                // __load.apply(this,[$igk(evt.target)]);
                // t
                for (var i = 0; i < tab.length; i++) {
                    var j = tab[i];
                    // if(j && !j.loaded){
                    if (!__load(j)) {
                        _ctab.push(j);
                    }
                    // delete(items[i]);
                    // }
                }
                $igk(evt.target).data["img-js"].items = _ctab;
                if (_ctab.length == 0) {
                    $igk(evt.target).unreg_event("scroll", __fcScroll);
                }
                console.debug("" + tab.length);
            }
            igk.dom.body().select("igk-img-js").each(function() {
                var p = this.o.offsetParent;
                if (p != null) {
                    var q = $igk(p);
                    // get the offset parent to register to visibility component
                    if (!q.data["img-js.parentScroll"]) {
                        q.reg_event("scroll", __fcScroll);
                        q.data["img-js.parentScroll"] = 1;
                        q.data["img-js"] = {
                            items: []
                        };
                    }
                    // store item affected
                    // 	 items.push(this);
                    q.data["img-js"].items.push(this);
                }
                return !0;
            });
        });
    // })();
    (function() {
        // igk-scroll-loader tag component
        // :Represent an element that will be load every time scroll change or visibility
        // :::testapi func:test_contentscroll
        function __fcScroll(q) {
            if (q.loaded || q.loading || !q.getisVisible())
                return !1;
            q.loading = !0;
            igk.ajx.get(q.getAttribute('data'), null, function(xhr) {
                if (this.isReady()) {
                    q.loaded = !0;
                    q.loading = false;
                    q.unreg_event("scroll", __load);
                    let p = q.data.scrollP;
                    let r = q.o.previousSibling;
                    this.replaceResponseNode(q.o);
                }
            });
            return !0;
        };

        function __load() {
            var d = $igk(this);
            var k = "igk-scroll-loader.items";
            var h = d.data[k];
            if (h.length == 0) {
                d.unreg_event("scroll", __load);
                d.data[k] = null;
                return;
            }
            for (var i = 0; i < h.length; i++) {
                if (__fcScroll(h[i])) {
                    h.splice(i, 1);
                    i--;
                }
            }
        }
        /**
         * init all scroll loader
         */
        function __init_doc_scrollloader() {
            igk.dom.body().select("igk-scroll-loader").each(function() {
                __init_tag(this);
                return !0;
            });
        }

        function __init_tag(t) {
            if (t.data["igk-scroll-loader"])
                return;
            let p = t.select("^.igk-scroll-loader_container").first() || t.getscrollParent().o;
            var sk = "igk-scroll-loader.parentScroll";
            if (p != null) {
                var q = $igk(p);
                if (!q.data[sk]) {
                    q.reg_event("scroll", __load);
                    q.data[sk] = 1;
                    q.data["igk-scroll-loader.items"] = [];
                }
                var h = q.data["igk-scroll-loader.items"];
                h.push(t);
                t.data["igk-scroll-loader"] = 1;
                t.data.scrollP = p;
                if (t.getisVisible()) {
                    __fcScroll(t);
                }
            }
            // else {
            // 	console.debug("no scrolling ");
            // }
        }
        // register a scroll loader component
        igk.reg_tag_component("igk-scroll-loader", {
            desc: "scroll loader",
            func: function() {
                __init_tag($igk(this));
            }
        });
        igk.ready(__init_doc_scrollloader);
    })();
    (function() {
        // binding select data
        // tagname : select
        // attribute expected : igk-bind-data-ajx
        igk.ctrl.registerAttribManager("igk-bind-data-ajx", { desc: "used to bind data for selection" });
        igk.ctrl.bindAttribManager("igk-bind-data-ajx", function(n, m) {
            var q = this;
            switch (this.o.tagName.toLowerCase()) {
                case 'select':
                    igk.ajx.post(m, null, function(xhr) {
                        if (this.isReady()) {
                            q.setHtml(xhr.responseText);
                            console.debug(xhr.responseText);
                        }
                    });
                    break;
            }
        });
    })();
    // obj: circle waiter
    (function() {
        function __init_waiter() {
            var q = this;
            var _running = true;
            var _dat = null;
            var _offset = 0;
            igk.appendProperties(this.data, {
                canva: null, // canva zone
                dir: 1, // direction
                penWidth: 2, // pen width
                render: function(v, cl, of_set) {
                    var w = igk.getNumber(this.canva.getComputedStyle("width"));
                    var h = igk.getNumber(this.canva.getComputedStyle("height"));
                    var v1 = 0;
                    var v2 = 0;
                    var cx = w / 2;
                    var cy = h / 2;
                    var penw = this.penWidth || 4;
                    var r = Math.min(w / 2, h / 2) - (penw / 2);
                    this.canva.setAttribute("width", w);
                    this.canva.setAttribute("height", h);
                    var ctx = this.canva.o.getContext('2d');
                    ctx.lineWidth = penw;
                    ctx.clearRect(0, 0, w, h);
                    // background
                    ctx.strokeStyle = '' + cl;
                    ctx.beginPath();
                    var offset = (_offset * (Math.PI / 180.0)) - (Math.PI / 2);
                    var _s = _getData();
                    switch (_s.mode) {
                        case 2:
                            if (this.dir == 1) {
                                v1 = offset;
                                v2 = offset + (2 * Math.PI) * v;
                                // ctx.arc(cx,cy,r,offset, offset+ (2*Math.PI)*v ,false);
                            } else {
                                v1 = offset + (2 * Math.PI) * (1 - v);
                                v2 = offset + (2 * Math.PI);
                                // ctx.arc(cx,cy,r,offset +(2*Math.PI)*(1-v) ,offset+ (2*Math.PI) ,false);
                            }
                            break;
                        case 1:
                            if (this.dir == 1) {
                                v1 = offset + (of_set * (Math.PI * 2));
                                v2 = offset + (2 * Math.PI) * v;
                            } else {
                                v1 = offset + (2 * Math.PI) * (1 - v);
                                v2 = offset + ((2 * Math.PI) * (1 - of_set));
                            }
                            break;
                    }
                    if (v1 != v2)
                        ctx.arc(cx, cy, r, v1, v2, false);
                    ctx.stroke();
                }
            });

            function _getData() {
                if (_dat == null) {
                    var _s = q.data.storyboard.getComputedStyle('content', ':before');
                    var _t = /^"((.)+)"$/i.exec(_s);
                    _dat = igk.JSON.init_data({ stop: 'width', mode: 1 }, (_t ? _t[1].replace(/\\\"/g, "\"") : null), function(s) {
                        s.stop = (_t ? _t[1] : null) || 'width';
                    });
                    q.data.penWidth = q.data.storyboard.getComputedStyle('border-size', ':before');
                }
                return _dat;
            };
            this.data.canva = this.add("canvas").addClass("posab fitw fith loc_t loc_l");
            this.data.storyboard = this.add("div").addClass("igk-anim-time-board")
                .reg_event("transitionend", function(evt) {
                    var _m = _getData();
                    if (evt.propertyName == _m.stop) {
                        // base of definition
                        if (q.data.dir == 1) {
                            q.data.storyboard.setCss({ 'width': '0px', height: '0px' }).rpClass("igk-cl-1", "igk-cl-2");
                            q.data.dir = -1;
                        } else {
                            q.data.storyboard.setCss({ 'width': '100px', height: '100px' }).rpClass("igk-cl-2", "igk-cl-1");
                            // .rmClass("igk-cl-2").addClass("igk-cl-1");
                            q.data.dir = 1;
                        }
                        //for infinit loop
                        //_running = false;
                        setTimeout(function() {
                            _running = !0;
                        }, 2000);
                    }
                })
                // .addClass("dispn")	
                .setCss({
                    "width": "0px",
                    "height": "0px"
                })
                .addClass("igk-cl-2")
                .setHtml(" ");
            // for animation
            setTimeout(function() {
                var n = q.data.storyboard;
                n.setCss({ width: '100px', height: '100px' })
                    .rpClass("igk-cl-2", "igk-cl-1");
                q.data.render(0, 'transparent', 0);
                igk.html.canva.animate(function(e) {
                    if (q.o.parentNode == null) {
                        // stop animation
                        return !1;
                    }
                    if (!q.data.end) {
                        var x = igk.getNumber(n.getComputedStyle("width"));
                        var y = igk.getNumber(n.getComputedStyle("height"));
                        var cl = n.getComputedStyle('color');
                        _offset += 5; //= (new Date()).getMilliseconds() *180/(1*1000);
                        if (_offset > 360) {
                            _offset = 1;
                        }
                        // console.debug(_offset);
                        q.data.render(
                            Math.round((x / 100.0) * 100) / 100,
                            cl,
                            Math.round((y / 100.0) * 100) / 100
                        );
                        return _running;
                    }
                    q.data.render(1.0, "", 1.0);
                    return _running;
                });
            }, 500);
            return !0;
        }
        // igk.winui.initClassControl
        igk.winui.initClassControl("igk-circle-waiter", __init_waiter);
    })();
    // -----------------------------------------------------------------
    // vertical - horizontal scrollbar
    // -----------------------------------------------------------------
    // obj: vscroll bar
    (function() {
        function _a(p, t) {
            // p: cibling
            // t: target active item in cibling 
            // var m = p.select('a.igk-active').first();
            var m = p.select(t).first();
            var s = p.getscrollParent();
            var y = 0;
            if (m && s) {
                var cH = s.o.clientHeight; // .o.parentNode.parentNode.clientHeight;// p.o.clientHeight;
                var sT = m.o.parentNode.offsetTop;
                if (sT > 0) {
                    if (!(sT < cH)) {
                        y = sT - cH + Math.ceil(igk.getNumber(m.getComputedStyle('height')));
                        p.setCss({ 'transform': 'translateY(-' + (y) + 'px)' });
                    }
                }
                // if (cH < sT){
                // var y =m.o.parentNode.offsetTop ;
                // // p.o.clientHeight - (m.o.parentNode.offsetTop - 
                // // igk.getNumber(m.getComputedStyle('font-size')));
                // if (y>0){
                // // m.setCss({fontSize:"2em"});
                // p.setCss({'transform':'translateY(-'+(y)+'px)'});
                // igk.publisher.publish("sys://doc/changed", {target:p});
                // }
                // }
            }
            return y;
        };

        function __init_scroll(orn) {
            // >param: orn : orientation
            // TODO: scroll in div on touching not yet implement
            var q = this;
            if (q.data["igk-scroll-b"]) {
                throw ("already igk-scroll-b init");
                return;
            }
            // cursor div
            var cur = q.add("div").addClass("igk-scroll-cur");
            var p = $igk(q.o.parentNode);
            var cibling = p.select(this.getAttribute("igk:cibling")).first();
            var m_spos = 0; // start position
            var m_psize = 0;
            if (cibling) {
                m_spos = _a(cibling, q.getAttribute("igk:target"));
            }
            var m_enable = false;
            var m_init = 0;
            q.data["igk-scroll-b"] = 1;
            p.addClass("igk-scroll-host");

            function init_view() {
                var real_size = p.getOffsetBounds();
                // // {x:0, y:0, w:0, h:0};
                // // for(var n = 0; n < p.o.childNodes.length; n++){
                // // var c = $igk( p.o.childNodes[n]);
                // // var loc = c.getScreenLocation();
                // // var boc = c.getScreenBounds();
                // // real_size.x = Math.min(loc.x, real_size.x);
                // // real_size.y = Math.min(loc.y, real_size.y);
                // // real_size.w = Math.max(loc.x + boc.w, real_size.w);
                // // real_size.h = Math.max(loc.y + boc.h, real_size.h);
                // real_size.x = Math.min(loc.x, real_size.x);
                // // }
                // // loc = p.getScreenLocation();
                // // real_size.w -= loc.x;
                // // real_size.h -= loc.y;
                m_psize = real_size;
                // igk.winui.transform.getData(p);
                // 1=> p.o.scrollHeight
                // x=> p.o.offsetHeight;
                var x = 0;
                var s = 0;
                var t = 0;
                // setup size - scroll size
                if (orn == 'h') {
                    x = p.o.offsetWidth / p.o.scrollWidth;
                    s = Math.max(32, (x * p.o.clientWidth));
                    cur.setCss({ width: s + "px" });
                } else {
                    // x = p.o.offsetHeight / p.o.scrollHeight;
                    //x = p.o.offsetHeight / p.o.clientHeight;
                    x = p.o.clientHeight / real_size.h;
                    if (x < 1) { // offset proportion
                        // s = Math.floor(Math.max(32, (x * p.o.clientHeight)));
                        // s = Math.floor(Math.max(32, (x * p.o.offsetHeight)));
                        s = Math.floor(Math.max(32, (x * p.o.clientHeight))); // p.o.offsetHeight)));
                        // var bh = cur.getHeight();
                        cur.setCss({ "Height": s + "px" });
                        // " Height: "+p.getHeight());
                        // p.o.scrollTo(0,200);
                    }
                }
                if (x < 1) {
                    if (!m_init) {
                        // setup marking position
                        // must show the 
                        if (orn == 'v') {
                            // t = Math.floor(p.o.offsetHeight * (m_spos / p.o.scrollHeight));
                            t = Math.floor(p.o.offsetHeight * (m_spos / p.o.clientHeight));
                            cur.setCss({ top: t + 'px' });
                        } else {
                            t = Math.floor(p.o.offsetWidth * (m_spos / p.o.scrollWidth));
                            cur.setCss({ left: t + 'px' });
                        }
                        // __update();
                        m_init = 1;
                    }
                    q.addClass("igk-show");
                    m_enable = 1;
                } else {
                    q.rmClass("igk-show");
                    m_enable = 0;
                    var e = __init_data();
                    e.start = 0;
                    e.end = 0;
                    e.value = 0;
                    // var dir=evt.deltaY > 0? -1: 1;
                    // step :: heigh scroll by 10
                    _u_cur(cur, e, 0);
                }
            };

            function __stop_capture() {
                igk.winui.mouseCapture.releaseCapture();
                cur.data["s:/msdown"] = null;
                cur.data["s:/msprop_s"] = null;
            };

            function __init_data(evt) {
                var x = evt ? (orn == "h" ? evt.clientX : evt.clientY) : 0;
                var f = igk.winui.transform.getData;
                var c = cibling;
                var v = c ? (orn == "h" ? f(c).getX() : f(c).getY()) : 0;
                return {
                    top: cur.getTop(),
                    start: x,
                    end: x,
                    value: v,
                    childs: p.select('>>')
                };
            };

            function _u_cur(cur, e, y) {
                e.end = y;
                e.diff = e.end - e.start;
                var t = e.top + e.diff;
                var maxt = (q.getHeight() - cur.getHeight());
                if (t <= 0)
                    t = 0;
                else
                    t = Math.min(t, maxt);
                var x = (t / maxt);
                // if (e.value == x){
                // return;
                // }
                var d = 0;
                e.value = x;
                if (orn == 'h') {
                    cur.setCss({ "left": t + "px" });
                    d = (-p.o.clientWidth + m_psize.w) * e.value;
                } else {
                    cur.setCss({ "top": t + "px" });
                    d = (-p.o.clientHeight + m_psize.h) * e.value;
                }
                // maxt==> 1
                // t==> x
                // (t / maxt);
                // p.o.clientHeight==>100%
                // p.o.offsetHeight==>x
                // 
                // p.o.offsetHeight * e.value==>x
                // var d = (p.o.scrollHeight -  p.o.offsetHeight) * e.value;
                // var d = (-p.o.clientHeight + m_psize.h) * e.value;
                // var d=Math.min(100,(p.o.offsetHeight * 100) /p.o.clientHeight );
                e.childs.each(function() {
                    if ((this != q) && !this.data["igk-scroll-b"]) {
                        // method 1 if transform support for performance
                        var data = igk.winui.transform.getData(this);
                        data.setTranslateY(-d);
                        this.setCss({ "transform": data.toString() });
                        // method 2 in global
                        // this.setCss({"top":(-d)+"px"});
                    }
                    return !0;
                });
            };

            function __update() {
                var e = __init_data();
                var v = e.value * -1;
                if (!m_init) {
                    e.start = 0;
                    e.end = 0;
                    e.value = 0;
                }
                _u_cur(cur, e, v);
            }
            // igk.android.log.add('is touchable ' + p.istouchable());
            if (p.istouchable()) {
                // support touch
                var tlog = null;
                var vlog = null;
                p.reg_event("touchstart", function(evt) {
                    // igk.android.log.add('touchstart');
                    if (evt.touches.length == 1) {
                        tlog = igk.createNode("div");
                        igk.android.log.add(tlog);
                        vlog = igk.createNode("div");
                        igk.android.log.add(vlog);
                        init_view();
                    }
                }).reg_event("touchend", function(evt) {
                    q.rmClass("igk-show");
                    // igk.android.log.add('touchend');
                }).reg_event("touchmove", function(evt) {
                    var _th = evt.touches[0];
                    // tlog.setHtml("{"+_th.clientX+" x "+_th.clientY+"}");
                    // vlog.setHtml("{"+_th.screenX+" x "+_th.screenY+"}");
                    // igk.android.log.add('touchmove '+evt.touches[0].clientX);
                }).reg_event("touchcancel", function(evt) {
                    // igk.android.log.add('touchcancel');
                });;
            }
            var passive = igk.features.supportPassive ? { passive: true } : false;
            p.reg_event("mouseover", function() {
                init_view();
            }).reg_event("mouseleave", function() {
                if (cur.data["s:/msdown"])
                    return;
                q.rmClass("igk-show");
            }).reg_event("mouseup", function(evt) {
                if (!p.getScreenBounds().contains(evt.clientX, evt.clientY)) {
                    q.rmClass("igk-show");
                }
            }).reg_event("mousewheel", function(evt) {
                // if (!igk.features.supportPassive){
                // console.debug("wheel");
                evt.stopPropagation();
                evt.preventDefault();
                // }
                if (!m_enable)
                    return;
                var e = __init_data();
                e.start = 0;
                e.end = 0;
                e.value = 0;
                var dir = evt.deltaY > 0 ? -1 : 1;
                // step :: heigh scroll by 10
                _u_cur(cur, e, dir * 10);
            }, false).reg_event("scroll", function(evt) {
                evt.stopPropagation();
                evt.preventDefault();
            }, false);
            igk.publisher.register("sys://doc/changed", function(o) {
                if ((o.target == p.o) || igk.dom.isChild(o.target, p.o)) { // __is_parent(o.target, p.o)){
                    // reload scroll
                    __update();
                }
            });
            cur.reg_event("mousedown", function(evt) {
                    if (!cur.data["s:/msdown"]) {
                        igk.winui.mouseCapture.setCapture(cur.o);
                        cur.data["s:/msdown"] = 1;
                        igk.winui.selection.stopselection();
                        var e = __init_data(evt);
                    };
                    if (orn == 'h') {
                        e.start = evt.clientX;
                        e.end = evt.clientX;
                        e.left = cur.getLeft();
                    }
                    cur.data["s:/msprop_s"] = e;
                })
                .reg_event("mousemove", function(evt) {
                    if (cur.data["s:/msdown"]) {
                        if (igk.winui.mouseButton(evt) != igk.winui.mouseButton.Left) {
                            // cause of drag and drop in chrome
                            return;
                        }
                        var e = cur.data["s:/msprop_s"];
                        if (orn == 'v') // check orientation:vertical
                        {
                            _u_cur(cur, e, evt.clientY);
                        } else { // orientation horizontal
                            e.end = evt.clientX;
                            e.diff = e.end - e.start;
                            var t = e.left + e.diff;
                            var maxt = (q.getWidth() - cur.getWidth());
                            if (t < 0)
                                t = 0;
                            else
                                t = Math.min(t, maxt);
                            cur.setCss({ "left": t + "px" });
                            e.value = (t / maxt);
                            var d = (p.o.scrollWidth - p.o.offsetWidth) * e.value;
                            // var d=Math.min(100,(p.o.offsetHeight * 100) /p.o.clientHeight );
                            e.childs.each(function() {
                                if ((this != q) && !this.data["igk-scroll-b"]) {
                                    // method 1 if transform support for performance
                                    var data = igk.winui.transform.getData(this);
                                    data.setTranslateX(-d);
                                    this.setCss({ "transform": data.toString() });
                                    // "translateX("+(-d)+"px)"
                                    // method 2 in global
                                    // this.setCss({"top":(-d)+"px"});						
                                }
                                return !0;
                            });
                        }
                    }
                })
                .reg_event("mouseup", function(evt) {
                    __stop_capture();
                    igk.winui.selection.enableselection();
                });
            // __update();
        }
        igk.winui.initClassControl("igk-vscroll", function() { __init_scroll.apply(this, ['v']); }, { "desc": "vertical scroll bar" });
        igk.winui.initClassControl("igk-hscroll", function() { __init_scroll.apply(this, ['h']); }, { "desc": "horizontal scroll bar" });
    })();
    igk.system.createNS("igk.system", {
        dateTime: function(date) {
            if (!date)
                return null;

            function _dateTimeObj(date) {
                this.date = date;
                igk.appendProperties(this, {
                    format: function(str) {
                        var o = "";
                        for (var i = 0; i < str.length; i++) {
                            var m = str[i];
                            switch (m) {
                                case "m":
                                    o += this.date.getMinutes();
                                    break;
                                case "M":
                                    o += (this.date.getMonth() + 1);
                                    break;
                                case "d":
                                    o += this.date.getDate();
                                    break;
                                case "Y":
                                    o += this.date.getFullYear();
                                    break;
                                case "h":
                                    o += this.date.getHours();
                                    break;
                                case "s":
                                    o += this.date.getSeconds();
                                    break;
                                default:
                                    o += m;
                            }
                        }
                        return o;
                    }
                });
            };
            return new _dateTimeObj(date);
        }
    });
    // used to manage android log
    (function() {
        var slog = null;
        igk.system.createNS("igk.android.log", {
            add: function(m) {
                if (slog == null)
                    return;
                if (typeof(m) == 'string') {
                    var d = new Date();
                    slog.add('div').setHtml(igk.system.dateTime(d).format('d:M:Y') + "=> " + m);
                } else {
                    // igk.android.log.add('reg obj '+typeof(m));
                    slog.add('div').add(m);
                }
            },
            clear: function(m) {
                slog.setHtml('');
            }
        });

        function __initAndroidLog() {
            if (slog) return;
            slog = this;
            igk.android.log.add('start');
        };
        igk.winui.initClassControl("igk-android-log", __initAndroidLog);
    })();
    (function() {
        function __transform_data(s) {
            var m0 = 1,
                m1 = 0,
                m2 = 0,
                m3 = 1,
                tx = 0,
                ty = 0,
                tz = 0;
            var mz = 0;
            var t = '2d';
            if (/^matrix\(/i.exec(s)) {
                // matrix pattern;
                var tb = s.match(/^matrix\((.+)\)$/)[1].split(',');
                m0 = parseFloat(tb[0]);
                m1 = parseFloat(tb[1]);
                m2 = parseFloat(tb[2]);
                m3 = parseFloat(tb[3]);
                tx = parseFloat(tb[4]);
                ty = parseFloat(tb[5]);
            } else if (s == 'none') { // identity
            } else {
                throw "Matrix not found :::: " + s;
            }

            function __get_s() {
                return "matrix(" + m0 + "," + m1 + "," + m2 + "," + m3 + "," + tx + "," + ty + ")";
            };
            igk.appendProperties(this, {
                getX: function() {
                    return tx;
                },
                getY: function() {
                    return ty;
                },
                getz: function() {
                    return tz;
                },
                setTranslateX: function(d) {
                    tx = d;
                },
                setTranslateY: function(d) {
                    ty = d;
                },
                setTranslateZ: function(d) {
                    tz = d;
                },
                setScaleX: function(d) {
                    m0 = d;
                },
                setScaleY: function(d) {
                    m3 = d;
                },
                setScaleZ: function(d) {},
                toString: __get_s
            });
        };
        igk.system.createNS("igk.winui.transform", {
            getData: function(n) {
                var s = $igk(n).getComputedStyle("transform");
                // string type
                return new __transform_data(s || 'none');
            }
        });
    })();
    // winui selection util
    (function() {
        var s = 0;
        var slfunc = 0;

        function __no_select(evt) {
            // onselectstart for document work for ie
            evt.preventDefault();
            evt.stopPropagation();
        };
        var NS = igk.system.createNS("igk.winui.selection", {
            stopselection: function() {
                igk.winui.selection.clear();
                // for global ie
                if (typeof(document.onselectstart) != 'undefined')
                    $igk(document).reg_event('selectstart', __no_select);
                else if (typeof(igk.dom.body().style.MozUserSelect) != 'undefined') // for firefox only
                    igk.dom.body().style.MozUserSelect = 'none';
                s = 1;
            },
            enableselection: function() {
                // for global ie
                if (typeof(document.onselectstart) != 'undefined')
                    $igk(document).unreg_event('selectstart', __no_select);
                else if (typeof(igk.dom.body().style.MozUserSelect) != 'undefined') // for firefox only
                    igk.dom.body().style.MozUserSelect = '';
                // if(typeof(igk.dom.body().style.MozUserSelect) !='undefined')
                // igk.dom.body().style.MozUserSelect='';
                s = 0;
            },
            clear: function() {
                var sel = NS.Selection;
                if (sel) {
                    var fc = sel.removeAllRanges || sel.empty;
                    fc.apply(sel);
                }
            }
        });
        igk.defineProperty(NS, "Selection", {
            get: function() {
                if (!slfunc) {
                    slfunc = window.getSelection ? window.getSelection() : document.selection;
                }
                return slfunc;
            }
        });
    })();
    // 
    (function() {
        // igk-ctrl-options
        igk.winui.initClassControl("igk-ctrl-options", function() {
            // this.setCss({zIndex:800,backgroundColor:'#eee',"position":"absolute","width":"100%"});
        });
    })();
    (function() {
        function __init_card_id() {
            var q = this;
            var _2PI = igk.math._2PI;
            var _src = q.getAttribute('igk:link');
            q.o.removeAttribute('igk:link');
            var _data = {
                img: _src == null ? null : q.add("img").addClass("posab dispn").setAttribute("src", _src).reg_event("load", function(evt) {
                    _data.render();
                    q.o.removeAttribute('igk:link');
                }).reg_event("error", function() {
                    console.error("/!\\ Error on loading image " + _src);
                }),
                storyline: (function() {
                    var s = q.add("div").addClass("posab dispn story-line");
                    var bg = s.add("div").addClass("posab dispn bg");
                    var bdr = s.add("div").addClass("posab dispn bdr");
                    return {
                        "bg": bg,
                        "bdr": bdr
                    };
                })(),
                canva: q.add("canvas"),
                ctx: null,
                render: function(w, h) {
                    if (this.ctx == null) {
                        this.ctx = this.canva.o.getContext('2d');
                    }
                    var w = igk.getNumber(this.canva.getComputedStyle("width"));
                    var h = igk.getNumber(this.canva.getComputedStyle("height"));
                    var v_bdrcl = _data.storyline.bdr.getComputedStyle("color");
                    var v_bg = _data.storyline.bg.getComputedStyle("background-color");
                    var v_bdrs = igk.getNumber(_data.storyline.bdr.getComputedStyle("height")) || 4;
                    var _ctx = this.ctx;
                    var cx = w / 2;
                    var cy = h / 2;
                    var penw = v_bdrs;
                    var minR = Math.min(w / 2, h / 2);
                    var R = minR - (penw / 2);
                    var r = minR - (penw / 2) - 10;
                    this.canva.setAttribute("width", w);
                    this.canva.setAttribute("height", h);
                    if ((R < 0) || (r < 0))
                        return;
                    _ctx.clearRect(0, 0, w, h);
                    _ctx.fillStyle = v_bg;
                    _ctx.arc(cx, cy, R, 0, _2PI, false);
                    _ctx.closePath();
                    _ctx.fill();
                    _ctx.save();
                    // clip to region
                    _ctx.clip();
                    // draw image
                    if (this.img && this.img.o.complete) {
                        var _W = w - 8;
                        var _H = h - 8;
                        var zx = _W / this.img.o.width;
                        var zy = _H / this.img.o.height;
                        var zx = Math.min(zx, zy);
                        _ctx.drawImage(this.img.o, 4 + ((_W - (this.img.o.width * zx)) / 2), 4, this.img.o.width * zx, this.img.o.height * zx);
                        // igk.drawing.effect.stackBlur(_ctx,0,0,w,h,5);
                    }
                    _ctx.restore();
                    _ctx.strokeStyle = v_bdrcl;
                    _ctx.lineWidth = v_bdrs;
                    _ctx.arc(cx, cy, R, 0, _2PI, false);
                    _ctx.closePath();
                    // _ctx.shadowColor='#999';
                    // _ctx.shadowBlur=4;
                    // _ctx.shadowOffsetX=4;
                    // _ctx.shadowOffsetY=4;
                    _ctx.stroke();
                }
            };
            _data.render();
        };
        igk.winui.initClassControl("igk-card-id", __init_card_id, {
            desc: "igk card id"
        });
    })();
    // ------------------------------------------------------------------
    // balafon js component management
    // ------------------------------------------------------------------
    (function() {
        igk.system.createNS("igk.balafonjs", {
            initComponent: function(t) {
                t = t || igk.getParentScript();
                if (!t)
                    return;
                var st = igk.winui.getClassList();
                $igk(t).add("div").setHtml(st);
            }
        });
        igk.ctrl.registerAttribManager("igk-balafonjs", {
            "desc": "inline script to be evaluate."
        });
        // node compopent with balafonjs js javascript
        igk.ctrl.bindAttribManager("igk-balafonjs", function(m, v) {
            if (m)
                eval(v);
        });
    })();
    // ----------------------------------------------------------
    // igk-hpageview  
    // ----------------------------------------------------------
    (function() {
        function __init() {
            var q = this;
            var def = [];
            var v_idx = 0; // store the next value
            var cur = 0; // current visible node
            var m_roles = {};
            var m_autosweep = 0;
            igk.appendProperties(q, {
                movenext: function() {
                    if (v_idx <= def.length - 1) {
                        goTo(def[v_idx]);
                        v_idx++;
                        update_role();
                    }
                },
                moveback: function() {
                    if ((v_idx > 1) && (def.length > 0)) {
                        goTo(def[v_idx - 2]);
                        v_idx--;
                        update_role();
                    }
                }
            });

            function reg_role(t, n) {
                if (!(t in m_roles)) {
                    m_roles[t] = [];
                }
                m_roles[t].push(n);
            };

            function update_role() {
                var t = m_roles["prev"];
                for (var s = 0; s < t.length; s++) {
                    if (v_idx <= 1) {
                        t[s].addClass("dispn");
                    } else {
                        t[s].rmClass("dispn");
                    }
                }
                t = m_roles["next"];
                var s = (def.length > 0) && (v_idx < def.length);
                for (var k = 0; k < t.length; k++) {
                    if (s) {
                        t[k].rmClass("dispn");
                    } else {
                        t[k].addClass("dispn");
                    }
                }
            };
            // function goBack(){
            // 	if(v_idx>0){
            // 		v_idx--;
            // 		q.scrollTo(def[v_idx]);
            // 	}
            // };
            function goTo(o) {
                q.scrollTo(o.o);
                cur = o;
            };
            // object of each view
            // {p: previous node,n: next node,nextCond: next condition to evaluate}
            q.select(">>").each(function() {
                if (this.o.tagName && this.o.tagName.toLowerCase() == "div") {
                    var obj = igk.JSON.parse(this.getAttribute("igk-hpageview-data"));
                    var id = this.getAttribute("id") || ((def.length + 1) + "");
                    def[id] = this;
                    def.push(this);
                    if (!obj) {
                        obj = { p: null, n: null };
                    }
                    if (m_autosweep) {
                        this.reg_event("click", function() {
                            q.movenext();
                            // if(obj.n)
                            // goTo(def[obj.n]);
                            // else if(v_idx<def.length){
                            // goTo(def[v_idx]);
                            // v_idx++;
                            // }
                        });
                    }
                }
                return !0;
            });
            q.getParentNode().select("input").each_all(function() {
                var s = this.getAttribute("igk-hpageview-role");
                if (s) {
                    reg_role(s, this);
                }
            });
            if (def.length > 0)
                v_idx = 1;
            // scroll to the begining
            q.scrollTo(def[0]);
            // igk.winui.reg_event(window,"transitionend", function(evt){
            // // goTo(cur);
            // // }
            // });
            // because of file
            igk.winui.reg_event(window, "resize", function() {
                var b = q.getComputedStyle("transition");
                if (b) {
                    q.setTransition("none");
                }
                setTimeout(function() {
                    if (cur) {
                        goTo(cur);
                    }
                }, 2000);
            });
            update_role();
        };
        igk.winui.initClassControl("igk-hpageview", __init, {
            desc: "igk-pageview control"
        });
    })();
    // -----------------------------------------------------------------------------
    // igk-svg-symbol
    // -----------------------------------------------------------------------------
    (function() {
        var START_ELEMENT = 1;
        var END_ELEMENT = 2;
        var ATTRIBUTE = 3;
        var TEXT = 4;
        var PROCESSOR = 5;
        var IGK_TAGNAME_CHAR_REGEX = /[0-9a-z\-\:_\.]/;

        function _html_domProperty() {
            return {
                createElement: function(t) {
                    return document.createElement(t);
                },
                createTextElement: function(v) {
                    return document.createTextNode(v);
                },
                getNS: function() {
                    // system namespace
                    return null; // igk.dom.body().namespaceURI;
                }
            };
        };
        igk.system.createNS("igk.dom", {
            isChild(o, p) { // echeck if an item [o] is a child of [p]
                while (o && (o != p)) {
                    o = o.parentNode;
                }
                return o != null;
            },
            isParent(s, p) {
                while (s && p && (s.nodeType == 1)) {
                    if ((s = s.parentNode) == p)
                        return 1;
                }
                return 0;
            },
            /**
             * load node's content in  reverse
             * @param {*} n node
             * @param {*} i sibling
             * @param {*} c callback
             */
            loadBeforeReverse(n, i, c) {
                let _c = $igk(n);
                i = $igk(i);
                while (i.o.lastChild) {
                    if (c) c(i.lastChild);
                    _c.o.parentNode.insertBefore(i.o.lastChild, _c.o);
                }
            },
            /**
             * load node's content in order
             * @param {*} n node
             * @param {*} i sibling
             * @param {*} c callback
             */
            loadBefore(n, i, c) {
                let _c = $igk(n);
                i = $igk(i);
                while (i.o.firstChild) {
                    if (c) c(i.o.firstChild);
                    _c.o.parentNode.insertBefore(i.o.firstChild, _c.o);
                }
            },
            loadDocument: function(txt, p) {
                p = p || _html_domProperty();
                // replace all script
                // text = txt.replace(/<script/
                var c = new igk.dom.reader(txt);
                var root = null,
                    i = null,
                    cnode = null;
                var ns = p.getNS();
                var stop = 1;
                while (stop && c.read()) {
                    switch (c.type) {
                        case PROCESSOR:
                            throw "not used";
                            // break;
                        case ATTRIBUTE:
                            for (i in c.attribs) {
                                cnode.setAttribute(i, c.attribs[i]);
                            }
                            break;
                        case TEXT:
                            if (cnode) {
                                if (cnode.childNodes.length == 0) {
                                    cnode.innerHTML = c.value;
                                } else {
                                    cnode.appendChild(p.createTextElement(c.value));
                                }
                            }
                            break;
                        case START_ELEMENT:
                            var m = p.createElement(c.name, c);
                            if (ns) {
                                m.setAttribute("xmlns", ns);
                            }
                            if (m.tagName == "SCRIPT") {
                                c.readScript(m);
                            }
                            if (root == null) {
                                root = m;
                                cnode = root;
                            } else {
                                if (cnode == null) {
                                    cnode = p.createElement("dummy");
                                    cnode.appendChild(root);
                                    root = cnode;
                                    cnode.appendChild(m);
                                    cnode = m;
                                } else {
                                    cnode.appendChild(m);
                                    cnode = m;
                                }
                            }
                            break;
                        case END_ELEMENT:
                            if (cnode)
                                cnode = cnode.parentNode;
                            break;
                        default:
                            console.debug("element ignored " + c.type);
                            break;
                    }
                }
                return root;
            },
            reader: function(txt) {
                    this.txt = txt;
                    this.pos = 0;
                    this.isEmpty = 1;
                    this.hasAttrib = 0;
                    this.attribs = null;
                    var self = this;
                    // private function 
                    function __canRead() {
                        return ((self.pos >= 0) && (self.txt && txt.length >= 0) && (self.pos < self.txt.length));
                    };

                    function __readName() {
                        var v = "";
                        // check reaed cararacter
                        while (__canRead() && IGK_TAGNAME_CHAR_REGEX.exec(self.txt[self.pos])) {
                            v += self.txt[self.pos];
                            self.pos++;
                        }
                        return v;
                    };

                    function __readLine() {
                        var v = "";
                        var v_ch = 0;
                        while (self.pos < self.txt.length) {
                            v_ch = self.txt[self.pos];
                            if (v_ch == "\n") {
                                break;
                            }
                            v += v_ch;
                            self.pos++;
                        }
                        return v;
                    };

                    function __readAttributes() {
                        var v = "";
                        while (__canRead()) {
                            v += self.txt[self.pos];
                            if (v.length > 2 && (v.substring(v.length - 2) == "/>")) {
                                v = v.substring(0, v.length - 2);
                                self.isEmpty = 1;
                                break;
                            } else if ((v.length > 0) && (v.substring(v.length - 1) == ">")) {
                                v = v.substring(0, v.length - 1);
                                self.isEmpty = 0;
                                break;
                            }
                            self.pos++;
                        }
                        return igk.system.string.trim(v);
                    };

                    function __readTo(end) {
                        var v = "";
                        var ln = end.length;
                        while (__canRead()) {
                            v += self.txt[self.pos];
                            if (v.length >= ln && (v.substring(v.length - ln) == end)) {
                                v = v.substring(0, v.length - ln);
                                break;
                            }
                            self.pos++;
                        }
                        return igk.system.string.trim(v);
                    }

                    function __loadAttribs(h) {
                        var s = {};
                        h.replace(igk.regex().attribs, function(m) {
                            var t = m.split('=');
                            var kl = /^("|')/.exec(t[1]) ?
                                t[1].substring(1, t[1].length - 1) : t[1];
                            s[t[0]] = kl;
                        });
                        return s;
                    }
                    igk.appendProperties(this, {
                        "name": null,
                        "value": null,
                        "type": null,
                        read: function() {
                            if (this.type == START_ELEMENT) {
                                var h = __readAttributes();
                                if (h.length > 0) {
                                    this.hasAttrib = 1;
                                    this.type = ATTRIBUTE;
                                    this.attribs = __loadAttribs(h);
                                    return 1;
                                } else if (this.isEmpty) {
                                    this.type = END_ELEMENT;
                                    return 1;
                                }
                            } else if ((this.type == ATTRIBUTE) && (this.isEmpty)) {
                                this.type = END_ELEMENT;
                                return 1;
                            }
                            // var v=0;
                            var v_r = 1,
                                v_enter = 0,
                                v_ch = 0,
                                v_temp;
                            // var v_tmp="";
                            while (__canRead()) {
                                v_r = 0;
                                v_ch = this.txt[this.pos];
                                switch (v_ch) {
                                    case "<": // start tag
                                        v_enter = 1;
                                        break;
                                    case ">":
                                        if ((this.type == ATTRIBUTE) ||
                                            (this.type == START_ELEMENT)) {
                                            // if current type is attribute 
                                            var c_pos = this.pos++;
                                            v_ch = ""; // read text
                                            v_temp = __readTo("<");
                                            if (v_temp.length > 0) {
                                                this.type = TEXT;
                                                this.value = v_temp;
                                                this.isEmpty = false;
                                                this.hasAttrib = false;
                                                return 1;
                                            } else
                                                this.pos = c_pos;
                                        }
                                        break;
                                    case "?": // for processor
                                        if (v_enter) {
                                            v_tmp = __readTo("?>");
                                        }
                                        break;
                                    case "!":
                                        if (v_enter) {
                                            v_tmp = __readTo("-->");
                                        }
                                        break;
                                    case "/": // for end tag
                                        if (v_enter) {
                                            this.pos++;
                                            this.type = END_ELEMENT;
                                            this.name = __readName();
                                            this.value = null;
                                            this.isEmpty = false;
                                            this.hasAttrib = false;
                                            return true;
                                        }
                                        // $v .=$c;
                                        break;
                                    default:
                                        if (v_enter) {
                                            if (v_ch == " ")
                                                throw new Error("empty char not valid");
                                            this.name = __readName();
                                            this.value = null;
                                            this.type = START_ELEMENT;
                                            this.isEmpty = false;
                                            this.hasAttrib = false;
                                            v_enter = 0;
                                            return true;
                                        }
                                        break;
                                }
                                this.pos++;
                            }
                            if (v_r) {
                                return false;
                            }
                            return true;
                        },
                        skip: function() {
                                // igk_wln("ddd ".$this->NodeType);
                                if (this.type == START_ELEMENT) {
                                    if (!_isEmpty) {
                                        $n = this.name.lowerCase();
                                        // igk_wln("is not empty".$n);
                                        var depth = 0;
                                        var end = 0;
                                        while (!end && this.Read()) {
                                            switch (this.type) {
                                                case START_ELEMENT:
                                                    depth++;
                                                    break;
                                                case END_ELEMENT:
                                                    if ((!depth) && (this.name.lowerCase() == n)) {
                                                        end = true;
                                                    } else if (depth > 0)
                                                        depth--;
                                                    break;
                                            }
                                        }
                                        return end;
                                    }
                                }
                                return 0;
                            } // end skip function
                            ,
                        readScript: function(m) {
                            var q = this;
                            var v = "";
                            var i = {};
                            var dx = 0;
                            var v_ch = 0;
                            var attribs = __readAttributes();
                            if (attribs.length > 0) {
                                q.type = ATTRIBUTE;
                                q.attribs = __loadAttribs(attribs);
                                for (i in q.attribs) {
                                    m.setAttribute(i, q.attribs[i]);
                                }
                            }
                            // if (q.read()){
                            // }
                            var e = 0;
                            while (!e && __canRead()) {
                                q.pos++;
                                v_ch = q.txt[q.pos];
                                switch (v_ch) {
                                    case ">":
                                        // start reading
                                        break;
                                    case "<": // detected the closing tag
                                        if (q.txt[q.pos + 1] == "/") {
                                            q.pos += 2;
                                            var n = __readName();
                                            if (n.toLowerCase() == m.tagName.toLowerCase()) {
                                                q.type = END_ELEMENT;
                                                q.value = n;
                                                e = 1;
                                                break;
                                            }
                                        }
                                        v += v_ch;
                                        break;
                                    case "\'":
                                    case '"':
                                        // read string
                                        var spos = q.pos;
                                        while ((dx = q.txt.indexOf(v_ch, q.pos + 1)) != -1) {
                                            if (dx > 0) {
                                                if (q.txt[dx - 1] == "\\") // escaped{
                                                {
                                                    q.pos = dx;
                                                    continue;
                                                }
                                            }
                                            break;
                                        }
                                        if (dx > spos) {
                                            v += q.txt.substr(spos, dx - spos + 1);
                                            q.pos = dx;
                                        }
                                        break;
                                    case "/": // detect comment
                                        if (q.txt.length - 1 > q.pos) {
                                            switch (q.txt[q.pos + 1]) {
                                                case "/":
                                                    // read line
                                                    v += __readLine();
                                                    break;
                                                case "*":
                                                    //TODO :::: RM Comment
                                                    dx = q.txt.indexOf("*\/", q.pos + 2);
                                                    v += "\/*" + q.txt.substr(q.pos, dx - q.pos);
                                                    q.pos = dx + 1;
                                                    break;
                                            }
                                        }
                                        break;
                                    default:
                                        v += v_ch;
                                        break;
                                }
                            }
                            m.innerText = v;
                            console.debug("done :" + v);
                        }
                    }); // end append prop
                } // end reader
                ,
            childto_array: function(n) {
                    var t = $igk(n).o.childNodes;
                    var o = [];
                    if (t) {
                        for (var s = 0; s < t.length; s++) {
                            o.push(t[s]);
                        }
                    }
                    return o;
                }
                // end igk.dom namespace declaration	
        });
        // end create document
    })();
    (function() {
        var m_symbols = [];
        var m_noloads = [];
        var m_keyloads = {};
        var m_ns = igk.namespaces.svg; // "http://www.w3.org/2000/svg";
        function __loadSvg(text) {
            return igk.dom.loadDocument(text, {
                createElement: function(tag) {
                    return document.createElementNS(m_ns, tag);
                },
                getNS: function() { return m_ns; }
            });
        };

        function __init() {
            var n = this.getAttribute("igk:svg-name");
            var g = m_symbols[n];
            if (g) {
                var c = g.clone();
                // append attribute to node class
                c.setAttribute("class", this.getAttribute("class"));
                // c.setAttribute("fill","red");
                this.o.parentNode.replaceChild(c.o, this.o);
            } else {
                m_noloads.push(this);
                var kobj = { name: n, t: this };
                if (!m_keyloads[n])
                    m_keyloads[n] = [];
                m_keyloads[n].push(kobj);
            }
        };

        function __initsymbol() {
            var n = this.getAttribute("id");
            if (!m_symbols[n]) {
                // if egdge for xbox one load outer document
                var ot = this.o.outerHTML;
                var doc = ot ? $igk(__loadSvg(ot)) : null;
                if (!doc) {
                    // if other 
                    m_symbols.push(this);
                    m_symbols[n] = this;
                } else {
                    m_symbols.push(doc);
                    m_symbols[n] = doc;
                }
                // doc.setAttribute("class",this.getAttribute("class"));
                if (m_keyloads[n]) {
                    for (var ii = 0; ii < m_keyloads[n].length; ii++) {
                        var t = m_keyloads[n][ii].t;
                        // debug=1;
                        __init.apply(t);
                        // debug=0;
                    }
                    m_keyloads[n] = null;
                    delete(m_keyloads[n]);
                }
            }
        };

        function __loadSymbols(f) {
            if (igk.getSettings().nosymbol) {
                // because of no symbol required IE specification 
                return;
            }
            igk.io.file.load(f, function(d) {
                if (typeof(d.data) != "string") {
                    if (d.error) {
                        return;
                    }
                }
                var d = igk.utils.getBodyContent(d.data);
                var q = igk.createNode("div").setHtml(d); //d.data);
                var svg_c = 0;
                var t = q.select(">>").each(function() {
                    if (this.o.tagName) {
                        switch (this.o.tagName.toLowerCase()) {
                            case "svgs":
                                // load multiple svg document
                                var m = this;
                                // select child svg
                                m.select(">> svg").each_all(function() {
                                    this.fn.svg = f;
                                    __initsymbol.apply(this);
                                });
                                break;
                            case "svg":
                                // load single document
                                __initsymbol.apply(this);
                                svg_c++;
                                break;
                        }
                    }
                    return !0;
                });
                igk.publisher.publish("sys://svg/lds", { target: this, file: f, count: svg_c, selector: t }); // loaded symbol
            });
        };
        igk.system.createNS("igk.svg", {
            /**
             * use svg symbol
             * @param {} n 
             * @returns 
             */
            useSvg(n) {
                let d = $igk.createNode('div');
                d.addClass("igk-svg-lst-i");
                d.setAttribute("igk:svg-name", n);
                return d;
            },
            loadSymbols: __loadSymbols,
            append: function(n, t) {
                var sv = igk.svg.newSvgContainer(n);
                t.add(sv);
                sv.init();
            },
            getLoadedKey: function() {
                var t = [];
                for (var i = 0; i < m_symbols.length; i++) {
                    t.push(m_symbols[i].getAttribute("id"));
                }
                return t;
            },
            newSvgContainer: function(n) { // create new igk:svgSymbol block container
                var q = igk.createNode("div");
                q.setAttribute('igk:svg-name', n).addClass("igk-svg-symbol " + n);
                return q;
            },
            newSvgDocument: function() {
                //return a new svg document
                return $igk(document.createElementNS(igk.namespaces.svg, 'svg')).setAttributes({
                    version: '1.1'
                });
            }
        });
        igk.winui.initClassControl("igk-svg-symbol", __init, {
            desc: "igk-svg-symbol item. get a symbol from a svg document files and render it to content"
        });
        // firefox load css before raise document ready
        // firefox allow creation of node reference without adding it to document
        var n = igk.createNode("div");
        n.addClass("igk-svg-symbol-lists");
        // var lst=n.getComputedStyle("content",":before").replace(/"/g,'').split(',');
        igk.ready(function() {
            if (igk.getSettings().nosymbol) {
                // because of no symbol required IE specification 
                return;
            }
            // add to body required to get content
            igk.dom.body().appendChild(n.o);
            var h = n.getComputedStyle("content", ":before");
            if (h && (h != 'none')) {
                var lst = h.replace(/"/g, '').split(',');
                for (var i = 0; i < lst.length; i++) {
                    igk.svg.loadSymbols(lst[i]);
                }
            }
            n.remove();
        });
    })();
    // ---------------------------------------------------------------------------------------------------	
    // ---------------------------------------------------------------------------------------------------	
    (function() {
        function __init() {
            igk.initpowered(this.o);
        }
        igk.winui.initClassControl("igk-powered", __init, {
            desc: "manage powered message"
        });
    })();
    // ---------------------------------------------------------------------------------------------------	
    // igk-comm-lnk
    // ---------------------------------------------------------------------------------------------------	
    (function() {
        function __init() {
            var hr = this.getAttribute("href");
            var n = this.getAttribute("igk:title");
            this.reg_event("click", function() {
                window.open(hr);
            });
            // 
            this.add(igk.svg.newSvgContainer(n));
        }
        igk.winui.initClassControl("igk-comm-lnk", __init, {
            desc: "represent a communication link node. image will be by default a igk-svg-symbol"
        });
    })();
    // ---------------------------------------------------------------------------------------------------	
    // igk-page : constrol that will bind page verticaliy according to screen size. but resize ::: 
    // ---------------------------------------------------------------------------------------------------	
    (function() {
        var m_pages = [];

        function __init() {
            m_pages.push(this);
            var s = igk.winui.screenSize();
            var w = s.width;
            var h = s.height;
            _update_i(this, w, h);
        };

        function _update_i(k, w, h) {
            // var m=k.getComputedStyle("height");
            k.setCss({ height: "auto" });
            if (k.o.offsetHeight < h) {
                k.setCss({
                    height: h + "px"
                });
            } else {
                k.setCss({
                    height: k.o.offsetHeight + "px"
                });
            }
            if (k.o.offsetWidth < w) {}
        }

        function __update() {
            var s = igk.winui.screenSize();
            var w = s.width;
            var h = s.height;
            for (var i = 0; i < m_pages.length; i++) {
                var k = m_pages[i];
                _update_i(k, w, h);
            }
        }
        var tmout = 0;
        igk.winui.initClassControl("igk-page", __init, {
            desc: "page management"
        });
        igk.winui.reg_event(window, "resize", function() {
            clearTimeout(tmout);
            if (m_pages.length > 0) {
                tmout = setTimeout(function() {
                    __update();
                }, 500);
            }
        });
    })();
    (function() {
        // 
        // button used to show dialog
        // 
        igk.winui.initClassControl("igk-js-btn-show-dialog", function() {
            var _id = this.getAttribute("igk:dialog-id");
            if (_id) {
                this.reg_event("click", function() {
                    ns_igk.winui.showDialog(_id);
                    return !1;
                });
            }
        }, {
            desc: "JS Component to show inline ms dialog button"
        });
    })();
    (function() {
        // 
        // input regex entrey
        // 
        function __init_input_regex(ctx) {
            if (!this.o.tagName || (this.o.tagName.toLowerCase() != 'input') || !/(text|password|email)/i.exec(this.o.type))
                return;
            var q = this;
            var def = "sys://control/inputregex";
            if (q.data[def]) {
                return !1;
            }
            q.data[def] = 1;
            q.data[def + "/class"] = ctx;
            var _rg = this.getAttribute("igk:char-regex"); // to validate input entry
            var _rg_opt = this.getAttribute("igk:char-regex-option") || "i"; // options for char input entry
            var _ig = this.getAttribute("igk:input-regex"); // to validate the data
            var _ig_opt = this.getAttribute("igk:input-regex-options") || "i"; // to validate the data
            var _e_msg = this.getAttribute("igk:error-msg");
            var _e_id_msg = this.getAttribute("igk:error-target-id");
            var _ms_n = null; // message node
            if (_ig) {
                _ig = new RegExp("^" + _ig + "$", _ig_opt);
                this.reg_event("change", function() {
                    q.igkCheckIsInvalid();
                });
                q.igkIsInvalid = function() {
                    return _ig.exec(q.o.value);
                };
                q.igkCheckIsInvalid = function() {
                    if (_ms_n) {
                        $igk(_ms_n).remove();
                        _ms_n = null;
                    }
                    if (q.igkIsInvalid()) {
                        q.rmClass("igk-invalid");
                        return !1;
                    } else {
                        q.addClass("igk-invalid");
                        _ms_n = igk.createNode('div');
                        _ms_n.addClass("igk-e-msg");
                        if (_e_id_msg) {
                            var _st = $igk(_e_id_msg);
                            if (_st) {
                                _ms_n.setHtml(_st.getHtml());
                            }
                        } else {
                            if (_e_msg) {
                                _ms_n.setHtml(_e_msg);
                            }
                        }
                        q.insertAfter(_ms_n.o);
                        return !0;
                    }
                };
            }
            if (!_rg)
                return;
            var _emsg = this.getAttribute("igk:error-msg");
            // if(_emsg)
            // this.o.setCustomValidity(_emsg);
            _rg = new RegExp(_rg, _rg_opt);
            this.reg_event("invalid", function() {
                q.o.setCustomValidity(_emsg);
            });
            this.reg_event("keypress", function(evt) {
                evt.stopPropagation();
                var c = evt.charCode || evt.keyCode;
                var ctrlkey = evt.ctrlKey;
                var altKey = evt.altKey;
                var ch = evt.char || evt.key; // get char expression ie vs modzilla
                // igk.show_prop(evt);	
                // if(!ctrlkey && !altKey &&(c > 31)&&(evt.key.length==1) && !_rg.exec(ch))
                // evt.preventDefault();
                if (!ctrlkey && !altKey && (c > 31) && !_rg.exec(ch))
                    evt.preventDefault();
                return !1;
            });
            // this.reg_event("keyup",function(evt){
            // evt.stopPropagation();
            // evt.preventDefault();
            // return !1;
            // });
            this.reg_event("input", function() {
                q.o.setCustomValidity("");
            });
            // this.reg_event("change",function(evt){
            // evt.preventDefault();
            // });
            // protect for paste data
            this.reg_event("paste", function(evt) {
                if (evt.clipboardData.types <= 0)
                    return;
                // var t=evt.clipboardData.types[0];
                // igk.show_prop(evt.clipboardData.types);
                var d = evt.clipboardData.getData("text/plain");
                for (var i = 0; i < d.length; i++) {
                    if (!_rg.exec(d[i])) {
                        evt.stopPropagation();
                        evt.preventDefault();
                        break;
                    }
                }
                return !1;
            });
            if (q.o.value)
                q.igkCheckIsInvalid();
            return !0;
        }
        igk.ctrl.registerAttribManager("igk-input-regex", {
            desc: "used to validate inform balafon js to validate data",
            ns: 'validation'
        });
        igk.ctrl.bindAttribManager("igk-input-regex", function(a, v) {
            // init binding input regex for item
            // a: attribute
            // v: value
            if (!a || !v) {
                return;
            }
            __init_input_regex.apply(this, ['attrib']);
        });
        igk.winui.initClassControl("igk-input-regex", function() {
            if (!this.o.tagName || (this.o.tagName.toLowerCase() != 'input'))
                return;
            __init_input_regex.apply(this, ['class']);
        }, {
            desc: "JS input-regex component"
        });
    })();
    // ------------------------------------------------------------------------------------------------------------
    // bind igk-input-data
    // ------------------------------------------------------------------------------------------------------------
    (function() {
        // input data must have {regex=string,maxlength=-1|int,casesensitive=0,validate=func,update=func}
        function inputdata(target, b) {
            this.target = target;
            this.options = b;
            var a = $igk(target);
            var self = this;

            function __updatecal() {
                if (self.options.update)
                    self.options.update(a.o.value);
            }

            function __removeSelectedChars(target) {
                var h = (target.value + "");
                var i = target.selectionStart;
                target.value = igk.system.string.remove(h, target.selectionStart, target.selectionEnd - target.selectionStart);
                target.selectionStart = i;
                target.selectionEnd = i;
            }
            a.reg_event("keypress", function(evt) {
                if (evt.key) {
                    // for number
                    if (evt.charCode > 0) {
                        var old = "" + evt.target.value;
                        var p = null;
                        var v_update = false;
                        if (evt.target.selectionStart == evt.target.value.length) {
                            old = old + "" + evt.key;
                        } else {
                            p = evt.target.selectionStart;
                            if (p != evt.target.selectionEnd) {
                                var m = igk.system.string.remove(old, p, evt.target.selectionEnd - p);
                                old = igk.system.string.insert(m, p, evt.key) // igk.system.string.remove(old,p,evt.target.selectionEnd-p)  igk.system.string.remove(old,p,evt.target.selectionEnd-p));
                            } else
                                old = igk.system.string.insert(old, p, evt.key);
                            p++;
                        }
                        if (new RegExp(self.options.regex).exec(old)) // new RegExp(""+self.options.regex+"","i").exec(old))
                        {
                            if (old == "-")
                                evt.target.value = "-";
                            else {
                                evt.target.value = old;
                            }
                            v_update = !0;
                        }
                        if (v_update && (p != null)) {
                            evt.target.selectionStart = p;
                            evt.target.selectionEnd = p;
                        }
                        __updatecal();
                        evt.preventDefault();
                    } else {
                        if (!(evt.keyCode > 0)) {
                            evt.preventDefault();
                        } else {
                            var s = evt.getPreventDefault();
                            if (!s) {
                                var i = evt.target.selectionStart;
                                var h = (evt.target.value + "");
                                switch (evt.keyCode) {
                                    case 8: // back
                                        if (i >= 0) {
                                            if (h.length == i) {
                                                evt.target.value = h.substring(0, i - 1);
                                            } else {
                                                if (i == evt.target.selectionEnd) {
                                                    evt.target.value = igk.system.string.remove(h, i - 1, 1);
                                                    evt.target.selectionStart = i - 1;
                                                    evt.target.selectionEnd = i - 1;
                                                } else {
                                                    __removeSelectedChars(evt.target);
                                                }
                                                // igk.show_prop(evt.target); 
                                            }
                                            __updatecal();
                                        }
                                        evt.preventDefault();
                                        break;
                                    case 46: // delete
                                        if ((evt.target.selectionStart == evt.target.selectionEnd) && (i < h.length)) {
                                            evt.target.value = igk.system.string.remove(h, i, 1);
                                            evt.target.selectionStart = i;
                                            evt.target.selectionEnd = i;
                                        } else if (i != evt.target.selectionEnd) {
                                            // remove value
                                            __removeSelectedChars(evt.target);
                                        }
                                        __updatecal();
                                        evt.preventDefault();
                                        break;
                                }
                            }
                        }
                    }
                }
                return !1;
            });
        }

        function __init() {
            // define some constant
            // var NUMBER_REGEX=/^(-)?([0-9]+)(\.[0-9]*)?$/;
            // var INT_REGEX=/^(-)?([0-9]+)$/;		
            var s = this.getAttribute("igk-input-data");
            var b = null;
            b = eval("(" + s + ")");
            if (b && b.regex) {
                new inputdata(this, b);
            }
        }
        igk.ctrl.bindAttribManager("igk-input-data", __init);
    })();
    // form a button
    (function() {
        igk.winui.initClassControl("igk-form-sbtn", function() {
            var q = this;
            if (q.o.tagName.toLowerCase() == "a") {
                var f = q.getAttribute("href");
                var frm = q.getParentByTagName("form");
                if (frm) {
                    q.reg_event("click", function(evt) {
                        evt.stopPropagation();
                        evt.preventDefault();
                        if ((f == '#') || (f == '')) {
                            switch (q.getAttribute("igk:rel")) {
                                case "rel":
                                default:
                                    frm.reset();
                                    break;
                            }
                        } else {
                            frm["action"] = f;
                            frm.submit();
                        }
                        return !1;
                    });
                }
            }
        });
        igk.winui.initClassControl("igk-form-sbtn-ajx", function() {
            var q = this;
            if (q.o.tagName.toLowerCase() == "a") {
                var f = q.getAttribute("href");
                var c = 0;
                q.reg_event("click", function(evt) {
                    evt.stopPropagation();
                    evt.preventDefault();
                    if (c == 0) {
                        c = 1;
                        igk.ajx.post(f, null, function() {
                            if (this.isReady()) {
                                igk.ajx.fn.replace_or_append_to_body.apply(this, arguments);
                                c = 0;
                            }
                        });
                    }
                    return !1;
                });
            }
        });
    })();
    // ajx button pickfile
    (function() {
        igk.winui.initClassControl("igk-ajx-pickfile", function(
            // 	n,m,o
        ) {
            var q = this;
            var u = this.getAttribute("igk:uri");
            if (u == null) {
                console.error("/!\\ igk-ajx-pickfile: no uri found found ");
                return;
            }
            var s = this.getAttribute("igk:data");
            var p = igk.JSON.parse(s, this);
            if (typeof(p) != 'object') {
                p = {};
            }
            if (q.o.tagName.toLowerCase() == "input") {
                var n = igk.dom.replaceTagWith(q, "div");
                var v = q.getAttribute("value");
                n.setHtml(v ? v : q.getHtml());
                n.o.removeAttribute("value");
                n.o.removeAttribute("type");
                n.o.removeAttribute("igk:uri");
                n.o.removeAttribute("igk:data");
                n.o.removeAttribute("igk:uri");
                q = n;
            }
            if (q.o.classList.contains('btn') !== 1) {

                if (q.o.nextSibling && (q.o.nextSibling.nodeType == q.o.TEXT_NODE)) {
                    q.add('div').setHtml(q.o.nextSibling.textContent);
                    $igk(q.o.nextSibling).remove();
                }
            }
            q.reg_event('click', function(evt) {
                evt.preventDefault();
                evt.stopPropagation();
                var cp = {};
                cp.type = p && p.type && p.type.exec("(p|l)") ? p.type : 'p';
                cp.complete = (p ? p.complete : null) || function(s) {
                    if (this.isReady()) {
                        if (p && p.complete) {
                            p.complete.apply(this, [s, q]);
                        } else {
                            this.source = q;
                            igk.ajx.fn.replace_or_append_to_body.apply(this, arguments);
                        }
                        if (_pl) {
                            _pl.each_all(function() {
                                this.setHtml('');
                            });
                        }
                        _pl = null;
                    }
                };
                var _pl = null;
                cp.progress = (p ? p.progress : null) || function(e) {
                    if (!_pl)
                        _pl = $igk("#igk-pickfile-progress");
                    _pl.each_all(function() {
                        switch (cp.type) {
                            case "p":
                                this.setHtml(Math.round((e.loaded / e.total) * 100) + "%");
                                break;
                            default:
                                this.setHtml(e.loaded + " / " + e.total);
                                break;
                        }
                        // this.setHtml(e);
                    });
                };
                cp.start = p ? p.start : null;
                cp.accept = p ? p.accept : null;
                // console.debug(cp);
                igk.system.io.pickfile(u, cp);
            });
        }, {
            desc: "Used to pick file in ajx context"
        });
    })();
    // XML stransform node
    (function() {
        igk.winui.initClassControl("igk-xsl-node", function(
            // 	o,m
        ) {
            // var attr = igk.winui.ClassRequireAttribute.apply(this, "igk:xslt-data");
            var u = igk.JSON.parse(this.getAttribute("igk:xslt-data"));
            if (u) {
                var q = this;
                igk.ready(function() {
                    igk.dom.transformXSLUri(u.xml, u.xsl, function(d) {
                        if (u.target) {
                            var s = $igk(d).select(u.target);
                            var t = s && (s.o.length == 1) && s.first();
                            if (t) {
                                q.setHtml(t.o.innerHTML);
                                // init childs node
                                q.select('>>').each_all(function() { igk.ajx.fn.initnode(this.o); });
                                igk.publisher.publish("sys://doc/changed", igk.publisher.createEventData());
                                return;
                            }
                        }
                        q.o.appendChild(d);
                        igk.publisher.publish("sys://doc/changed", igk.publisher.createEventData());
                    });
                });
            }
        }, {
            desc: 'used no build xsl node',
            create: function(xml, xsl) {
                if ((!xml) || (!xsl)) {
                    throw ('Arguments not valid');
                }
                var n = igk.createNode('div');
                n.addClass("igk-xsl-node");
                n.setAttribute("igk:xslt-data", igk.JSON.convertToString({ xml: xml, xsl: xsl }));
                return n;
            }
        });
    })();
    (function() {
        function __update(v) {
            var q = this;
            var w = igk.getNumber(q.getComputedStyle("width"));
            var h = igk.getNumber(q.getComputedStyle("height"));
            // w , h , can be used in the expression because of the eval
            if (w && h) {
                var exp = eval('obj=' + v + ';');
                this.setCss(exp);
            }
        }
        // fix position
        function __init(n, v) {
            if (!n) {}
            var q = this;
            var g = null;

            function __runfix() {
                __update.apply(q, [v]);
            };
            igk.appendProperties(q, {
                "fix": {
                    "update": __runfix
                }
            });
            // igk.ready(function(){__update.apply(q,[v])},0);
            igk.winui.reg_event(window, "resize", g = function() {
                __update.apply(q, [v]);
            });
            q.fix.update();
            igk.publisher.register("sys://html/doc/scroll", __runfix);
        }
        igk.ctrl.registerAttribManager("igk-attr-fix-position", { desc: 'used to correct element position according to expression' });
        igk.ctrl.bindAttribManager("igk-attr-fix-position", __init);
    })();
    // AJX URI LOADER
    (function() {
        igk.system.createNS("igk.thread", {
            wait: function(t, tg, fc) { // used to as sync an action
                if (t <= 0) {
                    fc.apply(tg);
                    return;
                }
                var s = 1;
                // var m=100;
                function _th_wait_fc() {
                    if (s)
                        s = 0;
                    fc.apply(tg);
                };
                setTimeout(_th_wait_fc, t);
            }
        });
        igk.winui.initClassControl("igk-ajx-uri-loader", function() {
            var u = this.getAttribute("igk:href");
            var a = this.getAttribute("igk:append");
            var self = this;
            if (u) {
                var q = $igk(this.o.parentNode);
                var ta = q.select(a).first();
                igk.ajx.get(u, null, function(xhr) {
                    if (this.isReady()) {
                        var x = this;
                        this.source = q;
                        igk.thread.wait(0, xhr, function() {
                            if (a) {
                                if (ta) {
                                    igk.ajx.fn.replace_or_append_to(ta).apply(x, [xhr]);
                                } else
                                    igk.ajx.fn.replace_or_append_to(q).apply(x, [xhr]);
                            } else {
                                q.setHtml(xhr.responseText).init();
                            }
                            // lw.remove();
                            self.remove();
                        });
                    } else if (xhr.readyState == 4) {
                        // lw.remove();
                        self.remove();
                    }
                }, true);
            }
        }, {
            desc: 'used load uri content in ajx context'
        });
    })();
    // (function(){
    // var sm_shader=null;
    // var mg_currentProgram=0;
    // igk.system.createNS("igk.html5.drawing.webgl",{
    // shader: function(){// singleton shader object
    // if(sm_shader){
    // return sm_shader;
    // }
    // if(this instanceof igk.object){	
    // return  new igk.html5.drawing.webgl.shader();
    // }
    // igk.appendProperties(this,{
    // loadAndCompile: function(gl,arraylistVShader,arraylistFShader){
    // var p=null; // program data
    // return null if failed
    // var vshader=[];
    // var fshader=[];
    // var vertexShader=null;// gl.createShader(gl.VERTEX_SHADER);
    // var fragmentShader=null;// gl.createShader(gl.FRAGMENT_SHADER);
    // gl.shaderSource(vertexShader,vertextShaderText);
    // gl.shaderSource(fragmentShader,fragShaderText);
    // gl.compileShader(vertexShader);
    // if(!gl.getShaderParameter(vertexShader,gl.COMPILE_STATUS)){
    // return;
    // }
    // gl.compileShader(fragmentShader);
    // if(!gl.getShaderParameter(fragmentShader,gl.COMPILE_STATUS)){
    // return;
    // }
    // var program=gl.createProgram();
    // var v_str="";
    // for(var i=0; i < arraylistVShader.length; i++){
    // v_str=arraylistVShader[i];
    // vertexShader=gl.createShader(gl.VERTEX_SHADER);
    // gl.shaderSource(vertexShader,v_str);
    // gl.compileShader(vertexShader);
    // if(!gl.getShaderParameter(vertexShader,gl.COMPILE_STATUS)){
    // return;
    // }
    // vshader[i]=vertexShader;
    // gl.attachShader(program,vertexShader);
    // }
    // for(var i=0; i < arraylistFShader.length; i++){
    // v_str=arraylistFShader[i];
    // fragmentShader=gl.createShader(gl.FRAGMENT_SHADER);
    // gl.shaderSource(fragmentShader,v_str);
    // gl.compileShader(fragmentShader);
    // if(!gl.getShaderParameter(fragmentShader,gl.COMPILE_STATUS)){
    // return;
    // }
    // fshader[i]=fragmentShader;
    // gl.attachShader(program,fragmentShader);
    // }
    // link program
    // gl.linkProgram(program);
    // if(!gl.getProgramParameter(program,gl.LINK_STATUS)){
    // return;
    // }
    // gl.validateProgram(program);
    // if(!gl.getProgramParameter(program,gl.VALIDATE_STATUS)){
    // return;
    // }
    // p=new function(){
    // var m_gl=0;
    // igk.appendProperties(this,{
    // program: program,
    // vertextShaders:vshader,
    // fragmentShaders:fshader,
    // useIt:function(gl){			
    // gl.useProgram(this.program);
    // if(mg_currentProgram!=this){
    // m_gl=0;
    // }else
    // m_gl=gl;
    // },
    // setUniform:function(name,v){
    // if(!m_gl)
    // return;
    // }
    // });
    // };
    // return p;
    // },
    // toString:function(){return "igk.html5.drawing.webgl.shader"; }
    // });
    // sm_shader=this;
    // return sm_shader;
    // }
    // });
    // })();
    // --------------------------------------------------------------------------------------------------------
    // HTML5
    // --------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------
    // AUDIO CONTEXT
    // --------------------------------------------------------------------------------------------------------
    (function() {
        igk.system.createNS("igk.html5", {
            audioContext: window.AudioContext || window.webkitAudioContext,
        });
        var _no_context = "no-audio-context";
        igk.system.createNS("igk.html5.audioBuilder", {
            getComponents: function() {
                if (!igk.html5.audioContext)
                    return _no_context;
                var s = new igk.html5.audioContext();
                var i = 0;
                var o = [];
                var m = "";
                if (s) {
                    for (var a in s) {
                        if (typeof(s[a]) == "function" && /^create/.test(a)) {
                            if (i)
                                m += ",";
                            m += a.substring(6);
                            i = 1;
                        }
                    }
                }
                return m;
            },
            getComponentsDescriptionFile: function() {
                function _getargs(nn) {
                    var args = {
                        "Buffer": [2, 20500, 41100],
                        "PeriodicWave": [new Float32Array(3), new Float32Array(3)],
                        "MediaStreamSource": [null],
                        "MediaElementSource": [igk.createNode("audio")]
                    };
                    if (nn in args)
                        return args[nn];
                    return [];
                };
                var r = igk.html5.audioBuilder.getComponents();
                if (r == _no_context) return;
                var s = new igk.html5.audioContext();
                var t = r.split(',');
                var m = igk.createNode("webaudiodef");
                var k = null;
                for (var i = 0; i < t.length; i++) {
                    k = m.add("node");
                    k.setAttribute("Name", t[i]);
                    k.setAttribute("desc", "");
                    // load properties
                    try {
                        var d = s["create" + t[i]].apply(s, _getargs(t[i]));
                        for (var jj in d) {
                            k.add("property").setAttribute("name", jj);
                        }
                    } catch (e) {
                        console.debug("error " + e);
                    }
                }
                igk.io.file.download("application/xml", "data.xml", m.render());
                return 1;
            }
        });
    })();
    //----------------------------------------------------------------------------------------------------------
    // WEBGLContext
    //----------------------------------------------------------------------------------------------------------
    (function() {
        igk.system.createNS("igk.html5", {
            freeWebGLContext: function(gl) { // free webgl context
                gl = gl || m_gl;
                if (gl) {
                    c = gl.getExtension('WEBGL_lose_context');
                    if (c) { c.loseContext(); }
                }
                if (m_gl == gl)
                    m_gl = null;
            },
            createWebGLContext: function(c) {
                return c.getContext("webgl") || c.getContext('experimental-webgl');
            }
        });
    })();
    // --------------------------------------------------------------------------------------------------------
    // HTML5 GAME SURFACE
    // --------------------------------------------------------------------------------------------------------
    (function() {
        var m_contexts = [];
        var _params = null;
        var m_gl = 0;
        var _def = 0;
        var _pause = 0;
        // var _log =0;
        function _Run(ol) {
            // external game listener;
            var gl = ol.gl;
            var fc = igk.animation.getAnimationFrame();
            var e = 0;
            var loop = function() {
                if (!_pause) {
                    ol.listener.tick(gl);
                    e = gl.getError();
                    if (e) {
                        throw "render failed cause of an error " + e;
                    }
                }
                fc(loop);
            };
            fc(loop);
        };
        igk.system.createNS("igk.html5.drawing", {
            getContext: function() {
                return m_contexts;
            },
            gameContextListener: function() {
                // base class of game rendering context
                var _p_data = _params;
                var _canvas = _p_data.canvas;
                var _self = this;
                var _p_settings = null;
                $igk(_canvas).addClass("igk-game-surface");
                igk.winui.reg_event(window, 'resize', function() {
                    _self.updateSize(_p_data.gl);
                    _self.raise("sizeChanged");
                });
                _params = null;
                igk.appendProperties(this, {
                    toString: function() {
                        return "[class: igk.html5.drawing.gameContextListener]";
                    },
                    raise: function(n) { // raiseEvent:  game Component event
                        var e = { target: this };
                        if (arguments.length > 1) {
                            e.param = arguments[1];
                        }
                        igk.publisher.publish("igk.bge://" + n, e);
                        return this;
                    },
                    on: function(n, callback) {
                        igk.publisher.register("igk.bge://" + n, callback);
                        return this;
                    },
                    setBgColor: function(bg) {
                        _bgCl = bg;
                    },
                    // gl function				
                    initContext: function(gl) {},
                    initGame: function(gl) {
                        // initGame
                    },
                    render: function(gl) {
                        gl.clearColor(_bgCl.r, _bgCl.g, _bgCl.b, _bgCl.a); // 											
                        gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
                    },
                    updateWorld: function(gl) {},
                    loadContent: function(gl) {},
                    unloadContent: function(gl) {},
                    updateSize: function(gl) {},
                    tick: function(gl) {
                        this.updateWorld(gl);
                        this.render(gl);
                    },
                    capture: function(u, w, h) {
                        _pause = 1;
                        var c = $igk(this.canvas).getParent().select(".scene").first();
                        var v_bck = $igk(this.canvas).o.style;
                        $igk(this.canvas).setCss({ position: "fixed" });
                        this.updateSize(_def.gl, w, h);
                        this.tick(_def.gl);
                        var i = this.canvas.toDataURL();
                        this.updateSize(_def.gl, null, null);
                        $igk(this.canvas).o.style = v_bck; // restore the previous style setting					
                        _pause = 0; // remove pause				
                        if (u) {
                            igk.ajx.post(u, "data=" + i, igk.ajx.fn.none);
                        }
                        return i;
                    },
                    fullscreen: function() {
                        var c = $igk(this.canvas);
                        var fc = igk.fn.getItemFunc(c.o, "requestFullScreen");
                        if (fc) {
                            this.raise("fullsizeRequest");
                            fc.apply(c.o);
                        }
                    }
                });
                igk.defineProperty(this, "canvas", { get: function() { return _canvas; } }); // get canvas sources
                igk.defineProperty(this, "gl", { get: function() { return _p_data.gl; } }); // get gl context
                igk.defineProperty(this, "settings", { get: function() { return _p_settings; } }); // get setting of this
                _currentGame = this;
            },
            FreeContext: igk.html5.freeWebGLContext,
            CreateContext: function(q, listener) {
                // init node
                var canvas = q.o; // document.getElementById("game-surface");
                var _ol = listener || q.getAttribute("igk-webgl-game-attr-listener");
                var gl = igk.html5.createWebGLContext(canvas);
                // init scene properties
                var _m = $igk(q.o.parentNode).add("div").addClass("scene dispn posfix"); // store scene properties
                var _mcl = _m.getComputedStyle("backgroundColor");
                var _clbg = igk.system.colorFromString(_m.getComputedStyle("backgroundColor")).toInt(); // String(); 
                // _m.remove();
                // 	return 0;
                if (!gl) {
                    if (igk.navigator.isSafari())
                        return 0;
                    ctx = canvas.getContext("2d");
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = igk.system.colors.cornflowerblue;
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = igk.system.colors.darkblue;
                    ctx.font = "12pt courrier new";
                    ctx.fillText(
                        "Sorry but your browser does not support WEBGL. try with another one",
                        0,
                        (canvas.height / 2),
                        100
                    );
                    return 0;
                }
                // delete gl;
                // igk.show_prop(gl);
                igk.winui.reg_event(window, "pagehide", function() {
                    gl = null;
                    q.remove();
                });
                if (!canvas.getAttribute("width"))
                    canvas.width = 200;
                if (!canvas.getAttribute("height"))
                    canvas.height = 200;
                _params = {
                    canvas: q.o,
                    gl: gl,
                    enable_error_handler: 1
                };

                function _createOl(fc) {
                    return new fc();
                };
                if (_ol) {
                    switch (typeof(_ol)) {
                        case 'string':
                            {
                                var ns = igk.system.getNS(_ol);
                                if (typeof(ns) == 'function') {
                                    // parameter to pass to init method
                                    _ol = _createOl(ns);
                                    // _params={
                                    // canvas: q.o,
                                    // gl:gl,
                                    // enable_error_handler:1
                                    // };
                                    // _ol=new ns();
                                } else {
                                    // no definition class found
                                    console.debug("/!\\ No game definition class found. [ " + _ol + " ]");
                                    _ol = null;
                                }
                            }
                            break;
                        case "function":
                            _ol = _createOl(_ol);
                            break;
                    }
                } else {
                    console.error("/!\\ igk-webgl-game-attr-listener not provided. ");
                    _ol = 0;
                    // return 0;
                }
                var cl = igk.system.colors.toFloatArray(_clbg);
                if (!_ol) {
                    // gl.clearColor(1.0,0.55,0.55,1.0);
                    gl.clearColor(cl.r, cl.g, cl.b, cl.a);
                    gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
                    return 0;
                } else {
                    // init _ol
                    gl.TRUE = 1;
                    gl.FALSE = 0;
                    _ol.setBgColor(cl);
                    _ol.initGame(gl); // init game environment
                    _ol.updateSize(gl); // 
                    _ol.initContext(gl);
                    _ol.loadContent(gl);
                    // data attached
                    var ol = {
                        listener: _ol,
                        gl: gl
                    };
                    m_gl = gl;
                    igk.winui.reg_event(window, "pagehide", function() {
                        igk.html5.drawing.FreeContext(gl);
                    });
                    _def = ol;
                    _Run(ol);
                }
                return ol;
            }
        });
        igk.winui.initClassControl("igk-webgl-game-surface", function() {
            var q = this;
            setTimeout(function() {
                var n = q.add(igk.createNode('canvas'));
                var g = igk.html5.drawing.CreateContext(n, q.o.getAttribute("igk-webgl-game-attr-listener"));
                if (!g) {
                    console.log('/!\\ can not attach webgl to surface . something bad happen');
                    n.remove();
                    return;
                }
                q.add(n);
                g.listener.canvas.setAttribute("id", q.o.getAttribute("id"));
                q.o.removeAttribute("id");
            }, 500);
        }, {
            desc: 'webgl graphic surface',
            create: function(listener) {
                var n = igk.createNode('canvas');
                n.addClass('igk-webgl-game-surface');
                if (listener)
                    n.setAttribute("igk-webgl-game-attr-listener", listener);
                return n;
            }
        });
    })();
    //--------------------------------------------------------------------------------------------------------
    //media management
    //--------------------------------------------------------------------------------------------------------
    (function() {
        function __media_setting(q, b) {
            // q.setCss({display:"none !important"});
            q.setAttribute("style", "display:none !important");
            var m_inf = {};

            function __media_change() {
                console.debug("media change");
                var c = igk.css.getMediaType();
                if ((c != 'global') && !(c in m_inf)) {
                    var d = document.createElement("div");
                    w = igk.getNumber(q.getComputedStyle("width"));
                    h = igk.getNumber(q.getComputedStyle("height"));
                    d.setAttribute("width", w);
                    d.setAttribute("height", h);
                    $igk(d).addClass(q.o.className + " " + c).setCss({ width: w + "px", height: h + "px", "backgroundImage": "url('" + b + "&w=" + w + "&h=" + h + "')" });
                    $igk(d).setConfig("igk:callAttribBindingData", 1);
                    $igk(d).setConfig("igk:initnode", 1);
                    q.o.parentNode.appendChild(d);
                    m_inf[c] = d;
                }
            };
            igk.appendProperties(this, {
                change: __media_change
            });
            igk.publisher.register(igk.publisher.events.mediachanged, this.change);
        }
        igk.winui.initClassControl("igk-winui-media-img", function() {
            var q = this;
            var b = q.getAttribute("igk:base");
            var c = new __media_setting(q, b);
            c.change();
        });
    })();
    // -----------------------------------------------------
    // init all parallax
    // -----------------------------------------------------
    (function() {
        var parallax = [];

        function __i_parallax() {
            var q = this;
            var p = q.getAttribute("igk:data");
            q.setCss({
                backgroundImage: "url('" + p + "')",
                // backgroundRepeat: "no-repeat",
                backgroundPosition: "center"
                    // backgroundSize:"100% auto"
            });
            var m = new function() {
                igk.appendProperties(this, {
                    callback: function(evt) {
                        var of; // off in percent
                        var m_t = $igk(evt.target).getscrollMaxTop();
                        var v_c = q.o;
                        var c_h = q.o.scrollHeight;
                        if (m_t > 0)
                            of = parseInt((evt.target.scrollTop / m_t) * 100.0);
                        else {
                            of = 0;
                        }
                        // firefox raise warning for scrolling effect position
                        var p = Math.round((of / 200.0) * 10) * 2;
                        var cl = "igk-pos-" + p;
                        if (!(new RegExp(cl + "$")).test(q.o.className)) {
                            q.rmAllClass("^igk-pos-(.)+$")
                                .addClass(cl);
                        }
                        // else{
                        // }
                        // q.setCss({backgroundPosition: "center "+(-of)+"px"});
                        // }
                    }
                });
            };
            igk.publisher.register('sys://html/doc/scroll', m.callback);
        };
        igk.winui.initClassControl("igk-winui-parallax", __i_parallax);
    })();
    // (function(){
    // var p= igk.getParentScript();
    // igk.ready(function(){
    // \$igk(p).select('.igk-winui-parallax').each_all(function(){
    // var q = this;
    // // --------------------------------------------------------------
    // // style fixed followed box 
    // // --------------------------------------------------------------
    // var m = new function(){
    // igk.appendProperties(this, {
    // callback:function(evt){
    // // return;
    // var of; // off in percent
    // var m_t = \$igk(evt.target).getscrollMaxTop();
    // var v_c = q.o;
    // var c_h = q.o.scrollHeight;
    // if (m_t>0)
    // of = parseInt((evt.target.scrollTop/m_t)*100.0);
    // else{
    // of = 0;
    // }
    // // if (of==0){
    // // // q.setCss({backgroundPosition: "center"});	
    // // }else{
    // // firefox raise warning for scrolling effect position
    // var p = Math.round((of/200.0)*10)*2;
    // var cl = "igk-pos-"+p;
    // if ( !(new RegExp(cl+"$")).test(q.o.className)){
    // q.rmAllClass("^igk-pos-(.)+$")
    // .addClass(cl);
    // }
    // // else{
    // // }
    // // q.setCss({backgroundPosition: "center "+(-of)+"px"});
    // // }
    // }
    // });
    // }; 
    // // igk.winui.reg_event(window, 'scroll', function(){
    // // });
    // });
    // // igk.show_prop(p);
    // });// end ready
    // })();
    // close managment
    // (function(){
    // igk.ready(function(){
    // igk.dom.body().onunload=function(){
    // };
    // window.onunload = function(){
    // };
    // });
    // });
    // })();
    // (function(){
    // var s=igk.system.regex.split("\\s*([^,]+)[\\s,]*","cubic-bezier(0,0,1,1),cubic-bezier(0,0,1,1)");
    // var s=igk.system.regex.split("([^,(]+(\\(.+?\\))?)[\\s,]*","cubic-bezier(1,3,5),width");
    // })(); 
    // var count = 0;
    // igk.publisher.register("sys://doc/changed",function(p){
    // count++;
    // }) 
    (function() {
        // igk.system.createNS();
        igk.winui.initClassControl("igk-winuin-jsa-ex", function() {
            if (igk.canInvoke()) {
                this.rmClass("dispn");
                var q = this;
                var c = this.getAttribute("igk:data");
                var p = igk.JSON.parse(c);
                this.reg_event("click", function() {
                    igk.invoke(p.m, p.args);
                });
            } else
                this.remove();
        });
    })();
    (function() {
        var dialgs = [];
        var sk = 0; // for key
        function __close(evt) {
            this.remove();
        }

        function n_dialog(g) {
            var q = this;
            q.options = null;
            q.closeByEscape = 0;
            q.closeBySubmit = 1;
            igk.appendProperties(this, {
                mediachanged: function(e) {
                    q.initLoc();
                },
                close: function(evt) {
                    __close.apply(g, evt);
                    var m = 0;
                    while ((m = dialgs.pop())) {
                        if (m == q)
                            break;
                    }
                },
                showOpts: function(evt) {
                    if (q.options) {
                        q.options.toggleClass("dispn");
                    }
                },
                initLoc: function() {
                    var W = igk.getNumber(g.getComputedStyle("width"));
                    var w = -(W / 2);
                    var h = (-igk.getNumber(g.getComputedStyle("height")) / 2);
                    if (W != igk.winui.screenSize().width) {
                        g.setCss({
                            position: "fixed",
                            top: '50%',
                            left: '50%',
                            marginLeft: w + "px",
                            marginTop: h + "px"
                        });
                    } else {
                        g.setCss({
                            position: "absolute",
                            top: 'auto',
                            left: 'auto',
                            marginLeft: "auto",
                            marginTop: "auto"
                        });
                    }
                },
                subfunc: function(frm) {
                    return function(evt) {
                        evt.preventDefault();
                        igk.ajx.postform(frm.o, frm.getAttribute("action"), function(xhr) {
                            if (this.isReady()) {
                                if (q.closeBySubmit)
                                    __close.apply(g, evt);
                                igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                            }
                        });
                    };
                }
            });
        }

        function __press(evt) {
            if (dialgs.length > 0) {
                switch (evt.keyCode) {
                    case 27: // key.Escape
                        console.debug("close by escape");
                        var m = dialgs[dialgs.length - 1];
                        if (m.closeByEscape) {
                            m.close();
                            evt.preventDefault();
                            evt.stopPropagation();
                        }
                        break;
                }
            }
        };
        igk.winui.initClassControl("igk-winui-dialogbox", function() {
            var g = new n_dialog(this);
            // attach properties
            var v_d = this.getAttribute("igk:data");
            if (v_d) {
                var e = igk.JSON.parse(v_d);
                for (var i in g) {
                    if (typeof(g[i]) == "function") continue;
                    g[i] = e[i];
                }
            }
            this.select(".cls").first().reg_event("click", g.close);
            var o = this.select(".opts").first().reg_event("click", g.showOpts);
            g.options = this.select(".d-opts").first(); // dialog options
            var frm = this.select("form").first();
            if (frm) {
                frm.reg_event("submit", g.subfunc(frm));
                // diseable pression on input to enter data
                frm.select("input").reg_event("keyup keydown keypress", function(evt) {
                    if (evt.keyCode == 13) {
                        evt.preventDefault();
                        return false;
                    }
                });
                // igk.getKeys(evt);
                // }
                // ));
            }
            if (g.options) {
                g.options.addClass("dispn");
                g.options.select("a").each_all(function() {
                    var r = this.getAttribute("href");
                    switch (r) {
                        case "::close":
                            this.reg_event("click", function(evt) {
                                evt.preventDefault();
                                g.close(evt);
                            });
                            break;
                    }
                });
            } else {
                o.addClass("dispn");
            }
            this.addClass("posfix ");
            // .setCss({
            // top:'50%',
            // left:'50%',
            // marginLeft: (-igk.getNumber(this.getComputedStyle("width"))/2)+"px",
            // marginTop: (-igk.getNumber(this.getComputedStyle("height"))/2)+"px",
            // });
            g.initLoc();
            igk.publisher.register(igk.publisher.events.mediachanged, g.mediachanged);
            if (!sk) {
                igk.winui.events.regKeyPress(__press);
            }
            dialgs.push(g);
        });
    })();
    (function() {
        // remove all item if item visible
        function _rm_js_hide() {
            igk.qselect(".igk-js-hide").each_all(function() {
                this.rmClass("igk-js-hide");
            });
        }
        igk.ready(_rm_js_hide);
        igk.publisher.register(igk.evts.dom[1], function(d) {
            if (d.evt.target == igk.dom.body().o) {
                _rm_js_hide();
            }
        });
    })();
    // winui-toast
    (function() {
        var c_toast = null;

        function __transition_end(evt) {
            if ((c_toast == null) || (evt.target != c_toast.o) || (evt.propertyName != "opacity"))
                return;
            // c_toast.remove();		
            igk.winui.unreg_event(c_toast, "transitionend", __transition_end);
            c_toast.remove();
            c_toast = null;
        };

        function __init_toast() {
            if (c_toast) {
                igk.winui.unreg_event(c_toast, "transitionend", __transition_end);
                c_toast.remove();
            }
            var i = this.getHtml();
            if (this.getAttribute("noHide"))
                return;
            this.setCss({
                opacity: 1.0
            });
            var q = this;
            var _fo = 0; // fadin out
            c_toast = q;
            setTimeout(function() {
                if (_fo) return;
                igk.winui.reg_event(q, "transitionend", __transition_end);
                q.setCss({ opacity: 0.0 });
                _fo = 1;
            }, 2000);
        };
        igk.system.createNS("igk.winui.controls", {
            toast: function() {
                //toast constructor
            }
        });
        igk.winui.controls.toast.show = function(m, type) {
            var d = igk.createNode("div").addClass("igk-winui-toast");
            if (type) {
                d.addClass(type);
            };
            d.setHtml(m);
            igk.dom.body().add(d);
            d.init();
        };
        igk.winui.controls.toast.initDemo = function() {
            igk.winui.controls.toast.show("Toast Demonstration");
        };
        igk.winui.initClassControl("igk-winui-toast", __init_toast);
    })();
    (function() {
        igk.winui.initClassControl("igk-framebox-dialog", function(n, s) {
            var d = igk.JSON.parse(this.getAttribute("data"));
            if (!d) {
                d = { w: null, h: null };
            }
            this.select(".framebox-title").each(function() {
                igk.ctrl.selectionmanagement.disable_selection(this.o);
            });
            igk.winui.framebox.init(this.o, d.w, d.h);
        });
    })();
    (function() {
        igk.system.createNS("igk.winui.stateBtn", {
            init: function(q) {
                var o = $igk(q.o.parentNode).select('input').first().o;
                q.reg_event("click", function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    o.click();
                });
            }
        });
    })();
    // ajx update component
    (function() {
        function __init_function() {
            var self = this;
            var s = this.getAttribute("igk:target");
            var tv = this.select(">>").first();
            var _initl = [];
            var fc = function(t) {
                var c = this;
                if (t.target == self.o) {
                    for (var i = 0; i < _initl.length; i++) {
                        _initl[i].init();
                    }
                    igk.publisher.unregister("sys://node/init", c.caller);
                }
            };
            igk.publisher.register("sys://node/init", fc);
            if (s && tv) {
                this.remove();
                var m = { d: s };
                var q = eval(s + ";");
                if (q) {
                    var g = $igk(q);
                    if (g.isSr()) {
                        var i = 0;
                        g.each_all(function() {
                            // 
                            var _ie = tv; // insert element
                            if (i) {
                                _ie = tv.clone();
                            }
                            this.o.parentNode.replaceChild(
                                _ie.o,
                                this.o
                            );
                            _initl.push(_ie);
                            // _ie.init();
                            i = 1;
                        });
                    } else {
                        g.o.parentNode.replaceChild(
                            tv.o,
                            g.o
                        );
                    }
                }
            }
        }
        igk.winui.initClassControl("igk-ajx-update-view", __init_function);
    })();
    (function() {
        // clone the repsonse and append to cibling
        function __init_function() {
            var self = this;
            var s = this.getAttribute("igk:target");
            var tv = this.select(">>");
            if (!s || (tv.getCount() == 0))
                return;
            var q = eval(s + ";");
            if (q) {
                if (q.isSr()) {
                    q.each_all(function() {
                        var m = this;
                        tv.each_all(function() {
                            m.add(this.clone());
                        });
                    });
                } else {
                    tv.each_all(function() {
                        q.add(this.clone());
                    });
                }
            }
        }
        igk.winui.initClassControl("igk-ajx-append-view", __init_function);
    })();
    //---------------------------------------------------------------------------
    // +| igk-svg-lst: svg list image
    // +| igk-svg-lst-i: svg list item
    //---------------------------------------------------------------------------
    (function() {
        var m_item = {};
        // svg list view item 
        function __init_svg_i() {
            var n = this.getAttribute("igk:svg-name");
            if (m_item[n]) {
                this.rmClass("igk-svg-lst-i");
                // replace 
                var g = $igk(m_item[n]).clone();
                var p = this.o.parentNode;
                $igk(p).addClass("igk-svg-host");
                // +| use set attribute to change class name
                g.o.setAttribute("class", this.o.className);
                p.replaceChild(g.o, this.o);
                if (p.getAttribute("title")) {
                    $igk(p).qselect("svg > title").remove();
                    // console.debug("remove title "+p.getAttribute('title'));
                }
            } else {
                console.debug("item not found :" + n);
            }
        };

        function __initlist() {
            for (var i = 0; i < this.o.childNodes.length; i++) {
                var j = this.o.childNodes[i];
                if (j.tagName) {
                    var tn = j.tagName.toLowerCase();
                    if (!(tn in m_item)) {
                        m_item[tn] = $igk(j).select("svg").first();
                        // console.log('init list ');
                    }
                }
            }
            this.remove();
        };
        // svg list init svg list
        function __init_svg_l() {
            igk.dom.body().select(".igk-svg-lst").each_all(__initlist);
            // var s = igk.dom.body().add("style");
            // s.o["type"]="text/css";
            // s.o["class"]="igk-svg-lst-manager";
            // s.setHtml("\/*igk.js : script init svg *\/ .igk-svg-lst svg, .igk-svg-lst svg.no-visibible{width:1px; height:1px; }");
            // s.remove();
        }
        igk.ready(__init_svg_l);
        igk.winui.initClassControl("igk-svg-lst", function() {
            __initlist.apply(this);
        });
        igk.ajx.fn.initBeforeReady(function() {
            $igk(this).select(".igk-svg-lst").each_all(__initlist);
        });
        igk.winui.initClassControl("igk-svg-lst-i", __init_svg_i);
        igk.winui.createSVGLi = function(n) {
            if (m_item[n]) {
                var g = $igk(m_item[n]).clone();
                return g;
            } else {
                console.error("[igk] - svg-lst-item <<" + n + ">> not found");
            }
        };
        // igk.ajx.fn.registerNodeReady(function(){	
        // $igk(this).select(".igk-svg-lst").each_all(__initlist);
        // });
    })();
    // when server sending ajx response  . css can be added we need to reload the css target
    (function() {
        igk.ajx.fn.registerNodeReady(function() {
            if (!this.tagName || this.tagName.toLowerCase() != "style")
                return;
            // $igk(this).select("style").each_all(function(){
            var f = $igk("#" + $igk(this).getAttribute("igk:from")).first();
            if (f) {
                f.setHtml(this.innerHTML);
                $igk(this).remove();
            }
            // });
        });
    })();
    // igk-iframe
    (function() {
        var iframes = [];

        function __init_iframe() {
            var f = igk.createNode("iframe");
            igk.dom.copyAttributes(this.o, f.o); // .copyAttributess(this);
            f.reg_event("error", function() {
                // alert("error");
            }).reg_event("load", function() {});
            igk.dom.replaceChild(this.o, f.o); // .replaceNode(f);
        };
        igk.ready(function() {
            igk.dom.body().select("igk-iframe").each_all(__init_iframe);
        });
    })();
    // 
    // igk-ptr-btn
    // 
    (function() {
        function __init_ptr_btn() {
            var q = this;
            var g = igk.JSON.parse(q.getAttribute("igk:data"));
            var u = 0;
            if (g) {
                if (typeof(g) == 'string')
                    u = g;
                else
                    u = g.uri;
            }
            this.reg_event('click', function(e) {
                if (u) {
                    ns_igk.winui.print(u);
                } else {
                    igk.winui.reg_event(window, 'beforeprint', function(e) {
                        console.debug("before print");
                        console.debug(e);
                    });
                    window.print();
                }
                igk.stop_event(e);
            });
        };
        igk.winui.initClassControl('igk-ptr-btn', __init_ptr_btn);
    })();
    (function() {
        // igk-ajx-replace-source : used to replace ajx cibling context
        // igk:data = data used to select the replacing zone
        igk.winui.initClassControl('igk-ajx-replace-source', function() {
            console.debug("replaceiont :::: ");
            var g = igk.ajx.getCurrentXhr();
            var s = g ? g.source : null;
            if (!s) return;
            var d = this.getAttribute("igk:data");
            var sl = s.select(d);
            var o = this.getHtml();
            sl.setHtml(o).init();
            this.remove();
        });
    })();
    // (function () {
    // igk-ajx-replace-ciblibk : used to replace ajx cibling context
    // igk:data = data used to select the replacing zone
    // igk.winui.initClassControl('igk-ajx-replace-source', function () {
    // var o = this.getHtml();
    // var f = this.o.firstChild;
    // var d = this.getAttribute("igk:data");
    // $igk(d).each_all(function(){
    // this.insetBefore(
    // });
    // this.remove();
    // });
    // })();
    // popup menu guide
    (function() {
        var doc_e = 0; // get if reg doc event for closing
        var m_d = 0;
        igk.winui.initClassControl("igk-winui-popup-menu", function() {
            var attr = this.getAttribute("igk:target");
            var c = 0;
            if (attr)
                c = $igk(attr).first();
            if (!c) {
                this.setCss({ "display": "none" });
                this.remove();
                return;
            }
            var d = igk.createNode("div"); // the menu guide
            d.addClass("igk-guide-menu posfix igk-sm-only igk-xsm-only menu");
            var dc = igk.dom.body();
            var initial = 0;
            this.reg_event("click", function(evt) {
                if (!initial) {
                    dc.prepend(d);
                    if (c) {
                        d.setHtml("<ul>" + c.getHtml() + "</ul>");
                    }
                    initial = 1;
                }
                evt.stopPropagation();
                evt.preventDefault();
                m_d = d;
                d.toggleClass("igk-show");
            });
            if (!doc_e) {
                igk.winui.reg_event(document, "click", function() {
                    // remove class anyway if click on document
                    if (m_d && m_d.supportClass("igk-show")) m_d.rmClass("igk-show");
                });
                doc_e = 1;
            }
        });
    })();
    (function() {
        function __render_xml_view_tag(q, n) {
            var tab = [];
            var s = 0;
            var m = 0;
            var tb = 0;
            var attrs = 0;
            tab.push({ n: n, p: q, l: 0 });
            while ((s = tab.pop())) {
                if (s.e) {
                    m = igk.createNode("div").addClass("l");
                    m.add("span").addClass("sm").setHtml("&lt;");
                    m.add("span").addClass("tag").setHtml(s.n.tagName.toLowerCase());
                    m.add("span").addClass("sm").setHtml("/&gt;");
                    s.p.add(m);
                    continue;
                }
                switch (s.n.nodeType) {
                    case 1:
                        if (s.n.tagName) {
                            m = igk.createNode("div").addClass("l");
                            m.add("span").addClass("sm").setHtml("&lt;");
                            m.add("span").addClass("tag").setHtml(s.n.tagName.toLowerCase());
                            if (s.n.hasAttributes) {
                                attrs = s.n.attributes;
                                for (var ai = 0; ai < attrs.length; ai++) {
                                    var la = m.add("span").addClass("attr");
                                    let vattr = attrs[ai].value;
                                    la.add("span").addClass("n").setHtml(attrs[ai].name);
                                    if (vattr.length > 0) {
                                        la.add("span").addClass("sm").setHtml("=");
                                        la.add("span").addClass("v").setHtml("\"" + attrs[ai].value + "\"");
                                    }
                                }
                            }
                            // s.n.getAttributes
                            s.p.add(m);
                        } else {
                            m = igk.createNode("span");
                            m.setHtml("NoTAG ");
                            s.p.add(m);
                        }
                        var tb = s.n.childNodes;
                        if (tb.length == 0) {
                            if (s.n.content.childNodes.length > 0) {
                                tb = s.n.content.childNodes;
                            }
                        }
                        if ((tb.length == 0) || (s.n.innerHTML.trim() == "")) {
                            m.add("span").addClass("sm").setHtml("/&gt;");
                        } else {
                            m.add("span").addClass("sm").setHtml("&gt;");
                            var quote = m.add("quote");
                            tab.push({ n: s.n, e: 1, p: m });
                            for (var mm = tb.length - 1; mm >= 0; mm--) {
                                tab.push({ n: tb[mm], p: quote, l: s.l + 1 });
                            }
                        }
                        break;
                    case 3: // text element
                        // return;
                        // m = igk.createNode("div");
                        // m.add("span").setHtml("&lt;");
                        // m.add("span").addClass("tag").setHtml(s.n.tagName.toLowerCase());
                        s.p.add('div').addClass("t-ctn string").setHtml(s.n.data);
                        //console.log ("text element ", s.n);
                        break;
                    case 8:
                        var d = s.p.add("div");
                        d.add("span").addClass("sm").setHtml("&lt;").addClass("pr");
                        d.add("span").setHtml(s.n.nodeValue);
                        d.add("span").addClass("sm").setHtml("&gt;").addClass("pr");
                        break;
                }
            }
        }
        igk.winui.initClassControl("igk-xml-viewer", function() {
            if (this.getAttribute("igk:loaded"))
                return;
            let g = this;
            if ((this.o.innerHTML + "").startsWith("<!--")) {
                let c = this.o.innerHTML.substring(4);
                c = c.substring(0, c.length - 3).trim();
                g = igk.createNode("dummy").setHtml(c);
            }
            var t = igk.dom.childto_array(g);
            let b = null;
            this.setHtml("");
            for (var i = 0; i < t.length; i++) {
                b = t[i];
                __render_xml_view_tag(this, b);
            }
        });
    })();
    // TODO: test extra attribute event - demonstration - hold click with BALAFON javasript 
    (function() {
        // --------------------------------------------------------------------------
        // bind attribute event
        //
        igk.system.createNS("igk.event", {
            stop: function(e) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
        // var list = {
        // 	"click": "touchOrClick",
        // 	"doubleclick": "doubleTouchOrClick"
        // };
        var meth = [];
        var c = ["click", "doublick", "contextmenu", "mouseover", "mousedown", "mouseup"];
        for (var j in window) {
            if (j)
                if (/^on/.test(j)) {
                    j = j.substring(2);
                    if (c.indexOf(j) == -1) {
                        meth.push(j);
                        igk.ctrl.registerAttribManager("[" + j + "]", { desc: j + " property event" });
                        igk.ctrl.bindAttribManager("[" + j + "]", function(m, n) {
                            let fc = new Function(n);
                            this.o.removeAttribute(m);
                            m = m.substring(1, m.length - 1);
                            this.reg_event(m, function(e) {
                                if (fc.apply(this, [e]) == !1) {
                                    igk.event.stop(e);
                                }
                            });
                        });
                    }
                }
        }
        igk.ctrl.registerAttribManager("[click]", { desc: "click property event" });
        igk.ctrl.registerAttribManager("[doubleclick]", { desc: "doubleclick property event" });
        igk.ctrl.registerAttribManager("[contextmenu]", { desc: "contextmenu property event" });
        igk.ctrl.bindAttribManager("[click]", function(m, n) {
            if (n == null) {
                return;
            }
            this.o.removeAttribute(m);
            var fc = new Function(n);
            var q = this;
            this.reg_event("touchOrClick", function(event) {
                if (event.handle)
                    return;
                if (fc.apply(q, [event]) == false) {
                    igk.event.stop(event);
                }
            });
        });
        igk.ctrl.bindAttribManager("[doubleclick]", function(m, n) {
            if (n == null) {
                return;
            }
            var fc = new Function(n);
            var q = this;
            this.reg_event("doubleTouchOrClick", function(event) {
                igk.event.stop(event);
                if (event.handle)
                    return;
                fc.apply(q, [event]);
            });
        });

        function _initmethod(ms) {
            return function(m, n) {
                if (n == null) {
                    return;
                }
                this.reg_event(ms, function(e) {
                    if (e.handle)
                        return;
                    eval(n);
                });
            };
        };
        var mouseevents = ["mouseover", "mousedown", "mouseup"];
        for (var i = 0; i < mouseevents.length; i++) {
            var n = mouseevents[i];
            igk.ctrl.registerAttribManager("[" + n + "]", { desc: "click property event" });
            igk.ctrl.bindAttribManager("[" + n + "]", _initmethod(n));
        }
        // + | disable context menu if necessary
        igk.ctrl.bindAttribManager("[contextmenu]", function(m, n) {
            let fc = new Function(n);
            this.reg_event('contextmenu', function(e) {
                if (fc.apply(this, [e]) == !1) {
                    igk.event.stop(e);
                }
            });
            this.o.removeAttribute(m);
        });
    })();
    (function() {
        var bind = function(n) {
            eval(n);
        };
        igk.ctrl.registerAttribManager("[ready]", { desc: "ready run load event" });
        igk.ctrl.bindAttribManager("[ready]", function(m, n) {
            if (n == null) {
                return;
            }
            var q = this;
            igk.ready(function() {
                bind.apply(q, [n]);
            });
        });
    })();
    // igk-js-button
    (function() {
        igk.winui.initClassControl("igk-js-button", function() {
            var s = this.getAttribute("igk:js-action");
            if (s) {
                this.reg_event("click", function(e) {
                    eval(s);
                    e.preventDefault();
                    e.stopPropagation();
                });
            }
        });
    })();
    // 
    // igk-winui-more-view
    // 
    (function() {
        igk.winui.initClassControl("igk-winui-more-view", function() {
            var hide = 0;
            var rem = this.getAttribute("igk:hide");
            var q = this.o.nextSibling;
            var b = [];
            var t = this;
            t.on("click", function() {
                t.toggleClass("igk-hide");
                if (rem) {
                    t.remove();
                }
            });
            return;
        });
    })();
    // igk-winui-js-logger
    (function() {
        // desc used to create a div that will recieve log message .
        var _glogger = 0;

        function JSLogger(t) {
            var _editable = t.getAttribute("editable");
            // event info
            var _ei = {
                target: t.o,
                host: this
            };
            if (_editable) {}
            // events
            t.addEvent("logchanged", _ei);

            function _raiseEvent(n, p) {
                igk.winui.events.raise(t, n, p);
            };

            function _add(te, msg) {
                var g = t.add("div").addClass(te).setHtml(msg);
                _raiseEvent("logchanged");
                return g;
            };
            igk.appendProperties(this, {
                add: function() {
                    var g = t.add.apply(t, arguments);
                    return g;
                },
                clear: function() {
                    t.setHtml("");
                },
                addi: function(msg) { // add info		
                    return _add('igk-info', msg);
                },
                adde: function(msg) { // add error 				
                    return _add('igk-danger', msg);
                },
                addw: function(msg) { // add error 
                    return _add('igk-warning', msg);
                }
            });
        };
        igk.system.createNS("igk.log", {
            write: function(msg) {
                if (_glogger)
                    _glogger.add("div").setHtml(msg);
                else
                    console.debug(msg);
            },
            clear: function() {
                if (_glogger)
                    _glogger.clear();
            }
        });
        igk.winui.initClassControl("igk-winui-js-logger", function() {
            if (_glogger) {
                return;
            }
            _glogger = new JSLogger(this);
        });
    })();
    (function() {
        function __searchParam() {};
        //form utility
        igk.system.createNS("igk.winui.form", {
            post(uri, args) {
                var searchParam = URLSearchParams || __searchParam;
                var f = document.createElement("form");
                f["method"] = "POST";
                f["action"] = uri;
                if ((typeof(args) == "string") || (typeof(args) == "object")) {
                    var cparam = new searchParam(args);
                    for (const [i, v] of cparam) {
                        $igk(f).add("input").setAttribute("type", "hidden")
                            .setAttribute("name", i)
                            .setAttribute("value", v);
                    }
                }
                document.body.appendChild(f);
                f.submit();
            },
            postData(link, target) {
                var q = link.select(target).first();
                if (q) {
                    var lnk = link.o.getAttribute("href");
                    var data = igk.winui.form.serialize(q.o, {});
                    igk.ajx.post({
                            uri: lnk,
                            param: JSON.stringify(data),
                            contentType: "application/json"
                        },
                        null, null);
                }
            },
            serialize(form, obj) {
                var obj = obj || {};
                var frmData = new FormData(form);
                frmData.forEach(function(v, k) {
                    if (k in obj) {
                        if (!Array.isArray(obj[k])) {
                            obj[k] = [obj[k]];
                        }
                        obj[k].push(v);
                        return;
                    }
                    obj[k] = v;
                });
                return obj;
            },
            validate(t) {
                var q = $igk(igk.getParentScript()).select("^form").first();
                q.reg_event("submit", function(e) {
                    for (var i = 0; i < t.length; i++) {
                        if (!q.o[t[i]].value) {
                            e.preventDefault();
                            e.stopPropagation();
                            return;
                        }
                    }
                });
            }
        });
    })();
    (function() {
        //no scroll item selections
        igk.ctrl.registerAttribManager("igk-winui-no-scroll", {
            "desc": "disable scrolling on item"
        });
        igk.ctrl.bindAttribManager("igk-winui-no-scroll", function(m, v) {
            if (!v || !igk.css.isItemSupport(["scrollBarWidth"]))
                return;
            this.setCss({ "scrollbarWidth": "none" });
        });
    })();
    (function() {
        //utility extension html to str
        var _d = 0;
        igk.system.createNS("igk", {
            toStr: function(v) {
                if (!_d) {
                    _d = igk.createNode("div");
                }
                return _d.setHtml(v).o.innerText;
            }
        });
    })();
    (function() {
        // auto hide core component
        igk.winui.initClassControl("anim-autohide", function() {
            var q = this;
            q.reg_event("animationend", function(e) {
                // console.log("anim end", e);
                if (e.animationName == "anim-autohide") {
                    q.remove();
                }
            });
        });
    })();
    (function() {
        // col hover table
        var cellindex = -1;

        function rmStyle(q, index) {
            q.qselect("tr td, tr th").each_all(function() {
                if (this.o.cellIndex == index) {
                    this.rmClass("hover");
                }
            });
        };

        function addStyle(q, index) {
            q.qselect("tr td, tr th").each_all(function() {
                if (this.o.cellIndex == index) {
                    this.addClass("hover");
                }
            });
        };
        igk.ctrl.registerAttribManager("igk-table-col-hover", {
            "desc": "table col management"
        });
        var time_ = 0;
        igk.ctrl.bindAttribManager("igk-table-col-hover", function(n, m) {
            var q = this;
            this.on("mouseover", function(e) {
                if (("cellIndex" in e.target) && (/TD|TH/.test(e.target.tagName))) {
                    clearTimeout(time_);
                    time_ = 0;
                    if (cellindex != e.target.cellIndex) {
                        rmStyle(q, cellindex);
                        cellindex = e.target.cellIndex;
                        addStyle(q, cellindex);
                    }
                }
            }).on("mouseout", function(e) {
                // console.debug("mouse out : "+e.target.tagName);
                if (/TD|TH/.test(e.target.tagName)) {
                    clearTimeout(time_);
                    time_ = setTimeout(function() {
                        rmStyle(q, cellindex);
                        cellindex = -1;
                    }, 500);
                }
            });
        });
    })();
    // -------------------------------------
    // | igk:validation-data validation data
    // -------------------------------------
    (function() {
        igk.ctrl.registerAttribManager("igk:validation-data", {
            "desc": "use for validation data."
        });
        igk.ctrl.bindAttribManager("igk:validation-data", function(t, v) {
            var d = igk.createNode("div");
            d.addClass("data");
            var state = 0;
            var q = $igk(this);
            var data = JSON.parse(v);
            var form = q.select("^form").first();

            function _updateState() {
                state = 0;
                if (q.o.value.length > 0) {
                    if (q.o.value.length < data.length) {
                        state = 1;
                    } else {
                        for (var s in data.pattern) {
                            if (!(new RegExp(data.pattern[s])).test(q.o.value)) {
                                if (s.indexOf("*") != -1) {
                                    state = 2;
                                } else {
                                    state = 1;
                                }
                                // console.debug("failed : "+s+ " = "+data.pattern[s]);
                                break;
                            }
                        }
                    }
                }
                // console.debug("state : "+state);
                switch (state) {
                    case 0:
                        d.rmClass("igk-danger igk-warning igk-info");
                        break;
                    case 1:
                        d.addClass("igk-danger");
                        break;
                    case 2:
                        d.addClass("igk-warning");
                        break;
                    case 3:
                        d.addClass("igk-info");
                        break;
                }
            };
            if (form) {
                form.on("submit igkFormBeforeSubmit", function(e) {
                    if (state == 1) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
                if (data.matchTarget) {
                    var target = form.select(data.matchTarget).first();
                    if (target) {
                        target.on("input", function() {
                            _updateState();
                            if (q.o.value != target.o.value) {
                                state = 1;
                            }
                        });
                    }
                }
            }
            q.on("input", function() {
                _updateState();
            }).insertAfter(d.o);
            _updateState(true);
        });
    })();
    (function() {
        var _ut = {
            getSep() {
                return igk.system.getNS('igk.system.locale.decimalSeperator') || ".";
            }
        };
        var _handler = {
            number(e, notdec) {
                var v = this.value;
                var def = notdec || true;
                if (typeof(notdec) == 'undefined')
                    notdec = true;
                if (notdec) {
                    var sep = _ut.getSep();
                    if ((e.key == sep)) {
                        //already contain separator
                        if (v.indexOf(sep) != -1) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        return;
                    }
                }
                if (/[0-9]/.test(e.key) == false) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            },
            pastenumber(e, notdec) {
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                var v = this.value;
                var sl = this.selectionStart;
                var sep = _ut.getSep();
                paste = v.substr(0, this.selectionStart) + paste + v.substr(sl);
                var rf = [/^[0-9]+(\.([0-9]+)?)?$/, /^[0-9]+$/];
                if (typeof(notdec) == 'undefined')
                    notdec = true;
                var rx = notdec ? rf[0] : rf[1];
                if (rx.test(paste) == false) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            },
            integer(e) {
                _handler.number(e, false);
            },
            pasteinteger(e) {
                _handler.pastenumber(e, false);
            }
        };

        function __init_form() {
            this.qselect('input').each_all(function() {
                if (this.supportClass('number')) {
                    this.on('keypress', _handler.number);
                    this.on('paste', _handler.pastenumber);
                }
                if (this.supportClass('integer')) {
                    this.on('keypress', _handler.integer);
                    this.on('paste', _handler.pasteinteger);
                }
            });
        };
        igk.winui.initClassControl('igk-form', __init_form);
    })();

    // | module management
    (function() {
        let modules = [];

        function esm(templateStrings, ...substitutions) {
            let js = templateStrings.raw[0];
            for (let i = 0; i < substitutions.length; i++) {
                js += substitutions[i] + templateStrings.raw[i + 1];
            }
            return 'data:text/javascript;base64,' + btoa(js);
        };

        igk.system.createNS("igk.system.modules", {
            response: null,
            esm: esm,
            /**
             * append module to document
             * @param {string} src 
             * @returns 
             */
            append(src, id) {
                let v = igk.createNode('script');
                v.o.type = 'module';
                v.o.id = id;
                v.setHtml(src);
                igk.dom.body().add(v.o);
                modules.push(v);
                return v;
            },
            import (src, callback) {
                let g = (async() => {
                    let m = await igk.system.modules.importAsync(src);
                    return m;
                })().then((m) => {
                    if (callback) {
                        callback(m);
                    }
                    return m;
                });
                return g;
            },
            async importAsync(src) {
                let b = esm `${src}`;
                // + | webpack request that import is a string - to avoid critical dependency for expression
                let c = await
                import (`${b}`);
                return c;
            }
        })
    })();


    /// cookies management

    (function() {
        // on init before set the properties ste the properties cookies name readonly
        let sC = null;
        const appCookies = igk.cookieName || 'blf-c';
        igk.system.createNS("igk.cookies", {
            set(n, v) {
                if (!sC) {
                    let l = igk.web.getcookies(appCookies);
                    if (l) {
                        sC = JSON.parse(l) || {};
                    } else {
                        sC = {};
                    }
                }
                sC[n] = v;
                igk.web.setcookies(appCookies, JSON.stringify(sC), undefined, "/");
                return sC;
            }
        });
    })();
    // igk.ctrl.bindAttribManager("igk-js-bind-select-to",function(n,v){
    // var s=null;
    // var q=this;
    // var qv=q.getAttribute('value');
    // if(igk.system.string.startWith(v,"#"))
    // {
    // s=$igk(v);
    // if(s){
    // s.select("option").each(function(){
    // copy
    // q.appendChild(this.clone());
    // continue execution
    // return !0;
    // });
    // }
    // }
    // else{
    // var s = igk.JSON.parse(v);	
    // if(s && s.id){
    // if(s.allowempty){
    // var opt=igk.createNode("option");
    // opt.setAttribute("value",typeof(s.emptyvalue) !=igk.constants.undef ? s.emptyvalue : null);
    // q.appendChild(opt);
    // }
    // var select=s.selected;
    // var tag=s.tag ? s.tag : 'option';
    // var present=false;
    // s=$igk(s.id).select(tag)
    // .each(function(){
    // copy
    // var r=null;
    // if(tag !='option')
    // {
    // r=igk.createNode("option");
    // r.copyAttributes(this);
    // r.setHtml(this.o.innerHTML);
    // }
    // else 
    // r=this.clone();
    // var vv=r.getAttribute('value');
    // if(vv==select){
    // r.setAttribute('selected','true');
    // }
    // if(tag !='option')
    // r.o.tagName="option";
    // q.appendChild(r);
    // present |=(vv==qv);
    // continue execution
    // return !0;
    // });
    // if((qv!=null) && !present){
    // var r=igk.createNode("option");
    // r.setAttribute('value',qv);
    // r.setHtml(qv);
    // q.appendChild(r);
    // }
    // }
    // }
    // });
    // handler for touch events
    (function() {
        var m = [];
        var c = igk.createNode("div");

        function __disable_func(evt) {
            evt.preventDefault();
        }
        // override the click event functions register function for click or touch screen
        igk.winui.registerEventHandler("click", {
            reg_event: function(item, func, useCapture) { // click host handler				
                return igk.winui.reg_system_event(item, "click", func, useCapture);
            },
            unreg_event: function(item, func) {
                return igk.winui.unreg_system_event(item, "click", func);
            }
        });

        function _dblevent(item, func) {
            var last = 0;
            func.badge = function(evt) {
                if ((evt.timeStamp - last) < 500) {
                    // console.debug("change type");
                    var h = igk.winui.events.createEvent("dbltouch", {});
                    //evt.type ="dbltouch";
                    func.apply($igk(item).o, [h]);
                }
                last = evt.timeStamp;
            };
            return func.badge;
        };
        igk.winui.registerEventHandler("dbltouch", {
            reg_event: function(item, func, useCapture) {
                if ($igk(item).istouchable()) {
                    return igk.winui.reg_system_event(item, "touchstart", _dblevent(item, func), useCapture);
                }
                return 0;
            },
            unreg_event: function(item, func) {
                if ($igk(item).istouchable()) {
                    return igk.winui.unreg_system_event(item, "touchstart", func, useCapture);
                }
            }
        });
        var m_eventDatas = {};

        function _regEventData(n, data) {
            var t = null;
            if (typeof(m_eventDatas[n]) == igk.constants.undef) {
                t = [];
            } else
                t = m_eventDatas[n];
            t.push(data);
            m_eventDatas[n] = t;
        };

        function _unregEventData(n, item, func) {
            var t = null;
            var c = false;
            if (m_eventDatas[n] != igk.constants.undef) {
                t = m_eventDatas[n];
                var cp = [];
                var e = null;
                for (var s = 0; s < t.length; s++) {
                    e = t[s];
                    if (!((e.i == item) && (e.func == func))) {
                        cp.push(e);
                        c = !0;
                    }
                }
                if (c)
                    m_eventDatas[n] = cp;
            }
            return c;
        };

        function _getEventData(n, item, func) {
            var e = null;
            var t = m_eventDatas[n];
            if (t) {
                for (var s = 0; s < t.length; s++) {
                    e = t[s];
                    if ((e.i == item) && (e.func == func)) {
                        return e;
                    }
                }
            }
            return !1;
        };

        function _unbindEventData(e, n) {
            var t = m_eventDatas[n];
            var r = 0;
            if (t) {
                var __t = [];
                for (var s = 0; s < t.length; s++) {
                    if (t[s] == e) {
                        r = 1;
                        continue;
                    }
                    __t.push(t[s]);
                }
                m_eventDatas[n] = __t;
            }
            return r;
        };
        // + | register click event
        (function(PN) {
            igk.winui.registerEventHandler(PN, {
                reg_event: function(item, func, useCapture, single) {
                    var _dc = 0;
                    if (typeof(single) == 'undefined') {
                        single = 1;
                    }
                    if (single && (_dc = _getEventData(PN, item, func))) {
                        console.debug("[BJS] - function already binded for .", item, item === _dc.i, _dc);
                        return;
                    }
                    var c = {
                        n: PN,
                        index: 0,
                        i: item,
                        h: 0,
                        "func": func,
                        bind: function(evt) {
                            // bind is the actual method that will be register 
                            if (evt.type == "touchend") {
                                if (evt.cancelable) {
                                    evt.preventDefault();
                                    evt.stopPropagation();
                                }
                                c.h = 1;
                            } else if (c.h == 1) {
                                c.h = 0;
                                return;
                            }
                            c.func.apply(item, [evt]);
                        }
                    };
                    c.index = m_eventDatas[c.n] ? m_eventDatas[c.n].length : 0;
                    _regEventData(c.n, c);
                    if ($igk(item).istouchable()) {
                        igk.winui.reg_system_event(item, "touchend", c.bind, useCapture);
                    }
                    return igk.winui.reg_system_event(item, "click", c.bind, useCapture);
                },
                unreg_event: function(item, func, useCapture) {
                    var n = PN;
                    var c = _getEventData(n, item, func);
                    if (c) {
                        if ($igk(item).istouchable()) {
                            igk.winui.unreg_system_event(item, "touchend", c.bind);
                        }
                        var o = igk.winui.unreg_system_event(item, "click", c.bind);
                        // console.debug("unbind event : "+ 
                        _unbindEventData(c, n);
                        return o;
                    }
                }
            });
        })("touchOrClick");
        // + | register double click event
        (function(PN) {
            igk.winui.registerEventHandler(PN, {
                reg_event: function(item, func, useCapture, single) {
                    var _dc = 0;
                    if (typeof(single) == 'undefined') {
                        single = 1;
                    }
                    if (single && (_dc = _getEventData(PN, item, func))) {
                        console.debug("[BJS] - function already binded for .", item, item === _dc.i, _dc);
                        return;
                    }
                    var c = {
                        n: PN,
                        index: 0,
                        i: item,
                        h: 0,
                        "func": func,
                        bind: function(evt) {
                            // bind is the actual method that will be register 
                            if (evt.type == "touchend") {
                                if (evt.cancelable) {
                                    evt.preventDefault();
                                    evt.stopPropagation();
                                }
                                c.h = 1;
                            } else if (c.h == 1) {
                                c.h = 0;
                                return;
                            }
                            c.func.apply(item, [evt]);
                        }
                    };
                    c.index = m_eventDatas[c.n] ? m_eventDatas[c.n].length : 0;
                    _regEventData(c.n, c);
                    if ($igk(item).istouchable()) {
                        igk.winui.reg_system_event(item, "dbltouchend", c.bind, useCapture);
                    }
                    return igk.winui.reg_system_event(item, "dblclick", c.bind, useCapture);
                },
                unreg_event: function(item, func, useCapture) {
                    var n = PN;
                    var c = _getEventData(n, item, func);
                    if (c) {
                        if ($igk(item).istouchable()) {
                            igk.winui.unreg_system_event(item, "doubletouchend", c.bind);
                        }
                        var o = igk.winui.unreg_system_event(item, "dblclick", c.bind);
                        // console.debug("unbind event : "+ 
                        _unbindEventData(c, n);
                        return o;
                    }
                }
            });
        })("doubleTouchOrClick");
        (function(PN) {
            igk.winui.registerEventHandler(PN, {
                reg_event: function(item, func, useCapture, single) {
                    var _dc = 0;
                    if (typeof(single) == 'undefined') {
                        single = 1;
                    }
                    if (single && (_dc = _getEventData(PN, item, func))) {
                        console.debug("[BJS] - function already binded for .", item, item === _dc.i, _dc);
                        return;
                    }
                    var c = {
                        n: PN,
                        index: 0,
                        i: item,
                        h: 0,
                        "func": func,
                        bind: function(evt) {
                            c.func.apply(item, [evt]);
                        }
                    };
                    c.index = m_eventDatas[c.n] ? m_eventDatas[c.n].length : 0;
                    _regEventData(c.n, c);
                    if (MutationObserver) {
                        var ob = new MutationObserver(c.bind);
                        ob.observe(item, { childList: true, subtree: true });
                        c.observer = ob;
                    } else {
                        item.addEventListener("DOMNodeInserted", c.bind, useCapture);
                        item.addEventListener("DOMNodeRemoved", c.bind, useCapture);
                    }
                },
                unreg_event: function(item, func) {
                    var n = PN;
                    var c = _getEventData(n, item, func);
                    if (c.observer) {
                        c.observer.disconnect();
                        _unregEventData(c.n, item, func);
                        console.debug("disconnect");
                    }
                    if (c) {
                        igk.winui.unreg_system_event(item, "DOMNodeInserted", c.bind);
                        igk.winui.unreg_system_event(item, "DOMNodeRemoved", c.bind);
                    }
                }
            });
        })("DOMChanged");

        function __mobile_device_event(n, mobe) {
            return {
                reg_event: function(item, func, useCapture) {
                    var c = {
                        n: n,
                        index: 0,
                        i: item,
                        h: 0,
                        "func": func,
                        bind: function(evt) {
                            if (evt.type == mobe) {
                                if (useCapture && !useCapture.passive) {
                                    evt.preventDefault();
                                    evt.stopPropagation();
                                }
                                c.h = 1;
                            } else if (c.h == 1) {
                                c.h = 0;
                                return;
                            }
                            c.func.apply(item, [evt]);
                        }
                    };
                    c.index = m_eventDatas[c.n] ? m_eventDatas[c.n].length : 0;
                    _regEventData(c.n, c);
                    if ($igk(item).istouchable()) {
                        igk.winui.reg_system_event(item, mobe, c.bind, useCapture);
                    }
                    return item; // null;// igk.winui.reg_system_event(item,"click",c.bind,useCapture);
                },
                unreg_event: function(item, func, useCapture) {
                    var c = _getEventData(n, item, func);
                    var o = null;
                    if (c) {
                        if ($igk(item).istouchable()) {
                            o = igk.winui.unreg_system_event(item, mobe, c.bind);
                        }
                        // var o=igk.winui.unreg_system_event(item,"click",c.bind);				
                        return o;
                    }
                }
            };
        };
        igk.winui.registerEventHandler("igkTouchStart", __mobile_device_event("igkTouchStart", "touchstart"));
        igk.winui.registerEventHandler("igkTouchMove", __mobile_device_event("igkTouchMove", "touchmove"));
        igk.winui.registerEventHandler("igkTouchEnd", __mobile_device_event("igkTouchEnd", "touchend"));
        // {
        // reg_event: function(item,func,useCapture){
        // var c={n:'igkTouchStart',index: 0,i: item,h: 0,"func":func,bind: function(evt){
        // if(evt.type=="touchend"){
        // evt.preventDefault();
        // evt.stopPropagation();
        // c.h=1;
        // }
        // else if(c.h==1){
        // c.h=0;
        // return;
        // }
        // c.func.apply(item,[evt]);
        // }};
        // c.index=m_eventDatas[c.n] ? m_eventDatas[c.n].length : 0;
        // _regEventData(c.n,c); 
        // if($igk(item).istouchable())
        // {			
        // igk.winui.reg_system_event(item,"touchstart",c.bind,useCapture);
        // }		
        // return item;// null;// igk.winui.reg_system_event(item,"click",c.bind,useCapture);
        // },
        // unreg_event: function(item,func,useCapture){
        // var c=_getEventData("igkTouchStart",item,func);
        // var o=null;
        // if(c){
        // if($igk(item).istouchable())
        // {			
        // o = igk.winui.unreg_system_event(item,"touchstart",c.bind);
        // }		
        // // var o=igk.winui.unreg_system_event(item,"click",c.bind);				
        // return o;
        // }
        // }		
        // });
        if (typeof(c.o.onmouseenter) == "undefined") { // for safari browser usage
            igk.winui.registerEventHandler("mouseenter", {
                reg_event: function(item, func, useCapture) { // mousenter				
                    return igk.winui.reg_system_event(item, "mouseover", func, useCapture);
                },
                unreg_event: function(item, func, useCapture) {
                    return igk.winui.unreg_system_event(item, "mouseover", func);
                }
            });
        }
        c.unregister();
        // delete(c);
    })();
    // handler for mousewhell
    (function() {
        var _N = "mousewheel wheel";
        igk.winui.registerEventHandler(_N, {
            reg_event: function(item, func, useCapture) {
                var _n = ("onmousewheel" in item) ? "mousewheel" : "wheel";
                return igk.winui.reg_system_event(item, _n, func, useCapture);
            },
            unreg_event: function(item, func, useCapture) {
                var _n = ("onmousewheel" in item) ? "mousewheel" : "wheel";
                return igk.winui.unreg_system_event(item, _n, func);
            }
        });
    })();
    // handler for transitionend
    (function() {
        var m = {};
        var webkit = new igk.system.collections.list(); // store key function
        var webkit_e = new igk.system.collections.dictionary();
        var inchain = false;

        function __webkitcall(evt) {
            var r = webkit.to_array();
            for (var i = 0; i < r.length; i++) {
                var s = webkit_e.getItem(r[i]);
                if (s != null) {
                    // dispatch event to child
                    __call(r[i], s.to_array(), arguments);
                }
                r[i].apply(igk.winui.eventTarget(evt), arguments);
            }
        }

        function __call(func, r, args) {
            for (var i = 0; i < r.length; i++) {
                func.apply(r[i], args);
            }
        }

        function __reg_item_func(item, func) {
            var s = webkit_e.getItem(func);
            if (s == null) {
                s = new igk.system.collections.list();
                webkit_e.add(func, s);
            }
            s.add(item);
        }

        function __unreg_item_func(item, func) {
            var s = webkit_e.getItem(func);
            if (s != null) {
                s.remove(item);
                if (s.getCount() <= 0) {
                    webkit_e.remove(func);
                    return !0;
                }
                return !1;
            }
            return !0;
        }
        // register webkit event
        function _reg_webkitevent(n) {
            var kn = 'onwebkit' + n;
            return {
                reg_event: function(item, func, useCapture) { // trans
                    // item.
                    if (item == null)
                        return;
                    if ((typeof(item[kn]) != igk.constants.undef) ||
                        ((item != window) && (typeof(window[kn]) != igk.constants.undef))
                    ) {
                        // if(kn=="onwebkittransitionend"){
                        // window[kn]=function(){
                        // };
                        // return ;
                        // }
                        if (webkit.contains(func)) {
                            if (item != window)
                                __reg_item_func(item, func);
                            return;
                        }
                        webkit.add(func);
                        if (window[kn] == null)
                            window[kn] = __webkitcall;
                        else if ((window[kn] != __webkitcall) && !inchain) {
                            var tfunc = window[kn];
                            window[kn] = function(evt) {
                                tfunc.apply(this, arguments);
                                __webkitcall.apply(this, arguments);
                            };
                            inchain = !0;
                        }
                        webkit_e.add(item);
                        return;
                    }
                    // register 		
                    if (item.addEventListener) {
                        item.addEventListener(n, func, useCapture);
                    }
                },
                unreg_event: function(item, func, useCapture) {
                    // 
                    if (item == null)
                        return;
                    if (webkit.contains(func)) {
                        if (item != window) {
                            if (!__unreg_item_func(item, func)) {
                                return;
                            }
                        }
                        webkit.remove(func);
                        if (webkit.getCount() == 0) {
                            if (!inchain) {
                                window[kn] = null;
                            }
                        }
                        return;
                    }
                    if (item.removeEventListener) {
                        item.removeEventListener(n, func);
                    }
                }
            };
        }
        // current version of firefox not  raising transition start
        igk.winui.registerEventHandler('transitionstart', _reg_webkitevent('transitionstart'));
        igk.winui.registerEventHandler('transitionend', _reg_webkitevent('transitionend'));
        igk.winui.registerEventHandler('animationend', _reg_webkitevent('animationend'));
        igk.winui.registerEventHandler('animationiteration', _reg_webkitevent('animationiteration'));
    })();
    (function() {
        //resizing handle
        function _resizingHandle() {
            return {
                reg_event: function(item, func, useCapture) { // trans
                    igk.winui.reg_event(window, 'resize', func);
                    return item;
                },
                unreg_event: function(item, func, useCapture) {
                    igk.winui.unreg_event(window, 'resize', func);
                    return item;
                }
            };
        };
        igk.winui.registerEventHandler('windowresize', _resizingHandle());
    })();
    (function() {
        // TODO Register fullscreenevent
        function __gettabname(item, tab) {
            var kn = 0;
            var s = "";
            for (var i = 0; i < tab.length; i++) {
                s = "on" + tab[i];
                if (s in item) {
                    kn = tab[i];
                    break;
                }
            }
            return kn;
        }

        function __initEvents(tab, fallbackitem) {
            return {
                reg_event: function(item, func, useCapture) { // trans
                    // item.
                    if (item == null)
                        return;
                    var kn = __gettabname(item, tab);
                    if (!kn) {
                        if (fallbackitem && (fallbackitem != item)) {
                            kn = __gettabname(fallbackitem, tab);
                            item = fallbackitem;
                        }
                        if (!kn) {
                            console.debug("can't register event : " + tab[0]);
                            return;
                        }
                    }
                    igk.winui.reg_system_event(item, kn, func, useCapture);
                },
                unreg_event: function(item, func, useCapture) {
                    // 
                    if (item == null)
                        return;
                    if (item == null)
                        return;
                    var kn = __gettabname(item, tab);
                    if (!kn) {
                        if (fallbackitem && (fallbackitem != item)) {
                            kn = __gettabname(fallbackitem, tab);
                            item = fallbackitem;
                        }
                        if (!kn) {
                            console.debug("can't unregister event " + tab[0]);
                            return;
                        }
                    }
                    igk.winui.reg_event(item, kn, func, useCapture);
                }
            };
        };
        igk.winui.registerEventHandler("fullscreenchange", __initEvents(["fullscreenchange", "webkitfullscreenchange", "mozfullscreenchange", "msfullscreenchange", "ofullscreenchange"], document));
        igk.winui.registerEventHandler("fullscreenerror", __initEvents(["fullscreenerror", "webkitfullscreenerror", "mozfullscreenerror", "msfullscreenerror", "ofullscreenerror"], document));
    })();
    // ---------------------------------------------------------
    // BIND SELECTION MANAGEMENT
    // ---------------------------------------------------------
    (function() {
        var m_viewarticle = false;
        var m_article = new igk.system.collections.list();
        igk.ctrl.bindAttribManager("igk-article-options", function() {
            var q = this;
            var source = igk.system.convert.parseToBool(this.getAttribute("igk-article-options"));
            q.show = function() {
                // this.setCss({display:"block",position:"relative"});
            };
            q.hide = function() {
                // this.setCss({display:"none",position:"absolute"});			
            };
            if (!m_viewarticle)
                q.hide();
            else {
                q.show();
            }
            m_article.add(q);
        });
        igk.ctrl.bindAttribManager("igk-js-fix-height", function(n, m) {
            var r = null;
            if (m == "::")
                r = $igk(this.o.parentNode);
            else
                r = $igk(m).first();
            if (r == null)
                return;
            var q = this;
            // register size changed
            var m_eventContext = igk.winui.RegEventContext(this, $igk(this));
            if (m_eventContext) {
                m_eventContext.reg_window("resize", function() {
                    q.setCss({ height: r.getHeight() + "px" });
                });
            }
            // r.reg_event('resize',function(){
            // });
            // for(var s in r.o){
            // if(igk.system.string.startWith(s,'on'))
            // {
            // r.o[s]=(function(s){
            // return function(){
            // switch(s){
            // case "onmouseenter":
            // case "onmouseout":
            // case "onpointerenter":
            // case "onpointermove":
            // case "onmousemove":
            // case "onmouseleave":
            // case "onpointerleave":
            // case "onpointerout":
            // case "onpointerover":
            // case "onmouseover":
            // return;
            // }
            // };
            // })(s);
            // }
            // }
            // var t=1;
            // function __change_size(){
            // if(t==1)
            // r.setCss({height:"150px"});
            // else
            // r.setCss({height:"200px"});		
            // t=(t+1) % 2;
            // setTimeout(__change_size,2000);
            // }
            // __change_size();
            // r.setCss({border: '1px solid black'});
            q.setCss({ height: r.getHeight() + "px" });
        });
        igk.ctrl.bindAttribManager("igk-js-fix-width", function(n, m) {
            var r = null;
            if (m == "::")
                r = $igk(this.o.parentNode);
            else
                r = $igk(m).first();
            if (r == null)
                return;
            var q = this;

            function __update() {
                q.setCss({ width: (r.getWidth()) + "px" });
            }
            // register size changed
            var m_eventContext = igk.winui.RegEventContext(this, $igk(this));
            if (m_eventContext) {
                m_eventContext.reg_window("resize", function() {
                    __update();
                });
            }
            __update();
        });
        igk.ctrl.bindAttribManager("igk-js-fix-eval", function(n, m) {
            var o = igk.JSON.parse(m);
            if (o == null)
                return;
            var q = this;

            function __update() {
                o.update.apply(q);
            }
            // register size changed
            var m_eventContext = igk.winui.RegEventContext(this, $igk(this));
            if (m_eventContext) {
                m_eventContext.reg_window("resize", function() {
                    __update();
                });
            }
            __update();
        });
        // igk-js-eval
        (function() {
            function __evalfunction() {
                var s = this.getAttribute("igk-js-eval");
                if (s) {
                    igk.evalScript(s, this.o);
                }
            };
            igk.ctrl.bindAttribManager("igk-js-eval", __evalfunction);
        })();
        // igk-js-eval-init
        (function() {
            var m_initItems = [];

            function __init() {
                for (var i = 0; i < m_initItems.length; i++) {
                    var s = m_initItems[i].getAttribute("igk-js-eval-init");
                    igk.evalScript(s, m_initItems[i].o);
                }
            };
            igk.ready(__init);

            function __evalfunction() {
                var s = this.getAttribute("igk-js-eval-init");
                if (s) {
                    if (document.readyState == "complete")
                        igk.evalScript(s, this.o);
                    else
                        m_initItems.push(this);
                }
            };
            igk.ctrl.bindAttribManager("igk-js-eval-init", __evalfunction);
        })();
        (function() {
            function __evalUri() {
                var self = this;
                var uri = this.getAttribute("igk-js-init-uri");
                if (uri) {
                    igk.ajx.get(uri, null, function(xhr) {
                        if (this.isReady()) {
                            this.replaceResponseNode(self.o, false);
                        }
                    }, true);
                }
            }
            igk.ctrl.bindAttribManager("igk-js-init-uri", __evalUri);
        })();
        // #igk-ajx-form
        (function() {
            igk.winui.registerEventHandler(BEFORESUBMIT_EVENT, {
                reg_event: function(item, fc) {
                    if (item.tagName == "FORM") {
                        if (!(BEFORESUBMIT_EVENT in item)) {
                            $igk(item).addEvent(BEFORESUBMIT_EVENT, {
                                "cancelable": true,
                                "bubbles": true
                            }).on("submit", function(e) {
                                var cancel = false;
                                $igk(item).raiseEvent(BEFORESUBMIT_EVENT, null, function(e) {
                                    cancel = e.defaultPrevented;
                                });
                                if (cancel) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                }
                            });
                        }
                        igk.winui.reg_system_event(item, 'igkFormBeforeSubmit', fc);
                    }
                    return item;
                },
                unreg_event: function(item, fc) {
                    igk.winui.reg_system_event(item, 'igkFormBeforeSubmit', fc);
                    return item;
                }
            });

            function close(q) {
                var h = q.select("^.igk-js-notify-box").first();
                if (h) {
                    igk.winui.notify.close();
                }
            }

            function __init() {
                var self = this;
                var r = this.getAttribute("igk-ajx-form");
                var r_obj = r && (r != '1') ? igk.JSON.parse(r) : null;
                var noa = this.getAttribute("igk-ajx-form-no-autoreset");
                var no_c = this.getAttribute("igk-ajx-form-no-close");
                var ajxdata = igk.JSON.parse(this.getAttribute("igk-ajx-form-data"));
                //register cusom event
                this.addEvent(BEFORESUBMIT_EVENT, {
                    "cancelable": true,
                    "bubbles": true
                });
                if (((r == 1) || r_obj) && (this.o.tagName.toLowerCase() == "form")) {
                    var b = r_obj && r_obj.targetid ? r_obj.targetid : self.getAttribute("igk-ajx-form-target");
                    this.reg_event("submit", function(evt) {
                        var cancel = false;
                        var c = self.raiseEvent(BEFORESUBMIT_EVENT, null, function(e) {
                            cancel = e.defaultPrevented;
                        });
                        if (evt.defaultPrevented) {
                            cancel = true;
                        } else {
                            evt.preventDefault();
                            evt.stopPropagation();
                        }
                        if (cancel) {
                            return;
                        }
                        igk.ajx.postform(self.o, self.o.getAttribute("action"), function(xhr) {
                            if ((xhr.readyState == 4) && (xhr.status != 200)) {
                                close(self);
                                return;
                            }
                            if (this.isReady()) {
                                if (b) {
                                    var k = null;
                                    switch (b) {
                                        case "=":
                                            k = self;
                                            break;
                                        case "::": // hold global selection selection 
                                        case "??": // body target
                                            k = igk.dom.body();
                                            k.unregister();
                                            igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                                            k = null;
                                            break;
                                        default:
                                            k = $igk(b).first();
                                            break;
                                    }
                                    if (k) {
                                        k.unregister();
                                        this.setResponseTo(k.o, false);
                                    }
                                } else {
                                    var s = self.o["igk:source"];
                                    var k = s ? $igk(s) : !1;
                                    if (k) {
                                        k.unregister();
                                        this.setResponseTo(k.o, false);
                                    } else {
                                        igk.ajx.fn.append_to(xhr, igk.dom.body().o);
                                    }
                                }
                                // close the frame box by default
                                var frame = self.o["igk:framebox"];
                                if (!no_c && frame) {
                                    frame.close();
                                } else {
                                    close(self);
                                }
                                // autoreset
                                if (!noa)
                                    self.o.reset();
                                if (ajxdata && ajxdata.complete) {
                                    ajxdata.complete.apply(self);
                                }
                                if (r_obj && r_obj.complete) {
                                    r_obj.complete.apply(self);
                                }
                            }
                        });
                    });
                }
            }
            igk.ctrl.bindAttribManager("igk-ajx-form", __init);
        })();
        (function() {
            var m_ctrl = null;
            igk.appendProperties(igk.ctrl, {
                show_article_options: function() {
                    for (var i = 0; i < m_article.length; i++) {
                        if (!m_viewarticle)
                            m_article[i].show();
                        else m_article[i].hide();
                    }
                    m_viewarticle = !m_viewarticle;
                    if (m_viewarticle)
                        igk.web.setcookies("igk-sao", "1");
                    else
                        igk.web.setcookies("igk-sao", null);
                },
                show_ctrl_options: function() {
                    if (m_ctrl == null) {
                        // ....
                        return;
                    }
                    for (var i = 0; i < m_ctrl.length; i++) {
                        if (!m_viewctrl)
                            m_ctrl[i].show();
                        else
                            m_ctrl[i].hide();
                    }
                    m_viewctrl = !m_viewctrl;
                    if (m_viewctrl)
                        igk.web.setcookies("igk-sco", "1");
                    else
                        igk.web.setcookies("igk-sco", null);
                }
            });
        })();

        function __init_roll_owner(q) {
            q.reg_event("mouseout", function(evt) { __rmClass.apply(q); });
            q.reg_event("mouseover", function(evt) { __addClass.apply(q); });
            q.select(".igk-roll-in").each_all(function() {
                var g = this.select("^.igk-roll-owner").first();
                if (g == q) {
                    this.rollparent = q;
                    var self = this;
                    this.reg_event("mouseover", function(evt) {
                        self.addClass("igk-roll-in-hover");
                    });
                    this.reg_event("mouseenter", function(evt) {
                        self.addClass("igk-roll-in-hover");
                    });
                }
            });
        };

        function __addClass() {
            var t = $igk(this);
            $igk(this).qselect(".igk-roll-in").each_all(function() {
                if (this.rollparent == t)
                    this.addClass("igk-roll-in-hover");
            });
        }

        function __rmClass() {
            var t = $igk(this);
            t.select(".igk-roll-in").each_all(function() {
                if (this.rollparent == t) {
                    this.rmClass("igk-roll-in-hover");
                }
            });
        };

        function __toggleview(v) {
            if (v)
                __addClass.apply(this);
            else
                __rmClass.apply(this);
        };

        function __bind_roll() {
            // 
            // init system web item class registration 
            // 
            // (function(){
            // rollowner manager
            // igk-roll-in-hover
            // igk-roll-in
            // roll style 
            igk.qselect(".igk-roll-owner").each(function() {
                var q = this;
                __init_roll_owner(q);
                return !0;
            });
            igk.qselect(".igk-touch-roll-owner").each(function() {
                this.rmClass("igk-roll-owner"); // for cohesion
                var m = false;
                var q = this;
                if (this.istouchable()) {
                    this.reg_event("touchend",
                        function() {
                            __toggleview.apply(q, [!m]);
                            m = !m;
                        }
                    );
                } else {
                    this.reg_event("click",
                        function() {
                            __toggleview.apply(q, [!m]);
                            m = !m;
                        }
                    );
                }
                return !0;
            });
        };
        igk.ready(function() {
            // roll owner specification
            igk.ctrl.registerReady(function(e) {
                if (igk.system.regex.item_match_class("igk-roll-owner", this)) {
                    __init_roll_owner($igk(this));
                }
            });
            new __bind_roll();
        });
    })();
    //----------------------------------------------------------
    // BIND EVENT ATTRIB Management
    //----------------------------------------------------------
    (function() {
        var _attribs = {};
        igk.ctrl.registerAttribManager("igk:oninit", {
            "desc": "on init register"
        });
        igk.ctrl.bindAttribManager("igk:oninit", function(m, v) {
            // alert("bind attrib management: "+v);
            try {
                var fc = Function(v);
                fc.apply(this.o);
                if (!_attribs["oninit"])
                    _attribs["oninit"] = [];
                _attribs["oninit"].push(this);
            } catch (e) {
                console.error(e);
            }
        });
    })();
    // 
    // class control: igk-fixed-action-bar
    // attributes : igk-target . target to the how if not found get the scroll parent
    // desc: used to show a action bar . the action bar is hidden by default.
    // when scrollling the target item scroll will then show the action-bar.
    // note action-bar is fixed
    // 
    (function() {
        var m_size = 0;
        var m_item = new igk.system.collections.list();

        function __initview(n, t) {
            var h = n.data.offset || n.getHeight();
            if (t.o.scrollTop <= h) {
                n.rmClass("igk-show");
            } else {
                n.addClass("igk-show");
            }
            // var w = n.data.measure.getWidth() 
            // - (-igk.getPixel(n.getComputedStyle("marginLeft"), n.o))
            // -igk.getPixel(n.getComputedStyle("marginRight"), n.o);
            var w = n.data.measure.getWidth() -
                -igk.getPixel(n.getComputedStyle("marginLeft"), n.o) -
                igk.getPixel(n.getComputedStyle("marginRight"), n.o);
            n.setCss({ width: w + "px" });
        };

        function __register(n) {
            if (!m_size) {
                igk.winui.reg_event(window, "resize", function() {
                    var t = m_item.getCount();
                    var n = 0;
                    for (var i = 0; i < t; i++) {
                        n = m_item.getItemAt(i);
                        __initview(n, n.data.target);
                    }
                });
                n.setCss({ top: "0px" });
                m_size = 1;
            }
            var id = n.getAttribute("igk-target") || "^.igk-parentscroll";
            var offset = n.getAttribute("igk-offset");
            if (id) {
                m_item.add(n);
                var me = igk.createNode("div").addClass("posab fitw").setCss({ height: "1px", "visibility": "hidden" });
                n.data.measure = me;
                n.data.offset = offset;
                n.insertAfter(me.o);
                var c = $igk(n.select(id));
                if (c) {
                    if (c.isSr()) {
                        var q = c.first();
                        if (!q && (id != "^.igk-parentscroll")) {
                            console.error("fixed-actionbar:  target not found : " + id);
                            return;
                            //q = n.select("^.igk-parentscroll").first();
                        }
                        q.reg_event("scroll", function() {
                            __initview(n, q);
                        });
                        __initview(n, q);
                        n.data.target = q;
                    } else {
                        n.data.target = c;
                        $igk(c).reg_event("scroll", function() {
                            __initview(n, c);
                        });
                        __initview(n, c);
                    }
                } else {
                    console.debug("item not found " + c);
                }
            } else {
                console.info("warning: no igk-fixed-action-bar-target attribute found");
            }
        };
        igk.winui.initClassControl("igk-fixed-action-bar", function() {
            __register(this);
        }, {
            "desc": "fixed: action-bar"
        });
    })();
    // 
    // igk.winui.framebox
    // 
    (function() {
        var m_targetResponse = null;
        var framebox_callback = [];

        function __submit_form(evt) {
            // submit the form frame
            var q = this;
            var c = (m_targetResponse != null) ? m_targetResponse : eval(q.getAttribute("igk-ajx-lnk-tg-response"));
            var clf = q.getAttribute("igk-frame-close"); // close frame after
            if (c) {
                var m = document.getElementById(c);
                if (m) {
                    window.igk.ajx.postform(q,
                        q.getAttribute('action'),
                        function(xhr) {
                            if (this.isReady()) { if (xhr.responseText.length > 0) { this.setResponseTo(m); } }
                            q.reset();
                        },
                        false);
                    if (clf) {
                        q['igk:framebox'].close();
                    }
                    evt.preventDefault();
                    return !1;
                }
            }
            // don't cancel the default prevent mecanism
            return !0;
        }

        function __call_frame_closed(frameid) { // call event for frame closed 
            // passsing an array to functions
            var v_fremoved = [];
            var v_args = [frameid];
            for (var i = 0; i < framebox_callback.length; i++) {
                var f = framebox_callback[i];
                if ((f != null) && (f.apply(window, v_args))) {
                    v_fremoved.push(f);
                }
            }
            // remove callback
            for (var j = 0; j < v_fremoved.length; j++) {
                framebox_callback.pop(v_fremoved[j]);
            }
        }

        function __close_frame(m_frame) {
            igk.winui.framebox.frames.pop(m_frame);
            var tf = igk.winui.framebox.frames;
            igk.winui.framebox.currentFrame = tf.length > 0 ? tf[tf.length - 1] : null;
        }
        // 
        igk.system.createNS("igk.winui.framebox", {
            // currentFrame: null,// visible frame
            frames: new Array(), // array of frames
            reg_frame_close: function(callback) { // call when frame closed on client side	
                framebox_callback.push(callback);
            },
            close_currentframe: function(node) {
                var frm = ns_igk.winui.framebox.getdialog_frame(node);
                igk.ajx.get(frm.closeuri + "&ajx=1", null, null);
                var f = frm;
                $igk(f.parentNode).setCss({ opacity: 1 }).animate({ opacity: 0 }, {
                    interval: 20,
                    duration: 200,
                    complete: function() {
                        ns_igk.winui.framebox.close(frm);
                    }
                });
            },
            getdialog_frame: function(node) { // register dialog for drawing manager
                var t = null;
                var k = "igk-framebox-dialog";
                if (node.id == k)
                    t = node;
                else
                    t = igk.getParentById(node, k);
                if (t) {
                    igk.appendProperties(t, {
                        "frameDialogOwner": t.parentNode
                    });
                }
                // return the framebox_dialog. normarly parent is body a div that is contained in a body.
                // the frameOwner is the node thatwill be removed 
                return t;
            },
            reg_dialog: function(node) { // register dialog
                if (node == null)
                    return;
                var t = igk.winui.framebox.getdialog_frame(node);
                var n = null;
                if (t !== null) {
                    n = $igk(t).select(".title").getNodeAt(0);
                    if (n) {
                        // init title
                        igk.winui.dragFrameManager.init(n, t);
                    }
                    igk.winui.framebox.currentFrame = t; // setup the current frame
                    igk.winui.framebox.frames.push(t);
                }
            },
            init: function(p, w, h) { // init frame box. p=parent,w=require width,h=require height
                if (p == null) {
                    return;
                }
                var m_frame = igk.winui.framebox.getdialog_frame(p);
                var m_closeBtn = p.getElementsByTagName("a")[0];
                if (m_frame == null)
                    return;
                if (m_closeBtn == null) {
                    igk.winui.notify.showErrorInfo("Error", '/!\\ JS: frame init :: No close button found.');
                    return;
                }
                igk.winui.framebox.reg_dialog(p);
                m_frame.closeuri = m_closeBtn.href + "&ajx=1";

                function __centerDialog() {
                    igk.winui.centerDialog(p, w, h);
                };

                function __setSize(m) {
                    m.setCss({ "width": (igk.winui.screenSize().width - 15) + "px" });
                };
                // center dialog
                if (igk.system.regex.item_inherit_class("igk-android", p)) {
                    var m = $igk(p).rmClass("resizable").addClass("dispb fit overflow-y-a")
                        .select(".datas");
                    igk.winui.reg_event(window, 'resize', function() {
                        __setSize(m);
                    });
                    __setSize(m);
                    // $igk(p).add("div").setHtml(igk.winui.screenSize());
                    // alert(igk.winui.screenSize());
                } else {
                    if (!$igk(p).supportClass("no-center"))
                        __centerDialog();
                }
                m_frame.parentNode.close = function() { // register frame to close 
                    igk.winui.framebox.close(m_frame);
                };
                m_frame.close = function() {
                    igk.winui.framebox.close(this);
                };
                var s = $igk(m_frame).select("*");
                // mark all properties width a frame property
                m_frame.parentNode.targetResponse = m_targetResponse ? m_targetResponse : igk.dom.body().o;
                s.setProperties({ "igk:framebox": m_frame });
                $igk(m_frame).select("form").each_all(function() {
                    var f = this.o.onsubmit;
                    if (f) {
                        this.o.onsubmit = f;
                    } else {
                        this.o.onsubmit = __submit_form;
                    }
                });

                function __a_close_ajx() {
                    igk.ajx.get(m_frame.closeuri, null, null);
                    var f = m_frame;
                    $igk(f.parentNode).setCss({ opacity: 1 }).animate({ opacity: 0 }, {
                        interval: 20,
                        duration: 200,
                        complete: function() {
                            igk.winui.framebox.close(f);
                        }
                    });
                }
                // register event
                function __a_event(evt) {
                    if (igk.winui.framebox.currentFrame == m_frame) {
                        if (evt.keyCode == 27) {
                            if (!evt.defaultPrevented) {
                                __a_close_ajx();
                                evt.preventDefault(); // for only one
                                igk.winui.events.unregKeyPress(__a_event);
                                // remove frames lists
                                __close_frame(m_frame);
                            }
                        }
                    }
                };
                igk.winui.events.regKeyPress(__a_event);
                var s = ("" + m_closeBtn.href).split("#")[1];
                var id = null;
                if (s && (s.split('?')[0] == "/closeframe")) {
                    id = s.split("/closeframe?id=")[1];
                    m_closeBtn.onclick = function() {
                        var frame = igk.getParentById(p, id);
                        if (frame) {
                            // close frame by animating it
                            $igk(frame).setCss({ opacity: 1 }).animate({ opacity: 0 }, {
                                interval: 20,
                                duration: 200,
                                complete: function() {
                                    frame.parentNode.removeChild(frame);
                                    __a_close_ajx();
                                }
                            });
                        }
                        return !1;
                    };
                } else if (m_closeBtn) {
                    m_closeBtn.onclick = function(evt) {
                        evt.preventDefault();
                        __a_close_ajx();
                        return !1;
                    };
                }
            },
            close: function(frame) { // close the current frame
                frame = frame;
                if (frame && frame.frameDialogOwner) {
                    var p = frame.frameDialogOwner;
                    var s = $igk(frame);
                    if (s.unregEventContext) {
                        s.unregEventContext();
                    }
                    if (p && p.parentNode) {
                        p.parentNode.removeChild(p);
                        __call_frame_closed(p.id);
                    }
                }
            },
            closeCurrentFrame: function() {
                var f = igk.winui.framebox.currentFrame;
                if (f) {
                    // unreg event
                    __close_frame(f);
                }
            },
            init_confirm_frame: function(p, uri, ajxcontext) {
                var frm = igk.getParentByTagName(p, 'form');
                if (!frm) {
                    console.debug($igk(p).getParentByTagName('form'));
                    console.debug(p);
                    console.error("parent form not found");
                    return;
                }
                var r = frm.getAttribute("igk-confirmframe-response-target"); // get response id	
                if (r != null) {
                    m_targetResponse = document.getElementById(r);
                }

                function __closeForm() {
                    igk.winui.events.unregKeyPress(__keypressfunc);
                    var v_frame = igk.winui.framebox.getdialog_frame(frm);
                    if (v_frame != null) {
                        igk.winui.framebox.close(v_frame);
                    }
                }

                function __keypressfunc(evt) {
                    switch (evt.keyCode) {
                        case 27: // escape
                            console.debug("baseid");
                            if (!ajxcontext) {
                                frm.action = uri;
                                frm.confirm.value = 0;
                                frm.submit();
                            } else {
                                igk.ajx.post(
                                    frm["frame-close-uri"].value + "&id=" + frm["frame-id"].value,
                                    null, null, false);
                                __closeForm();
                            }
                            igk.winui.events.unregKeyPress(__keypressfunc);
                            evt.preventDefault();
                            evt.stopPropagation();
                            return !1;
                        case 13:
                            igk.winui.events.unregKeyPress(__keypressfunc);
                            frm.submit();
                            evt.preventDefault();
                            return !1;
                    }
                }
                if (frm) {
                    igk.winui.events.regKeyPress(__keypressfunc);
                }
            },
            btn: {
                yes: function(q) { // for yes button message response
                    // return !1;			
                    var v_frame = q['igk:framebox'];
                    window.igk.ajx.postform(q.form, q.form.getAttribute('action'), function(xhr) {
                            if (this.isReady()) {
                                if (v_frame) {
                                    v_frame.close();
                                    if (v_frame.targetResponse) {
                                        this.setResponseTo(v_frame.targetResponse);
                                    } else {
                                        igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                                    }
                                } else {
                                    igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                                }
                            }
                        },
                        false);
                    return !1;
                }
            }
        }); // end namespace
        var _c_frame = null;
        igk.defineProperty(igk.winui.framebox, "currentFrame", {
            get: function() { return _c_frame; },
            set: function(v) { _c_frame = v; }
        });
    })();
    // -------------------------------------------------------------
    // igk.ctrl.menu
    // -------------------------------------------------------------
    (function() {
        igk.system.createNS("igk.ctrl.menu", {
            init: function(target) {
                var q = $igk(target);
                var e = $igk(q.getAttribute("igk-data-menu-binding")).first();
                if (this.namespace) {
                    if (e)
                        return new this.init(target);
                    return null;
                }
                var b = igk.createNode("div");
                b.setCss({
                    overflowY: 'auto'
                }).addClass("fitw fith igk-data-src");
                b.setHtml(e.getHtml());
                q.o.appendChild(b.o);
                igk.ajx.fn.initnode(b.o);
            }
        });
        var v_menu_name = "igk-data-menu";
        if (!igk.ctrl.isAttribManagerRegistrated(v_menu_name))
            igk.ctrl.registerAttribManager(v_menu_name, { n: "js", desc: "menu register " });
        igk.ctrl.bindAttribManager(v_menu_name, function() {
            var q = this;
            var source = igk.system.convert.parseToBool(this.getAttribute(v_menu_name));
            if (source) {
                igk.ctrl.menu.init(q);
            }
        });
    })();
    // -------------------------------------------------------------
    // 
    // -------------------------------------------------------------
    (function() {
        // ajx stored
        var m_ajx = null;

        function __init_tab_control() {
            var q = this.select("ul").select("li.igk-active").first();
            var self = this;
            if (q != null) {
                this.o.activetab = q;
            }
            this.o.activate = function(i) {
                var s = i.select("^li");
                if (self.o.activetab)
                    self.o.activetab.rmClass("igk-active");
                s.addClass("igk-active");
                self.o.activetab = s;
            };
            this.init = !0;
        };
        igk.winui.initClassControl("igk-tabcontrol", __init_tab_control, {
            desc: "igk-control : tabcontrol"
        });
        igk.system.createNS("igk.winui.controls.tabcontrol", {
            init: function(uri, q) {
                ns_igk.ajx.fn.scriptReplaceContent('GET', uri, q);
            }
        });
        igk.ctrl.registerAttribManager("igk-ajx-tab-lnk", { ns: "ajx", desc: "tabcontrol ajx link" });
        igk.ctrl.bindAttribManager("igk-ajx-tab-lnk", function(m, s) {
            if (typeof(this.attribs) == 'undefined')
                this.attribs = {};
            if (this.attribs.tabattribs) {
                return;
            }
            this.attribs.tabattribs = 1;;
            var tab = this.select('^.igk-tabcontrol').first();
            if (tab == null) { console.error("/!\\ no tabcontrol found"); return; }
            if (typeof(tab.init) == "undefined") {
                init_tab_control.apply(tab);
            }
            var self = this;
            if (s) {
                this.reg_event("click", function(evt) {
                    var q = tab.select('.igk-tabcontent').first();
                    evt.preventDefault();
                    if (m_ajx) {
                        m_ajx.abort();
                    }
                    if (!q) {
                        return;
                    }
                    q.addClass("fade-out"); // .setHtml('');	
                    m_ajx = igk.ajx.get(self.o.href, null, function(xhr) {
                        if (this.isReady()) {
                            igk.ajx.fn.replace_content(q.o).apply(this, [xhr]);
                        } else {
                            if ((xhr.readyState == 4) && (xhr.readyState != 200)) {
                                m_ajx.abort();
                                q.setHtml('Error');
                            }
                        }
                    }, true);
                    tab.o.activate(self);
                }).reg_event("focus", function(evt) {
                    evt.preventDefault();
                    this.blur();
                });;
            }
        });
    })();
    (function() {
        function _init_track() {
            var c = this.select(".igk-trb-cur").first();
            if (c) {
                var r = new(function(q, c) {
                    q.addEvent("igk-trackchange", { value: null });
                    // demo reg event track changed		
                    this.target = q;
                    this.c = c;
                    this.data = null;
                    var v_d = q.getAttribute("igk-trb-data");
                    if (v_d)
                        this.data = igk.JSON.parse(v_d);
                    var self = this;
                    var m_s, m_e;
                    var m_st = false;

                    function __update(evt) {
                        if (!m_st)
                            return;
                        var l = q.getScreenLocation();
                        var W = q.getWidth();
                        var H = q.getHeight();
                        r = igk.getNumber(q.getComputedStyle("paddingTop")) +
                            igk.getNumber(q.getComputedStyle("paddingBottom"));
                        H -= r;
                        var dirup = igk.system.regex.item_match_class("igk-dir-up", q.o);
                        var hv = false;
                        if (dirup) {
                            hv = !0;
                            m_s = Math.min(100, parseInt((Math.max(0, H - Math.min(evt.clientY - l.y, H)) / H) * 10000) / 100);
                            // self.c.setCss({marginTop: parseInt((H *( 1 - m_s/100)))+"px"});			
                            self.c.setCss({ top: parseInt((H * (1 - m_s / 100))) + "px", bottom: '0px' });
                        } else if (igk.system.regex.item_match_class("igk-dir-down", q.o)) {
                            hv = !0;
                            m_s = Math.min(100, parseInt((Math.max(0, Math.min(evt.clientY - l.y, H)) / H) * 10000) / 100);
                        }
                        q.o["igk-track"] = m_s;
                        if (hv) {
                            self.c.setCss({ height: m_s + "%" });
                            if (self.data && self.data.update) {
                                self.data.update.apply(self, [{ progress: m_s, bar: self }]);
                            }
                        } else {
                            m_s = parseInt((Math.max(0, Math.min(evt.clientX - l.x, W)) / W) * 10000) / 100;
                            self.c.setCss({ width: m_s + "%" });
                            if (self.data && self.data.update) {
                                self.data.update.apply(self, [{ progress: m_s, bar: self }]);
                            }
                        }
                        // DAISE EVENT for data changed
                        q.o["igk-trackchange"].value = m_s;
                        q.raiseEvent("igk-trackchange");
                    }
                    q.reg_event("mousedown", function(evt) {
                        if (m_st)
                            return;
                        m_st = !0;
                        // cancel mouse selection
                        igk.winui.mouseCapture.setCapture(q.o);
                        igk.winui.selection.stopselection();
                        __update(evt);
                    });
                    q.reg_event("mousemove", function(evt) {
                        __update(evt);
                    });
                    q.reg_event("mouseup", function(evt) {
                        if (m_st) {
                            __update(evt);
                            m_st = false;
                            igk.winui.mouseCapture.releaseCapture();
                            igk.winui.selection.enableselection();
                        }
                    });
                    igk.appendProperties(this, {
                        toString: function() { return "igk-trb-info" }
                    });
                })(this, c);
            }
        }
        igk.ctrl.registerReady(function(e) {
            if (igk.system.regex.item_match_class("igk-trb", this)) {
                _init_track.apply($igk(this));
            }
        });
    })();
    // ---------------------------------------------------------------
    // indication
    // ---------------------------------------------------------------
    // igk-ajx-lnk
    // possible value : 1 | {method:function([ajx.get|ajx.post]),execute: [execute directly] ,complete: after receive}
    (function() {
        var m_xhr = null;
        // host for reponse in case no tag setup
        var m_host = null;
        var _NS = igk.system.createNS("igk.winui.ajx.lnk", {
            getLink: function() { return m_xhr.source; }, // expose link for evaluation
            getXhr: function() { return m_xhr; }
        });
        igk.defineProperty(_NS, "host", {
            get() {
                return m_host;
            },
            set(v) {
                m_host = v;
            }
        });
        igk.ctrl.registerAttribManager("igk-ajx-lnk", { ns: "ajx", desc: "Ajax link. used in combination with 'igk-ajx-lnk-tg' properties. " });
        igk.ctrl.bindAttribManager("igk-ajx-lnk", function(n, m) {
            if (!m)
                return;
            var q = this;
            var v = this.getAttribute("href");
            var meth = this.getAttribute("igk-ajx-lnk-method") || "GET";
            if (m && v) {
                q.addClass("igk-ajx-lnk");
                var v_meth = m.method || igk.ajx.get;
                if (meth == "POST") {
                    v_meth = igk.ajx.post;
                }
                v = igk.html.appendQuery(v, "ajx-lnk=1");
                var fc = m.update;
                var obj = igk.JSON.parse(m, q);
                var opxhr = null;
                q.reg_event("click", function(evt) {
                    if (evt.handle || evt.defaultPrevented) {
                        return;
                    }
                    evt.preventDefault();
                    evt.stopPropagation();
                    evt.handle = 1;
                    if (obj.execute) {
                        obj.execute.apply(this, evt);
                        return;
                    }
                    var tn = q.getAttribute("igk:target");
                    var rpm = q.getAttribute("igk:replacemode") || 'content'; // content| node
                    var r = null;
                    if (obj.target)
                        r = $igk(obj.target).first();
                    else if (tn) {
                        r = q.select(tn).first() || $igk(tn).first(); // || q.select(tn).first();
                    } else {
                        r = m_host;
                        m_host = null;
                    }
                    if (r != null) {
                        if (rpm == "content") {
                            fc = igk.ajx.fn.replace_content(r.o);
                        } else {
                            fc = igk.ajx.fn.replace_node(r.o);
                        }
                    }
                    if (opxhr != null) {
                        opxhr.abort();
                        opxhr = null;
                    }
                    if (fc == null) {
                        fc = igk.ajx.fn.append_to_body;
                    }
                    opxhr = v_meth(v, null, function(xhr) {
                        if (this.isReady()) {
                            m_xhr = xhr;
                            this.source = q;
                            if (fc) {
                                fc.apply(this, [xhr]);
                            }
                            if (obj.complete) {
                                obj.complete.apply(this, [xhr]);
                            }
                            m_xhr = null;
                        }
                    });
                });
            }
        });
        igk.ctrl.registerAttribManager("igk-callback", {
            "desc": "register extra function to be called from server script"
        });
        var fcs = {
            'hide': function(n) {
                return function() {
                    this.select(n).hide(); // ("igk-hide").remove();
                };
            }
        };
        igk.ctrl.bindAttribManager("igk-callback", function(t, v) {
            var c = igk.JSON.parse(v);
            var g = {};
            if (c) {
                for (var i in c) {
                    var f = c[i];
                    if (igk.typeofs(f) && (i in fcs)) {
                        this[i] = fcs[i](f);
                    } else if (igk.typeoff(f)) {
                        this[i] = f;
                    } else {
                        console.debug("failed " + typeof(f));
                    }
                }
            }
            this.callback = g;
        });
        igk.winui.initClassControl("igk-winui-ajx-lnk-replace", function() {
            var q = this;
            var _i = q.getAttribute("igk-lnk-target");
            var _index = q.getAttribute("igk-lnk-index") || "0";
            var _host = m_host || igk.ajx.GetParentHost();
            // return;
            var ck = 0;
            if (_host) {
                var sl = _host.select(_i);
                if (sl.getCount() > 0) {
                    if (_index == "*")
                        sl.setHtml(q.o.innerHTML).init();
                    else
                        sl.getItemAt(_index).setHtml(q.o.innerHTML).init();
                    ck = 1;
                }
            }
            if (!ck) {
                var v = igk.dom.body().select(_i).first();
                if (v) {
                    v.setHtml(q.o.innerHTML).init();
                } else
                    console.debug("item not found " + _i);
            }
            q.remove();
        }, { desc: "use to replace a cibling node with the inner content" })
    })();
    (function() {
        igk.ctrl.registerAttribManager("igk-ajx-lnk-form", { ns: "ajx", desc: "ajax link. used in combination with igk-ajx-data properties" });
        igk.ctrl.bindAttribManager("igk-ajx-lnk-form", function(n, m) {
            var q = this;
            var v = this.getAttribute("href");
            if (m && v) {
                q.reg_event("click", function(evt) {
                    evt.preventDefault();
                    var t = $igk(q.getAttribute("igk-ajx-lnk-tg")).first();
                    var frm = q.getParentForm();
                    if (frm) {
                        var fc = null;
                        if (t)
                            fc = igk.ajx.fn.replace_node(t.o);
                        igk.ajx.postform(frm, v, fc);
                    }
                });
            }
        })
    })();
    (function() {
        igk.ctrl.registerAttribManager("igk-js-cn", { ns: 'js', desc: "igk clone node target" });
        igk.ctrl.bindAttribManager("igk-js-cn", function(n, a) {
            var q = $igk(a);
            if (q) {
                var b = q.clone();
                b.o["id"] += "-cn";
                this.o.parentNode.replaceChild(b.o, this.o);
            }
        });
    })();
    // igk-toggle
    (function() {
        igk.ctrl.registerAttribManager("igk-js-toggle", { ns: 'js', desc: "igk toggle property on click" });
        igk.ctrl.bindAttribManager("igk-js-toggle", function(n, a) {
            if (!a) return;
            var p = igk.JSON.parse(a);
            var self = this;
            var co_id = this.getAttribute("igk-js-toggle-cookies");
            if (!p.name)
                p.name = "class";
            var pt = this.select(p.parent).first();
            if (!pt)
                return;
            var q = pt.select(p.target).first();
            var self = this;
            if (!q) {
                igk.show_notify_error(n + " [not found]", "can't notify error");
                return;
            }

            function __click(evt) {
                evt.preventDefault();
                switch (p.name) {
                    case 'class':
                    default:
                        if (igk.system.regex.item_match_class(p.data, q.o)) {
                            q.rmClass(p.data);
                            pt.addClass("igk-toggle");
                            if (co_id) {
                                igk.web.setcookies(co_id, 1);
                            }
                        } else {
                            q.addClass(p.data);
                            pt.rmClass("igk-toggle");
                            if (co_id) {
                                igk.web.rmcookies(co_id);
                            }
                        }
                        if (p.complete)
                            p.complete.apply(self);
                        break;
                }
            }
            if (this.istouchable()) {
                this.reg_event("touchend", __click);
            } else
                this.reg_event("click", __click);
            if (co_id) {
                var s = igk.web.getcookies(co_id);
                if (s) {
                    this.o.click();
                }
            }
        });
    })();
    // disable-selection
    (function() {
        igk.system.createNS("igk.ctrl.selectionmanagement", {
            disable_selection: function(t) {
                t = $igk(t).o;
                if (typeof(t.onselectstart) != "undefined")
                    t.onselectstart = function() { return !1; };
                if (typeof(t.style.MozUserSelect) != "undefined")
                    t.style.MozUserSelect = "none";
                t.onblur = function() { return !1; };
                t.ondragstart = function() { return !1; };
                $igk(t).fn.noselection = 1;
            },
            enableSelection: function(t) {
                t = $igk(t);
                if (t.fn.noselection) {
                    t.o.onblur = null; //function () { return !1; };
                    t.o.ondragstart = null; //function () { return false };
                    if (typeof(t.o.onselectstart) != "undefined")
                        t.o.onselectstart = null; // function () { return !1; };
                    if (typeof(t.o.style.MozUserSelect) != "undefined")
                        t.o.style.MozUserSelect = "";
                    delete(t.fn.noselection);
                }
            },
            clearSelection: function() { //clear selction 
                var sl = window.getSelection();
                if (sl.rangeCount > 0) {
                    for (var i = 0; i < sl.rangeCount; i++) {
                        sl.removeRange(sl.getRangeAt(i));
                    }
                }
            },
            initnode: function() { // init node for selection management
                var q = this;
                var source = this.getAttribute("igk-js-anim-over");
                var store = {};
                if (source) {
                    var t = eval("new Array(" + source + ")");
                    for (var m in t[0]) {
                        store[m] = q.getComputedStyle(m);
                    }
                    this.reg_event("mouseover", function(evt) {
                        if (q.supportAnimation()) {
                            var d = igk.JSON.parse(source);
                            q.setCss({ transition: 'all 0.5s ease-in-out' })
                                .setCss(d);
                        } else {
                            eval("q.animate(" + source + ");");
                        }
                    });
                    this.reg_event("mouseleave", function() {
                        if (q.supportAnimation()) {
                            q.setCss({ transition: 'all 0.2s ease-in-out' }).setCss(store).timeOut(300, function() {
                                q.setCss({ transition: null });
                            });
                        } else {
                            q.animate(store, t[1]);
                        }
                    });
                }
            }
        });
        igk.ctrl.bindAttribManager("igk-node-disable-selection", function() {
            var s = igk.system.convert.parseToBool(this.getAttribute("igk-node-disable-selection"));
            if (s == true) {
                var q = this.o;
                igk.ctrl.selectionmanagement.disable_selection(q);
            }
        });
    })();
    // disable context menu attribute
    (function() {
        igk.ctrl.bindAttribManager("igk-no-contextmenu", function() {
            this.on("contextmenu", function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
        }, {
            desc: 'disable context menu on node'
        });
    })();
    // parent scroll marker
    (function() {
        igk.winui.initClassControl("igk-parentscroll", function() {
            var q = this;
            // TASK: remove scroll parent 
            // q.reg_event("scroll", function (evt) {
            // 	igk.publisher.publish("sys://html/doc/scroll", { target: this, args: evt });
            // });
        });
    })();
    //----------------------------------------------------
    // igk-js-autofix attribute data bidning
    //----------------------------------------------------
    (function() {
        igk.ctrl.registerAttribManager("igk-js-autofix", { ns: 'js', desc: "auto fix position of element" });
        igk.ctrl.bindAttribManager("igk-js-autofix", function(m, n) {
            if (!n)
                return;
            var o = igk.JSON.parse(n); // 1|{left: length, top: length, offset: }
            var c = null;
            var autofixcallback = this.getAttribute("igk:autofixcallback") || function() {};
            if (o == 1) {
                o = { target: null, style: null, offset: 0 };
                var fixs = this.getAttribute("igk-autofix-style");
                o.style = igk.JSON.parse(fixs) || null;
            } else if (typeof(o) != 'object') {
                return;
            }
            if (!o.target) c = this.select("^.igk-parentscroll").first();
            else c = this.select(o.target).first();
            if (!c)
                return;
            if (!o.style)
                o.style = { left: "0px", top: "0px" };
            var t = this;
            var tc = igk.createNode("div");
            tc.setHtml("&nbsp;").addClass("no-visibility");
            var oldStyle = {};
            for (var s in o.style) {
                oldStyle[s] = this.getComputedStyle(s);
            }

            function __initview() {
                var g = t.getScreenLocation();
                var npos = 0;
                if (npos == 'fixed') {
                    if (c.o.scrollTop <= o.offset) {
                        t.rmClass("posfix");
                        t.setStyle(oldStyle);
                        autofixcallback(1);
                    }
                } else {
                    if (c.o.scrollTop > o.offset) {
                        t.addClass("posfix");
                        if (o.style) {
                            t.setCss(o.style);
                        }
                        autofixcallback(2);
                    }
                }
                // alert("top : "+o.style.top);
                // if (c.o.scrollTop > o.offset) {
                // // $igk(t.o.parentNode).prependChild(
                // if (!igk.system.regex.item_match_class("posfix", t.o)) {
                // t.o.parentNode.insertBefore(tc.o, t.o);
                // tc.setCss({ height: t.getHeight() + "px" });
                // t.addClass("posfix_i")
                // .setCss(o.style)
                // .setCss({'width': tc.getWidth() + "px" });
                // }
                // }
                // else {
                // t.rmClass("posfix").setCss({
                // left: 'auto',
                // top: 'auto',
                // width: 'auto'
                // });
                // tc.remove();
                // }
            };
            $igk(c).reg_event("scroll", function(evt) {
                __initview();
            });
            igk.ready(function() {
                __initview();
            });
        });
    })();


})();