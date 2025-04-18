// contains helper function for create element 
'use strict';
(function () {
    function createlitteral(regex){
        let p = regex.begin(":\\s+", "\\s+:", null, null, null, "expression");
        p.patterns = [
            {
                'match': "\\\\."
            }
        ];
        return p;
    }
    function createElement(tagExpression, namespace) {
        const { RegexContainer } = igk.system.text;
        let regex = new RegexContainer();
        regex.match("(?i)\\.[a-z]+[a-z0-9\\-_]*", 'class');
        regex.match("(?i)#[a-z]+[a-z0-9\\-_]*", 'id');
        regex.match("(?i)@[a-z]+[a-z0-9\\-_]*", 'activateAttrib');
        regex.match('\\w+\\b', 'tag');
        regex.match('\\s*>\\s*', 'split');
        regex.match('\\s*!\\s*', 'parent');
        regex.begin('{', '}', null, null, null, 'styling');
        regex.begin('\\[', '\\]', null, null, null, 'attribs');
        regex.begin('\\(', '\\)', null, null, null, 'funcs');
        let p = regex.begin(":\\s+", "\\s+:", null, null, null, "expression");
        p.patterns = [
            {
                'match': "\\\\."
            }
        ];

        let src = tagExpression;
        let pos = 0;
        let g = null;
        let options = { offset: 0 };
        let i = 0;
        const createE = namespace ? (v) => {
            return document.createElementNS(namespace, v);
        } : (v) => { return document.createElement(v); };

        const _listener = (function () {
            let _node = null;
            let g = {
                root: null,
                split() { },
                attribs(lv) {
                    if (!_node)return;
                    let regex = new RegexContainer();
                    lv = lv.replace(/^\[/, '').replace(/\]$/, '');
                    regex.match('(=|:)', 'split');
                    regex.match('(;)', 'end');
                    regex.match('\\w+\\b', 'key');
                    let p = createlitteral(regex);
                    p.name = 'key';
                    const attrib = {};
                    let n = '', v = '';
                    let mode = 0;
                    while (g = regex.detect(lv)) {
                        let e = regex.end(g);
                        if (e) {
                            const { name, value } = e;
                            if (mode == 0) {
                                switch (name) {
                                    case 'key':
                                        n += value;
                                        break;
                                    case 'split':
                                        mode = 1;
                                        break;
                                }
                            } else{
                                switch (name) {
                                    case 'key':
                                        v += value;
                                        break;
                                    case 'end':
                                        mode = 0;
                                        attrib[n] = v;
                                        n = v = '';
                                        break;
                                }
                            }
                        } else {
                            // console.log("stop end definition....");
                            break;
                        }
                    }
                    if (n.length>0){
                        attrib[n] = v;
                    }
                    for(let i in attrib){
                        _node.setAttribute(i, attrib[i]);
                    }
                    
                },
                funcs(v) {

                },
                activateAttrib(v) {
                    $igk(_node).o.setAttribute(v.substring(1), '');
                },
                styling(v) {
                    let r = (new Function('return ' + v)).apply();
                    if (_node)
                        $igk(_node).setCss(r);
                },
                expression(v) {
                    if (!_node)
                        return;
                    v = v.replace(/^:\s+/, '');
                    v = v.replace(/\s+:$/, '');
                    v = v.trim();
                    if (v.length > 0)
                        _node.appendChild(document.createTextNode(v));
                },
                parent() {
                    if (_node) {
                        _node = _node.parentNode;
                    }
                },
                id(v) {
                    if (_node)
                        _node.setAttribute('id', v.substring(1));
                }, class(v) {
                    if (_node)
                        $igk(_node).addClass(v.substring(1));
                }, tag(v) {
                    let c = createE(v);
                    if (!_node) {
                        _node = c;
                        this.root = _node;
                    } else {
                        _node.appendChild(c);
                        _node = c;
                    }
                }
            };
            Object.defineProperty(g, 'lastNode', { get() { return _node } });
            return g;
        })();
        while (g = regex.detect(src)) {
            let e = regex.end(g);
            if (e) {
                const { name, value } = e;
                if (name && _listener && (name in _listener)) {
                    // console.log('bind : '+name + " : "+value);
                    _listener[name].apply(_listener, [value]);
                }
            } else {
                // console.log("stop end definition....");
                break;
            }
        }
        const { root, lastNode } = _listener;
        return { root, lastNode };
    };

    const _JS = igk.system.createNS('igk.system.html.dom', {
        /**
         * create and expression 
         * @param {string} $tagExpression 
         * @param {undefined|string} namespace 
         */
        createElement(tagExpression, namespace) {
            const { RegexContainer } = igk.system.text;
            if (!RegexContainer) {
                console.log('try create ', tagExpression);
                return null;
            }
            return createElement(tagExpression, namespace);
        }
    });
    Object.defineProperty(_JS.createElement, 'support', {
        get() {
            const { RegexContainer } = igk.system.text;
            return !!RegexContainer;
        }
    });

})();