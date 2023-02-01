'use strict';
(function() {
    const createNS = igk.system.createNS;
    // define a navigator object
    igk.navigator = new(function() {
        var m_version = "1.3";
        igk.defineProperty(this, 'version', {
            get: function() { return m_version; },
            enumerable: true,
            configurable: true,
            nopropfunc: function() {
                this.m_version = m_version;
            }
        });
    })();
    // init control device
    var XBox360 = false;
    var XBoxOne = false;
    var m_navprop = {};
    var _nav = igk.navigator;
    if (/Xbox/.test(igk.platform.osAgent)) {
        if (/Xbox One/.test(igk.platform.osAgent)) {
            XBoxOne = true;
        } else {
            XBox = true;
        }
        igk.ready(function() {
            igk.dom.body().addClass("xbox" + (XBoxOne ? 'one' : ''));
        });
    }
    igk.appendProperties(_nav, { // static function
        getLang() {
            if (window.navigator.language)
                return window.navigator.language + "";
            return window.navigator.languages + "";
        },
        getProperty(n) {
            return m_navprop[n] || false;
        },
        isXBoxOne() { return XBoxOne; },
        isXBox360() { return XBox360; },
        isFirefox() {
            return igk.platform.osAgent.indexOf("Firefox/") != -1;
        },
        getFirefoxVersion() {
            var i = igk.platform.osAgent.indexOf("Firefox/");
            if (i != -1)
                return /Firefox\/([0-9]+\.[0-9]+)/.exec(igk.platform.osAgent + "")[1];
            return -1;
        },
        getUserMedia: (function() {
            return function _t() {
                var e = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;
                if (e)
                    return e.apply(navigator, arguments);
                else {
                    // alert("No getUserMedia supported");
                }
            }
        })(),
        getScreenWidth() { return igk.winui.screenSize().width; },
        getScreenHeight() { return igk.winui.screenSize().height; },
        getOrientation() { return window.orientation || 0 },
        getDevicePixelRatio() { return window.devicePixelRatio || (window.screen.availWidth / document.documentElement.clientWidth) },
        navTo: function(uri) { // navigate to that uri				
            var a = igk.createNode("a");
            a.setCss({ display: "none" });
            a.o.href = uri;
            igk.dom.body().appendChild(a.o);
            a.o.click();
        },
        logAgent() {
            var _nav = igk.navigator;
            console.debug("IsChrome: " + _nav.isChrome());
            console.debug("IsFireFox: " + _nav.isFirefox());
            console.debug("IsSafari: " + _nav.isSafari());
            console.debug("IsEdge: " + _nav.isIEEdge());
        },
        isSecure() {
            // get if this navigator is secure
            return document.location.protocol == "https:";
        },
        isChrome() {
            var ua = igk.platform.osAgent + '';
            if ((ua.indexOf("Chrome/") != -1) && /Google Inc\./.test(window.navigator.vendor)) // for chrome
            {
                return !0;
            }
            return !1;
        },
        chromeVersion() {
            if (igk.navigator.isChrome()) {
                var i = igk.platform.osAgent.indexOf("Chrome/");
                return (igk.platform.osAgent + "").substring(i + 7).split(' ')[0];
            }
            return 0;
        },
        isIE() {
            var ua = igk.platform.osAgent + '';
            if ((ua.indexOf("MSIE") != -1) || ua.indexOf("Trident/") != -1) // for ie 11
            {
                return !0;
            }
            return !1;
        },
        isIEEdge() {
            var ua = igk.platform.osAgent + '';
            if (ua.indexOf("Edge/") != -1)
                return !0;
            return !1; // igk.navigator.isIE() &&(igk.navigator.IEVersion()>=11);
        },
        getEEdgeVersion() {
            var i = igk.platform.osAgent.indexOf("Edge/");
            if (i != -1)
                return /Edge\/([0-9]+\.[0-9]+)/.exec(igk.platform.osAgent + "")[1];
            return -1;
        },
        IEVersion() {
            if (!igk.navigator.isIE())
                return -1;
            var ua = igk.platform.osAgent + '';
            var i = ua.indexOf("MSIE");
            if (i != -1) {
                return ua.substring(i).split(';')[0].split(' ')[1];
            }
            i = ua.indexOf("Trident/");
            if (i != -1) {
                var v = ua.substring(i + 8).split(';')[0];
                if (v == 7)
                    return 11;
            }
            return -1;
        },
        isAndroid() {
            // window.navigator.userAgent 
            var v = (igk.platform.osAgent + "").toLowerCase().indexOf("android");
            if (v != -1) {
                return !0;
            }
            return !1;
        },
        isIOS: function() {
            return 0;
        },
        isSafari: function() { // return real safari web browser
            var i = igk.platform.osAgent.indexOf("Safari/");
            var _nav = igk.navigator;
            if ((i != -1) && /Apple Computer, Inc\./.test(window.navigator.vendor)) {
                return !0;
            }
            return !1;
        },
        SafariVersion: function() {
            var i = igk.platform.osAgent.indexOf("Safari/");
            if (i != -1) {
                return (igk.platform.osAgent + "").substring(i + 7).split(' ')[0];
            }
            return 0;
        },
        FFVersion: function() {
            var i = igk.platform.osAgent.indexOf("Firefox/");
            if (i != -1) {
                return (igk.platform.osAgent + "").substring(i + 8).split(' ')[0];
            }
            return 0;
        },
        $ActiveXObject: function() {
            if ('ActiveXObject' in window)
                return 1;
            return 0;
        },
        toString: function() { return "igk.navigator"; }
    });
    m_navprop.cssDomRequire = _nav.isChrome() || _nav.isFirefox() || _nav.isSafari(); //element must be added to dom before getting css computed style

})();