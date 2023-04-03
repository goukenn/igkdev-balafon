// author: C.A.D. BONDJE DOUE
// file: ctrl.js
// @date: 20230102 14:16:01
// @desc: 

'use strict';

(function () {
    const createNS = igk.system.createNS;
    const is_string = igk.is_string;
    // ---------------------------------------------------------
    // script controller entity
    // ---------------------------------------------------------

    var m_controllers = []; // list of controller
    var m_initialize = false; // initialized or noted
    // attributes data
    var m_attrib_datas = {
        "igk-type": { n: "igk", desc: "Declare type. attribute is used as Type" },
        "igk-js-anim-over": { n: "js", desc: "execute anim on mouseover and mouse release", code: "igk-js-anim-over=\"{backgroundColor: 'red'},{ anim property....}\"" },
        "igk-js-anim-focus": { n: "js", desc: "execute anim on fucus ans blur" },
        "igk-js-eval": { n: "js", desc: "used to eval script in current context" },
        "igk-js-eval-init": { n: "js", desc: "used to eval script in current context after document is ready" },
        "igk-js-init-uri": { n: "js", desc: "uri that will be invoke on document ready" },
        "igk-js-bodyheight": { n: "js", desc: "indicate that this node must target the body height" },
        "igk-node-disable-selection": { n: "node", type: "attribute", desc: "node can be selelected. default is true" },
        "igk-article-options": { n: "ctrl", desc: "indicate must be considered as an article options" },
        "igk-ctrl-options": { n: "ctrl", desc: "indicate must be considered as a controller options node" },
        "igk-input-focus": { n: "js", desc: "force the cibling node to get focus" },
        "igk-input-data": { n: "js", desc: "setup the data used to validate the input" },
        "igk-form-validate": { n: "js", desc: "used with form to indicate that the form must be validate before submit." },
        "igk-js-fix-height": { n: "js", desc: "fix element height to target height. value is a id of the element" },
        "igk-js-fix-width": { n: "js", desc: "fix element width to target width. value is a id of the element" },
        "igk-js-fix-size": { n: "js", desc: "fix element size to target size. value is a id of the element" },
        "igk-js-fix-prop": { n: "js", desc: "fix element properties on size.expect property wait: {target,prop} " },
        "igk-js-fix-eval": { n: "js", desc: "fix element properties on window resize property wait: {update:function()} " },
        "igk-ajx-form": { n: 'js', desc: 'bool indicate that a form is used in ajx context. mixed(0|1|[{complete:func,targetid:[id of response node]}]. used igk-ajx-form-targedid to indicated the response node' },
        // select utility data
        "igk-js-bind-select-to": { n: "js", desc: "bind select to data from target ref #id,passing object refid or json: {id:#sample,selected:value,usecustom:0|1,allowempty:1|0,emptyvalue:value}" },
        "igk-ajx-form-target": { n: 'js', type: 'attr', desc: 'used in form to set the target of the form response,(select|=|::) where=is for the current form,:: if for the entire document ' }
    };
    var m_ctrl_types = {
        "igk-ctrl-options": { n: "igk", desc: "declare controller options" },
        "igk-ctrl": { n: "igk", desc: "declare a controller" }
    };
    var m_attribManager = []; // attrib manager
    var m_h_ctrl = {}; // hosted controller functions
    var m_readylist = null;
    var m_callflag = 0;

    function __attribToString() {
        return "igk.ctrl.attribManagerInfo";
    };

    function __registerAttribManager(key, callback) {
        if (callback && key) {
            var e = null;
            if (m_attribManager[key])
                e = m_attribManager[key];
            else {
                e = { k: key, s: new Array(), toString: __attribToString };
                m_attribManager[key] = e;
                m_attribManager.push(e);
            }
            e.s.push(callback);
        }
    };

    function __callReadyFunc(n, de) {
        if (!n || (m_readylist == null) || m_callflag) {
            return;
        }
        var e = null;
        var i = m_readylist.getCount();
        var j = 0;
        m_callflag = 1;
        for (j = 0; j < m_readylist.getCount(); j++) {
            e = m_readylist.getItemAt(j);
            e.apply(n, [de]);
        }
        m_callflag = 0;
        if (i != j)
            console.debug("after call to ready list " + m_readylist.getCount());
    }

    function __readyFuncEventArgs() {
        var m_preventContinue = false;
        igk.appendProperties(this, {
            preventContinue: function () {
                m_preventContinue = !0;
            },
            getPreventContinue: function () {
                return m_preventContinue;
            }
        });
    };
    var m_attribCtrl = [];

    function __loadAttribCtrl(n, k) {
        // >node 
        // >key
        if (!(k in m_attribCtrl)) {
            m_attribCtrl[k] = [];
        }
        m_attribCtrl[k].push(n);
    }

    function __callBindAttribData(node) {
        var q = $igk(node);
        var cnf = q.getConfig("igk:callAttribBindingData");
        if (cnf == 1) {
            return;
        }
        var re = new __readyFuncEventArgs();
        // call ready function on node
        __callReadyFunc(node, re);
        if (re.getPreventContinue()) {
            return;
        }
        var s = q.getAllAttribs();
        var r = null;
        var e = null;
        if (typeof (s) == igk.constants.undef)
            return;

        function __invoke(key, tab, node) {
            var e = null;
            var n = $igk(node);
            var attr = null;
            for (var i = 0; i < tab.length; i++) {
                e = tab[i];
                try {
                    attr = n.getAttribute(key);
                    e.apply(n, [key, attr]);
                    __loadAttribCtrl(n, key);
                } catch (ex) {
                    console.error(ex);
                    igk.winui.notify.showErrorInfo("Exception",
                        "__invoke::Binding ::: " + key + " <br />" + ex +
                        "<p class=\"stack\" style=\"max-height: 240px; font-family:'courier new'; line-height:1.5; font-size:8pt; overflow: auto\">" + ex.stack.split("\n").join("<br />") + "</p>"
                        //+ "<div class=\"source\" >From : Source Code</div><pre style=\"max-height:200px; overflow-y:auto;\">" + e + "</pre>
                        +
                        "");
                }
            }
        };
        var fc = node.hasAttribute || function (n) {
            return node.getAttribute(n) != null;
        };
        for (var i = 0; i < m_attribManager.length; i++) {
            e = m_attribManager[i];
            r = new RegExp("(" + e.k + ")");
            if (r.test(s) && fc.apply(node, [e.k])) {
                __invoke(e.k, e.s, node);
            }
        }
        // if (igk.BINDING)
        // mark it as binding data
        q.setConfig("igk:callAttribBindingData", 1);
    }

    function __clearBindAttribData() {
        m_attribManager = [];
    }

    function __ctrl_utility_functions(n) { // controler utility function access with "igk/nodeobj".fc
        var m_o = n;
        igk.defineProperty(this, "o", {
            get: function () {
                return m_o;
            }
        });
        igk.appendProperties(this, {
            toString: function () {
                return "[object igk.ctrl.utility.function]";
            }
        });
    }
    createNS("igk.ctrl", {
        getAttribCtrl: function (k) {
            return m_attribCtrl[k] || null;
        },
        /**
         * retrieve used attribute list
         * @returns array
         */
        getAttribCtrlList: function () {
            var t = [];
            for (var i in m_attribCtrl) {
                if (i == "length")
                    continue;
                t.push(i);
            }
            return t;
        },
        bindAttribManager: function (key, callback, setting) {
            if (setting && !(key in m_attrib_datas)) {
                igk.ctrl.registerAttribManager(key, setting);
            }
            if (m_attrib_datas[key] && callback) {
                __registerAttribManager(key, callback);
            } else {
                igk.winui.notify.showMsg("<div class=\"igk-notify igk-notify-danger\">" +
                    "Error No Binding Attrib registrated for \"" + key +
                    "\".<br/><div>You must register it before call the bindAttribManager function. igk.ctrl.registerAttribManager");
            }
        },
        // bind function to call when document page loaded before ready
        // bindPreloadDocument: function(name, callback) {
        //     __registerHtmlPreloadDocumentCallBack(name, callback);
        // },
        getController: function (item) { // get the parent controller
            if (item == null)
                return null;
            if (item.getAttribute && (item.getAttribute("igk-type") == "controller"))
                return item;
            else
                return igk.ctrl.getcontroller(item.parentNode);
        },
        confirm: function (item, msg, uri) { // used to confirm frame
            if ((window.confirm) && window.confirm(msg)) {
                item.form.confirm.value = 1;
                item.form.action = uri;
                return !0;
            }
            return !1;
        },
        init_controller: function () {
            // init controller on first page loading	
            if (m_initialize)
                return;
            // init all visible ctrl
            var d = document.getElementsByTagName("*");
            var id = null;
            var s = null;
            m_controllers.length = 0; // clear m_controller
            if (d) { // element found		
                for (var i = 0; i < d.length; i++) {
                    s = d[i];
                    if (s.getAttribute("igk-type") == "controller") {
                        id = s.getAttribute("igk-type-id");
                        m_controllers.push(s);
                        if (id) {
                            // register controllers
                            m_controllers[id] = s;
                        }
                        $igk(s).fc = new __ctrl_utility_functions($igk(s));
                    }
                }
                igk.ready(function () {
                    // bind attribute data on document ready
                    igk.ctrl.callBindAttribData(igk.dom.body().o);
                });
            }
            m_initialize = !0;
        },
        // controller function
        getCtrlById: function (id) {
            if (m_controllers[id])
                return m_controllers[id];
            return null;
        },
        findCtrlById: function (id) {
            if (m_initialize) {
                return igk.ctrl.getCtrlById(id);
            }
            var d = document.getElementsByTagName("*");
            var id = null;
            var s = null;
            m_controllers.length = 0;
            if (d) { // element found		
                for (var i = 0; i < d.length; i++) {
                    s = d[i];
                    if (s.getAttribute("igk-type") == "controller") {
                        id = s.getAttribute("igk-type-id");
                        if (id == id) {
                            return s;
                        }
                    }
                }
            }
            return null;
        },
        // get all controller
        getCtrls: function () {
            return m_controllers;
        },
        isregCtrl: function (q) {
            if ((q.nodeType == 1) && (q.getAttribute("igk-type") == "controller")) {
                var id = q.getAttribute("igk-type-id");
                if (id && !m_controllers[id]) {
                    m_controllers[id] = $igk(q);
                    $igk(q).fc = new __ctrl_utility_functions($igk(q));
                }
                return q;
            }
            return null;
        },
        getParentController: function (node) {
            // go to parent until parent controller is found
            if ((node == null) || (node.parentNode == null))
                return null;
            var q = node.parentNode;
            while (q != null) {
                if (igk.ctrl.isregCtrl(q)) {
                    return q;
                }
                q = q.parentNode;
            }
            return q;
        },
        invokeAttribEventData: function (node, attrib_name, value) {
            if (attrib_name in m_attrib_datas) {
                // get function - to call
                let e = m_attribManager[attrib_name].s[0];
                if (e) {
                    new Promise((p, n) => {
                        e.apply(node, [attrib_name, value]);
                        p();
                    });
                    return true;
                }
            }
        },
        callBindAttribData: function (node) {
            // call binding to system on node	
            if (node) {
                __callBindAttribData(node);
                if (node.getElementsByTagName) {
                    var s = node.getElementsByTagName("*");
                    if (s) {
                        for (var i = 0; i < s.length; i++) {
                            igk.invokeAsync(__callBindAttribData, s[i]);
                        }
                    }
                } else {
                    switch (node.nodeType) {
                        case 3:
                        case 8: // comment
                            break;
                        default:
                            console.debug("/!\\ getElementsByTagName function not found : " + node.nodeType);
                            break;
                    }
                }
            }
        },
        getAttribData: function () { // get binding schema help
            return m_attrib_datas;
        },
        registerReady: function (func) {
            // register a callback function that will be called everytime new node is added to document in igk.ajx.fn.initnode chain.
            // initnode use it in 'igk.ready' callback to maintain chain. function will be call for every node.
            // note: if you whant your new created callback to be called on new document complete used igk.ajx.fn.registerNodeReady against
            if (m_readylist == null)
                m_readylist = new igk.system.collections.list();
            m_readylist.add(func);
        },
        registerAttribManager: function (name, options) {
            if ((name) && (typeof (m_attrib_datas[name]) == igk.constants.undef)) {
                m_attrib_datas[name] = options;
                return !0;
            }
            return !1;
        },
        isAttribManagerRegistrated: function (name) {
            if ((name) && (typeof (m_attrib_datas[name]) != igk.constants.undef)) {
                return !0;
            }
            return !1;
        },
        regCtrl: function (name, baseuri) { // register controller		
            m_h_ctrl[name] = new (function (name, baseuri) {
                this.name = name;
                this.baseuri = baseuri;
                igk.appendProperties(this, {
                    getUri: function (funcname) {
                        if (funcname) {
                            return this.baseuri + "&f=" + funcname;
                        }
                        return this.baseuri;
                    }
                });
            })(name, baseuri);
        },
        getRegCtrl: function (name) {
            return igk.get_v(m_h_ctrl, name, null);
        },
        getRegCtrls: function () {
            return m_h_ctrl;
        },
        initselect_model: function (t, o, model) { // target select output zone		
            var q = null;
            var to = null;
            var p = igk.getParentScript();
            if (is_string(t)) {
                q = $igk(p).select(t).first();
            } else {
                q = t;
            }
            if (is_string(o)) {
                to = $igk(p).select(o).first();
            } else
                to = o;
            if (!q || !to) {
                return;
            }

            function __changez(evt) {
                var v = q.o.value;
                var i = q.o.selectedIndex;
                var n = $igk(q.o[i]).getAttribute("model") || "default";
                var cl = null;
                if (model)
                    cl = model.select("." + n).first();
                else
                    cl = $igk(p).select(".igk-select-model ." + n).first();
                if (cl) {
                    to.setHtml(cl.getHtml());
                } else
                    to.setHtml("no data");
                // manual raise resize event
                igk.winui.events.raise(window, 'resize');
            };
            // -------------------------------------------------------------------------
            // 
            // -------------------------------------------------------------------------
            $igk(q).reg_event("change", __changez)
                .reg_event("keyup", function (evt) {
                    var kc = evt.keyCode || 0;
                    switch (kc) {
                        case 37: // LEFT
                        case 38: // UP
                        case 39: // RIGHT
                        case 40: // BOTTOM
                            __changez();
                            break;
                    }
                });
            __changez();
            // alert("bad===="+$igk(q).getParentBody());		
            if (q.o.form) {
                // restore the default view
                $igk(q.o.form).reg_event("reset", function () {
                    var s = q.select(">:option");
                    // get the first option
                    var i = s.first();
                    if (i) {
                        q.o.value = i.o.value;
                        __changez();
                    }
                });
            }
        }
    });
    var e = igk.winui.events.global();
    igk.winui.events.raise(e, 'igk_controller_ready', igk.ctrl, null);
    igk.winui.events.clean(e, 'igk_controller_ready');
    igk.winui.events.raise(e, 'igk_controller_ready', igk.ctrl, null);

    // + | --------------------------------------------------------------
    // + | bypass setattribute to get and replace attribute set behaviour

    let global = {};    
    global.setAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function (n, v) {
        // console.log("setting attributes ... ", n, v);
        if (/\[[^\]]+\]/.test(n)) {
            // globa environment management properties 
            let t = null; 
            if (typeof this.igk === undefined) {
                $igk(this).init();
            }
            t = $igk(this);
            var q = t.getAttribute(n);
            if (q == n) {
                return;
            }
            igk.ctrl.invokeAttribEventData(t, n, v);
            return;
        }
        global.setAttribute.apply(this, [n, v]);
    };


})();


