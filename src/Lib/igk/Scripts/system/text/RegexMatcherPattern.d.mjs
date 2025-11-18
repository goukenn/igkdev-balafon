class RegexMatcherPattern {
    /**
     * internal detected type of the regex
     */
    type;
    /**
     * container list .
     * @deprecated
     */
    container;
    /**
     * begin resolution for both begin/end - begin/while
     */
    begin;
    /**
     * begin/end end resoilution 
     */
    end;
    /**
     * begin/while while resolution 
     */
    while;
    /**
     * match resolution 
     */
    match;
    /**
     * list of patterns
     */
    patterns;
    /**
     * name of the pattern 
     */
    name;
    /**
     * content name specification 
     */
    contentName;
    /**
     * @var {string?} comment definition 
     */
    comment;
    /**
     * list of captures list 
     */
    captures;
    /**
     * begin of captures list 
     */
    beginCaptures;
    /**
     * end of captures list 
     */
    endCaptures;

    /**
     * while capture 
     */
    whileCaptures;
    /**
     * include reference usage 
     */
    include;
    /**
     * internal created reference id 
     */
    refId;
    /**
     * token id  use . to categorise token 
     */
    tokenID;
    toString() {
        return 'RegexMatcherPattern';
    }  
}

export {
    RegexMatcherPattern
}