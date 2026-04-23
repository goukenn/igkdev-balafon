// author: C.A.D. BONDJE DOUE
// file: dragdrop.js
// @date: 20230102 14:22:37
// @desc: 
'use strict';
(function() {
    igk.system.createNS("igk.winui.dragdrop", {
        init: function(target, properties) {
            // usage * properties
            // uri: uri,
            // update:function(evt){}  
            // enter : function(e){}
            // drop: function(e){}
            function dragdropManager(target, properties) {
                if (!target)
                    return;
                var m_target = $igk(target);
                var m_properties = properties;
                var m_q = target;
                var m_eventcontext = igk.winui.RegEventContext(m_target, $igk(m_target));
                if (m_eventcontext == null)
                    return;
                var q = this;
                var m_supportlist = null;
                igk.appendProperties(this, {
                    getUri: function() {
                        if (m_properties && m_properties.uri)
                            return m_properties.uri;
                        return null;
                    },
                    getProperties: function() {
                        return m_properties;
                    },
                    support: function(k) { // get if this dragdrop support
                        if (m_properties && m_properties.supported) {
                            if (m_supportlist == null) {
                                var e = m_properties.supported.split(",");
                                var p = new(function(e) {
                                    var m_tab = e;
                                    var m_obj = {};
                                    for (var i = 0; i < e.length; i++) {
                                        m_obj[e[i]] = i;
                                    }
                                    this.contains = function(s) {
                                        return typeof(m_obj[s]) != IGK_UNDEF;
                                    }
                                })(e);
                                m_supportlist = p;
                            }
                            return m_supportlist.contains(k);
                        }
                        return !0;
                    },
                    toString: function() {
                        return "igk.winui.dragdrop.dragdropManager";
                    }
                });
                m_eventcontext.reg_event(m_q, "dragenter", function(evt) {
                    // console.debug("drag enter");
                    evt.preventDefault();
                    if (m_properties && m_properties.enter) {
                        m_properties.enter.apply(q, arguments);
                    }
                });
                m_eventcontext.reg_event(m_q, "dragleave", function() {
                    if (m_properties && m_properties.leave) {
                        m_properties.leave.apply(q, arguments);
                    }
                });
                m_eventcontext.reg_event(m_q, "dragover", function(evt) {
                    // allow drop on item 
                    // evt.dataTransfer.effectAllowed="copy";
                    evt.preventDefault();
                    if (m_properties && m_properties.over) {
                        m_properties.over.apply(q, arguments);
                    }
                });
                // not define on firefox
                // m_eventcontext.reg_event(m_q,"dragdrop",function(evt){
                // evt.preventDefault();
                // });
                m_eventcontext.reg_event(m_q, "drop", function(evt) {
                    evt.preventDefault();
                    if (m_properties && m_properties.drop) {
                        m_properties.drop.apply(q, arguments);
                        return;
                    }
                    if (igk.system.array.isContain(evt.dataTransfer.types, "text/html")) {
                        var n = igk.createText(evt.dataTransfer.getData("text/html"));
                        m_target.appendChild(n);
                    }
                });
                // m_eventcontext.reg_event(m_q,"drag",function(evt){
                // });
                // set up
                m_target.setAttribute("draggable", false);
            }
            return new dragdropManager(target, properties);
        }
    });
    function __init_drag() {
        var opts = igk.JSON.parse(this.getAttribute("igk-draggable-data"), this);
        igk.winui.dragdrop.init(this.o, opts);
    }
    // register class
    igk.winui.initClassControl("igk-draggable", __init_drag, { desc: "draggable node" });
})();