(function () {
    const createNS = igk.system.createNS;

    function __append_frametobody(responseText) {
        var q = document.createElement("div");
        q.innerHTML = responseText;
        var c = q.childNodes[0];
        if (c) { // have node				
            igk.dom.body().appendChild(c);
            igk.ajx.fn.initnode(c);
            if (c.getElementsByTagName) {
                var tforms = c.getElementsByTagName("form")[0];
                var tc = $igk(c).select("form");
                // init default frame management		
                if (tc.each) {
                    tc.each(function () {
                        if (this.o.onsubmit == null) {
                            this.setProperties({
                                "onsubmit": function (evt) {
                                    // raised on submit									
                                    window.igk.ajx.postform(this, this.action.split("#")[0], function (xhr) {
                                        if (this.isReady()) {
                                            if (global) {
                                                this.replaceBody(xhr.resonseText);
                                            } else {
                                                if (m)
                                                    this.setResponseTo(m);
                                            }
                                        }
                                    });
                                    this.frame.close();
                                    evt.preventDefault();
                                    return !1;
                                }
                            });
                        }
                        return this;
                    });
                }
            }
        }
    };
    createNS("igk.ctrl.frames", {
        appendFrameResponseToBody: __append_frametobody,
        postframe: function (e, uri, ctrl) {
            var m = null;
            var t = $igk(e).select(ctrl).first(); // getParentByTagName("form");
            if (ctrl != null) {
                m = window.igk.ctrl.getCtrlById(ctrl);
            }
            // alert("m is "+m + " " + ctrl+ " "+window.igk.ctrl.getCtrlById(ctrl));
            igk.ajx.post(uri, null, function (xhr) {
                // igk.frames.post function			
                if (this.isReady()) {
                    if (t) {
                        t.setHtml(xhr.responseText).init();
                    } else {
                        igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                        // __append_frametobody(xhr.responseText);					
                    }
                }
            });
        }
    });
    // end igk.ctrl.frames
})();