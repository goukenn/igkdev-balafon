/*
Name:balafon.js
*/
// igk.js
// Author : C.A.D. BONDJE DOUE
// copyright: igkdev @ 2008-2016
// igkdev - balafon.js framework scripts utility
// 27-06-2013
// 18-02-2014 expose utility
// 23-05-14
// Release : 13-04-2016
// Release : 28/12/2017
// read fix for more info 
"use strict";
// + | -------------------------------------------------------------------------------------
// + | Core balafon.js definition
// + | -------------------------------------------------------------------------------------
(function(window) {
    if (typeof(window.igk) != 'undefined') {
        return;
    }
    let _options = {debug:false};
    let _sc = 0;
    if (document.scripts && (_sc = document.scripts[document.scripts.length-1])){
        var _location = _sc['src'];
        if (_location && (new URL(_location)).pathname.endsWith('/igk.js')){
            _options.debug = true;
        }
    } 
    // ----------------------------------------------------------------------------------------
    // defining namespace
    // ----------------------------------------------------------------------------------------
    const DEBUG = _options.debug;
    var m_scriptTag = null;
    var yes = !0;
    var no = false;
    var readyFunc = []; // store function to call when document complete. after this will be flush
    var m_readyGlobalFunc = [];
    var m_tag_obj = []; // component by tag name // tag will be converted to balafon.js usage.
    // and tag name will be attached to replaced tag managed by you
    var m_ready_calling = false; // flag for ready call
    // var __parentScript=null;
    var m_scriptNode = null; // store the execution script node. on eval context or running
    var __nsigk = null;
    var sm_regEventContext = new Array();
    var m_attribs = {}; // attributes for declaring namespace type
    // class constant
    var IGK_UNDEF = "undefined";
    var IGK_FUNC = "function";
    var IGK_CLASS = "class";
    var IGK_OBJECT = "object"; 
    var __eventObjectManager = null;
    var __libName = "BalafonJS";
    var __version = "4.0.18.0705";
    var __author = "C.A.D. BONDJE DOUE";
    var __devscript = "igk.js";
    var __lang = [];
    var __initDocSetting = 0;
    var __scriptsEval = {};
    var __platform = {};
    var __igk_settings = {
        nosymbol: 0
    }; // load setting
    var m_LibScript = "";
    var __debug_z = 0;
    var m_provider = ['webkit', 'ms', 'moz', 'o'];
    var _rgx = {
        attribs: /([^\s]+)\s*=\s*(((")([^"]*)("))|((')([^']*)(')))/g,
        ff: /Firefox\/([0-9]+\.[0-9]+)/,
        ios: /IOS/
    };
    var __isdev;
    var _context_; /* context mode : global_ready, xhr */
    __lang[0xEA001] = "failed to transform xml with xsl . {0}";
    var __global = null;


    function __init_console() {
        __debug_z = $igk("#debug-z").first();
        if (__debug_z) {
            var d = __igk_settings["console"];
            if (d) {
                for (var i = 0; i < d.length; i++) {
                    var dv = __debug_z.add('div');
                    switch (d[i].t) {
                        case -1:
                            dv.addClass('igk-danger');
                            break;
                        case 1:
                            dv.addClass('igk-info');
                        default:
                            break;
                    }
                    dv.setHtml(d[i].m);
                }
            }
        }
        delete(__igk_settings["console"]);
    };
    if (typeof(window.console) == IGK_UNDEF) {
        // alert("create console");
        window.console = {
            error: function(m) {
                __debug_z = __debug_z ? __debug_z : $igk("#debug-z").first();
                if (__debug_z) {
                    __debug_z.add('div').addClass("igk-danger").setHtml(m);
                } else {
                    // alert("Error : "+m);
                    var c = __igk_settings["console"] || [];
                    c.push({ t: -1, m: m });
                    __igk_settings["console"] = c;
                }
            }
        };
        m_readyGlobalFunc.push(__init_console);
    }
    if (typeof(console.debug) == IGK_UNDEF) {
        // alert("create debug");
        console.debug = function(m) {
            __debug_z = __debug_z ? __debug_z : $igk("#debug-z").first();
            if (__debug_z) {
                __debug_z.add('div').addClass("igk-info").setHtml(m);
            } else {
                // alert("/!\\ debug "+m);
                var c = __igk_settings["console"] || [];
                c.push({ t: 1, m: m });
                __igk_settings["console"] = c;
            }
            // var  g=$igk('#log.debug').first();
            // if(g){
            // g.add('div').setHtml(msg);
            // }
        };
    }
    (function() {
        let c = window.navigator.userAgentData;
        if (typeof(c) != IGK_UNDEF) {
            __platform.osType = c.platform;
            __platform.osAgent = c.brands.map(function(i) {
                return i.brand + "/" + i.version + " ";
            }).join();
        } else {
            // console.debug("OS use old ways ");
            __platform.osType = "Unknow";
            __platform.osAgent = navigator.userAgent;
        }
    })();

    function __dom_innerHTML(i) {
        if ("innerHTML" in i)
            return i.innerHTML;
        if ("nodeType" in i) {
            var k = 0;
            var s = "";
            // while(k<i.childNodes.length){
            // s +="d"+ __dom_innerHTML(i.childNodes[k]);
            // k++;
            // }
            return s;
        }
        return "/!\\ NotSupported";
    }
    /**
     * 
     */
    // + | Global function 
    function igk_object() {
        // define an igk balafonjs object
    };

    function igk_class() {
        // define an class type
    };
    igk_class.prototype = new igk_object();

    function igk_namespace() {
        // define a namespace object
    };

    function igk_winui_reg_event(item, method, func, useCapture) { // global	
        var g = method.split(' ');
        var s = 0;
        var o = 1;
        while (o && (s = g.pop())) {
            var eventHandler = igk.winui.getEventHandler(s);
            if (eventHandler != null) {
                o = o && eventHandler.reg_event(item, func, useCapture);
            } else o = o && igk_winui_reg_system_event(item, s, func, useCapture);
        }
    };


    igk_namespace.prototype = new igk_object();

    function igk_is_string(t) {
        return typeof(t) == 'string';
    };

    function igk_is_notdef(t) {
        return typeof(t) == IGK_UNDEF;
    };

    function igk_is_object(t) {
        return typeof(t) == 'object';
    };
    // register a component objet create by using tagname
    function igk_reg_tag_obj(n, p) {
        if (m_tag_obj[n]) {
            m_tag_obj[n] = p;
        } else {
            m_tag_obj[n] = p;
            m_tag_obj.push({ n: n, data: p });
        }
    };

    function igk_stop_event(ev) {
        ev.preventDefault();
        ev.stopPropagation();
    };
    /**
     * trim text
     * @param {*} m string 
     */
    function igk_str_trim(m) {
        if (m == null) {
            throw new igk.exception('bad');
        }
        if (m.trim)
            return m.trim();
        while (m && (m.length > 0) && (m[0] == ' ')) {
            m = m.substring(1);
        }
        while (m && (m.length > 0) && (m[m.length - 1] == ' ')) {
            m = m.substring(0, m.length - 1);
        }
        return m;
    };

    function igk_url(u) {
        return new(function() {
            this.uri = u;
        })();
    };

    function igk_is_coers(uri) {
        var cu = window.URL || igk_url;
        var c = 0;
        if (typeof(cu) == 'function') {
            c = new cu(uri);
        } else {
            if (typeof(URL.createObjectURL) != 'function')
                return -1;
            return 0;
        }
        if (igk.validator.isUri(uri)) {
            if ((window.location.protocol != c.protocol) ||
                (window.location.host != c.host)
            ) {
                return 1;
            }
            return 0;
        }
        return 0;
    };

    function igk_io_getData(uri, callback, mimetype) {
        if (!uri)
            return null;
        var _promise = new igk.system.Promise();
        if (igk_is_coers(uri)) {
            igk.ajx.get(uri, null, function(xhr) {
                if (this.isReady()) {
                    var _o = {
                        uri: uri,
                        source: "xhr",
                        "from": 'igk_io_getData',
                        data: null
                    };
                    _o.data = xhr.responseText;
                    callback(_o);
                }
            });
            return _promise;
        };
        // TODO : GET FILES - 
        if (/^file:\/\//.test(uri)) {
            // alert("loading from data file : "+uri);
            if (window.Fetch) {
                Fetch(uri).then(function() {}).error(function() {});
            }
            /// Get from the file service 
            // setTimeout(function () {
            // 	try{
            // 		var s = igk.invoke("IOGetFileContent", uri);				 
            // 		if (s && (s.length > 0)) {
            // 			if (callback) {
            // 				try {
            // 					callback({ data: s, source: 'IOGetFileContent', uri: uri });
            // 				}
            // 				catch (e) {
            // 					console.error("exception raise: " + e);
            // 				}
            // 			}
            // 		}
            // 	} catch(e){

            // 	}
            // }, 1);
            return;
        }
        if (igk.navigator.isIE()) {
            // ie force download
            var _o = {
                uri: uri,
                "from": 'igk_io_getData',
            };
            igk.ajx.get(uri, null, function(xhr) {
                if (this.isReady()) {
                    _o.data = xhr.responseText;
                    // _o.data = _data;
                    callback(_o);
                } else {
                    if ((xhr.readyState == 4) && (xhr.status != 200)) {
                        _o.data = null;
                        _o.errorCode = xhr.status;
                        callback(_o);
                    }
                }
            });
            return;
        };
        // console.debug("create an object to get data :::: ", uri);
        // return;
        var ob = igk.createNode('object');
        var __loaded = !1; // false;		
        ob.o.style.visibility = 'hidden';
        ob.o.style.position = 'absolute';
        ob.o.width = 4; // 1;
        ob.o.height = 4; // 1;		
        var _data_first = 0; // for object data need to setup data before added it to dom
        _data_first = igk.navigator.isChrome() || igk.navigator.isFirefox() || /Google Inc\./.test(navigator.vendor);
        if (igk.navigator.isIEEdge()) {
            ob.o.type = "text/xml";
        } else
            ob.o.type = mimetype || "text/xml";
        ob.addClass("bdr-1");
        ob.reg_event("load", function(evt) { 
            var _o = {
                uri: uri,
                source: ob.o,
                "from": 'igk_io_getData',
                data: null
            };
            if (callback) {
                var o = ob.o;
                if (ob.o.data == "") {
                    var e = callback.error;
                    if (e)
                        e();
                    else
                        callback(_o);
                } else {
                    var _data = '';
                    try {
                        if (ob.o.contentDocument) {
                            var c = (ob.o.contentDocument.firstElementChild || ob.o.contentDocument.firstChild);
                            _data = c.innerText || c.textContent;
                        } else {
                            throw new Error("no content document : " + uri + " for " + ob.o.type);
                        }
                        _o.data = _data;
                        callback(_o);
                    } catch (e) {
                        igk.log("Failed: loading ===> " + uri);
                        _o.error = 1;
                        _o.errormsg = igk.R.error_failedtoload;
                    }
                }
            }
            _promise.resolve();
            setTimeout(function() { ob.remove(); }, 100);
        }).reg_event("error", function(evt) {
            ob.remove();
            var e = callback.error;
            if (__loaded || (igk.navigator.isFirefox() && igk.navigator.getFirefoxVersion() >= 50)
                // igk.navigator.isIEEdge()
            ) {
                if (e)
                    e();
                else if (callback) {
                    callback({ source: ob, data: null, error: 1 });
                }
                _promise.reject();
                return;
            }
            if (e)
                e();
            else if (callback) {
                callback({ source: ob, data: null, error: 1 });
            }
            _promise.reject();
        });
        var _body = igk.dom.body();
        //always initialize first before append to body for loading
        ob.o.data = uri;
        _body.prepend(ob);
        return _promise;
    };
    // return a selection expreession object
    function igk_select_exp(p) {
        var m_items = [];

        function loader(t) {
            m_items.push(t);
        }
        var m = new RegExp("[ ]?[\.# ]{0,1}[^\.# ]+", "ig");
        // tip: use replace function to get all element that match the pattern
        p.replace(m, loader);
        var q = {
            // selector: null,
            pattern: p,
            getCount: function() {
                return m_items.length;
            },
            check: function(item, offset) {
                for (var i = offset; i < m_items.length; i++) {
                    var p = m_items[i];
                    switch (p[0]) {
                        case ".":
                            if (!igk_item_match_class(p.substring(1), item)) {
                                return !1;
                            }
                            break;
                        case "#":
                            if (item.id != p.substring(1)) {
                                return !1;
                            }
                            break;
                        case " ":
                            if (item.tagName.toLowerCase() != p.substring(1)) {
                                return !1;
                            }
                    }
                }
                return !0;
            },
            select: function(selector, item) { // select in expression
                if (!item)
                    return null;
                var pk = null;
                var v_it = null;
                var b = false;
                var depth = false; // to match the current item or good to childs			
                function __select(pk) {
                    if (!b) {
                        var sq = $igk(item).select(pk);
                        if (sq.getCount() > 0) {
                            selector.load(sq);
                        }
                        // igk.debug.assert(debug,"OK QQ "+sq + " "+selector);		
                    } else {
                        // filter selection					
                        var isel = null;
                        if (depth) {
                            isel = selector.select(pk);
                        } else {
                            isel = selector.select('?' + pk);
                        }
                        selector.clear();
                        selector.load(isel);
                    }
                    b = true;
                }
                for (var j = 0; j < m_items.length; j++) {
                    v_it = null;
                    pk = m_items[j];
                    switch (pk[0]) {
                        case ".": // select by class	
                        case "#": // select by id							
                            __select(pk);
                            continue;
                        case ' ': // select 
                            while ((pk = pk.substring(1)) && (pk[0] == ' ')) {}
                            if (pk) {
                                depth = !0;
                                __select(pk);
                                depth = false;
                            }
                            break;
                        case "$":
                            console.debug("is dollar error. not implement");
                            break;
                        default:
                            break;
                    }
                }
                if (!b) {
                    var v_it = item.getElementsByTagName(p);
                    if (v_it) {
                        for (var i = 0; i < v_it.length; i++) {
                            var s = v_it[i];
                            if (this.check(s, 1)) {
                                selector.push(s);
                            }
                        }
                    }
                }
            }
        };
        if (q.getCount() == 0)
            return null;
        return q;
    }

    function igk_getScriptLocation() {
        var idx = 0;
        var e = 0;

        function _readUri(h, starti) {
            var tg = h.substring(starti).split(':');
            var uri = '';
            if (tg.length > 2) {
                uri = tg.slice(0, tg.length - 2).join(':');
            }
            idx = starti + uri.length;
            return uri;
        };

        function _readChromeUri(h) {
            var idx = h.indexOf('(');
            var ch = 0;
            if (idx != -1) {
                idx = 1;
                var depth = 1;
                while ((depth > 0) && idx < h.length) {
                    ch = h[idx];
                    if (ch == ')')
                        depth--;
                    else if (ch == '(') {
                        depth++;
                        uri = '';
                    } else
                        uri += ch;
                    idx++;
                }
                var tb = uri.trim().split(':');
                if (tb.length > 2) {
                    uri = _readUri(uri, 0);
                    l = tb[tb.length - 2];
                    c = tb[tb.length - 1];
                }
            } else {
                var tb = h.split(" ");
                h = tb[tb.length - 1];
                uri = _readUri(h, 0);
                l = h.substring(idx + 1, idx = h.indexOf(':', idx + 1));
                c = h.substring(idx + 1).trim();
            }
            var e = {
                location: uri,
                line: l,
                column: c
            };
            return e;
        };
        var _nav = igk.navigator;
        var uri = "";
        var c = 0;
        var l = 0;
        try {
            throw new Error('[BJS]:getscriptlocation');
        } catch (ex) {
            if (ex.stack) {
                // stack is different from browser. 
                // FF: 
                //	 - start at indice 1 - mark this entrie data
                //Chrome and IEEdge set to error message
                //safari act strange:
                //our goal is to convert stact to interpreted exploitable data in oder to get code and line
                var tdb = (ex.stack + '').split('\n');
                var idx = 1;
                // the first stack line must be ignored because is where the exception is throw.
                var stackinfo = [];
                if (/^Error:/.test(tdb[0]))
                    idx++;
                if (_nav.isChrome())
                    idx++;
                //push only available data that not concern eval data expression as <uri>:<line>:<number>
                while ((stackinfo.length == 0) && (idx < tdb.length)) {
                    // ie - eval stack line
                    // firefox stack line
                    if (/(at eval code|> eval:)/.test(tdb[idx])) {
                        idx++;
                        continue;
                    }
                    // /(([^: ]+)@){0,1}([^ ]+):([0-9]+):([0-9]+)/
                    tdb[idx].replace(/([@ ]{0,1})([^ \(\)]+):([0-9]+):([0-9]+)/, function(x, b, u, l, c) {
                        var p = 0;
                        var _eval = 0;
                        var _cidx = -1;
                        if (u == 'eval') {
                            //firefox invoke eval on line 
                            //firefox current stack script @uri line l > eval
                            _eval = 1;
                        }
                        if ((_cidx = u.indexOf('@')) != -1) {
                            //for firefox
                            u = u.substr(_cidx + 1);
                        }
                        stackinfo.push({
                            src: tdb[idx],
                            text: x,
                            'func': p,
                            location: u,
                            line: parseInt(l || '0'),
                            column: parseInt(c || '0'),
                            index: idx,
                            'eval': _eval,
                            stack: tdb
                        });
                        return tdb[idx];
                    });
                    idx++;
                }
                if (stackinfo.length == 1) {
                    e = stackinfo[0];
                }
                //return;
                // var tdb = (ex.stack+'').split('\n');
                // var cidx = 1;
                // var h = tdb[idx];
                // var is_chrome = _nav.isChrome(); //safari edge return chrome
                // if (/^Error/.test(h)){
                // //ie and chrome promt Error tag on stack
                // cidx ++;
                // h = tdb[cidx].trim();
                // while( (cidx <= (tdb.length-1)) && /eval code/.test(h)) {//edge evaluation colode
                // cidx++;
                // h = tdb[cidx].trim();
                // }
                // if(h.startsWith("at")){
                // e = _readChromeUri(h);
                // return e;
                // }
                // return null;
                // }
                // cidx++;
                // h= tdb[cidx].trim();
                // // return null;
                // if (!_nav.isIEEdge() && ( _nav.isFirefox() || _nav.isSafari())) {
                // e = {
                // func: h.substring(0, idx = h.indexOf('@', 0)),
                // location: _readUri(h, idx + 1),
                // line: h.substring(idx + 1, idx = h.indexOf(':', idx + 1)),
                // column: h.substring(idx + 1)
                // };
                // }
                // else {
                // // edge and contain '('
                // e = {
                // func: h.substring(idx = h.indexOf(' at ') + 4, idx = h.indexOf('(', idx)).trim(),
                // location: _readUri(h, idx + 1),
                // line: h.substring(idx + 1, idx = h.indexOf(':', idx + 1)),
                // column: h.substring(idx + 1, h.indexOf(')', idx + 1))
                // }
                // }
                // } else {
                // //old safari does not support stack property
                // return null;
                // }
            }
        }
        return e;
    }
    // p:propertie
    // n:dom node
    function igk_item_match_class(p, n) {
        if (!n)
            return !1;
        var q = new RegExp("(\\s|^)(" + p + ")(\\s{1}|$)", "i");
        if (q.exec("" + n.className)) {
            return !0;
        }
        return !1;
    };

    function igk_item_inherit_class(p, n) {
        var q = n;
        while (q) {
            if (igk_item_match_class(p, q)) {
                return !0;
            }
            q = q.parentNode;
        }
        return !1;
    };

    function igk_freeEventContext() {
        igk_unreadyAll();
        if (sm_regEventContext.length > 0) {
            igk_clearEventContent(sm_regEventContext);
            if (sm_regEventContext.length != 0) {
                console.debug("[igk.js] igk_freeEventContext [for some reson not cleared]");
            }
        }
    }

    function igk_show_notify_error(t, m) {
        igk.winui.notify.showErrorInfo(t, m);
    };

    function igk_show_notify_msg(t, m, c) {
        igk.winui.notify.showMsBox(t, m, c);
    };

    function igk_get_script_src() {
        if (typeof(script_src_lnk) != IGK_UNDEF) {
            return script_src_lnk;
        }
        var p = igk_getLastScript();
        if (p) {
            return p.getAttribute("src");
        }
        return null;
    }

    function igk_unRegEventContext(chain) {
        var r = sm_regEventContext.length;
        var s = [];
        for (var i = 0; i < r; i++) {
            if (sm_regEventContext[i] == chain) {
                // register chain
                s.push(sm_regEventContext[i]);
            }
        }
        sm_regEventContext.pop(chain);
        return (r > sm_regEventContext.length);
    }

    function igk_get_html_item_definition_value(item) {
        if (item == null)
            return "<div class=\"igk-notify igk-notify-danger\">item is null</div>";
        var msg = "";
        var func = "";
        var e = igk.createNode("div");
        msg += "<div><div class=\"igk-notify-title\" style=\"color:white;\">" + item.toString() + "</div><div class=\"igk-title-4\">Properties</div><ul>";
        func += "<div class=\"igk-title-4\">Functions</div><ul>";
        var tab = [];
        for (var i in item) {
            tab.push(i);
        }
        igk.system.array.sort(tab);
        // for(var i in item)
        for (var i = 0; i < tab.length; i++) {
            try {
                var n = tab[i];
                var txt = "<div class=\"dispb igk-bigger\">" + n + "=</div>";
                if (typeof(item[n]) == IGK_FUNC) {
                    func += "<li class=\"igk-col-lg-12-2\" >" + txt + "</li>";
                } else
                    msg += "<li class=\"igk-col-lg-12-2\" >" + txt + "<pre class=\"dispb\" style='background-color:white;'>" + item[tab[i]] + "</pre>" + "</li>";
            } catch (ex) {
                igk.winui.debug.addMsg("error i :" + i + " : " + ex);
            }
        }
        func += "</ul><div class=\"disptable clearb\"> </div></div>";
        msg += "</ul><div class=\"disptable clearb\"> </div></div>";
        e.setHtml(msg + func);
        return e.o.outerHTML;
    }

    function igk_get_html_item_definition(item) {
        if (item == null)
            return "<div class=\"igk-notify igk-notify-danger\">item is null</div>";
        var msg = "";
        var func = "";
        var e = igk.createNode("div");
        msg += "<div><div class=\"igk-notify-title\" style=\"color:white;\">" + item + "</div><div class=\"igk-title-4\">Properties</div><ul>";
        func += "<div class=\"igk-title-4\">Functions</div><ul>";
        var tab = [];
        // push item in table
        for (var i in item) {
            tab.push(i);
        }
        igk.system.array.sort(tab);
        for (var i = 0; i < tab.length; i++) {
            try {
                var n = tab[i];
                if (typeof(item[n]) == IGK_FUNC) {
                    func += "<li class=\"igk-col-lg-5-1\" >" + n + "</li>";
                } else
                    msg += "<li class=\"igk-col-lg-5-1\" >" + n + "</li>";
            } catch (ex) {
                igk.winui.debug.addMsg("error i :" + i + " : " + ex);
            }
        }
        func += "</ul><div class=\"disptable clearb\"> </div></div>";
        msg += "</ul><div class=\"disptable clearb\"> </div></div>";
        e.setHtml(msg + func);
        return e.o.outerHTML;
    }

    function igk_getRegEventContextByOwner(o) {
        if (o == null)
            return null;
        var k = sm_regEventContext;
        for (var i = 0; i < k.length; i++) {
            if (k[i].o == o)
                return k[i];
        }
        return null;
    }

    function igk_getRegEventContext(prop, reg, callback) { // igk-properties,reg,callback
        var k = sm_regEventContext;
        for (var i = 0; i < k.length; i++) {
            if (k[i].properties == prop)
                return k[i];
        }
        if (reg) {
            var c = callback();
            sm_regEventContext.push(c);
            return c;
        }
        return null;
    }

    function igk_clearEventContent(context) {
        var ctab = new Array();
        var k = context;
        if ((k == null) || (k.length == 0))
            return;
        // copy 
        for (var i = 0; i < k.length; i++) {
            ctab.push(k[i]);
        }
        // clear
        for (var j = 0; j < ctab.length; j++) {
            var r = ctab[j]; // get chain
            if (r.unregEventContext) {
                r.unregEventContext();
            }
        }
    }

    function igk_unreadyAll() {
        var tab = readyFunc;
        for (var i = 0; i < tab.length; i++) {
            igk.unready(tab[i]);
        }
        readyFunc = [];
    }
    // ----------------------------------------------------------------------------------------
    // igk html utility function
    // ----------------------------------------------------------------------------------------
    function a(a) {
        return IGK_UNDEF === typeof a;
    }

    function b(b) { // check if b exist
        // return (s in b) && (a(b[s]) ||(null===b[s]));
        return a(b) || (null === b);
    }

    function igkJSError(msg) {
        console.debug("igkJSError: " + typeof(this));
        this.name = "igkerror";
        this.message = msg;
        this.level = 1;
        this.toString = function() { return this.message; };
    }

    function __igksetAttribute(ns, n, v) {
        var d = ns.__igk__.attr; // __ATTRIBS__;
        if (d)
            d[n] = v;
    };

    function igk_getCurrentScript() {
        if (m_scriptNode)
            return m_scriptNode;
        if (document.currentScript)
            return document.currentScript;
        // fallback
        var s = document.scripts;
        return s.length > 0 ? s[s.length - 1] : null;
    }

    function igk_getAjxInitiator() {
        return __ajxInitiator;
    }

    function igk_namespaceBuilder(t, a, c, d, callback) {
        var h = 0;
        var i = "";
        var ns = "";
        var ps = "";
        var win = t;
        var _iswin = (t == window); // if not window adding property to object
        if (typeof(win) == 'undefined') {
            throw 'win is not defined';
        }
        var _fileSrc = (d && d.fileSrc) || igk_getCurrentScript().src;
        if ((_fileSrc == "") && (_iswin)) {
            _fileSrc = "@" + m_LibScript;
        }
        this.build = function() {
            function __igksysNameSpace(n, ps) {
                var m_type = new function() {
                    igk_appendProp(this, {
                        getFullName: function() {
                            return n;
                        },
                        toString: function() { return "class:igk:systemType"; }
                    });
                };
                var tn = new igk_namespace();
                var mps = ps;
                var _hierarchie = {
                    getParent: function() {
                        if (mps)
                            return igk_get_namespace(mps);
                        return null;
                    }
                };
                igk_defineProperty(tn, "namespace", { get: function() { return n; }, set: function() { throw "not allowed"; } });
                igk_defineProperty(tn, "type", { get: function() { return IGK_CLASS; } });
                igk_defineProperty(tn, "fullname", { get: function() { return n; } });
                igk_defineProperty(tn, "hierarchie", { get: function() { return _hierarchie; } });
                igk_appendProp(tn, {
                    // namespace: n,
                    // type: IGK_CLASS,
                    // fullname: n,
                    // hierarchie:,
                    getParent: function() {
                        return this.hierarchie.getParent();
                    },
                    getType: function() {
                        return m_type;
                    },
                    toString: function() {
                        return "namespace[" + this.namespace + "]";
                    }
                });
                igk_defineProperty(tn, "__igk__", {
                    get: function() {
                        const p = {
                            "src": _fileSrc,
                            "attr": d,
                            "namespace": this.namespace,
                            "hierarchie": _hierarchie
                        };
                        return p;
                    }
                });
                return tn;
            };
            for (var f = 0 <= a.indexOf(".") ? a.split(".") : [a], h = 0; h < f.length; h++) {
                i = f[h];
                ps = ns;
                if (h > 0)
                    ns += ".";
                ns += i;
                (!(i in win) || (typeof(win[i]) == 'undefine') || b(win[i])) && (win[i] = __igksysNameSpace(ns, ps));
                win = win[i];
            }
            if (d) {
                // add addiional definition properties		
                m_attribs[a] = d;
            }
            if (!b(c)) {
                igk_appendProp(win, c);
                if (callback)
                    callback(win);
            }
            return win;
        };
        // return;
        // var _fileSrc=(d && d.fileSrc) || igk_getScript().src;
    }
    // >@ create private namespace
    function createPNS(t, a, c, d, callback) {
        if (!a || (typeof(a) != 'string'))
            throw new igkJSError("[igk] - wrong argument: namespace " + a);
        var g = new igk_namespaceBuilder(t, a, c, d, callback);
        return g.build();
    }
    // convert string namespace to balafonJS object
    function igk_get_namespace(n) {
        if (typeof(n) != 'string')
            return null;
        var t = n.split(".");
        var win = window[t[0]];
        if (win) {
            for (var i = 1; win && i < t.length; i++) {
                win = win[t[i]];
            }
            return win;
        }
        return null;
    }

    function igk_console_debug(msg) {
        if (igk.DEBUG && console && console.debug) {
            console.debug(msg);
        }
    }
    // register console if not defined
    if (typeof(console) == IGK_UNDEF) {
        createNS("window", {
            console: new(function() {
                igk_appendProp(this, {
                    write: function(msg) {
                        alert(msg);
                    },
                    debug: function(m) {
                        // alert(m);
                    }
                });
            })()
        });
    }
    // register getComputedStyle if not defined
    if (typeof(getComputedStyle) == IGK_UNDEF) {
        createNS("window", {
            getComputedStyle: function(item, selector) {
                if (!item) return null;

                function __getstylev(i, p) {
                    if (i.style) {
                        var h = i.style[p];
                        if (h)
                            return h;
                        else if (i.parentNode) {
                            return __getstylev(i.parentNode, p);
                        } else if (i != igk.dom.body().o) {
                            return __getstylev(igk.dom.body().o, p);
                        }
                    }
                    return '';
                };

                function __initStyle(item) {
                    var d = igk.createNode('div');
                    var m = {};
                    var t = "";
                    m.cssText = '';
                    for (var i in d.o.style) {
                        if (i != "cssText") {
                            m[i] = __getstylev(item, i); // d.o.style[i];
                            t += " " + i + ":" + m[i] + ";";
                        }
                    }
                    m.cssText = t;
                    return m;
                };
                return __initStyle(item, selector);
            }
        });
    }
    // >@@ name: fullname of function to call
    // >@@ params : array or parameter to pas to function
    function igk_callfunction(name, params) {
        igk_console_debug("call function");
        var func = null;
        if (!name) throw Error("wrong argument: namespace ");
        var h = 0;
        var i = "";
        var ns = "";
        var a = name;
        var t = window;
        // init
        for (var win = t, f = (0 <= a.indexOf(".")) ? a.split(".") : [a], h = 0; h < f.length; h++) {
            if (!f[h]) {
                func = null;
                break;
            }
            i = f[h];
            if (h > 0)
                ns += ".";
            ns += i;
            win = win[i];
            if (!win) { func = null; break; } else
                func = win;
        }
        if (func) {
            func.apply(window, params);
        } else {
            console.debug("function " + name + " doen't exists");
        }
    }

    function igk_getEmValue(q, property) {
        if (!q)
            return 1.0;
        // property="fontSize";
        // var f=q.getComputedStyle(property);
        var f = q.getComputedStyle("fontSize");
        var e = 0.0;
        var p = null;
        var r = 1.0;
        if (property != "fontSize") {
            e = q.getComputedStyle(property);
            r = Math.round(igk.getNumber(e) / igk.getNumber(f));
        } else {
            p = q.getParentNode();
            if (p != null) {
                e = p.getComputedStyle(property);
                r = Math.round(igk.getNumber(f) / igk.getNumber(e));
            }
        }
        return r;
    }
    // >@ get new value number
    function igk_getNumber(value, cibling, property) {
        var ex = /(((-{0,1})[0-9]+(\.[0-9]+){0,1})(px|em|%){0,1})$/;
        var t = ex.exec(value);
        if (t) {
            if (t[5]) {
                switch (t[5]) {
                    case "px":
                        break;
                    case "em":
                        if (cibling && property) {
                            var r = parseFloat(t[2]);
                            var e = igk_getEmValue(cibling, property);
                            var h = igk_getNumber($igk(cibling).getComputedStyle(property));
                            var o = (r / e) * h;
                            return o;
                        }
                        break;
                    case "%":
                        break;
                }
            }
            return parseFloat(t[2]);
        }
        return 0;
    }

    function igk_getInt(value) {
        var t = /((-{0,1})[0-9]+(\.[0-9]+){0,1})(px|em|%)*$/.exec(value);
        if (t)
            return parseInt(t[1]);
        return 0;
    }

    function igk_getUnit(value) {
        var t = /([0-9]+(\.[0-9]+){0,1})(px|em|pt|cm|mm|rem|%)*$/.exec(value);
        if (t) {
            if (typeof(t[3]) == igk.constants.undef) {
                return "px";
            }
            return t[3];
        }
        return "";
    }

    function igk_getPixel(value, cibling, property) {
        switch (value) {
            case "auto":
                if (cibling && cibling.parentNode) {
                    var v = igk_getPixel($igk(cibling.parentNode).getComputedStyle(property));
                    // d=v *(d /100.0);
                    return v;
                }
                break;
            default:
                var ex = /(((-{0,1})[0-9]+(\.[0-9]+){0,1})(px|em|%){0,1})$/;
                var t = ex.exec(value);
                if (t) {
                    var d = parseFloat(t[2]);
                    if (t[5]) {
                        switch (t[5]) {
                            case "em":
                                var e = igk_getEmValue(cibling, property);
                                var h = igk_getNumber($igk(cibling).getComputedStyle(property));
                                var o = (d / e) * h;
                                return o;
                                break;
                            case "%":
                                if (cibling && cibling.parentNode) {
                                    var v = igk_getPixel($igk(cibling.parentNode).getComputedStyle(property));
                                    d = v * (d / 100.0);
                                    return d;
                                } else {
                                    console.debug("no cibling : and cibling no parent " + property);
                                }
                                break;
                            case "px":
                            default:
                                // default value
                                break;
                        }
                    }
                    return parseFloat(t[2]);
                }
                break;
        }
        return 0;
    }

    function igk_getv(obj, keys, defaultvalue) {
        if (obj) {
            if (obj[keys])
                return obj[keys];
        }
        return defaultvalue;
    }

    function igk_newGUID() {
        var result = '';
        var hexcodes = "0123456789abcdef".split("");
        for (var index = 0; index < 32; index++) {
            var value = Math.floor(Math.random() * 16);
            switch (index) {
                case 8:
                    result += '-';
                    break;
                case 12:
                    value = 4;
                    result += '-';
                    break;
                case 16:
                    value = value & 3 | 8;
                    result += '-';
                    break;
                case 20:
                    result += '-';
                    break;
            }
            result += hexcodes[value];
        }
        return result;
    };
    // @ create window namespace
    // a:string
    // c:properties 
    // d:description
    // callback
    function createNS(a, c, d, callback) {
        return createPNS(window, a, c, d, callback);
    };
    // >@ note that files are not poste
    function igk_get_form_posturi(form) {
        var msg = "";
        var e = null;
        var p = [];
        for (var i = 0; i < form.length; i++) {
            e = form.elements[i];
            switch (e.type) {
                case "radio":
                case "checkbox":
                    if (e.checked) {
                        if (p[p.id]) {
                            var m = p[p.id];
                            if (!m.push) {
                                t = [];
                                t.push(m);
                            } else
                                m.push(e.value);
                        } else
                            p[e.id] = e.value;
                    }
                    break;
                case "file": // continue
                    break;
                default:
                    p[e.id] = e.value;
                    break;
            }
        }
        e = 0;
        for (var i in p) {
            if (e != 0)
                msg += "&";
            msg += i + "=" + p[i];
            e = 1;
        }
        return msg;
    };
    // define property proprties 
    // <summary>
    // target : object to define
    // name : name of the property
    // name : property=property
    // </summary>
    function igk_defineProperty(target, name, property) {
        if (!target)
            return;
        try {
            var s = typeof(Object.defineProperty);
            if (s == 'function') {
                // define property
                var prop = {};
                if (property.get)
                    prop.get = property.get;
                if (property.set)
                    prop.set = property.set;
                prop.configurable = igk_getv(property, "configurable", true);
                prop.enumerable = igk_getv(property, "enumerable", true);
                if (!property.set && !property.get)
                    prop.writable = igk_getv(property, "writable", false);
                var k = Object.defineProperty(target, name, prop);
                return k; // Object.defineProperty(target,name,prop);
            } else {
                if (property.nopropfunc) {
                    property.nopropfunc.apply(target);
                } else {
                    var t = {
                        toString: function() { return "data"; }
                    };
                    target[name] = t; // function(){} property.get;
                }
            }
        } catch (ex) {
            if (property.nopropfunc) {
                property.nopropfunc.apply(target);
            } else {
                // work arround
                var t = {
                    toString: function() {
                        return property.get();
                    }
                };
                if (igk && igk.navigator && !igk.navigator.isSafari())
                    target[name] = t;
            }
        }
    };
    // define numeration on object property with only get value
    function igk_defineGetValue(s) {
        return function() {
            return s
        };
    }
    // define enumeration 
    function igk_defineEnum(o, t) {
        if (!o)
            o = {};
        var _is = (o.isEnum && o.isEnum());
        if (_is) {
            for (var i in t) {
                igk_defineProperty(o, i, { get: igk_defineGetValue(t[i]) });
                o.addEnum(i, t[i]);
            }
        } else {
            for (var i in t) {
                igk_defineProperty(o, i, { get: igk_defineGetValue(t[i]) });
            }
        }
        o.addEnum = function(i, v) {
            t[i] = v;
        };
        o.isEnum = function() {
            return 1;
        };
        o.isValuePresent = function(i) {
            return i in t;
        };
        o.getValues = function() {
            return t;
        };
        o.getComboboxEnumValue = function() {
            var t = this.getValues();
            var g = [];
            for (var i in t) {
                g.push({ name: i, val: i });
            }
            g.sort(function(a, b) {
                return a.name.localeCompare(b.name);
            });
            return g;
        };
        return o;
    }
    // append property
    function igk_appendProp(t, e, override) {
        override = typeof(override) == IGK_UNDEF ? !0 : override;
        if (t && e) {
            for (var j in e) {
                try {
                    // var s = Object.getOwnPropertyDescriptor(t, j);
                    // if (s && !s.configurable)
                    // 	continue;
                    t[j] = e[j]; // copy function to igk context				
                } catch (exception) {
                    igk.console_debug(" exception : " + j + " " + exception);
                }
            }
        }
        return t;
    };
    // create a igk object element
    function __igk(name) {
        if (window.igk && window.igk.DEBUG) {
            // igk.debug.write("[CREATE A IGKOBJECT] " + name +  " : "+igk.ajx);
        }
        if (name == window) {
            throw ("/!\\ Call to __igk function on [window] is not allowed. It will break the igk js framework namespace hierarchi." +
                "if you want to register event please use 'igk_winui_reg_event instead'");
        }
        var item = null;
        if (typeof(name) == "string") {
            // name or expression
            var e = $igk(document).select(name);
            return e;
        } else if (typeof(name) == "igk.selector") {
            item = name;
        } else {
            item = name;
        }
        if (item == null)
            return null;
        // verify that element containt a o and that the o is igk
        if (item.o && item.o.igk)
            return item;
        if (window.igk) {
            window.igk.initprop(item);
        }
        return item.igk;
    };
    // show property.
    // @i : element to check
    // @element : element to show if element==0 alert prompt
    function igk_show_prop(i, element) {
        var msg = "";
        var space = "\n";
        var s = "";
        if (element != null)
            space = "<br />";
        try {
            var ig = { "outerHTML": 1, "innerHTML": 1 };
            for (s in i) {
                if (s in ig)
                    continue;
                try {
                    msg += s + "=" + i[s] + space;
                } catch (Exception) {
                    msg += s + "; ex:[" + Exception + "]" + space;
                }
            }
            if (element == null) {
                console.log(msg);
            } else {
                $igk(element).o.innerHTML = msg;
            }
        } catch (ex) {
            msg = "can't evaluate object";
            igk.winui.notify.showError(msg);
        }
    }

    function igk_show_prop_keys(i, element) {
        var msg = "";
        var space = "\n";
        var s = "";
        if (element != null)
            space = "<br />";
        try {
            for (s in i) {
                try {
                    msg += s + "" + space;
                } catch (Exception) {
                    msg += s + "; ex:[" + Exception + "]" + space;
                }
            }
            if (element == null)
                console.error(msg);
            else {
                $igk(element).o.innerHTML = msg;
            }
        } catch (ex) {
            igk.show_notify_error("Exception", "can't evaluate object");
        }
    }

    function igk_show_event(i, element) {
        var msg = "";
        var space = "\n";
        if (element != null)
            space = "<br />";
        for (var s in i) {
            if (/^on/.test(s)) {
                try {
                    msg += s + "=" + i[s] + space;
                } catch (Exception) {}
            }
        }
        if (element == null) {
            igk.winui.notify.showError(msg);
        } else {
            $igk(element).o.innerHTML = msg;
        }
    }
    // get all property in html
    function igk_get_prop(i) {
        var msg = "";
        for (var s in i) {
            try {
                msg += s + "=" + i[s] + "<br />\n";
            } catch (Exception) {}
        }
        return msg;
    }
    // 
    // utility function preload igk-img tag
    // 
    function igk_preload_image(node, async) {
        if ((node == null) || (typeof(node.getElementsByTagName) == igk.constants.undef))
            return;
        // TODO: PRELOAD IMAGe Failed on configuration pages
        function __preload() {
            var v_timg = node.getElementsByTagName("igk-img");
            if (!v_timg || (v_timg.length <= 0)) {
                return;
            }
            var v_tab = new Array();
            // copy items
            for (var s = 0; s < v_timg.length; s++) {
                v_tab[s] = v_timg[s];
            }
            var v_img = null;
            var v_div = null;
            var v_cimg = null;
            // create a preload obj
            var v_preload = {
                // load image on document init
                init: function(img, source) {
                    // source.parentNode.replaceChild(img,source);
                    $igk(img).reg_event("load", function(evt) {
                        if (source.parentNode)
                            source.parentNode.replaceChild(img, source);
                    });
                },
                replace: function(img, source) {
                    if (source.parentNode)
                        source.parentNode.replaceChild(img, source);
                },
                copyAttribute: function(source, dest) {
                    for (var x = 0; x < source.attributes.length; x++) {
                        var j = source.attributes[x];
                        if (j.name == "src") continue;
                        try {
                            dest.setAttribute(j.name, j.value);
                        } catch (Exception) {
                            console.debug("error");
                        }
                    }
                }
            };
            var i = 0,
                v_host = null;
            for (; i < v_tab.length; i++) {
                // new Image();// 
                var src = v_tab[i].getAttribute("src");
                if (src) {
                    v_host = document.createElement("span");
                    $igk(v_host).addClass("igk-img-host");
                    v_cimg = document.createElement("img");
                    v_cimg.source = v_tab[i];
                    v_cimg.src = src;
                    v_host.appendChild(v_cimg);
                    if (/^data:/.test(src)) {
                        console.debug("data source");
                        v_preload.copyAttribute(v_tab[i], v_cimg);
                        v_preload.replace(v_host, v_tab[i]);
                    } else {
                        if (v_cimg.complete && ((v_cimg.width + v_cimg.height) > 0)) {
                            // console.debug("loding access");
                            // directly loaded
                            v_preload.copyAttribute(v_tab[i], v_cimg);
                            v_preload.replace(v_cimg, v_tab[i]);
                        } else {
                            // console.debug("loding access 2" + v_cimg.complete);
                            // loading from 
                            v_img = v_cimg;
                            v_preload.init(v_img, v_tab[i]);
                            v_preload.copyAttribute(v_tab[i], v_img);
                        }
                    }
                }
            }
        }
        __preload();
        // setTimeout(__preload,500);
    }

    function igk_preload_anim_image(node) { // preload igk framework image element
        if ((node == null) || (typeof(node.getElementsByTagName) == igk.constants.undef))
            return;
        var v_timg = node.getElementsByTagName("igk-anim-img");
        if (v_timg.length <= 0)
            return;
        var v_tab = new Array();
        // copy items
        for (var s = 0; s < v_timg.length; s++) {
            v_tab[s] = v_timg[s];
        }
        var v_img = null;
        var v_div = null;
        var v_cimg = null;
        var v_preload = {
            replace: function(img, source) {
                try {
                    if (source.parentNode) {
                        source.parentNode.replaceChild(img.o, source);
                    }
                } catch (ex) {
                    igk.winui.notify.showError("Error : " + ex + " " + img);
                }
            },
            copyAttribute: function(source, dest) {
                for (var x = 0; x < source.attributes.length; x++) {
                    var j = source.attributes[x];
                    if (j.name == "src") continue;
                    try {
                        // dest.setAttribute(j.name,j.value);
                    } catch (Exception) {
                        console.debug("error");
                    }
                }
            }
        };
        var i = 0;
        for (; i < v_tab.length; i++) {
            var src = v_tab[i].getAttribute("src");
            var v_width = v_tab[i].getAttribute("width");
            var v_height = v_tab[i].getAttribute("height");
            if (src) {
                v_cimg = igk.createNode("span");
                v_cimg.source = v_tab[i];
                v_cimg.src = src;
                $igk(v_cimg).setCss({
                    width: v_width,
                    height: v_height,
                    cursor: "pointer",
                    backgroundImage: "url('" + src + "')",
                    overflow: "hidden",
                    backgroundImageRepeat: "no-repeat",
                    display: "inline-block",
                    margin: "0px",
                    padding: "0px"
                });
                v_preload.copyAttribute(v_tab[i], v_cimg);
                v_preload.replace(v_cimg, v_tab[i]);
                $igk(v_cimg).reg_event("mouseover", function() { $igk(this).addClass("btnover").setCss({ backgroundPosition: -this.clientWidth + "px" }); });
                $igk(v_cimg).reg_event("mouseleave", function() { $igk(this).rmClass("btnover").setCss({ backgroundPosition: "0px 0px" }); });
                $igk(v_cimg).reg_event("mousedown", function() { $igk(this).addClass("btndown").setCss({ backgroundPosition: -(2 * this.clientWidth) + "px 0px" }); });
            }
        }
    }
    // encode script in base64 
    function igk_base64encode(str) {
        return igk.utils.Base64.encode(str);
    }
    // remove all script
    // x:item
    function igk_remove_all_script(x) {
        var t = x.getElementsByTagName("script");
        var c = t.length;
        for (var i = 0; i < c; i++) {
            t[0].parentNode.removeChild(t[0]);
        }
    }

    function igk_array_copy(tab) {
        var c = tab.length;
        var btab = new Array();
        for (var i = 0; i < c; i++) {
            btab[i] = tab[i];
        }
        return btab;
    }

    function igk_is_array(tab) {
        return tab instanceof Array;
    }
    // evaluate inner script
    function igk_eval_all_script(item) {
        if (item == null) {
            return;
        }
        var s = $igk(item);
        var v_script = "";
        var c = null;
        var tab = null;
        var i = 0;
        if (s == null) {
            return;
        }
        try {
            if (s.o.getElementsByTagName) {
                tab = igk_array_copy(s.o.getElementsByTagName("script"));
                c = tab.length;
                if (c > 0) {
                    for (i = 0; i < c; i++) {
                        if (!tab[i]) {
                            console.log('not define ' + tab[i] + " i=" + i + " c=" + c + " tab.length=" + tab.length + " \n " + tab[i - 1].innerHTML);
                            continue;
                        }
                        var j = tab[i];
                        v_script = j.innerHTML;
                        igk_eval(v_script, j.parentNode, j);
                    }
                }
            }
        } catch (ex) {
            igk.show_notify_error("Exception",
                "igk_eval_all_script:\nERROR: " + ex + "<br />\n" +
                "The script have error : " + v_script + "\ni=" + i + " \ntab.length=" + tab.length +
                "<div class='tracebox'> " + ex.stack + "</div>"
            );
        }
    }

    function igk_isdefine(i, d) { // use with declared properties. if try to check that a variable is define use typeof(var) instead
        if (typeof(i) == IGK_UNDEF) {
            if (typeof(d) == IGK_UNDEF) {
                return !1;
            }
            return d;
        }
        return i;
    }

    function igk_eval(s, sn, n) {
        // s:source code	
        // sn: script node the source script tag
        // n: the target node
        if (!s || (s.length == 0) || n.tagName !== "SCRIPT") {
            return;
        }
        m_scriptNode = n;
        igk.evaluating = !0;
        //try {
        (new Function(s)).apply(window, [m_scriptNode]);
        // eval(s);
        //} catch (ex) {
        // for chrome disable code extension in some case.
        //console.error($ex);
        // 	var sg = '';
        // 	if (ex.stack) {
        // 		sg = ex.stack.replace('\n', '<br />');
        // 	} 
        // 	igk_show_notify_error("Exception",
        // 		"<div class='igk-error'><p>Error: igk_eval</p>Message: "
        // 		+"<b>:"+ ex + "</b>"
        // 		+ "<pre>"+s+"</pre>"				
        // 		+ "</div>"
        // 	);
        // 	console.debug(ex);
        // }
        m_scriptNode = null;
        igk.evaluating = false;
    }
    // append script to head . 
    function igk_append_script_to_head(item) {
        var tab = item.getElementsByTagName("script");
        var c = tab.length;
        var head = null;
        if (!document.getElementsByTagName("head")[0]) {
            head = igk.createNode("head");
        } else {
            head = document.getElementsByTagName("head")[0];
        }
        for (var i = 0; i < c; i++) {
            var scriptText = tab[i].text;
            var scriptFile = tab[i].src;
            var scriptTag = igk.createNode("script");
            if ((scriptFile != null) && (scriptFile != "")) {
                scriptTag.src = scriptFile;
            }
            scriptTag.text = scriptText;
            head.appendChild(scriptTag);
        }
    }

    function igk_confirm_lnk(lnk) {
        var frm = igk_getParentByTagName(lnk, 'form');
        if (frm) {
            frm.confirm.value = 1;
            frm.action = lnk.href;
            frm.submit();
        }
    }
    // get last inserted script
    function igk_getLastScript() {
        var t = document.getElementsByTagName('script');
        if (t.length > 0)
            return t[t.length - 1];
        return null;
    }
    // get parent by tag name
    function igk_getParentByTagName(e, tagname) {
        if ((e == null) || (typeof(tagname) != "string"))
            return null;
        if (e.parentNode == null) return null;
        if ((e.parentNode.tagName != null) && (e.parentNode.tagName.toLowerCase() == tagname.toLowerCase()))
            return e.parentNode;
        return igk_getParentByTagName(e.parentNode, tagname);
    }
    // get parent script Dom Node 
    function igk_getParentScriptByTagName(tagname) {
        var p = m_scriptNode;
        var q = null;
        if (p != null) {
            q = p.parentNode;
            if (tagname) {
                if (q.tagName.toLowerCase() != tagname.toLowerCase())
                    return igk_getParentByTagName(q, tagname);
            }
            return q;
            // __parentScript;
        }
        if (tagname != null)
            return igk_getParentByTagName(igk_getLastScript(), tagname);
        else {
            return igk_getLastScript().parentNode;
        }
    }
    // get parent by id
    function igk_getParentById(e, id) {
        if ((e == null) || (id == null))
            return null;
        var p = e.parentNode;
        if (!id.toLowerCase) {
            igk.show_notify_error("Error", "ERROR FOR: " + e + " Request " + id);
            return null;
        }
        var i = id.toLowerCase();
        if (p == null)
            return null;
        if ((p.id != null) && (p.id.toLowerCase() == i))
            return p;
        return igk_getParentById(p, id);
    }
    // retrieve first child by tagname
    function igk_getfirstchild(e, tag) {
        var c = [];
        var t = e.childNodes;
        var p = null;
        for (var i = 0; i < t.length; i++) {
            p = t[i];
            if (p.tagName && (p.tagName.toLowerCase() == tag.toLowerCase())) {
                c[c.length] = p;
            }
        }
        return c;
    }
    // select all child that match attribute
    function igk_getChildsByAttr(item, attribute) {
        if ((item == null) || (attribute == null) || (!item.childNodes))
            return null;
        var t = item.childNodes;
        var p = null;
        var b = null;
        var i = 0;
        var isok = !0;
        var ret = new Array();
        var iret = null;
        for (var i = 0; i < t.length; i++) {
            p = t[i];
            isok = !0;
            for (var j in attribute) {
                if (!p.getAttribute || (p.getAttribute(j) != attribute[j])) {
                    isok = false;
                    break;
                }
            }
            if (isok) {
                ret[ret.length] = p;
            }
            iret = igk_getChildsByAttr(p, attribute);
            if (iret != null) {
                // copy array to this
                for (j = 0; j < iret.length; j++) {
                    ret[ret.length] = iret[j];
                }
            }
        }
        return ret;
    }

    function igk_checkOnePropertyExists(item, proplist) {
        if (item == null) return !1;
        var t = proplist.split(' ');
        for (var s in t) {
            if (typeof(item[t[s]]) !== IGK_UNDEF)
                return !0;
        }
        return !1;
    }

    function igk_checkAllPropertyExists(item, proplist) {
        if (item == null) return !1;
        var t = proplist.split(' ');
        for (var s in t) {
            if (typeof(item[t[s]]) === IGK_UNDEF)
                return !1;
        }
        return !0;
    }
    // retrieve a child by id
    function igk_getChildById(item, id) {
        if ((item == null) || (id == null))
            return null;
        var t = item.childNodes;
        var p = null;
        var b = null;
        var i = 0;
        for (var i = 0; i < t.length; i++) {
            p = t[i];
            if (p.id && (p.id.toLowerCase() == id.toLowerCase())) {
                return p;
            } else {
                b = igk_getChildById(p, id);
                if (b != null)
                    return b;
            }
        }
        return null;
    }
    // get value integer
    function igk_getvi(item, prop) {
        if (item[prop]) {
            return item[prop];
        } else if (item.igk && item.igk["get" + prop]) {
            return item.igk["get" + prop];
        }
        return 0;
    }

    function is_observe() {
        return typeof(window.ResizeObserver) != 'undefined';
    };

    function igk_init_powered(n) {
        // get parent node
        // var node = n || window.igk.getParentScriptByTagName('div');
        var node = $igk(n || window.igk.getParentScriptByTagName('div'));
        igk.ready(function() {
            var q = $igk(".igk-powered-viewer").last();
            if (!q){
                q = $igk('body').first().qselect('[igk-type:controller]');
                let h = q.getHeight();
                let p = q.getoffsetParent();  
                q = null;
            }  
           
            
            if (q) {
                // is_observe() && (new ResizeObserver(_resizing)).observe(q.o);
                // node.setCss({
                //     "position": "sticky",
                //     "bottom": "0px",
                //     "top": "calc(100vh - " + node.getHeight() + "px)",
                //     "left": "calc(100% - 130px)",
                //     "width": "130px",
                // });
                // q.add(node);
            }
        });
    };

    function igk_powered_manager(node, ciblingnode) {
        if (!node) return;

        function init(node, poweredhost) {
            var node = node;
            var s = poweredhost;
            var animinfo = {
                duration: 200,
                interval: 10,
                animtype: "timeout",
                context: "init_powered_manager",
                effect: "circ",
                effectmode: "easeinout"
            };

            function update_size() {
                var animprop = {
                    right: "0px",
                    bottom: "0px"
                };
                if ($igk(s).fn.hasVScrollBar()) {
                    // get scroll size
                    var _q = $igk(s).add("div").addClass("posab fitw fith").setHtml("width:info");
                    var h = s.offsetWidth - _q.o.offsetWidth;
                    _q.remove();
                    animprop.right = (h + 2) + "px";
                }
                if ($igk(s).fn.hasHScrollBar()) {
                    var _q = $igk(s).add("div").addClass("posab fitw fith").setHtml("height:info");
                    var h = s.offsetHeight - _q.o.offsetHeight;
                    _q.remove();
                    animprop.bottom = (h + 2) + "px";
                    // animprop.bottom="16px";
                }
                $igk(node).animate(animprop, animinfo);
            }
            var v_self = this;
            v_self.toString = function() { return "igk_powered_manager"; };
            var m_eventContext = igk.winui.RegEventContext(this, $igk(this));
            if (m_eventContext) {
                m_eventContext.reg_window("resize", function() { update_size(); });
            };
            update_size();

            function __doc_changed() {
                update_size();
            };
            igk.publisher.register("sys://doc/changed", __doc_changed);
        };
        var s = ciblingnode ? ciblingnode : document.getElementById(ciblingnode);
        if (s) {
            var q = new init(node, s);
        } else {
            console.debug("/!\\ parent cibling not found");
        }
    }

    function igk_getdir(uri) {
        if (uri == null)
            return null;
        var d = uri.split('/');
        var o = "";
        for (var s = 0; s < d.length - 1; s++) {
            if (s != 0)
                o += "/";
            o += d[s];
        }
        return o;
    };
    // initialize object
    // @n: source object
    // @d: default object
    function igk_initobj(n, d) {
        if (!n)
            return d;
        for (var i in d) {
            if (i in n) {
                continue;
            }
            n[i] = d[i];
        }
        return n;
    };
    // init
    m_scriptTag = igk_getLastScript();
    let fc_false = function() { return !1 };
    // --------------------------------------------------
    // expose global function 
    // --------------------------------------------------
    // define a igk global namespace
    createNS("igk", {}, { desc: "global igk js namespace" });
    const _ENVIRONMENT = createNS("igk.ENVIRONMENT", {}, { desc: "Environment information" });
    createNS("igk.ctrl", {}, { desc: "manage controller" });
    createNS("igk.system", {}, { desc: "global system igk js namespace" });
    createNS("igk.type", {}, { desc: "global igk js namespace" });
    createNS("igk.JSON", (function() {
        var source = null;
        return {
            /**
             * get the source
             */
            getSource() {
                return source;
            },
            /**
             * set the source
             * @param {*} src 
             */
            setSource: function(src) {
                source = src;
            },
            /**
             *  used to parse attribute property
             * @param {*} js 
             * @param {*} target 
             */
            parse: function(js, target) {
                var q = null;
                source = target;
                try {
                    if ((js != null) && (typeof(js) == "string"))
                        q = eval('(' + js + ')');
                } catch (ex) {
                    q = js;
                }
                this.target = null;
                source = null;
                return q;
            },

            /**
             * convert json object to string
             * @param {*} jsonobj 
             */
            convertToString: function(jsonobj) {
                if (typeof(JSON) != 'undefined' && JSON.stringify) {
                    return JSON.stringify(jsonobj);
                }
                var r = "{";
                var k = 0;
                var prop = [];
                for (var i in jsonobj) {
                    if ((typeof(jsonobj[i]) == IGK_FUNC) ||
                        (typeof(jsonobj[i]) == IGK_UNDEF)
                    ) {
                        continue;
                    }
                    if (k > 0)
                        r += ",";
                    if (typeof(jsonobj[i]) == "object") {
                        // treat data
                        prop.push(jsonobj[i]);
                        r += "\"" + i + "\": ";
                    } else
                        r += "\"" + i + "\":" + jsonobj[i];
                    k++;
                }
                r += "}";
                return r;
            }
        };
    })(), { desc: "global namespace . JSON helper" });
    createNS("igk.navigator", {
        isIE: function() { return false },
        getLang: function() {
            return 'fr';
        },
        isChrome: fc_false,
        isSafari: fc_false,
        isIEEdge: fc_false,
        isFirefox: fc_false,
    }, { desc: "global navigator properties" });
    createNS("igk.reflection", {}, { desc: "global igk js namespace" });
    createNS("igk.exception", {}, { desc: "global igk exception class" });
    createNS("igk.os", {}, { desc: "balafon os utility function" });
    createNS("igk.android", {}, { desc: "balafon android utility namespace" });
    createNS("igk.winui", {}, { desc: "winui global igk js namespace. manage interface" });
    createNS("igk.winui.fn", {}, { desc: "utility functions" });
    createNS("igk.winui.notify", {}, { desc: "winui.notify global igk js namespace" });
    createNS("igk.html", {
        getDefinition: igk_get_html_item_definition,
        getDefinitionValue: igk_get_html_item_definition_value,
    }, { desc: "igk.html namespace. to manage dom element" });
    createNS("igk.html5", {}, { desc: "igk.html5 namespace" });
    igk.log = function(msg, tag, t) {
        if (!igk.DEBUG) {
            return;
        }
        var fc = null;
        switch (t) {
            case "i":
                fc = console.log;
                break;
            case "e":
                fc = console.error;
                break;
            case "w":
                fc = console.warning;
                break;
            default:
                fc = console.debug;
                break;
        };
        tag = tag || 'BJS';
        if (fc) {
            if (typeof(msg) == "string")
                fc("[" + tag + "] - " + msg);
            else {
                fc("[" + tag + "]");
                fc(msg);
            }
        }
    };
    createNS("igk.log", {
        debug: function(m) {
            if (igk.DEBUG) {
                igk.log(m);
            }
        },
        error: function(m) {
            if (igk.DEBUG) {
                console.error(m);
            }
        },
        write: function(m) {
            /**
             * write log in html gobloa 
             */
            console.log(m);
            $igk(igk.winui.events.global()).raiseEvent('igk_log', m);
        }
    }, { desc: 'manage log' });
    createNS("igk.attribute", {
        setAttribute: __igksetAttribute
    });
    // export navite io functions
    createNS("igk.system.io", {
        getdir: igk_getdir,
        getlocationdir: function(inf) {
            if (inf && ('location' in inf))
                return igk_getdir(inf.location);
            return null;
        },
        getData: igk_io_getData,
        getExtension: function(n) {
            return n.split('.').pop();
        },
        getLocation: function() {
            return document.location.href;
        },
        baseUri: function() {
            var uri = "" + window.location;
            var t = "";
            uri.replace(/^([^#;\?]+)/g, function(m, c, s) {
                t = m;
            });
            while ((t.length > 0) && (t[t.length - 1] == "/")) {
                t = t.substr(0, t.length - 1);
            }
            return t;
        },
        baseUriDomain: function() {
            var port = "";
            var loc = window.location;
            var is_secure = (/^https/.test(loc.protocol));
            if (((!is_secure) && (loc.port != '') && (loc.port != 80)) || (is_secure && (loc.port != '') && (loc.port != 443))) {
                port += ':' + loc.port;
            }
            return loc.protocol + "//" + loc.hostname + port;
        },
        rootUri: function() { // get uri location setup base base tag
            var t = document.head.getElementsByTagName("base");
            if (t.length == 0) {
                var s = igk.getDocumentSetting();
                var h = 0;
                if (s)
                    h = s.baseuri;
                return (h || (igk.system.io.baseUriDomain())) + "/";
            } else {
                return document.baseURI || t[0].href;
            }
        },
        URL: function(uri) {
            //construct a url object
            var _NS = igk.system.io;
            var src = uri;
            var relative = 0;
            var _opts = {
                relative: 0,
                isLocal: 0,
                uri: uri,
                request: ''
            };
            if (/^(\.(\.)?\/|\/[^\/]+)/.test(uri)) {
                _opts.relative = 1;
                _opts.isLocal = 1;
                _opts.protocol = window.location.protocol;
                // while(uri.indexOf('../')){
                // uri = uri.substring(3);
                // }
                var tab = uri.split('/');
                var i = 0;
                var luri = '';
                var ruri = _NS.rootUri();
                // while ((ruri.length>0) && ruri[ruri.length-1]=='/' ){
                // ruri = ruri.substring(0, ruri.length-1);
                // }
                for (var j = 0; j < tab.length; j++) {
                    switch (tab[j]) {
                        case '..':
                            if (i) {
                                //remove last entry
                                luri = luri.substring(0, luri.lastIndexOf('/'));
                            } else {
                                ruri = ruri.substring(0, ruri.lastIndexOf('/'));
                            }
                            break;
                        case '.':
                            break;
                        default:
                            if (luri.length > 0)
                                luri += "/";
                            luri += tab[j];
                            i = 1;
                            break;
                    }
                }
                _opts.uri = ruri + '/' + luri;
                _opts.request = '/' + luri;
            } else {
                if (uri.startsWith(_NS.rootUri())) {
                    _opts.isLocal = 1;
                    _opts.uri = uri;
                    _opts.protocol = window.location.protocol;
                    _opts.request = "/" + uri.substring(_NS.baseUriDomain().length + 1);
                } else if (/^(((http(s)?|ftp|sw):)?\/\/)/.test(uri)) {
                    _opts.uri = uri;
                    _opts.request = "";
                }
            }
            igk.appendProperties(this, {
                toString: function() {
                    return _opts.uri;
                }
            });
            igk.defineProperty(this, "relative", { get: function() { return _opts.relative; } });
            igk.defineProperty(this, "isLocal", { get: function() { return _opts.isLocal; } });
            igk.defineProperty(this, "uri", { get: function() { return _opts.uri; } });
            igk.defineProperty(this, "request", { get: function() { return _opts.request; } });
        },
        loadLangRes: function(uri, lang, callback, error) {
            var e = null;
            var _getRes = 0;
            var _chtml = igk.dom.html();
            var _lang = lang || (_chtml ? _chtml.getAttribute("lang") : null) || igk.navigator.getLang();
            var _loc = igk.system.io.getdir(uri);
            var _uri = igk.resources.getLangLocation(_loc, _lang);
            // console.debug("loadLangRes"); 
            // error.apply(this);
            // return null;
            var _promise = {
                then: function(callback) {
                    this.__then = callback;
                }
            };
            try {
                if (igk.navigator.isSafari())
                    throw ('safari not handle async - await');
                // TODO: Update to function 
                eval(["_getRes = async function (){",
                    "var o=0; ",
                    "try{",
                    "o  = await igk.ajx.asyncget(_uri,null, 'text/json');",
                    "o  = typeof(o)=='string' && o.length>0 ? JSON.parse(o) : null;",
                    "return  o;",
                    "}catch(ex){",
                    " console.error(ex);",
                    "}",
                    "return null;",
                    "};"
                ].join(" "));
            } catch (ex) {
                _getRes = function() {
                    if (!document.body) {
                        igk.ajx.get(_uri, null, function(xhr) {
                            if (this.isReady()) {
                                if (e.__then__) {
                                    e.__then__(JSON.parse(xhr.responseText));
                                }
                            }
                        });
                    } else {
                        igk.system.io.getData(_uri, function(o) {
                            if (e.__then__ && o.data && o.data.length > 0)
                                e.__then__(JSON.parse(o.data));
                            else if (e.__catch__)
                                e.__catch__("/!\\ loadLangRes : " + _uri + "  " + o.data);
                        }, "application/json");
                    }
                    var e = {
                        "then": function(t) {
                            this.__then__ = t;
                            return this;
                        },
                        "catch": function(t) {
                            this.__catch__ = t;
                            return this;
                        }
                    };
                    return e;
                }
            }
            if (typeof(_getRes) != 'undefined') {
                _getRes().then(function(o) {
                    callback(o);
                    // var _res = o;
                    if (_promise.__then) {
                        _promise.__then();
                    }
                    // igk.winui.events.raise(_sysnode, "resLoaded");
                }).catch(function(e) {
                    console.debug(e);
                    console.error("[BJS] - there is an error : " + e +
                        "\nUri:" + _uri);
                });
            }
        }
    });
    /**
     * --- manage language resources
     */
    createNS("igk.resources", {
        /**
         * get language resources location
         * @param {} _loc 
         * @param {*} t 
         * @returns 
         */
        getLangLocation(_loc, t) {
            if (!_loc) {
                return null;
            }
            if (!t) {
                t = igk.dom.html().getAttribute("lang") || igk.navigator.getLang();
            }
            return _loc + '/Lang/res.' + t + '.json';
        },
    });
    if (typeof(igk.resources.lang) == 'undefined') {
        igk.resources.lang = function(n) {
            if (n in igk.resources.lang) {
                return igk.resources.lang[n];
            }
            return n;
        };
        // + | lang to french
        igk_appendProp(igk.resources.lang, {
            "Hello": "Bonjour",
            "Welcome": "Bienvenue",
            "BalafonJS": "BalafonJS",
        });
    }
    createNS("igk.system.Db", {
        getIndexedDb: function() {
            return window.indexDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
        }
    });

    igk_defineProperty(_ENVIRONMENT, "DEV", {
        get:function(){
            return DEBUG;
        }
    });

    (function() {
        igk.system.Promise = function() {
            var _t = [];
            var _e = [];
            this._t = _t;
            this._e = _e;
        };
        igk_appendProp(igk.system.Promise.prototype, {
            resolve: function(args) {
                for (var j = 0; j < this._t.length; j++) {
                    this._t[j].apply(this, args);
                }
            },
            reject: function(args) {
                for (var j = 0; j < this._e.length; j++) {
                    this._e[j].apply(this, args);
                }
            },
            then: function(callback) {
                this._t.push(callback);
                return this;
            },
            error: function(callback) {
                this._e.push(callback);
                return this;
            }
        });
    })();
    // new function added for promise call usage
    igk.system.promiseCall = function() {
        var _p = {};
        return {
            then: function(func) {
                igk.appendChain(_p, 'then', func);
                return this;
            },
            error: function(func) {
                igk.appendChain(_p, 'error', func);
                return this;
            },
            resolve: function() {
                if ('then' in _p) {
                    _p.then();
                }
            },
            failed: function() {
                if ('error' in _p) {
                    _p.error();
                }
            }
        };
    };
    //igk namespace utility function 
    let _NS = createNS("igk", {
        getQueryOptions: function(s) { //retrive query options from string
            var e = {};
            s.replace(/(;)?([^;]+)=([^;]+)/ig, function(m, t, n, v) {
                e[n] = v.trim();
            });
            return e;
        },
        getDocumentSetting: function() {
            return __initDocSetting;
        },
        isInteger: function(n) {
            var f = Number.isInteger || _is_integer;
            return f(n);
        },
        invokeAsync: function(callback, args) {
            // custom async data call
            setTimeout(function() {
                callback.call(null, args);
            }, 1);
        },
        isFunc: function(n) {
            return n == 'function';
        },
        configure: function(o) { // load configuration setting
            for (var i in o) {
                if (typeof(o[i]) == 'function') continue;
                __igk_settings[i] = o[i];
            }
        },
        regex: function() { // get global regex list
            return _rgx;
        },
        getSettings: function() { return __igk_settings; },
        object: igk_object,
        is_notdef: igk_is_notdef,
        is_string: igk_is_string,
        is_object: igk_is_object,
        reg_tag_component: igk_reg_tag_obj,
        regEventContextByOwner: igk_getRegEventContextByOwner,
        die: function(msg, code) {
            throw code + " : " + msg;
        },
        geti: function(t) {
            if (t) return 1;
            return 0;
        },
        stop_event: igk_stop_event,
        bool_parse: function(t) {
            if (igk_is_string(t)) {
                var s = t.toLowerCase();
                if ((s.length == 0) || (s == "false") || (s == "0"))
                    return !1;
                return !0;
            }
            if (t)
                return !0;
            return !1;
        }
    }, { desc: "global igk.object base class namespace" });

    igk_defineProperty(_NS, 'context', {
        get(){
            return _context_;
        },set(v){
            _context_ = v;
        }
    });

    // define global namespace variable
    _NS = createNS("igk.namespaces", {
        xhtml: "http://www.w3.org/1999/xhtml",
        xml: "http://www.w3.org/1999/xhtml",
        xsl: "http://wwww.w3.org/1999/XSL/Transform",
        svg: "http://www.w3.org/2000/svg"
    });
    igk_defineProperty(_NS, "igk", {
        get: function() {
            return "https://schemas.igkdev.com/balafonjs"
        }
    });
    createNS("igk.convert", {
        toHtmlColor: function(i) {
            var c = igk.system.colorFromString(i);
            if (c)
                return c.toHtml();
            return 0;
        }
    });
    var __jsload = {};
    var __jstoload = {};
    createNS("igk.js", {
        load: function(s) {
            if (s in __jsload)
                return;
            var p = document.createElement("script");
            var e = 0;
            p.type = "text/javascript";
            p.language = "javascript";
            p.async = true;
            p.onerror = function() {
                console.error("failed to load " + s);
                $igk(p).remove();
                delete(__jsload[s]);
                e = 1;
            };
            p.onload = function(e) {
                console.debug("laod complete " + p.readyState);
            };
            igk.ajx.get(s, null, function(xhr) {
                if (this.isReady()) {
                    p.innerHTML = xhr.responseText;
                }
            }, true);
            document.head.appendChild(p);
            // 		igk.dom.body().prepend(p);
            __jsload[s] = 1;
        },
        require: function(scs, sync, host) {
            // @scs: scripts source array
            // @async: synchronize call :  true|false default is false. 
            var syncdata = 0;
            host = host || document.head;

            function __loadscript(i) {
                // syncdata
                if ((i < 0) || (i >= scs.length)) {
                    return;
                }
                var u = scs[i];
                if (__jsload[u] || !__jstoload[u]) {
                    i++;
                    syncdata.skipped++;
                    if (def.c >= 0) {
                        __loadscript(i);
                    }
                    return;
                }
                // if (!__jstoload[u]){
                // return;
                // }
                __jsload[u] = 1;
                __jstoload[u] = 0;
                var sc = document.createElement("script");
                sc.src = u;
                def.e[u] = i;
                sc.onload = (function(i, u) {
                    return function() {
                        syncdata.loaded++;
                        syncdata.files = igk.isDefine(syncdata.files, []);
                        syncdata.files.push(u);
                        delete(def.e[u]);
                        i++;
                        if (i > scs.length - 1) {
                            __invoke();
                        } else {
                            __loadscript(i);
                        }
                        $igk(sc).remove();
                    };
                })(i, u);
                sc.onerror = function() {
                    console.error("/!\\ failed:" + u);
                };
                document.head.appendChild(sc);
            };
            var prop = {};
            var def = { c: 0, e: {} };
            var u = 0;
            sync = sync || 0;

            function _fail(message) {
                if (prop.failed)
                    prop.failed.apply(o, [message]);
            };
            var _scripts = [];
            var o = {
                promise: function(callback, h) {
                    // @ success - callback
                    // @h the host parameter
                    prop.callback = callback;
                    prop.host = h;
                    if (def.c <= 0) {
                        // direct invoke promise
                        __invoke();
                    }
                    return this;
                },
                fail: function(callback) {
                    prop.failed = callback;
                    return this;
                },
                load: function(callback) {
                    //called when a script is loaded 
                    prop.load = callback;
                    return this;
                }
            };

            function _loadinScript(u, type, index) {
                return function(xhr) {
                    if (this.isReady()) {
                        __jsload[u] = xhr.responseText;
                        try {
                            var s = document.createElement("script");
                            s.type = type || "text/javascript";
                            s.innerHTML = __jsload[u];
                            _scripts.push(s);
                            if (prop.load) {
                                prop.load(s);
                            }
                            delete(def.e[u]);
                            def.c--;
                        } catch (ex) {
                            console.error("error loading : " + ex);
                        }
                        if (def.c == 0) {
                            __invoke();
                        }
                    }
                };
            };

            function __invoke() {
                for (var g = 0; g < _scripts.length; g++) {
                    host.appendChild(_scripts[g]);
                }
                _scripts = [];
                var m = 0;
                if (prop.host)
                    m = $igk(prop.host).o;
                if (prop.callback)
                    prop.callback.apply(o, [m, syncdata]);
            };

            function _loadFormalScript(u, i) {
                var sc = document.createElement("script");
                sc.src = u;
                def.e[u] = i;
                def.c++;
                sc.onload = (function(i, u) {
                    return function() {
                        if (prop.load) {
                            prop.load(sc);
                        }
                        delete(def.e[u]);
                        def.c--;
                        if (def.c == 0) {
                            __invoke();
                        }
                    };
                })(i, u);
                sc.onerror = function() {
                    console.error("/!\\ failed: " + u);
                    _fail();
                };
                host.appendChild(sc);
            };
            if (sync) {
                def.c = scs.length;
                syncdata = {
                    loaded: 0,
                    skipped: 0,
                    status: typeof(null)
                };
                for (var i = 0; i < scs.length; i++) {
                    if (!__jstoload[scs[i]])
                        __jstoload[scs[i]] = 1;
                }
                __loadscript(0);
            } else {
                //async call
                var _type = null;
                for (var i = 0; i < scs.length; i++) {
                    u = scs[i];
                    _type = null;
                    if (typeof(u) == 'object') {
                        _type = u.type;
                        u = u.uri;
                    }
                    if (__jsload[u]) {
                        def.c--;
                        continue;
                    }
                    if (u in scs) {
                        igk.ajx.get(u, null, _loadingScript(u, _type, i));
                        continue;
                    };
                    __jsload[u] = 1;
                    _loadFormalScript(u, i);
                }
            }
            // promise object
            return o;
        },
        getLoadedScript: function() {
            return __jsload;
        },
        loadInlineScript: function() {
            var d = igk.getParentScript();
            var sc = $igk(igk.getCurrentScript());
            if (!d)
                return;
            var l = [];
            $igk(d).select('script').each_all(function() {
                var s = this.o.src;
                var r = this.getAttribute("reloaded");
                if (s && (!(s in __jsload) || r)) {
                    if (r) {
                        l.push(s);
                        l[s] = null;
                        delete(__jsload[s]);
                        delete(__jstoload[s]);
                    } else {
                        l.push(s);
                    }
                }
                this.remove();
            });
            if (l.length > 0)
                igk.js.require(l, true, d).promise(function() {
                    sc.remove();
                });
        }
    });
    //store inline script to eval
    igk.scripts = {};
    igk.scripts["@injectlocation"] = "((typeof(getLocalScriptUri) !='undefined')?" + "getLocalScriptUri():" + "igk.system.io.getlocationdir(igk.getScriptLocation()))";
    (function() {
        // store global event
        createNS("igk.evts", {
            stop: igk.stop_event
        });
        igk.evts.dom = [
            "sys://dom/pageloaded",
            "sys://dom/bodyreplaced",
            "sys://ddom/nodetextchanged"
        ];
    })();
    createNS("igk.reflection", {
        getFunctions: function(item) { // get an array of functions
            if (!item)
                return null;
            var r = [];
            for (var i in item) {
                if (typeof(item[i]) == IGK_FUNC) {
                    r.push(i);
                }
            }
            return r;
        },
        // getFunctions:function(e){
        // var t=[];
        // if(typeof(e) !=IGK_UNDEF){
        // for(var i in e){
        // if(typeof(e[i])=="function"){
        // t.push(i);
        // }
        // }
        // }
        // return t;
        // },
        getProperties: function(e) {
            var t = [];
            if (typeof(e) != IGK_UNDEF) {
                if (typeof(e) == 'string') {
                    for (var i in e) {
                        if (!igk.isInteger(i) && (typeof(e[i]) != "function")) {
                            t.push(i);
                        }
                    }
                } else {
                    for (var i in e) {
                        if (typeof(e[i]) != "function") {
                            t.push(i);
                        }
                    }
                }
            }
            return t;
        }
    });
    createNS("igk.html5", {
        animate: igk_animate,
        cancelAnimate: igk_animate_cancel
    });
    __nsigk = igk; // igk namespace
    // window.igk= igk;
    window.$igk = function(n) {
        var m = __igk(n);
        if (m == null) {
            console.error('found - [' + n + ']');
        }
        return m;
    }; // register function igk function
    window.$ns_igk = __nsigk;
    window.ns_igk = __nsigk;
    window.igk.wln = console.debug;
    igk_appendProp(window.$igk, {
        /**
         * create text node utility
         */
        create(s) {
            return igk.dom.loadDocument(s);
        },
        /**
         * helper to create node
         * @param {*} d 
         * @returns 
         */
        createNode(d) {
            return igk.createNode(d);
        },
        /**
         * helper check if is function
         * @param {*} n 
         * @returns 
         */
        isFunction(n) {
            return typeof(n) == 'function';
        },
        isObject(n) {
            return typeof(n) == 'object';
        },
        isString(n) {
            return typeof(n) == 'string';
        },
        /**
         * extends global node properties
         * @param {e} p 
         */
        extendNodeProperties(p) {
            igk_appendProp(__igk_nodeProperty.prototype, p);
        }
    });
    // tiny mce require matchMedia. provide a match media
    if (typeof(matchMedia) == 'undefined') {
        window.matchMedia = function() {
            return 0;
        };
    }

    // loader 
    igk_appendProp(igk.object.prototype, {
        toString: function() {
            return "igk.object";
        },
        getType: function() {
            return igk.object;
        }
    });

    function igk_regex_constant() {
        return {
            tagName: /^[\w_]+[\w0-9_\-]*$/,
            className: /^\.[\w_]+[\w0-9_\-]*$/,
            idSearch: /^#[\w\-_]+(\[[\w\-_]*\])?$/
        }
    }

    function isUndef(d) {
        return (d == "unknown") || (d == IGK_UNDEF) || (typeof(d) == IGK_UNDEF);
    }
    function isNumber(d){
        return !isUndef(d) && typeof(d)=='number';
    }
    var _FM_ = "BalafonJS";

    function __igk_nodeProperty(element) {
        var m_o = element;
        var m_self = this;
        var m_anim; // animation property
        var m_timeOutList = [];
        var m_unregfuncs = [];
        var m_config = {};
        var m_data = {};
        // define readonly property
        igk_defineProperty(this, "o", {
            get: function() { return m_o; },
            nopropfunc: function() { this.o = m_o; }
        });
        igk_defineProperty(this, "t", {
            get: function() { return $igk(m_o.parentNode); },
            nopropfunc: function() { this.t = $igk(m_o.parentNode); }
        });
        igk_defineProperty(this, "timeOutList", {
            get: function() { return m_timeOutList; }
        });
        // used to store extra data
        // this.o=element;
        // define function property
        createPNS(this, "fn", { // function utility
            o: element,
            igk: m_self,
            vscrollWidth: function() { return this.o.offsetWidth - this.o.clientWidth; },
            hscrollHeight: function() { return this.o.offsetHeight - this.o.clientHeight; },
            hasVScrollBar: function() { // has vertical scrollbar
                var h = this.igk.getHeight();
                // return (this.o.clientHeight > 0) && (this.o.scrollHeight > h);
                return (this.o.clientHeight > 0) && (this.o.offsetHeight > h);
            },
            hasHScrollBar: function() { // has horizontal scrollbar
                var w = this.igk.getWidth();
                // return (this.o.clientWidth > 0) && (this.o.scrollWidth > w);
                return (this.o.clientWidth > 0) && (this.o.offsetWidth > w);
            }
        });
        createPNS(this, "data", {
            contains: function(k) {
                if (typeof(m_data[k]) != IGK_UNDEF)
                    return !0;
                return !1;
            },
            add: function(k, v) {
                m_data[k] = v;
            },
            remove: function(k) {
                delete(m_data[k]);
            },
            getData: function() {
                return m_data;
            }
        });
        this.getUnregfunc = function() {
            return m_unregfuncs;
        };
        this.getConf = function() {
            return m_config;
        };
        // 
        // configuration 
        // 
        // used to store properties configuration on igk node
        // 
        if (!this.isSr()) {
            // -------------------------------
            // not selector properties
            // -------------------------------
            var m_bE = "igk-eventcreated";
            igk_appendProp(this, {
                // -------------------------------
                // EVENTS
                // -------------------------------
                raiseEventCreated: function(n) {
                    var e = this.o[m_bE];
                    if (e) {
                        e.name = n;
                        this.raiseEvent(m_bE);
                    } else {
                        console.error("no event to raise : " + n);
                    }
                },
                postRegisterEvent: function(n, func) {
                    if (!n || !func)
                        return;
                    if (n == "igk-eventcreated") {
                        this.reg_event(n, func);
                    } else {
                        if (typeof(this.o[n]) == IGK_UNDEF) {
                            var _b = this;
                            this.reg_event(m_bE, function _postevent(evt) {
                                if (evt.name == n) {
                                    _b.unreg_event(m_bE, _postevent);
                                    _b.reg_event(n, func);
                                }
                            });
                        } else {
                            this.reg_event(n, func);
                        }
                    }
                }
            });
            this.addEvent("igk-eventcreated", { name: null });
            // good way to register function extension according to tag name
            var tag = element.tagName;
            if (tag) {
                var b = igk.system.createExtensionProperty(tag, "igk.dom.extension", this);
            }
        }
    };

    function __extensionPrototype(tag) {
        this.ctag = tag;
        igk_defineProperty(this, "tagName", { get: function() { return this.ctag } });
        igk_defineProperty(this, "IsExtension", { get: function() { return true; } });
        this.__proto__ = __igk_nodeProperty.prototype;
    }
    var __prop = __igk_nodeProperty.prototype;
    igk_appendProp(__prop, {
        setConfig: function(k, v) {
            this.getConf()[k] = v;
        },
        getConfig: function(k) {
            var m_config = this.getConf();
            return k in m_config ? m_config[k] : null;
        },
        getConfigKeys: function() {
                var t = [];
                var m_config = this.getConf();
                for (var i in m_config) {
                    t.push(i);
                }
                return t;
            }
            // -------------------------------
            // -------------- SET PROPERTIES			
            // -------------------------------
            ,
        toString: function() { return "igk_node_properties[" + this.o + "]"; },
        get: function(s, d) {
            if (typeof(d) == 'undefined') {
                d = 0;
            }
            var _t = s.split('/');
            var _q = this;
            for (var i = 0; i < _t.length - 1; i++) {
                if (_q[_t[i]])
                    _q = _q[_t[i]];
                else {
                    _q[_t[i]] = {};
                    _q = _q[_t[i]];
                }
            }
            return _q[_t[i]] ? _q[_t[i]] : d;
        },
        unregister: function() {
            // unregister functions. delete mecanism
            // unregevents 
            var m_unregfuncs = this.getUnregfunc();
            igk.winui.unreg_system_event_object(this);
            for (var i = 0; i < m_unregfuncs.length; i++) {
                m_unregfuncs[i].apply(this);
            }
        },
        getParentCtrl: function(func) {
            return $igk(igk.ctrl.getParentController(this.o));
        },
        registerUnregCallback: function(func) { // register function that will be call on unreg mecanism
            if (func) {
                m_unregfuncs.push(func);
            }
        },
        // -------------- DOM FUNCTIONS
        add: function(t, properties) {
            if (typeof(t) == "string") {
                var c = $igk(this.appendNChild(t));
                c.setProperties(properties);
                return c;
            }
            return this.appendChild(t);
        },
        addText: function(s) {
            var i = document.createTextNode(s);
            return this.add(i);
        },
        addComment: function(txt) {
            var i = document.createComment(txt);
            return this.add(i);
        },
        addXml: function(tag) {
            var _export = $igk(document.createElementNS(igk.namespace.XML, tag));
            this.add(_export);
            return _export;
        },
        addNode: function(tag) {
            var _export = document.createElementNS(this.o.namespaceURI, tag);
            this.o.appendChild(_export);
            return _export;
        },
        isOnDocument: function() { // get if this node is present to document
            var q = 0;
            if (this.isSr())
                return 0;
            q = this;
            let b  = igk.dom.body().o;
            while ((q = q.o.parentNode) && (q != b)) {
                // 
                q = $igk(q);
            }
            return q == b;
        },
        prepend: function(n) { // prepend child
            var i = null;
            if (typeof(n) == "string")
                i = igk.createNode(n);
            else i = $igk(n);
            if (i == null)
                return null;
            if (this.o.firstChild == null) {
                this.o.appendChild(i.o);
            } else // insert before 
            {
                this.o.insertBefore(i.o, this.o.firstChild);
            }
            return i;
        },
        replace: function(i, by) {
            if (!this.isSr()) {
                this.o.replaceChild($igk(by).o, $igk(i).o);
            } else {
                this.o.each(this.replace, arguments);
            }
            return this;
        },
        fullscreen: function() {
            if (!this.isSr()) {
                var fc = igk.fn.getItemFunc(this.o, "requestFullScreen");
                if (fc) {
                    igk.publisher.publish("sys://dom/beforefullsizeRequest");
                    fc.apply(this.o);
                }
            }
            return this;
        },
        addSpace: function() {
            if (this.isSr()) {
                this.each(this.addSpace, arguments);
            } else
                this.o.appendChild(igk.createHtml("&nbsp;").o);
            return this;
        },
        // style functions
        // -------------- STYLES FUNCTIONS
        setOpacity: function(v) {
            if (this.isSr()) {
                this.o.each(this.setOpacity, arguments);
                return this;
            } else {
                this.o.style.opacity = v;
                if (this.o.style.filter) { // for internet explorer
                    this.o.style["filter"] = "alpha(opacity=" + (v * 100) + ")";
                }
                this.o.style["-moz-opacity"] = v;
                this.o.style["-khtml-opacity"] = v;
            }
            return this;
        },
        setBoxShadow: function(offsetX, offsetY, blur, color) {
            // mod,ie10,chrome
            this.o.style["boxShadow"] = offsetX + " " + offsetY + " " + blur + " " + color;
        },
        setTextShadow: function(offsetX, offsetY, blur, color) {
            // mod,ie10,chrome
            this.o.style["textShadow"] = offsetX + " " + offsetY + " " + blur + " " + color;
        },
        focus: function() { if (this.o.focus) this.o.focus(); },
        click: function() {
            if (this.o.click) {
                this.o.click();
            } else {
                var e = document.createEvent('HTMLEvents');
                e.initEvent("click", true, true);
                this.o.dispatchEvent(e);
            }
            return this;
        },
        clone: function() { // return a clone object of this node
            var cl = igk.createNode(this.o.tagName.toLowerCase(), this.o.namespaceURI);
            cl.copyAttributes(this);
            if (this.o.childNodes.length > 0) {
                for (var i = 0; i < this.o.childNodes.length; i++) {
                    if (this.o.childNodes[i].tagName)
                        cl.add($igk(this.o.childNodes[i]).clone());
                    else {
                        cl.o.appendChild(document.createTextNode($igk(this.o.childNodes[i]).text()));
                    }
                }
            }
            return cl;
        },
        copyAttributes: function(i) {
            i = $igk(i);
            if ((i == null) || !i.o.hasAttributes || !i.o.attributes)
                return;
            var j = "";
            for (var k = 0; k < i.o.attributes.length; k++) {
                j = i.o.attributes[k];
                try {
                    this.o.setAttribute(j.name, j.value);
                } catch (Exception) {
                    console.debug("error when try to copy " + j.name);
                }
            }
            return this;
        },
        each: function(func, args) {
            if (this.o.each)
                this.o.each(func, args);
            return this;
        },
        each_all: function(func, args) {
            if (this.o.each_all)
                this.o.each_all(func, args);
            return this;
        },
        getItemAt: function(index) {
            if (this.o.getItemAt) {
                return this.o.getItemAt(index);
            }
            return null;
        },
        index: function() { // get index of the element
            var k = 0;
            e = this.o;
            while (('previousCibling' in e) && ((e = e.previousCibling) != null)) {
                k++;
            }
            return k;
        },
        first: function() { // get the first
            if (this.o.getItemAt) {
                return this.o.getItemAt(0);
            }
            return null;
        },
        last: function() { // get the last
            if (this.o.getItemAt) {
                return this.o.getItemAt(this.o.getCount() - 1);
            }
            return null;
        },
        found: function() { // get if selection get on on more items 
            if (this.o.getItemAt) {
                return this.o.getCount() > 0;
            }
            return !1;
        },
        getNodeAt: function(index) {
            if (this.o.getNodeAt) {
                return this.o.getNodeAt(index);
            }
            return this.o.ChildNodes[index];
        },
        getCount: function() {
            if (this.o.getCount)
                return this.o.getCount();
            return 0;
        },
        isSr: function() { // check if selector. the each method is define
            if (this.o.each)
                return !0;
            return !1;
        },
        getChildCount: function() {
            if (this.o.childNodes) {
                return this.o.childNodes.length;
            }
            return 0;
        },
        timeOut: function(time, func) { // time out function class/ uses with animation callback
            var q = this;
            var s = this.timeOutList.length;
            var i = setTimeout(function() {
                func.apply(q);
                q.timeOutList.pop(s);
            }, time);
            q.timeOutList.push(i);
            return this;
        },
        clearTimeOut: function() {
            while (this.timeOutList.length > 0) {
                clearTimeout(this.timeOutList[0]);
                this.timeOutList.pop(0);
            }
            return this;
        },
        on: function(method, func, useCapture) {
            /**
            // sample: target.on('click', func) ; shortcut to reg_event
            // note : 
            */
            if (this.o.each) {
                this.o.each(this.on, arguments);
                return this;
            } else {
                igk_winui_reg_event(this.o, method, func, useCapture);
                return this;
            }
        },
        waitInterval: function(duration, func) {
            if (this.isSr()) {
                this.o.waitInterval(duration, func);
            } else {
                var q = this;
                setTimeout(function() {
                    func.apply(q)
                }, duration);
            }
            return this;
        },
        css: function(value) {
            if (this.o.each) {
                this.o.each(this.css, arguments);
                return this;
            } else {
                if ($igk.isString(value))
                    this.o.style = value;
                else {
                    this.setCss(value);
                }
                return !0;
            }
        },
        getCssSelector: function() { // get style node selection
            var o = 0;
            var cl = 0;
            o = this.o.tagName.toLowerCase();
            if (this.o.id)
                o += "#" + this.o.id;
            cl = this.o.className;
            if (cl) {
                var t = cl.split(" ");
                for (var k = 0; k < t.length; k++) {
                    o += "." + t[k];
                }
            }
            return o;
        },
        getCssValue: function() {
            var o = "";
            var v, d;
            var prop = igk.css.getProperties();
            var q = this;
            var check = typeof(window.getDefaultComputedStyle) == 'function' ? function(n, d) {
                return window.getDefaultComputedStyle(q.o).getPropertyValue(n) != d;
            } : function(n, d) {
                var domStyle = igk.css.getDomStyle();
                return (d != domStyle[n]);
            };
            for (var i in prop) {
                v = i;
                if ((d = this.getComputedStyle(i)) && check(i, d)) //o.style[i];
                {
                    o += v + ":" + d + "; ";
                }
            }
            return o;
        },
        disablecontextmenu: function() {
            if (this.isSr()) {
                this.o.each(this.editable, arguments);
                return this;
            } else {
                this.on("contextmenu", igk.winui.events.cancelBehaviour);
            }
            return this;
        },
        selectable: function(v) {
            if (this.isSr()) {
                this.o.each(this.selectable, arguments);
                return this;
            } else {
                if (!v) {
                    igk.ctrl.selectionmanagement.disable_selection(this);
                } else {
                    igk.ctrl.selectionmanagement.enableSelection(this);
                }
            }
            return this;
        },
        editable: function(v) {
            if (this.isSr()) {
                this.o.each(this.editable, arguments);
                return this;
            } else {
                this.o.contentEditable = v;
            }
            return this;
        },
        autocorrect: function(v) {
            if (this.isSr()) {
                this.o.each(this.autocorrect, arguments);
                return this;
            } else {
                this.o.setAttribute("spellcheck", v);
            }
            return this;
        },
        setCss: function(properties) {
            if (this.isSr()) {
                this.o.each(this.setCss, arguments);
                return this;
            } else {
                igk.css.setProperties(this.o, properties);
            }
            return this;
        },
        height: function() {
            var h = this.getComputedStyle("height");
            return igk.getNumber(h, this.o, "height");
        },
        width: function() {
            var h = this.getComputedStyle("width");
            return igk.getNumber(h, this.o, "width");
        },
        hasHCroll() {
            return (this.o.scrollTopMax || (this.o.scrollHeight - this.o.offsetHeight)) > 0;
        },
        setCssAssert: function(c, p) // conditional css property
            {
                if (c) {
                    this.setCss(p);
                }
                return this;
            },
        setProperties: function(properties) {
            if (!properties) return this;
            if (this.isSr()) {
                this.o.each(this.setProperties, arguments);
            } else {
                for (var i in properties) {
                    try {
                        this.o[i] = properties[i];
                    } catch (Ex) {
                        console.debug("can't  set property " + i);
                    }
                }
            }
            return this;
        },
        hide: function() {
            this.setCss({ "display": "none" });
        },
        show: function() {
            this.setCss({ "display": "inline-block" });
        },
        fadein: function(interval, duration, opacityOrProperties, callback) {
            window.igk.animation.fadein(this.o, interval, duration, opacityOrProperties, callback);
            return this;
        },
        fadeout: function(interval, duration, opacityOrProperties, callback) {
            window.igk.animation.fadeout(this.o, interval, duration, opacityOrProperties, callback);
            return this;
        },
        getoffsetParent: function() {
            if (this.isSr()) {
                return this;
            }
            return $igk(igk.winui.GetRealOffsetParent(this.o));
        },
        getscrollParent: function() {
            if (this.isSr()) {
                return this;
            }
            return $igk(igk.winui.GetRealScrollParent(this.o));
        },
        appendProperties: function(properties, override) {
            if (!properties)
                return this;
            if (this.isSr()) {
                this.each(this.appendProperties, arguments);
                return this;
            } else {
                igk.appendProperties(this, properties, override);
            }
            return this;
        },
        // -------------------------------------------------------------
        // events
        // -------------------------------------------------------------
        dispatchEvent: function(evt) {
            if (this.o.dispatchEvent) {
                this.o.dispatchEvent(evt);
            } else {
                if (document.dispatchEvent) {
                    document.dispatchEvent(evt);
                }
            }
        },
        raiseEvent: function(n, p, callback) {
            // raise custom event
            // n: event name
            // p: properties
            if (!this.isSr()) {
                var e = this.o[n];
                if (e != null) {
                    // ei make some error
                    if (arguments.length > 1) {
                        igk.appendProperties(e, arguments[1]);
                    }
                    try {
                        if (this.o.dispatchEvent) {
                            this.o.dispatchEvent(e);
                        } else {
                            if (document.dispatchEvent)
                                document.dispatchEvent(e);
                            else {
                                document.documentElement[n]++;
                            }
                        }
                    } catch (ex) {
                        console.debug(n + " " + ex.message + " ");
                    }
                }
                if (callback) {
                    callback(e);
                }
            }
            return this;
        },
        /**
         * register event 
         * @param {*} n event name
         * @param {*} p event property {}|
         */
        addEvent: function(n, p) {
            // add custom event to property
            // n: name
            // p: propertie for the event 
            var q = this;
            if (!this.isSr()) {
                var e = null;
                if (typeof(Event) == IGK_FUNC) { // check if window object event exists
                    var _init = {};
                    if (p.cancelable) {
                        _init.cancelable = true;
                    }
                    if (p.composed) {
                        _init.composed = true;
                    }
                    if (p.bubbles) {
                        _init.bubbles = true;
                    }
                    e = new Event(n, _init);
                } else {
                    if (document.createEvent) {
                        e = document.createEvent('CustomEvent');
                        // forget this will raise UnpsecifiedEventTypeError in ie7
                        e.initCustomEvent(n, true, false, {});
                        igk_appendProp(e, p);
                        igk_appendProp(e, { fordoc: true });
                    } else {
                        // this will raise UnpsecifiedEventTypeError in ie8 not supporting createEvent Method
                        // alert(" disp "+document.dispatchEvent);
                        // e= document.createEventObject(window.event); // document.createEventObject(q.o);
                        // igk_appendProp(e,p);
                        // igk_appendProp(e,{fordoc:true});
                        e = new __igk_event(q, p, n);
                    }
                }
                if (e != null) {
                    try {
                        // + | register event 
                        this.o[n] = e;
                        this.raiseEventCreated(n);
                    } catch (e) {
                        // + | failed to add property 
                    }
                }
            }
            return this;
        },
        getParentBody: function() {
            if (this.isSr()) { return null; }
            var q = this.o;
            while (q && (q.tagName.toLowerCase() != "body")) {
                q = q.parentNode;
            }
            return q;
        },
        disabled: function(d) {
            if (this.isSr()) {
                this.o.each(this.disabled, arguments);
            } else {
                if (d) {
                    this.o.setAttribute("disabled", "disabled");
                } else {
                    this.o.removeAttribute("disabled");
                }
            }
            return this;
        },
        // class
        addClass(classname) {
            function __appendClass(t, tab) {
                var ch = 0;
                var s = "";
                var k = "";
                var st = {};
                if (t.o.className && !t.o.className.indexOf) {
                    // class name is not a string
                    // svg case 
                    console.error("object.className is not a string (probably svg case) : " + t.o.className);
                    return ch;
                }
                var ucl = typeof(t.o.classList) != 'undefined';
                for (var i = 0; i < tab.length; i++) {
                    k = igk_str_trim(tab[i]);
                    if ((k.length == 0) || st[k]) {
                        continue;
                    }
                    st[k] = 1;
                    if (ucl) {
                        t.o.classList.add(k);
                        continue;
                    }
                    if ((k.length == 0) || (t.o.className && (t.o.className.indexOf(k) !== -1)))
                        continue;
                    if (s.length > 0)
                        s += " ";
                    s += k;
                    ch = !0;
                }
                if (!ucl && (s.length > 0)) {
                    if (t.o.className == " ")
                        t.o.className = igk_str_trim(ts);
                    else {
                        t.o.className = igk_str_trim(t.o.className + " " + s);
                    }
                }
                return ch;
            };

            function __rmClass(t, tab) {
                var g = t.o.className.split(' ');
                var ch = 0;
                var s = "";
                var ucl = t.o.classList;
                if (ucl) {
                    tab.map(function(i) { ucl.remove(i); });
                    return 1;
                }
                for (var i = 0; i < g.length; i++) {
                    if (g[i] in tab) {
                        ch = 1;
                    } else {
                        if (s.length > 0)
                            s += " ";
                        s += g[i];
                    }
                }
                t.o.className = s;
                return ch;
            };
            if (this.isSr()) {
                this.o.each(this.addClass, arguments);
                return this;
            } else {
                var tab = [];
                if ((typeof(classname) === "string") && (classname.length > 0)) {
                    var ch = false;
                    if (typeof(this.o.className) == IGK_UNDEF) {
                        this.o.className = classname;
                        ch = true;
                    } else {
                        tab = classname.split(" ");
                        ch = __appendClass(this, tab);
                    }
                } else {
                    var rm = [];
                    var s = "";
                    for (var i in classname) {
                        s = igk_str_trim(i);
                        if (((typeof(classname[i]) == "function") && classname[i]()) ||
                            classname[i]) {
                            tab.push(s);
                        } else {
                            rm.push(s);
                        }
                    }
                    ch = __appendClass(this, tab) || __rmClass(this, rm);
                }
                if (ch) {
                    igk.publisher.publish("sys://dom/classchanged", { target: this, className: classname });
                }
            }
            return this;
        },
        // remove class 
        rmClass: function(classname) {
            if (typeof classname === "string") {
                classname = igk_str_trim(classname);
                if (classname.length == 0) {
                    return this;
                }
                if (this.isSr()) {
                    this.o.each(this.rmClass, arguments);
                    return this;
                } else {
                    var tab = classname.split(" ");
                    var cur = this.o.className.split(" ");
                    var rms = "";
                    var index = 0;
                    var s = "";
                    if (cur) {
                        for (var i = 0; i < tab.length; i++) {
                            s = tab[i];
                            //if(s == classname){
                            index = cur.indexOf(s);
                            if (index != -1) {
                                delete(cur[index]);
                            }
                        }
                        var h = igk_str_trim(cur.join(" "));
                        if (h.length == 0) {
                            this.o.className = "";
                            this.o.removeAttribute("class");
                        } else {
                            this.o.className = h;
                        }
                    }
                }
            }
            return this;
        },
        rmAllClass: function(rg) { // remove all class that match the pattern. space is used as separator
            if (typeof rg === "string") {
                if (this.isSr()) {
                    this.o.each(this.rmClass, arguments);
                    return this;
                } else {
                    var cur = this.o.className;
                    var tab;
                    var _rg;
                    if (cur) {
                        tab = cur.split(" ");
                        _rg = new RegExp(rg);
                        var o = "";
                        for (var i = 0; i < tab.length; i++) {
                            if (_rg.test(tab[i])) {
                                continue;
                            }
                            o += tab[i] + " ";
                            // cur = cur.replace(_rg, '');		
                        }
                        cur = o;
                        var h = igk_str_trim(cur);
                        this.o.className = h;
                        igk.success = 1;
                    } else
                        igk.success = 0;
                }
            }
            return this;
        },
        // replace class
        rpClass: function(oldcl, newcl) {
            if (typeof oldcl === "string") {
                if (this.isSr()) {
                    this.o.each(this.rpClass, arguments);
                    return this;
                } else {
                    var tab = oldcl.split(" ");
                    var cur = this.o.className;
                    var rms = "";
                    var index = 0;
                    var s = "";
                    var removing = false;
                    if (cur) {
                        for (var i = 0; i < tab.length; i++) {
                            s = tab[i];
                            index = cur.indexOf(s);
                            while ((index >= 0) && ((index + s.length) <= cur.length)) {
                                cur = cur.replace(tab[i], "");
                                index = cur.indexOf(tab[i]);
                                removing = !0;
                            }
                        }
                        var h = igk_str_trim(cur);
                        if (newcl)
                            h += " " + newcl;
                        this.o.className = h;
                    }
                }
            }
            return this;
        },
        // determine 
        supportClass(classname) {
            if (this.isSr()) {
                return !1;
            }
            if (this.o.classList) {
                return this.o.classList.contains(classname);
            }
            var cl = this.o.className;
            var exp = "($|[\\s]?)(" + classname + "){1}($|[\\s]+)";
            var rg = new RegExp(exp, "g");
            if (rg.test(cl)) {
                return !0;
            }
            return !1;
        },
        toggleClass: function(cl) {
            if (this.isSr()) {
                return !1;
            } else {
                if (this.supportClass(cl)) {
                    this.rmClass(cl);
                } else {
                    this.addClass(cl);
                }
            }
            return this;
        },
        qselect: function(pattern) {
            if (this.isSr()) {
                return this.o.qselect(pattern);
            }
            // select with global 
            return igk.qselect(this.o, pattern);
        },
        select: function(pattern) { // igk node utility selector
            // select with pattern					
            if (this.isSr()) {
                return this.o.select(pattern);
            }
            // select with global 
            return igk.select(this.o, pattern);
        },
        wait: function(t, func) {
            // wait on the function
            // if (return 1: finish ) if return 0: continue to wait
            var q = this;

            function _wait_callback() {
                if (!func.apply(q)) {
                    setTimeout(_wait_callback, t);
                }
            }
            setTimeout(_wait_callback, t);
        },
        getForm: function() {
            if (this.o.form)
                return this.o.form;
            return igk.getParentByTagName(this.o, "form");
        },
        getAttributeCount: function() {
            if (this.o.attributes)
                return this.o.attributes.length;
            return 0;
        },
        replaceOwner: function(d) {
            if (d) {
                m_o = d;
            }
        },
        replaceTagWith: function(nTagName) { // replace tag name
            if (this.isSr()) {
                this.o.each(this.replaceTagWith, arguments)
            } else {
                var d = igk.createNode(nTagName);
                d.setHtml(this.o.innerHTML);
                igk.dom.copyAttributes(this.o, d.o);
                igk.dom.replaceChild(this.o, d.o);
                this.replaceOwner(d.o);
            }
            return this;
        },
        view: function(p) {
            // make this element visible by scrolling to it
            if (this.isSr()) {
                return this;
            }
            p = p || this.getscrollParent();
            if (p) {
                if (p.o.scroll)
                    p.o.scroll(this.o.offsetLeft, this.o.offsetTop);
            }
            return this;
        },
        scroll: function(x, y) {
            // change scroll button
            this.o.scrollLeft = x;
            this.o.scrollTop = y;
            if (this.o.scrollLeft != x) {
                console.error("/!\\ scroll x not changed: " + this.o.scrollLeft + "=>" + x);
            }
            if (this.o.scrollTop != y) {
                console.error("/!\\ scroll y not changed: " + this.o.scrollTop + "=>" + y);
            }
        },
        animate: function(propertyToAnimate, animationProperty) { // used to animate this element
            if (this.isSr()) {
                this.each(this.animate, arguments);
            } else {
                igk.animation.animate(this.o, propertyToAnimate, animationProperty);
            }
            return this;
        },
        animateUpdate: function(animationProperty) {
            igk.animation.animateUpdate(this.o, animationProperty);
            return this;
        },
        scrollTo: function(t, property, callback) {
            // igk[object].scrollTo:
            // >t : la cible contenue par cet élément
            // >property is an object { interval: @time ,duration: @time,speed : @speding,orientation : @orientation}
            if ((t == null) || typeof(t) != 'object')
                return;
            if ((property == null) || (typeof(property) == IGK_UNDEF)) {
                property = {
                    duration: 500,
                    interval: 20,
                    effect: "linear",
                    effectmode: "easeinout"
                };
            }
            var ts = igk.fn.isItemStyleSupport(this.o, "transition");
            var trf = igk.fn.isItemStyleSupport(this.o, "transform");
            var it = $igk(t);
            var self = this;
            var counter = 0;
            // item list
            var m_il = new igk.system.collections.list();
            var scrollprop = null;
            var m_duration_ms = property ? igk.datetime.timeToMs(property.duration) : null;
            var mout = null; // count time out
            function __init_scroll() { // on 		
                var offsetParent = it.getscrollParent();
                if (offsetParent == null) {
                    return null;
                }
                return new(function() {
                    var q = this;
                    var orientation = "vertical";
                    var distance = 0;
                    var d = 0;
                    var o = self.o;
                    var v_loc = self.getLocation(); // parent location
                    var startpos = 0,
                        endpos = 0,
                        endscroll = 0;
                    var pos = 0;
                    v_loc.y += self.o.scrollTop;
                    v_loc.x += self.o.scrollLeft;
                    var v_ttloc = it.getLocation();
                    v_ttloc.y += it.o.scrollTop;
                    v_ttloc.x += it.o.scrollLeft;
                    var v_tloc = it.getscrollLocation(offsetParent.o);
                    // fixed data
                    // if((v_loc.y==v_tloc.y) &&(v_loc.x==v_tloc.x))
                    // {
                    // don't translate							
                    // return;
                    // }
                    if (!property.orientation) {
                        // auto detect orientation 
                        var pp = it.getOffsetScreenLocation(self);
                        orientation = pp.orientation;
                    } else {
                        orientation = property.orientation ? property.orientation : "vertical";
                    }
                    if (orientation == "vertical") {
                        startpos = v_loc.y; // q.scrollTop;							
                        endscroll = v_tloc.y; // target.offsetTop; // (target.offsetTop + target.offsetHeight) - q.clientHeight; // (this.getOwner().o.clientHeight);
                        d = endscroll - startpos; // -0;// - v_loc.y;						
                        q.dir = (d > 0) ? "godown" : "goup";
                    } else {
                        startpos = v_loc.x; // q.scrollLeft;
                        endscroll = v_tloc.x; // target.offsetLeft;// + target.offsetWidth) - q.clientWidth;// + q.scrollLeft;							
                        d = endscroll - startpos; // - 0;// v_loc.x;
                        q.dir = (d > 0) ? "goright" : "goleft";
                    }
                    // calculate the normal step	
                    distance = Math.abs(d);
                    var sign = d >= 0 ? 1 : -1;
                    q.distance = distance;
                    q.sign = sign;
                    q.target = t;
                    igk.appendProperties(this, {
                        getTransform: function() {
                            if (orientation == "vertical")
                                return "TranslateY(" + (-sign * distance) + "px)";
                            return "TranslateX(" + (-sign * distance) + "px)";
                        },
                        getStartTransform: function() {
                            if (orientation == "vertical")
                                return "TranslateY(" + startpos + "px)";
                            return "TranslateX(" + startpos + "px)";
                        },
                        resetTransform: function() {
                            if (orientation == "vertical")
                                return "TranslateY(0px)";
                            return "TranslateX(0px)";
                        },
                        getStartScroll: function() {
                            return startpos;
                        },
                        X: function() {
                            if (orientation == "horizontal")
                                return startpos + (sign * distance);
                            return 0;
                        },
                        Y: function() {
                            if (orientation == "vertical")
                                return startpos + (sign * distance);
                            return 0;
                        }
                    });
                })();
            };

            function __clearTransition() {
                self.select('>>').each_all(function() {
                    if (!this.o.nodeType || (this.o.nodeType != 1) || (this.o.tagName.toLowerCase() == 'script'))
                        return;
                    if (!this.o.style ||
                        (property.filter && property.filter(this))) {
                        return;
                    }
                    this.rmClass("igk-trans-all-200ms")
                        .setCss({
                            transform: scrollprop.resetTransform(),
                            transitionDuration: null
                        });
                });
                var pp = scrollprop;
                self.scroll(pp.X(), pp.Y());
                // self.setCss({"transform":"translate("+pp.X()+"px, "+pp.Y()+"px)"});
                if (callback) {
                    callback({ "type": "transition", target: t, x: pp.X(), y: pp.Y() });
                }
            }

            function __transition_end(evt) {
                var g = $igk(igk.winui.eventTarget(evt));
                m_il.remove(g);
                counter--;
                if (counter <= 0) {
                    __clearTransition();
                } else {
                    if (mout)
                        clearTimeout(mout);
                    mout = setTimeout(function() {
                        if (counter > 0) {
                            __clearTransition();
                            counter = 0;
                        }
                    }, 500);
                }
                igk.winui.unreg_event(this, 'transitionend', __transition_end);
            };
            if (ts && trf) { // item support animation and transitions
                scrollprop = __init_scroll();
                counter = 0;
                if (scrollprop && scrollprop.distance != 0) {
                    if (!scrollprop.getTransform) {
                        throw ("[IGK] no scrollprop.getTransform function found");
                        return;
                    }
                    counter = 0;
                    self.select('>>')
                        .each_all(function() {
                            // increment counter if this content style
                            // check if this contain style property and is filtered
                            if (!this.o.nodeType || (this.o.nodeType != 1) || (this.o.tagName.toLowerCase() == 'script'))
                                return;
                            if (!this.o.style || (property.filter && property.filter(this))) {
                                return;
                            }
                            // if(this.getComputedStyle("display")=='none'){
                            // no display does't recieve thransition end. non visible item . 
                            // return;
                            // }
                            counter++;
                            m_il.add(this);
                            this.reg_event("transitionend", __transition_end);
                            this.addClass("igk-trans-all-200ms")
                                .setCss({
                                    "transform": scrollprop.getTransform()
                                });
                        });
                } else {
                    if (callback)
                        callback();
                }
                // end transition end properties
                return;
            }
            if (m_anim && m_anim.scrolling) {
                // stop scrolling of this item
                m_anim.scrolling.stop();
            }
            if (property == null) {
                target.scrollIntoView();
                if (callback != null)
                    callback({ "type": "scrollIntoView" });
                return !0;
            }
            var anim1 = igk.animation.init(
                this,
                property.interval,
                property.duration,
                function() { // init
                    // store animation context
                    var self = this;
                    self.offsetParent = $igk(t).getscrollParent();
                    if (self.offsetParent == null)
                        return;
                    var d = 0;
                    var q = self.getOwner().o;
                    var v_loc = self.getOwner().getLocation(); // parent location
                    v_loc.y += q.scrollTop;
                    v_loc.x += q.scrollLeft;
                    var v_tloc = $igk(t).getscrollLocation(self.offsetParent.o);
                    self.speed = property.speed ? property.speed : 0.05;
                    if (!property.orientation) {
                        // auto detect orientation 
                        if (v_loc.y == v_tloc.y)
                            self.orientation = "horizontal";
                        else
                            self.orientation = "vertical";
                    } else {
                        self.orientation = property.orientation ? property.orientation : "vertical";
                    }
                    self.pos = 0;
                    if (self.orientation == "vertical") {
                        self.startpos = v_loc.y; // q.scrollTop;							
                        self.endscroll = v_tloc.y; // target.offsetTop; // (target.offsetTop + target.offsetHeight) - q.clientHeight; // (this.getOwner().o.clientHeight);
                        d = self.endscroll - self.startpos; // -0;// - v_loc.y;						
                        self.dir = (d > 0) ? "godown" : "goup";
                    } else {
                        self.startpos = q.scrollLeft;
                        self.endscroll = v_tloc.x; // target.offsetLeft;// + target.offsetWidth) - q.clientWidth;// + q.scrollLeft;							
                        d = self.endscroll - self.startpos; // - 0;// v_loc.x;
                        self.dir = (d > 0) ? "goright" : "goleft";
                    }
                    // calculate the normal step						
                    self.distance = Math.abs(d);
                },
                function() { // update 
                    var d = this.distance;
                    if (d == 0) {
                        return !0;
                    }
                    var v_o = this.offsetParent != null ? this.offsetParent : this.getOwner();
                    var y = 0;
                    var end = false;
                    var f = this.getStepfactor();
                    var sign = (this.dir == "goleft") || (this.dir == "goup") ? -1 : 1;
                    y = this.startpos + (sign * f * d); // ( this.step * 2 * Math.sin(Math.PI * this.getEllapsed()/this.getDuration())));	
                    this.pos = y; // Math.min(this.startpos +d ,y);		
                    end = (this.getEllapsed() / this.getDuration()) >= 1.0;
                    // (y- this.startpos)==d);// d( this.pos - d);(this.pos >=this.startpos +d);							
                    switch (this.dir) {
                        case "godown":
                            v_o.scroll(0, y); // this.startpos + this.pos); 							
                            break;
                        case "goup":
                            v_o.scroll(0, y); // this.startpos - this.pos); 							
                            break;
                        case "goright":
                            v_o.scroll(y, 0); // (this.startpos + this.pos,0); 
                            break;
                        case "goleft":
                            v_o.scroll(y, 0); // this.startpos - this.pos,0); 
                            break;
                        default:
                            return !1;
                    }
                    return !end;
                },
                function() { // end
                    var v_o = this.getOwner();
                    if (m_anim && m_anim.__anim && m_anim.__anim.scrolling)
                        delete m_anim.__anim.scrolling;
                    if (callback)
                        callback({ "type": "igk.animation" });
                    // delete m_anim;						
                }
            );
            m_anim = createPNS(this, "__anim", { "scrolling": anim1 });
            m_anim.type = "scrolling";
            anim1.properties = property;
            anim1.start();
            return !0;
        },
        // return the first matching parent with tag name
        getParentByTagName: function(tag) { return igk_getParentByTagName(this.o, tag); },
        // return the first parent by id
        getParentById: function(id) { return igk_getParentById(this.o, id); },
        // T: function,remove element
        remove: function() {
            if (this.isSr()) {
                this.o.each(this.remove, arguments);
                // return this;
            } else {
                if (this.o.parentNode)
                    this.o.parentNode.removeChild(this.o);
                // return !0;
            }
            return this;
        },
        transEnd: function(n, t) {
            var q = this;

            function __function_end(evt) {
                if (evt.target == q.o) {
                    if (typeof(n) == "function")
                        n.apply(q);
                    else {
                        if (n in q) {
                            q[n].apply(q);
                        }
                    }
                    q.unreg_event("transitionend", __function_end);
                }
            };
            if (this.isSr()) {
                this.o.each(q.transEnd, arguments);
            } else {
                this.reg_event("transitionend", __function_end);
            }
            return q;
        },
        loadStringAsHtml: function(s, evalscript) {
            if (this.o.each) {
                this.o.each(this.loadStringAsHtml, arguments);
            } else {
                if (typeof(this.o.appendChild) != "undefined") {
                    var m = igk.createNode("dummyNode");
                    m.setHtml(s);
                    if (m.o.childNodes.length == 1) {
                        var p = m.o.childNodes[0];
                        var c = p.childNodes.length;
                        this.o.innerHTML = "";
                        while (c > 0) {
                            this.o.appendChild(p.childNodes[0]);
                            c--;
                        }
                    }
                }
                if (evalscript)
                    igk.system.evalScript(this.o);
            }
            return this;
        },
        istouchable: function() { // utility
                if (
                    (typeof(this.o.ontouchstart) != IGK_UNDEF) &&
                    (typeof(this.o.ontouchend) != IGK_UNDEF) &&
                    (typeof(this.o.ontouchmove) != IGK_UNDEF) &&
                    (typeof(this.o.ontouchcancel) != IGK_UNDEF)
                )
                    return !0;
                return !1;
            }
            // checking css properties 
            ,
        isCssSupportTransition: function() {
            return igk.fn.isItemSupport(this.o.style, ['transition', 'webkitTransition', 'msTransition', 'oTransition']);
        },
        isCssSupportAnimation: function() {
            return igk.fn.isItemSupport(this.o.style, ['animation', 'webkitAnimation', 'msAnimation', 'oTransition']); // igk_checkOnePropertyExists(this.o.style,"animation");
        },
        setTransitionDelay: function(time) {
            if (this.o.each) {
                this.o.each(this.setTransitionDelay, arguments);
            } else
                igk.css.setTransitionDelay(this, time);
            return this;
        },
        setTransition: function(k) {
            if (this.o.each) {
                this.o.each(this.setTransition, arguments);
            } else
                this.setCss({ transition: k });
            return this;
        },
        setTransitionDuration(time) {
            if (this.o.each) {
                this.o.each(this.setTransitionDuration, arguments);
            } else
                igk.css.setTransitionDuration(this, time);
            return this;
        },
        replaceWith(t) {
            // t:target node or text
            // desc: replace content of the current nodes list by t. t 
            if (this.o.each) {
                this.o.each(this.replaceWith, arguments);
            } else {
                this.clearAttributes();
                igk.dom.copyAttributes(this.o, $igk(t).o);
                this.setHtml($igk(t).getHtml());
            }
            return this;
        },
        replaceBy: function(t) {
            if (this.o.each) {
                this.o.each(this.replaceBy, arguments);
            } else {
                var p = this.o.parentNode;
                if (p) {
                    this.insertBefore($igk(t).o);
                    this.remove();
                }
            }
            return this;
        },
        clearAttributes: function() {
            while (this.o.attributes.length > 0) {
                this.o.removeAttribute(this.o.attributes[0].name);
            }
            return this;
        },
        text: function() {
            if (this.o.each) {
                this.o.each(this.text, arguments);
            } else {
                return this.o.textContent || this.o.text || this.o.innerText;
                // if ("textContent" in this.o)
                // 	return this.o.textContent;
                // return ("data" in this.o) ? this.o.data : null
            }
        },
        setHtml: function(v, evalScript) { // set innerHTML  @v:text context ,@eval:bool true to evaluate . default is false
            if (this.o.each) {
                this.o.each(this.setHtml, arguments);
            } else {
                try {
                    this.o.innerHTML = v;
                    if (evalScript) {
                        igk.system.evalScript(this.o);
                    }
                } catch (ex) {
                    console.debug("set:InnerHtml failed : " + v + "\n" + ex);
                }
            }
            return this;
        },
        getText: function() {
            var s = "";
            if (this.o.innerHTML) {
                s = igk.html.string(this.getHtml());
            }
            return s;
        },
        /**
         * get element text or 
         */
        text: function() {
            return this.getHtml() || this.o.textContent;
        },
        val: function() {
            if (this.o.each) {
                this.o.each(this.setHtml, arguments);
            } else {
                if (arguments.length > 0) {
                    if ('value' in this.o) {
                        this.o.value = arguments[0];
                    }
                }
                return this.o.value;
            }
            return this;
        },
        // get htmlcontent retreive all content
        getHtml: function() {
            var s = "";
            if (this.o.each) {
                var fc = this;
                var args = arguments;
                this.o.each_all(function() {
                    s += fc.getHtml.apply(this, args);
                });
            } else {
                return ('innerHTML' in this.o) ? this.o.innerHTML : __dom_innerHTML(this.o);
            }
            return s;
        },
        getOuterHtml: function() {
            if (this.o.outerHTML)
                return this.o.outerHTML;
            var s = "";
            if (this.o.each) {
                s += this.o.each(this.getOuterHtml, arguments);
            } else {
                if (typeof(this.o.tagName) != IGK_UNDEF) {
                    s = "<" + this.o.tagName;
                    if (this.o.hasAttributes) {
                        s += " ";
                        for (var i = 0; i < this.o.attributes.length; i++) {
                            var g = this.o.attributes[i];
                            if (i != 0)
                                s += " ";
                            s += g.name + "=\"" + g.value + "\"";
                        }
                    }
                    s += ">";
                    for (var i = 0; i < this.o.childNodes.length; i++) {
                        s += $igk(this.o.childNodes[i]).getOuterHtml();
                    }
                    // s+=this.o.innerHTML;
                    s += "</" + this.o.tagName + ">";
                } else {
                    s += this.o.wholeText;
                }
            }
            return s;
        },
        evalScript: function() {
            if (this.o.each) {
                this.o.each(this.evalScript, arguments);
            } else {
                igk.system.evalScript(this.o);
            }
            return this;
        },
        /**
         * init current node
         */
        init: function() { // init the current node
            if (this.o.each) {
                this.o.each(this.init, arguments);
            } else
                igk.ajx.fn.initnode(this.o);
            return this;
        },
        render: function() {
            var s = "";
            if (this.o.each) {
                s += this.o.each(this.renber, arguments);
            } else {
                return this.o.outerHTML;
            }
            return s;
        },
        getAllAttribs: function() { // return a string of all attribute of this node				
            var msg = "";
            var attrs = null;
            if (this.o.attributes) {
                attrs = this.o.attributes;
                var ln = attrs.length;
                for (var h = 0; h < ln; h++) {
                    try {
                        var j = attrs[h];
                        if (j)
                            msg += j.name + " ";
                    } catch (ex) {
                        console.debug("error on " + h);
                    }
                }
            }
            return msg;
        },
        getParentNode: function() { return $igk(this.o.parentNode); },
        getStyle: function(name) {
            if (!this.isSr()) {
                return igk.css.getValue(this.o, name);
            }
            return "no-value";
        },
        getComputedStyle: function(n, select) {
            //@n property to get
            //@select* : speudo class selector
            if (!this.isSr()) {
                if (window.getComputedStyle) {
                    // get styles
                    if (this.o.nodeType && (this.o.nodeType == 3)) {
                        return null;
                    }
                    var q = window.getComputedStyle(this.o, select);
                    return igk.css.getStyleValue(q, n);
                }
                return null; //"no-value-computed"; 
            }
            return "no-value";
        },
        getComputedEmSize(p, target){
            return igk.css.getComputedEmSize(this, p, target);
        },
        getComputedStylePropertyValue: function(n, select) {
            //@n property to get
            //@select* : speudo class selector
            if (!this.isSr()) {
                var s = null;
                if (window.getComputedStyle && (s = window.getComputedStyle(this.o, select))) {
                    // get styles
                    var q = window.getComputedStyle(this.o, select).getPropertyValue(n);
                    return q;
                }
                return null; //"no-value-computed"; 
            }
            return "no-value";
        },
        setAttributes: function(properties) { if (!properties) return; for (var i in properties) { this.o.setAttribute(i, properties[i]); } return this; }
            // set attributre set attribute as string value that will be interpreted
            ,
        setAttribute: function(name, value) { if (this.o.each) { this.o.each(this.setAttribute, arguments); } else this.o.setAttribute(name, value); return this; },
        setAttributeAssert: function(condition, name, value) {
            if (condition) {
                this.setAttribute(name, value);
            }
            return this;
        },
        setSize: function(w, h) {
                if (h == null) h = w;
                this.setCss({ "width": w, "height": h });
                return this;
            }
            // -------------- GET PROPERTIES
            ,
        getAttribute: function(value) {
            if (typeof(this.o.getAttribute) == 'function')
                return this.o.getAttribute(value);
            return null;
        },
        getChildById: function(id) { return igk_getChildById(this.o, id); },
        getChildsByAttr: function(properties) { return igk_getChildsByAttr(this.o, properties); },
        getParent: function(tagname) { if (tagname) { return this.getParentByTagName(tagname); } return $igk(this.o.parentNode); },
        getParentForm: function() { return this.getParentByTagName("form"); },
        getPixel: function(propName, o) { // return a pixel value
            return igk.getPixel(this.getComputedStyle(propName), o || this.o, propName);
        },
        appendChild: function(name) {
            var item = null;
            if (typeof(name) == "string") { item = document.getElementById(name); } else { item = name; }
            if (item != null) {
                var s = $igk(item);
                this.o.appendChild(s.o);
                return s;
            }
        },
        appendNChild: function(tagname) {
            var item = this.createElement(tagname);
            this.appendChild(item);
            return item;
        },
        insertBefore: function(item) {
            // this.o.insertBefore(i,j); 
            // @ item to add
            var p = 0;
            if (item && (p = this.o.parentNode)) {
                if (p.firstChild == this.o) {
                    if (p.prepend)
                        p.prepend(item);
                    else {
                        p.insertBefore(item, p.firstChild);
                    }
                } else
                    p.insertBefore(item, this.o);
            }
            return this;
        },
        insertAfter: function(item) { // insert item after the node
            if (item && this.o.parentNode) {
                if (this.o.parentNode.lastChild == this.o)
                    this.o.parentNode.appendChild(item);
                else
                    this.o.parentNode.insertBefore(item, this.o.nextSibling);
            }
            return this;
        },
        firstChild: function() { return (this.o.firstChild) ? $igk(this.o.firstChild) : null; },
        firstNode: function(type) {
            // get the first node element type math the requirement
            var r = null;
            this.select('>>').each(function() {
                if (this.o.nodeType == type) {
                    r = this.o;
                    // cancel
                    return !1;
                }
                return !0;
            });
            return r;
        },
        prependChild: function(node) {
            if (this.o.firstChild) this.o.insertBefore(node, this.o.firstChild);
            else { this.o.appendChild(node); }
        },
        reg_event: function(method, func, opts) {
            
            if (this.o.each) {
                this.o.each(this.reg_event, arguments);
            } else {
                igk_winui_reg_event(this.o, method, func, opts);
            }
            return this;
        },
        unreg_event: function(method, func) {
            if (this.o.each) {
                this.o.each(this.unreg_event, arguments);
            } else {
                igk.winui.unreg_event(this.o, method, func);
            }
            return this;
        },
        getOffsetScreenLocation: function(t) { // return the point 
            var v1 = t.getScreenLocation();
            var v2 = { x: this.o.offsetLeft, y: this.o.offsetTop };
            var q = this.o.offsetParent;
            var v3 = { x: 0, y: 0 };
            var v4 = this.getScreenLocation();
            var p = this.o;
            var o = "horizontal";
            while (q && (q != igk.dom.body().o)) {
                if (q == t.o) {
                    if (t.o.offsetWidth < v2.x) {}
                    if (((v4.x == 0) && (v4.y != 0)) || (t.o.offsetHeight < v2.y)) {
                        o = "vertical";
                    }
                    break;
                }
                p = q;
                q = q.offsetParent;
                v2.x += p.offsetLeft;
                v2.y += p.offsetTop;
                v4.x -= p.offsetLeft;
                v4.y -= p.offsetTop;
            }
            return { orientation: o, x: v4.x, y: v4.y };
        }
    });
    __prop.getElementsByTagName = function(tag) { if (this.o.getElementsByTagName) return this.o.getElementsByTagName(tag); };
    // return the client width
    __prop.getWidth = function() { return this.o.clientWidth; };
    __prop.getglobalWidth = function() { return this.o.clientWidth + this.o.scrollWidth; };
    __prop.getglobalHeight = function() { return this.o.cliclientHeight + this.o.scrollHeight; };
    // return the client height
    __prop.getHeight = function() { return this.o.clientHeight; };
    __prop.getTop = function() { return this.getLocation().y; };
    __prop.getLeft = function() { return this.getLocation().x; };
    __prop.getSize = function() { return { w: this.getWidth(), h: this.getHeight(), toString: function() { return "w:" + this.w + " h:" + this.h; } }; };
    // return the location of the host in global display
    __prop.getLocation = function() { return igk.winui.GetScreenPosition(this.o); };
    // return the location of the host in client screen display
    __prop.getScreenLocation = function() { return igk.winui.GetRealScreenPosition(this.o); };
    __prop.getOffsetBounds = function() {
        var p = this;
        var real_size = { x: 0, y: 0, h: 0, w: 0 };
        for (var n = 0; n < p.o.childNodes.length; n++) {
            var c = $igk(p.o.childNodes[n]);
            var loc = c.getScreenLocation();
            var boc = c.getScreenBounds();
            real_size.x = Math.min(loc.x, real_size.x);
            real_size.y = Math.min(loc.y, real_size.y);
            real_size.w = Math.max(loc.x + boc.w, real_size.w);
            real_size.h = Math.max(loc.y + boc.h, real_size.h);
            // real_size.x = Math.min(loc.x, real_size.x);
        }
        loc = p.getScreenLocation();
        real_size.w -= loc.x;
        real_size.h -= loc.y;
        return real_size;
    };
    // get the bounding visibility in client screen display
    __prop.getBoundingClientRect = function() {
        // shortcut function
        var g = this.o.getBoundingClientRect ? this.o.getBoundingClientRect() : {
            x: 0,
            y: 0,
            width: 0,
            height: 0,
            left: 0,
            top: 0,
            right: 0,
            bottom: 0,
            toString: function() {
                "igk.boundingClientRect[]"
            }
        };
        // some browser don't implement x and y properties
        if (!g.x) g.x = g.left;
        if (!g.y) g.y = g.top;
        return g;
    };
    // get the screen bounding item
    __prop.getScreenBounds = function() {
        var l = igk.winui.GetRealScreenPosition(this.o);
        var s = this.getSize();
        return {
            x: l.x,
            y: l.y,
            w: s.w,
            h: s.h,
            contains: function(x, y) {
                return (l.x <= x) && ((l.x + s.w) >= x) &&
                    (l.y <= y) && ((l.x + s.h) >= y);
            },
            toString: function() { return "getScreenBounds[" + igk.system.stringProperties(this) + "]"; }
        };
    };
    // return true is item is visible in screen display
    __prop.getisVisible = function() {
        var j = this;
        var loc = j.getBoundingClientRect();
        // j.o.getBoundingClientRect ? j.o.getBoundingClientRect(): 
        // {x:0,y:0};
        // {x:j.getscrollLeft(), y:j.getscrollTop()};
        // getLocation();
        var size = igk.winui.screenSize();
        // get screen visibility
        var vsb = ((loc.x >= 0) && (loc.x <= size.width) && (loc.y >= 0) && (loc.y <= size.height));
        return vsb;
    };
    __prop.getpresentOnDocument = function(doc) {
        // > get if this is present on document
        var j = this.o;
        var _doc = doc || document;
        while (j && (j != _doc)) {
            j = j.parentNode;
        }
        return (j != null);
    };
    __prop.getscrollLeft = function() { if (this.o.pageXOffset) { return this.o.pageXOffset; } else if (this.o.scrollLeft) return this.o.scrollLeft; return 0; };
    __prop.getscrollTop = function() { if (this.o.pageYOffset) { return this.o.pageYOffset; } else if (this.o.scrollTop) return this.o.scrollTop; return 0; };
    __prop.getscrollLocation = function(targetParent) { return igk.winui.GetScrollPosition(this.o, targetParent); };
    __prop.getscrollMaxTop = function() {
        if (this.o.scrollTopMax) return this.o.scrollTopMax;
        else return this.o.offsetHeight;
    };
    __prop.getscrollMaxLeft = function() {
        if (this.o.scrollLeftMax) return this.o.scrollLeftMax;
        else return this.o.offsetWidth;
    };
    // -------------- WINDOW function
    __prop.createElement = function(tagname) {
        if (this.o.namespaceURI == null) {
            var c = document.createElementNS(null, tagname);
            return c;
        }
        return igk.createNode(tagname, this.o.namespaceURI || igk.namespaces.xml);
    };
    __prop.isChildOf = function(target) {
        var q = this.o.parentNode;
        while (q) {
            if (q == target)
                return !0;
            q = q.parentNode;
        }
        return !1;
    };
    // -------------- ADDITIONAL FUNCTION	
    __prop.collapse = function(property, callback) {
        if (m_anim && m_anim.collapsing) {
            // stop scrolling of this item
            m_anim.collapsing.stop();
        }
        // create animation context
        var anim1 = igk.animation.init(this, property.interval, property.duration,
            function() {},
            function() {},
            function() {
                if (m_anim && m_anim.__anim && m_anim.__anim.collapsing)
                    delete m_anim.__anim.collapsing; // =null;
                if (callback)
                    callback();
                // delete m_anim;						
            });
        m_anim = createPNS(this, "__anim", { "collapsing": anim1 });
        m_anim.type = "scrolling";
        anim1.start();
        return !0;
    };
    __prop.expand = function(property, callback) {
        if (m_anim && m_anim.expanding) {
            // stop scrolling of this item
            m_anim.expanding.stop();
        }
        var anim1 = igk.animation.init(this, property.interval, property.duration,
            function() {},
            function() {},
            function() {
                if (m_anim && m_anim.__anim && m_anim.__anim.expanding)
                    delete m_anim.__anim.expanding; // =null;
                if (callback)
                    callback();
                // delete m_anim;						
            });
        m_anim = createPNS(this, "__anim", { "expanding": anim1 });
        m_anim.type = "scrolling";
        anim1.start();
        return !0;
    };

    function __igk_event(q, p, n) {
        var self = this;

        function __ecall(evt) {
            if (evt.propertyName == n) {
                igk.winui.getEventObjectManager && igk.winui.getEventObjectManager().raise(self.target.o, n);
            }
        }
        document.documentElement.attachEvent('onpropertychange', __ecall);
        if (p)
            igk_appendProp(this, p);
        igk_appendProp(this, {
            fordoc: true,
            target: q,
            unregister: function() {
                document.documentElement.detachEvent('onpropertychange', __ecall);
            }
        });
    };
    // extend properties
    // _base['fill'] = Object.getOwnPropertyDescriptor(this,'fill');
    // igk.defineProperty(this, 'fill',  {get:function(){ return _base['fill'].get.apply(_q); }, set: function(v){
    // if (!_ri)
    // _base['fill'].set(v);
    // }});
    function igk_extendProperty(o, n, p) {
        if (!o)
            return !1;
        var h = Object.getOwnPropertyDescriptor(o, n) || Object.getOwnPropertyDescriptor(o, n);
        var e = {};
        if (('get' in p) && ('get' in h)) {
            e.get = function() {
                return p.get.apply(this, [h.get]);
            };
        }
        if (('set' in p) && ('set' in h)) {
            e.set = function(v) {
                return p.set.apply(this, [v, h.set]);
            };
        }
        igk.defineProperty(o, n, e);
    };
    // >namespace: igk
    createNS("igk", {
        DEBUG: false, // for debuging purpose		
        release: "17/01/15", // date of birth
        evaluating: false,
        elog: function(msg, tag) { // login function 
            tag = tag || _FM_;
            console.error("[" + (_FM_) + "]" + ":" + msg);
        },
        createObj: function(s, d) {
            // createobject from string or array
            if (isUndef(d))
                d = 0;
            var o = {};
            var t = s.split('|');
            for (var s = 0; s < t.length; s++) {
                o[t[s]] = d;
            }
            return o;
        },
        isUndef: isUndef,
        isNumber,
        initObj: igk_initobj,
        createNode: function(tag, ns) {
            if (!tag)
                return 0;
            if (ns && document.createElementNS)
                return __igk(document.createElementNS(ns, tag));
            tag = tag.toLowerCase();
            return __igk(document.createElement(tag));
        },
        createNSComponent: function(ns, classname) { // create component in namespace
            var lst = igk.winui.getClassList();
            var g = lst["ns://"] && lst["ns://"][ns] && lst["ns://"][ns][classname];
            if (g) {
                var s = null;
                if (g.data.create) {
                    s = g.data.create.apply(window, igk.system.array.slice(arguments, 2));
                } else {
                    s = igk.createNode("div");
                    s.addClass(classname);
                }
                igk.ajx.fn.initnode(s.o);
                return s;
            }
            return null;
        },
        createComponent: function(classname) {
            // create a control class component
            // usage sample : igk.createComponent("igk-ajx-uri-loader")
            // component must be first registrated with igk.winui.initClassControl
            var lst = igk.winui.getClassList();
            var g = lst[classname];
            if (g) {
                var s = null;
                if (g.data.create) {
                    s = g.data.create.apply(window, igk.system.array.slice(arguments, 1));
                } else {
                    s = igk.createNode("div");
                    s.addClass(classname);
                }
                igk.ajx.fn.initnode(s.o);
                return s;
            }
            return null;
        },
        createText: function(s) {
            return __igk(document.createTextNode(s));
        },
        createHtml: function(s) {
            var o = igk.createNode("div").setHtml("&nbsp;").o;
            return igk.createText(o.textContent);
        },
        clearTimeout: function(timeout) {
            window.clearTimeout(timeout);
        },
        evalScript: igk_eval,
        typeofs: function(n) {
            return typeof(n) == 'string';
        },
        typeoff: function(n) {
            return typeof(n) == 'function';
        },
        loadScript: function(filename) {
            if (!filename) return;
            var uri = null;
            var p = m_scriptTag; // get the current script tag
            if (p) {
                uri = igk.constants.http_scheme + document.domain + igk_getdir(window.location.pathname + "") + "/" + igk_getdir(p.getAttribute("src")) + "/" + filename;
            }
            if (!uri) {
                return;
            }
            // load the plugins js scripts
            igk.ajx.get(uri, null, function(xhr) {
                if (this.isReady()) {
                    var s = igk.createNode("script");
                    s.setAttribute("type", "text/javascript");
                    s.setAttribute("language", "javascript");
                    s.o.innerHTML = xhr.responseText;
                    document.head.appendChild(s);
                }
            });
        },
        getScriptLocation: igk_getScriptLocation,
        loadPlugins: function(plugins) {
            // every plugin must be a comma separated string of name
            // exemple : animation,regex,info
            // and every folder that contain a plugins must have a plugin.js script at root
            if (!plugins)
                return;
            // alert("load plugins ... ");
            var t = plugins.split(',');
            var uri = null;
            var p = m_scriptTag;
            if (p) {
                uri = igk.constants.http_scheme + document.domain + "/" + igk_getdir(window.location.pathname + "") + "/" + igk_getdir(p.getAttribute("src")) + "/plugins";
            }
            if (!uri) {
                return;
            }
            for (var i = 0; i < t.length; i++) {
                // load the plugins js scripts
                igk.ajx.get(uri + "/" + t[i] + "/plugin.js", null, function(xhr) {
                    if (this.isReady()) {
                        var s = igk.createNode("script");
                        s.setAttribute("type", "text/javascript");
                        s.setAttribute("language", "javascript");
                        s.setHtml(xhr.responseText);
                        document.head.appendChild(s.o);
                    }
                });
            }
        },
        preload: function(u) {
            // load the plugins js scripts
            igk.ajx.get(u, null, function(xhr) {
                if (this.isReady()) {
                    var s = igk.createNode("script");
                    s.setAttribute("type", "text/javascript");
                    s.setAttribute("language", "javascript");
                    s.setHtml(xhr.responseText);
                    var b = document.head || igk.dom.body().o;
                    if (b)
                        b.appendChild(s.o);
                }
            });
        },
        canInvoke: function() {
            return (window.external) && (('notify' in window.external) || (typeof(window.external.callFunc) != igk.constants.undef));
            // return ('notify' in window.external) || window.external && (typeof (window.external.callFunc) != igk.constants.undef);
        },
        invoke: function(method, params) { // used to invoke external script function	
            var n = 0;
            var fc = null;
            var _out = 0;
            var _json = { method: method };
            if (params) {
                if (typeof(params) == 'object') {
                    params = igk.JSON.convertToString(params);
                }
                _json.param = params;
            }
            if ((igk.navigator.IEVersion() <= 7.0) || ('notify' in window.external)) {
                try {
                    _out = window.external.notify(igk.JSON.convertToString(_json));
                } catch (ex) {
                    igk.winui.notify.showMsg("<div class=\"igk-notify-danger\">Execption : External JS Notify [" + method +
                        "] not invoked <br /> " + ex + "</div");
                }
                return _out;
            }
            fc = ('callFunc' in window.external) ? window.external.callFunc : null;
            if (n) {
                try {
                    if (n) {
                        _out = window.external.notify(igk.JSON.convertToString(_json));
                    } else {
                        _out = fc(_json); //"{method:"+method+", param:\""+ params+"\"}");
                    }
                } catch (ex) {
                    igk.winui.notify.showMsg("<div class=\"igk-notify-danger\">Execption : External JS Notify [" + method +
                        "] not invoked <br /> " + ex + "</div");
                }
            } else {
                igk.winui.notify.showMsBox(__libName, "<div class=\"igk-notify-danger\">No external function defined [" + method + " : " + n + "]</div>", "igk-info");
            }
            return _out;
        },
        constants: {
            // declaring usage constants
            http_scheme: "http://",
            https_scheme: "https://",
            namespace: "http://www.igkdev.com",
            "undef": IGK_UNDEF,
            "true": true,
            "false": false,
            regex: igk_regex_constant()
        },
        "selector_showlist": function(s) {
            var ss = "";
            s.each(function() { ss += " " + this.o.tagName + ":" + this.o + "\n"; return !0; });
            // alert("Show selector list " + ss);
        },
        eval_all_script: igk_eval_all_script,
        init_document: function(s) { //initialize document 		 
            if (igk.ctrl.init_controller) {
                igk.ctrl.init_controller();
            } else {
                $igk(igk.winui.events.global()).reg_event('igk_controller_ready', function() {
                    igk.ctrl.init_controller();
                });
            }
            igk.ready(function() {
                igk_preload_image(document);
                igk_preload_anim_image(document);
            });
            // apply preload document
            __applyPreloadDocument(document);
            __initDocSetting = s;
        },
        alert: function(m, t) {
            igk_show_notify_msg(t || 'alert', m);
        },
        console_debug: igk_console_debug,
        show_prop: igk_show_prop,
        show_notify_prop: function(e) {
            igk.winui.notify.showMsg(igk.html.getDefinition(e));
        },
        show_notify_prop_v: function(e) {
            igk.winui.notify.showMsg(igk.html.getDefinitionValue(e));
        },
        show_notify_msg: igk_show_notify_msg,
        show_notify_error: igk_show_notify_error,
        get_v: function(o, k, d) {
            if (typeof(o[k] != IGK_UNDEF))
                return o[k];
            if (typeof(d) == IGK_UNDEF)
                return null;
            return d;
        },
        show_prop_keys: igk_show_prop_keys,
        preload_image: igk_preload_image,
        show_event: igk_show_event,
        getParentScriptByTagName: igk_getParentScriptByTagName,
        getParentScriptForm: function() { return igk_getParentScriptByTagName("form"); },
        getElementsByTagName: function(e, tag) { return e.getElementsByTagName(tag); },
        getParentScript: function() { return igk_getParentScriptByTagName(null); },
        getLastScript: igk_getLastScript,
        getCurrentScript: igk_getCurrentScript,
        rmCurrentScript: function() {
            var s = igk.getCurrentScript();
            if (s) {
                $igk(s).remove();
            }
        },
        initpowered: igk_init_powered,
        getParentByTagName: igk_getParentByTagName,
        getParentById: igk_getParentById,
        appendProperties: igk_appendProp,
        defineProperty: igk_defineProperty,
        extendProperty: igk_extendProperty,
        defineEnum: igk_defineEnum,
        checkOnePropertyExists: igk_checkOnePropertyExists,
        checkAllPropertyExists: igk_checkAllPropertyExists,
        callfunction: igk_callfunction,
        appendChain: function(q, n, func) {
            switch (typeof(q[n])) {
                case 'function':
                    var fc = q[n];
                    q[n] = function() {
                        fc.apply(q, arguments);
                        func.apply(q, arguments);
                    };
                    return 1;
                case 'undefined':
                    q[n] = func;
                    return 1;
            }
            return 0;
        },
        prependChain: function(q, n, func) {
            switch (typeof(q[n])) {
                case 'function':
                    var fc = q[n];
                    q[n] = function() {
                        func.apply(q, arguments);
                        fc.apply(q, arguments);
                    };
                    return 1;
                case 'undefined':
                    q[n] = func;
                    return 1;
            }
            return 0;
        },
        pushFunction: function(f, fc) {
            //utility function used push 
            var _s = f;
            if (_s == null)
                return fc;
            _s = function() {
                f.apply(this, arguments);
                fc.apply(this, arguments);
            };
            return _s;
        },
        check: function(item, pattern) {
            var m = igk_select_exp(pattern.substring(1));
            if (m.check(item, 0)) {
                return !0;
            }
            return !1;
        },
        qselect: function(item, pattern) { // with query selector
            var v_sl = new igk.selector();
            if (item.querySelectorAll) {
                var p = item.querySelectorAll(pattern);
                for (var i = 0; i < p.length; i++) {
                    v_sl.push($igk(p[i]));
                }
            }
            return $igk(v_sl);
        },
        select: function(item, pattern) { 
            // + | selection in igk			
            var b = null;
            var v_sl = new igk.selector();
            if (!item || (pattern == null) || (pattern.length == 0) || /['`\[\]]/.exec(pattern)) {
                return $igk(v_sl);
            }
            // query selector detection
            var v_list = pattern.split(',');
            if (v_list.length > 1) {
                for (var sm = 0; sm < v_list.length; sm++) {
                    var v_cq = igk.select(item, v_list[sm]);
                    if (v_cq.getCount() > 0) {
                        v_sl.load(v_cq);
                    }
                }
                return v_sl;
            }
            if (/^\^\?/.test(pattern)) {
                // load block pattern data
                pattern = pattern.substring(2);
                return $igk(pattern);
            }
            if (pattern == "::") {
                // select parent
                v_sl.push($igk(item).o.parentNode);
                return $igk(v_sl);
            }
            if (pattern == "??") { // select body content
                // var k=igk.dom.body();
                v_sl.push(igk.dom.body().o);
                return $igk(v_sl);
            }
            if (pattern.indexOf('?') == 0) {
                // if element contain criteria
                // exemple ?.igk-body
                var m = igk_select_exp(pattern.substring(1));
                if (m.check(item, 0)) {
                    v_sl.push(item);
                }
                return $igk(v_sl);
            }
            if (pattern.indexOf('^') == 0) {
                //+ parent search 
                // sample : ^div
                var spattern = pattern.split(" ");
                pattern = spattern[0];
                if ((/^\^[\w\-_]+$/.exec(pattern))) {
                    // search parent by tagname
                    // exemple: ^div
                    b = $igk($igk(item).getParentByTagName(pattern.substring(1)));
                    if (b)
                        v_sl.push(b);
                } else if ((/^\^#[\w\-_]+$/.exec(pattern))) { // search parent by id
                    // exemple: ^#info
                    b = $igk(item).getParentById(pattern.substring(2));
                    if (b)
                        v_sl.push(b);
                } else if ((/^\^\./.exec(pattern))) { // search parent by class by class
                    pattern = pattern.substring(2);
                    var s = $igk(item).o.parentNode;
                    var rx = new RegExp("(^| )(" + pattern + ")(\\s|$)", "i");
                    while (s != null) {
                        if (rx.exec("" + s.className)) {
                            v_sl.push(s);
                        }
                        s = s.parentNode;
                    }
                }
                if ((spattern.length > 1) && (v_sl.getCount() == 1)) {
                    return $igk(v_sl).first().select(spattern.slice(1).join(" "));
                }
                return $igk(v_sl);
            }
            if (pattern.indexOf('+') == 0) {
                // search on next sibling
                var h = $igk(item).o.nextSibling;
                var spattern = pattern.substr(1).split(" ");
                pattern = spattern[0];
                while (h) {
                    if (igk.css.isMatch(h, pattern)) {
                        v_sl.push(h);
                    }
                    h = h.nextSibling;
                }
                return $igk(v_sl);
            }
            // used to select item on the current node
            // pattern: 
            // [*] for all item. exemple igk.select(node,'*');
            // [:attribute_name] that have the attribute name. exemple igk.select(node,":id")
            // [:^expression] the requested attribute start with the expression
            // [.callName] that match the class name
            // [tagname] that match the tag name . exemple igk.select(node,"div")
            // [>>] child only selection
            // [>:expression] child only expression
            // [>tagname] child only tag name
            var v_it = null;
            var s = null;
            var exp = null;
            var fid = false;
            if (typeof(pattern) == "string") {
                // special meaning
                switch (pattern) {
                    case ">>":
                        // child only
                        // sample:  select('>>')
                        for (var i = 0; i < item.childNodes.length; i++) {
                            s = item.childNodes[i];
                            v_sl.push(s);
                        }
                        return $igk(v_sl);
                        break;
                    default:
                        if (/^>:([\w_#\.]+[\w\-_0-9 ]*)$/.exec(pattern)) {
                            // child that match tag name
                            // sample:  select(':>xcv')
                            pattern = pattern.substring(2);
                            for (var i = 0; i < item.childNodes.length; i++) {
                                s = item.childNodes[i];
                                if (v_sl.isMatch(pattern, s)) {
                                    v_sl.push(s);
                                }
                            }
                            return $igk(v_sl);
                        }
                        break;
                }
                if (!item.getElementsByTagName) {
                    // return empty selection. becoause of item not supported getElementsByTagName
                    if (igk.DEBUG) console.debug("/!\ selection will failed because element not support getElementByTagName");
                    return $igk(v_sl);
                }
                v_it = item.getElementsByTagName("*");
                if (pattern == "*") {
                    // push all
                    for (var i = 0; i < v_it.length; i++) {
                        s = v_it[i];
                        v_sl.push(s);
                    }
                } else {
                    if (/^:([\w_]+[\w\-_0-9]*)$/.exec(pattern)) { // search by attribute
                        pattern = pattern.substring(1);
                        for (var i = 0; i < v_it.length; i++) {
                            s = v_it[i];
                            if (s.getAttribute(pattern)) {
                                v_sl.push(s);
                            }
                        }
                    } else if (/^:\^([\w_]+[\w\-_0-9]*)$/.exec(pattern)) { // search by starting with attribute 
                        pattern = pattern.substring(2);
                        for (var i = 0; i < v_it.length; i++) {
                            s = v_it[i];
                            var msg = $igk(s).getAllAttribs();
                            if (RegExp("" + pattern + "", "i").test(msg)) {
                                v_sl.push(s);
                            }
                        }
                    } else if ((/^\.[\w\-_]+$/.exec(pattern))) { // search in class name
                        pattern = pattern.substring(1);
                        for (var i = 0; i < v_it.length; i++) {
                            s = v_it[i];
                            if (igk_item_match_class(pattern, s)) {
                                v_sl.push(s);
                            }
                        }
                    } else if ((/^\>[\w\-_]+$/.exec(pattern))) { // search by child node tagname
                        // if(igk.DEBUG) 
                        pattern = pattern.substring(1);
                        exp = new RegExp("(" + pattern + ")", "i");
                        for (var i = 0; i < v_it.length; i++) {
                            s = v_it[i];
                            if ((s.parentNode == item) && exp.exec("" + s.tagName)) {
                                v_sl.push(s);
                            }
                        }
                    } else {
                        if ((igk.constants.regex.idSearch.exec(pattern))) { // search in id
                            fid = !0;
                            pattern = pattern.substring(1);
                            for (var i = 0; i < v_it.length; i++) {
                                s = v_it[i];
                                if (new RegExp("^(" + pattern + ")$", "i").exec("" + s.id)) {
                                    v_sl.push(s);
                                }
                            }
                        } else {
                            var m = igk_select_exp(pattern);
                            // if(pattern==".igk-body#query-s-r"){
                            // m.debug=1;
                            // }
                            if (m != null) {
                                m.select(v_sl, item);
                            } else {
                                v_it = item.getElementsByTagName(pattern);
                                for (var i = 0; i < v_it.length; i++) {
                                    s = v_it[i];
                                    v_sl.push(s);
                                }
                            }
                        }
                    }
                }
            }
            // if(fid){
            // if(v_sl.getCount()==0)
            // return null;
            // }	
            return $igk(v_sl);
        },
        load: function(func) { // load function
            if (func == null)
                return;
            if (document.readyState == "loading") {
                igk_winui_reg_event(window, "load", func);
            } else {
                func.apply(window);
            }
        },
        is_readyRegister: function(func) {
            for (var i = 0; i < readyFunc.length; i++) {
                if (func == readyFunc[i])
                    return !0;
            }
            return !1;
        },
        readyCountFunc: function() {
            return readyFunc.length;
        },
        readyGlobal: function(func) {
            if (document.readyState == "complete") {
                func.apply(document);
            } else { // store to call on ready complete
                m_readyGlobalFunc[m_readyGlobalFunc.length] = func;
            }
        },
        isdevelop: function() {
            if (typeof(__isdev) == 'undefined') {
                __isdev = igk.getScriptLocation().location.split('/').pop() == __devscript;
            }
            return __isdev;
        },
        readyinvoke: function(n) { // call this function in script that have source content 
            //executing script name
            var s = igk_getCurrentScript();
            var t = igk.system.array.slice(arguments, 1);
            igk.ready(function() {
                // script :  readyinvoke
                var ns = igk.system.getNS(n);
                if (typeof(ns) == 'function') {
                    var bck = m_scriptNode;
                    m_scriptNode = s;
                    ns.apply(s, t);
                    m_scriptNode = bck;
                } else if (igk.isdevelop()) {
                    console.debug(n + " : not found");
                }
            });
        },
        isDefine: igk_isdefine,
        onContentLoad: function(func, priority) {
            // register document load content
            // alert('on content load');
            // if (typeof(document.DOMContentLoad) == 'undefined'){
            // return;
            // }
            priority = igk.isDefine(priority, 10);
            var k = "igk.event.contentLoad";
            var g = igk.system.getNS(k) || igk.system.createNS(k, { isRegister: 0 });
            func = { fn: func, priority: priority };
            if (g.callback)
                g.callback.push(func);
            else
                g.callback = [func];
            if (!g.isRegister) {
                document.addEventListener('DOMContentLoaded', function(evt) {
                    // sort list
                    g.callback.sort(function(e, i) {
                        var c = e.priority - i.priority;
                        if (c != 0)
                            c /= Math.abs(c);
                        return c;
                    });
                    for (var i = 0; i < g.callback.length; i++) {
                        var fc = g.callback[i];
                        fc.fn.apply(document, [evt]);
                    }
                    delete(window[k]);
                });
                g.isRegister = 1;
            }
        },
        ready: function(func, sys, complete) {
            // ready function
            // sys call by system
            function _async_call(e, document, i) {
                //@i : name of the measurement function for 'warning duration violation' raise in chrome
                // console.log("function : "+i);
                // if (i=="f5")
                setTimeout(function() {
                    e.apply(document, [i]);
                }, 10);
            }
            if ((func == null) || (func == 0)) {
                if ((document.readyState !== "complete") || m_ready_calling)
                    return;
                m_ready_calling = !0;
                // call ready global function
                var e = null;
                if (m_readyGlobalFunc.length > 0) {
                    for (var i = 0; i < m_readyGlobalFunc.length; i++) {
                        e = m_readyGlobalFunc[i];
                        try {
                            _async_call(e, document, i);
                            // e.apply(document);
                        } catch (ex) {
                            igk.winui.notify.showError("<div class=\"igk-title-5\">igk.js ReadyGlobal function call failed </div> <br />" + ex + "<br /><quote>" + ex.stack + "</quote><pre style=\"max-height:200px; overflow-y:auto;\">" + e + "</pre>");
                        }
                    }
                    m_readyGlobalFunc = [];
                }
                // call all ready function		
                if (readyFunc.length > 0) {
                    for (var i = 0; i < readyFunc.length; i++) {
                        e = readyFunc[i];
                        try {
                            _async_call(e, document, "f" + i);
                            // e.apply(document);
                        } catch (ex) {
                            if (igk.DEBUG) {
                                var ox = "";
                                if (ex.stack)
                                    ox = (ex.stack + "").replace("\n", "<br />");
                                igk.winui.notify.showError("<div class=\"igk-title-5\">igk.js Ready function call failed </div> <br />" + ex + "<br /><quote>" +
                                    ox +
                                    "</quote><pre class='error-code' style=\"max-height:200px; overflow-y:auto;\"><code>" + e + "</code></pre>");
                            } else {
                                console.error(ex);
                            }
                        }
                    }
                }
                // clear ready func				
                readyFunc = [];
                m_ready_calling = false;
                if (complete){
                    component.call();
                }
            } else {
                if (m_ready_calling || (document.readyState == "complete")) {
                    func.apply(document);
                } else {
                    // store to call on ready complete
                    readyFunc[readyFunc.length] = func;
                }
            }
        },
        unready: function(func) {
            var s = [];
            for (var i = 0; i < readyFunc.length; i++) {
                if (readyFunc[i] == func) {
                    continue;
                }
                s.push(readyFunc[i]);
            }
            readyFunc = s;
        },
        getElementsByAttribute: function(properties) {
            if (!properties)
                return null;
            var d = document.getElementsByTagName("*");
            var out = [];
            var b = !0;
            for (var i = 0; i < d.length; i++) {
                for (var k in properties) {
                    if (d[i].getAttribute(k) == properties[k]) {
                        out.push(d[i]);
                    }
                }
            }
            return out;
        },
        selector: function() {
            // ------------------------------------------------------
            // selector element
            // ------------------------------------------------------
            var m_items = [];
            igk_appendProp(this, {
                getCount: function() { return m_items.length; },
                push: function(item) {
                    m_items.push(item);
                    this.length = this.getCount();
                },
                isSr: function() { return !0; }, // selector property
                toString: function() { return "igk.selector[" + this.getCount() + "]"; },
                each: function(func, args) {
                    args = args ? args : [];
                    for (var i = 0; i < this.getCount(); i++) {
                        if ((func) && func.apply && !func.apply($igk(m_items[i]), args)) {
                            break;
                        }
                    }
                    return this;
                },
                // call func in interval chain
                waitInterval: function(duration, func) {
                    function __func_waiter(i, func) {
                        var self = this;
                        igk_appendProp(this, {
                            next: null,
                            wait: function() {
                                func.apply(i, arguments);
                                // var h=new __func_waiter(self.next,func);
                                // h.timeout=setTimeout(h.wait,duration);
                                if (self.next)
                                    self.next.start();
                            },
                            start: function() {
                                this.timeout = setTimeout(this.wait, duration);
                            }
                        });
                    }
                    var iduration = duration;
                    var h = null;
                    var t = null;
                    for (var i = 0; i < this.getCount(); i++) {
                        if (i == 0) {
                            h = new __func_waiter($igk(m_items[i]), func);
                            t = h;
                        } else {
                            h.next = new __func_waiter($igk(m_items[i]), func);
                            h = h.next;
                        }
                    }
                    if (t != null) {
                        t.start();
                    }
                    return this;
                },
                each_all: function(func, args) {
                    // >@ call function in all element
                    if (func) {
                        // correct object expected in ie 8
                        if (typeof(args) == IGK_UNDEF)
                            args = [];
                        for (var i = 0; i < this.getCount(); i++) {
                            func.apply($igk(m_items[i]), args);
                        }
                    }
                    return this;
                },
                getItemAt: function(index) {
                    if ((index >= 0) && (index < this.getCount()))
                        return $igk(m_items[index]);
                    return null;
                },
                getLastItem: function() {
                    var c = this.getCount();
                    return c > 0 ? this.getItemAt(c - 1) : null;
                },
                getFirstItem: function() {
                    var c = this.getCount();
                    return c > 0 ? this.first() : null;
                },
                getNodeAt: function(index) {
                    if ((index >= 0) && (index < this.getCount()))
                        return m_items[index];
                    return null;
                },
                isMatch: function(p, n) {
                    // check if this property match
                    // @p:pattern
                    // @n:dom node			
                    if (igk.constants.regex.tagName.exec(p)) {
                        return n.tagName && (n.tagName.toLowerCase() == p.toLowerCase());
                    } else if (igk.constants.regex.className.exec(p)) {
                        return igk_item_match_class(p, n);
                    } else if (igk.constants.regex.idSearch.exec(p)) {
                        return (new RegExp("^(" + p + ")$", "ig")).exec("" + n.id);
                    }
                    return !1;
                },
                select: function(pattern) {
                    // selector selection pattern		
                    var v_s = new igk.selector();
                    if (this.getCount() == 0) {
                        return v_s; // empty selector
                    }
                    for (var i = 0; i < this.getCount(); i++) {
                        var g = igk.select(m_items[i], pattern);
                        if (g && (g.getCount() > 0)) {
                            for (var j = 0; j < g.getCount(); j++) {
                                v_s.push(g.getNodeAt(j));
                            }
                        }
                    }
                    return $igk(v_s);
                },
                load: function(g) { // load selector
                    if (g && g.isSr()) {
                        for (var j = 0; j < g.getCount(); j++) {
                            this.push(g.getNodeAt(j));
                        }
                    }
                },
                clear: function() {
                    m_items = new Array();
                },
                first(){
                    return null;
                }
            });
            igk.initprop(this);
        },
        initprop: function(element) {
            if (typeof(element.igk) == igk.constants.undef) {
                // build element igk property
                (function() {
                    this.igk = new __igk_nodeProperty(element);
                    this.igk.$ = window.igk;
                }).apply(element);
            }
            if (typeof(element.$) == igk.constants.undef) {
                // build point to igkFramework
                (function() {
                    // this.$=function(){
                    // window.igk;
                    // return null;
                    // }
                    this.global = window.igk;
                }).apply(element);
            } // endif
            // element.igk.$=window.igk;
        },
        getNumber: igk_getNumber, // expose get number function
        getUnit: igk_getUnit, // expose get unit
        getPixel: igk_getPixel // igk.getPixel
            // toString: function(){
            // return "namespace:igk";
            // }// end tostring
    });
    // igk exception
    igk_appendProp(igk, {
        exception: function(msg) {
            this.name = "IGKException";
            this.level = "";
            if (typeof(msg) == "string") {
                this.message = "Error:" + msg;
            } else if (typeof(msg) == "object") {
                igk_appendProp(this, msg);
            }
            this.toString = function() {
                return this.name + " : " + this.message;
            };
        }
    });
    igk_defineProperty(igk, "platform", {
        get() {
            return __platform;
        }
    });

    // ---------------------------------------------------------
    // 
    // 
    // TODO: initialize environment style
    igk.ready(function() {
        var n, b = igk.dom.body();
        if (typeof(igk.navigator) == 'undefined') {
            return;
        }
        n = igk.navigator;

        if (n.isIE() && n.IEVersion() <= 11) {
            b.addClass("ie-11-service"); // no support of css 3 setting
        }
        //
        if (n.isSafari()) {
            b.addClass("safari"); //.igk.dom.body();
        }
    });
    // ---------------------------------------------------------
    // publish lib entity
    // ---------------------------------------------------------
    (function() {
        var m_publisher = {};
        var m_staticReg = {};
        var m_publisherobj = new function() {
            igk.appendProperties(this, {
                toString: function() { return "publisher obj"; }
            });
        };
        var m_cinf = {};
        // register function to publish. also used to register static function . call
        createNS("igk.publisher", {
            createEventData: function(t, ctx) {
                return {
                    target: t,
                    context: ctx
                };
            },
            register: function(n, func) {
                if (!n || !func)
                    return !1;
                var e = null;
                if (m_publisher[n]) {
                    e = m_publisher[n];
                } else {
                    e = { k: n, s: new igk.system.collections.list(), toString: function() { return "publisher-entity"; } };
                    m_publisher[n] = e;
                }
                e.s.add(func);
            },
            unregister: function(n, func) {
                var e = m_publisher[n];
                if (typeof(e) == IGK_UNDEF)
                    return;
                e.s.remove(func);
                if (e.s.getCount() == 0) {
                    delete(m_publisher[n]);
                }
            },
            publish: function(n, prop) {
                var e = m_publisher[n];
                if (typeof(e) == IGK_UNDEF)
                    return;
                // m_names.push(n);
                // array copy
                var tab = [];
                for (var i = 0; i < e.s.getCount(); i++) {
                    tab.push(e.s.getItemAt(0));
                }
                for (var i = 0; i < tab.length; i++) {
                    m_cinf = {
                        name: n,
                        caller: tab[i],
                        funcs: e.s
                    };
                    m_cinf.caller.apply(m_cinf, [prop]);
                }
                // this.name = m_names.pop();
            },
            getName: function(obj, ns, name) {
                if (obj instanceof igk.object) {
                    if (obj.type == "class") {
                        return name;
                    }
                } else {
                    for (var i in ns) {
                        if (obj.constructor == ns[i])
                            return i;
                    }
                }
            },
            inheritFrom: function(src, parent) {
                if ((typeof(src) == 'function') && (typeof(parent) == 'function')) {
                    var p = new parent();
                    src.prototype = p;
                    src.prototype.$parent = p;
                    src.prototype.$super = function(m) {
                        var r = arguments.length > 1 ? igk.system.array.slice(arguments, 1) : null;
                        this.$parent[m].apply(this, r);
                    };
                    return !0;
                }
                return !1;
            },
            registerStatic: function(ns, name, func) {
                var fname = ns.namespace + "." + name;
                if (m_staticReg[fname]) {
                    return;
                }
                m_staticReg[fname] = 1;
                // if(obj instanceof igk.object){
                // if(obj.type=="class"){
                // func.call(obj);
                // }
                // }
                // else{			
                func.call(ns);
                // m_staticReg[fname]=1;
                // }
            },
            createPublisherEvent: function(prop) {
                var c = new igk.publisher.event();
                igk.appendProperties(c, prop);
                return c;
            },
            event: function() {
                igk.appendProperties(this, {
                    toString: function() { return "publisher event"; }
                });
            },
            getCount: function(n) {
                // number of function list. -1 : no name
                // -2 : no function
                n = n || this.name;
                if (!n) return -1;
                var e = m_publisher[n];
                if (typeof(e) == IGK_UNDEF)
                    return -2;
                return e.s.getCount();
            }
        });
    })();
    var _js_version = -1;
    createNS("igk.os", {
        destroysession: function(uri, callback) {
            igk.ajx.send({
                uri: uri,
                method: 'DELETE',
                contentType: 'application/json',
                func: function(xhr) {
                    if (this.isReady()) {
                        if (callback) {
                            callback.apply(this, [xhr]);
                        }
                    }
                },
                param: '{destroysession:1}'
            });
        },
        javascriptVersion: function() {
            if (_js_version != -1)
                return _js_version;
            var _body = igk.dom.body();
            var t = ["1.1", "1.2", "1.3", "1.4",
                "1.5", "1.6", "1.7", "1.8", "1.8.1", "1.8.5", "1.9"
            ];
            for (var i = 0; i < t.length; i++) {
                var s = igk.createNode("script");
                if (i == 0) {
                    s.o["type"] = "text/javascript";
                    s.setHtml("var _jver='1.1';");
                } else {
                    // s.o["type"] = "text/javascript1."+i;
                    s.o.setAttribute("language", "javascript" + t[i]);
                    s.setHtml("_jver='" + t[i] + "';");
                }
                _body.add(s);
                s.remove();
            }
            _js_version = _jver;
            return _js_version;
        },
        checkupdate: function(uri) {
            var q = $igk(igk.getParentScript());
            (function() {
                igk.io.file.load(uri, {
                    error() {
                        q.remove();
                    },
                    complete() {
                        var w = $igk("#dialog.error").first();
                        if (w) {
                            var m = w.select(".msg").first().clone();
                            igk.show_notify_error(
                                w.select(".title").first().getHtml(),
                                m.getHtmll());
                        }
                        q.remove();
                    }
                });
            })();
        },
        update: function(u) {
            var w = $igk("#dialog.wait").first();
            if (w) {
                var m = w.select(".msg").first().clone();
                igk.winui.notify.showMsBox(
                    w.select(".title").first().getHtml(),
                    m,
                    'default',
                    1
                );
                igk.ajx.fn.initnode(m.o);
            }
            igk.io.file.load(u, function(d) {
                igk.winui.notify.close(function() {
                    var w = $igk("#dialog.os-complete").first();
                    if (w) {
                        // view notifyied dialog
                        igk.winui.notify.showMsBox(
                            w.select(".title").first().getHtml(),
                            w.select(".msg").first().getHtml());
                    } else {
                        // genearl dialog notification
                        igk.winui.notify.showMsBox("OS", "Update complete");
                    }
                    var k = igk.utils.getBodyContent(d.data);
                    var m = igk.createNode('div').setHtml(k);
                    var uk = m.select('ruri').first().o.innerHTML;
                    // var not=igk.winui.notify.getView();
                    if (uk) {
                        igk.ajx.post(uk + "/?c=c_sc&f=forceview", null, igk.ajx.fn.replace_body);
                    }
                });
            });
        },
        // install os zip lib
        install: function(f, msg) {
            var q = igk.createNode("div");
            ns_igk.ajx.postform(f, f.getAttribute('action'), function(xhr) {
                if (this.isReady()) {
                    f.reset();
                    q.remove();
                    igk.ajx.replaceBody(xhr.responseText, true);
                    igk.winui.notify.showMsBox("OS", msg.complete);
                }
            });
            $igk(f).o.appendChild(q.o);
            q.setHtml(msg.wait);
        }
    });
    createNS("igk.features", {});
    (function() {
        var _passive = false;
        try {
            var opts = {};
            igk_defineProperty(opts, 'passive', {
                get: function() {
                    _passive = true;
                }
            });
            if (window.addEventListener) {
                window.addEventListener('test', null, opts);
            }
        } catch (e) {}
        igk_defineProperty(igk.features, 'supportPassive', {
            get: function() {
                return _passive;
            }
        });
        igk_defineProperty(igk.features, 'supportBackgroundWorker', {
            get: function() {
                return typeof(window.Worker) !== 'undefined';
            }
        });
    })();
    createNS("igk.fn", {
        isset: function(i) {
            if ((i == null) || (typeof(i) == IGK_UNDEF))
                return !1;
            return !0;
        }
    }, { desc: "igk utility fonctions" });

    // represent utility fonction
    var prop_toextend = 0;
    createNS("igk.fn", {
        getItemFunc: function(it, n, fallback) {
            if (n in it)
                return it[n];
            var c = "";
            var r = n[0].toUpperCase() + n.substring(1);
            for (var i = 0; i < m_provider.length; i++) {
                c = m_provider[i] + r; // n.s;			
                if (c in it)
                    return it[c];
            }
            return fallback;
        },
        getWindowFunc: function(n, fallback) {
            return igk.fn.getItemFunc(window, n, fallback);
        },
        isItemStyleSupport: function(item, name) {
            if (!item || !item.style)
                return !1;
            var t = [name.toLowerCase()];
            var h = m_provider; // ['webkit','ms','o'];
            var k = name.charAt(0).toUpperCase() + name.substring(1).toLowerCase();
            for (var i = 0; i < h.length; i++) {
                t.push(h[i] + k);
            }
            return igk.fn.isItemSupport(item.style, t);
        },
        isItemSupport: function(e, tab) {
            if (!e || !tab)
                return !1;
            for (var i = 0; i < tab.length; i++) {
                if (typeof(e[tab[i]]) != IGK_UNDEF)
                    return !0;
            }
            return !1;
        },
        isItemSupportAll: function(e, tab) {
            if (!e || !tab)
                return !1;
            for (var i = 0; i < tab.length; i++) {
                if (typeof(e[tab[i]]) == IGK_UNDEF)
                    return !1;
            }
            return !0;
        },
        getItemProperty: function(i, n) { // properties to extends
            var g = prop_toextend || (function() {
                return prop_toextend = {
                    isFullScreen: ["webkitIsFullScreen", "mozFullScreen"],
                    fullscreenElement: ["webkitFullScreenElement", "mozFullScreenElement"],
                    fullscreenEnabled: ["webkitFullScreenEnabled", "mozFullScreenEnabled"]
                };
            })();
            if (n in i)
                return n;
            var tab = g[n];
            // for(var s in g){
            // if (s in i)
            // return s;
            for (var m = 0; m < tab.length; m++) {
                if (tab[m] in i) {
                    return tab[m];
                }
            }
            // }
            return 0;
        }
    });
    // createNS("igk.document",{
    // },
    // {
    // desc: "document utility function"
    // });
    // function __init_doc_prope(o){
    // var _wdoc = window.document;
    // igk_defineProperty(o,"isFullScreen",{
    // get:function(){ return _wdoc[igk.fn.getItemProperty(_wdoc, "isFullScreen")]; }
    // });
    // etant la propriété navigateur de document 
    // utilisation $igk(document).isFullScreen
    function def_property(o, n) {
        igk_defineProperty($igk(o), n, {
            get: function() { return o[igk.fn.getItemProperty(o, n)]; }
        });
    }
    def_property(window.document, "isFullScreen");
    def_property(window.document, "fullscreenElement");
    def_property(window.document, "fullscreenEnabled");
    // __init_doc_prope(igk.document);
    // for external management function
    createNS("igk.ext", {
        call: function(name, p) {
            return igk.invoke(name, p);
        },
        buildJSONData: function(t) {
            // build json data from target
            if ((t == null) || (typeof(t) == IGK_UNDEF))
                return "";
            var ob = {};
            $igk(t).select(".dial-item").each_all(function() {
                var id = this.getAttribute("id");
                switch (this.o.tagName.toLowerCase()) {
                    case "select":
                        ob[id] = this.o.value;
                        break;
                    default:
                        var o = this.getAttribute("type") || "text";
                        switch (o) {
                            case "checkbox":
                                if (this.o.checked)
                                    ob[id] = this.o.value;
                                break;
                            default:
                                ob[id] = this.o.value;
                                break;
                        }
                        break;
                }
            });
            var s = JSON.stringify(ob);
            return s;
        }
    });
    // igk.color namespace
    var _colors = { transparent: "Transparent", black: "#000", navy: "#00007F", darkblue: "#00008C", mediumblue: "#00C", blue: "#00F", darkgreen: "#006300", green: "#007F00", teal: "#007F7F", darkcyan: "#008C8C", deepskyblue: "#00BFFF", darkturquoise: "#00CED1", mediumspringgreen: "#00F999", lime: "#0F0", springgreen: "#00FF7F", aqua: "#0FF", cyan: "#0FF", midnightblue: "#191970", dodgerblue: "#1E8EFF", lightseagreen: "#21B2AA", forestgreen: "#218C21", seagreen: "#2D8C56", darkslategrey: "#2D4F4F", darkslategray: "#2D4F4F", limegreen: "#3C3", mediumseagreen: "#3DB270", turquoise: "#3FE0D1", royalblue: "#3F68E0", steelblue: "#4482B5", darkslateblue: "#473D8C", mediumturquoise: "#47D1CC", indigo: "#490082", darkolivegreen: "#546B2D", cadetblue: "#5E9EA0", cornflowerblue: "#6393ED", mediumaquamarine: "#6CA", dimgray: "#686868", dimgrey: "#686868", slateblue: "#6B59CC", olivedrab: "#6B8E23", slategrey: "#707F8E", slategray: "#707F8E", lightslategray: "#778799", lightslategrey: "#778799", mediumslateblue: "#7A68ED", lawngreen: "#7CFC00", chartreuse: "#7FFF00", aquamarine: "#7FFFD3", maroon: "#7F0000", purple: "#7F007F", olive: "#7F7F00", grey: "#7F7F7F", gray: "#7F7F7F", skyblue: "#87CEEA", lightskyblue: "#87CEF9", blueviolet: "#892BE2", darkred: "#8C0000", darkmagenta: "#8C008C", saddlebrown: "#8C4411", darkseagreen: "#8EBC8E", lightgreen: "#8EED8E", mediumpurple: "#9370D8", darkviolet: "#9300D3", palegreen: "#99F999", darkorchid: "#93C", yellowgreen: "#9C3", sienna: "#A0512D", brown: "#A52828", darkgrey: "#A8A8A8", darkgray: "#A8A8A8", lightblue: "#ADD8E5", greenyellow: "#ADFF2D", paleturquoise: "#AFEDED", lightsteelblue: "#AFC4DD", powderblue: "#AFE0E5", firebrick: "#B22121", darkgoldenrod: "#B7870A", mediumorchid: "#BA54D3", rosybrown: "#BC8E8E", darkkhaki: "#BCB76B", silver: "#BFBFBF", mediumvioletred: "#C61484", indianred: "#CC5B5B", peru: "#CC843F", chocolate: "#D1681E", tan: "#D1B58C", lightgrey: "#D3D3D3", lightgray: "#D3D3D3", palevioletred: "#D87093", thistle: "#D8BFD8", orchid: "#D870D6", goldenrod: "#D8A521", crimson: "#DB143D", gainsboro: "#DBDBDB", plum: "#DDA0DD", burlywood: "#DDB787", lightcyan: "#E0FFFF", lavender: "#E5E5F9", darksalmon: "#E8967A", violet: "#ED82ED", palegoldenrod: "#EDE8AA", lightcoral: "#EF7F7F", khaki: "#EFE58C", aliceblue: "#EFF7FF", honeydew: "#EFFFEF", azure: "#EFFFFF", sandybrown: "#F4A360", wheat: "#F4DDB2", beige: "#F4F4DB", whitesmoke: "#F4F4F4", mintcream: "#F4FFF9", ghostwhite: "#F7F7FF", salmon: "#F97F72", antiquewhite: "#F9EAD6", linen: "#F9EFE5", lightgoldenrodyellow: "#F9F9D1", oldlace: "#FCF4E5", red: "#F00", magenta: "#F0F", fuchsia: "#F0F", deeppink: "#FF1493", orangered: "#F40", tomato: "#FF6347", hotpink: "#FF68B5", coral: "#FF7F4F", darkorange: "#FF8C00", lightsalmon: "#FFA07A", orange: "#FFA500", lightpink: "#FFB5C1", pink: "#FFBFCC", gold: "#FFD600", peachpuff: "#FFD8BA", navajowhite: "#FFDDAD", moccasin: "#FFE2B5", bisque: "#FFE2C4", mistyrose: "#FFE2E0", blanchedalmond: "#FFEACC", papayawhip: "#FFEFD6", lavenderblush: "#FFEFF4", seashell: "#FFF4ED", cornsilk: "#FFF7DB", lemonchiffon: "#FFF9CC", floralwhite: "#FFF9EF", snow: "#FFF9F9", yellow: "#FF0", lightyellow: "#FFFFE0", ivory: "#FFFFEF", white: "#FFF" };
    createNS("igk.system.colors", {}); // transparent: "Transparent", black: "#000", navy: "#00007F", darkblue: "#00008C", mediumblue: "#00C", blue: "#00F", darkgreen: "#006300", green: "#007F00", teal: "#007F7F", darkcyan: "#008C8C", deepskyblue: "#00BFFF", darkturquoise: "#00CED1", mediumspringgreen: "#00F999", lime: "#0F0", springgreen: "#00FF7F", aqua: "#0FF", cyan: "#0FF", midnightblue: "#191970", dodgerblue: "#1E8EFF", lightseagreen: "#21B2AA", forestgreen: "#218C21", seagreen: "#2D8C56", darkslategrey: "#2D4F4F", darkslategray: "#2D4F4F", limegreen: "#3C3", mediumseagreen: "#3DB270", turquoise: "#3FE0D1", royalblue: "#3F68E0", steelblue: "#4482B5", darkslateblue: "#473D8C", mediumturquoise: "#47D1CC", indigo: "#490082", darkolivegreen: "#546B2D", cadetblue: "#5E9EA0", cornflowerblue: "#6393ED", mediumaquamarine: "#6CA", dimgray: "#686868", dimgrey: "#686868", slateblue: "#6B59CC", olivedrab: "#6B8E23", slategrey: "#707F8E", slategray: "#707F8E", lightslategray: "#778799", lightslategrey: "#778799", mediumslateblue: "#7A68ED", lawngreen: "#7CFC00", chartreuse: "#7FFF00", aquamarine: "#7FFFD3", maroon: "#7F0000", purple: "#7F007F", olive: "#7F7F00", grey: "#7F7F7F", gray: "#7F7F7F", skyblue: "#87CEEA", lightskyblue: "#87CEF9", blueviolet: "#892BE2", darkred: "#8C0000", darkmagenta: "#8C008C", saddlebrown: "#8C4411", darkseagreen: "#8EBC8E", lightgreen: "#8EED8E", mediumpurple: "#9370D8", darkviolet: "#9300D3", palegreen: "#99F999", darkorchid: "#93C", yellowgreen: "#9C3", sienna: "#A0512D", brown: "#A52828", darkgrey: "#A8A8A8", darkgray: "#A8A8A8", lightblue: "#ADD8E5", greenyellow: "#ADFF2D", paleturquoise: "#AFEDED", lightsteelblue: "#AFC4DD", powderblue: "#AFE0E5", firebrick: "#B22121", darkgoldenrod: "#B7870A", mediumorchid: "#BA54D3", rosybrown: "#BC8E8E", darkkhaki: "#BCB76B", silver: "#BFBFBF", mediumvioletred: "#C61484", indianred: "#CC5B5B", peru: "#CC843F", chocolate: "#D1681E", tan: "#D1B58C", lightgrey: "#D3D3D3", lightgray: "#D3D3D3", palevioletred: "#D87093", thistle: "#D8BFD8", orchid: "#D870D6", goldenrod: "#D8A521", crimson: "#DB143D", gainsboro: "#DBDBDB", plum: "#DDA0DD", burlywood: "#DDB787", lightcyan: "#E0FFFF", lavender: "#E5E5F9", darksalmon: "#E8967A", violet: "#ED82ED", palegoldenrod: "#EDE8AA", lightcoral: "#EF7F7F", khaki: "#EFE58C", aliceblue: "#EFF7FF", honeydew: "#EFFFEF", azure: "#EFFFFF", sandybrown: "#F4A360", wheat: "#F4DDB2", beige: "#F4F4DB", whitesmoke: "#F4F4F4", mintcream: "#F4FFF9", ghostwhite: "#F7F7FF", salmon: "#F97F72", antiquewhite: "#F9EAD6", linen: "#F9EFE5", lightgoldenrodyellow: "#F9F9D1", oldlace: "#FCF4E5", red: "#F00", magenta: "#F0F", fuchsia: "#F0F", deeppink: "#FF1493", orangered: "#F40", tomato: "#FF6347", hotpink: "#FF68B5", coral: "#FF7F4F", darkorange: "#FF8C00", lightsalmon: "#FFA07A", orange: "#FFA500", lightpink: "#FFB5C1", pink: "#FFBFCC", gold: "#FFD600", peachpuff: "#FFD8BA", navajowhite: "#FFDDAD", moccasin: "#FFE2B5", bisque: "#FFE2C4", mistyrose: "#FFE2E0", blanchedalmond: "#FFEACC", papayawhip: "#FFEFD6", lavenderblush: "#FFEFF4", seashell: "#FFF4ED", cornsilk: "#FFF7DB", lemonchiffon: "#FFF9CC", floralwhite: "#FFF9EF", snow: "#FFF9F9", yellow: "#FF0", lightyellow: "#FFFFE0", ivory: "#FFFFEF", white: "#FFF" });
    for (var c in _colors) {
        igk_defineProperty(igk.system.colors, c, {
            get: (function(c) {
                return function() {
                    return _colors[c];
                }
            })(c),
            configurable: false,
            enumerable: true
        });
    }
    createNS("igk.Number", {
        parseByte: function(i) {
            if (i > 255)
                return 255;
            if (i < 0)
                return 0;
            return i;
        }
    });
    createNS("igk.system.color", {
        HSVtoColor: function(h, s, v) {
            // angle 
            var r, g, b;
            var c = v * s;
            var x = c * (1 - Math.abs(((h / 60.0) % 2) - 1));
            var m = v - c;
            if (h < 60) {
                r = c;
                g = x;
                b = 0;
            } else if (h < 120) {
                r = x;
                g = c;
                b = 0;
            } else if (h < 180) {
                r = 0;
                g = c;
                b = x;
            } else if (h < 240) {
                r = 0;
                g = x;
                b = c;
            } else if (h < 300) {
                r = x;
                g = 0;
                b = c;
            } else {
                r = c;
                g = 0;
                b = x;
            }
            return {
                r: igk.Number.parseByte(r * 255),
                g: igk.Number.parseByte(g * 255),
                b: igk.Number.parseByte(b * 255)
            };
        }
    });

    function _setall(t, s) {
        for (var i = 0; i < t.length; i++) {
            t[i] = s;
        }
    }
    createNS("igk.system.colors", {
        toFloatArray: function(n) { // convert expression color to float array of argb
            var c = 0; // igk.system.colors[n.toLowerCase()];
            if (igk.isInteger(n)) {
                // for number
                c = "#" + n.toString(16);
            } else {
                c = igk.system.colors[n.toLowerCase()];
            }
            var t = new Float32Array(4);
            if (c) {
                if (c[0] == "#") {
                    var s = c.substring(1);
                    switch (s.length) {
                        case 1:
                            _setall(t, parseInt((s + s), 16) / 255.0);
                            break;
                        case 3:
                            t[0] = 1.0;
                            t[1] = parseInt((s[0] + s[0]), 16) / 255.0;
                            t[2] = parseInt((s[1] + s[1]), 16) / 255.0;
                            t[3] = parseInt((s[2] + s[2]), 16) / 255.0;
                            break;
                        case 4:
                            t[0] = parseInt((s[0] + s[0]), 16) / 255.0;
                            t[1] = parseInt((s[1] + s[1]), 16) / 255.0;
                            t[2] = parseInt((s[2] + s[2]), 16) / 255.0;
                            t[3] = parseInt((s[3] + s[3]), 16) / 255.0;
                            break;
                        case 6:
                            t[0] = 1.0;
                            t[1] = parseInt((s[0] + s[1]), 16) / 255.0;
                            t[2] = parseInt((s[2] + s[3]), 16) / 255.0;
                            t[3] = parseInt((s[4] + s[5]), 16) / 255.0;
                            break;
                        case 8:
                            t[0] = parseInt((s[0] + s[1]), 16) / 255.0;
                            t[1] = parseInt((s[2] + s[3]), 16) / 255.0;
                            t[2] = parseInt((s[4] + s[5]), 16) / 255.0;
                            t[3] = parseInt((s[6] + s[7]), 16) / 255.0;
                            break;
                    }
                }
            }
            return {
                a: t[0],
                r: t[1],
                g: t[2],
                b: t[3]
            };
        }
    });

    createNS("igk.char", {
        isKeyCodeNumber: function(keyCode) {
            return (keyCode >= 46) && (keyCode < 69);
        },
        isChar: function(key) {
            var t = /^[\w\d]$/.exec(key, true);
            return (t != null);
        },
        isControl: function(keyCode) {
            return (keyCode <= 40);
        }
    });
    createNS("igk.datetime", { // date time utility function
        timeToMs: function(d) {
            if (typeof(d) == 'string') {
                d = igk_str_trim(d);
                if (igk.system.string.endWith(d, 's')) {
                    return parseInt(d.substr(0, d.length - 1) * 1000);
                }
                return parseInt(d) + "ms";
            }
            if (typeof(d) == 'number') {
                return parseInt(d) + "ms";
            }
            return 0;
        }
    });

    function __igk_get_activeXDocument() {
        var progIDs = [
            "Msxml2.DOMDocument.6.0",
            "Msxml2.DOMDocument.5.0",
            "Msxml2.DOMDocument.4.0",
            "Msxml2.DOMDocument.3.0",
            "MSXML2.DOMDocument",
            "MSXML.DOMDocument",
            "htmlfile"
        ];
        for (var i = 0; i < progIDs.length; i++) {
            try {
                return new ActiveXObject(progIDs[i]);
            } catch (e) {};
        }
        return null;
    };
    // static return a new active igk x document
    function __new_activeDocumentObject() {
        var d = __igk_get_activeXDocument();
        if (!d) return null;
        return new(function(d) {
            var m_d = d;
            // encapsulate propertye of created active document to be exposed
            igk_defineProperty(this, "async", {
                get: function() { return m_d.async; },
                set: function(v) { m_d.async = v; }
            });
            igk_defineProperty(this, "readyState", {
                get: function() { return m_d.readyState; }
            });
            igk_defineProperty(this, "firstChild", {
                get: function() { return m_d; }
            });
            igk_appendProp(this, {
                load: function(f) {
                    return m_d.load(f);
                },
                loadXML: function(s) {
                    return m_d.loadXML(s);
                },
                onreadystatechange: null,
                addEventListener: function(method, func, capture) {
                    m_d["on" + method] = func;
                },
                removeEventListener: function(method, func) {
                    m_d["on" + method] = null;
                },
                toString: function() { return "[object://igk:activeXDocument]"; }
            });
        })(d);
    }

    function __dom_get_root(e) {
        var r = null;
        $igk(e).select(">>").each(function() {
            if (this.o.nodeType == 1) {
                r = this.o;
                return false;
            }
            return true;
        });
        return r;
    };

    function __replace_xml_doc(d, txt) {
        var r = igk.dom.loadXML(txt);
        if (r) {
            d.replaceChild(r, d.documentElement);
        }
    };
    var __html = 0;
    var __body = 0;
    createNS("igk.dom", { // dom utilities
        body: function() {
            if (__body && __body.parentNode) {
                return __body;
            }
            __body = $igk(document).select('body').first();
            if (!__body)
                throw '[BJS] - body not yet loaded';
            return __body;
        },
        html: function() {
            if (!__html) {
                var c = document.getElementsByTagName('html');
                if (c.length > 0)
                    __html = $igk(c[0]);
            }
            return __html;
        },
        replaceTagWith: function(q, tag) {
            // @q : node
            // @tag: the new tag
            var d = igk.createNode(tag);
            d.setHtml(q.o.innerHTML);
            igk.dom.copyAttributes(q.o, d.o);
            igk.dom.replaceChild(q.o, d.o);
            return d;
        },
        rectContains: function(domRect, x, y) {
            // determine if domRect contains cursor location
            return ((x >= domRect.x) && (x <= domRect.right)) && ((y >= domRect.y) && (y <= domRect.bottom));
        },
        createXMLDocument: function(tagName) {
            if (igk.navigator.$ActiveXObject()) {
                return __igk_get_activeXDocument();
            }
            return igk.dom.createDocument(tagName || "xml");
        },
        activeXDocument: function() {
            return __new_activeDocumentObject();
        },
        createDocument: function(t, ns) {
            t = t || "xml";
            var d = null;
            var m_cb = null;
            var cs = 1;
            ns = ns || "http://www.w3.org/1999/xhtml";
            if (document.implementation) {
                // if(typeof(document.implementation.createHTMLDocument) !=igk.constants.undef){
                // d=document.implementation.createHTMLDocument('');
                // }else 
                if (typeof(document.implementation.createDocument) != igk.constants.undef) {
                    // in ie createDocument create an XMLDocument object
                    d = document.implementation.createDocument(null, t, null);
                }
            } else {
                if (igk.navigator.$ActiveXObject()) {
                    d = __new_activeDocumentObject(); // new ActiveXObject("htmlfile");
                    cs = 0; // disable the register event location
                }
            }
            if (d) {
                if (cs && !d.load) {
                    d.load = function(l, callback) {
                        m_cb = callback;
                        var fc = d.async ? function(xhr) {
                            // dispatch ready state
                            var e = document.createEvent("HTMLEvents"); // custom ready state
                            e.initEvent("readystatechange", false, false);
                            e.readyState = xhr.readyState;
                            e.xhr = xhr;
                            if ((xhr.readyState == 4) && (xhr.status == 200)) {
                                __replace_xml_doc(d, xhr.responseText);
                                if (m_cb)
                                    m_cb();
                            }
                            d.dispatchEvent(e);
                        } : null;
                        var g = igk.ajx.get(l, null, fc, d.async, false, {
                            //"ajx.xhr" :{responseType:"msxml-document"}
                        });
                        if (!d.async) {
                            __replace_xml_doc(d, g.xhr.responseText);
                        }
                        return d;
                    };
                    d.async = 1;
                } else if (d.load && d.async) {
                    var bfc = d.load;
                    // replace load function					
                    d.load = function(l, callback) {
                        m_cb = callback;
                        bfc.apply(this, [l]);
                        return d;
                    };
                    // __igk(d).reg_event("load",function(){
                    // });
                    __igk(d).reg_event("readystatechange", function() {
                        if (m_cb && ((d.readyState == "complete") || (d.readyState == 4))) {
                            m_cb();
                        }
                    });
                }
            }
            return d;
        },
        getPropertiesTab: function(t) {
            var o = [];
            for (var i in t) {
                if (typeof(t[i]) == "function")
                    continue;
                o[i] = t[i];
            }
            return o;
        },
        compare: function(tab, t) {
            var o = [];
            var c = {};
            for (var i in t) {
                if (typeof(t[i]) == "function")
                    continue;
                o[i] = t[i];
                if (tab[i] != o[i])
                    c[i] = o[i];
            }
            return c;
        },
        replaceChild: function(target, node) { // replace the "target" node with the requested "node"
            if (target)
                target.parentNode.replaceChild(node, target);
        },
        copyAttributes: function(f, n, a) { // copy attribute
            // f from node
            // n to node
            // a ignore attribute
            if ((f == null) || (n == null) || (!n.hasAttributes))
                return;
            var j = "";
            for (var i = 0; i < f.attributes.length; i++) {
                j = f.attributes[i];
                try {
                    if ((a != null) && (a[j.name] == 1))
                        continue;
                    n.setAttribute(j.name, j.value);
                } catch (Exception) {}
            }
        },
        transformXSL: function(xml, xsl) {
            if (!xml || !xsl)
                return null;
            var ex = null;
            if (document.implementation && document.implementation.createDocument && (typeof(XSLTProcessor) != igk.constants.undef)) {
                // navigator: sf,ch,fi
                var xsltProcessor = new XSLTProcessor();
                xsltProcessor.importStylesheet(xsl);
                ex = xsltProcessor.transformToFragment(xml, document);
                if (ex) {
                    // get root node
                    ex = __dom_get_root(ex);
                }
            } else if (igk.navigator.$ActiveXObject() || (xml.responseType == "msxml-document")) {
                if (xml.responseType == "msxml-document") {
                    ex = xml.transformNode(xsl);
                } else {
                    var srcTree = new ActiveXObject("Msxml2.DOMDocument.6.0");
                    srcTree.async = false;
                    // You can substitute other XML file names here.
                    // srcTree.documentElement=xml.documentElement;
                    srcTree.loadXML($igk(xml.documentElement).getOuterHtml());
                    if (srcTree.parseError.errorCode != 0)
                        console.debug("srcTree : error code " + srcTree.parseError.errorCode + " : " + srcTree.parseError.reason);
                    var xsltTree = new ActiveXObject("Msxml2.DOMDocument.6.0");
                    xsltTree.async = false;
                    xsltTree.validateOnParse = false;
                    xsltTree.loadXML($igk(xsl.documentElement).getOuterHtml());
                    if (xsltTree.parseError.errorCode != 0)
                        console.debug("xsl : error code " + xsltTree.parseError.errorCode + " : " + xsltTree.parseError.reason);
                    ex = igk.dom.loadXML(srcTree.transformNode(xsltTree));
                }
            }
            return ex;
        },
        transformXSLString: function(sxml, sxsl) {
            // return the document 
            var ex = null;
            if (igk.navigator.$ActiveXObject()) {
                var srcTree = new ActiveXObject("Msxml2.DOMDocument.6.0");
                srcTree.async = false;
                srcTree.loadXML(sxml);
                if (srcTree.parseError.errorCode != 0) {
                    console.debug("srcTree : error code " + srcTree.parseError.errorCode + " : " + srcTree.parseError.reason);
                    return 0;
                }
                var xsltTree = new ActiveXObject("Msxml2.DOMDocument.6.0");
                xsltTree.async = false;
                xsltTree.validateOnParse = false;
                xsltTree.loadXML(sxsl);
                // xsltTree.replaceChild(xsl.documentElement,xsltTree.xml);
                if (xsltTree.parseError.errorCode != 0) {
                    console.debug("xsl : error code " + xsltTree.parseError.errorCode + " : " + xsltTree.parseError.reason);
                    return 0;
                }
                ex = igk.dom.loadXML(srcTree.transformNode(xsltTree));
            } else {
                if (document.implementation && document.implementation.createDocument) {
                    var xsltProcessor = new XSLTProcessor();
                    // load xml 
                    var xsl = igk.dom.loadXML(sxsl);
                    var xml = igk.dom.loadXML(sxml);
                    if (xsl && xml) {
                        var dsl = document.implementation.createDocument(null, 'xml', null); // msxml-document');
                        // 	  alert(dsl.documentElement);
                        dsl.replaceChild(xsl, dsl.documentElement);
                        var dxl = document.implementation.createDocument(null, 'xml', null); // msxml-document');
                        dxl.replaceChild(xml, dxl.documentElement);
                        // xsltProcessor.importStylesheet(dsl);
                        // ex=xsltProcessor.transformToFragment(dxl,document);
                        xsltProcessor.importStylesheet(dsl);
                        ex = xsltProcessor.transformToFragment(dxl, document);
                        // if(ex){
                        // get root node
                        // ex=__dom_get_root(ex);
                        // }
                    }
                }
            }
            return ex;
        },
        transformXSLUri: function(u_xml, u_xsl, callback) {
            var doc = igk.dom.createDocument();
            var ex = null;
            if (callback) {
                // use async strategie
                doc.async = true;
                doc.load && doc.load(u_xml,
                    function(evt) {
                        var doc2 = igk.dom.createDocument();
                        doc2.async = false;
                        doc2.validateOnParse = false;
                        var xsl = doc2.load(u_xsl);
                        var _ch = igk.navigator.isChrome() || igk.navigator.isSafari();
                        if (_ch) {
                            var dh = null;
                            // correct html tag tag because ignore by those navigator implementation
                            dh = doc2.getElementsByTagName("html");
                            if (dh.length == 1) {
                                $igk(dh[0]).replaceTagWith("igk-html");
                            }
                        }
                        ex = igk.dom.transformXSL(doc, xsl);
                        if (_ch && (ex.tagName.toLowerCase() == "igk-html")) {
                            ex = $igk(ex).replaceTagWith("html").o;
                            // igk.DEBUG=1;
                            // order is important
                            $igk(ex).select(" igk-th,igk-td,igk-tr,igk-table").each(function() {
                                this.replaceTagWith(this.o.tagName.substring(4).toLowerCase());
                                return true;
                            });
                            // igk.DEBUG=0;
                        }
                        ex && callback(ex);
                    }
                );
            } else {
                doc.async = false;
                doc.load && doc.load(u_xml,
                    function(evt) {
                        var doc2 = igk.dom.createDocument();
                        doc2.async = false;
                        var xsl = doc2.load(u_xsl);
                        ex = igk.dom.transformXSL(doc, xsl);
                    }
                );
                return ex;
            }
        },
        loadXML: function(s) {
            var r = null;
            var _ch = igk.navigator.isChrome() || igk.navigator.isSafari();
            if (_ch) {
                s = s.replace(/(\<(\/)?)(html|table|th|tr|td)( [^>]+)?>/gi, '$1igk-$3$4>', s);
            }
            if ("DOMParser" in window) {
                var g = (new window.DOMParser()).parseFromString(s, "text/xml");
                r = __dom_get_root(g);
                if (r && r.tagName.toLowerCase() == "parsererror") {
                    return null;
                }
            } else {
                // TODO
                r = igk.dom.activeXDocument();
                r.load(s);
            }
            return r;
        }
    });
    (function() {
        var m_xsltransform = []; // array of good transformation
        createNS("igk.dom.xslt", {
            initTransform: function() {
                var p = $igk(igk.getParentScript());
                if (!p) {
                    return;
                }
                var _s_ns = igk.system.string;
                var _x = p.select('.xml').first();
                var _y = p.select('.xslt').first();
                var _g = 0;
                if (_x && _y) {
                    var _sxml = _s_ns.rmComment(_x.getHtml());
                    // .substr(4) ; // remove comment
                    // _sxml = _sxml.substring(0, _sxml.length-3);
                    var _sxsl = _s_ns.rmComment(_y.getHtml());
                    // .substr(4) ; // remove comment
                    // _sxsl = _sxsl.substring(0, _sxsl.length-3);
                    var _dxsl = _sxsl; // copy data
                    var opts = igk.JSON.parse(_y.getAttribute("xslt:data"));
                    var rgx = 0;
                    if (opts) {
                        for (var s in opts) {
                            rgx = new RegExp("%" + s + "%", "g");
                            _dxsl = _dxsl.replace(rgx, opts[s]);
                        }
                    }
                    try {
                        _g = igk.dom.transformXSLString(_sxml, _dxsl);
                        _y.remove();
                        if (_g)
                            p.o.replaceChild(_g, _x.o);
                        var _callback = 0;
                        var o = {
                            xml: _sxml,
                            xsl: _sxsl,
                            type: "xmltransform",
                            idx: m_xsltransform.length,
                            options: opts,
                            initResult: function(callback) {
                                callback(p);
                                _callback = callback;
                            },
                            reload: function(opts) {
                                console.debug("reload");
                                console.debug(opts);
                                var c = this.idx;
                                var _dxsl = _sxsl;
                                if (opts) {
                                    for (var s in opts) {
                                        rgx = new RegExp("%" + s + "%", "g");
                                        _dxsl = _dxsl.replace(rgx, opts[s]);
                                    }
                                }
                                var _tg = igk.dom.transformXSLString(_sxml, _dxsl);
                                // p.o.replaceChild(_tg, _g);
                                p.setHtml('').add(_tg); // .o.replaceChild(_tg, _g);
                                _g = _tg;
                                _callback(p);
                            }
                        };
                        m_xsltransform.push(o);
                        return o;
                    } catch (ex) {
                        console.error(ex);
                    }
                }
            }
        });
        igk.appendProperties(igk.dom.xslt.initTransform, {
            lastTransform: function() {
                if (m_xsltransform.length > 0)
                    return m_xsltransform[m_xsltransform.length - 1];
                return null;
            }
        });
    })();


    function igk_form_geturi(frm) { // get the ajx form uri. or action
        var u = $igk(frm).getAttribute("igk-ajx-form-uri");
        if (u)
            return u;
        return frm.getAttribute("action");
    };

    function igk_form_ajx_getfunc(frm) {
        var s = $igk(frm).getAttribute("igk-ajx-form-target");
        var q = null;
        if (s) {
            q = $igk(s).first();
        }
        if (q && !q.isSr()) {
            return igk.ajx.fn.replace_content(q.o);
        }
        // else{
        // igk.winui.notify.showErrorInfo("Error ","Item not found : "+s + " | "+q);
        // }
        return null;
    };

    function igk_form_submit(frm) {
        if (frm) {
            var ajxform = $igk(frm).getAttribute("igk-ajx-form");
            if (ajxform) {
                ns_igk.ajx.postform(frm, igk_form_geturi(frm), igk_form_ajx_getfunc(frm));
            } else {
                frm.submit();
            }
        }
    }
    createNS("igk.form", {
        keypress_validate: function(i, event) {
            var frm = null;
            if (event.keyCode == 13) {
                event.preventDefault();
                frm = window.igk.getParentByTagName(i, 'form');
                igk_form_submit(frm);
                return !1;
            }
            return !0;
        },
        submitonclick: function(l) {
            var q = window.igk.getParentByTagName(l, 'form');
            if (q) {
                igk_form_submit(q);
                return !1;
            }
            return !0;
        },
        confirmLink: function(e, msg) {
            var f = igk.getParentByTagName(e, 'form');
            if (f) {
                if (confirm(msg)) {
                    f.action = '' + e.href;
                    f.confirm.value = 1;
                    f.submit();
                }
            }
        },
        updateTarget: function(form, cibling) {
            // update the cibling object
            // >form : source form 
            // >cibling : object to update properties
            if (!cibling || !form)
                return;
            var q = form;
            for (var i in cibling) {
                if (typeof(q[i]) != 'undefined' && (q[i].type != 'undefined')) {
                    switch (q[i].type) {
                        case "checkbox":
                        case "radio":
                            if (q[i].checked)
                                cibling[i] = q[i].value;
                            else
                                cibling[i] = '';
                            break;
                        default:
                            cibling[i] = q[i].value;
                            break;
                    }
                }
            }
        },
        posturi: function(uri, method) {
            var frm = igk.createNode("form");
            frm.setCss({ "display": "none" });
            igk.dom.body().appendChild(frm.o);
            frm.o.method = method ? method : "POST";
            frm.o.action = uri;
            frm.o.submit();
        },
        form_tojson: function(form) {
            if (!form)
                return null;
            var p = [];
            var e = null;
            var k = "";
            for (var i = 0; i < form.length; i++) {
                e = form.elements[i];
                k = e.id;
                if (!k)
                    continue;
                if (igk.system.string.endWith(k, "[]")) {
                    k = igk.system.string.remove(k, k.length - 2, 2);
                    if (typeof(p[k]) == "undefined") {
                        p[k] = [];
                    }
                }
                switch (e.type) {
                    case "radio":
                    case "checkbox":
                        var vv = e.value;
                        if (!e.checked) {
                            vv = ""; // empty value
                        }
                        if (p[k]) {
                            var m = p[k];
                            if (typeof(m) != "string") {
                                m.push(vv);
                                p[k] = m;
                            } else {
                                var t = [];
                                t.push(m);
                                t.push(vv);
                                p[k] = t;
                            }
                        } else
                            p[k] = vv;
                        break;
                    case "file": // continue
                        break;
                    default:
                        if (p[k]) {
                            var m = p[k];
                            if (typeof(m) != "string") {
                                m.push(e.value);
                                p[k] = m;
                            } else {
                                var t = [];
                                t.push(m);
                                t.push(e.value);
                                p[k] = t;
                            }
                        } else {
                            p[k] = e.value;
                        }
                        break;
                }
            }
            e = 0;
            var msg = "";
            for (var i in p) {
                if (e != 0)
                    msg += ",";
                if (p[i].push)
                    msg += i + ":[" + p[i] + "]";
                else
                    msg += i + ":" + p[i];
                e = 1;
            }
            return "{" + msg + "}";
        },
        parse: function(frm) {
            var m = "";
            m = igk_get_form_posturi(frm);
            return m;
        }
    });
    // method used for ajx form control
    createNS("igk.form.ajxform", {
        submit: function(frm) {
            // var q = $igk(frm).getAttribute("igk:target");
            ns_igk.ajx.postform(frm, igk_form_geturi(frm), function(xhr) {
                if (this.isReady()) {
                    var v = $igk(frm).select(".response").first();
                    if (v) {
                        igk.ajx.fn.replace_content(v.o);
                    } else {
                        igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
                        // igk.winui.notify.showMsBox("ajx form response not found ", this.xhr.responseText);
                    }
                }
            });
        }
    });
    // web utility functions
    createNS("igk.web", {
        setcookies: function(name, value, exdays, path) {
            var exdate = new Date();
            if (exdays)
                exdate.setDate(exdate.getDate() + exdays);
            var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
            c_value += "; SameSite=Strict";
            if (path)
                c_value += "; Path=" + path;
            document.cookie = name + "=" + c_value;
        },
        getcookies: function(name) {
            var c_value = document.cookie;
            var c_start = c_value.indexOf(" " + name + "=");
            if (c_start == -1) {
                c_start = c_value.indexOf(name + "=");
            }
            if (c_start == -1) {
                c_value = null;
            } else {
                c_start = c_value.indexOf("=", c_start) + 1;
                var c_end = c_value.indexOf(";", c_start);
                if (c_end == -1) {
                    c_end = c_value.length;
                }
                c_value = unescape(c_value.substring(c_start, c_end));
            }
            return c_value;
        },
        clearcookies: function() {
            document.cookie = null;
        },
        rmcookies: function(name) {
            var s = document.cookie + "";
            var i = s.indexOf(name);
            if (i >= 0) {
                document.cookie = name + "=;expires=-10";
            }
        }
    });
    // web utility functions
    createNS("igk.web.storage", {
        get: function(k, v) {
            if (window.sessionStorage) {
                return window.sessionStorage[k] || v;
            }
        },
        set: function(k, v) {
            if (window.sessionStorage) {
                window.sessionStorage[k] = v;
            }
        },
        getLocal: function(k, v) {
            if (window.localStorage) {
                return window.localStorage[k] || v;
            }
        },
        storeLocal: function(k, v) {
            if (window.localStorage) {
                window.localStorage[k] = v;
            }
        }
    });
    (function() { // init system
        if (typeof(Event) == IGK_UNDEF) {
            Event = {
                prototype: {},
                toString: function() {
                    return "[object igk.event]";
                }
            };
        }
        if ((typeof(Event.prototype.preventDefault) == IGK_UNDEF)) {
            Event.prototype.preventDefault = function() {};
        }
        return !0;
    })();
    var m_currentModule = null; // current module name
    var m_module_info = null; // current module info
    var m_module_loaded = {}; // module loaded execution
    var m_moduleList = {}; // module info flist
    function igk_get_trace_info(_m, e, c) {
        if (e.stack) {
            // FIREFOX 
            var s = e.stack + "";
            var t = s.trim().split("\n");
            // var tt=t[c];
            // get chain list
            var li = [];
            // FIREFOX | chrome | and ie nor render the stack at the same
            // var regex=new RegExp("[^(\@]*((ftp|http|https):// [^:]+)");
            var regex = new RegExp("((ftp|http|https)://([^:]+:[0-9]+(/([^:]+))?|[^:]+))");
            for (var i = 0; i < t.length; i++) {
                if (regex.test(t[i])) {
                    li.push(t[i]);
                }
            }
            c = c || li.length - 1;
            var tt = li[c];
            if (tt) {
                var uri = regex.exec(tt)[0];
                _m.module_src = uri; // module start source
                _m.dir = uri.substring(0, uri.lastIndexOf("/"));
            }
            _m.stack = e.stack;
            _m.stack_list = li;
        } else {
            // can't get stack in strick mode or SAFARI < 6
            var src = igk_getScriptSrc();
            if (src != null) {
                _m.modedule_src = src; // tt.substring(0,tt.lastIndexOf("/"));
                _m.dir = src.substring(0, src.lastIndexOf("/"));
            } else {
                console.error("/!\\ cant get module path" + igk_getScriptSrc());
                return 0;
            }
        }
        return 1;
    }
    // define the a module script. set the root module
    function igk_defineModule(n) {
        m_currentModule = n;
        var e = new Error();
        var _m = {
            name: n,
            module_src: null,
            dir: null
        };
        igk.error.lastError = e;
        var v_script = document.currentScript;
        if (v_script) {
            var u = (v_script.src + "");
            _m.initScript = v_script;
            _m.dir = u.substring(0, u.lastIndexOf("/"));
            m_moduleList[n] = _m;
        } else {
            if (igk_get_trace_info(_m, e, 1)) {
                m_module_info = _m;
                m_moduleList[n] = _m;
            } else {
                m_module_info = null;
            }
        }
        return _m;
    };

    function igk_getModule(n) {
        n = n || m_currentModule;
        return m_moduleList[n];
    };

    function igk_getScriptSrc() {
        // get current script declaration file
        var g = document.scripts;
        if (g) {
            return g[g.length - 1].src;
        }
        return null;
    };
    m_LibScript = igk_getScriptSrc();

    function createObject(T, t, args) {
        for (var j in T.prototype) {
            if (j in t) {
                // filter 
                // check if read only 
                //
                var check = window.Object.getOwnPropertyDescriptor(t.prototype, j);
                if (check && !check.writable) {
                    continue;
                }
            }
            t[j] = T.prototype[j];
        }
        T.apply(t, args);
    };
    createNS("igk.system", {
        // system global management namespace
        createExtensionProperty(p, ns, obj) {
            var prop = ns;
            var _n = igk_get_namespace(prop);
            if (typeof(_n) == "undefined") {
                _n = createNS(prop, {});
            }
            if (typeof(_n[p]) == "undefined") {
                var ob = new __extensionPrototype(p);
                igk_defineProperty(_n, p, {
                    get: function() {
                        return ob;
                    }
                });
            }
            if (obj)
                Object.setPrototypeOf(obj, _n[p]);
            return _n[p];
        },
        /**
         * extends system option 
         * @param {*} n name
         * @param {*} ob object
         */

        defineOption(n, ob){
            let op = igk.system.createNS(n,ob);            
            return op;
        },
        stringProperties: function(o) {
            // stringify properties
            var m = "";
            var k = 0;
            for (var i in o) {
                if (typeof(o[i]) == "function") continue;
                if (k == 1)
                    m += ",";
                m += i + ":" + o[i];
                k = 1;
            }
            return m;
        },
        createNS() {
            return createNS.apply(this, arguments);
        },
        createPNS: createPNS,
        getScriptSrc: igk_getScriptSrc,
        appendProp: igk.appendProperties,
        module() {
            return igk_defineModule.apply(this, arguments);
        },
        isClass(o, t, opts) {
            // o: object
            // t: true to raise error
            // opts: options object with [msg] properties
            var r = o instanceof igk_namespace;
            if (t)
                throw opts.msg;
            return r;
        },
        isInherit(o, A) {
            if (!o || (typeof(o) != 'object'))
                return false;
            var c = Object.getPrototypeOf(o);
            while (c) {
                if (c == A) {
                    return true;
                }
                c = c.prototype;
                if (c == Object)
                    break;
            }
            return false;
        },
        createClass(ns, tn, constructor, opts) {
            if (!igk.system.class) {
                igk.system.class = function() {};
            }
            if (!constructor) {
                console.error("createClass constructor required");
                return null;
            }
            // var tn = '';
            var p = null;
            if (typeof(tn) == 'object') {
                var g = tn;
                tn = g.name;
                p = g.parent;
                if (('parent' in g) && !igk.isDefine(p)) {
                    throw ("[igk] - parent not found for " + g.name + " " + p);
                }
            }
            var fc = 0;
            fc = function() {
                if (this == ns) {
                    throw (opts ? opts.msg : null) || igk.R.gets("<< new >> operator is require");
                }
                if (p) { //invoke parent constructor
                    createObject(p, this, arguments);
                }
                constructor.apply(this, arguments);
                //bind properties that apply to 
                igk.appendProperties(this, {
                    getTypeFullName: function() {
                        return ns.fullname + "." + tn;
                    },
                    getType: function() {
                        return tn;
                    },
                    isInstanceOf: function(t) {
                        return igk.system.isInherit(this, t);
                    }
                });
                // this.getTypeFullName = function () {
                // return ns.fullname + "." + tn;
                // };
                // this.isInstanceOf = function (t) {
                // return this instanceof t;
                // };
            };
            fc.Name = tn;
            //igk extra info
            fc.__source__ = 'class';
            fc.__name__ = tn;
            igk.defineProperty(fc, 'fullname', {
                get: function() {
                    return ns.fullname + '.' + tn;
                }
            });
            fc.__fullname__ = ns.fullname + '.' + tn;
            if (typeof(p) == 'string') {
                var r = igk.system.getNS(p);
                if (r == null) {
                    var bfc = fc;
                    fc = function() {
                        var k = igk.system.getNS(p);
                        if (k) {
                            bfc.prototype = k;
                            ns[tn] = bfc;
                        } else {
                            throw ("not found : " + p);
                        }
                        p = k;
                        return new bfc();
                    };
                } else {
                    p = r;
                    fc.prototype = p;
                }
            } else {
                if (p) {
                    //fc.__igk__prototype = p;
                    // fc.prototype = p.prototype;
                    fc.prototype = p;
                }
            }
            igk.system.createNS(ns.fullname, {
                tn: fc
            });
            igk.defineProperty(fc, 'name', { get: function() { return tn; } });
            ns[tn] = fc;
            return fc;
        },
        createClassList: function(ns, e) {
            if (!ns)
                return 0;
            var g = 0;
            for (var i in e) {
                if (typeof(g = e[i]) == "function") {
                    ns[i] = igk.system.createClass(ns, i, g);
                    // ns[i].__source__ = "class";
                }
            }
        },
        getClassList: function(ns) {
            if (typeof(ns) == 'string')
                ns = igk.system.getNS(ns);
            var tab = [];
            for (var s in ns) {
                if (typeof(ns[s]) == 'function' && (ts = ns[s].__source__) && (ts == 'class')) {
                    tab.push({ name: s, ns: ns.fullname, func: ns[s] });
                }
            }
            return tab;
        },
        getBindFunctions: function(n) {
            var r = {};
            if (typeof(n) == 'object') {
                for (var i in n) {
                    if (typeof(n[i]) == 'function')
                        r[i] = n[i].bind(n);
                }
            }
            return r;
        },
        evalScript: function(item) {
            if (item == null) return;
            var t = item.getElementsByTagName("script");
            for (var i = 0; i < t.length; i++) {
                igk.evalScript(t[i].innerHTML, t[i].parentNode, t[i]);
            }
        },
        toString: function() { return "igk.system"; },
        getNS: igk_get_namespace,
        require: function(n) {
            var t = igk_get_namespace(n);
            if (!t)
                throw new Error('namespace ' + n + ' not found');
            return t;
        },
        include: function(u) {
            var src = null;
            // include script in expression
            igk.ajx.post(u, null, function(xhr) {
                if (this.isReady()) {
                    src = xhr.responseText;
                    if (src)
                        eval(src);
                }
            });
        }
    });
    createNS("igk.system.diagnostic", {
        traceinfo: igk_get_trace_info, // module, error, level
        debug: function(m) {
            console.debug(m);
        },
        assert: function(c, m) {
            if (c)
                console.assert(c, m);
        }
    });

    function _s(t) {
        if ((t + "").length <= 1)
            return "0" + t;
        return t;
    };
    // igk.system.color management
    (function() {
        var _cnode = 0; //convertter node with css
        var S = igk.system;
        var _CNS = igk.system.color || {};

        function __ColorClass(r, g, b, a) { // .ctrl color 
            this.r = r;
            this.g = g;
            this.b = b;
            this.a = a ? a : 1.0;
            igk.appendProperties(this, {
                toString: function() {
                    var r = this.r;
                    var g = this.g;
                    var b = this.b;
                    var a = this.a;
                    if (a == 1.0)
                        return "rgb(" + r + "," + g + "," + b + ")";
                    return "rgba(" + r + "," + g + "," + b + "," + a + ")";
                },
                toHtml: function() { // var decColor=b +(256 * g) +(65536 * r);
                    var r = this.r;
                    var g = this.g;
                    var b = this.b;
                    var a = this.a;
                    var decColor = (r << 16) + (g << 8) + b;
                    if (decColor == 0)
                        if (this.a == 0)
                            return "#00000000";
                        else
                            return "#000";
                    return "#" + decColor.toString(16).padStart(6, '0');
                },
                toColorf: function(p) {
                    var _r = Math.round;
                    p = Math.pow(100, (p || 5));
                    return {
                        a: 1.0,
                        r: _r((this.r / 255.0) * p) / p,
                        g: _r((this.g / 255.0) * p) / p,
                        b: _r((this.b / 255.0) * p) / p
                    };
                },
                toInt: function() { return (this.r << 16) + (this.g << 8) + this.b; },
                getLuminance: function() {
                    var r, g, b, cl = this;
                    r = cl.r;
                    g = cl.g;
                    b = cl.b;
                    return ((r * 299) + (g * 587) + (b * 114)) / (255 * 1000.0);
                }
            });
        };
        igk.appendProperties(__ColorClass, _CNS);
        igk.appendProperties(igk.system, {
            color: __ColorClass,
            colorGetA: function(v) {
                if (!v)
                    return 0;
                if (typeof(v) == 'string') {
                    if (v.toLowerCase() == "transparent") {
                        return 0;
                    }
                    if (/^#/.exec(v)) {
                        v = v.substring(1);
                        switch (v.length) {
                            case 8:
                                var result = /^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(v);
                                return Math.round(parseInt(result[1], 16) * 100 / 255) / 100;
                                break;
                            case 4:
                                var result = /^([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})$/i.exec(v);
                                return Math.round(parseInt(result[1] + result[1], 16) * 100 / 255) / 100;
                                break;
                        }
                    }
                }
                return 1;
            },
            colorFromString: function(value) {
                // @@@ static function get color from string
                var r = 0;
                var g = 0;
                var b = 0;
                var a = 1.0;
                if (value) {
                    var t = /rgb\(([^\)])+\)/.exec(value, "i");
                    if (t) {
                        var rgb = value.substring(4, value.length - 1)
                            .replace(/ /g, '')
                            .split(',');
                        r = parseInt(rgb[0]);
                        g = parseInt(rgb[1]);
                        b = parseInt(rgb[2]);
                    } else if ((t = /rgba\(([^\)])+\)/.exec(value, "i"))) {
                        var rgb = value.substring(5, value.length - 1)
                            .replace(/ /g, '')
                            .split(',');
                        r = parseInt(rgb[0]);
                        g = parseInt(rgb[1]);
                        b = parseInt(rgb[2]);
                        a = parseFloat(rgb[3]);
                    } else if (/^#/.exec(value)) {
                        value = value.substring(1);
                        switch (value.length) {
                            case 6:
                                var result = /^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(value);
                                r = parseInt(result[1], 16);
                                g = parseInt(result[2], 16);
                                b = parseInt(result[3], 16);
                                break;
                            case 8:
                                var result = /^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(value);
                                //#FF00FF00 = aaRRGGBB
                                a = parseInt(result[1], 16) / 255.0;
                                r = parseInt(result[2], 16);
                                g = parseInt(result[3], 16);
                                b = parseInt(result[4], 16);
                                break;
                            case 4:
                                var result = /^([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})$/i.exec(value);
                                //#F000 = aRGB
                                a = parseInt(result[1] + result[1], 16) / 255.0;
                                r = parseInt(result[2] + result[2], 16);
                                g = parseInt(result[3] + result[3], 16);
                                b = parseInt(result[4] + result[4], 16);
                                break;
                            case 3:
                                var result = /^([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})$/i.exec(value);
                                r = parseInt(result[1] + result[1], 16);
                                g = parseInt(result[2] + result[2], 16);
                                b = parseInt(result[3] + result[3], 16);
                                break;
                        }
                    } else {
                        if (igk.system.color[value.toLowerCase()]) {
                            return igk.system.colorFromString(igk.color[value.toLowerCase()]);
                        }
                    }
                }
                return new igk.system.color(r, g, b, a);
            },
            colorfFromString: function(value) {
                return igk.system.colorFromString(value).toColorf();
            },
            hsl2rgb: function(h, l, v) {
                if (!_cnode) {
                    _cnode = igk.createNode("div");
                    //chrome require node to be added
                    if (igk.navigator.getProperty('cssDomRequire'))
                        igk.dom.body().add(_cnode);
                }
                _cnode.setCss({ backgroundColor: 'hsl(' + h + ',' + l + '%, ' + v + '%)' });
                return S.colorFromString(_cnode.getComputedStyle('backgroundColor'));
            },
            hsl2rgbf: function(h, l, v) {
                if (!_cnode) {
                    _cnode = igk.createNode("div");
                }
                _cnode.setCss({ backgroundColor: 'hsl(' + h + ',' + l + '%, ' + v + '%)' });
                return S.colorfFromString(_cnode.getComputedStyle('backgroundColor'));
            },
            rgb2hsl: function(r, g, b) {
                var min;
                var max;
                var delta;
                var r = r / 255.0;
                var g = g / 255.0;
                var b = b / 255.0;
                var h;
                var s;
                var v;
                min = Math.min(Math.min(r, g), b);
                max = Math.max(Math.max(r, g), b);
                v = max;
                delta = max - min;
                if (max == 0 || delta == 0) {
                    // R, G, and B must be 0, or all the same.
                    // In this case, S is 0, and H is undefined.
                    // Using H = 0 is as good as any...
                    s = 0;
                    h = 0;
                } else {
                    s = delta / max;
                    if (r == max) {
                        // Between Yellow and Magenta
                        h = (g - b) / delta;
                    } else if (g == max) {
                        // Between Cyan and Yellow
                        h = 2 + (b - r) / delta;
                    } else {
                        // Between Magenta and Cyan
                        h = 4 + (r - g) / delta;
                    }
                }
                // Scale h to be between 0 and 360. 
                // This may require adding 360, if the value
                // is negative.
                h *= 60;
                if (h < 0) {
                    h += 360;
                }
                // Scale to the requirements of this 
                // application. All values are between 0 and 255.
                return { h: h, s: (s * 100), l: (v * 100) };
            }
        });
    })();
    //date management
    createNS("igk.system.Date", {
        format: function(date, format) {
            var _date = new Date(date);
            var s = format;
            s = s.replace(/(Y|m|d|H|i|s)/g, function(m) {
                switch (m) {
                    case "Y":
                        return _date.getFullYear();
                    case "m":
                        return _s(_date.getMonth() + 1);
                    case "d":
                        return _s(_date.getDate());
                    case "H":
                        return _s(_date.getHours());
                    case "i":
                        return _s(_date.getMinutes());
                    case "s":
                        return _s(_date.getSeconds());
                }
            });
            return s;
            // var _dates =
            // _date.getFullYear() + "-" + _s(_date.getMonth() + 1) + "-" + _s(_date.getDate()) +
            // " " + _s(_date.getHours()) + ":" + _s(_date.getMinutes()) + ":" + _s(_date.getSeconds());
        }
    });
    // export module loading
    createNS("igk.system.module", {
        load: function(n, callback) { // load script in module script async
            var _m = m_module_info; // get current module info
            var _s = null;
            if (!_m || !_m.dir)
                return;
            // var _loaded 
            if (_m && !(n in m_module_loaded)) {
                m_module_loaded[n] = 1;
                var q = igk.createNode("script");
                q.setAttribute("defer", 1);
                if (document.head)
                    $igk(document.head).add(q);
                q.reg_event("load", function(evt) {
                    if (callback) {
                        callback(q);
                    }
                });
                q.o.src = _m.dir + n;
            }
        },
        getFileUri: function(f) {
            if (igk.validator.isUri(f))
                return f;
            var _m = m_module_info;
            return _m.dir + f;
        },
        getModuleLocation: function() {
            return m_module_info ? m_module_info.dir : null;
        },
        getModule: igk_getModule
    });
    igk.defineProperty(igk.system.module, "loadedModules", { get: function() { return m_module_loaded; } });
    igk.defineProperty(igk.system.module, "currentModuleInfo", { get: function() { return m_module_info; } });
    var _app_link = null;
    createNS("igk.system.apps", { // system application manager namespace
    });
    igk.defineProperty(igk.system.apps, "link", { get: function() { return _app_link; } });
    createNS("igk.system.styles", {
        textShadow: function(x, y, offset, color) {
            this.x = x;
            this.y = y;
            this.offset = offset,
                this.color = igk.system.createFromString(color);
            this.toString = function() { return "igk.system.styles.textshadow"; };
        },
        textShadowCreate: function(s) { // create a text shadow properties
            if (s == "none") {
                return new igk.system.styles.textShadow(0, 0, 0, "transparent");
            } else {
                var tab = s.split(" ");
                if (tab.length == 4) {
                    return new
                    igk.system.styles.textShadow(
                        igk.getNumber(tab[0]),
                        igk.getNumber(tab[1]),
                        igk.getNumber(tab[2]),
                        tab[3]);
                }
                return null;
            }
        }
    });
    var m_lastError;
    createNS("igk.error", {});
    igk_defineProperty(igk.error, "lastError", { get: function() { return m_lastError; }, set: function(v) { m_lastError = v; } });
    createNS("igk.system.collections", {
        list: function() { // list object
            var m_list = [];
            igk.appendProperties(this, {
                getCount: function() {
                    return m_list.length;
                },
                toString: function() { return "igk.system.collections.list#" + this.getCount() },
                add: function(item) {
                    // if(item && !this.contains(item))
                    m_list.push(item);
                },
                clear: function() {
                    if (this.getCount() > 0) {
                        m_list = [];
                    }
                },
                remove: function(item) {
                    var cp = [];
                    var c = false;
                    for (var s = 0; s < m_list.length; s++) {
                        if (m_list[s] == item)
                            continue;
                        cp.push(m_list[s]);
                        c = !0;
                    }
                    m_list = cp;
                    return c;
                },
                removeAt: function(index) {
                    // create a clone copy
                    var cp = [];
                    var c = false;
                    for (var s = 0; s < m_list.length; s++) {
                        if (s == index) continue;
                        cp.push(m_list[s]);
                        c = !0;
                    }
                    m_list = cp;
                    return c;
                },
                indexOf: function(item) {
                    if (!item)
                        return -1;
                    for (var i = 0; i < this.getCount(); i++) {
                        if (m_list[i] == item) {
                            return i;
                        }
                    }
                    return -1;
                },
                getItemAt: function(index) {
                    if ((index >= 0) && (index < this.getCount()))
                        return m_list[index];
                    return null;
                },
                to_array: function() {
                    var t = [];
                    for (var i = 0; i < this.getCount(); i++) {
                        t.push(m_list[i]);
                    }
                    return t;
                },
                contains: function(item) {
                    if (!item)
                        return !1;
                    for (var i = 0; i < this.getCount(); i++) {
                        if (m_list[i] == item) {
                            return !0;
                        }
                    }
                    return !1;
                },
                forEach: function(callback) {
                    if (callback) {
                        var c = this.getCount();
                        for (var i = 0; i < c; i++) {
                            callback(m_list[i], i);
                        }
                    }
                    return this;
                }
            });
        },
        dictionary: function() { // represent igk system dictionary
            var m_keys = new igk.system.collections.list(); // keys list
            var m_values = new igk.system.collections.list(); // obj list
            var m_index = -1;
            var m_ck = null;
            var m_cv = null;
            igk.appendProperties(this, {
                it: function() {
                    m_index = 0;
                    m_ck = null;
                    m_cv = null;
                },
                clear: function() {
                    m_keys.clear();
                    m_values.clear();
                    m_ck = null;
                    m_cv = null;
                    m_index = 0;
                },
                moveNext: function() {
                    if (m_index < this.getCount()) {
                        m_ck = m_keys.getItemAt(m_index);
                        m_cv = m_values.getItemAt(m_index);
                        m_index++;
                        return !0;
                    } else {
                        m_ck = null;
                        m_cv = null;
                        return !1;
                    }
                },
                getcurrentKey: function() { return m_ck; },
                getcurrentValue: function() { return m_cv; },
                toString: function() {
                    return "igk.system.collections.dictionary#[count:" + this.getCount() + "]";
                },
                getKeys: function() { // return keys of the collection as an array
                    return m_keys.to_array();
                },
                getValues: function() { // return value of the collection as an array
                    return m_values.to_array();
                },
                containKey: function(key) {
                    return (m_keys.indexOf(key) != -1);
                },
                getItem: function(key) {
                    var i = m_keys.indexOf(key);
                    if (i == -1)
                        return null;
                    return m_values.getItemAt(i);
                },
                getCount: function() {
                    return m_keys.getCount();
                },
                add: function(key, value) {
                    if (!key) return;
                    var i = m_keys.indexOf(key);
                    if (i == -1) {
                        m_keys.add(key);
                        m_values.add(value);
                    }
                },
                remove: function(key) {
                    var i = m_keys.indexOf(key);
                    if (i != -1) {
                        m_keys.removeAt(i);
                        m_values.removeAt(i);
                        return 1;
                    }
                    return 0;
                }
            });
        }
    });
    createNS("igk.system.array", { // array utility fonctions
        isContain: function(t, u) {
            if (t) {
                if (t.contains) { // firefox
                    return t.contains(u);
                } else {
                    for (var i = 0; i < t.length; i++) {
                        if (t[i] == u)
                            return !0;
                    }
                }
            }
            return !1;
        },
        slice: function(tab, startindex) {
            var v_otab = [];
            for (var i = startindex; i < tab.length; i++) {
                v_otab.push(tab[i]);
            }
            return v_otab;
        },
        sort: function(tab) {
            if (tab) {
                if (tab && Array && Array.sort) {
                    return Array.sort(tab);
                } else if (tab.sort) {
                    // implement a sort method function
                    tab.sort(function(a, b) {
                        var ta = typeof(a);
                        var tb = typeof(b);
                        if (ta == tb) {
                            switch (ta) {
                                case 'number':
                                    if (a < b)
                                        return -1;
                                    if (a == b)
                                        return 0;
                                    return 1;
                                    break;
                                default:
                                    break;
                            }
                        }
                        return (a + '').localeCompare(b + '');
                    });
                }
                return tab;
            }
        }
    });



    function _CustomEvent(event, params) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }
    createNS("igk.winui.events", { // utility events function
        raise: function(t, n) { // raiseEvent
            // @t : target 
            // @n : event name 
            // extra: properties to append
            if (igk.Event) {
                var evt = igk.Event.createEvent(n);
                if (arguments.length > 2) {
                    igk.appendProperties(evt, arguments[2]);
                }
                t.dispatchEvent(evt);
                return;
            }
            if (window.Event) {
                try {
                    var evt = new Event(n);
                    if (arguments.length > 2) {
                        igk.appendProperties(evt, arguments[2]);
                    }
                    t.dispatchEvent(evt);
                    createNS("igk.Event", {
                        createEvent: function(n) {
                            return new Event(n);
                        }
                    });
                } catch (ex) {
                    createNS("igk.Event", {
                        createEvent: function(n) {
                            return new _CustomEvent(n, null);
                        }
                    });
                    var evt = igk.Event.createEvent(n);
                    if (arguments.length > 2) {
                        igk.appendProperties(evt, arguments[2]);
                    }
                    t.dispatchEvent(evt);
                }
            }
        },
        createEvent: _CustomEvent,
        regKeyPress: function(fc) {
            igk_winui_reg_event(document, "keypress keyup", fc);
        },
        unregKeyPress: function(fc) {
            igk.winui.unreg_event(document, "keypress keyup", fc);
        },
        /**
         * return system global management event
         */
        global: function() {
            if (!__global) {
                __global = document.createElement('div');
                $igk(__global).addEvent('igk_controller_ready', {});
            }
            return __global;
        },
        /**
         * clean all registrated event
         * @param {*} item 
         * @param {*} n 
         */
        clean: function(item, n) {
            let w = $igk(item);
            let e = w.o[n];
            if (e && (e.constructor.name == 'Event')) {
                w.unreg_event(n);
                w.o[n] = null;
            }
        }
    });
    (function() {
        var maillink = null;
        createNS("igk.mail", {
            sendmail: function(mail) {
                if (maillink == null)
                    maillink = igk.createNode('a');
                var a = maillink;
                a.o.href = 'mailto:' + mail;
                igk.dom.body().appendChild(a.o);
                a.o.click();
            }
        });
    })();
    (function() {
        var m_iframes = [];

        function _get_loaded_text(item) {
            var c = "";
            if (item) {
                try {
                    if (item.contentDocument)
                        item = item.contentDocument;
                    else if (item.object) {
                        item = item.object;
                    }
                } catch (ex) {
                    // igk.show_notify_error('Exception','ie contentDocument error');
                }
                if (item.childNodes == null)
                    return;
                var x = item.childNodes.length;
                for (var i = 0; i < x; i++) {
                    if (item.childNodes[i]) {
                        c += $igk(item.childNodes[i]).getOuterHtml();
                    }
                }
            }
            return c;
        };
        createNS("igk.file", { // file utiility name space
            getcontents: function(uri, callback, async) {
                if (uri == null)
                    return null;
                // obtentions du contenu ne peut 
                var c = "";
                if (typeof(async) == IGK_UNDEF)
                    async = !0;
                if (!async) {
                    // used ajx // in that case log is writed to console debug . 		
                    igk.ajx.get(uri, null, function(xhr) {
                        if (this.isReady()) {
                            callback.apply(window, [xhr.responseText]);
                        }
                    }, false);
                    return;
                }
                var wait = !0;
                var q = igk.createNode("iframe");
                q.addClass("no-vibility")
                    .setAttribute("src", uri)
                    .setAttribute("async", false)
                    .reg_event("load", function(e) {
                        wait = false;
                        e.preventDefault();
                        c = _get_loaded_text(q.o);
                        q.o.parentNode.removeChild(q.o);
                        if (callback)
                            callback.apply(window, [c]);
                    });
                if (document.head) {
                    document.head.appendChild(q.o);
                } else {
                    console.debug('c = ');
                    var h = document.getElementsByTagName('head')[0];
                    if (h) h.appendChild(q.o);
                }
            },
            get_loaded_text: _get_loaded_text
        });
    })();

    function __ctrl_ctr() {
        this.name = "ctrl";
    };
    createNS("igk.html.ctrl", {
        ctrl: __ctrl_ctr,
        checkbox: new(function() {
            __ctrl_ctr.apply(this);
            igk_appendProp(this, {
                toggle: igk_check_all,
                init: function(t, c) {
                    igk.html.ctrl.checkbox.toggle(t,
                        $igk(t).getParentByTagName('table'),
                        t.checked,
                        true,
                        c);
                },
                toString: function() { return "[igk.Obj igk.html.ctrl.checkbox]"; }
            });
        })(),
        btn: new(function() {
            igk_appendProp(this, {
                toString: function() { return "[igk.Obj igk.html.ctrl.btn]"; }
            });
        })()
    });
    //STRING MANIPULATION
    createNS("igk.system.string", {
        split: function(str, ch) {
            var i = 0;
            var r = [];
            var w = "";
            while (i < str.length) {
                if (str[i] == ch) {
                    r.push(w);
                    w = "";
                } else
                    w += str[i];
                i++;
            }
            if (w.trim().length > 0) {
                r.push(w);
            }
            return r;
        },
        rmComment: function(s) { // remove comment
            var vi = s.substr(4); // remove comment
            return vi.substring(0, vi.length - 3);
        }
    });
    //----------------------------------------------------------------
    //update string proptotype
    //----------------------------------------------------------------
    var _str_ = window.String.prototype;
    if (!_str_.padStart) {
        _str_.padStart = function(n, s) {
            var g = "";
            while (this.length < n) {
                n--;
                g += s;
            }
            return g + this;
        };
    }
    createNS("igk.system.regex", {
        item_match_class: igk_item_match_class,
        item_inherit_class: igk_item_inherit_class,
        split: function(pattern, v) {
            var q = new RegExp(pattern);
            var s = new RegExp("^(" + pattern + ")+$");
            if (igk.isUndef(v)) {
                throw "is undefr(is undef " + pattern;
                return v;
            }
            if (!v.match(s)) {
                return [v];
            }
            var r = [];
            while ((v.length > 0) && v.match(q)) {
                r.push(RegExp.$1);
                v = v.replace(q, "");
            }
            return r;
        }
    });
    createNS("igk.system.fonts", {
        // special function to install font in balafon system
        installFont: function(a, uri) {
            var s = a.getAttribute('igk-font-name');
            var uri = uri + '&n=' + s;
            var frm = $igk(a).getParentForm();
            s = a.getAttribute("id");
            var top = a.offsetParent.scrollTop;
            igk.ajx.get(uri, null, function(xhr) {
                if (this.isReady()) {
                    var i = igk.createNode("dummy");
                    this.setResponseTo(i.o);
                    // get the first form
                    var n = i.select("form").first();
                    if (n) {
                        if (n.o.parentNode) {
                            n.o.parentNode.replaceChild(n, frm);
                        } else {
                            // no parent
                            console.debug(";-) No parent found for " + frm);
                        }
                        n.select("#" + s).each(function() {
                            this.o.offsetParent.scrollTop = top;
                            return !1;
                        });
                        if (igk.system.apps.link)
                            igk.system.apps.link.reload();
                    }
                }
            });
        }
    });


    // ------------------------------------------------------------------------------------
    // igk.winui NAME SPACE
    // ------------------------------------------------------------------------------------
    // used to animate . callback must return 1 in oder to continue animation
    function igk_animate(callback) {
        var animFrame = igk.animation.getAnimationFrame();
        var animObj = {
            cancel: function() {
                igk.html.canva.cancel(this.id);
            }
        };

        function __doCall() {
            if (callback(1000 / 60)) {
                setTimeout(__doCall, 1000 / 60);
            }
        };

        function __runCall() {
            if (callback()) {
                // continue animate
                animObj.id = animFrame(__runCall);
            }
        };
        // used to animated rendering scene
        if (typeof(animFrame) == "undefined") {
            __doCall();
        } else {
            animObj.id = animFrame(__runCall);
            return animObj;
        }
    };
    // used to cancel 
    function igk_animate_cancel(id) {
        var _canimFrame = igk.animation.getAnimationCancelFrame();
        if (_canimFrame) {
            return _canimFrame(id);
        }
    };

    function igk_create_event_ojectManager() {
        var m_obj = new igk.system.collections.list(); // keys list
        var m_value = new igk.system.collections.list(); // obj list
        var m_key = null;
        var m_ukey = null;
        var m_unreg = 0;
        igk.appendProperties(this, {
            raise: function(item, method) { // raiseEvent created
                // dispatch event used for ie8 compatibility
                if (!item)
                    return;
                m_key = item;
                var i = m_obj.indexOf(item);
                // alert("raise "+item);
                if (i != -1) {
                    var dic = m_value.getItemAt(i);
                    if (dic.containKey(method)) {
                        var funcs = dic.getItem(method).funcs;
                        // alert("ddd " + item + ": "+ " "+method+" " );
                        var args = [item[method]];
                        for (var s = 0; s < funcs.getCount(); s++) {
                            funcs.getItemAt(s).apply(item, args);
                        }
                    }
                }
            },
            register: function(item, method, func) {
                if (!item || !func || (typeof(func) != IGK_FUNC) || (m_key == item)) // last to avoid recursion
                    return;
                m_key = item;
                var i = m_obj.indexOf(item);
                var o = null;
                if (i == -1) {
                    o = { target: item, funcs: new igk.system.collections.list(), method: method };
                    // maintain index
                    o.funcs.add(func);
                    var dic = new igk.system.collections.dictionary();
                    dic.add(method, o);
                    m_obj.add(item);
                    m_value.add(dic);
                } else {
                    var dic = m_value.getItemAt(i);
                    if (dic) {
                        if (dic.containKey(method)) {
                            dic.getItem(method).funcs.add(func);
                        } else {
                            var o = { target: item, funcs: new igk.system.collections.list(), method: method };
                            o.funcs.add(func);
                            dic.add(method, o);
                        }
                    }
                }
                m_key = null;
            },
            unregister: function(item, method, func) {
                if (m_unreg)
                    return
                m_unreg = 1;
                var o = null;
                if (item == window)
                    o = item;
                else
                    o = $igk(item).o;
                if ((!item) || (m_ukey == item))
                    return;
                m_ukey = item;
                var i = m_obj.indexOf(o);
                var dic = null;
                if (i != -1) {
                    dic = m_value.getItemAt(i);
                    var tab = dic.getValues();
                    if (method) {
                        // remove all method registrated
                        if (typeof(func) == 'undefined') {
                            for (var t = 0; t < tab.length; t++) {
                                if (tab[t].method == method) {
                                    var c = tab[t].funcs.to_array();
                                    for (var x = 0; x < c.length; x++) {
                                        igk.winui.unreg_event(o, tab[t].method, c[x]);
                                    }
                                    dic.remove(method);
                                    continue;
                                }
                            }
                        } else {
                            for (var t = 0; t < tab.length; t++) {
                                if (tab[t].method == method) {
                                    var c = tab[t].funcs.to_array();
                                    for (var x = 0; x < c.length; x++) {
                                        if (c[x] == func) {
                                            igk.winui.unreg_event(o, tab[t].method, c[x]);
                                        }
                                    }
                                    continue;
                                }
                            }
                        }
                        m_ukey = null;
                        m_unreg = 0;
                        return;
                    }
                    for (var t = 0; t < tab.length; t++) {
                        // 							
                        var c = tab[t].funcs.to_array();
                        for (var x = 0; x < c.length; x++) {
                            igk.winui.unreg_event(o, tab[t].method, c[x]);
                        }
                    }
                    m_obj.removeAt(i);
                    m_value.removeAt(i);
                }
                // else{
                // }
                m_unreg = 0;
                m_ukey = null;
            },
            toString: function() {
                return "igk.winui.eventObjectManager";
            },
            unregister_child: function(q) {
                if (q)
                    $igk(q).select("*").each(function() {
                        this.unregister();
                        return !0;
                    });
            }
        });
        return this;
    };

    function igk_winui_get_event_handler(method) {
        if (typeof(this.eventRegister) == IGK_UNDEF) {
            this.eventRegister = new function() {
                this.m_manager = {};
                igk.appendProperties(this, {
                    getMethod: function(name) {
                        if (typeof(this.m_manager[name]) != IGK_UNDEF) {
                            return this.m_manager[name];
                        }
                        return null;
                    },
                    registerEvent: function(name, obj) {
                        if (typeof(this.m_manager[name]) == IGK_UNDEF) {
                            this.m_manager[name] = obj;
                        }
                    },
                    toString: function() {
                        return "method register ";
                    }
                });
            };
            igk.system.createNS("igk.winui.events", {
                register: this.eventRegister,
                exceptionEvent: new(function() { // for manage exception method event
                    var m_t = {};
                    m_t["mouseleave"] = { replace: "mouseout" };
                    igk.appendProperties(this, {
                        contain: function(m) {
                            return typeof(m_t[m]) != IGK_UNDEF;
                        },
                        getMethod: function(m) {
                            if (typeof(m_t[m]) != IGK_UNDEF) {
                                return m_t[m].replace;
                            }
                            return null;
                        },
                        toString: function() {
                            return "[object igk.winui.events.exceptionEvent]";
                        }
                    });
                })()
            });
        }
        return this.eventRegister.getMethod(method);
    };

    function igk_winui_reg_system_event(item, method, func, useCapture) {
        if (item == null)
            return !1;
        // igk.debug.assert( method=="webkittransitionend","register please handle transitionend");
        if (typeof(item[method]) == igk.constants.undef) {
            if (item["on" + method] + "" == igk.constants.undef) {
                var e = igk.winui.events.exceptionEvent;
                if (e.contain(method)) {
                    return igk_winui_reg_system_event(item, e.getMethod(method), func, useCapture);
                }
                console.debug("/!\\ can't register event on" + method);
                return !1;
            }
        }
        if (item.addEventListener) {
            var t = item.addEventListener(method, func, typeof(useCapture) == 'object' ? useCapture : false);
            igk.winui.getEventObjectManager().register(item, method, func);
            return !0;
        } else if (item.attachEvent) {
            if (igk.DEBUG) {
                console.debug("attachevent " + method);
            }
            if (igk.navigator.IEVersion() == 7) {
                // 
                var ftp = function(evt) {
                    evt.preventDefault = function() {};
                    func.apply(item, [evt]);
                };
                item.attachEvent("on" + method, ftp);
                igk.winui.getEventObjectManager().register(item, method, ftp);
            } else {
                var p = item.attachEvent("on" + method, func);
                igk.winui.getEventObjectManager().register(item, method, func);
                if (method == "igk-trackchange") {
                    // var h=document.createEventObject(window.event);
                    // alert("register on medthod "+item.fireEvent + " p is "+p + "  "+h  + " "+item.attachEvent("onclick",func));
                    // alert(item["onclick"]);
                    // item.fireEvent("ontest",h);
                    // var clickEvent=document.createEventObject(window.event);
                    // clickEvent.button=1;  // left click
                    // item.fireEvent(method,clickEvent);
                    // alert("done");
                    $igk(item).raiseEvent("igk-trackchange");
                }
            }
            return !0;
        } else {
            var m = item["on" + method];
            if (typeof(m) == "function") {
                item["on" + method] = function() {
                    m.apply(this, arguments);
                    func.apply(this, arguments);
                }
            } else {
                item["on" + method] = func;
            }
            igk.winui.getEventObjectManager().register(item, method, func);
        }
        return !0;
    };

    // winui class control management
    (function() {
        var m_class_control = [];
        var m_class_list = null;
        var m_init = 0;
        var m_rg = 0;
        var m_rcg;

        function __initClassControl(n) {
            var q = $igk(this);
            if (q.fn.isClassControl) {
                console.debug("is already a class control " + q.fn.controlName);
                return;
            }
            if (!m_rcg)
                m_rcg = new RegExp("((^| )+)(" + m_rg + ")($| )");
            var t = this.className;
            if (m_rcg.test(t)) {
                var m = m_rcg.exec(t);
                var n = m[3];
                var g = m_class_control[n];
                if (!g) {
                    console.debug('test ' + t);
                    console.debug(m);
                    console.debug(m[2]);
                    return;
                }
                var c = g.func;
                c.apply(q, [n, g.data]);
                q.fn.isClassControl = 1; // mark as controller
                q.fn.ControlName = n; // contorol name
            }
        };
        createNS("igk.winui", {
            getEventHandler: igk_winui_get_event_handler,
            reg_system_event: igk_winui_reg_system_event,
            getEventObjectManager: function() {
                if (__eventObjectManager == null) {
                    __eventObjectManager = new igk_create_event_ojectManager();
                }
                return __eventObjectManager;
            },
            reloadClassList: function() { // reload class list
                m_class_list = null;
                return igk.winui.getClassList();
            },
            getClassList: function() {
                if (m_class_list != null)
                    return m_class_list;
                var t = [];
                var nslist = {};
                for (var i in m_class_control) {
                    t.push(i);
                    t[i] = m_class_control[i];
                    if (m_class_control[i].ns) {
                        if (!nslist[m_class_control[i].ns])
                            nslist[m_class_control[i].ns] = [];
                        nslist[m_class_control[i].ns].push(t[i]);
                    } else {
                        if (!nslist[igk.constants.namespace])
                            nslist[igk.constants.namespace] = [];
                        nslist[igk.constants.namespace][i] = t[i];
                        nslist[igk.constants.namespace].push(t[i]);
                    }
                }
                t["ns://"] = nslist;
                m_class_list = t;
                return t;
            },
            initClassObj: function(n) {
                // n: dom node
                igk_init_node_class_obj.apply(n);
            },
            initClassControl: function(n, c, inf) {
                if (!m_init && igk.ctrl.registerReady) {
                    // register node ready class control
                    m_init = 1;
                    igk.ctrl.registerReady(__initClassControl);
                }
                // n: name
                // c: callback function
                // inf : info
                if (m_class_control[n])
                    return;
                m_class_control[n] = {
                    data: inf || 1,
                    func: c,
                    n: n
                };
                if (m_rg)
                    m_rg += '|' + n;
                else if (m_rg == 0) {
                    m_rg = n;
                } else
                    m_rg += n;
                m_rcg = null; 
            }
        });

        function igk_init_node_class_obj() {
            // init node objet attached by registered class
            var c = this;
            var b = m_class_control;
            var fc = null;
            for (var i in b) {
                fc = b[i].data.func;
                if (fc) {
                    if (igk_item_match_class(b[i].n, c)) {
                        fc.apply($igk(c));
                    } else
                        $igk(c).select("." + b[i].n).each(function() {
                            fc.apply(this);
                            return !0;
                        });
                }
            }
        }
        // igk.ready(igk_init_class_obj);
        // igk.ajx.fn.registerNodeReady(function(){igk_init_node_class_obj.apply(this); });
    })();
    igk_defineProperty(igk, 'version', {
        get: function() { return __version; }
    });
    igk_defineProperty(igk, 'author', {
        get: function() { return __author; }
    });
    // >namespace: igk.android
    createNS("igk.android", {
        init: function(ds) {
            if (igk.navigator.isAndroid()) {
                igk.dom.body().addClass("igk-android");
                var m = igk.createNode("meta");
                m.setAttribute("name", "viewport");
                m.setAttribute("content", ds);
                $igk(document.head).add(m);
            }
        }
    });
    // .msdialog object	
    (function() {
        var m_dlgx = []; // list of opened dialog
        var m_d = 0; // demand for showing a dialog
        function __setupview(div) {
            div.setCss({
                "height": "auto" // auto by default
            }); // .forceview();	
            var _h = div.getHeight();
            var p = -(_h / 2.0);
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
                // p=-t;
                if (pn.getHeight() < div.getHeight()) {
                    h = pn.getHeight() + "px";
                    oflow = true;
                }
            }
            div.setCss({
                "marginTop": p + "px"
            });
            // div.setCss({
            // "height": "auto" // auto by default
            // });		
            // var p=-(div.getHeight() /2.0);
            // var pn=div.getParentNode();
            // var t=0;// to get half position
            // var h=div.getHeight()+"px";
            // var oflow=false;
            // if(pn){
            // t=pn.getHeight()/2.0;
            // }
            // else{
            // t=div.getTop();			
            // }
            // if((t +p)<0)
            // {
            // p=-t;
            // if(pn.getHeight() < div.getHeight())
            // {
            // h=pn.getHeight()+"px";
            // oflow= true;						
            // }
            // }
            // div.setCss({
            // "marginTop": p+"px"
            // });		
            // if(h!=div.getComputedStyle('height'))
            // {
            // ========				
            // div.animate({height: h},{
            // duration:200,
            // interval:10,
            // animtype:"timeout",
            // context:"notify-height-context",
            // effect: "circ",
            // effectmode:"easeinout",
            // complete: function(){
            // if(!oflow){
            // $igk(div).setCss({height: "auto",overflowY:"hidden"});
            // }
            // else {
            // $igk(div).setCss({overflowY: "auto"});
            // }
            // }
            // });
            // }
        }

        function __hide_dialog(q) {
            q.rmClass("igk-show");
            if (q.data["ms-dialog-dispose"]) {
                q.remove();
                m_dlgx.pop();
            }
            if (m_dlgx.length == 0) {
                igk.winui.events.unregKeyPress(__key_press);
            }
            q.data["ms-dialog-init"] = null;
        }

        function __key_press(evt) {
            switch (evt.keyCode) {
                case 27: // escape	
                    console.debug("pop dialog");
                    var q = m_dlgx.pop();
                    if (q)
                        __hide_dialog(q);
                    evt.preventDefault();
                    evt.stopPropagation();
                    return !1;
            }
        }
        // show dialog private function
        function __show_dialog(s) {
            s.addClass("igk-show");

            function __init() {
                // init each dialog
                if (!this.data["ms-dialog-init"]) {
                    var q = this;
                    __setupview(q);
                    q.select(".igk-btn-close").each(function() {
                        var a = this;
                        a.addClass("igk-btn igk-btn-close");
                        a.reg_event("click", function(evt) {
                            evt.stopPropagation();
                            evt.preventDefault();
                            __hide_dialog(q);
                        });
                    });
                    // init ms-dialog
                    this.data["ms-dialog-init"] = 1;
                    if (m_dlgx.length == 0) {
                        igk.winui.events.regKeyPress(__key_press);
                    }
                    m_dlgx.push(q);
                }
                return !0;
            };
            if (s.isSr()) {
                s.each(__init);
            } else {
                __init.apply(s);
            }
        };
        // extend winui for winui dialog support
        createNS("igk.winui", {
            showDialog: function(id) {
                var s = $igk(id);
                if (s != null) {
                    __show_dialog(s);
                }
            },
            hideDialog: function(id) {
                var s = $igk(id);
                if (s != null) {
                    s.each(function() {
                        if (this.hide)
                            this.hide();
                    });
                }
            },
            showDialogUri: function(uri) {
                if (m_d == 1) {
                    return;
                }
                m_d = 1;
                igk.ajx.post(uri, null, function(xhr) {
                    if (this.isReady()) {
                        var n = igk.winui.createDialog();
                        n.add("div").setHtml(xhr.responseText);
                        igk.dom.body().appendChild(n);
                        __show_dialog(n);
                        m_d = 0;
                    }
                });
            },
            createDialog: function(id, data) {
                var n = igk.createNode("div");
                n.setAttribute("id", id);
                n.addClass("igk-ms-dialog");
                var a = n.add("a").setAttribute("href", "#")
                    .addClass("igk-btn-close");
                a.setHtml(igk.R.btn_close);
                a.on("click", function() {
                    n.remove();
                });
                n.data["ms-dialog-dispose"] = 1;
                if (data) {
                    n.add(data);
                }
                n.init();
                return n;
            }
        });
    })();
    // manage keyPress	- keyUp - define usage key code
    (function() {
        var _keyns = createNS("igk.winui.inputKeys", {});
        var _keys = {
            Enter: 13,
            Escape: 27,
            Left: 37,
            Up: 38,
            Right: 39,
            Down: 40
        };

        function _defkey(k) {
            return function() {
                return _keys[k];
            };
        };

        function _getCode(event) {
            return event.charCode || event.keyCode;
        };
        for (var k in _keys) {
            igk_defineProperty(_keyns, k, { get: _defkey(k) });
        }
        //register key event
        createNS("igk.winui", {
            keyPress: function(event, code, callback) {
                var c = _getCode(event);
                if (c == code) {
                    callback(event);
                    event.preventDefault();
                    event.stopPropagation();
                }
            },
            keyUp: function(event, code, callback) {
                var c = _getCode(event);
                if (c == code) {
                    callback(event);
                    event.preventDefault();
                    event.stopPropagation();
                }
            }
        });
    })();
    // Manage printing service
    (function() {
        var m_ptrframe = null;
        createNS("igk.winui", {
            openUrl: function(u, n, p) {
                var hwn = window.open(u, n, p);
                return hwn;
            },
            print: function(u) {
                var _prf = document.domain + "::/prf";
                var _n = (window[_prf] == null); // check for new window
                var _wnd = window[_prf] || window.open(u, _prf, "fullscreen=0, toolbar=0,resizable=0,menubar=0,title=0, width=420, height=500, left=-9999, top=0");
                var _el = igk_winui_reg_event;
                var _echain = null;
                var _tchain = null;
                var _P = {
                    resolve: function() {},
                    then: function(callback) {
                        if (_tchain == null)
                            _tchain = callback;
                        else
                            _tchain = function() {
                                _tchain.apply(_P);
                                callback.apply(_P);
                            };
                        return this;
                    },
                    error: function(callback) {
                        if (_echain == null)
                            _echain = callback;
                        else
                            _echain = function() {
                                _echain.apply(_P);
                                _echain.apply(_P);
                            };
                        return this;
                    }
                };
                if (_wnd) {
                    try {
                        if (_n) {
                            _el(_wnd, 'load', function() {
                                if (_wnd.hide)
                                    _wnd.hide();
                                _wnd.print();
                                // chrome need the control to still be hopen
                                if (!igk.navigator.isChrome())
                                    _wnd.close();
                            });
                        } else {
                            // refresh location
                            _wnd.location = u;
                        }
                    } catch (ex) {
                        console.debug("[igk.winui.print] - can't register load event  - " + ex);
                    }
                }
                return _P;
            },
            printUrl: function(u) {
                // print url 
                if (m_ptrframe == null) {
                    m_ptrframe = igk.createNode("iframe");
                    m_ptrframe.reg_event("load", function() {
                        m_ptrframe.o.contentWindow.focus();
                        m_ptrframe.o.contentWindow.print();
                    });
                    m_ptrframe.addClass("dispn");
                    igk.dom.body().appendChild(m_ptrframe.o);
                }
                m_ptrframe.o["href"] = u;
                m_ptrframe.o["src"] = u;
            },
            confirm: function(t, m, callback) { // confirm dialog title message
                var div = igk.createNode("div");
                div.setCss({ maxWidth: "480px", margin: "auto" });
                div.add("div").setHtml(m);
                var b = div.add("div");
                b.addClass("igk-action-bar igk-pull-right").setCss({ backgroundColor: "transparent" });
                var btn = b.add("input").setAttributes({ type: 'button', value: 'confirm' }).addClass("igk-btn");
                btn.reg_event("click", function(evt) {
                    evt.preventDefault();
                    if (callback)
                        callback.apply(this, evt);
                    igk.winui.notify.close();
                });
                igk.winui.notify.showMsBox(t, div);
            }
        });
    })();
    // utily winui functions
    (function() {
        createNS("igk.winui", {
            'click': function(u) {
                return function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    var a = document.createElement("a");
                    a.href = u;
                    igk.dom.body().appendChild(a);
                    $igk(a).click();
                    // .remove();
                };
            }
        });
    })();
    (function() { // mouse button utility
        var Left = 1;
        var Right = 2;
        var Middle = 3;
        var None = 0;

        function getButton(t) {
            switch (t) {
                case 0:
                    return Left;
                case 1:
                    return Middle;
                case 2:
                    return Right;
            }
            return None;
        };
        var tev = {};
        tev["mousemove"] = function(e) {
            if ((e.buttons == e.button) && (e.button == 0)) {
                return None;
            }
            switch (e.buttons) {
                case 1:
                    return Left;
                case 4:
                    return Middle;
                case 2:
                    return Right;
            }
            return getButton(e.button);
        };
        tev["mousedown"] = function(e) {
            return getButton(e.button);
        };
        tev["mouseup"] = function(e) {
            if ((e.buttons == e.button) && (e.button == 0)) {
                return Left;
            }
            return getButton(e.button);
        };
        igk.winui.mouseButton = function(evt) {
            //if (evt.buttons == 0)
            //return None;
            // for most browser
            if (igk.navigator.isIE() && igk.navigator.IEVersion() < 10) {
                switch (evt.button) {
                    case 1:
                        return Left;
                    case 4:
                        return Middle;
                    case 2:
                        return Right;
                }
            } else {
                if (evt.type in tev)
                    return tev[evt.type].apply(null, [evt]);
                switch (evt.button) {
                    case 0:
                        if ((evt.buttons == 0) && (evt.type == "mousemove")) {
                            return None;
                        }
                        return Left;
                    case 1:
                        return Middle;
                    case 2:
                        return Right;
                }
            }
            return None;
        };
        igk_defineProperty(igk.winui.mouseButton, "Left", { get: function() { return Left; } });
        igk_defineProperty(igk.winui.mouseButton, "Right", { get: function() { return Right; } });
        igk_defineProperty(igk.winui.mouseButton, "Middle", { get: function() { return Middle; } });
        igk_defineProperty(igk.winui.mouseButton, "None", { get: function() { return None; } });
        // empty name space
        createNS("igk.winui.mouseButton", {});
        // event utilitiy
        igk.winui.eventTarget = function(evt) {
            if (evt.target)
                return evt.target;
            if (evt.srcElement)
                return evt.srcElement;
            return null;
        };
        igk.winui.headDocument = function() {
            if (document.head)
                return document.head;
            document.head = document.getElementsByTagName("head")[0];
            return document.head;
        };
    })();
    // dialog manager
    (function() {
        var dialogs = [];
        var init = 0;

        function HDialog(d) {
            var self = this;
            this.closeDialog = function() {
                var q = 0;
                while (q = dialogs.pop()) {
                    d.unreg_event(q.closeDialog);
                    if (q == d) {
                        return 1;
                    }
                }
                init = 0;
                igk.winui.events.unregKeyPress(__close_dialog);
                return 0;
            };
        };

        function __close_dialog(e) {
            if ((dialogs.length > 0) && (e.type == 'keyup') && (e.keyCode == igk.winui.inputKeys.Escape)) {
                dialogs[dialogs.length - 1].close();
                e.preventDefault();
                e.stopPropagation();
            }
        };
        igk.system.createNS("igk.winui.dialogs", {
            register: function(d) {
                var _d = new HDialog(d);
                d.on("close", _d.closeDialog);
                dialogs.push(d);
                if (!init) {
                    igk.winui.events.regKeyPress(__close_dialog);
                    init = 1;
                }
            },
            getlastDialog: function() {
                if (dialogs.length > 0) {
                    return dialogs[dialogs.length - 1];
                }
                return null;
            },
            isOpen: function() {
                return dialogs.length > 0;
            }
        });
    })();
    // balafon form builder engine
    (function() {
        var engines = {};
        var default_e = null;
        igk.system.createNS("igk.winui.engine", {
            register: function(n, o) {
                engines[n] = o;
            },
            setDefaultEngine: function(n) {
                default_e = n;
            },
            getEngine: function(n, t, R) {
                //@R: asset manager for lang support
                n = n || default_e;
                var s = null;
                if (n) {
                    if (n in engines) {
                        s = engines[n];
                        if (t) {
                            s.host = t;
                        }
                        if (R)
                            s.setAsset(R);
                        return s;
                    }
                }
                if (default_e && (n != default_e) && (default_e in engines)) {
                    s = engines[default_e];
                    if (t)
                        s.host = t;
                    if (R)
                        s.setAsset(R);
                    return s;
                }
                return null;
            },
            getEngineNameList: function() {
                var t = [];
                for (var i in engines)
                    t.push(i);
                t.sort();
                return t;
            },
            formBuilderEngine: function() {
                //form builder engine constructor
                var host;
                var R = null;
                igk.defineProperty(this, "host", {
                    get: function() {
                        return host;
                    },
                    set: function(v) {
                        host = $igk(v);
                        if (host.o && !host.engine) {
                            host.engine = new hostEngine(this);
                        }
                    }
                });
                this.setAsset = function(r) {
                    R = r;
                };

                function _getRes(id) {
                    return (R ? R[id] : null) || id;
                };
                this.getRes = _getRes;
                this.add = function(t) {
                    this.host.add(t);
                    return this;
                };
            }
        });

        function hostEngine() {
            var m_items = {};
            this.getItem = function(n) {
                if (n in m_items)
                    return m_items[n];
                return null;
            };
            this.register = function(n, h) {
                m_items[n] = h;
            };
            this.clear = function(n, h) {
                m_items = {};
            };
            this.getFields = function() {
                return m_items;
            };
        };
    })();
    (function() {
        var m_capture;
        var m_r = false;
        var m_event_capture = false;
        var m_bck = {};

        function __capturemouse(evt) {
            if (m_r) {
                evt.stopPropagation();
                return;
            }
            if (m_capture && m_event_capture) {
                var t = null;
                if (typeof(evt.constructor) == 'function') {
                    t = new evt.constructor(evt.type, evt);
                } else {
                    // ie mouse event
                    if (/mouse/.test(evt.type)) {
                        t = document.createEvent("MouseEvents");
                        t.initMouseEvent(evt.type, evt.bubbles, evt.cancelable, evt.view, evt.detail, evt.screenX, evt.screenY, evt.clientX, evt.clientY, evt.ctrlKey, evt.altKey, evt.shiftKey, evt.metaKey, evt.button, null);
                    }
                    if (!t)
                        return;
                }
                t.EventTarget = m_capture;
                t["igk-event-source"] = "mouse-capture";
                m_r = !0;
                m_capture.dispatchEvent(t);
            }
            evt.stopPropagation();
            m_r = false;
        };
        // mouse capture definition : chrome do not implement capture specification
        igk.system.createNS("igk.winui.mouseCapture", {
            getCapture: function() { //return the current capture object reference
                return m_capture;
            },
            setCapture: function(n, i) { // set the current capture reference
                // n: target node that handle and capture the mouse. exemple in colorpicker.js script
                if ((n != null) && (m_capture != n)) {
                    m_capture = n;
                    let fc = n.setPointerCapture || n.setCapture;
                    if (fc) {
                        fc.apply(n, [i]);
                    } else {
                        //chrome not supporting set capture
                        m_event_capture = true;
                        igk_winui_reg_event(window, "mousemove", __capturemouse);
                        igk_winui_reg_event(window, "mouseup", __capturemouse);
                        // to correct chrome behaviour
                        m_bck["ondragstart"] = m_capture["ondragstart"];
                        m_bck["ondrop"] = m_capture["ondrop"];
                        m_capture["ondragstart"] = igk.winui.fn.cancelEventArgs;
                        m_capture["ondrop"] = igk.winui.fn.cancelEventArgs;
                        igk.dom.body().setCss({ pointerEvents: 'none' });
                        $igk(n).setCss({ pointerEvents: 'auto' });
                    }
                }
            },
            releaseCapture: function(e) { // free capture
                if (!m_capture) {
                    return;
                }
                if (m_capture.setCapture) {
                    if (m_capture.setPointerCapture) {
                        m_capture.setPointerCapture(null);
                    } else {
                        if (m_capture.setCapture) {
                            m_capture.setCapture(null);
                        }
                    }
                }
                if (m_capture.releasePointerCapture) {
                    m_capture.releasePointerCapture(e);
                } else {
                    if (document.releaseCapture)
                        document.releaseCapture();
                }
                if (m_event_capture) {
                    igk.winui.unreg_event(window, "mousemove", __capturemouse);
                    igk.winui.unreg_event(window, "mouseup", __capturemouse);
                    m_capture["ondragstart"] = m_bck["ondragstart"];
                    m_capture["ondrop"] = m_bck["ondrop"];
                    igk.dom.body().setCss({ pointerEvents: 'auto' });
                }
                m_capture = null;
                m_event_capture = false;
            }
        })
    })();
    (function() {
        createNS("igk.winui.history", {
            push: function(uri, data, t) {
                if (typeof(window.history.pushState) == "function") {
                    window.history.pushState(data, t, uri);
                } else {
                    console.debug("no history state available");
                }
            },
            replace: function(uri, data) {
                if (typeof(window.history.replaceState) == "function") {
                    window.history.replaceState(data, null, uri);
                } else {
                    console.debug("no history state available");
                }
            }
        });

        function __popstate(e) {
            e.preventDefault();
            var u = 0;
            if (e.state) { // safari pop that at first state
                // if ( e.state.src=='balafonjs'){
                // }else{
                // u=e.state.uri;
                // if(u){
                // igk.ajx.get(u,null,igk.ajx.fn.append_to_body);
                // }else{
                // }
            }
            // else{
            // u=(document.location.href+"").split('?')[0];
            // if(u){
            // u+="/default/goback";
            // igk.ajx.get(u,null,igk.ajx.fn.append_to_body);
            // }
            // }
        };
        if ('onpopstate' in window)
            igk_winui_reg_event(window, "popstate", __popstate);
    })();
    (function() {
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
                getTarget: function() { return m_target; },
                load: function(d) {
                    m_target.setHtml(null);
                    var dummy = igk.createNode("div");
                    dummy.setHtml(d);
                    __loadItemTo(m_target, dummy);
                },
                close: function() {
                    // unreg event
                    igk.winui.unreg_event(document, "click", __click);
                    igk.qselect(".overflow-y-a").unreg_event("scroll", __scroll);
                    var q = m_target;
                    m_target.addClass("igk-trans-all-200ms").setCss({ "opacity": 0.0 }).timeOut(400,
                        function() {
                            m_target.rmClass("igk-trans-all-200ms igk-show");
                            q.o.parentNode.removeChild(q.o);
                            q.clearTimeOut();
                        }
                    );
                },
                show: function(t, c, l) {
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
                    }).addClass("igk-trans-all-200ms").setCss({ opacity: 1 }).timeOut(400, function() {
                        m_target.rmClass("igk-trans-all-200ms");
                    });
                    // register click 
                    igk_winui_reg_event(document, "click", __click);
                    // register scroll
                    igk.qselect(".overflow-y-a").reg_event("scroll", __scroll);
                },
                toString: function() {
                    return "igk.winui.contextmenu";
                }
            });
        };
        // init global ctx menu
        m_contextMenu = new __ctr();
        // define global context menu property
        igk_defineProperty(igk.winui, 'contextMenu', {
            get: function() { return m_contextMenu; },
            nopropfunc: function() { this.contextMenu = m_contextMenu; }
        });
        igk.winui.initClassControl("igk-context-menu", function() {
            // init all system class menu
            var id = $igk(this.getAttribute("igk:for"));
            if (!id)
                return;
            var q = this;
            var v = 0;
            q.close = function() {
                igk.winui.unreg_event(document, "click", __q_click);
                igk.qselect(".overflow-y-a").unreg_event("scroll", __q_scroll);
                q.addClass("igk-trans-all-200ms").setCss({ "opacity": 0.0 }).timeOut(400,
                    function() {
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
            q.show = function() {
                q.addClass("posfix igk-show").addClass("igk-trans-all-200ms").setCss({ opacity: 1 }).timeOut(400, function() {
                    q.rmClass("igk-trans-all-200ms");
                });
                // reg event
                igk_winui_reg_event(document, "click", __q_click);
                igk.qselect(".overflow-y-a").reg_event("scroll", __q_scroll);
            };
            $igk(id).reg_event("click", function(evt) {
                if (v == 0) {
                    q.show();
                    evt.preventDefault();
                    evt.stopPropagation();
                    v = 1;
                }
            }).setCss({ "cursor": "pointer" });
        }, { desc: "igk context menu" });
    })();
    // notify item controller
    igk.system.createNS("igk.winui.notifyctrl", {
        init: function(t) {
            igk.ready(function() { new igk.winui.notifyctrl.run(t).start(); });
        },
        run: function(target) {
            this.target = $igk(target);
            this.noautohide = this.target.getAttribute("igk-no-auto-hide");
            var q = this;
            this.start = function() {
                var self = this;
                if (q.noautohide == 1)
                    return;
                setTimeout(function() {
                    igk.animation.fadeout(self.target.o, 20, 500, 1.0, function() {
                        self.target.setCss({ "display": "none" }).remove();
                    });
                }, 2000);
            };
        }
    });
    createNS("igk.winui.events", {
        stopPropagation: function(e) {
            if ((e != null) && (e.stopPropagation)) {
                e.stopPropagation();
            }
        },
        preventDefault: function(e) {
            if (e) {
                if (e.preventDefault)
                    e.preventDefault();
                // no preventDefault function found
            }
        },
        cancelBehaviour: function(e) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
    (function() {
        var m = [];
        createNS("igk.winui.events.fn", { // utility event namespace 
            handleKeypressExit: function(p) {
                m.push(p);
                var fc = function(evt) {
                    switch (evt.keyCode) {
                        case 27: // escape
                            console.debug("close frame");
                            if (igk.winui.framebox.currentFrame == null) {
                                var t = m.pop();
                                if (p.complete) {
                                    p.complete.apply(this, [evt]);
                                    igk.winui.unreg_event(document, "keypress", fc);
                                }
                            }
                            break;
                    }
                };
                return fc;
            }
        });
    })();
    igk.winui.controlUtils = {
        HasChildContainPoint: function(item, point) {
            // >item: DomNode
            // >item: offsets point
            var loc = igk.winui.GetRealScreenPosition(item);
            var o = ((point.x >= 0.0) && (point.x <= item.clientWidth) && (point.y >= 0.0) && (point.y <= item.clientHeight));
            return o;
        }
    };
    createNS("igk.animation", {
        init: function(o, interval, duration, initcallbackfunc, updatecallbackfunc, endcallbackfunc, properties) {
            var m_timeout = null; // time out validate
            var m_updatecallback = null;
            var m_o = o;
            var m_interval = interval;
            var m_duration = duration;
            var m_updatecallback = updatecallbackfunc;
            var m_initcallback = initcallbackfunc;
            var m_endcallback = endcallbackfunc;
            var m_ellapsed = 0.0;
            var m_properties = properties; // additional properties
            var m_opupdate = null;
            // anim.init instance constructor
            function __animInstance() {
                this.getOwner = function() { return m_o; };
                this.getInterval = function() { return m_interval; };
                this.getDuration = function() { return m_duration; };
                this.getEllapsed = function() { return m_ellapsed; };
                this.getStepfactor = function() {
                    if (m_opupdate) {
                        var v_delta = Math.round(m_opupdate(m_ellapsed / m_duration) * 100) / 100;
                        return v_delta;
                    }
                    return this.getEllapsed() / this.getDuration();
                };
                this.start = function() {
                    m_initcallback.apply(this);
                    m_opupdate = igk.animation.getEffect(this.properties.effect, this.properties.effectmode);
                    this.update();
                };
                this.update = function() {
                    var self = this;
                    m_ellapsed += m_interval;
                    if ((m_ellapsed <= m_duration) && (m_updatecallback.apply(this))) {
                        // continue
                        m_timeout = setTimeout(function() {
                            self.update();
                        }, this.getInterval());
                    } else {
                        // call end callback
                        m_endcallback.apply(this);
                    }
                };
                this.stop = function() {
                    if (m_timeout) {
                        igk.clearTimeout(m_timeout);
                        m_timeout = null;
                    }
                    m_ellapsed = 0;
                };
                this.reset = function() {
                    this.stop();
                    m_initcallback.apply(this);
                };
                this.toString = function() { return "igk.animation.instance"; };
            };
            return new __animInstance();
        },
        InitBgAdorator: function(element, frequency, interval, fadeinterval, israndom, getimguri, defindex, updateuri) {
            // alert(interval);
            // interval=3000;
            var q = new igk.animation.adorator(element, frequency, interval, fadeinterval, israndom);
            igk.ajx.get(getimguri, null,
                function(xhr) {
                    if (this.isReady()) {
                        var d = igk.createNode('datas');
                        d.setHtml(xhr.responseText);
                        var tt = d.getElementsByTagName('igk-img');
                        d.select('igk-img').each_all(function() {
                            q.add(this.getAttribute("src"));
                        });
                        // q.loadImages(tt);
                        setTimeout(function() {
                                q.start(function() { igk.ajx.get(updateuri + q.index, null, null); });
                            },
                            400);
                    }
                }
            );
        },
        // background image adorator
        adorator: function(element, frequency, interval, fadeinterval, israndom) {
            if (element == null)
                throw "element is null";
            this.element = element;
            igk.initprop(this.element);
            this.frequency = frequency;
            this.interval = interval;
            this.fadeinterval = fadeinterval;
            this.israndom = israndom;
            this.images = new Array();
            this.index = 0;
            this.lasttimeout = null;
            this.defaultset = false;
            this.muststart = false;
            this.setDefaultIndex = function(index) {
                if (this.images) {
                    if ((index >= 0) && (index < this.images.length)) {
                        this.index = index;
                    }
                    this.defaultset = !0;
                }
            };
            this.toString = function() { return "igk.animation.adorator"; };
            this._setStyle = function(e) {
                e.style.width = "100%";
                e.style.height = "100%";
                // e.style.minHeight="768px";
                // e.style.minWidth="1024px";
                e.style.position = "absolute";
                e.style.top = "0px";
                e.style.left = "0px";
                e.style.display = "block";
                e.className = "igk-bgadorator-block";
            };
            this.loadImages = function(imgs) {
                function __regimage(item, e) {
                    item._setStyle(e.o);
                    // igk.initprop(e.o);
                    item.images[item.images.length] = e;
                    e.parentNode.removeChild(e);
                };
                var truncated = false;
                if (imgs.length > 0) {
                    for (var i = 0; i < imgs.length; i++) {
                        // check for complete
                        if (!imgs[i].complete) {
                            var self = this;
                            var timgs = imgs;
                            if (self.loaded) {
                                igk.clearTimeout(self.loaded);
                            }
                            // recall function and check that all image are loaded completly
                            self.loaded = setTimeout(function() { self.loadImages(timgs); }, 400);
                            truncated = !0;
                            return;
                        }
                    }
                }
                if (!truncated) {
                    var e = null;
                    this.images = new Array();
                    var self = this;
                    var c = imgs.length;
                    // copy images
                    for (var i = 0; i < c; i++) {
                        e = imgs[0];
                        if (e) {
                            __regimage(self, e);
                        }
                    }
                    if (this.muststart) {
                        this.start();
                    }
                } else {
                    console.error("Some Image(s) truncated");
                }
            };
            this.add = function(uri) {
                var e = igk.createNode("img");
                e.o.src = uri;
                this._setStyle(e.o);
                this.images[this.images.length] = e;
            };
            this._init = function() {
                if ((!this.images) && (this.images.length <= 0))
                    return;
                var t = this.element.getElementsByTagName("img");
                var e = null;
                if (t.length > 0) {
                    e = t[0];
                } else {
                    this.element.insertBefore(this.images[0], this.element.childNodes[0]);
                    e = this.images[0];
                    igk.animation.fadein(e, this.frequency, this.fadeinterval);
                }
            };
            this.start = function(notifyfunction) {
                if ((!this.images) || (this.images.length <= 0)) {
                    this.notifyfunction = notifyfunction;
                    this.muststart = !0;
                    return;
                }
                var t = this.element.getElementsByTagName("img");
                var e = null;
                if (t.length > 0) {
                    e = t[0];
                } else {
                    var v_re = this.images[0].o;
                    var v_ce = this.element.childNodes[0];
                    if ((v_re != null) && (v_ce != null)) {
                        this.element.insertBefore(v_re, v_ce);
                    }
                    e = this.images[0].o;
                    igk.animation.fadein(e, this.frequency, this.fadeinterval);
                }
                var d = !this.defaultset;
                this.notifyfunction = notifyfunction;
                if (!this.defaultset) {
                    this.setDefaultIndex(Math.floor((Math.random() * this.images.length)));
                    if (this.notifyfunction) {
                        this.notifyfunction();
                    }
                }
                var img = this.images[this.index].o;
                if (img && e && (img !== e)) {
                    this.element.replaceChild(img, e);
                    // if(d)
                    igk.animation.fadein(img, this.frequency, this.fadeinterval);
                }
                var self = this;
                if (this.lasttimeout) {
                    igk.clearTimeout(this.lasttimeout);
                    this.lasttimeout = null;
                }
                this.lasttimeout = setTimeout(function() { self.update(); }, this.interval);
                this.muststart = false;
            };
            this.update = function() {
                var self = this;
                var i = 0;
                if (this.israndom) {
                    i = Math.floor((Math.random() * this.images.length));
                } else {
                    i = ((this.index + 1) % this.images.length);
                }
                if (i == this.index)
                    return;
                this.index = i;
                // get image to replace
                var e = this.element.getElementsByTagName("img")[0];
                var img = null;
                if (typeof(e) == IGK_UNDEF) {
                    // backerror;
                } else {
                    img = this.images[this.index];
                    if (img == null) {
                        throw "element is null at " + this.index;
                    }
                    if (e != img.o) {
                        try {
                            if (img)
                                $igk(e).insertAfter(img);
                            if (e)
                                igk.animation.fadeout(e, this.frequency, this.fadeinterval, 1, function() { if (e.parentNode) { e.parentNode.removeChild(e); } });
                            if (img)
                                igk.animation.fadein(img, this.frequency, this.fadeinterval);
                        } catch (ex) {
                            igk.show_notify_error("Exception",
                                "Exception__adorator__ : index=" + this.index + " <br />" + ex +
                                "<p class=\"igk-trace\">" + ex.stack + "</p>");
                        }
                    }
                    this.lasttimeout = setTimeout(function() { self.update(); }, this.interval);
                    if (this.notifyfunction) {
                        this.notifyfunction();
                    }
                }
            };
            this.stop = function() {
                if (this.lasttimeout) {
                    igk.clearTimeout(this.lasttimeout);
                    this.lasttimeout = null;
                }
            };
        },
        // init animation context 
        // >@@ style: fadein fadeout
        // every animation context must have a unique style name in order to be retreive or replace on the animation system
        context: function(element, style, duration, updatetype) {
            if (element == null)
                throw "Context failed : Operation Not Allowed element is null";
            // reg primary context. 
            function __regAnimationContext(context, item, style) {
                var i = null;
                if (typeof(item.igk.animationContext) == igk.constants.undef) {
                    // create a new object of the animation context
                    i = new __primarycontext(element, style);
                    // register new context
                    item.igk.animationContext = new function() {
                        var m_contexts = [];
                        igk.appendProperties(this, {
                            pushContext: function(style, context) {
                                m_contexts[style] = context;
                            },
                            getContext: function(style) {
                                if (typeof(m_contexts[style]) == IGK_UNDEF) {
                                    return m_contexts[style];
                                }
                                return null;
                            }
                        });
                    };
                    item.igk.animationContext.pushContext(style, i);
                } else {
                    // get the init animation context
                    i = item.igk.animationContext.getContext(style);
                    if (i == null) { // register and push animation context
                        i = new __primarycontext(element, style);
                        item.igk.animationContext.pushContext(style, i);
                    }
                }
                return i;
            }
            // 	primarycontext object
            function __primarycontext(element, style) {
                this.element = element;
                this.style = style;
                this.interval = 20;
                this.duration = duration ? duration : 1000;
                this.updatetype = updatetype || "timeout"; // timeout or interval
                this.step = 0.1;
                this.ellapsed = 0.0;
                this.opacity = 1.0;
                this.endopacity = 1.0;
                this.callback = null;
                this.toString = function() { return "igk.animation.context"; };
                this.start = function() { // start animation function
                    switch (this.style) {
                        case "fadein":
                        case "fadeout":
                            this.element.igk.setOpacity(this.opacity);
                            break;
                    }
                    this.step = this.interval / this.duration;
                    var q = this;
                    if (this.updatetype == "timeout") {
                        this.lasttimeout = setTimeout(function() { q.update(); }, this.interval);
                    } else {
                        // register interval function
                        this.lasttimeout = setInterval(function() {
                            q.update();
                            if (q.end) {
                                clearInterval(this.lasttimeout);
                            }
                        }, this.interval);
                    }
                };
                this.update = function() {
                    var end = false;
                    switch (this.style) {
                        case 'fadein':
                            this.opacity += this.step;
                            if (this.opacity > this.endopacity) {
                                this.opacity = this.endopacity;
                                end = !0;
                            }
                            break;
                        case 'fadeout':
                            this.opacity -= this.step;
                            if (this.opacity < this.endopacity) {
                                this.opacity = this.endopacity;
                                end = !0;
                            }
                            break;
                    }
                    this.element.igk.setOpacity(this.opacity);
                    var q = this;
                    if (!end) {
                        if (this.lasttimeout) {
                            igk.clearTimeout(this.lasttimeout);
                        }
                        this.lasttimeout = setTimeout(function() { q.update(); }, this.interval);
                    } else {
                        this.stop();
                        if (this.callback) {
                            this.callback();
                        }
                    }
                };
                this.stop = function() {
                    if (this.updatetype == "timeout") {
                        if (this.lasttimeout) {
                            igk.clearTimeout(this.lasttimeout);
                            this.lasttimeout = null;
                        }
                    } else {
                        if (this.lasttimeout) {
                            clearInterval(this.lasttimeout);
                            this.lasttimeout = null;
                        }
                    }
                };
            };;
            return __regAnimationContext(this, element, style);
        },
        fadecontext: function(element, style, duration) {
            if (element == null)
                throw "Context failed : Operation Not Allowed element is null";
            igk.animation.context.apply(this, new Array(element, style, duration));
        },
        // >@@ static function
        // >@@ element,refresh interval ,duration of the effect,default opacity,fonction to call on complete
        fadein: function(element, interval, duration, opacity, callback) {
            if (element == null) return;
            var c = igk.animation.context($igk(element).o, 'fadein');
            c.stop();
            c.interval = interval;
            c.duration = duration ? duration : 1000;
            if (typeof(opacity) == "object") {
                c.opacity = opacity.from ? opacity.from : 0;
                c.endopacity = opacity.to ? opacity.to : 1;
            } else {
                c.opacity = opacity ? opacity : 0;
                c.endopacity = 1;
            }
            c.callback = callback;
            c.start();
            return c;
        },
        // >@@ static function
        // >@@ element ,refresh interval,duration of the effect,default opacity,call back function ,endopacity
        fadeout: function(element, interval, duration, opacity, callback) {
            if (element == null)
                return;
            var op = 1;
            var ep = 0;
            if (typeof(opacity) == "object") {
                op = opacity.from ? opacity.from : 1;
                ep = opacity.to ? opacity.to : 0;
            } else {
                op = opacity ? opacity : 1;
                ep = 0;
            }
            if ((callback == null) && $igk(element).isCssSupportTransition()) {
                $igk(element)
                    .setCss({ opacity: op })
                    .addClass("igk-transition-easeinout")
                    .setTransitionDuration(((duration ? duration : 1000) / 1000.0) + 's')
                    .setCss({ opacity: ep });
                return;
            }
            var d = duration ? duration : 1000;
            var c = igk.animation.context($igk(element).o, 'fadeout', d);
            c.stop();
            c.interval = interval;
            c.duration = d;
            c.opacity = op;
            c.endopacity = ep;
            c.callback = callback;
            c.start();
            return c;
        },
        toString: function() {
            return "igk.animation";
        },
        autohide: function(item, startat) {
            setTimeout(function() {
                    igk.animation.fadeout(item, 20, 500, 1.0, function() {
                        $igk(item).setCss({ "display": "none" });
                        $igk(item).remove();
                    });
                },
                startat);
        },
        // >@@ animate properties
        animate: function(element, properties, animinfo) {
            if (element == null) {
                return;
            }
            var contextname = igk_getv(animinfo, "context", 'animate');
            var v_animtype = igk_getv(animinfo, "animtype", 'timeout'); // timeout or inteval
            var v_duration = animinfo ? (animinfo.duration ? animinfo.duration : 1000) : 1000;
            var v_animeffect = igk_getv(animinfo, "effect", 'linear'); // // easein,easeout,easeinout
            var v_animeffectmode = igk_getv(animinfo, "effectmode", 'easein');
            var v_update = igk_getv(animinfo, "update", null);
            var v_complete = igk_getv(animinfo, "complete", null);
            // if((v_complete==null) && $igk(element).isCssSupportTransition())
            // {
            // $igk(element).addClass("igk-transition-"+v_animeffectmode).setCss(properties);
            // return;
            // }
            var c = igk.animation.context($igk(element).o, contextname, v_duration, v_animtype);
            // stop the previous animation context
            c.stop();
            // setup animation properties
            c.interval = animinfo ? (animinfo.interval ? animinfo.interval : 20) : 20;
            c.callback = animinfo ? (animinfo.complete || null) : null;
            c.updatecallback = animinfo ? (animinfo.update || null) : null;
            var k = new function() {
                this.time = 0; // maintain time
                this.steps = new Array(); // step info of the moved item	
            };
            var s = null;
            var m = null;
            var step = null;
            var out_func = igk.animation.getEffect(v_animeffect, v_animeffectmode);
            for (var i in properties) {
                switch ((i + "").toLowerCase()) {
                    case "textshadow":
                        s = window.igk.system.styles.textShadowCreate($igk(element).getComputedStyle(i));
                        m = window.igk.system.styles.textShadowCreate(properties[i]);
                        break;
                    case "color":
                    case "backgroundcolor":
                    case "bordercolor":
                        {
                            s = window.igk.system.colorFromString($igk(element).getComputedStyle(i));
                            m = window.igk.system.colorFromString(properties[i]);
                            k.steps[i] = {
                                step: {
                                    // calculate the distance of the 2 animation
                                    r: (m.r - s.r), // *(c.interval/ c.duration),
                                    g: (m.g - s.g), // *(c.interval/ c.duration),
                                    b: (m.b - s.b), // *(c.interval/ c.duration)
                                    toString: function() { return "(" + this.r + "," + this.g + "," + this.b + ")"; }
                                },
                                start: s,
                                end: m,
                                pos: { r: 0, g: 0, b: 0 },
                                name: i,
                                unit: igk_getUnit(m)
                            };
                        }
                        break;
                    default:
                        s = igk_getNumber($igk(element).getComputedStyle(i));
                        m = igk_getNumber(properties[i], $igk(element), i);
                        k.steps[i] = {
                            // get distance
                            step: (m - s), // (c.interval/ c.duration),
                            start: s,
                            end: m,
                            pos: 0,
                            name: i,
                            unit: igk_getUnit(m)
                        };
                }
            }
            // replace update
            c.update = function() {
                var q = this;
                var end = false;
                var v_delta = 0.0;
                k.time += q.interval; // update the time		
                // calculate the delta
                v_delta = Math.round(out_func(k.time / q.duration) * 100) / 100;
                for (var i in k.steps) {
                    if ((i == "length"))
                        continue;
                    var h = k.steps[i];
                    if ((typeof(h.step) == 'undefined') || (h.step == 0))
                        continue;
                    var item = {};
                    var v = 0;
                    var key = i.toLowerCase();
                    switch (key) {
                        case "opacity": // float value
                            h.pos = v_delta * h.step;
                            v = (h.start + h.pos);
                            break;
                        case "color":
                        case "backgroundcolor":
                        case "bordercolor":
                            h.pos.r = (v_delta * h.step.r);
                            h.pos.g = (v_delta * h.step.g);
                            h.pos.b = (v_delta * h.step.b);
                            // v=h.start;
                            var r = parseInt(h.start.r + h.pos.r);
                            var g = parseInt(h.start.g + h.pos.g);
                            var b = parseInt(h.start.b + h.pos.b);
                            v = new igk.system.color(r, g, b);
                            v = v.toHtml();
                            break;
                        default:
                            // h.pos +=h.step;
                            h.pos = v_delta * (h.end - h.start);
                            v = parseInt((h.start + h.pos)) + "" + h.unit;
                            break;
                    }
                    item[i] = v;
                    $igk(this.element).setCss(item);
                }
                if (this.updatecallback) {
                    this.ellapsed = k.time;
                    this.updatecallback.apply(this);
                }
                end = k.time >= q.duration;
                if (!end) {
                    if (q.updatetype == "timeout") {
                        if (this.lasttimeout) {
                            igk.clearTimeout(this.lasttimeout);
                        }
                        this.lasttimeout = setTimeout(function() { q.update(); }, this.interval);
                    }
                } else {
                    // end ;								
                    $igk(this.element).setCss(properties);
                    this.stop();
                    if (this.callback) {
                        this.callback();
                    }
                }
                this.end = end;
            };
            c.start();
            return c;
        },
        animateUpdate: function(element, animinfo) { // used to animate with a custom update function
            if ((element == null) || (animinfo == null) || (typeof(animinfo.update) === IGK_UNDEF))
                return;
            var contextname = igk_getv(animinfo, "context", 'animateUpdate');
            var v_animtype = igk_getv(animinfo, "animtype", 'timeout');
            var v_duration = animinfo ? (animinfo.duration ? animinfo.duration : 1000) : 1000;
            var c = igk.animation.context($igk(element).o, contextname, v_duration, v_animtype);
            c.stop();
            c.interval = animinfo ? (animinfo.interval ? animinfo.interval : 20) : 20;
            c.duration = v_duration;
            c.callback = animinfo ? animinfo.complete : null;
            var k = new function() {
                this.time = 0; // maintain time
            };
            // replace update
            c.update = function() {
                var q = this;
                var end = false;
                k.time += q.interval;
                animinfo.update.apply(this, [k]);
                end = k.time >= q.duration;
                if (!end) {
                    if (this.lasttimeout)
                        igk.clearTimeout(this.lasttimeout);
                    this.lasttimeout = setTimeout(function() { q.update(); }, this.interval);
                } else {
                    $igk(this.element).setCss(properties);
                    this.stop();
                    if (this.callback) {
                        this.callback();
                    }
                }
            };
            c.start();
            return c;
        },
        getAnimationFrame: function() {
            return igk.fn.getWindowFunc("requestAnimationFrame", __getAnimationFrame);
        },
        getAnimationCancelFrame: function() {
            return igk.fn.getWindowFunc("cancelAnimationFrame", __cancelAnimationFrame);
        }
    });
    var m_animFrames = [];
    var lastTime = 0;

    function __getAnimationFrame(callback) {
        var currTime = new Date().getTime();
        var timeToCall = Math.max(0, 16 - (currTime - lastTime));
        var id = window.setTimeout(function() {
                callback(currTime + timeToCall);
            },
            timeToCall);
        lastTime = currTime + timeToCall;
        return id;
    }

    function __cancelAnimationFrame(id) {
        window.clearTimeout(id);
    }
    igk_appendProp(igk.animation, {
        effects: new function() {
            igk_appendProp(this, {
                "linear": function(progress) {
                    return progress;
                },
                "quad": function(progress) {
                    return Math.pow(progress, 2);
                },
                "quint": function(progress) {
                    return Math.pow(progress, 2);
                },
                "pow": function(progress, pow) {
                    return Math.pow(progress, pow || 2);
                },
                "circ": function(progress) {
                    return 1 - Math.sin(Math.acos(progress));
                },
                "back": function(progress) {
                    return Math.pow(progress, 2) * ((1.5 + 1) * progress - 1.5);
                },
                "bounce": function(progress) {
                    for (var a = 0, b = 1, result; 1; a += b, b /= 2) {
                        if (progress >= (7 - 4 * a) / 11) {
                            return -Math.pow((11 - 6 * a - 11 * progress) / 4, 2) + Math.pow(b, 2);
                        }
                    }
                },
                "makeEaseInOut": function(delta) {
                    return function(progress) {
                        if (progress < .5)
                            return delta(2 * progress) / 2;
                        else
                            return (2 - delta(2 * (1 - progress))) / 2;
                    };
                },
                "makeEaseOut": function(delta) {
                    return function(progress) {
                        return 1 - delta(1 - progress);
                    };
                },
                toString: function() { return "igk.anmation.effects"; }
            });
        },
        getEffect: function(animeffect, animeffectmode) {
            if (!animeffect)
                animeffect = "linear";
            if (!animeffectmode)
                animeffect = "easein";
            var out_func = igk.animation.effects[animeffect];
            switch (animeffectmode) {
                case "easein": // no reverse effect
                    break;
                case "easeout":
                    out_func = igk.animation.effects.makeEaseOut(out_func);
                    break;
                case "easeinout":
                    out_func = igk.animation.effects.makeEaseInOut(out_func);
                    break;
            }
            return out_func;
        }
    });
    createNS("igk.validator", {
        isUri: function(s) {
            return /(http(s){0,1}|ftp|file):\/\/(.)+$/.test(s);
        },
        toString: function() { return igk.validator.fullname; }
    });

    function __ajx_initfunc(func) { // get ajx func request		
        if (typeof(func) == 'string') {
            var g = $igk(func);
            if (g.getCount() > 0) {
                func = function(xhr) {
                    if (this.isReady()) {
                        g.setHtml(xhr.responseText).init();
                    }
                };
            }
        }
        func = func || igk.ajx.fn.replace_or_append_to_body;
        return function(xhr) {
            if (xhr.readyState == 2) {
                if (xhr.status == 200) {
                    if (xhr.getResponseHeader("Content-Type").toLowerCase()
                        .indexOf("application/force-download") != -1) {
                        xhr.responseType = "blob";
                    }
                    // console.debug("receive header");
                }
            }
            // + | force file download
            if (this.isReady() && (xhr.responseType == "blob") && !this.noBlob) {
                var ctype = xhr.getResponseHeader("Content-Type");
                var mime_type = xhr.getResponseHeader("Content-Mime-Type") || xhr.getResponseHeader("Content-Type");
                var cname = "download.txt";
                var pos = 0;
                if ((pos = ctype.indexOf("name=\"")) != -1) {
                    pos += 6;
                    cname = ctype.substring(pos, ctype.indexOf('"', pos)).trim();
                }
                igk.io.file.download(mime_type, cname, xhr.response);
                return;
            }
            if (typeof(func) == "function")
                func.apply(this, [xhr]);
        };
    };
    (function() {
        var m_ajx_monitorListener; // for monitoring
        var m_hxhr; // store xhr instance for the ready state change
        var sm_join = {};
        var m_ajxhe = 0; // object of {xhr header}
        var m_exajxdata = 0; // {responseType:'text/plain|blob|arraybuffer'}
        function __ajx_setExtraData(ajx, extradata) {
            extradata = extradata || m_exajxdata;
            if (extradata) {
                igk_appendProp(ajx, extradata);
                if ('responseType' in extradata) {
                    ajx.xhr.responseType = extradata.responseType;
                }
                if ('contentType' in extradata) {
                    ajx.xhr.contentType = extradata.contentType;
                }
            }
            m_exajxdata = 0;
        };

        (function(){
            var _ajx_info = {
                initializer:null
            };
        createNS("igk.ajx", {
            bindHeader: function(p) { //bind properties to next ajx header
                m_ajxhe = p;
            },
            bindExtraData: function(exdata) { // bind extra data that will be used for ajx request
                //object property or null 
                m_exajxdata = exdata;
            },
            setHeader: function(xhr) {
                // ini ovh must be the minus sign
                xhr.setRequestHeader("IGK-X-REQUESTED-WITH", "XMLHttpRequest");
                if (m_ajxhe) {
                    for (var m in m_ajxhe) {
                        xhr.setRequestHeader(m, m_ajxhe[m]);
                    }
                    m_ajxhe = 0;
                }
            },
            setMonitorListener: function(monitor) { // static func
                m_ajx_monitorListener = monitor;
            },
            isMonitoring: function() { // static func
                return m_ajx_monitorListener != null;
            },
            getCurrentXhr: function() {
                return m_hxhr;
            },
            GetParentHost: function() {
                return m_hxhr ? m_hxhr.source : null;
            },
            evalNode: function(n) { // evaluate only script content. not forcing file to download again and initialize
                if (typeof(n) != "string") {
                    if (n.tagName && n.tagName.toLowerCase() == "script") {
                        var pn = n.parentNode ? n.parentNode : igk.dom.body().o;
                        try {
                            var v_script = $igk(n).getHtml();
                            if (igk_str_trim(v_script).length > 0) {
                                igk.evalScript(v_script, pn, n);
                            }
                            // remove useless
                            if (n.getAttribute("autoremove"))
                                $igk(n).remove();
                        } catch (ex) {
                            // for chrome disable code extension in some case.
                            console.debug(ex);
                        }
                    } else {
                        igk_preload_image(n); // preload n
                        // igk_eval_all_script(n);					
                        var ct = $igk(n).select("script");
                        ct.each(function() {
                            var _t = this.getAttribute("type");
                            if (_t == 'text/balafonjs') {
                                __bindbalafonjs.apply(this);
                            } else {
                                if (_t in __scriptsEval) {
                                    var fc = __scriptsEval[_t];
                                    fc.apply(this);
                                } else {
                                    // + | eval  script 
                                    igk_eval(this.getHtml(), this.o, this.o);
                                }
                            }
                            return this;
                        });
                    }
                }
            },
            // replace entire body with the content of text	
            replaceBody: function(text, unregister) {
                var c = window.igk.utils.getBodyContent(text);
                try {
                    // igk_freeEventContext();		
                    // igk.ctrl.clearAttribManager();
                    if (unregister) {
                        igk.winui.getEventObjectManager().unregister(document);
                        igk.winui.getEventObjectManager().unregister(window);
                        igk.winui.getEventObjectManager().unregister_child(igk.dom.body().o);
                    }
                    // clear body content
                    igk.dom.body().setHtml("");
                    // set the new content
                    igk.dom.body().setHtml(c);
                    // force call of ready on function
                    // int body node
                    igk.ajx.fn.initnode(igk.dom.body().o);
                    // raise body replaced event
                    igk.publisher.publish(igk.evts.dom[1], { evt: { text: text, target: igk.dom.body().o } });
                    // igk.ready();
                } catch (ex) {
                    igk.winui.notify.showErrorInfo("Javascript Exception", "replaceBody Evaluation failed <br />" + ex);
                    console.error(ex);
                }
            },
            a_postResponse: function(a, parentTag) {
                window.igk.ajx.post(a.href, null, new window.igk.ajx.targetResponse(a, parentTag).update);
                return !1;
            },
            a_getResponse: function(a, parentTag) {
                window.igk.ajx.get(a.href, null, new window.igk.ajx.targetResponse(a, parentTag).update);
                return !1;
            },
            aposturi: function(uri, targetNodeId) { // post uri and set response to targetNodeId
                window.igk.ajx.post(uri, null, function(xhr) { if (this.isReady()) { this.setResponseTo(document.getElementById(targetNodeId), true); } });
            },
            ageturi: function(uri, targetNodeId) { // get uri and set response to targetnodeid
                var q = $igk(targetNodeId);
                if (q && uri) {
                    igk.ajx.get(uri, null, function(xhr) {
                        if (this.isReady()) {
                            this.replaceResponseNode(q.o);
                        }
                    });
                }
            },
            // create a response node;
            responseNode: function(nodeId) {
                var m_target = document.getElementById(nodeId);
                if (m_target) {
                    window.igk.appendProperties(this, {
                        "response": function(xhr) {
                            if (this.isReady()) {
                                this.setResponseTo(m_target, true);
                            }
                        }
                    });
                }
            },
            getResponseNodeFunction: function(cibling, parentNodeTag) {
                var b = $igk(cibling).getParentByTagName(parentNodeTag);
                if (b) {
                    return function(xhr) {
                        if (this.isReady()) {
                            this.setResponseTo(b);
                        }
                    };
                }
                return null;
            },
            // create a new response bodyset the response body
            responseBody: function() {
                window.igk.appendProperties(this, {
                    "response": function(xhr) {
                        if (this.isReady()) {
                            window.igk.ajx.replaceBody(xhr.responseText, true);
                        }
                    }
                });
            },
            targetResponse: function(item, parentTag) { // object used to set response of ajx query
                var m_target = null;
                if (parentTag) {
                    m_target = $igk(item).getParentByTagName(parentTag);
                } else
                    m_target = item;
                if (m_target == null)
                    return null;
                window.igk.appendProperties(this, {
                    update: function(xhr) {
                        // update the response string
                        if (this.isReady()) {
                            this.setResponseTo(m_target, true);
                        }
                    },
                    toString: function() {
                        return "igk.ajx.targetResponse";
                    }
                });
            },
            ajx: function(monitorlistener) { // ajx object
                var xhr = null;
                if (igk.isFunc(typeof(window.XMLHttpRequest))) //  Objet standard
                {
                    xhr = new window.XMLHttpRequest(); //  Firefox,Safari,...
                } else {
                    if (igk.navigator.$ActiveXObject()) //  Internet Explorer
                    {
                        try {
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        } catch (ex) {
                            throw ("No Ajax Support");
                        }
                    }
                }
                if ((xhr == null) || (xhr.readyState + "" == igk.constants.undef)) {
                    throw ("No Ajax Support");
                }
                this.xhr = xhr;
                this.saveState = false;
                this.uri = null;
                this.postargs = null;
                this.method = "GET";
                this.synchronize = false;
                this.noBlob = false; // true to disable file auto download
                this.setResponseMethod = function(method) { // instructions de traitement de la réponse 
                    var q = this;
                    if (method) {
                        switch (typeof(method)) {
                            case "function":
                                q.responseMethod = method;
                                q.xhr.onreadystatechange = function() {
                                    // try{	
                                    if (q.responseMethod) {
                                        m_hxhr = q;
                                        igk.context = 'xhr';
                                        q.responseMethod(this);
                                        m_hxhr = null;
                                        igk.context = null;
                                    }
                                    // }
                                    // catch(ex)
                                    // {				
                                    // igk.winui.notify.showErrorInfo("Exception",
                                    // "<h3>AJX:__setResponseMethod__</h3><div>"+ ex +"<div><code>Trace : " +ex.trace+"</code> <pre style='text-overflow:ellipsis; overflow:hidden; max-height:4em;'>"+ 
                                    // igk.html.string(this.responseText)
                                    // +"</pre><p class=\"igk-trace\" style='padding-top:1.3em' ><pre style='max-height:3em; overflow-y:auto;'>"+ex.stack
                                    // +"</pre></p>"
                                    // // + " <div style='color:#222'>"+q.responseMethod+"</div>"
                                    // );
                                    // }
                                };
                                break;
                            case 'object':
                                var fc = method.complete ? function() { method.complete.apply(method, [q.xhr, method]); } : null;
                                q.xhr.onreadystatechange = null; // ,fc;
                                q.xhr.onload = fc; // method.complete? function(){ method.complete.apply(method, [q.xhr, method]);  }; // method.complete;
                                q.xhr.onerror = method.error;
                                break;
                        }
                    }
                };

                function __initheader() {
                    // migration of apache request that name must not contains underscore or will be ignored
                    igk.ajx.setHeader(this.xhr);
                    if (!igk.isFunc(typeof(document.domain))) {
                        this.xhr.setRequestHeader("IGK-FROM", "igkdev");
                    }
                };
                this.send = function(method, url, postargs, sync) {
                    this.uri = url;
                    // igk.constants constant definition
                    this.asynchronize = typeof(sync) == igk.constants.undef ? true : sync;
                    this.postargs = postargs;
                    this.method = method;
                    this.xhr.open(method, url, this.asynchronize);
                    // this.xhr.setRequestHeader("Content-Type","multipart/form-data");   		
                    // this.xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=iso-8859-1");
                    // TODO: help microsoft to create a xml in result
                    if (this["ajx.xhr"]) {
                        igk_appendProp(this.xhr, this["ajx.xhr"]);
                    }
                    __initheader.apply(this);
                    if (postargs) {
                        if (typeof(postargs) == "string") {
                            // for url encoding
                            this.xhr.setRequestHeader("Content-Type", this.contentType || "application/x-www-form-urlencoded;charset=iso-8859-1");
                            // chrome refuse to set those header. type is marked as unsafe
                            // this.xhr.setRequestHeader("Content-length",postargs.length);
                            // this.xhr.setRequestHeader("Connection","close");
                        }
                    }
                    if (monitorlistener) {
                        this.xhr.onloadstart = monitorlistener.loadstart;
                        this.xhr.onloadend = monitorlistener.loadend;
                        this.xhr.onprogress = monitorlistener.loadprogress;
                    }
                    this.xhr.send(postargs);
                };
            },
            postWebRequest: function(uri, action, param, func, headers, async, savestate, extradata) { // for service 
                if (typeof(async) == igk.constants.undef)
                    async = !0;
                var ajx = new igk.ajx.ajx();
                ajx.saveState = (savestate) ? true : false;
                __ajx_setExtraData(ajx, extradata);
                igk.appendProperties(ajx, {
                    serviceResponse: function() {
                        var d = document.createElementNS(igk.namespaces.xhtml, "dummy");
                        $igk(d).setHtml(ajx.xhr.responseText);
                        var r = $igk(d).select(action + "_result").first();
                        if (r) {
                            return r.o.innerHTML;
                        }
                        return 0;
                    }
                });
                ajx.setResponseMethod(__ajx_initfunc(func)); // || igk.ajx.fn.replace_or_append_to_body);
                ajx.xhr.open("POST", uri, async);
                // ajx.xhr.setRequestHeader("Content-Type", "text/html");
                ajx.xhr.setRequestHeader("Content-Type", "text/plain");
                igk.ajx.setHeader(ajx.xhr);
                ajx.xhr.setRequestHeader("SOAPACTION", action);
                // if (headers) {
                // for (var i in headers) {
                // ajx.xhr.setRequestHeader(i, headers[i]);
                // }
                // }
                var v_params = ""; // params to send 		
                for (var i in param) {
                    v_params += "<" + i + ">" + param[i] + "</" + i + ">";
                }
                var v_body = "<" + action + ">" + v_params + "</" + action + ">";
                // build packet to send
                var packet = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body>' + v_body + '</soap:Body></soap:Envelope>';
                ajx.xhr.send(packet);
                return ajx;
            },
            post(uri, param, func, async, savestate, extradata) {
                return igk.ajx.send(uri, 'POST', param, func, async, savestate, extradata);
            },
            apost(uri, param, func, savestate, extradata) {
                return igk.ajx.post(uri, param, func, true, savestate, extradata);
            },
            get(uri, param, func, async, savestate, extradata) {
                // extradata used to init ajx object
                var ajx = new igk.ajx.ajx(m_ajx_monitorListener);
                if (typeof(async) == igk.constants.undef)
                    async = !0;
                ajx.saveState = (savestate == igk.constants.undef) ? true : false;
                __ajx_setExtraData(ajx, extradata);
                ajx.setResponseMethod(__ajx_initfunc(func));
                ajx.send("GET", uri, param, async);
                return ajx;
            },
            aget(uri, param, func, extradata) {
                return igk.ajx.get(uri, param, func, true, extradata);
            },
            join: function(n) {
                if (n in sm_join) {
                    sm_join[n].abort();
                }
                sm_join[n] = this;
                return igk.ajx;
            },
            /**
             * 
             * @param {string|postOptions} uri uri 
             * @param {string} method verbs to ask
             * @param {*} param param to send
             * @param {*} func 
             * @param {*} async 
             * @param {*} savestate 
             * @param {*} extradata 
             * @returns 
             */
            send(uri, method, param, func, async, savestate, extradata) {
                if (typeof(async) == igk.constants.undef)
                    async = !0;
                var contentType = null;
                if (typeof(uri) == 'object') {
                    method = uri.method || method;
                    param = uri.param || param;
                    func = uri.func || func;
                    contentType = uri.contentType || contentType;
                    uri = uri.uri || uri;
                }
                var ajx = null;
                try {
                    ajx = new igk.ajx.ajx(m_ajx_monitorListener);
                    ajx.saveState = (savestate) ? true : false;
                    ajx.contentType = contentType;
                    __ajx_setExtraData(ajx, extradata);
                    ajx.setResponseMethod(__ajx_initfunc(func));
                    // console.debug(ajx.contentType, param, ajx);
                    ajx.send(method, uri, param, typeof(async) == igk.constants.undef ? !0 : async);
                } catch (e) {
                    igk.log.write("ajx log error : Error ::::: ");
                    igk.show_notify_prop_v(e);
                }
                return ajx;
            },
            /**
             * post form 
             * @param {*} form form to that host post
             * @param {*} uri uri target 
             * @param {*} func callback function 
             * @param {*} sync async call
             */
            postform: function(form, uri, func, sync) {
                if (!form)
                    return;
                uri = uri || form.getAttribute("action");
                var method = (form.getAttribute("method") || "POST").toUpperCase();
                var msg = "";
                var e = null;
                var p = [];
                if (window.tinyMCE) { // to update the tinyMce before update
                    window.tinyMCE.triggerSave();
                }
                var fc = method == "POST" ? igk.ajx.post : igk.ajx.get;

                function __appendForm(id, value) {
                    if (p[id]) {
                        var tab = p[id];
                        if (igk_is_array(tab)) // array
                        {
                            tab.push(value);
                        } else {
                            var tt = [];
                            tt.push(tab);
                            tt.push(value);
                            p[id] = tt;
                        }
                    } else
                        p[id] = value;
                }
                // prepend waiter
                if (igk.winui.lineWaiter) {
                    var lw = igk.winui.lineWaiter.prependTo(form);
                    var tf = func;
                    $igk(form).select(".actions").addClass("dispn");
                    func = function(xhr) {
                        if (tf) {
                            tf.apply(this, [xhr]);
                        } else {
                            console.debug("there is no function callback");
                        }
                        if (this.isReady()) {
                            lw.remove();
                            $igk(form).select(".actions").rmClass("dispn");
                        } else if (xhr.readyState == 4) {
                            lw.remove();
                        }
                    };
                }
                if (window.FormData) {
                    // if supporting FormData
                    var frmData = null;
                    var enctype = form.getAttribute("enctype");
                    if (enctype == "application/json") {
                        var obj = 0;
                        var data = 0;
                        if (obj = form.getAttribute("form-data")) {
                            data = obj;
                            if (typeof(data) == 'object')
                                data = JSON.stringify(data);
                        } else {
                            obj = {};
                            igk.winui.form.serialize(form, obj);
                            data = JSON.stringify(obj);
                        }
                        igk.ajx.send({
                            uri: uri,
                            method: method,
                            contentType: 'application/json',
                            func: function(xhr) {
                                if (this.isReady()) {
                                    if (typeof(func) == "function") {
                                        func.apply(this, [xhr]);
                                    }
                                }
                            },
                            param: data
                        });
                    } else {
                        frmData = new FormData(form);
                        if (window.event && window.event.submitter) {
                            var i = window.event.submitter;
                            var n = i.getAttribute("name");
                            if (n) {
                                frmData.append(n, i.getAttribute("value"));
                            }
                        } else {
                            form.querySelectorAll("input[type='submit']").forEach(function(i) {
                                var n = i.getAttribute("name");
                                if (n) {
                                    frmData.append(n, i.getAttribute("value"));
                                }
                            });
                        }
                        // console.debug("send....form data", form.getAttribute("enctype") ,frmData, fc );
                        if (enctype == "application/x-www-form-urlencoded") {
                            //convert to encoding data
                            frmData = encodeQueryFromData(frmData);
                        }
                        // return; 
                        fc(uri, frmData, func, (sync == igk.constants.undef) ? sync : true, true, {
                            contentType: "application/x-www-form-urlencoded",
                            source: $igk(form) // setting the source of the current definition,
                        });
                    }
                } else {
                    for (var i = 0; i < form.length; i++) {
                        e = form.elements[i];
                        switch (e.type) {
                            case "radio":
                            case "checkbox":
                                if (e.checked) {
                                    __appendForm(e.id, e.value);
                                }
                                break;
                            case "file":
                                if (e.files.length > 0) {
                                    frmData.append(e.id, e.files[0]);
                                }
                                break;
                            default:
                                __appendForm(e.id, e.value);
                                break;
                        }
                    }
                    e = 0;
                    for (var i in p) {
                        if (e != 0)
                            msg += "&";
                        if (igk_is_array(p[i])) {
                            for (var t in p[i]) {
                                if (e != 0)
                                    msg += "&";
                                msg += i + "=" + p[i][t];
                                e = 1;
                            }
                        } else
                            msg += i + "=" + p[i];
                        e = 1;
                    }
                    fc(uri, msg, func, (sync == igk.constants.undef) ? sync : true, true, {
                        source: form
                    });
                }
            },
            asyncget: function(uri, param, mimetype) {
                var Prom = window.Promise;
                if (!Prom)
                    return false;
                try {
                    var b = igk.dom.body(); // body document not found
                } catch (ex) {
                    //used ajx to get data
                    var p = new Promise(function(r, j) {
                        var fc = function(o) {
                            r(o);
                        };
                        fc.error = function() {
                            j('[BJS] failed to get : ' + uri);
                        };
                        igk.ajx.get(uri, null, function(xhr) {
                            console.debug("for : " + xhr.status);
                            if (this.isReady()) {
                                fc(xhr.responseText);
                            }
                        });
                    });
                    return p;
                }
                //use ob to get data
                mimetype = mimetype || 'text/plain';
                var p = new Promise(function(r, j) {
                    var fc = function(o) {
                        r(o.data);
                    };
                    fc.error = function() {
                        j(new Error('[BJS] - objdata failed: ' + uri + ', ' + mimetype));
                    };
                    igk.system.io.getData(uri, fc, mimetype);
                });
                return p;
            },
            /**
             * upload file 
             * @param {*} inputfile tagnode
             * @param {*} uri where 
             * @param {*} async async or not
             * @param {*} responseCallback response finish
             * @param {*} startcallBack start
             * @param {*} progressCallback progress 
             * @param {*} doneCallback done
             */
            uploadInputFile: function(inputfile, uri, async, responseCallback, startcallBack, progressCallback, doneCallback) {
                const _ln = inputfile.files.length;
                let _task = [];
                let _max = _ln;
                let _error_fc=null;
                if (typeof(uri) == 'object'){
                    _max = typeof(uri.max)!='undefined' ? uri.max : _max;
                    _error_fc = uri.error;
                    uri = uri.uri;
                }
                if (_ln > _max){
                    if (_error_fc){
                        _error_fc(uri, 'not allowed to upload more than {0} file(s).', _max);
                    }
                    return;
                }
                if (_ln > 0){
                    if (_ln ==1)
                        return igk.ajx.uploadFile(null, inputfile.files[0], uri, async, responseCallback, startcallBack, progressCallback, doneCallback);
                        
                        let _min = Math.min(_max, _ln);
                        for(let i = 0; i < _min; i++){
                            _task.push( (function(i){
                                const g = inputfile.files[i];
                                let args = [i, g];
                                return new Promise(function(resolve, reject){
                                igk.ajx.uploadFile(null, g, uri, async, 
                                        function(xhr){ 
                                            if ((xhr.status >0) &&  (xhr.status != 200)){
                                                reject(xhr)
                                            }else{
                                            // 
                                            // console.log('data: ', xhr.status);
                                            responseCallback.apply(this, [xhr, i, g]);
                                            }
                                        }, 
                                        function(){
                                            startcallBack.apply(this, args);
                                        }
                                        ,  function(){                                             
                                            progressCallback.apply(this, args);
                                        }, 
                                    function(){                                   
                                        doneCallback.apply(this, args); 
                                        resolve(i, g);
                                    });
                            }); 
                        })(i));
                        }
                    return Promise.all(_task).then(function(){
                        // console.log("all task complete");
                    });
                }
            },
            setUploadFileInitializer(fc){
                _ajx_info.initializer = fc;
            },
            uploadFileInitializer(xhr){
                let _fc = _ajx_info.initializer;
                if (_fc){
                    _fc(xhr);
                }
            },
            /**
             * 
             * @param {*} osrc 
             * @param {*} file 
             * @param {*} uri 
             * @param {*} async 
             * @param {*} responseCallback 
             * @param {*} startcallBack 
             * @param {*} progressCallback 
             * @param {*} doneCallback 
             * @param {*} method 
             */
            uploadFile: function(osrc, file, uri, async, responseCallback, startcallBack, progressCallback, doneCallback, method) {
                if (!file) {
                    console.error("/!\\ file not define");
                    return;
                }
                // method: blob or null
                var bob = null;
                var reader = null;
                var v_ajx = new igk.ajx.ajx();
                v_ajx.source = osrc;
                var self = this;
                var xhr = v_ajx.xhr;
                var m = method ? method : 'blob';
                responseCallback = responseCallback || igk.ajx.fn.replace_or_append_to_body;
                if (igk.navigator.isIE() && igk.navigator.IEVersion()) {
                    v_ajx.setResponseMethod(function(xhr) {
                        if (this.isReady()) {
                            // get file....
                            igk.ajx.post(uri + '&ie=1', null, responseCallback);
                        }
                    });
                } else {
                    v_ajx.setResponseMethod(responseCallback);
                }
                // 
                // var async=!0;// important to view progression .on chrome
                xhr.open("POST", uri, async); // async);// devnull.php");
                // return;
                v_ajx.xhr.upload.onerror = function(evt) {
                    igk.winui.notify.showErrorInfo(
                        "Error",
                        "/!\\ uploaded failed: " + uri + "<br />" +
                        " " + evt);
                    console.debug("Error : " + v_ajx.xhr.statusText);
                    console.debug(v_ajx.xhr);
                };
                // async registration function
                // v_ajx.xhr.onprogress=function(evt){
                // };
                // async registration function
                if (async) {
                    v_ajx.xhr.upload.onprogress = function(evt) {
                        if (progressCallback) {
                            progressCallback.apply(this, arguments);
                        }
                    };
                    v_ajx.xhr.upload.onload = function(evt) {
                        if (doneCallback) {
                            doneCallback.apply(this, arguments);
                        }
                    };
                    v_ajx.xhr.upload.onloadstart = function(evt) {
                        if (startcallBack) {
                            startcallBack.apply(this, arguments);
                        }
                    }
                }
                xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
                xhr.setRequestHeader("Cache-Control", "no-cache");
                // + | ajx context header
                igk.ajx.setHeader(xhr);
                xhr.setRequestHeader("IGK-UPLOADFILE", true);
                xhr.setRequestHeader("IGK-FILE-NAME", file.name);
                xhr.setRequestHeader("IGK-UP-FILE-SIZE", file.size); // file.file[0] size);
                xhr.setRequestHeader("IGK-UP-FILE-TYPE", file.type);

                igk.ajx.uploadFileInitializer(xhr);

                // xhr.setRequestHeader("Content-Type","multipart/form-data");
                // xhr.setRequestHeader("Content-Type","multipart/form-data;charset=utf-8; boundary=" + Math.random().toString().substr(2));
                // for security reason need to pass Content-Type with proper content-type
                var type = file.type;
                // if (!type){
                type = "application/octet-stream";
                // }
                xhr.setRequestHeader("Content-Type",
                    type + "; charset=utf-8; boundary=" + Math.random().toString().substr(2));
                //"Content-type","multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2));
                var r = file;
                var filer = false;
                if (m != 'blob') {
                    if (typeof(FileReader) != IGK_UNDEF)
                        filer = new FileReader();
                    if (!filer) {
                        return;
                    }
                    // 1.
                    reader = filer;
                    // reader.onprogress = function (evt) {
                    // 	console.debug("progress read");
                    // };
                    reader.onload = function(evt) {
                        // igk.winui.notify.showMsg("<div class=\"igk-notify igk-notify-default\">"+ igk.html.getDefinition(xhr)+"</div>");
                        var t = igk.winui.eventTarget(evt);
                        if (xhr.sendAsBinary) // mod 
                            xhr.sendAsBinary(t.result);
                        else { // ie and other				
                            xhr.send(t.result);
                        }
                    };
                    if (reader.readAsBinaryString)
                        reader.readAsBinaryString(r);
                    else
                        reader.readAsText(r);
                } else {
                    if (r.slice) {
                        bob = r.slice(0, file.size);
                        // important to avoid
                        try {
                            xhr.send(bob);
                        } catch (ex) {
                            igk.winui.notify.showErrorInfo("JS Exception ", "" + ex);
                        }
                    }
                }
            },
            load: function(f, callback) {
                var async = 0;
                var param = null;
                var ajx = new igk.ajx.ajx();
                ajx.saveState = 1;
                ajx.setResponseMethod(callback); // func || igk.ajx.fn.replace_or_append_to_body);		
                ajx.send("GET", f, param, async);
                return ajx;
            },
            toString: function() { return "igk.ajx"; },
            globalMonitorListener: function() {
                // object		
                igk.appendProperties(this, {
                    loadstart: function(evt) { igk.publisher.publish('sys://ajx/loadstart', { evt: evt }); },
                    loadend: function(evt) { igk.publisher.publish('sys://ajx/loadend', { evt: evt }); },
                    loadprogress: function(evt) {
                        igk.publisher.publish('sys://ajx/loadprogress', { evt: evt });
                    }
                });
            }
        });

        })();

        function encodeQueryFromData(frmData) {
            // code from : https://stackoverflow.com/questions/7542586/new-formdata-application-x-www-form-urlencoded			
            var s = '';

            function encode(s) { return encodeURIComponent(s).replace(/%20/g, '+'); }
            for (var pair of frmData.entries()) {
                if (typeof pair[1] == 'string') {
                    s += (s ? '&' : '') + encode(pair[0]) + '=' + encode(pair[1]);
                }
            }
            return s;
        };
        createNS("igk.ajx", {
            fetch: function(uri, data) {
                if (!window.Promise) {
                    throw ("Promise not found - old browser");
                }
                var p = new Promise(function(resolv, reject) {
                    igk.ajx.aget(uri, data, function(xhr) {
                        if (this.isReady()) {
                            resolv.apply(xhr);
                        } else if (xhr.readyState == 4) {
                            reject.apply(xhr);
                        }
                    });
                });
                return p;
            },
            postFormData: function(uri, data) {
                var f = igk.dom.body().add("form");
                f.setAttribute("action", uri);
                f.setAttribute("method", "POST");
                f.setAttribute("enctype", "multipart/form-data");
                for (var i in data) {
                    // console.debug(typeof(data[i]));
                    f.add("input").setAttribute("name", i)
                        .setAttribute("value", (typeof(data[i]) == 'object') ? JSON.stringify(data[i]) : data[i]);
                }
                f.o.submit();
            }
        });
        m_ajx_monitorListener = new igk.ajx.globalMonitorListener();
        igk_appendProp(igk.ajx.ajx.prototype, {
            isReady: function() {
                return (this.xhr.readyState == 4) && (this.xhr.status == 200);
                // (
                // (( this.xhr.readyState == 4) && (this.xhr.status == 200)) 
                // || // for chrome
                // ((this.xhr.readyState == 4) && (this.xhr.status == 200) && (/^(OK|No Error)/.test(this.xhr.statusText) ))
                // );
            },
            json: function() {
                // get json data
                var r = this.xhr.responseText;
                if (r) {
                    return JSON.parse(r);
                }
            },
            isFailed: function() {
                if ((this.xhr.readyState == 4))
                    return (this.xhr.status != 200);
                return true;
            },
            toString: function() { return "igk.ajx"; },
            setResponseTo: function(q, unregister) { // q is node
                if (q && (typeof(q.innerHTML) != IGK_UNDEF)) { // set response to node
                    if (q == igk.dom.body().o) {
                        this.replaceBody();
                    } else {
                        // unregister childs
                        if (unregister) {
                            igk.winui.getEventObjectManager().unregister_child(q);
                        }
                        q.innerHTML = this.xhr.responseText;
                        igk.ajx.fn.initnode(q);
                    }
                }
            },
            replaceBody: function() {
                igk.ajx.replaceBody(this.xhr.responseText, true);
            },
            replaceResponseNode: function(node, preload) { // replaceNode ,preload document. default is true
                var i = igk.createNode("dummy");
                var p = typeof(preload) == igk.constants.undef ? !0 : preload;
                var t = this.xhr.responseText;
                // this.setResponseTo(i.o); 
                i.setHtml(t);
                if (node && node.parentNode) {
                    // load childs						 
                    var pt = $igk(node.parentNode);
                    var _ini = [];
                    var _c = (node.previousSibling == null) ? pt.o.firstChild : node;
                    // + | chain node
                    // copy in reverse order
                    // while (i.o.lastChild) {
                    // 	_ini.push(i.o.lastChild);
                    // 	pt.o.insertBefore(i.o.lastChild, _c);
                    // }
                    // copy in normal order 
                    while (i.o.firstChild) {
                        _ini.push(i.o.firstChild);
                        pt.o.insertBefore(i.o.firstChild, _c);
                    }
                    // + | remove node
                    $igk(node).remove();
                    for (var i = 0; i < _ini.length; i++) {
                        if (_ini[i].nodeType == 1)
                            igk.ajx.fn.initnode(_ini[i]); // .push(i.o.lastChild)
                    }
                }
            },
            appendResponseTo: function(q) {
                var s = igk.createNode("dummy");
                s.setHtml(this.xhr.responseText);
                var f = s.o.firstChild;
                var h = s.o.firstChild;
                while (f) {
                    q.appendChild(f);
                    igk.ajx.evalNode(f);
                    f = f.nextSibling;
                }
            },
            prependResponseTo: function(q) {
                var s = igk.createNode("dummy");
                s.o.innerHTML = this.xhr.responseText;
                var m = q.o.firstChild;
                var f = s.o.firstChild;
                var c = f;
                while (f) {
                    c = c.nextSibling;
                    q.insertBefore(f, m);
                    igk.ajx.evalNode(f);
                    f = c;
                }
            },
            abort: function() {
                // abort xhr response
                this.xhr.abort();
            }
        });
    })();
    createNS("igk.soap", {
        query: function(data) {
            var ns = "http://schemas.xmlsoap.org/soap/envelope/";
            var r = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
            var g = igk.createNode("soap:Body", ns);
            // return;
            var m = data.method;
            if (m) {
                var fm = g.add(igk.createNode(m, ns));
                if (data.params) {
                    for (var i in data.params) {
                        fm.add(i).setHtml(data.params[i]);
                    }
                }
                // fm.add("name").setHtml("dof");
            }
            // <soap:Body><getCountry><name>abc</name></getCountry></soap:Body></soap:Envelope>
            r += g.o.outerHTML;
            r += "</soap:Envelope>";
            igk.ajx.post(data.uri, r, function(xhr) {
                if (this.isReady()) {
                    var q = igk.createNode("div");
                    q.setHtml(xhr.responseText);
                    if (xhr.status == 200) {
                        if (m && data.ready) {
                            var g = q.getElementsByTagName(m + "_result")[0];
                            data.ready({ data: g.innerHTML });
                        }
                    } else {
                        var g = q.getElementsByTagName("faultstring")[0];
                        if (data.error && g) data.error({ data: g.innerHTML });
                    }
                }
            });
        }
    }, { desc: "used to handle soap request" });
    (function() {
        var m_nodeReady = [];
        var m_initBeforeReady = [];
        /**
         * global current host to replace. tab 
         */
        var m_ajx_host = null;
        // var _rootDepth=0;// count init node
        // 
        // uitilies ajx function
        // 
        var _postdata = [];

        function _bready(c) {
            // before ready call node
            var l = m_initBeforeReady.length;
            if (l <= 0)
                return;
            var m = $igk(c);
            for (var i = 0; i < l; i++) {
                m_initBeforeReady[i].apply(m);
            }
        };

        function _initnode(c) {
            if (!c){
                return;
            }
            _bready(c);
            // _rootDepth++;		
            // evaluate all script in this 
            // igk.ready(null,'initnode');
            // evaluate script contained in this node
            igk.ajx.evalNode(c);
            // evaluate binding Attrib Data
            igk.ctrl.callBindAttribData(c);
            // call ready on node
            igk.ajx.fn.nodeReady(c);
            var p = igk.publisher;
            p.publish("sys://node/init", {
                target: c,
                rootDepth: 0
            });
            p.publish("sys://doc/changed", { target: c, context: 'initnode' });
        };
        createNS("igk.ajx.fn", {
            none: function() {},
            complete_ready: function(select) { // create a ready for a select node	
                return function(xhr, q) {
                    var s = q.select(select).first();
                    if (s)
                        s.setHtml(xhr.responseText).init();
                };
            },
            postData: function(uri, n, t, m) {
                if (uri in _postdata) {
                    _postdata[uri].abort();
                }
                var fc = null;
                if (typeof(t) == 'string') {
                    var g = $igk(t).first();
                    if (g)
                        fc = igk.ajx.fn.replace_content(g.o);
                } else if (typeof(t) == 'function') {
                    fc = t;
                } else {
                    fc = igk.ajx.fn.replace_or_append_to_body;
                }
                var v = n.value;
                if (typeof(v) == 'object')
                    v = igk.JSON.convertToString(v);
                // method type
                var _send = igk.ajx.post;
                var _data = n.id + "=" + v;
                if (!m) {
                    _send = igk.ajx.get;
                    uri += ((uri.indexOf("?") == -1) ? "?" : "&") + _data;
                }
                g = _send(uri, _data, function(xhr) {
                    if (this.isReady()) {
                        if (fc)
                            fc.apply(this, [xhr]);
                        delete(_postdata[uri]);
                    }
                });
                _postdata[uri] = g;
            },
            appendTo: function(t) {
                return function(xhr) {
                    if (this.isReady()) {
                        igk.ajx.fn.append_to(xhr, t);
                    }
                }
            },
            scriptReplaceContent: function(m, u, t) {
                var fc = igk.ajx.get;
                if (m == "post")
                    fc = igk.ajx.post;

                function rp(xhr) {
                    if (this.isReady()) {
                        if (igk.winui.lineWaiter)
                            igk.winui.lineWaiter.remove(t);
                        igk.ajx.fn.replace_content(t).apply(this, [xhr]);
                    } else if ((xhr.status != 200) && (xhr.readyState == 4)) {
                        igk.winui.lineWaiter.remove(t);
                        if (igk.ENVIRONMENT.debug)
                            $igk(t).setHtml(igk.R.gets("AJX - Error"));
                    }
                }
                if (igk.winui.lineWaiter) {
                    igk.winui.lineWaiter.prependTo(t);
                }
                fc.apply(window, [u, null, rp]);
            },
            getfortarget: function(u, t) {
                // u:uri
                // t:target
                if (!t)
                    return;
                igk.ajx.get(u, null, function(xhr) {
                    if (this.isReady()) {
                        this.setResponseTo(t);
                        igk.ajx.fn.initnode(t);
                    }
                });
            },
            replace_body: function(xhr) {
                if (this.isReady()) {
                    this.replaceBody();
                }
            },
            replace_node: function(host) { // get the replace node function
                return function(xhr) {
                    if (this.isReady()) {
                        this.replaceResponseNode(host, false);
                    }
                }
            },
            replace_content: function(host) {
                return function(xhr) {
                    if (this.isReady()) {
                        // get body content
                        var g = igk.utils.getBodyContent(xhr.responseText, 1);
                        if (g != null) {
                            igk.dom.body().setHtml('');
                            igk.ajx.fn.replace_or_append_to(igk.dom.body().o).apply(this, [xhr]);
                        } else {
                            this.setResponseTo(host, true);
                        }
                    }
                };
            },
            replace_or_append_to: function(t) {
                return function(xhr) {
                    if (this.isReady()) {
                        // igk.dom.body().setHtml("");
                        var q = igk.createNode("dummy");
                        // $igk(igk.dom.loadDocument(xhr.responseText));// igk.createNode("dummy");
                        q.setHtml(xhr.responseText);
                        var i = q.o.childNodes.length;
                        if (i > 0) {
                            var vo = new igk.selector();
                            while (i > 0) {
                                var c = q.o.childNodes[0];
                                var p = 0;
                                if (igk.ajx.fn.ctrl_replacement(c) == false) {
                                    if (c.id) {
                                        var ki = document.getElementById(c.id);
                                        if (ki) {
                                            p = 1;
                                            ki.parentNode.replaceChild(c, ki);
                                        }
                                    }
                                } else {
                                    p = 1;
                                }
                                if (p == 0)
                                    t.appendChild(c);
                                igk.ajx.fn.initnode(c);
                                i--;
                                vo.push(c);
                            }
                            // return vo;
                        }
                        igk.publisher.publish(igk.evts.dom[2], { target: t, src: xhr.responseText });
                    }
                };
            },
            replace_or_append_to_body: function(xhr) {
                // replace_or_append_to_body
                // console.debug("start data");
                if (this.isReady()) {
                    // get body only content
                    // console.debug("start data is ready : "+xhr.responseText); 
                    var g = igk.utils.getBodyContent(xhr.responseText, 1);
                    if (g != null)
                        igk.dom.body().setHtml('');
                    igk.ajx.fn.replace_or_append_to(igk.dom.body().o).apply(this, [xhr]);
                }
            },
            append_to_body: function(xhr) {
                if (this.isReady()) {
                    igk.ajx.fn.append_to(xhr, igk.dom.body().o);
                }
            },
            prepend_to_body: function(xhr) {
                if (this.isReady()) {
                    igk.ajx.fn.prepend_to(xhr, igk.dom.body().o);
                }
            },
            ctrl_replacement: function(c) {
                if (c && c.tagName && (c.tagName.toLowerCase() == 'igk:replace-ctrl')) {
                    var q = $igk(c);
                    var st = q.getAttribute("target");
                    var h = q.getAttribute("hash");
                    // alert("dkdk : "+st);
                    if (st) {
                        var t = $igk(st).first();
                        if (t) {
                            var s = q.select(">>");
                            if (s.getCount() == 1) {
                                s.each(function() {
                                    t.replaceBy(this);
                                    igk.ajx.fn.initnode(this.o);
                                    return !1;
                                })
                            } else {
                                var m = "";
                                s.each(function() {
                                    m += this.o.outerHTML;
                                    return !0;
                                });
                                t.setHtml(m);
                                // t.unregister();
                                igk.ajx.fn.initnode(t.o);
                            }
                            q.remove();
                            if (h) {
                                document.location.hash = h;
                            }
                            return !0;
                        }
                    }
                    // no target
                    q.select(">>").each(function() {
                        // get first child node			
                        var g = $igk("#" + this.o.id).first();
                        if (g && (g.getAttribute('igk-type') == "controller")) {
                            g.replaceBy(this);
                            // this.o.id="default";
                            igk.ajx.fn.initnode(this.o);
                        }
                        return !0;
                    });
                    $igk(c).remove();
                    return !0;
                }
                return !1;
            },
            append_to: function(xhr, target) {
                if (xhr.responseText.length > 0) {
                    var q = igk.createNode("div");
                    var txt = igk.utils.treatBodyContent(xhr.responseText);
                    q.setHtml(txt);
                    var i = q.o.childNodes.length;
                    if (i > 0) {
                        var vo = new igk.selector();
                        var ct = 0;
                        while (i > 0) {
                            var c = q.o.childNodes[ct];
                            if (igk.ajx.fn.ctrl_replacement(c) == false) {
                                if (c.tagName && (c.tagName.toLowerCase() == 'igk-body') && ($igk(target).o == igk.dom.body().o)) {
                                    igk.dom.body().replaceWith(c);
                                    igk.ajx.fn.initbody();
                                    ct++;
                                } else {
                                    target.appendChild(c);
                                    igk.ajx.fn.initnode(c);
                                }
                            }
                            i--;
                            vo.push(c);
                        }
                        return vo;
                    }
                }
                return null;
            },
            prepend_to: function(xhr, t) {
                if (xhr.responseText.length > 0) {
                    var q = igk.createNode("div");
                    q.setHtml(xhr.responseText);
                    var i = q.o.childNodes.length;
                    while (i > 0) {
                        var c = q.o.childNodes[0];
                        if (igk.ajx.fn.ctrl_replacement(c) == false) {
                            $igk(t).prependChild(c);
                            igk.ajx.fn.initnode(c);
                        }
                        i--;
                    }
                }
            },
            nodeReady: function(c) {
                if (c.tagName && /(no)?script/.test(c.tagName.toLowerCase()))
                    return;
                if (m_nodeReady && (m_nodeReady.length > 0)) {
                    for (var i = 0; i < m_nodeReady.length; i++) {
                        m_nodeReady[i].apply(c);
                    }
                }
            },
            registerNodeReady: function(f) {
                // 
                // register callback function that will call at the end on igk.ajx.fn.initnode chain.
                // remark: if you want to call on every node used igk.ctrl.registerReady
                // 
                if (typeof(f) == "function")
                    m_nodeReady.push(f);
            },
            unregisterNodeReady: function(f) {
                var s = [];
                for (var i = 0; i < m_nodeReady.length; i++) {
                    if (m_nodeReady[i] == func) {
                        continue;
                    }
                    s.push(readyFunc[i]);
                }
                m_nodeReady = s;
            },
            initBeforeReady: function(f) {
                // register global function function that will be called when ever a node require to be ready
                if (typeof(f) == "function")
                    m_initBeforeReady.push(f);
            },
            uninitBeforeReady: function(f) {
                var c = [];
                for (var i = 0; i < m_initBeforeReady.length; i++) {
                    if (m_initBeforeReady[i] == f) continue;
                    c.push(m_initBeforeReady[i]);
                }
                m_initBeforeReady = c;
            },
            initbody: function() { // init body and publish event system/bodychange
                igk.ajx.fn.initnode(igk.dom.body().o);
                igk.publisher.publish("system/bodychange", { target: igk.dom.body().o });
            },
            bindto: function(n, h) {
                // >n:target node 
                // >h:history link
                return function(xhr) {
                    if (this.isReady()) {
                        this.setResponseTo(n);
                        igk.ajx.fn.initnode(n.o);
                        if (h)
                            window.igk.winui.saveHistory(h);
                    }
                }
            },
            initnode: function(c) { // initnode utility fonction. in ajx context. accept only dom tag node 
                if (!c)
                    return;
                if (typeof(c.nodeType) == "undefined") {
                    throw new Error("BAD dom node initialization ... element is not a node : " + c);
                }

                function bindNode(n) {
                    this.c = n;
                    var q = this;
                    igk.appendProperties(this, {
                        init: function() {
                            // + -- -
                            // + because of the readyState will initialize all "component" on 'complete' no need to call init node
                            // + -- - 
                            _initnode(q.c);
                        }
                    });
                };
                if (document.readyState != 'complete') {
                    igk.ready(new bindNode(c).init);
                    return;
                }
                _initnode(c);
            },
            setHost(h) {
                if (typeof(h) == 'string') {
                    m_ajx_host = igk.dom.body().qselect(h);
                } else {
                    m_ajx_host = h;
                }
            }
        });
    })();
    igk.animation.constants = {
        gL: "gotoleft",
        gR: "gotoright",
        gT: "gototop",
        gB: "gotobottom",
        DIR_VERTICAL: "vertical",
        DIR_HORIZONTAL: "horizontal"
    };

    function igk_setcss_bound(i, rect) {
        $igk(i).setCss({ "left": rect.x + "px", "top": rect.y + "px", "width": rect.width + "px", "height": rect.height + "px" });
    }

    function igk_debug_show_heararchi(node) {
        var q = frm;
        while (q) {
            igk.console_debug(q.tagName + ":" + q.id);
            q = q.parentNode;
        }
    }

    function igk_debug(msg) {
        if (igk.DEBUG) {
            igk.console_debug(msg);
        }
    }

    function igk_debug_t(tag, msg) {
        if (igk.DEBUG) {
            igk.console_debug("[" + tag + "]-" + msg);
        }
    }

    createNS("igk.utils", {
        getv: igk_getv,
        get_form_posturi: igk_get_form_posturi,
        submit_fromenter: function(k, evt) {
            if (evt.keyCode && (evt.keyCode == 13)) {
                k.form.submit();
                return !1;
            }
            return !0;
        },
        treatBodyContent: function(txt) {
            if (!txt)
                throw ("/!\\ txt not define");
            return txt.replace(/(<\/?)body( .+?)?>/gi, '$1IGK-BODY$2>', txt);
        },
        getBodyContent: function(text, bonly) {
            var s = "";
            var t = igk.createNode("div");
            if (text) {
                s = igk.utils.treatBodyContent(text);
                t.setHtml(s);
            }
            var tt = t.getElementsByTagName("IGK-BODY");
            if (tt.length == 1) {
                // body tag found return inner content
                var out = tt[0].innerHTML;
                return out;
            }
            if (bonly)
                return null;
            return s;
        }
    });
    igk.utils.Base64 = {
        // private property
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
        // public method for encoding
        encode: function(input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;
            input = igk.utils.Base64._utf8_encode(input);
            while (i < input.length) {
                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);
                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;
                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }
                output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
            }
            return output;
        },
        // public method for decoding
        decode: function(input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;
            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
            while (i < input.length) {
                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));
                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;
                output = output + String.fromCharCode(chr1);
                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }
            }
            output = igk.utils.Base64._utf8_decode(output);
            return output;
        },
        // private method for UTF-8 encoding
        _utf8_encode: function(string) {
            if (!string)
                return null;
            string = string.replace(/\r\n/g, "\n");
            var utftext = "";
            for (var n = 0; n < string.length; n++) {
                var c = string.charCodeAt(n);
                if (c < 128) {
                    utftext += String.fromCharCode(c);
                } else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                } else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
            }
            return utftext;
        },
        // private method for UTF-8 decoding
        _utf8_decode: function(utftext) {
            var string = "";
            var i = 0;
            var c = 0;
            var c1 = 0;
            var c2 = 0;
            while (i < utftext.length) {
                c = utftext.charCodeAt(i);
                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                } else if ((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i + 1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                } else {
                    c2 = utftext.charCodeAt(i + 1);
                    c3 = utftext.charCodeAt(i + 2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }
            }
            return string;
        }
    };

    function igk_check_change(uri, every) {
        var obj = {
            uri: uri,
            interval: every,
            doaction: function() {
                var self = this;
                igk.ajx.post(uri, null, function(xhr) {
                    if (this.isReady()) {
                        if (xhr.responseText.length > 0) {
                            var q = igk.createNode("data");
                            // q.innerHTML=xhr.responseText;
                            igk_remove_all_script(q);
                            var tdata = q.getElementsByTagName("data");
                            if (tdata.length != 1) {
                                var t = document.getElementById("changeresponse");
                                if (t == null) {
                                    t = igk.createNode("div");
                                    t.id = "changeresponse";
                                    igk.dom.body().appendChild(t);
                                }
                            }
                            setTimeout(function() { self.doaction(); }, self.interval);
                        }
                    }
                });
            }
        };
        obj.doaction();
    }

    function igk_confirm_article_del(item) {
        if (confirm("voulez vous vraiment supprimer l'article ?\nNote: la suppression es irréversible")) {
            item.href = item.href + '&confirm=1';
            return !0;
        }
        return !1;
    }
    // check all element in target
    function igk_check_all(parent, node, value, toggle, target) {
        if (node == null) {
            console.debug("igk_check_all: node is null. perhaps IGKcontext failed");
            return;
        }
        var t = null;
        if (!target)
            t = $igk(node).select("input");
        else
            t = $igk(node).select(target);

        function __update() {
            if ((this.o != parent) &&
                (this.o.type == "checkbox")) {
                if (toggle) {
                    if (this.o.checked == value) {
                        this.o.checked = !value;
                    } else {
                        this.o.checked = value;
                    }
                } else {
                    this.o.checked = value;
                }
            }
            return !0;
        };
        if (t) {
            if (t.isSr())
                t.each(__update);
            else {
                __update.apply(t);
            }
        }
    }
    // preloaded functions
    var m_preloadFunctions = [];

    function __applyPreloadDocument(document) {
        for (var i = 0; i < m_preloadFunctions.length; i++) {
            m_preloadFunctions[i].apply(document);
        }
    };

    function __registerHtmlPreloadDocumentCallBack(name, callback) { // single callback
        if (callback && name) {
            if (!m_preloadFunctions[name]) {
                // m_preloadFunctions.push(callback);
                // m_preloadFunctions[name]=callback;
                var c = {
                    funcs: [],
                    funcCount: function() {
                        return this.funcs.length;
                    },
                    apply: function(target) {
                        for (var i = 0; i < this.funcs.length; i++) {
                            this.funcs[i].apply(target);
                        }
                    },
                    add: function(callback) {
                        if (callback) {
                            this.funcs.push(callback);
                        }
                    },
                    remove: function(callback) {
                        for (var i = 0; i < this.funcs.length; i++) {
                            if (callback == this.funcs[i]) {
                                this.funcs.pop(this.funcs[i]);
                            }
                        }
                    },
                    toString: function() {
                        return "functionRegister";
                    }
                };
                m_preloadFunctions[name] = c;
                m_preloadFunctions.push(c);
            }
            var s = m_preloadFunctions[name];
            s.add(callback);
        }
    };

    function __unregisterHtmlPreloadDocumentCallBack(name, callback) {
        if (m_preloadFunctions[name]) {
            m_preloadFunctions.remove(callback);
        }
    }
    // configuration menu management
    createNS("igk.configmenu", {
        init: function(p) {
            var t = p.getElementsByTagName("a");
            var a = null;
            for (var i = 0; i < t.length; i++) {
                a = t[i];
                a.onclick = function() { return igk.configmenu.navigate(this, this.getAttribute('page')); };
            }
        },
        navigate: function(i, p) {
            var frm = igk.getParentById(i, 'form');
            if (frm == null) return;
            frm.action = i.href;
            frm.menu.value = p;
            frm.submit();
            return !1;
        }
    });

    createNS('igk.ctrl', {
        bindPreloadDocument: __registerHtmlPreloadDocumentCallBack
    });
    createNS("igk.ctrl.utils", {
        check_all: igk_check_all
    });

    // page editor entity
    createNS("igk.winui.gui", {
        rectangleSnippet: function(o) {
            this.o = o;
            this.bound = new igk.math.rectangle(0, 0, 8, 8);
            this.view = igk.createNode("div");
            $igk(this.view).setCss({ "position": "absolute", "border": "1px solid black", "backgroundColor": "transparent" });
            $igk(this.view).setProperties({ "class": "dark_op90" });
            igk_setcss_bound(this.view, this.bound);
            this.o.view.appendChild(this.view);
            this.setBound = function(rc) {
                this.bound = rc;
                igk_setcss_bound(this.view, this.bound);
            }
        }
    });
    createNS("igk.winui.rectangleSelector", {
        init: function(target) {
            if (!target)
                return null;
            $igk(target).setCss({ "position": "absolute", "left": "0px", "top": "0px", "width": "400px", "height": "400px", "border": "1px dotted gray", "backgroundColor": "transparent", "zIndex": "500" });
            var m_bound = new igk.math.rectangle();
            $igk(m_bound).setProperties({ x: 10, y: 10, width: 400, height: 250 });

            function __updatesize() {
                var m_rc = 0;
                var m_snippets = this.snippets;
                igk_setcss_bound(this.view, this.bound);
                m_rc = new igk.math.rectangle(0 + this.bound.width / 2, 0, 0, 0);
                m_rc.inflate(4, 4);
                m_snippets[0].setBound(m_rc);
                m_rc = new igk.math.rectangle(0 + this.bound.width, this.bound.height / 2, 0, 0);
                m_rc.inflate(4, 4);
                m_snippets[1].setBound(m_rc);
                m_rc = new igk.math.rectangle(0 + this.bound.width / 2, +this.bound.height, 0, 0);
                m_rc.inflate(4, 4);
                m_snippets[2].setBound(m_rc);
                m_rc = new igk.math.rectangle(0, +this.bound.height / 2, 0, 0);
                m_rc.inflate(4, 4);
                m_snippets[3].setBound(m_rc);
            };

            function __initSippets(t) {
                var m_snippets = new Array();
                m_snippets[0] = new igk.winui.rectangleSnippet(t);
                m_snippets[1] = new igk.winui.rectangleSnippet(t);
                m_snippets[2] = new igk.winui.rectangleSnippet(t);
                m_snippets[3] = new igk.winui.rectangleSnippet(t);
                return m_snippets;
            }
            // igk.dom.body().appendChild(this.view);
            return new function() {
                this.view = target;
                this.bound = m_bound;
                this.update = __updatesize;
                this.toString = function() { return "igk.winui.rectangleSelector"; };
                this.snippets = __initSippets(this);
                this.update();
            };
        }
    });
    // for igk.css compatibility utility
    createNS("igk", {
        css: new(function() {
            // var props = {};
            // var domProp = null;
            // var vendors = ['webkit', 'ms', 'o'];
            // function getchars(x) {
            // 	var t = [];
            // 	for (var i = 0; i < x.length; i++)
            // 		t.push(x[i]);
            // 	return t;
            // }
            // function getstring(ch) {
            // 	var t = "";
            // 	for (var i = 0; i < ch.length; i++)
            // 		t += ch[i];
            // 	return t;
            // }
            // function __setProperty(item, properties) {
            // 	var _navsupport = igk.navigator.isIE();
            // 	if (item && item.style && properties) {
            // 		for (var i in properties) {
            // 			try {
            // 				if (i.startsWith("--")) {
            // 					//ie 11 not supporting custom data on css								
            // 					item.style.setProperty(i, properties[i]);
            // 					if (!_navsupport)
            // 						continue;
            // 				}// else
            // 				item.style[i] = properties[i];
            // 			}
            // 			catch (ex) {
            // 				// boxSizing cause error					
            // 			}
            // 		}
            // 	}
            // 	else {
            // 		console.debug('[BJS] -/!\v properties ' + item + ' not defined');
            // 	}
            // }
            // // load dummy css style properties
            // var xdum = igk.createNode("div", igk.namespaces.xhtml);
            // var dum = xdum.o;
            // var l = false;
            // if (dum.style) {
            // 	for (var i in dum.style) {
            // 		if (typeof (dum.style[i]) != IGK_FUNC) {
            // 			switch (i) {
            // 				case 'cssText':
            // 				case 'length':
            // 				case 'parentRule':
            // 					continue;
            // 			}
            // 			// 
            // 			// firefox implement some property with - symbol ignore them
            // 			// 
            // 			if (i.indexOf('-') != -1)
            // 				continue;
            // 			props[(i + '').toLowerCase()] = i;
            // 			l = !0;
            // 		}
            // 	}
            // }
            // // delete(dum);
            // // load css from dummy style resolving the safary error
            // if (!l && window.getComputedStyle) {
            // 	var txt = window.getComputedStyle(dum).cssText;
            // 	if (txt) {
            // 		var tab = txt.split(';');
            // 		for (var i = 0; i < tab.length; i++) {
            // 			var s = tab[i].split(':')[0]; // first word
            // 			var d = getchars(s);
            // 			var index = 1;
            // 			if (s[0] == '-') {
            // 				index = 2;
            // 			}
            // 			else {
            // 				// replace all next segment width uppercase layer
            // 			}
            // 			while (index > 0) {
            // 				index = s.indexOf('-', index);
            // 				if (index == -1)
            // 					break;
            // 				if (index + 1 < s.length) {
            // 					d[index + 1] = (s[index + 1] + '').toUpperCase();
            // 				}
            // 				index++;
            // 			}
            // 			s = getstring(d).replace(/( |\-)/g, "");
            // 			if (typeof (props[s.toLowerCase()]) == IGK_UNDEF)
            // 				props[s.toLowerCase()] = s;
            // 		}
            // 	}
            // }
            // // animation and transition
            // var e = ['animation', 'transition'];
            // var v = vendors;
            // // checking global prop
            // for (var i = 0; i < v.length; i++) {
            // 	for (var j = 0; j < e.length; j++) {
            // 		var s = (v[i] + e[j]).toLowerCase();
            // 		if ((typeof (props[s]) == IGK_UNDEF) && props[s + "delay"]) {
            // 			props[s] = v[i] + e[j][0].toUpperCase() + e[j].substring(1);
            // 		}
            // 	}
            // }
            // function __getStyleValue(stylelist, n) {
            // 	switch (n.toLowerCase()) {
            // 		case "transition":
            // 			var s = stylelist[n];
            // 			if (!igk.isUndef(s) && s.length > 0) // you specify a transition. get by chrome
            // 				return s;
            // 			// other navigation join property style
            // 			var v_p = ['property', 'duration', 'timing-function', 'delay'];
            // 			var v_v = vendors;
            // 			var v_k = "";
            // 			var v_prop = {
            // 				toString: function () {
            // 					var t = v_prop.property;
            // 					var di = v_prop.duration;
            // 					var tf = v_prop["timing-function"];
            // 					var dl = v_prop.delay;
            // 					var s = "";
            // 					if (!igk.isUndef(t)) {
            // 						for (var i = 0; i < t.length; i++) {
            // 							if (i > 0) {
            // 								s += ',';
            // 							}
            // 							s += t[i] + " " + di[i] + " " +
            // 								tf[i] + " " +
            // 								dl[i]
            // 								;
            // 						}
            // 					}
            // 					return s;
            // 				}
            // 			};
            // 			var v_t = 0;
            // 			var v_splitcsss_pattern = "([^,(]+(\\(.+?\\))?)[\\s,]*";
            // 			// for standard
            // 			for (var i = 0; i < v_p.length; i++) {
            // 				v_k = n + "-" + v_p[i];
            // 				if ((i == 0) && (typeof (stylelist[v_k]) != igk.constants.undef))
            // 					v_t = 1;
            // 				if (!v_t)
            // 					break;
            // 				s += ((i > 0) ? " | " : "") + stylelist[v_k];
            // 				v_prop[v_p[i]] = igk.system.regex.split(v_splitcsss_pattern, stylelist[v_k]);
            // 			}
            // 			if (!v_t) {
            // 				// find througth specification
            // 				v_t = 0;
            // 				for (var j = 0; j < v_v.length; j++) {
            // 					v_prop[v_v[j]] = {};
            // 					if (v_t)
            // 						s += "|";
            // 					for (var i = 0; i < v_p.length; i++) {
            // 						v_k = v_v[j] + n + "-" + v_p[i];
            // 						s += stylelist[v_k];
            // 						if (!igk.isUndef(stylelist[v_k])) // style found ..
            // 							v_prop[v_p[i]] = igk.system.regex.split(v_splitcsss_pattern, stylelist[v_k]);
            // 					}
            // 					v_t = 1;
            // 				}
            // 			}
            // 			return v_prop.toString();
            // 			break;
            // 		default:
            // 			return stylelist[n];
            // 	}
            // }
            // css utility properties
            igk.appendProperties(this, {
                getProperties: function() {
                    return props;
                },
                getDomStyle: function() {
                    if (domProp == null) {
                        //generate
                        domProp = {};
                        var rb = igk.createNode("body"); //initialize
                        rb.setCss({ "display": "none", "clear": "both", "width": "auto" });
                        document.body.appendChild(rb.o);
                        for (var i in props) {
                            domProp[i] = rb.getComputedStyle(props[i]);
                            if (i == "width") {
                                console.debug(i + " x = " + domProp[i]);
                            }
                        }
                        rb.remove();
                    }
                    return domProp;
                },
                // getStyleValue: function (stylelist, n) {
                // 	// get css style list value
                // 	// @stylelist: get width getComputedStyle function
                // 	// @n : the name of the property to get
                // 	return __getStyleValue(stylelist, n);
                // },
                getValue: function(item, name) {
                    var tprops = this.getProperties();
                    var d = igk.createNode("div");
                    // for(var  i in tprops)
                    // {
                    // d.add("div").addClass("igk-col-lg-12-3").setHtml(i + " : "+tprops[i]);
                    // }
                    // igk.show_notify_msg("properties", d);
                    var kr = tprops[name.toLowerCase()];
                    if (tprops[kr]) {
                        return item.style[tprops[kr]];
                    }
                    return item.style[name];
                },
                // getVendors: function () {
                // 	return vendors;
                // },
                // isItemSupport: function (names) {
                // 	if (typeof (names) == 'string') {
                // 		var s = names.toLowerCase();
                // 		return s in props;
                // 	}
                // 	for (var i = 0; i < names.length; i++) {
                // 		if (typeof (props[names[i].toLowerCase()]) != IGK_UNDEF)
                // 			return !0;
                // 	}
                // 	var s = dum.style;
                // 	if (s) {
                // 		// for safari
                // 		for (var i = 0; i < names.length; i++) {
                // 			if (typeof (s[names[i]]) != IGK_UNDEF)
                // 				return !0;
                // 		}
                // 	}
                // 	return !1;
                // },
                // setProperty: function (item, name, value) {
                // 	var k = {};
                // 	var n = null;
                // 	if (igk.css.isItemSupport(['webkit' + name])) {
                // 		n = props[('webkit' + name).toLowerCase()];
                // 		k[n] = value;
                // 		i
                // 	}
                // 	else if (igk.css.isItemSupport([name])) {
                // 		n = props[name.toLowerCase()];
                // 		k[n] = value;
                // 		// that notation work only for firefox
                // 		// item.setCss({[n]: value});						
                // 	}
                // 	// setting real value
                // 	__setProperty(item, k);
                // },
                // setProperties: function (item, properties) {
                // 	if ((item == null) || (!item.style)) {
                // 		return;
                // 	}
                // 	var k = {};
                // 	var n = null;
                // 	var v = null;
                // 	for (var ni in properties) {
                // 		if (typeof (ni) != 'string')
                // 			continue;
                // 		if (ni.startsWith("--")) {
                // 			k[ni] = properties[ni];
                // 			continue;
                // 		}
                // 		v = properties[ni];
                // 		if (igk.css.isItemSupport(['webkit' + ni])) {
                // 			n = props[('webkit' + ni).toLowerCase()];
                // 			if (n)
                // 				k[n] = v;
                // 		}
                // 		else if (igk.css.isItemSupport([ni])) {
                // 			n = props[ni.toLowerCase()];
                // 			if (n) {
                // 				k[n] = v;
                // 			}
                // 		}
                // 	}
                // 	// setting real value		
                // 	__setProperty(item, k);
                // },
                // setProperty: function (item, name, value) {
                // 	var k = {};
                // 	var n = null;
                // 	if (igk.css.isItemSupport(['webkit' + name])) {
                // 		n = props[('webkit' + name).toLowerCase()];
                // 		k[n] = value;
                // 		i
                // 	}
                // 	else if (igk.css.isItemSupport([name])) {
                // 		n = props[name.toLowerCase()];
                // 		k[n] = value;
                // 		// that notation work only for firefox
                // 		// item.setCss({[n]: value});						
                // 	}
                // 	// setting real value
                // 	__setProperty(item, k);
                // },
                // setTransitionDuration: function (item, time) {
                // 	this.setProperty(item.o, 'transitionduration', time);
                // 	return this;
                // }, 
                // setTransitionDelay: function (item, time) {
                // 	this.setProperty(item.o, 'transitiondelay', time);
                // 	return this;
                // },
                // changeStyle: function (name, style) {// change css style definition
                // 	function changeStyle(selectorText) {
                // 		var theRules = new Array();
                // 		if (document.styleSheets[0].cssRules) {
                // 			theRules = document.styleSheets[0].cssRules;
                // 		}
                // 		else if (document.styleSheets[0].rules) {
                // 			theRules = document.styleSheets[0].rules;
                // 		}
                // 		for (n in theRules) {
                // 			if (theRules[n].selectorText == selectorText) {
                // 				theRules[n].style = style;
                // 			}
                // 		}
                // 	}
                // }
            });
            return this;
        })()
    });

    function _is_integer(n) {
        return parseInt(n) + "" == n + "";
    };
    // utility function 
    // createNS("igk", {
    // getQueryOptions: function(){
    // },	
    // isInteger: function (n) {
    // var f = Number.isInteger || _is_integer;
    // return f(n);
    // }
    // });
    // ----------------------------------------------------------------------------------
    // register node mecanism 
    // ----------------------------------------------------------------------------------
    // manage tag component
    const _ready_func = function() {
        var t = this.tagName ? this.tagName.toLowerCase() : "";
        if (m_tag_obj[t] && m_tag_obj[t].func) {
            m_tag_obj[t].func.apply(this);
        }
    };

    if (igk.ctrl.registerReady) {
        igk.ctrl.registerReady(_ready_func);
    } else {
        $igk(igk.winui.events.global()).reg_event('igk_controller_ready', function() {
            igk.ctrl.registerReady(_ready_func);
        });
    }


    // ----------------------------------------------------------------------------
    // balafon js utility fonction
    // ----------------------------------------------------------------------------
    var igk_rmScript = function() {
        var c = igk_getCurrentScript();
        if (c && !c.src) {
            $igk(c).remove();
        }
    };
    createNS("igk.balafonjs.utils", {
        closeDialog: function() {
            igk_rmScript();
        },
        closeNotify: function(g) {
            igk.winui.notify.close();
            if (g)
                igk_rmScript();
        }
    }, {
        desc: 'balafon utility functions namespace'
    });
    //encapsulate event source for best handling behaviour
    createNS("igk.winui.eventArgs", {
        progress: function(evt) {
            var q = this;
            var m_evt = evt;
            igk.appendProperties(this, {
                getEventSource: function() { return m_evt; },
                getTotalSize: function() { return m_evt.TotalSize; },
                getUploadedSize: function() { return m_evt.position; }
            });
        }
    });
    // manage drag and drop operations
    // drag drop properties descriptions
    // {
    // uri: cibling uri
    // 
    // }
    (function() {
        var m_f = [];
        var m_i = null;

        function _get_parentscroll(p) {
            var q = $igk(p);
            var c = q.getscrollParent();
            if (p && p.target) {
                c = q.select(p.target);
            } else {
                c = q.select('^.igk-parentscroll').getNodeAt(0);
            }
            return c;
        }

        function fixscrollmanager(t, p, c) {
            var q = $igk(t);
            $igk(c).reg_event("scroll", function(evt) {
                if (p && p.update) {
                    p.update.apply(q, [{
                        parent: c,
                        scroll: { x: c.scrollLeft, y: c.scrollTop }
                    }]);
                }
            });
            return this;
        };
        createNS("igk.winui.fn.fixscroll", {
            init: function(p) { // init with property {update:function}
                var q = igk.getParentScript();
                igk.ready(function() {
                    if ((q == null) || igk.system.array.isContain(m_f, q)) {
                        return;
                    }
                    var c = _get_parentscroll(q);
                    if (c) {
                        m_f.push(q);
                        return new fixscrollmanager(q, p, c);
                    }
                });
            }
        });
    })();
    createNS("igk.storage", { // used to store shared data
    });
    createNS("igk.winui.fn", {
        cancelEventArgs: function(evt) {
            evt.preventDefault();
            evt.stopPropagation();
            return !1;
        },
        close_all_frames: function() {
            igk.winui.notify.closeAlls();
            igk.ready(function() {
                var t = [];
                igk.dom.body().select(".igk-notify-box").each_all(function() {
                    var q = this;
                    q.remove();
                });
            });
        },
        isNodeVisible: function(n) {
            throw "not implement[isNodeVisible]";
        },
        navigateTo: function(t, pr) {
            var v_t = null;
            return function() {
                var c = null;
                if (typeof(t) == 'string') {
                    c = $igk(t).first();
                } else {
                    c = $igk(t);
                }
                if (c) {
                    var p = $igk(c.o.offsetParent);
                    if (p) {
                        p.scrollTo(c, pr);
                    }
                } else {
                    igk.show_notify_error("Item not found", t);
                }
            };
        }
    });
    (function() { // igk resources manager
        var keys = {};
        var m_lang = {
            fr: ['fr', 'fr-FR', 'fr-Be'],
            en: ['en', 'en-En', 'en-US']
        };
        var _res = {};

        function init_res(_res, loc) {
            igk.appendProperties(_res, {
                getLang() {
                    var s = igk.navigator.getLang().split(',')[0];
                    for (var i in m_lang) {
                        if (igk.system.array.isContain(m_lang[i], s))
                            return i;
                    }
                    return s;
                },
                gets: function(k) {
                    if (__lang[k]) {
                        return __lang[k];
                    }
                    return k;
                },
                format: function(n) {
                    var txt_ = n;
                    if (/(string)/.test(typeof(_res[n])))
                        txt_ = _res[n];
                    if (arguments.length > 1) {
                        var tab = igk.system.array.slice(arguments, 1);
                        txt_ = txt_.replace(/\{([0-9]+)\}/g, function(m, s) {
                            return tab[s];
                        });
                    }
                    //format string 
                    return txt_;
                },
                getLocation: function() {
                    return loc;
                },
                "__": function(n) {
                    if (n in igk.R) {
                        return igk.R[n];
                    }
                    return n;
                }
            });
        };
        igk.defineProperty(igk, 'R', {
            get: function() {
                return _res;
            }
        });

        function _loadResource(loc, mime, opts) {
            mime = mime || "text/plain";
            var _getRes = 0;
            var _src = ["_getRes = async function (){",
                "try{",
                "var o  = await igk.ajx.asyncget(loc,null, mime);",
                "o  = o ? opts.then(o) : null;",
                "return  o;",
                "}catch(ex){",
                " console.error('get resource failed: '+loc+ '\nMessage:'+ex.getMessage());",
                "}",
                "return null;",
                "};"
            ].join(" ");
            try {
                if (igk.navigator.isSafari())
                    throw ('safari not handle aync await');
                eval(_src);
            } catch (ex) { // do not support async data 
                _getRes = function() {
                    var _ext = igk.navigator.isIE() ? ".ejson" : ".json"; // because of mime type        
                    var e = {
                        "then": function(t) {
                            this.__then__ = t;
                            return this;
                        },
                        "catch": function(t) {
                            this.__catch__ = t;
                            return this;
                        }
                    };
                    igk.system.io.getData(loc, function(o) {
                        if (e.__then__ && o.data && (o.data.length > 0)) {
                            e.__then__(opts.then(o.data));
                        } else if (e.__catch__) {
                            e.__catch__("/!\\ failed: " + loc);
                        }
                    }, "application/xml");
                    return e;
                };
            }
            if (typeof(_getRes) != 'undefined') {
                _getRes().then(function(o) {
                    _res = o;
                    init_res();
                })["catch"](function(e) {
                    var error = 1;
                    if (igk.log)
                        igk.log.write("[BJS] - " + e);
                });
            }
        };
        igk.ready(function() {
            var _lang = igk.dom.html().getAttribute("lang") || igk.navigator.getLang();
            //if windows application 
            // var _ext = ".json";
            var dir = igk.system.io.getlocationdir(igk.getScriptLocation());
            if (!dir)
                return;
            var loc = igk.resources.getLangLocation(dir, _lang);
            if (igk.resources.lang[_lang]) {
                init_res(igk.resources.lang[_lang], loc);
                return;
            }
            // TODO: Load language resources files ::::: in productions
            var g = null;
            var _storage = window.localStorage;
            if (typeof(_storage) != 'undefined')
                g = _storage.getItem("lang/" + _lang); //store global language items
            if (g == null) {
                _loadResource(loc, "text/json", {
                    then: function(o) {
                        if (_storage)
                            _storage.setItem("lang/" + _lang, o);
                        return igk.JSON.parse(o);
                    }
                });
            } else {
                igk.invokeAsync(function() {
                    _res = igk.JSON.parse(g);
                    init_res(_res, loc);
                });
            }
        });
    })();
    (function() {
        function __sendFile(evt, target, async, update, startCallback, progressCallback, doneCallBack) {
            var p = target.getProperties();
            if (p.buzy) {
                return;
            }
            p.buzy = !0;
            if (evt.dataTransfer && igk.system.array.isContain(evt.dataTransfer.types, "Files")) {
                var _t = evt.dataTransfer.files.length;
                var _m = _t;
                p.total = _t;
                p.current = 0;
                var _r = function() {
                    _t--;
                    if (_t <= 0) {
                        p.buzy = null;
                        p.current = 0;
                    } else {
                        p.current = _m - _t;
                    }
                    doneCallBack.apply(this, arguments);
                };
                for (var i = 0; i < _m; i++) {
                    if (target.support(evt.dataTransfer.files[i].type)) {
                        igk.ajx.uploadFile(null, evt.dataTransfer.files[i], target.getUri(), async, update, startCallback, progressCallback, _r /*doneCallBack*/ );
                    } else {
                        //not supported to reduce the total of file upload
                        _t--;
                        p.total = _t;
                    }
                }
            } else {
                console.debug(evt.dataTransfer.types);
                igk.winui.notify.showMsBox("Warning", "[ BJS ] - Send file to serveur failed", "warning");
            }
        };
        // 
        // utility function for dropting file
        // 
        createNS("igk.winui.dragdrop.fn", {
            upload_file: function(evt) { // upload file
                evt.preventDefault();
                evt.stopPropagation();
                // important must stop propagation 
                var target = this;
                var p = target.getProperties();
                __sendFile(evt, target, p.async, p.update, p.start, p.progress, p.done);
            }
        });
    })();

    // ns uri
    // (function() {
    //     createNS("igk.io.file", {
    //         // / TODO::check to download data builded with javascript
    //         download: function(t, n, v) {
    //             // t: mime-type image/png
    //             // n: name
    //             // v: value 
    //             var a = igk.createNode("a");
    //             var data = new Blob([v], { "type": t });
    //             igk.dom.body().appendChild(a.o); // not require in IE 
    //             a.o.download = n || "file.data"; // f
    //             a.o.href = URL.createObjectURL(data);
    //             a.o.type = t;
    //             a.o.click();
    //             a.remove();
    //             return 1;
    //         }
    //     });

    //     // -------------------------------------------------------------------
    //     // testing xsl transformation
    //     // -------------------------------------------------------------------
    //     // xml=new ActiveXObject("MSXML2.DOMDocument");
    //     // xml.async=false;
    //     // xml.loadXML(xmltxt);
    //     // igk_show_prop(xml);
    //     // igk.io.xml.parseString("info");
    //     // var doc =igk.dom.createDocument();// document.implementation.createDocument(null,"root",null);
    //     // doc.async=false;
    //     // __igk(doc).reg_event("readystatechange",function(evt){
    //     // if(evt.readyState==4){
    //     // doc =igk.dom.createDocument();
    //     // doc.async=false;
    //     // xsl =doc.load("Lib/igk/Scripts/demo.xsl").firstChild;
    //     // }
    //     // });
    //     // igk.ready(function(){
    //     // document.write(doc.firstChild.outerHTML);
    //     // });
    //     // doc.load && doc.load("Lib/igk/Scripts/data.xml",function(evt){
    //     // var doc2 =igk.dom.createDocument();
    //     // doc2.async=false;
    //     // var	xsl=doc2.load("Lib/igk/Scripts/demo.xsl");
    //     // var ex=igk.dom.transformXSL(doc,xsl);
    //     // var ex= doc.firstChild.transformNode(xsl);
    //     // }
    //     // );
    //     // document.write(doc.firstChild);
    //     // igk.ajx.load("Lib/igk/Scripts/data.xml");
    //     // doc.open("Lib/igk/Scripts/data.xml");		
    // })();
    window.onbeforeunload = function(evt) {
        if (igk_freeEventContext) {
            // free all context
            igk_freeEventContext();
        }
    };

    function __global_ready(evt) {
        console.log('log ready changed complete ? .... ',document.readyState);
        if (document.readyState == "complete") {
            _context_ = "global_ready";
            igk.ready(null, "readystatechange", (0,()=>_context_=null)()); 
        }
    };
    igk_winui_reg_event(document, "readystatechange", __global_ready);

    function __bindbalafonjs() { // bind balafon js data type
        var q = this.o;
        var src = '"use strict"; (function(){' + q.innerHTML + '}).apply(this);';
        try {
            var _bck = m_scriptNode;
            m_scriptNode = this.o;
            (new Function(src)).apply(this);
            m_scriptNode = _bck;
        } catch (e) {
            console.debug(src);
            console.error(e);
        }
        if (q.getAttribute("autoremove")) {
            this.remove();
        }
    };
    // + -------------------------------------------------------------------------------
    // + | init balafon js script on ready
    // + |
    igk.ready(function() {
        var j = 0;
        igk.dom.body().qselect("script[type='text/balafonjs']").each_all(function() {
            __bindbalafonjs.apply(this);
            j++;
        });
    });
    // manage component
    (function() {
        var j = 0; // count number of component
        __scriptsEval['text/balafon-component'] = function() {
            var p = $igk(this.o.parentNode);
            var src = '"use strict"; (function(){' + this.o.innerHTML + '}).apply(this);';
            p.component = new Object();
            (new Function(src)).apply(p.component);
            this.remove();
            j++;
        };
        // + -------------------------------------------------------------------------------
        // + | init balafon js script on ready
        // + |
        igk.ready(function() {
            var _initComponent = __scriptsEval['text/balafon-component'];
            igk.dom.body().qselect("script[type='text/balafon-component']").each_all(function() {
                _initComponent.apply(this);
            });
        });
    })();
    igk.appendProperties(igk, {
        /**
         * get node component
         * @param {*} n dom node
         * @returns node component
         */
        component(n) {
            return igk.retrieveProperty(n, 'component');
        },
        /**
         * retrieve chain properties
         */
        retrieveProperty(n, name) {
            var g = $igk(n);
            while (g != null) {
                if (name in g) {
                    return g[name];
                }
                if ((g.o.parentNode == null) || (typeof(g.o.parentNode) == 'undefined')) {
                    break;
                }
                g = $igk(g.o.parentNode);
            }
            return null;
        }
    });
    var _udef = 'undefined';
    // special functions
    ns_igk._$exists = function(n) { return typeof(igk.system.getNS(n)) != _udef; };

})(window);
