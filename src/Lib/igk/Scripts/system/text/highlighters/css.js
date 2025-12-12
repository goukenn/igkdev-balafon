igk.system.createNS('igk.highlightjs', {
    css: function () {
        igk.highlightjs.base.apply(this);
        let c = 0;
        let state = {};
        let handle = {
            'line-comment': function (e) {
                const tb = [content];
                if (e.match.contentName)
                    tb.push(e.match.contentName);
                return cnode(e.value, tb.join(' '));
            }
        };
        igk.appendProperties(this, {
            initRegex(regex) {
                let ref = {};
                let block = regex.begin('(?:\\{)', '(?:\\})');
                regex.begin('\\/\\*', '\\*\\/', 'line-comment');
                regex.match('#([0-9a-fA-F]{6}|[0-9a-fA-F]{4}|[0-9a-fA-F]{3})', null, 'color');
                ref['len'] = regex.match('\\b(\\.[0-9]+|[0-9]+(\\.[0-9]+)?)(px|pt|em|rem|pica|%)?', null, 'length');
                ref['attribute'] = regex.createPattern({
                    'tokenID':'attribute',
                    'match':"\\b(whitespace|wait|w-resize|visible|vertical-text|vertical-ideographic|uppercase|upper-roman|upper-alpha|underline|transparent|top|thin|thick|text|text-top|text-bottom|tb-rl|table-header-group|table-footer-group|sw-resize|super|strict|static|square|solid|small-caps|separate|se-resize|scroll|s-resize|rtl|row-resize|ridge|right|repeat|repeat-y|repeat-x|relative|progress|pointer|overline|outside|outset|oblique|nowrap|not-allowed|normal|none|nw-resize|no-repeat|no-drop|newspaper|ne-resize|n-resize|move|middle|medium|ltr|lr-tb|lowercase|lower-roman|lower-alpha|loose|list-item|line|line-through|line-edge|lighter|left|keep-all|justify|italic|inter-word|inter-ideograph|inside|inset|inline-block|inline|inherit|inactive|ideograph-space|ideograph-parenthesis|ideograph-numeric|ideograph-alpha|horizontal|hidden|help|hand|groove|fixed|ellipsis|e-resize|double|dotted|distribute|distribute-space|distribute-letter|distribute-all-lines|disc|disabled|default|decimal|dashed|crosshair|collapse|col-resize|circle|char|center|capitalize|break-word|break-all|bottom|both|bolder|bold|block|bidi-override|below|baseline|auto|always|all-scroll|absolute|table|table-cell)\\b"
                });
                regex.match('#([\\w\\-]+)\\b', null, 'identifier-name');
                regex.match('\\.([\\w]+)\\b', null, 'identifier-class');
                regex.match('((~|\\+)?=|:|;|\\+|\\{|\\}|\\[|\\]|\\(|\\)|\\.|>|~|,)', null, 'operator');
                regex.match('@\\w+\\b', null, 'directive');
                regex.begin('("|\')', "\\1", 'string');
                block.tokenID = 'curl-block';

                block.beginCaptures = {
                    '0': {
                        'name': 'curl-brank start'
                    }
                };
                block.endCaptures = {
                    '0': {
                        'name': 'curl-brank end'
                    }
                };
                let prop_value = regex.createPattern({
                    'tokenID': "prod-selection",
                    'begin': "(?=:)",
                    'end': ";|(?=\\})",
                    endCaptures: {
                        "0": {
                            "name": "end-instruct"
                        }
                    },
                    patterns: [
                        regex.match(':', null, 'select-start'),
                        ref['len'],
                        ref['attribute']
                    ]
                });
                block.patterns = [
                    prop_value,
                    {
                        'match': '--[a-zA-Z][a-zA-Z\0-9]*\\b',
                        'tokenID': 'css-custom-prop'
                    },
                    {
                        'match': '[a-zA-Z][a-zA-Z\0-9]*\\b',
                        'tokenID': 'css-prop'
                    },
                    //regex.match(';', null, 'end-instruct'),
                    //regex.match(':', null, 'select-start'),
                    block
                ];
            }
        });
        /**
         * create a node 
         * @param {string} s the content to add  
         * @param {string} c the class to bind
         * @returns {string} builded html result
         */
        function cnode(s, c) {
            let n = igk.createNode('span');
            if (c)
                n.addClass(c);
            n.setHtml(s);
            return n.o.outerHTML;
        }
        const regex = new igk.system.text.RegexContainer;

        regex.captureListener = (m, { capture, e, source }) => {
            const { name } = capture;
            if (name) {
                return cnode(m, name);
            }
            return m;
        };

        const replacements = {};
        this.initRegex(regex); 
        function _handle(e) {
            const tid = e.tokenID;
            const rp = replacements[e.from];
            const { detectResult, endMatch } = e;
            const { match } = detectResult;

            let ls = detectResult.src.substr(e.from, e.to - e.from);
            let nv = '';
            // + | offset if according to value
            let toffset = 0;
            let bv = e.value;
            if (bv.trim().length == 0) {
                bv = ls;
            }
            // console.log('_handle: '+tid, e, `[${ls}]`);

            if (!e.isMatch && match && (match.length > 0)) {
                nv += e.begin;
                toffset = match[0].length;
                e.begin = '';
                detectResult.match = null;
            }
            if (rp) {
                for (let j in rp) {
                    let jj = rp[j];
                    nv += bv.substr(toffset, (rp[j].from - e.from) - toffset) + rp[j].value;
                    toffset = rp[j].to - e.from;
                }
                delete (replacements[e.from]);
            }
            if (endMatch) {
                nv += bv.substr(toffset, endMatch.index - e.from - toffset) + e.end;
                toffset = e.to;
            }
            nv += bv.substr(toffset);// - e.from);  
            e.value = nv;
            if (tid) {
                if (tid in handle) {
                    const fc = handle[tid];
                    return fc.apply(null, [e]);
                }
            }
            return cnode(e.value, tid);
        }
        function _handle_before(e, s, start) {
            let v_ots = '';
            let v_g = start || 0;
            let ts = s.substr(v_g, e.from - v_g);
            if (ts.length > 0) {
                if (ts == ' ') {
                    ts = '&nbsp;';
                }
                v_ots += ts;
            }
            return v_ots;
        }
        this.evals = function (s) {
            c++; 
            let o = { offset: 0, source: s };
            let g = null, e = null;
            if (state.e) {
                regex.continueDetect(state.e, o);
                state.e = null;
            } else {
                regex.clear();
            }
            let v_ots = '';
            let v_loffset = 0;
            let v_last = null;
            while (g = regex.detect(s, o)) {
                if (e = regex.end(g)) {
                    //  console.log(':the token:'+e.tokenID+": value : ["+e.value+"]");                
                    if (e.parent) {
                        const _type = e.detectResult.pattern.type;
                        // if (_type == 'Match'){
                        const _offset = e.parent.offset; // offset always update on each line of the selecting highlight
                        let bp = _handle(e);
                        if (!replacements[_offset]) {
                            replacements[_offset] = {};
                        }
                        replacements[_offset][e.from] = { value: bp, from: e.from, to: e.to };
                        // } else{
                        //     // begin/end | while/end

                        if (!e.missingEnd && ((e.isEOS) || (e.to == s.length))) {
                            //v_last = e;
                        }
                    } else {
                        if (!e.missingEnd) {
                            v_ots += _handle_before(e, s, v_loffset);
                            v_ots += _handle(e);
                            v_loffset = e.to;
                        }
                    }
                }

            }
            if (e && !e.isMatch && e.missingEnd) {
                let ns = s.substr(e.from);
                v_ots += _handle_before(e, s, v_loffset);
                e.value = ns;
                v_ots += _handle(e);
                state.e = v_last || e;
            } else {
                state = {};
                v_ots += s.substr(v_loffset);
            }
            const gc = '<span class="line">' + c + '</span>';
            return gc + v_ots;
        };
    }
});
