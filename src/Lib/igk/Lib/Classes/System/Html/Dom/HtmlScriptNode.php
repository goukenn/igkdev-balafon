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
/**
* Html script node.
* @package IGK\System\Html\Dom
*/
class HtmlScriptNode extends HtmlNode implements IHtmlScript{
    /**
     * script tag
     * @var string
     */
    protected $tagname = "script";
    /**
    * Property: link.
    * @var mixed
    */
    protected $link;
    /**
     * script version
     * @var mixed
     */
    protected $version;
    /**
     * autohtml entities
     * @var bool
     */
    protected $autohtmlentities = false;
    /**
    * Constant: accept.
    * @var mixed
    */
    const ACCEPT=0xb3;
    /**
    * Constant: canmerge link.
    * @var mixed
    */
    const CANMERGE_LINK=0xb2;
    /**
    * Constant: not singleview.
    * @var mixed
    */
    const NOT_SINGLEVIEW=0xb4;
    /**
    * Constant: script link.
    * @var mixed
    */
    const SCRIPT_LINK=0xb1;
    /**
    * Constant: script tag.
    * @var mixed
    */
    const SCRIPT_TAG=0xb0;
    /**
    * Constant: temporary.
    * @var mixed
    */
    const TEMPORARY=0xb5;
    /**
    * auto generate doc.
    */
    public function getCanBeMerged(){
        return $this->getFlag(self::CANMERGE_LINK) ?? true;
    }
    /**
    * auto generate doc.
    */
    public function getlink(){
        return $this->getFlag(self::SCRIPT_LINK);
    }
    /**
    * auto generate doc.
    */
    public function getNotSingleView(){
        return $this->getFlag(self::NOT_SINGLEVIEW);
    }
    /**
    * auto generate doc.
    */
    public function getTag(){
        return $this->getFlag(self::SCRIPT_TAG);
    }
     /**
    * get is temp
    */
    public function IsTemporary(){
        return $this->getFlag(self::TEMPORARY);
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setIsTemp($v){
        $this->setFlag(self::TEMPORARY, $v);
        return $this;
    }
    /**
    * .ctr
    * @param null|mixed $source
    * @param null|mixed $version
    */
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
    /**
    * Creates Attribute Array.
    */
    protected function createAttributeArray(){ 
        return new HtmlAttributeArray([
            "src"=>new HtmlResolvLinkValue()
        ]);
    }
    /**
    * Text.
    * @param string $content
    */
    public function text(string $content){
        return $this->setContent($content);
    }
    /**
    * auto generate doc.
    */    private function _iaccept(){
        $this->setFlag(self::ACCEPT, !(!empty($this->link) && (!IGKValidator::IsUri($this->link) && !igk_io_file_exists(igk_getv(explode("?", $this->link), 0), true))));
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    public function getCanAddChilds()
    {
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setCanBeMerged($v){
        $this->setFlag(self::CANMERGE_LINK, $v);
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setIsTemporary($v){
        $this->setFlag(self::TEMPORARY, $v);
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setlink($v){
        $this->setFlag(self::SCRIPT_LINK, $v);
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setNotSingleView($v){
        $this->setFlag(self::NOT_SINGLEVIEW, $v);
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setTag($v){
        $this->setFlag(self::SCRIPT_TAG, $v);
        return $this;
    }
    /**
    * Getcan load content.
    * @param mixed $value
    * @return bool
    */
    protected function getcanLoadContent($value):bool{        
        return false;
    }
}