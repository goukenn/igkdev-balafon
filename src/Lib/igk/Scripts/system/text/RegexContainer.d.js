'use strict';
// common js for regex definition 
(function () {
    const BEGIN_END = 'begin/end';
    const BEGIN_WHILE = 'begin/while';
    const MATCH = 'match';
    const REF_INCLUDE = 'include';
    const TREAT_OPT_REGEX = "^\\(\\?\\b(?<add>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\\b(:\\b(?<remove>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\\b)?\\)";

    function _endWhileUpdateInfo(event, e, src) {
        e.to = event.pos - 1;
        e.value = src.substring(e.from, e.to);
        return { offset: event.pos };
    }
    function _handleEndMatchRegex(p, src, line, offset, callback) {
        let treat = [[p, line, offset]];
        if (detectStartLine(p + '')) {
            const g = startLine(src, offset);
            if (g) {
                const [nline, noffset] = g;
                treat.push([p, nline, noffset]);
            }
        }
        if (detectEndLine(p + '')) {
            let idx = line.indexOf("\n");
            if (idx != -1) {
                treat.push([p, line.substring(0, idx), offset]);
            }
        }
        let m = null;
        while (treat.length) {
            const [p, line, offset] = treat.shift();
            let cm = p.exec(line);
            if (cm) {
                _updateRegexIncides(cm, offset);
                if (!m || (cm.index < m.index)) {
                    m = cm;
                }
            }
        }
        if (m)
            callback(m);
    }
    /**
     * get next explored line 
     * @param {string} line 
     * @param {number} offset if 0 return the start line 
     * @returns {undefined|[string:string,offset:number]}
     */
    function startLine(line, offset) {
        if (offset == 0) return [line, offset];
        let gs = line.slice(offset);
        let idx = gs.indexOf("\n");
        if (idx != -1) {
            let nidx = gs.indexOf("\n");
            idx += offset + 1;
            return [line.slice(idx), idx, nidx != -1 ? line.slice(idx, nidx + idx) : null];
        }
    }

    /**
     * update indice offset
     * @param {*} g 
     * @param {*} offset 
     */
    function _updateRegexIncides(g, offset) {
        g.index += offset;
        if ('indices' in g) {
            g.indices?.map(function (a) {
                a[0] += offset;
                a[1] += offset;
            });
        }
    }
    // + | ------------------------------------------------------------------------
    // + | treat regex expression 
    // + | 

    /**
     * treatet pattern regex 
     * @param {string} regex 
     * @returns 
     */
    function treatRegex(regex) {
        return removeBracketChar(removeOption(regex));
    }

    /**
     * remove brank expression . 
     * @param {*} src 
     * @param {*} offset 
     * @param {*} start 
     * @param {*} end 
     * @returns 
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
    }
    /**
     * @param {string} regex
     */
    function detectStartLine(regex) {
        regex = removeBracketChar(removeOption(regex));
        return /(?<!\\)\^/.test(regex);
    }
    /**
     * 
     * @param {*} regex 
     * @returns 
     */
    function detectEndLine(regex) {
        regex = removeBracketChar(removeOption(regex));
        return /(?<!\\)\$/.test(regex);
    }
    /**
     * 
     * @param {*} regex 
     * @returns 
     */
    function removeOption(regex) {
        if (/^\(\?/.test(regex)) {
            regex = removeBrank(regex, 0);
        }
        return regex;
    }
    /**
     * 
     * @param {*} regex 
     * @returns 
     */
    function removeBracketChar(regex) {
        let b = null;
        while (b = /(?<!\\)\[/d.exec(regex)) {
            regex = removeBrank(regex, b.index, '[', ']');
        }
        return regex;
    }


    /**
 * check that next line is empty
 * @param {string} line 
 * @param {any|{pos: number, end: number, value:string}|undefined} [param]
 * @returns {boolean}
 */
    function nextLineIsEmpty(line, param) {
        let idx = line.indexOf("\n");
        if (idx != -1) {
            // next line found check that is an empty line 
            idx++;
            let r = line.slice(idx);
            let next_line = r.indexOf("\n");
            let g = /^\s*$/.exec(next_line == -1 ? r : r.slice(0, next_line));
            if (g) {
                if (param) {
                    param.value = g[0];
                    param.pos = g.index + idx;
                    param.end = param.pos + g[0].length;
                }
                return true;
            }
        }
        return false;
    };


    const initRegexContainer = function ({ RegexDetectionInfo, RegexMatcherPattern }) {
        const igk = (() => {
            const { igk } = globalThis;
            return igk || {}
        })();




        /**
      * @typedef IRegexDetectInfo
      * @property {string} source source of detection
      * @property {string} line detect subline
      * @property {Array} matchs list of match
      * @property {number} offset offset
      * @property {*} resolutions regex resolution list
      */
        /**
         * @typedef IRegexDetectResult
         * @property {*} match match result 
         * @property {*} pattern selected pattern
         * @property {null|IRegexDetectResult} parent parent pattern 
         * @property {*} info current mark info 
         * @property {number} offset current offset position
         * @property {String} src source detection  
         * @property {RegExp|undefined} [endRegex] offset 
         * @property {RegExp|undefined} [endWhileRegex] offset 
         * @property {boolean|undefined} [isContinue] offset 
         */
        /**
         * result end definition 
         * @typedef IRegexEndDefinition 
        * @property {number} from: start index in buffered string 
        * @property {number} to: end index in buffered string 
        * @property {String} value detected value in buffered string
        * @property {String?} tokenID: pattern.tokenID,
        * @property {String?} name: pattern.name
        * @property {String?} begin begin value
        * @property {String?} end end value
        * @property {*} parent: null
        * @property {*} detectResult pattern detected result
        * @property {String?} src global source 
        * */
        /**
         * @typedef ISourceRegexMatcherDetectObject
         * @property {string} src source string
         * @property {*} match match result 
         * @property {*} parent parent 
         * @property {*} pattern pattern match 
         * @property {number} offset offset 
         * @property {RegExp|undefined} [endRegex] offset 
         * @property {boolean|undefined} [isContinue] offset 
         */
        /**
         * @typedef ISourceMatchDef
         * @property {string} src
         * @property {string} line
         * @property {Array} matchs matched list 
         * @property {*} pattern parent pattern
         * @property {*} repository repository list
         * @property {number} offset offset definition
         * @property {*} [resolutions] regex resolution list
         * @property {(info:*)=>IRegexDetectResult|undefined} createResultListener
         */
        /**
         * @typedef IHandleMatchParam
         * @property {String} source
         * @property {number} offset
         * @property {*} pattern parent pattern 
         * @property {String} line
         * @property {Array} matchs
         */
        const _NS = {
            RegexDetectionInfo,
            RegexMatcherPattern
        };
        /**
         * 
         * @param {IRegexEndDefinition} q 
         */
        function _nameIdDefinition(q) {
            const p = [];
            do {
                if (q.name) {
                    p.unshift(q.name);
                }
            }
            while (q = q.parent?.info);
            return p;
        }
        function _loadExtraProperties(inf, a) {
            // load extract properties
            let ks = Object.keys(inf);
            for (let i of ks) {
                if (/\b(type|begin|end|while|match)\b/.test(i)) {
                    continue;
                }
                inf[i] = a[i];
            }
            return inf;
        }
        /**
         * treat loaded patterh 
         * @param {*} patterns 
         * @returns 
         */
        function _treatLoadPattern(patterns, { createMatcherPattern }) {
            const tab = patterns.map((a) => {
                if ((a instanceof RegexMatcherPattern) || (
                    a instanceof RefIncludePattern
                )) {
                    return a;
                }
                const { include, begin, end, match } = a;
                const _while = a.while;
                if (include && /^#/.test(include)) {
                    return new RefIncludePattern(include.slice(1));
                }
                let inf = createMatcherPattern();
                if (match) {
                    inf.type = MATCH;
                    inf.match = match;
                } else if (begin && _while) {
                    inf.type = BEGIN_WHILE;
                    inf.begin = begin;
                    inf.while = _while;
                } else {
                    inf.type = BEGIN_END;
                    inf.begin = begin;
                    inf.end = end;
                }
                return _loadExtraProperties(inf, a);
            });
            return tab;
        }
        function _treatLoadCapture(cap) {
            if (cap instanceof RegexCaptureMatcher) {
                return cap;
            }
        }
        /**
         * create a regex expression 
         * @param {*} b 
         * @returns 
         */
        function _treatRegexMatch(b) {
            if (typeof (b) == 'string') {
                b = _treatConvertStringToRegex(b);
            }
            if (!(b instanceof RegExp)) {
                throw new Error('not a regex matching');
            }
            if (!b.hasIndices) {
                // must have the index 
                const t = (b + '');
                let s = (b + '').substring(1, t.lastIndexOf('/'));
                b = new RegExp(s, b.flags + 'd');
            }
            return b;
        };
        /**
         * convert and treat regex
         * @param {string} b 
         * @returns 
         */
        function _treatConvertStringToRegex(b) {
            const { option, regex } = _treatGetOption(b);
            if (option.indexOf('d') == -1)
                option.push('d');
            return new RegExp(regex, option.join(''));
        }
        /**
         * 
         * @param {*} e info to save 
         * @param {*} src 
         * @param {*} from 
         * @param {*} to 
         * @param {*} [prefixListener] 
         * @param {*} [type]  
         */
        function _preUpdateValue(e, src, from, to, prefixListener, type) {
            let l = src.substring(from, to);
            if (l.length > 0) {
                if (prefixListener) {
                    l = prefixListener(l, { type, info: e });
                }
                e.value += l ? l : '';
                e.to += to - from;
            }
        };
        function _postUpdateValue(listener, stub, { from, to, pattern }) {
            if (listener) {
                listener(stub, { from, to, pattern });
            }
        };
        /**
         * 
         * @param {*} e 
         * @param {*} index 
         * @param {*} src 
         * @param {*} postUpdateListener 
         */
        function _postUpdateValueLogic(e, index, src, postUpdateListener, pattern) {
            const stub = src.substring(e.to, index);
            if ((stub.length > 0) && (postUpdateListener)) {
                _postUpdateValue(postUpdateListener, stub, { src, from: e.to, to: index, pattern });
            }
        };

        /**
        * treat get options
        * @param {String} b check string
        * @returns 
        */
        function _treatGetOption(b) {
            const t = [];
            const g = b.match(TREAT_OPT_REGEX);
            let regex = b;
            if (g) {
                const _groups = g.groups;
                if (_groups) {
                    const { add, remove } = _groups;
                    const f = ['i', 'm', 'x'];
                    if (add) {
                        let rm = {};
                        if (remove) f.forEach((d) => { if (remove.indexOf(d) != -1) { rm[d] = d; } });
                        f.forEach((d) => { if (!(d in rm) && (add.indexOf(d) != -1)) t.push(d); });
                    }
                    regex = b.replace(new RegExp(TREAT_OPT_REGEX), '');
                }
            }
            return { option: t, regex };
        };
        const MATCH_RESOLUTION = {};
        /**
         * 
         * @param {*} info 
         * @param {*} b 
         * @param {*} pattern 
         * @param {*} resolutionList contain regexString=>RegExp 
         * @param {(info:*)=>IRegexDetectResult|undefined} createResultListener  create a result litener 
         */
        function _startMatch(info, b, pattern, resolutionList, createResultListener) {
            let p = null;
            if (resolutionList) {
                if (resolutionList && (b in resolutionList)) {
                    p = resolutionList[b];
                } else {
                    p = _treatRegexMatch(b);
                    resolutionList[b] = p;
                }
            } else {
                p = _treatRegexMatch(b);
            }
            if (p) {
                _handleMatch(p, { ...info, pattern }, createResultListener)
            }
        };
        /**
         * 
         * @param {RegExp} p 
         * @param {IHandleMatchParam} param 
         * @param {(info)=>IRegexDetectResult|undefined} createResultListener 
         */
        function _handleMatch(p, { source, pattern, line, offset, matchs }, createResultListener) {
            let treat = [[p, line, offset]];
            let tps = p + '';
            if (detectStartLine(tps)) {
                let pp = startLine(source, offset);
                if (pp) {
                    const [nline, noffset] = pp;
                    if (noffset != offset) {
                        treat.push([p, nline, noffset])
                    }
                }
            }

            while (treat.length > 0) {
                const [p, line, ofset] = treat.shift();
                let c = p.exec(line);
                if (c) {
                    _updateRegexIncides(c, offset);
                    let inf = createResultListener({ pattern, match: c, offset: c.index, src: source, parent: null, isContinue: false });
                    if (matchs.length == 0) {
                        matchs.push(inf);
                    }
                    else {
                        if (c.index < matchs[0].index) {
                            matchs[0] = inf;
                        }
                    }
                }
            }

        };
        /**
         * handle pattterns 
         * @param {Array} patterns 
         * @param {ISourceMatchDef} info
         * @returns {null|IRegexDetectResult}
         */
        function _handlePatterns(patterns, info) {
            const matchs = info.matchs;
            /**
             * @type {IHandleMatchParam}
             */
            const v_info = {
                source: info.src,
                offset: info.offset,
                pattern: null,
                matchs: matchs,
                line: info.line
            };
            let idx = 0;
            const { repository, createResultListener, createMatcherPattern } = info;
            const uf = { createMatcherPattern };
            patterns.forEach((pattern) => {
                if (!(pattern instanceof RegexMatcherPattern) &&
                    !(pattern instanceof RefIncludePattern)
                ) {
                    let l = _treatLoadPattern([pattern], uf);
                    if (l) {
                        l = l.pop();
                        patterns[idx] = l;
                        pattern = l;
                    }
                }
                if ('include' in pattern) {
                    let inc = pattern.include;
                    if (inc) {
                        pattern = repository[inc.slice(1)];
                    }
                }
                let v_b = _getStartRegex(pattern);
                if (v_b) {
                    v_info.pattern = pattern;
                    let p = _treatRegexMatch(v_b);
                    _handleMatch(p, v_info, createResultListener);
                }
                idx++;
            });
            return matchs.pop();
        }
        /**
         * convert match result
         * @param {String|RegExp} end 
         * @param {*} match 
         * @returns {RegExp}
         */
        function _endMatch(end, match, value) {
            if (end instanceof RegExp) {
                end = end.toString();
            }
            value = match[0] || value;
            let p = /\\(?<id>\d+)/g;
            let n_value = end.replace(p, function (s, id) {
                let ms = match[id];
                if (ms) {
                    ms = ms.replace('*', '\\*').replace('+', '\\+');
                }
                return ms;
            });
            return _treatRegexMatch(n_value);
        }
        // let m = /(jour)/d.exec("bonjour basic jour-x")
        // const l = _endMatch("\\1-x", m);
        // console.log({l });
        // throw Error('done');
        /**
         * 
         * @param {{patterns:Array, resolutions:Object, repository:Object}} param0 
         */
        function _handlePatternsGlobal({ patterns, resolutions, repository }, createResultListener) {
            let v_b = null;
            let v_resolutions = resolutions;
            const v_info = arguments[0];
            for (let i of patterns) {
                if (i.type == REF_INCLUDE) {
                    /**
                     * @type {Array|null|string}
                     */
                    let _id = /^#(.+)/.exec(i.include);
                    if (!_id) {
                        throw new Error('not a valid reference');
                    }
                    const _x = _id[1];
                    if (_x in repository) {
                        i = repository[_x];
                    } else {
                        throw new Error('repository missing definition ' + _x);
                    }
                }
                v_b = _getStartRegex(i);
                if (v_b) {
                    _startMatch(v_info, v_b, i, resolutions, createResultListener);
                }
            }
        };
        /**
         * retrieve start regex definition
         * @param {*} i 
         * @returns 
         */
        function _getStartRegex(i) {
            let v_b = null;
            switch (i.type) {
                case BEGIN_END:
                case BEGIN_WHILE:
                    v_b = i.begin;
                    break;
                case MATCH:
                    v_b = i.match;
                    break;
                case REF_INCLUDE:
                    break;
            }
            return v_b;
        }
        /**
         * init capture listener 
         * @param {*} capture 
         * @param {{captureListener:()=>string, e}} param 
         * @returns 
         */
        function _InitTreatClosure(capture, { captureListener, e, source }) {
            if (captureListener) {
                return (c) => {
                    return captureListener(c, { capture, e, source });
                }
            }
            return (c) => { return c };
        }
        /**
         * 
         * @param {Array} cap 
         * @returns 
         */
        function CreateChainList(cap) {
            let root = {};
            root.childs = [];
            root.value = cap[0];
            root.indice = cap.indices[0][0];
            root.parent = null;
            let li = [root];
            let k = 1, i = null,
                preview = null, plen = null, clen = null, v = null, child = null, ki, vparent = null;
            let indices = cap.indices.slice(0);
            let vcap = cap.slice(0);
            vcap.shift(); indices.shift();
            while (vcap.length > 0) {
                v = vcap.shift();
                i = indices.shift();
                child = {};
                child.childs = [];
                child.parent = null;
                child.value = v;
                child.indice = i[0];//v[1];
                // get child parent
                ki = k - 1;
                vparent = null;
                clen = child.indice + child.value.length;
                while ((ki >= 0) && (!vparent)) {
                    preview = li[ki--];
                    plen = preview.indice + preview.value.length;
                    if ((child.indice >= preview.indice) && (plen >= clen)) {
                        // is child of 
                        vparent = preview;
                    }
                }
                child.parent = vparent;
                if (vparent) {
                    vparent.childs.push(k);
                }
                li.push(child);
                k++;
            }
            return li;
        }
        /**
         * 
         * @param {*} captures 
         * @param {*} cap 
         * @param {*} sourceValue 
         * @param {*} option 
         * @param {*} chainList 
         * @returns 
         */
        function TreatCaptures(captures, cap, sourceValue, option = null, chainList = null) {
            chainList = chainList ?? CreateChainList(cap);
            option = { ...(() => option || {})(), ...{ source: sourceValue } };
            let offset = 0;
            const v_keys = Object.keys(captures);
            v_keys.sort();
            // ksort(captures);
            let boffset = cap.index, // [0][1],
                v = cap[0],
                lpos = 0,
                rv = '',
                spos = 0,
                select = false,
                marks = [],
                cap_treat_mark = [], mark = null, c = null, chain = null;
            function handle_mark_capture(k, mark, c, register = true) {
                if (!(typeof (mark) == 'function')) {
                    mark = _InitTreatClosure(mark, option);
                }
                chain = chainList[k];
                const offset = c[1] - boffset;
                let src = c[0];
                let ch = '', prefix = null, sb = null, toffset = null;
                if (chain.childs?.length > 0) {
                    // + | contains children so request so update next children with mark value 
                    let children = chain.childs.slice(0);
                    let nsb = src;
                    let toffset = 0;
                    let tq = null, cmark = null, tc = null, sch = null, ti = null;
                    while (children.length > 0) {
                        tq = children.shift();
                        if (tq in cap_treat_mark) {
                            throw new Exception("not allowed");
                        }
                        cmark = captures[tq];
                        tc = cap[tq];
                        ti = cap.indices[tq][0];
                        sch = handle_mark_capture(tq, cmark, [tc, ti], false);
                        // just update the parent fields
                        sb = ti - boffset;
                        prefix = src.substring(toffset, sb) + sch;
                        nsb = prefix + src.substring(sb + tc.length);
                        toffset = prefix.length;
                    }
                    // ch = nsb;
                    ch = mark(nsb, sourceValue, k, option);
                } else {
                    ch = mark(src, sourceValue, k, option);
                }
                if (register) {
                    marks.push({
                        "value": src,
                        "treatValue": ch,
                        "indice": offset
                    });
                }
                cap_treat_mark[k] = [ch, offset];
                return ch;
            };
            // foreach (captures as k => mark) {
            for (let k of v_keys) {
                mark = captures[k];
                if (!/^\d+$/.test(k) || (k in cap_treat_mark)) {
                    continue;
                }
                c = cap[k];
                if (!c) continue;
                k = parseInt(k);
                handle_mark_capture(k, mark, [c, cap.indices[k][0]], true);
            }
            rv = v;
            if (marks.length > 0) {
                let spos = 0;
                let lpos = 0;
                let tv = '';
                let q = null, prefix = null;
                while (marks.length > 0) {
                    q = marks.shift();
                    // replace with marked value - substring : startindex, endindex
                    prefix = v.substring(spos, q.indice) + q.treatValue;
                    tv = tv.substring(0, lpos) + prefix + v.substring(q.indice + q.value.length);
                    spos = q.indice + q.value.length;
                    lpos += prefix.length;
                }
                rv = tv;
            }
            return rv;
        };
        class RegexCaptureMatcher {
            name;
            patterns;
        }
        /**
         * the regex container 
         */
        class RegexContainer {
            #m_detect_info;
            /**
             * set the base offset before detecting
             * @var {number}
             */
            offset;
            /**
             * @var {undefined|()=>IRegexDetectResult} create a detect info listener 
             */
            createDetectInfoListener;
            /**
             * @var {undefined|(value:string, info: any)=>string} capture value listener 
             */
            captureListener;
            /**
             * create a detect info listener  
             * @var {undefined|()=>RegexMatcherPattern} 
             */
            createRegexMatcherListener;
            /**
             * store version 
             * @var {String}
             */
            version = '1.0';
            /**
             * @var {String?}
             */
            description;
            /**
             * store reference object 
             * @var {object}
             */
            repository = {};
            /**
             * @var {Array?}
             */
            patterns = [];
            /**
             * @var {String?}
             */
            scopeName;

            /**
             * injection selector
             * @var {String?}
             */
            injectionSelector;
            /**
             * @var {(value: string, [option]: Any)=>string}
             */
            prefixListener;

            /**
             * @var {undefined|(value:string, {from:number, to:number, pattern})=>null}
             */
            postUpdateListener;
            /**
             * is end of source 
             * @param {string} source source detected
             * @returns {boolean}
             */
            isEndOfSource(source) {
                return this.#m_detect_info.offset >= source.length;
            }
            /**
             * get current offset 
             */
            get currentOffset() {
                return this.#m_detect_info.offset;
            }
            /**
             * calculate id from pattern
             * @param {*} pattern 
             * @returns 
             */
            static GetIds(pattern) {
                return _nameIdDefinition(pattern);
            }
            /**
             * load object repository 
             * @param {*} data 
             */
            loadRepository(data) {
                for (let i in data) {
                    let pattern = data[i];
                    if (pattern.include) throw new Error('invalid repository');
                    let l = _treatLoadPattern([pattern], { createMatcherPattern: () => this.#_createRegexMatcherPattern() });
                    if (l && (l.length > 0)) {
                        this.repository[i] = l.pop();
                    }
                }
            }

            #_createRegexMatcherPattern() {
                let l = null;
                if (this.createRegexMatcherListener) {
                    l = this.createRegexMatcherListener();
                }
                return l || new RegexMatcherPattern();
            }
            /**
             * create a begin match pattern
             * @param {string}  start 
             * @param {string?} [end] 
             * @param {string?} [tokenID]
             * @param {string?} [refId]
             * @param {Array?}  [patterns]
             * @param {String?} [name]
             */
            begin(start, end, tokenID, refId, patterns, name) {
                const { RegexMatcherPattern } = _NS;
                const createMatcherPattern = () => this.#_createRegexMatcherPattern();
                let inf = createMatcherPattern();

                inf.type = BEGIN_END;
                inf.begin = start;
                inf.end = end;
                inf.tokenID = tokenID;
                inf.name = name;
                inf.patterns = patterns ? _treatLoadPattern(patterns, { createMatcherPattern }) : null;
                if (refId) {
                    var cp = new RefIncludePattern(refId);
                    this.repository[refId] = inf;
                    this.patterns.push(cp);
                } else
                    this.patterns.push(inf);
                return inf;
            }
            /**
             * match expression
             * @param {String} match 
             * @param {*} name 
             * @param {*} tokenID 
             * @param {*} patterns 
             */
            match(match, name, tokenID, patterns) {
                const { RegexMatcherPattern } = _NS;
                let inf = this.#_createRegexMatcherPattern();
                inf.type = MATCH;
                inf.match = match;
                inf.tokenID = tokenID;
                inf.name = name;
                inf.patterns = patterns ? _treatLoadPattern(patterns) : null;
                this.patterns.push(inf);
                return inf;
            }
            /**
             * register while condition
             * @param {String} start 
             * @param {String?} whileCondition 
             * @param {String?} tokenID 
             * @param {String?} name 
             * @param {Array?} patterns 
             * @returns 
             */
            while(start, whileCondition, tokenID, name, patterns) {
                const { RegexMatcherPattern } = _NS;
                let inf = this.#_createRegexMatcherPattern();
                inf.type = BEGIN_WHILE;
                inf.begin = start;
                inf.while = whileCondition;
                inf.tokenID = tokenID;
                inf.name = name;
                inf.patterns = patterns ? _treatLoadPattern(patterns) : null;
                this.patterns.push(inf);
                return inf;
            }
            /**
             * 
             * @param {String} src 
             * @param {(s:string)=>boolean} filter 
             * @param {number} offset 
             * @returns 
             */
            extract(src, filter, offset) {
                const match = [];
                let g = null;
                this.clear();
                this.offset = offset;
                while (g = this.detect(src)) {
                    let e = this.end(g);
                    if (!e) continue;
                    if (!filter || filter(e.value))
                        match.push(e.value);
                }
                return match;
            }
            #_createEndPatternInfo(g, index, parent) {
                const { pattern } = g;
                const q = {
                    isMatch: false,
                    from: index, // start index in buffered
                    to: index, // target index
                    value: '', // contains the real value in the bufferer string 
                    detectResult: g,
                    begin: null,
                    end: null,
                    src: null
                };
                Object.defineProperty(q, 'tokenID', { value: pattern.tokenID, writable: false, configurable: false });
                Object.defineProperty(q, 'parent', { value: parent, writable: false, configurable: false });
                Object.defineProperty(q, 'name', { value: pattern.name, writable: false, configurable: false });
                Object.defineProperty(q, 'isEOS', {
                    get() {
                        return this.to >= this.detectResult.src?.length;
                    }
                });
                return q;
            }
            /**
             * @param {IRegexDetectResult} g source detect object 
             * @returns {IRegexEndDefinition|null}
             */
            end(g) {
                const q = this;
                const { pattern, match, src, endRegex, endWhileRegex, isContinue, parent, info } = g;
                const index = g.offset;
                const v_createResultListener = (info) => {
                    return q.#_createDetectInfoResult(info);
                };
                let value = match[0];
                let v_skip = false;
                let offset = 0;
                const v_fc_checkcap = (a) => Object.keys(a).length > 0 ? a : null;
                // + | -------------------------------------------------------
                // + | captures                                            ---
                // + | -------------------------------------------------------
                let e = info || q.#_createEndPatternInfo(g, index, parent);
                // - captures treatment
                const _cap = pattern.capture || {};
                const begin_cap = v_fc_checkcap({ ..._cap, ...(pattern.beginCaptures || {}) });
                const end_cap = v_fc_checkcap({ ..._cap, ...(pattern.endCaptures || {}) });
                const end_while = v_fc_checkcap({ ..._cap, ...(pattern.whileCaptures || {}) });
                const patterns = pattern.patterns;
                const createMatcherPattern = () => this.#_createRegexMatcherPattern();
                if (!info) {
                    offset = index;
                    offset += value.length;
                } else {
                    offset += q.#m_detect_info.offset;
                }
                (function () {
                    switch (pattern.type) {
                        case MATCH:
                            if (!isContinue) {
                                e.value = value;
                                e.from = index;
                                e.to = offset;
                                e.isMatch = true;
                            }
                            if (begin_cap) {
                                // treat capture only 
                                e.end = TreatCaptures(begin_cap, m, src, {
                                    e,
                                    captureListener: q.captureListener
                                });
                            }
                            // + treat captures
                            // if (patterns) {
                            // + ignore patterns definitions
                            // console.log(" treated pattern ... ")
                            // q.subTreatment.push(patterns, end);
                            // }
                            if (parent) {
                                // update parent info 
                                // throw new Error("no match handler");
                                _preUpdateValue(parent.info, src, parent.info.to, e.from, q.prefixListener);
                                // + | move cursor 
                                parent.info.to = offset;
                            }
                            break;
                        case BEGIN_END:
                        case BEGIN_WHILE:
                            if (!isContinue) {
                                e.value = value;
                                e.from = index;
                                e.to = offset; // will be update to - selection 
                                e.begin = value;
                                if (begin_cap) {
                                    e.begin = TreatCaptures(begin_cap, match, src, {
                                        e,
                                        captureListener: q.captureListener
                                    });
                                }
                            }
                            // + | continue 
                            g.isContinue = true;
                            const _while = pattern.while;
                            const { end } = pattern;

                            let matchs = [];
                            let line = src.slice(offset);
                            let m = null; // end match 
                            let v_matchPattern = patterns?.length > 0 ? _handlePatterns(patterns, {
                                src: g.src,
                                repository: q.repository,
                                pattern: pattern, // pass parent pattern 
                                line,
                                offset,
                                matchs,
                                resolutions: q.#m_detect_info.regexList,
                                createResultListener: v_createResultListener,
                                createMatcherPattern
                            }) : null;
                            // compare match to end priority to end
                            // if ((v_matchPatterns) && (!m || (v_matchPatterns.offset < m.index))) {
                            //     // continue match to child 
                            //     _preUpdateValue(e, src, e.to, v_matchPatterns.offset, q.prefixListener, 'pre-match');
                            //     offset = v_matchPatterns.offset;
                            //     q.#m_detect_info.nextDetection = v_matchPatterns;
                            //     v_matchPatterns.parent = g;
                            //     g.isContinue = true;
                            //     v_skip = true;
                            //     return;
                            // }

                            if (pattern.type == BEGIN_END) {
                                if (end && !endRegex) {
                                    g.endRegex = _endMatch(end, match, value);
                                }
                                _handleEndMatchRegex(g.endRegex, src, line, offset, (im) => {
                                    e.end = im[0];
                                    m = im;
                                });
                                let matchs = [];
                                // let v_matchPatterns = patterns?.length > 0 ? _handlePatterns(patterns, {
                                //     src: g.src,
                                //     repository: q.repository,
                                //     pattern: pattern, // pass parent pattern 
                                //     line, 
                                //     offset,
                                //     matchs,
                                //     resolutions: q.#m_detect_info.regexList,
                                //     createResultListener: v_createResultListener,
                                //     createMatcherPattern
                                // }) : null;
                                // compare match to end priority to end
                                if ((v_matchPattern) && (!m || (v_matchPattern.offset < m.index))) {
                                    // continue match to child 
                                    _preUpdateValue(e, src, e.to, v_matchPattern.offset, q.prefixListener, 'pre-match');
                                    offset = v_matchPattern.offset;
                                    q.#m_detect_info.nextDetection = v_matchPattern;
                                    v_matchPattern.parent = g;
                                    g.isContinue = true;
                                    v_skip = true;
                                    return;
                                }
                                if (m) {
                                    if (end_cap) {
                                        e.end = TreatCaptures(end_cap, m, src, {
                                            e,
                                            captureListener: q.captureListener
                                        });
                                    }
                                    //if (!v_matchPatterns) {
                                    _preUpdateValue(e, src, e.to, m.index, q.prefixListener, 'pre-end');
                                    e.to = m.index + m[0].length;
                                    e.value = src.substring(e.from, e.to);
                                    offset = e.to;
                                    //}
                                } else {
                                    // + | missing end
                                    e.missingEnd = true;
                                    e.to = offset = src.length;
                                }
                                return;
                            } else {
                                // TODO: Check - end while
                                if (_while && !endWhileRegex) {
                                    g.endWhileRegex = _endMatch(_while, match, value);
                                }
                                _handleEndMatchRegex(g.endWhileRegex, src, line, offset, (im) => {
                                    e.end = im[0];
                                    m = im;
                                });
                                //  preUpdateMatch('pre-while-match)
                                if ((v_matchPattern) && (!m || (v_matchPattern.offset < m.index))) {
                                    // continue match to child 
                                    _preUpdateValue(e, src, e.to, v_matchPattern.offset, q.prefixListener, 'pre-while-match');
                                    offset = v_matchPattern.offset;
                                    q.#m_detect_info.nextDetection = v_matchPattern;
                                    v_matchPattern.parent = g;
                                    v_skip = true;
                                    return;
                                }
                                let _next_line_exists = ((idx) =>
                                    (idx = line.indexOf("\n")) == -1 ? !1 : { index: idx + offset })();
                                const _v_line_event = _next_line_exists ?
                                    ((e) => nextLineIsEmpty(line, e) ? e : null)({}) : null;
                                if (_v_line_event) {
                                    // stop next line is empty  ;
                                    _v_line_event.pos += offset;
                                    _v_line_event.end += offset;
                                }

                                if (m) {
                                    if (end_while) {
                                        e.endWhile = TreatCaptures(end_while, m, src, {
                                            e,
                                            captureListener: q.captureListener
                                        });
                                    }
                                    //if (!v_matchPatterns) {
                                    _preUpdateValue(e, src, e.to, m.index, q.prefixListener, 'pre-while');
                                   
                                    if (_v_line_event) {
                                        _postUpdateValueLogic(e, _v_line_event.pos-1, src, q.postUpdateListener, pattern);
                                        ({ offset } = _endWhileUpdateInfo(_v_line_event, e, src));
                                    } else {
                                        e.to = m.index + m[0].length;
                                        e.value = src.substring(e.from, e.to);
                                        offset = e.to;
                                        v_skip = true;
                                        q.#m_detect_info.nextDetection = g;
                                    }
                                    //}
                                } else {
                                    // + | missing end
                                    if (_v_line_event) {
                                        _postUpdateValueLogic(e, _v_line_event.pos-1, src, q.postUpdateListener, pattern);
                                        ({ offset } = _endWhileUpdateInfo(_v_line_event, e, src));
                                    } else {
                                        if (_next_line_exists) {
                                            // move to next line 
                                            _postUpdateValueLogic(e, _next_line_exists.index, src, q.postUpdateListener, pattern);

                                            e.to = _next_line_exists.index;
                                            e.value = src.substring(e.from, e.to);
                                            offset = e.to;
                                        }
                                        else {
                                            e.missingEnd = true;
                                            e.to = offset = src.length;
                                        }
                                    }
                                } 
                            }
                            break;
                    }
                })();
                // + update offset 
                q.#m_detect_info.offset = offset;
                if (v_skip) {
                    if (!g.info) {
                        g.info = e;
                    }
                    return null;
                } else {
                    if (e.parent) {
                        e.parent.info.to = offset; // move parent offset 
                    }
                    this.#m_detect_info.nextDetection = e.parent;
                }
                return e;
            }
            clear() {
                this.#m_detect_info = null;
            }
            reset(e, { offset = 0 }) {
                if (e?.missingEnd) {
                    /**
                     * @type {IRegexDetectResult}
                     */
                    let nResult = { ...e.detectResult };
                    if (!nResult.tValue) {
                        nResult.tValue = [];
                    }
                    const nv = e.detectResult.src.substring(nResult.info.from, nResult.info.to);
                    if (nv.trim().length > 0) {
                        nResult.tValue.push(nv);
                    }
                    // + | reset next detection definition 
                    nResult.src = null;
                    nResult.info.value = nv;
                    // + next offset position 
                    nResult.offset = offset;
                    nResult.info.from = offset;
                    nResult.info.to = offset;
                    delete nResult.info.missingEnd;
                    nResult.info.detectResult = nResult;
                    this.#m_detect_info.nextDetection = nResult;
                    this.#m_detect_info.offset = offset;
                } else {
                    this.clear();
                }
            }
            /**
             * export definitions 
             */
            export() {
                return { scopeName: this.scopeName, patterns: this.patterns, respository: this.repository };
            }
            /**
             * 
             * @returns {IRegexDetectInfo}
             */
            #_createDetectInfo() {
                return { matchs: [], offset: 0, line: '', source: '', resolutions: null, nextDetection: null };
            }
            /**
             * create a detect info result 
             * @return {IRegexDetectResult}
             */
            #_createDetectInfoResult(info) {
                let i = ((this.createDetectInfoListener) ?
                    this.createDetectInfoListener.apply(this, [info]) : null) || {
                    ...info
                };
                return i;
            }
            /**
             * detecting
             * @param {String} source 
             * @param {undefined|null|{offset:number, callback:()=>void|null|undefined}} options 
             * @returns {false|null|undefined|IRegexDetectResult}
             */
            detect(source, options = null) {
                const { RegexDetectionInfo } = _NS;
                if (Array.isArray(source)) {
                    throw new Error("source is array. not allowed");
                }
                if (this.patterns?.length < 0) {
                    return false;
                }
                let v_info = null;
                if (!this.#m_detect_info) {
                    v_info = this.#_createDetectInfo();
                    this.#m_detect_info = new RegexDetectionInfo;
                    this.#m_detect_info.offset = (options ? options.offset : null) || 0;
                    this.#m_detect_info.info = v_info;
                    this.#m_detect_info.callback = options ? options.callback : null;
                } else {
                    v_info = this.#m_detect_info.info;
                }
                let v_callback = options ? options.callback : this.#m_detect_info.callback;
                if (this.#m_detect_info && this.#m_detect_info.nextDetection) {
                    // + | flag retrieve next detect on select 
                    let l = this.#m_detect_info.nextDetection;
                    this.#m_detect_info.nextDetection = null;
                    if (l.src == null) {
                        // udpate src definition to reset to end 
                        l.src = source;
                    }
                    return l;
                }
                const v_line = source.slice(this.#m_detect_info.offset);
                if (v_line.length == 0) {
                    return null;
                }
                let v_detect = true;
                v_info.line = v_line;
                v_info.source = source;
                v_info.offset = this.#m_detect_info.offset;
                v_info.resolutions = this.#m_detect_info.regexList;
                v_info.callback = v_callback;
                v_info.patterns = this.patterns;
                const q = this;
                const v_createResultListener = (info) => {
                    return q.#_createDetectInfoResult(info);
                };
                while (v_detect) {
                    _handlePatternsGlobal(v_info, v_createResultListener);
                    if (v_info.matchs.length > 0) {
                        return v_info.matchs.pop();
                    }
                    v_detect = false;
                }
            }
        };
        class RefIncludePattern {
            /**
             * 
             */
            include;
            constructor(refId) {
                Object.defineProperty(this, 'type', { value: REF_INCLUDE, configurable: false, writable: false });
                this.include = '#' + refId;
            }
        }
        _NS.RegexMatcherPattern = RegexMatcherPattern;
        if (typeof (igk?.system) !== 'undefined') {
            const _NS = igk.system.createNS("igk.system.text", {
                RegexContainer
            });
        }
        // export {
        //     RegexContainer
        // };
        // module.exports = {
        //     RegexContainer
        // }
        return {
            RegexContainer
        }
    };
    const _export = {
        initRegexContainer,
        MATCH,
        BEGIN_WHILE,
        BEGIN_END,
        TREAT_OPT_REGEX
    };
    if (typeof (module) != 'undefined') {
        module.exports = _export;
    }
    const _NS = ((q, a) => { a = a.split('.'); while (q && (a.length > 0)) { q = q[a.shift()]; } return q; })
        (globalThis, 'igk.system.text');
    if (_NS) {
        igk.appendProperties(_NS, _export);
    }
})();

