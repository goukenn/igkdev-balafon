// author: C.A.D. BONDJE DOUE
// file: math.js
// @date: 20230102 14:12:32
// @desc: 
'use strict';
(function() {
    const createNS = igk.system.createNS;
    // ------------------------------------------------------------------------------------
    // igk.math NAME SPACE
    // ------------------------------------------------------------------------------------
    createNS("igk.math", {
        _2PI: Math.PI * 2,
        vector2d: function(x, y) {
            return {
                x: x,
                y: y,
                sub: function(d) {
                    this.x -= d.x;
                    this.y -= d.y;
                    return this;
                },
                add: function(d) {
                    this.x += d.x;
                    this.y += d.y;
                    return this;
                },
                distance: function(d) {
                    var dx = (this.x - d.x);
                    var dy = (this.y - d.y);
                    var f = Math.sqrt((dx * dx) + (dy * dy));
                    return f;
                },
                clone: function() {
                    return igk.math.vector2d(this.x, this.y);
                },
                toString: function() { return "vector2d{x:" + this.x + ";y:" + this.y + "}" }
            };
        },
        matrix3x3: function() {
            var m_element = [];
            var MATRIX_LENGTH = 9;
            function mult_matrix(tb1, tb2) {
                var rtb = new Array(MATRIX_LENGTH);
                var k = 0;
                var offsetx = 0;
                var offsety = 0;
                var v_som = 0;
                for (var k = 0; k < MATRIX_LENGTH;) {
                    for (var i = 0; i < 4; i++) { // columns
                        v_som = 0.0;
                        for (var j = 0; j < 4; j++) {
                            offsety = (4 * j) + i; // calculate column index
                            v_som += tb1[offsetx + j] * tb2[offsety];
                        }
                        rtb[k] = v_som;
                        k++;
                    }
                    offsetx += 4;
                }
                return rtb;
            };
            igk.appendProperties(this, {
                getElements: function() {
                    return m_element;
                },
                reset: function() {
                    m_element[0] = m_element[4] = m_element[8] = 1;
                    m_element[1] = m_element[2] = m_element[3] = 0;
                    m_element[5] = m_element[6] = m_element[7] = 0;
                },
                translate: function(dx, dy, dz) {
                    m_element[6] += dx;
                    m_element[7] += dy;
                    if (dz) {
                        m_element[8] *= dz;
                    }
                },
                scale: function(ex, ey, ez) {
                    m_element[0] *= ex;
                    m_element[4] *= ey;
                    if (ez) {
                        m_element[8] *= ez;
                    }
                },
                rotate: function(angle) {},
                rotateAt: function(angle, point) {}
            });
            this.reset();
        },
        rectangle: function(x, y, w, h) {
            this.x = x ? x : 0;
            this.y = y ? y : 0;
            this.width = w ? w : 0;
            this.height = h ? w : 0;
            this.toString = function() { return "igk.math.rectangle[" + this.x + "," + this.y + "," + this.width + "," + this.height + "]"; };
            this.isEmpty = function() { return (this.width == 0) || (this.height == 0); };
            this.inflate = function(x, y) {
                this.x -= x;
                this.y -= y;
                this.width += 2 * x;
                this.height += 2 * y;
            };
        }
    });
    createNS("igk.math.vector2d", {
        empty: function() {
            return new igk.math.vector2d(0, 0);
        },
        parse: function(s) {
            if (s == null)
                return new igk.math.vector2d(0, 0);
            var t = s.split(';');
            var g = null;
            if (t.length == 2)
                g = new igk.math.vector2d(parseFloat(t[0]), parseFloat(t[1]));
            else
                g = new igk.math.vector2d(parseInt(t), parseInt(t));
            return g;
        }
    });
})();