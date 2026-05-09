<?php
// @file: MenuItem.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\WinUI\Menus;
use IGKException;
use function igk_resources_gets as __;

/**
 * contextual menu item
 * @package IGK\System\WinUI\Menus
 */
class MenuItem{
    /**
    * Constant: gp name.
    * @var mixed
    */
    const GP_NAME=0x2;
    /**
    * Constant: name.
    * @var mixed
    */
    const NAME=0x1;
    /**
    * Constant: page.
    * @var mixed
    */
    const PAGE=0x6;
    /**
    * Constant: position.
    * @var mixed
    */
    const POSITION=0x4;
    /**
    * Constant: title.
    * @var mixed
    */
    const TITLE=0x5;
    /**
    * Constant: imagekey.
    * @var mixed
    */
    const IMAGEKEY=0x6;
    /**
    * Constant: uri.
    * @var mixed
    */
    const URI=0x3;
    /**
    * Property: .
    * @var mixed
    */
    private $_;
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $n
    * @param mixed $args
    */
    public function __call($n, $args){
        igk_die(__CLASS__.":::NO EXTRA ".$n);
    }
    /**
     * return the menu item fullname
     * @return string 
     * @throws IGKException 
     */
    public function fullName(){
        return implode(".", array_filter([
            $this->getGroup(),
            $this->getName(),
            $this->getIndex()            
        ]));
    }
    /**
     * iniit 
     * @param string $name menu name
     * @param ?string $title title to dispay 
     * @param ?string $uri target uri
     * @param int $position 
     * @param mixed $imagekey the image key 
     * @param mixed $group 
     * @return void 
     */
    public function __construct($name, $title=null, $uri=null, $position=10, $imagekey=null, $group=null){
        $this->_=array();
        $this->setFlag(self::NAME, $name);
        $this->setFlag(self::TITLE, $title ?? $name);
        $this->setFlag(self::PAGE, $title);
        $this->setFlag(self::POSITION, $position);
        $this->setFlag(self::URI, $uri);
        $this->setFlag(self::GP_NAME, $group);
        $this->setFlag(self::IMAGEKEY, $imagekey);
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if(method_exists($this, "get".$n)){
            return call_user_func_array(array($this, "get".$n), array());
        }
        return igk_die(__CLASS__.":::not defined ".$n);
    }
    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        igk_die("can't set : ".$n);
    }
    /**
    * get string presentation.
    */
    public function __toString(){
        return __CLASS__."[".$this->getName()."]";
    }
    /**
    * Returns Current Page.
    */
    public function getCurrentPage(){
        return igk_getv($this->_, self::PAGE);
    }
    /**
    * Returns Group.
    */
    public function getGroup(){
        return igk_getv($this->_, self::GP_NAME);
    }
    /**
    * Returns Has Childs.
    */
    public function getHasChilds(){
        return 0;
    }
    /**
    * Returns Index.
    */
    public function getIndex(){
        return igk_getv($this->_, self::POSITION);
    }
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return igk_getv($this->_, self::NAME);
    }
    /**
    * Returns Title.
    */
    public function getTitle(){
        return igk_getv($this->_, self::TITLE);
    }
    /**
    * Returns Uri.
    */
    public function getUri(){
        return igk_getv($this->_, self::URI);
    }
    /**
    * Sets Flag.
    * @param mixed $p
    * @param mixed $v
    */
    public function setFlag($p, $v){
        if($v == null){
            unset($this->_[$p]);
        }
        else
            $this->_[$p]=$v;
    }
    /**
    * Sets Group.
    * @param mixed $gpName
    */
    public function setGroup($gpName){
        $this->setFlag(self::GP_NAME, $gpName);
        return $this;
    }
    /**
    * Sorts Menu By Display Text.
    * @param mixed $a
    * @param mixed $b
    */
    public static function SortMenuByDisplayText($a, $b){
        return strcmp(__("menu.".$a->Name), __("menu.".$b->Name));
    }
    /**
    * Sorts Menu By Index.
    * @param mixed $a
    * @param mixed $b
    */
    public static function SortMenuByIndex($a, $b){
        if($a->Index < $b->Index)
            return -1;
        else if($a->Index == $b->Index)
            return self::SortMenuByName($a, $b);
        return 1;
    }
    /**
    * Sorts Menu By Name.
    * @param mixed $a
    * @param mixed $b
    */
    public static function SortMenuByName($a, $b){
        return strcmp($a->Name, $b->Name);
    }
    /**
    * Updates Uri.
    * @param mixed $uri
    */
    public function updateUri($uri){  
        $this->setFlag(self::URI, $uri);
    }
}