<?php
// @file: IGKHtmlEventProperty.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

use ArrayAccess;

class HtmlEventProperty implements IHtmlGetValue, ArrayAccess{
    use \IGK\System\Polyfill\EventPropertyArrayAccessTrait;
    private $_n;
    protected $_p;
    ///<summary></summary>
    ///<param name="name"></param>
    protected function __construct($name){
        $this->_n=$name;
        $this->_p=[];
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __get($n){
        return igk_getv($this->_p, $n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
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
    ///<summary>display value</summary>
    public function __toString(){
        return get_class($this). " : ".$this->_n;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public static function CreateEventProperty($name){
        return new HtmlEventProperty($name);
    }
    ///<summary></summary>
    public function getid(){
        return $this->_n;
    }
    ///<summary>override this to get vavlue</summary>
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
        // return "[".$this->_n. "]=\"".HtmlUtils::GetAttributeValue($s)."\"";
    }
    ///<summary></summary>
    ///<param name="id"></param>
    public function setid($id){
        $this->_n=$id;
    }
}
