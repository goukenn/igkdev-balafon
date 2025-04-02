// @ts-nocheck
'use strict';
(function(){
    function bufferTo64(buffer){
        let s = '';
        let u = new Uint8Array(buffer);
        for (let i = 0; i < u.byteLength; i++){
            s+= String.fromCharCode(u[i]);
        }
        return btoa(s);
    };
    function _toServerData(credentials){
        // loop on fields and convert every ArrayBuffer
        const _d = {};
        let q = [{n:_d, p: credentials}];
        while(q.length>0){
            let d = q.shift();
            for(let h in d.p){
                let v = d.p[h];
                if (typeof(v)=='function'){
                    continue;
                }
                const is_array_buffer =  v instanceof ArrayBuffer;

                if (!is_array_buffer && (typeof(v)=='object')){
                    d.n[h] = {};
                    q.push({n:d.n[h], p:v});
                }else{
                    d.n[h] = is_array_buffer? bufferTo64(v) : v;
                }
            }
        }
        return _d;
    };
    const bufferUtils = {
        serveData(data){
            return _toServerData(data);
        },
        bta(o){// convert definition - detecte Binary Stream and convert to 
            let tq = [o];
            const pre = '=?BINARY?B?', suf='?=';
            while(tq.length > 0){
                let q = tq.shift();
                for (let i in q){
                    let s = q[i];
                    if (typeof(s)=='string'){
                        if ((s.substring(0, pre.length)==pre) &&
                            (s.substring(s.length- suf.length) == suf)){
                                // binary daata 
                                    let b = atob(s.substring(pre.length, s.length-suf.length)), 
                                    u = new Uint8Array(b.length);
                                for (let mi = 0; mi < b.length; mi++){
                                    u[mi] = b.charCodeAt(mi);
                                }
                                q[i] = u.buffer; 
                        }   
                    } else {
                        if (s){
                            tq.push(s);
                        }
                    }
                }
            }
    
            return o;
        }
    };
    // @ts-ignore
    igk.system.createNS('igk.auth.webAuthn', {
        bufferUtils
    });
})();
(async function () {
    /**
     * @var mixed igk
     */
    // @ts-ignore
    igk.winui.initClassControl('webauthn-signin-btn', function () {
        const resolve = null;// this.getAttribute('data-webauthn-resolve');
        const { bufferUtils }= igk.auth.webAuthn;
        // console.log('initialize .... 595', this);
        this.on('click', async function () { 
            const options = (resolve ? await (async () => {
                const config = await fetch(resolve, {
                    method: 'POST'
                }).then(o=>o.json()).then(data=>bufferUtils.bta(data));
                return config;               
            })() : null ) || {publicKey:{challenge: new Uint8Array([12])}};
            // console.log({ options })
            const credentials = await navigator.credentials.get({
                publicKey : options.publicKey
            });
        }); 
    });
})();

