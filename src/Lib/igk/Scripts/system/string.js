// author: C.A.D. BONDJE DOUE
// file: string.js
// @date: 20230102 14:29:06
// @desc: extends string extentions 
'use strict';
(function () {
    // php function helper
    String.prototype.ucfirst = function () {
        const m = this;
        return m.length>0?m[0].toUpperCase() + m.slice(1):m;
    };
    /**
 * remove brank expression . 
 * @param {string} src 
 * @param {number} offset 
 * @param {string} start 
 * @param {string} end 
 * @returns {string}
 */
    function removeBrank(src, offset = 0, start = '(', end = ')') {
        let depth = 0;
        let stop = false;
        let is_escaped = false;
        let _start_offset = offset;
        while (!stop && (offset < src.length)) {
            let ch = src[offset];
            offset++;
            if (!is_escaped) {
                if (ch == start)
                    depth++;
                else if (ch == end) {
                    depth--;
                    if (depth == 0) {
                        stop = true;
                        break;
                    }
                }
                else if (ch == '\\') {
                    is_escaped = true;
                }
            } else
                is_escaped = false;
        }
        if (stop) {
            src = src.substring(0, _start_offset) + src.substring(offset);
        }
        return src;
    };
    // igk.system.string for string utility used fonction
    igk.system.createNS("igk.system.string", {
        removeBrank,
        padleft: function (m, s, l) {
            while (m && (m.length < l)) {
                m = s + m;
            }
            return m;
        },
        padright: function (m, s, l) {
            while (m && (m.length < l)) {
                m = m + s;
            }
            return m;
        },
        trim: function (m) {
            if (m == null) {
                throw new igk.exception('bad');
            }
            if (m.trim)
                return m.trim();
            while (m && (m.length > 0) && (m[0] == ' ')) {
                m = m.substring(1);
            }
            while (m && (m.length > 0) && (m[m.length - 1] == ' ')) {
                m = m.substring(0, m.length - 1);
            }
            return m;
        },
        startWith: function (m, str) {
            if (m && m.slice)
                return m.slice(0, str.length) == str;
            return !1;
        },
        endWith: function (m, str) {
            return m.slice(-str.length) == str;
        },
        remove: function (m, index, length) {
            return m.substring(0, index) + m.substring(index + length, m.length);
        },
        insert: function (m, index, pattern) {
            return m.substring(0, index) + pattern + m.substring(index, m.length);
        },
        capitalize: function (s) {
            if (s && s.length > 1)
                return s[0].toUpperCase() + s.substring(1).toLowerCase();
            return s;
        }
    });
})();