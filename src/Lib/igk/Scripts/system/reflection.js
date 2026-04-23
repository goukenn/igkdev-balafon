// @ts-nocheck
'use strict';
(function () {
    /**
     * list only balafon namespace object 
     * @param {*} n 
     * @returns 
     */
    function listReference(n) {
        let i = 0;
        let tab = [];
        let g = [n];
        let exclude = ['namespace', 'type', 'fullname', 'hierarchi'];
        while (g.length > 0) {
            n = g.shift();
            let ns = n['namespace'];
            let nt = n['type'];
            let fn = n['fullname'];
            exclude = [];
            if (ns && nt && fn) {
                tab.push(fn);
                exclude = ['namespace', 'type', 'fullname', 'getParent', 'toString', '__igk__', 'hierarchie', 'getType'];
            } else continue;
            for (i in n) {
                if (exclude.indexOf(i) != -1) {
                    continue;
                }
                let c = n[i];
                if (c && (typeof (c) == 'object'))
                    g.push(c);
                //tab.push(fn+'.'+i);
            }
        }
        tab.sort();
        return tab;
    };
    const _NS = igk.system.createNS('igk.system.reflection', {
        listReference
    });
})(); 