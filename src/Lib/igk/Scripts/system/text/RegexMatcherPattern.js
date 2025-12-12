'use strict';

(function(){ 

    // console.log('1 - log regex matcher pattern');
    /**
     * base Regex Matcher Pattern 
     */
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
        /**
         * name used
         */
        name;
        /**
         * content name 
         */
        contentName;
        captures;
        beginCaptures;
        endCaptures;
        /**
         * special token id
         */
        tokenID;
        toString(){
            return 'RegexMatcherPattern';
        }
    }
    igk.system.createNS('igk.system.text',{
        RegexMatcherPattern
    });
})();