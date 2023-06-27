'use strict';
// force input auto focus on attribute
(function() {
    function __forceFocus() {
        var s = this.getAttribute("igk-input-focus");
        if (s == 1) {
            this.focus();
        }
    }
    igk.ctrl.bindAttribManager("igk-input-focus", __forceFocus);
})();