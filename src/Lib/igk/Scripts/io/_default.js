'use strict';
(function() {
    //igk.io 
    igk.system.createNS("igk.io", {
        copyToClipboard: function(s) {
            var g = document.createElement("textarea");
            var selected =
                document.getSelection().rangeCount > 0 ?
                document.getSelection().getRangeAt(0) :
                !1;
            g.innerText = s;
            g.style.position = "absolute";
            g.style.left = "-9999";
            document.body.appendChild(g);
            g.select();
            document.exec("copy");
            $igk(g).remove();
            if (selected) {
                document.getSelection().removeAllRanges();
                document.getSelection().addRange(selected);
            }
        }
    });
})();