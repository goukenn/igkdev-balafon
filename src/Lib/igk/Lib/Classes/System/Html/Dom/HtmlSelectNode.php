<?php

namespace IGK\System\Html\Dom;


class HtmlSelectNode extends HtmlNode{    
    protected $tagname = "select";
    public function __construct(bool $autoremove=true){
        parent::__construct();    
    }
    
}   