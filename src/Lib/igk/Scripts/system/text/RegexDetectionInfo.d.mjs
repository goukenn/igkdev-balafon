'use strict';
(function(){
class RegexDetectionInfo{
    /**
     * 
     * @var {*}  
     */
    matchPatterns;

    /**
     * base offset 
     */
    offset;

    /**
     * detect info storage
     */
    info;

    /**
     * detected regex list 
     */
    regexList = {};

    /**
     * next detection info 
     * @var {?IRegexDetectResult}
     */
    nextDetection;
};
const __exports = { 
    RegexDetectionInfo
};
if (typeof(module)!= 'undefined'){
    module.exports = __exports; 
} 

const _NS = ((q, a) => { a = a.split('.'); while (q && (a.length > 0)) { q = q[a.shift()]; } return q; })
    (globalThis, 'igk.system.text');
if (_NS) {
    igk.appendProperties(_NS, __exports);
}

})();