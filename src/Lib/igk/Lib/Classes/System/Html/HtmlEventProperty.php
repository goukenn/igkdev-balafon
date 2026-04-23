<?php
// @file: IGKHtmlEventProperty.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use ArrayAccess;

/**
* Html event property.
* @package IGK\System\Html
*/
class HtmlEventProperty implements IHtmlGetValue, ArrayAccess{
    use \IGK\System\Polyfill\EventPropertyArrayAccessTrait;
    /**
    * Property: n.
    * @var mixed
    */
    private $_n;
    /**
    * Property: p.
    * @var mixed
    */
    protected $_p;
    /**
    * .ctr
    * @param mixed $name
    */
    protected function __construct($name){
        $this->_n=$name;
        $this->_p=[];
    }
    /**
    * Magic getter for dynamic properties.
    * @param mixed $n
    */
    public function __get($n){
        return igk_getv($this->_p, $n);
    }
    /**
    * Magic setter for dynamic properties.
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        switch(strtolower($n)){
            case '@__callback':
            $this->$n=$v;
            return;
            case 'content':
            $this->_p=$v;
            return;
        }
        if($v == null){
            unset($this->_p[$n]);
        }
        $this->_p[$n]=$v;
    }
    /**
    * Returns string representation.
    */
    public function __toString(){
        return get_class($this). " : ".$this->_n;
    }
    /**
    * Creates Event Property.
    * @param mixed $name
    */
    public static function CreateEventProperty($name){
        return new HtmlEventProperty($name);
    }
    /**
    * Getid.
    */
    public function getid(){
        return $this->_n;
    }
    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options=null){
        $s="";
        if(is_string($this->_p)){
            $s=$this->_p;
        }
        else{
            foreach($this->_p as $k=>$v){
                $s .= $k."=".$v.";";
            }
        }
        if(empty($s))
            return "";
        $s = HtmlUtils::GetAttributeValue($s);
        return implode(" ", array_map(function($k)use($s){
            return "[".$k. "]=\"".$s."\"";
        },array_filter(explode(" ", $this->_n))));
    }
    /**
    * Setid.
    * @param mixed $id
    */
    public function setid($id){
        $this->_n=$id;
    }
}