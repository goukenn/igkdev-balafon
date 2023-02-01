(function() {
    igk.system.createNS("igk.JSON", {
        init_data: function(src, str, fallback) {
            var _v = igk.JSON.parse(str);
            if ((_v != null) && (typeof(_v) == "object")) {
                for (var i in src) {
                    if (typeof(_v[i]) != igk.constants.undef) {
                        src[i] = _v[i];
                    }
                }
            } else {
                if (fallback) {
                    fallback(src);
                }
            }
            return src;
        }
    });
})();