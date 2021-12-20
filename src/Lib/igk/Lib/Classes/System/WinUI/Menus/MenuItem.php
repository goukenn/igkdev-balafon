<?php
// @file: MenuItem.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\WinUI\Menus;

use function igk_resources_gets as __;


class MenuItem{
    const GP_NAME=0x2;
    const NAME=0x1;
    const PAGE=0x6;
    const POSITION=0x4;
    const TITLE=0x5;
    const URI=0x3;
    private $_;
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="args"></param>
    public function __call($n, $args){
        igk_die(__CLASS__.":::NO EXTRA ".$n);
    }
    ///<summary></summary>
    ///<param name="$name"></param>
    ///<param name="$title" default="null"></param>
    ///<param name="$uri" default="null"></param>
    ///<param name="$position" default="10"></param>
    ///<param name="$imagekey" default="null"></param>
    ///<param name="$t" default="null"></param>
    ///<param name="$group" default="null"></param>
    public function __construct($name, $title=null, $uri=null, $position=10, $imagekey=null, $t=null, $group=null){
        $this->_=array();
        $this->setFlag(self::NAME, $name);
        $this->setFlag(self::TITLE, $title ?? $name);
        $this->setFlag(self::PAGE, $title);
        $this->setFlag(self::POSITION, $position);
        $this->setFlag(self::URI, $uri);
        $this->setFlag(self::GP_NAME, $group);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __get($n){
        if(method_exists($this, "get".$n)){
            return call_user_func_array(array($this, "get".$n), array());
        }
        return igk_die(__CLASS__.":::not defined ".$n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v){
        igk_die("can't set : ".$n);
    }
    ///<summary>display value</summary>
    public function __toString(){
        return __CLASS__."[".$this->getName()."]";
    }
    ///<summary></summary>
    public function add(){
        igk_die(__METHOD__."");
    }
    ///<summary></summary>
    public function getCurrentPage(){
        return igk_getv($this->_, self::PAGE);
    }
    ///<summary></summary>
    public function getGroup(){
        return igk_getv($this->_, self::GP_NAME);
    }
    ///<summary></summary>
    public function getHasChilds(){
        return 0;
    }
    ///<summary></summary>
    public function getIndex(){
        return igk_getv($this->_, self::POSITION);
    }
    ///<summary></summary>
    public function getName(){
        return igk_getv($this->_, self::NAME);
    }
    ///<summary></summary>
    public function getTitle(){
        return igk_getv($this->_, self::TITLE);
    }
    ///<summary></summary>
    public function getUri(){
        return igk_getv($this->_, self::URI);
    }
    ///<summary></summary>
    ///<param name="p"></param>
    ///<param name="v"></param>
    public function setFlag($p, $v){
        if($v == null){
            unset($this->_[$p]);
        }
        else
            $this->_[$p]=$v;
    }
    ///<summary></summary>
    ///<param name="gpName"></param>
    public function setGroup($gpName){
        $this->setFlag(self::GP_NAME, $gpName);
        return $this;
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    public static function SortMenuByDisplayText($a, $b){
        return strcmp(__("menu.".$a->Name), __("menu.".$b->Name));
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    public static function SortMenuByIndex($a, $b){
        if($a->Index < $b->Index)
            return -1;
        else if($a->Index == $b->Index)
            return self::SortMenuByName($a, $b);
        return 1;
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    public static function SortMenuByName($a, $b){
        return strcmp($a->Name, $b->Name);
    }
    ///<summary></summary>
    ///<param name="u"></param>
    public function updateUri($u){    }
}
