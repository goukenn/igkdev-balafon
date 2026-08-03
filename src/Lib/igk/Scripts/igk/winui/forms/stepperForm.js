'use strict';
(function(){

igk.winui.initClassControl('igk-stepper-form', function(){
 
    const d = this.qselect('section.step');
    const route = this.select('div.route').first();
    const actions = this.select('.igk-action-bar').first();
    const form = this.select('^form').first();
    let _info = {step: 1, TotalStep:d.getCount(), routes:null, prevButton:null, primaryButton:null, primaryButtonValue:null};

 

    function udateActive(){
    d.each_all(function(){
        if (this.getAttribute('data-step')==_info.step){
            this.addClass('is-active');
        } else{
            this.rmClass('is-active');
        }
    }); 
};
function validatePassword(pwd, progress, rules){
    const cur = progress.qselect('.igk-progressbar-cur').first();
    const states = ['danger','warning','success'];
    let lstate = 0;
    const cp = pwd.o.checkValidity;
    let _ok = true;
    pwd.o.checkValidity = function(){        
        if (!_ok){
            return !1;
        }
        return cp.apply(this);
    };
    pwd.on('input', function(){
        let _state = states[0];
        let _TRule = 0;
        let _TPassed = 0;
        var v = pwd.o.value;
        for(let i in rules){
            if (rules[i](v)){
                _TPassed++;
            }
            _TRule++;
        }

        if (_TPassed==0){
            progress.addClass('no-visibility');
            return;
        }

        const pct = (_TPassed / _TRule) * 100;
        if (pct<=25)
            _state = states[0];
        else if (pct<100)
            _state = states[1];
        if (pct>=100){
            _state = states[2];
        }
        progress.rmClass('no-visibility');
        let c = [];        
        if (lstate){
            c.push(lstate);
        }
        cur.rmClass(c); 
        cur.setCss({width: pct+'%'}).addClass(_state);
        lstate = _state;
        _ok = _TPassed == _TRule;
    });
};

(function(){
    const pwd = form.qselect('#passwd').first();
    const progress = form.qselect('.igk-progressbar').first();
    if (pwd && progress){ 
        validatePassword(pwd, progress,{
            length: (v) => v.length >= 8,
            lower:  (v) => /[a-z]/.test(v),
            upper:  (v) => /[A-Z]/.test(v),
            digit:  (v) => /\d/.test(v),
            special:(v) => /[^A-Za-z0-9]/.test(v)
    });
    }  
})();
 
/**
 * @param any number
 */
function moveToStep(number){
    if ((number <1) || (number> _info.TotalStep))return;
    _info.step = number;
    udateActive();
    renderRoute();
    updateButtons();
};
    function renderRoute(){
        if (!_info.routes){
            _info.routes = [];
            d.each_all(function(){
                _info.routes.push(this.select('h2.step__title').first().o.innerText);
            });
            route.on('click', function(e){
                const c = e.target.closest('.route__stop.is-visited');
                if (c){
                    let j = Number(c.getAttribute('data-step'));
                    moveToStep(j); 
                }
            });
        }
        let step = 1;
        route.setHtml(_info.routes.map(function(t){
            const is_active = step == _info.step;
            const is_visited = step < _info.step;
            let extra = [];
            if (is_active){
                extra.push('is-active');
            }
            if (is_visited){
                extra.push('is-visited');
            }
            if (extra.length>0)
                extra = ' '+extra.join(' ');
            const ts = step;
            step++;
            return `<div class="route__stop${extra}" data-step="${ts}">
    <div class="route__block"> 
        <div class="route__seal">${ts}</div>
        <div class="route__label">${t}</div>
    </div>
</div>
`;
        }).join(''));
    };
    /**
     * validate form
     */
    function validateForm(){
        const l = d.getItemAt(_info.step - 1);
        const tr = l.o.querySelectorAll('input, select');
        let hasError = false;
        let p = null;
        tr.forEach((c)=>{
            const p = c.closest('.igk-form-group') || c;
            if (!c.checkValidity()){
                hasError = true;
                p.classList.add('error');
            }else{
                p.classList.remove('error');
            }
        }); 
        if (!hasError && (p = l.getAttribute('data-match-validity'))){         
            let c_in = {value: null, init:false, items:[]};
            JSON.parse(p).forEach(element => {
               let m = l.qselect('#'+element).first();
               let v = m.o.value;
               c_in.items.push(m);
               if (!c_in.init){
                c_in.init = true;
                c_in.value = v;
               }else{
                    if (c_in.value != v){
                        hasError = true; 
                    }
               }
            });

            if (hasError){
                l.o.classList.add('has-mismatch');
            }else{
                l.o.classList.remove('has-mismatch');
            }
            
            c_in.items.forEach((i)=>{
                const p = i.o.closest('.igk-form-group') || i.o;
                if (hasError){
                    p.classList.add('error');
                }else{
                    p.classList.remove('error');
                }
        });
            

        }

        return !hasError;
    }
    function clickButton(e){
        if (e.target.action == 'continue'){
            if (_info.step < _info.TotalStep){
                if (validateForm()){
                    _info.step++;
                }
            } else{
                if (validateForm()){   
                    form.o.requestSubmit();
                }
                return;
            }
        }else{
            _info.step = Math.max(_info.step-1, 1);
        }
        udateActive();
        renderRoute();
        updateButtons();
    };
    function updateButtons(){
        if (_info.step==1){
            _info.prevButton.o.setAttribute('hidden','');
        }else
            _info.prevButton.o.removeAttribute('hidden');
        if (_info.step == _info.TotalStep){            
            _info.primaryButton.setHtml(_info.primaryButton.o.getAttribute('data-submit-text'));
        }else{
            _info.primaryButton.setHtml(_info.primaryButtonValue);
        }
    };
    function initButtons(){
        actions.select('button').each_all(function(){            
            this.on('click', clickButton); 
        });
        (_info.prevButton = actions.select('button.previous').first()).o.setAttribute('hidden', '');
        _info.primaryButton = actions.select('button.primary').first();
        _info.primaryButton.o.action = 'continue';
        _info.primaryButtonValue = _info.primaryButton.o.innerHTML;
    }
    udateActive();
    renderRoute();
    initButtons();
    updateButtons();

    

    form.on('keypress', function(e){        
        if (e.keyCode == 13){
            _info.primaryButton.o.click();
        }
    });

});
})();