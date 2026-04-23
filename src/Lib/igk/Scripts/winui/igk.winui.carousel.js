// file: igk.winui.carousel.js
// desc: manage carousel component
// author: C.A.D. BONDJE DOUE
// copyright: MIT © 2022 igkdev.com
"use strict";
(function(){
    igk.system.createNS("igk.winui.carousel", {
        init(){
            // function used to initialize the carousel object
            var interval = this.getAttribute("igk:interval") || 6000;
            var indicators = this.o.hasAttribute("indicators");
            // var interval = this.o.hasAttribute("controls");
            var activeSlide = null;
            var index = 0;
            var q = this;
            var pe = "activeSlideChange";     
            var _items = [];
            var ptime = 0;
            if (indicators){
                this.o.removeAttribute('indicators');
                this.qselect("nav").remove();
                var nav = this.add("nav").addClass('indicators');
                var active = this.getAttribute("active") || 0;
                this.qselect(".igk-winui-carousel-slide").each_all(function(){
                    var li = nav.add("li");
                    if (active !== null){
                        if (active == 0){
                            li.addClass("igk-active");
                            active = null;
                        }
                        else {
                            active--;
                        }
                    }
                });
            }
            function startInterval(){
                if (ptime ) {
                    clearTimeout(ptime);
                }
                ptime = setTimeout( function () {
                    if (activeSlide && (_items.length > 0)){
                       var i= (activeSlide.currentIndex + 1) % _items.length; 
                       _items[i].o.click();
                    }
                }, interval);
            };
            function _setActiveSlide(a){
                activeSlide = $igk(a).addClass("igk-active");                    
                var idx = activeSlide.currentIndex;
                activeSlide.carousel.qselect(".igk-winui-carousel-slide")
                    .each_all(function(){
                        this.setCss({"transform":"translateX(calc(-100% * "+idx+"))"}); 
                });
                q.raiseEvent(pe);
            };
            this.qselect("nav > li").each_all(function(){
                this.on("click", function(){
                    if (activeSlide){
                        if (activeSlide.o == this)
                            return;
                        activeSlide.rmClass("igk-active");
                    }
                    _setActiveSlide(this);
                    startInterval();
                });
                this.carousel = q;
                this.currentIndex = index;
                _items.push(this);
                index++;
            });
            activeSlide = this.qselect("nav > li.igk-active").first();
            if (activeSlide){
                _setActiveSlide(activeSlide);
            }
            startInterval();
        }
    });
    igk.winui.initClassControl("igk-winui-carousel", igk.winui.carousel.init);
})();