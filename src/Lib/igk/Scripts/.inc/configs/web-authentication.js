// @ts-nocheck
'use strict';
(function () {
    function bufferTo64(buffer) {
        let s = '';
        let u = new Uint8Array(buffer);
        for (let i = 0; i < u.byteLength; i++) {
            s += String.fromCharCode(u[i]);
        }
        return btoa(s);
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
    function _getRandomEID() {
        let l = Math.ceil(Math.random() * 10) + 1;
        const r = [];
        while (l > 0) {
            r.push(Math.ceil(Math.random() * 30));
            l--;
        }
        return r;
    };
    function _initChallenge() {
        let v_ref_challenge = _getRandomEID();
        document.cookie = 'webauth-challenge=' + v_ref_challenge; "; httpOnly; path=/";
        return new Uint8Array(v_ref_challenge);
    };
    async function _registerUser(uri) {
        const challenge = _initChallenge();


        const registerUser = await (async function () {
            let p = uri ? await fetch(uri, {
                method: 'POST',
                body: JSON.stringify({ action: 'create', challenge: bufferTo64(challenge) })
            }).then(a => a.json())
                .then(m => bufferUtils.bta(m))
                .catch(e => {
                    console.error("error", e);
                }) : null;

            let _default = (p ? p.publicKey : null) || {
                // required members 
                challenge: challenge,
                rp: { name: 'localhost' },
                // publicKey: true,
                user: {
                    id: new Uint8Array(_getRandomEID()),
                    name: 'user@igkdev.com',
                    displayName: 'IGKDEV (user)'
                },
                pubKeyCredParams: [
                    { type: 'public-key', alg: -7 }
                    // {type: 'public-key', alg:-257}

                ],
                // optional parameter ,
                // authenticatorSelection:{
                //     authenticatorAttachment: "platform",
                //     requireResidentKey: false,
                //     userVerification : 'required'
                // },
                timeout: 60000
            }; return _default;
        })();
        return registerUser;
    };
    // @ts-ignore
    igk.system.createNS('igk.auth.webAuthn', {
        bufferUtils,
        /**
         * 
         * @param {string} uri 
         */
        async register(uri) {
            const registerUser = await _registerUser(uri);
            if (registerUser) {
                try {
                    const credentials = await navigator.credentials.create({ publicKey: registerUser });
                    if (credentials) {
                        // send and store information to server 
                        const _new = bufferUtils.serveData(credentials);
                        _new.transport = credentials.response.getTransports ? credentials.response.getTransports() : [];

                        const response = await fetch(uri, {
                            method: 'POST',
                            body: JSON.stringify({ credentials: _new, action: 'store' }),
                            credentials: 'include'
                        }).then(o => {
                            return o.json();
                        }).then(data => {
                            if (data.error) {
                                console.error('failed : ' + data.msg)
                                // igk.notify('registration failed.');
                            }
                        });
                    }
                } catch (e) {
                    console.error('create credential failed.', e);
                }
            }
        },
        async signin(uri){
            
        }

    });
})();
(async function () {
    /**
     * @var mixed igk
     */
    // @ts-ignore
    igk.winui.initClassControl('webauthn-signin-btn', function () {
        const resolve = this.getAttribute('data-webauthn-resolve');
        const { bufferUtils } = igk.auth.webAuthn;
        this.on('click', async function () {
            const options = (resolve ? await (async () => {
                const config = await fetch(resolve, {
                    method: 'POST',
                    body: JSON.stringify({'action':'create'}),
                    credentials: 'include'
                }).then(o => o.json()).then(data => bufferUtils.bta(data));
                return config;
            })() : null) || { publicKey: { challenge: new Uint8Array([12]) } };
            if (options==null){
                console.log('missing webauth get settings');
                return;
            }
            try {
                const credentials = await navigator.credentials.get({
                    publicKey: options.publicKey
                });
                const _topass = { credentials: bufferUtils.serveData(credentials), action:'get' };
                const _response = await fetch(resolve,{
                    method: 'POST',
                    credentials: 'include',
                    body: JSON.stringify(_topass)
                } ).then(
                    o=>o.json()
                ).catch(e=>{
                    console.log("error");
                });
            } catch(e){
                console.error('error: ', e);
            }
        });
    });
})();

