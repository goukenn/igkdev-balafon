// author: C.A.D. BONDJE DOUE
// file: igk.winui.toggle.theme.js
// @date: 20260816 09:52:55
// @desc: used to toggle css theme 
// @component-class : igk-winui-toggle-theme

'use strict';

(function () {
    function _init() {
        let themeBtn = this;
        const props = igk.initObj(JSON.parse(this.o.getAttribute('data-toggle-theme') ?? '{}'), {
            light: "🌙",
            dark: "☀️"
        });
        const updateBtnIcon = function (theme) {
            themeBtn.o.textContent = theme === "dark" ? props.dark : props.light;
        };
        const currentTheme = function(){
            const htmlEl = document.documentElement;
            return htmlEl.getAttribute("data-theme");
        };
        const updateTheme = function(){
            const newTheme = currentTheme() === "dark" ? "light" : "dark";
            console.log('update theme ', newTheme);
            igk.css.changeDocumentTheme(newTheme);
        };
        themeBtn.on('click', () => {
            updateTheme();
        });
        igk.publisher.register('sys://dom/css/theme-changed', function (e) {
            updateBtnIcon(e.theme);
        });
        updateBtnIcon(currentTheme()); 

    };
    // + | init compoment
    igk.winui.initClassControl('igk-winui-toggle-theme', _init);
})();
