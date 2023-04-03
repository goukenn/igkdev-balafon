<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlScriptNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlAttributeArray;
use IGK\System\Html\HtmlResolvLinkValue;
use IGKValidator;

require_once IGK_LIB_CLASSES_DIR . "/System/Html/Dom/IHtmlScript.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Html/HtmlResolvLinkValue.php";

class HtmlScriptNode extends HtmlNode implements IHtmlScript{
    /**
     * script tag
     * @var string
     */
    protected $tagname = "script";

    protected $link;

    /**
     * script version
     * @var mixed
     */
    protected $version;

    const ACCEPT=0xb3;
    const CANMERGE_LINK=0xb2;
    const NOT_SINGLEVIEW=0xb4;
    const SCRIPT_LINK=0xb1;
    const SCRIPT_TAG=0xb0;
    const TEMPORARY=0xb5;

     ///<summary></summary>
    /**
    * 
    */
    public function getCanBeMerged(){
        return $this->getFlag(self::CANMERGE_LINK) ?? true;
    }
///<summary></summary>
    /**
    * 
    */
    public function getlink(){
        return $this->getFlag(self::SCRIPT_LINK);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getNotSingleView(){
        return $this->getFlag(self::NOT_SINGLEVIEW);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getTag(){
        return $this->getFlag(self::SCRIPT_TAG);
    }

     ///<summary>get if is temp script</summary>
    /**
    * get is temp
    */
    public function IsTemporary(){
        return $this->getFlag(self::TEMPORARY);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setIsTemp($v){
        $this->setFlag(self::TEMPORARY, $v);
        return $this;
    }
    public function __construct($source=null, $version=null)
    {
        parent::__construct();
        $this["type"] = "text/javascript";
        $this["language"] = "javascript";
        $this["src"] = $source;
        $this->version = $version;
        $this->canBeMerged=true;
        $this->_iaccept(); 
    }

    protected function createAttributeArray(){ 
        return new HtmlAttributeArray([
            "src"=>new HtmlResolvLinkValue()
        ]);
    }
    public function text(string $content){
        return $this->setContent($content);
    }
     ///<summary></summary>
    /**
    * 
    */
    private function _iaccept(){
        $this->setFlag(self::ACCEPT, !(!empty($this->link) && (!IGKValidator::IsUri($this->link) && !file_exists(igk_getv(explode("?", $this->link), 0)))));
    }
    public function getCanAddChilds()
    {
        return false;
    }


      ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setCanBeMerged($v){
        $this->setFlag(self::CANMERGE_LINK, $v);
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setIsTemporary($v){
        $this->setFlag(self::TEMPORARY, $v);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setlink($v){
        $this->setFlag(self::SCRIPT_LINK, $v);
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setNotSingleView($v){
        $this->setFlag(self::NOT_SINGLEVIEW, $v);
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setTag($v){
        $this->setFlag(self::SCRIPT_TAG, $v);
        return $this;
    }
}