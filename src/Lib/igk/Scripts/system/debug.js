'use strict';
(function() {
    // represent debug functions
    igk.system.createNS("igk.debug", {
        write: function(msg) {
            if (igk.DEBUG) {
                console.debug(msg);
            }
        },
        assert: function(c, m) { // condition ,message
            if (c) {
                console.debug(m);
            }
        },
        enable: function(f) {
            igk.DEBUG = f;
        }
    });
})();