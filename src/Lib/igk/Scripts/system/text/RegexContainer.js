'use strict';
(async function(){
    const _NS = igk.system.createNS('igk.system.text',{});
    await import('./RegexContainer.d.js');
    const { RegexDetectionInfo, RegexMatcherPattern, initRegexContainer } = _NS; 
    const { RegexContainer } = initRegexContainer({RegexDetectionInfo, RegexMatcherPattern});
    igk.appendProperties(_NS, {
        RegexContainer
    }); 
  
})();