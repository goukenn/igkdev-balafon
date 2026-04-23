(function () {
    /**
     * special litteral expression to get the raw method must be call without () and ``     */
    function esm(templateStrings, ...substitutions) {
        let js = templateStrings.raw[0];
        for (let i = 0; i < substitutions.length; i++) {
            js += substitutions[i] + templateStrings.raw[i + 1];
        }
        return 'data:text/javascript;base64,' + btoa(js);
    };
    igk.system.createNS("igk.system.modules", {
        response: null,
        esm: esm,
        /**
         * append module to document
         * @param {string} src 
         * @returns 
         */
        append(src, id) {
            let v = igk.createNode('script');
            v.o.type = 'module';
            v.o.id = id;
            v.setHtml(src);
            igk.dom.body().add(v.o);
            modules.push(v);
            return v;
        },
        import(src, callback) {
            let g = (async () => {
                let m = await igk.system.modules.importAsync(src);
                return m;
            })().then((m) => {
                if (callback) {
                    callback(m);
                }
                return m;
            });
            return g;
        },
        async importAsync(src) {
            let b = esm`${src}`;
            // + | webpack request that import is a string - to avoid critical dependency for expression
            let c = await
                import(/* @vite-ignore */`${b}`);
            return c;
        }
    })
})();