'use strict';
(function () {
    let def = null;
  
    igk.winui.initClassControl('igk-winui-preview-pwd', function () {
      // if (null==def){
      //   document.
      //   def = 1;
      // }
        let p = $igk(this.o.parentNode);
        let next = this.o.nextSibling;
        let n = $igk.createNode('div');
        n.addClass('posr pw-field');
        let r = $igk.createNode('div');
        r.setHtml(`<button type="button" class="pw-toggle" id="pw-toggle" aria-label="Afficher le mot de passe" aria-pressed="false">
      <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
      <svg class="icon-eye-off" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.6 20.6 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a20.6 20.6 0 0 1-4.06 5.06M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
        <line x1="1" y1="1" x2="23" y2="23"/>
      </svg>
    </button>`);
        let btn = r.qselect('button').first();
        const input = this.o;
        btn.on('click', function () {
            const iconEye = btn.qselect('.icon-eye').first().o;
            const iconEyeOff = btn.qselect('.icon-eye-off').first().o;
            const isHidden = input.type === 'password';
            const toggle = btn.o;
            input.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(isHidden));
            toggle.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            iconEye.style.display = isHidden ? 'none' : 'block';
            iconEyeOff.style.display = isHidden ? 'block' : 'none';
            input.focus();
        });
        n.appendChild(this.o);
        n.appendChild(btn);
        if (next){ 
            p.o.insertBefore(n.o, next);
        }
        else{ 
            p.o.appendChild(n.o);
        }
    });
})();

