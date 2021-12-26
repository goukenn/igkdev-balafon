<?php

namespace IGK\System\Html\Dom;

class HtmlScriptNode extends HtmlNode implements IHtmlScript{
    protected $tagname = "script";

    const ACCEPT=0xb3;
    const CANMERGE_LINK=0xb2;
    const NOT_SINGLEVIEW=0xb4;
    const SCRIPT_LINK=0xb1;
    const SCRIPT_TAG=0xb0;
    const TEMPORARY=0xb5;


    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setIsTemp($v){
        $this->setFlag(self::TEMPORARY, $v);
    }
    public function __construct()
    {
        parent::__construct();
        $this["type"] = "text/javascript";
        $this["language"] = "javascript";
    }
    public function getCanAddChilds()
    {
        return false;
    }
}