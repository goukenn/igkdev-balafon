"use strict";
//--------------------------------------------------------------------------------------
//represent an horizontal scroll pane. used in combination of class.IGKJS_horizontalPane
//--------------------------------------------------------------------------------------
//HPane anim type supported : translation|fade|rotation

//definition of hpane
//--igk-hpane-container
//|_______>igk-pane
//|____________>igk-pane-page
//|____________>igk-pane-page
//|____________>...
//|____________>igk-pane-page
//|_______>igk-hpane-bz : bullet zone


//define option of igk-hpane-container: 
//{
//@style:'[animType]', 
//@animDuration::'auto animation duration
//@showBullet:0|1
//@showNav:0|1

//styling: 1 igk-pane overflow is hidden ! important
//for rotation

(function() {
    if (!igk || (undefined != igk.system.getNS('igk.winui.horizontalScrollPane'))){
        return;
    }
    // console.log('load scroll pane');
    // igk.debug.log('init horizontal pane');
    var g_panes = [];
    var ckeys = ['.igk-pane-page', '.igk-pane', '.hpane-bz'];
    var ifc = igk.fn.isItemStyleSupport;
    var support_transition = 0;


    igk.winui.horizontalScrollPane = function(t, options) {
        //.ctr horizontal pane contructor
        this.host = t;
        var m_init = 0; //init for left property avoid firefox flicker
        var _idx = g_panes.length;
        var pane = t.select(ckeys[1]).first();
        var bz = t.select(ckeys[2]).first();
        var _pos = 0;
        var _bullets = [];
        var opts = igk.initObj(options, {
            style: 'rotation',
            showBullets: 1,
            showNav: 1,
            animDuration: 5000,
            autoAnim: 1
        });
        var q = this; 
        var tout = 0; //timeout
        var v_observe = null; // to observe if slider is visible or not
        var v_needtoupdate= false;
        function __startAnim() {
            if (tout)
                clearTimeout(tout);

            if ((_pos + 1) < __items().getCount()) {
                q.goNext();
            } else {
                _pos = 0;
                q.scrollTo(0);
            }

        };

        function __restartAnim() {
            if (tout)
                clearTimeout(tout);
            if (opts.autoAnim) {
                tout = setTimeout(__startAnim, opts.animDuration);
            }
        }

        function __items() {
            return pane.select(ckeys[0]);
        };

        function __updateBullet() {
            if (!opts.showBullets)
                return;

            if (_bullets.active) {
                _bullets.active.rmClass("igk-active");
            }
            _bullets.active = _bullets[_pos];
            if (_bullets.active)
                _bullets.active.addClass("igk-active");
        };
        function _update_viewport(pane, c, s){
            var posx, posy;
            //this.reset();
            //var f = "translate(-"+posx+"px, "+posy+"px)";//pixel positionning failed on resize
            if (igk.navigator.isFirefox()) {
                if (!m_init) {
                    s.each_all(function() {
                        this.setCss({ "left": "0%" });
                        // console.debug("done :"+f);
                    });
                    m_init = 1;
                }

                if (c.offsetLeft != 0) {
                    var l = $igk(c).getComputedStyle('left');
                    posx = ((c.offsetLeft - igk.getNumber(l)) / pane.o.offsetWidth) * 100;
                    // return;
                    s.each_all(function() {
                        this.setCss({ "left": -posx + "%" });
                        // console.debug("done :"+posx);
                    });
                } else {
                    // mean that element is not visible ...
                    if (!v_observe && window.IntersectionObserver){
                        v_observe = new window.IntersectionObserver((entries)=>{
                            entries.forEach(
                                entry => { 
                                    if (entry.isIntersecting){
                                        if (v_needtoupdate){
                                            v_needtoupdate =false;
                                            q.scrollTo(_pos);
                                            v_observe.disconnect();
                                            v_observe = null;
                                        } 
                                    }
                                }
                            )
                        });
                        v_observe.observe(q.host.o);                        
                    }
                    v_needtoupdate = true;
                }
            } else {
                posx = 100 * c.offsetLeft / pane.o.offsetWidth;
                posy = 100 * c.offsetTop / pane.o.offsetHeight;
                var f = "translate(-" + posx + "%, -" + posy + "%)"; //pixel positionning failed on resize.use %
                s.each_all(function() {
                    this.setCss({ "transform": f });
                });
            }
        };
        igk.appendProperties(this, { //object properties
            remove: function() {
                t.remove();
            },
            goNext: function() {
                var s = pane.select(ckeys[0]);
                if ((_pos >= 0) && (_pos < s.getCount() - 1)) {
                    _pos++;
                    this.scrollTo(_pos);
                }
            },
            goPrev: function() { 
                if (_pos > 0) {
                    _pos--;
                    this.scrollTo(_pos);
                }
            },
            scrollTo: function(c) {
                var s = pane.select(ckeys[0]);
                let _pos = 0;
                if (igk.isInteger(c)) {
                    _pos = c;
                    c = s.getItemAt(c).o;
                }
                _update_viewport(pane,c,s);               
                __updateBullet();
                __restartAnim();
                q_prop.host.raiseEvent('item-changed', {index:_pos, target:q_prop.host});
            },
            reset: function() {
                if (igk.navigator.isFirefox()) {
                    __items().each_all(function() {
                        this.setCss({ "left": "0px" });
                    });
                } else {
                    __items().each_all(function() {
                        this.o.style.transform = null; //.setCss({"left":"0px"});
                    });
                }
                var fl = pane.o.scrollTo || pane.o.scroll;
                if (fl)
                    fl.apply(pane.o, [0, 0]);
                _pos = 0;
                __updateBullet();
            }
            
        });

        // public injected property 
        let q_prop = this;
        this.host.addEvent("item-changed", {});
        igk.appendProperties($igk(t), {
            selectedIndex(idx){
                _pos = idx;
                q_prop.scrollTo(idx); 
            }
        });
        g_panes[_idx] = this;


        var l = __items().getCount();
        //init bullets		
        bz.setHtml(""); //clear bullet zone
        if (opts.showBullets && (l > 1) ) {
            for (var i = 0;(i < l); i++) {
                var e = igk.createNode("div")
                    .addClass("hpane-b")
                    .reg_event('click', (function(i) {
                        return function(e) {
                            _pos = i;
                            q.scrollTo(i);
                        };
                    })(i));
                bz.add(e);
                _bullets.push(e);
            }
            __updateBullet();
        }

        if (opts.showNav) {
            //init navigation button 
            t.add("div").addClass("hpane-btn hpane-btn-n")
                .setCss({ "right": "2px", "top": "50%", "marginTop": "-24px" }).reg_event("click", function() { q.goNext(); });

            t.add("div").addClass("hpane-btn hpane-btn-p")
                .setCss({ "left": "2px", "top": "50%", "marginTop": "-24px" }).reg_event("click", function() { q.goPrev(); });
        }

        if (opts.autoAnim && (l > 1) ) {

            setTimeout(__startAnim, opts.animDuration);
        }
    };
    var _class_ = igk.winui.horizontalScrollPane;

    igk.system.createNS("igk.winui.horizontalScrollPane", {
        //global static properties
        init: function(t, options) {
            //init hpane
            var q = $igk(t);
            var pane = new _class_(q, options);
            pane.reset();
            window.pan = pane;
            return pane;
        },
        item: function(i) {
            return g_panes[i];
        }
    });

    igk.ready(function() {
        var _b = igk.dom.body();
        support_transition = ifc(_b.o, 'transition') && ifc(_b.o, 'transform');
    });



    (function() {
        // + | ------------------------------------------------------------------------
        // + | init balafon js component - igk-hpane-container 
        // + | requirement : igk-data = json data with initial properties

        igk.winui.initClassControl("igk-hpane-container", function(q) { 
            let i = this;  
            let options = JSON.parse(i.o.getAttribute('igk-data')) || {}; 
            igk.winui.horizontalScrollPane.init(i, options); 
        });

    })();

})();