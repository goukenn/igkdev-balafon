'use strict';
(function() {
    igk.system.createNS("igk.winui.debug", {
        addMsg: function(msg) {
            var d = $igk("igk-debugger").first();
            if (d) {
                d.setHtml(msg);
            }
        }
    });
})();