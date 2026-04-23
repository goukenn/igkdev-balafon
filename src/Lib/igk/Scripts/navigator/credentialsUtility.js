(function(){
    const primaryFetchConfig = {
        credentials: 'include',
        headers: { 
            'Content-Type': 'application/json'
        }
    };
    function _toServerData(credentials) {
        // loop on fields and convert every ArrayBuffer
        const _d = {};
        let q = [{ n: _d, p: credentials }];
        while (q.length > 0) {
            let d = q.shift();
            for (let h in d.p) {
                let v = d.p[h];
                if (typeof (v) == 'function') {
                    continue;
                }
                const is_array_buffer = v instanceof ArrayBuffer;
                if (!is_array_buffer && (typeof (v) == 'object')) {
                    d.n[h] = {};
                    q.push({ n: d.n[h], p: v });
                } else {
                    d.n[h] = is_array_buffer ? bufferTo64(v) : v;
                }
            }
        }
        return _d;
    };
    function _getRandomEID() {
        let l = Math.ceil(Math.random() * 10) + 1;
        const r = [];
        while (l > 0) {
            r.push(Math.ceil(Math.random() * 30));
            l--;
        }
        return r;
    };
    const bufferUtils = {
        serveData(data) {
            return _toServerData(data);
        },
        bta(o) {// convert definition - detecte Binary Stream and convert to 
            let tq = [o];
            const pre = '=?BINARY?B?', suf = '?=';
            while (tq.length > 0) {
                let q = tq.shift();
                for (let i in q) {
                    let s = q[i];
                    if (typeof (s) == 'string') {
                        if ((s.substring(0, pre.length) == pre) &&
                            (s.substring(s.length - suf.length) == suf)) {
                            // binary daata 
                            let b = atob(s.substring(pre.length, s.length - suf.length)),
                                u = new Uint8Array(b.length);
                            for (let mi = 0; mi < b.length; mi++) {
                                u[mi] = b.charCodeAt(mi);
                            }
                            q[i] = u.buffer;
                        }
                    } else {
                        if (s) {
                            tq.push(s);
                        }
                    }
                }
            }
            return o;
        }
    };
    function _initChallenge() {
        let v_ref_challenge = _getRandomEID();        
        return new Uint8Array(v_ref_challenge);
    };
    function bufferTo64(buffer) {
        let s = '';
        let u = new Uint8Array(buffer);
        for (let i = 0; i < u.byteLength; i++) {
            s += String.fromCharCode(u[i]);
        }
        return btoa(s);
    };
    const algorigthm = {};
    Object.defineProperty(algorigthm, 'ES256', {get(){ return -7; }}); 
    Object.defineProperty(algorigthm, 'Ed25519', {get(){ return -8; }}); 
    Object.defineProperty(algorigthm, 'RS256', {get(){ return -257; }}); 
    const fecthWithCredentials = async function(uri, option){
        option = igk.initObj(option, primaryFetchConfig);  
        return fetch(uri,option);
    };
    igk.system.createNS('igk.navigator.credentialsUtility',{
        _initChallenge,
        _getRandomEID,
        bufferUtils,
        bufferTo64,
        primaryFetchConfig,
        algorigthm,
        fecthWithCredentials
    });
})();