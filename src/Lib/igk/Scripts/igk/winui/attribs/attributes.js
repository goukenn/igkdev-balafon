'use strict';
(function(){
  igk.ctrl.registerAttribManager('data-attr-no-paste', {});
  igk.ctrl.bindAttribManager('data-attr-no-paste', function(){    
    this.on('paste', (e)=>{
      e.preventDefault();
    })
  });
})();