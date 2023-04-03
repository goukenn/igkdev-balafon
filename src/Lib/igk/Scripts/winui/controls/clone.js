(function(){
    /**
     * clone target without attribute 
     */
    igk.winui.initClassControl('igk-winui-clone', function(){
        let t = this.getAttribute('igk-target');
        let q = $igk(t).first();
        if (q){
            this.o.removeAttribute('igk-target');
            let l = document.createElement(q.o.tagName.toLowerCase());
            l.innerHTML = q.o.innerHTML; 
            this.o.appendChild(l);
            $igk(l).init();
        }
    })
})();