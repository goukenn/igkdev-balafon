"use strict";
(function () { 
    const _attr = "igk:ref-attribute";
    igk.ctrl.registerAttribManager(_attr, {}); 
        igk.ctrl.bindAttribManager(_attr, function (n, v) {
            let data = JSON.parse(v) || {};
            for (let i in data) {
                let q = this.select(data[i]).first(); 
            } 
        }); 
})();