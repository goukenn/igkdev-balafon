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
    const GP_NAME=0x2;
    const NAME=0x1;
    const PAGE=0x6;
    const POSITION=0x4;
    const TITLE=0x5;
    const IMAGEKEY=0x6;
    const URI=0x3;
    private $_;
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
    public function __get($n){
        if(method_exists($this, "get".$n)){
            return call_user_func_array(array($this, "get".$n), array());
        }
        return igk_die(__CLASS__.":::not defined ".$n);
    }
    public function __set($n, $v){
        igk_die("can't set : ".$n);
    }
    public function __toString(){
        return __CLASS__."[".$this->getName()."]";
    }
    // // public function add(){
    //     igk_die(__METHOD__."");
    // }
    public function getCurrentPage(){
        return igk_getv($this->_, self::PAGE);
    }
    public function getGroup(){
        return igk_getv($this->_, self::GP_NAME);
    }
    public function getHasChilds(){
        return 0;
    }
    public function getIndex(){
        return igk_getv($this->_, self::POSITION);
    }
    public function getName(){
        return igk_getv($this->_, self::NAME);
    }
    public function getTitle(){
        return igk_getv($this->_, self::TITLE);
    }
    public function getUri(){
        return igk_getv($this->_, self::URI);
    }
    public function setFlag($p, $v){
        if($v == null){
            unset($this->_[$p]);
        }
        else
            $this->_[$p]=$v;
    }
    public function setGroup($gpName){
        $this->setFlag(self::GP_NAME, $gpName);
        return $this;
    }
    public static function SortMenuByDisplayText($a, $b){
        return strcmp(__("menu.".$a->Name), __("menu.".$b->Name));
    }
    public static function SortMenuByIndex($a, $b){
        if($a->Index < $b->Index)
            return -1;
        else if($a->Index == $b->Index)
            return self::SortMenuByName($a, $b);
        return 1;
    }
    public static function SortMenuByName($a, $b){
        return strcmp($a->Name, $b->Name);
    }
    public function updateUri($uri){  
        $this->setFlag(self::URI, $uri);
    }
}
