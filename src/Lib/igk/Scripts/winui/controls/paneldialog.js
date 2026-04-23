"use strict";
(function() {
    var _dialog = [];
    var _init = 0;
    var _NS = "igk.winui.controls";
    var _FC = 0;
    function _trans_end(p) {
        return function() {
            if (!p.no_auto_remove) {
                p.remove();
            } else {
                p.addClass("dispn");
                p.rmClass("igk-hide");
            }
            p.unreg_event("transitionend", _FC);
            _FC = 0;
        };
    };
    function _closeDialog(b) {
        var p = b || _dialog.pop();
        if (p) {
            _FC = _trans_end(p);
            p.addClass("igk-hide").reg_event("transitionend", _FC);
        }
    };
    var no_close = !1;
    function _showDialog(){
        _dialog.push(this);
    };
    function _panDialogInit() {
        if (!_init) {
            igk.winui.events.regKeyPress(function(e) {
                if (e.keyCode == igk.winui.inputKeys.Escape) {
                    if (no_close) {
                        no_close = !1;
                        return;
                    }
                    if (_dialog.length > 0) {
                        _closeDialog();
                        e.stopPropagation();
                        e.preventDefault();
                    }
                }
            });
            $igk(document.body).on("click", function(e) {
                if (e.target.tagName == "INPUT") {
                    if (e.target.getAttribute("type") == "file") {
                        no_close = !0;
                    }
                    return;
                }
            });
            _init = 1;
        }
        var q = this;
        var _idx = _dialog.length - 1;
        var _container = q.select('^.igk-ajx-panel-dialog-container').first();
        q.no_auto_remove = this.supportClass('dispn');
        igk.dom.body().add(q);
        //var _rm = q.remove;
        igk.appendChain(q, 'remove', function() {
            q.unreg_event("windowresize", _updating);
        });
        var _closeBtn = this.select(".box .igk-title .close").first();
        if (_closeBtn) {
            _closeBtn.reg_event("click", function(e) {
                _closeDialog(q);
                e.stopPropagation();
                e.preventDefault();
                delete(_dialog[_idx]);
            });
        };
        function _update_size() {
            q.rmClass("inner");
            // console.debug("size changed ");
            var f = q.select(".igk-content").first();
            f.setCss({
                "height": "auto"
            });
            var H = q.o.scrollHeight;
            var h = window.innerHeight;
            var box = q.select("> .box").first();
            var c = box.getScreenLocation();
            var m = f.getScreenLocation();
            var diff = { x: c.x - m.x, y: c.y - m.y };
            // console.debug(diff);
            // console.debug(H+" vs "+h);
            if (H > h) {
                // console.debug("add class");
                q.addClass("inner inner-h");
                f.setCss({ "height": (box.height() + diff.y - 10) + "px" });
            } else {
                // console.debug("rm class");
                q.rmClass("inner inner-h");
            }
        };
        function _updating() {
            if (tm_out)
                clearTimeout(tm_out);
            // console.debug("update size");
            tm_out = setTimeout(_update_size, 500);
        };
        this.select("form").each_all(function() {
            if (this.getAttribute("igk-ajx-form")) {
                var input = document.createElement("input");
                input["type"] = "hidden";
                input["name"] = "igk-ajx-form";
                input["value"] = 1;
                this.appendChild(input);
            }
        });
        var tm_out = 0;
        q.on("windowresize", _updating);
        q.showDialog = _showDialog;
        q.closeDialog = _closeDialog;
        _update_size();
        if (!q.no_auto_remove){
            q.showDialog();
        }
    };
    var _LNS = igk.system.createNS(_NS, {
        panelDialog: function() {},
        /**
         * notify dialog
         * @param {*} t target
         * @param string defStyle extra def classes
         */
        showPanelDialog(t, defStyle, type) {
            let m = $igk(t).first();
            if (!m) {
                return;
            }
            defStyle = defStyle || "";
            type = type || "";
            let q = igk.createNode("div")
                .addClass("igk-winui-panel-dialog " + defStyle);
            let box = q.add('div').addClass('box');
            let title = box.add('div').addClass("igk-title");
            let content = box.add('div').addClass("igk-content");
            content.setHtml(m.o.outerHTML);
            let item = $igk(content.o.firstChild);
            let it = item.qselect('div.title').first();
            if (it) {
                title.setHtml(it.getHtml());
                it.remove();
            }
            let g = igk.winui.createSVGLi("drop");
            if (g) {
                title.add("a").addClass("igk-btn close igk-svg-host").setAttribute("href", "#")
                    // .on("click", ()=>_LNS.panelDialog.close() )
                    .add(g); //etHtml("close");
            }
            item.toggleClass("dispn");
            $igk(document.body).add(q);
            igk.ajx.fn.initnode(q.o);
        }
    });
    igk.appendProperties(_LNS.panelDialog, {
        close: _closeDialog
    });
    igk.winui.createPanelDialog = function(t, d) {
        var dd = igk.createNode("div");
        dd.addClass("igk-winui-panel-dialog");
        var box = dd.add("div").addClass("box");
        var tl = box.add("div").addClass("igk-title");
        tl.add("span").setHtml(t);
        var ctn = box.add("div").addClass("igk-content");
        var s = 0;
        if (d) {
            ctn.add(d);
        }
        //	if (s){
        //	if ((svgBtn == igk_getv(s, "closeBtn")){
        //		var btn = igk.createNode("div").addClass("igk-svg-lst-i").setAttribute("igk:svg-name", "drop");
        //		console.debug(btn);
        //	}
        var g = igk.winui.createSVGLi("drop");
        if (g) {
            tl.add("a").addClass("igk-btn close igk-svg-host").setAttribute("href", "#").add(g); //etHtml("close");
        }
        _panDialogInit.apply(dd);
        return dd;
    };
    igk.winui.initClassControl("igk-winui-panel-dialog", _panDialogInit);
    igk.winui.closePanelDialog = _closeDialog;
})();