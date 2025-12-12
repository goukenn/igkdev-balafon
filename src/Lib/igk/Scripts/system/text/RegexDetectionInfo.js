'use strict';

(async function(){   
    /**
     * inject detectd 
     */
    const r = await import('./RegexDetectionInfo.d.mjs');
    const { RegexDetectionInfo } = r; 
    const _N_ = "igk.system.text";
    const _MOD_ = igk.system.module( _N_ );  
    const _NS = igk.system.createNS( _N_,{
        RegexDetectionInfo
    });
})();