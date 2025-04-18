'use strict';

(function(){ 

    // console.log('1 - log regex matcher pattern');
    class RegexMatcherPattern{
        type;
        container;
        /**
         * target include 
         */
        include;
        begin;
        end;
        while;
        patterns;
        name;
        contentName;
        captures;
        beginCaptures;
        endCaptures;
        toString(){
            return 'RegexMatcherPattern';
        }
    }
    igk.system.createNS('igk.system.text',{
        RegexMatcherPattern
    });
})();