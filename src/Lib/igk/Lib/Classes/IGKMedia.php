<?php
// @file: IGKMedia.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Css\CssSupport;
use IGK\Css\ICssStyleContainer;

final class IGKMedia implements ArrayAccess, ICssStyleContainer{
    use IGK\System\Polyfill\IGKMediaArrayAccessTrait;
    const CUSTOM_COLOR=0x1;
    const DEFAULT_THEME=0x2;
    const FILES_THEME=0x5;
    const FONT_THEME=0x3;
    const MEDIA_ID=0x0;
    const PROPERTIES_THEME=0x4;
    private $_;
    ///<summary>.ctr media </summary>
    public function __construct($type, $name){
        $this->_=array();
        $this->_[self::MEDIA_ID] = $type.":".$name;
    }

    public function getProperties() { return []; }

    public function Clear(){
        $id = igk_getv($this->_, self::MEDIA_ID);
        $this->_ = [];
        $this->_[self::MEDIA_ID] =$id;
    }

    /**
     * support css rules 
     * @param string $rule 
     * @return CssSupport 
     * @throws IGKException 
     */
    public function supports(string $rule){
        $key = "@supports (".trim($rule).")";
        if ($g = igk_getv($this->getDef(), $key)){
            if ($g instanceof CssSupport){
                return $g;
            }
        }
        $s = new CssSupport($rule);
        $this[$key] = $s; 
        return $s;
    }
    public function bindSupport(CssSupport $support){ 
        $key = "@supports (".trim($support->rule).")";
        $this[$key] = $support;
    }
      /**
     * return a copy of this media storage
     * @return array 
     */
    public function to_array(){
        foreach([self::DEFAULT_THEME=>"def", 
            self::CUSTOM_COLOR=>"color", 
            self::FILES_THEME=>"file", 
            self::FONT_THEME=>"font"] as $t=>$n){
            $out[$n] = igk_getv($this->_, $t);
        }
        return array_filter($out);
    }
    /**
     * load media data
     * @param array $data 
     * @return void 
     */
    public function load_data(array $data){
       
        $this->_ = [self::MEDIA_ID=>
            $this->getId()
        ];
        foreach([self::DEFAULT_THEME=>"def", 
            self::CUSTOM_COLOR=>"color", 
            self::FILES_THEME=>"file", 
            self::FONT_THEME=>"font"] as $t=>$n){
            
            if (is_array($g = igk_getv($data, $n))){
                $this->_[$t] = $g;
            }
        }
        return true;

    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<return refout="true"></return>
    public function & __get($n){
        $o=null;
        if(method_exists($this, "get".$n)){
            $o=call_user_func(array($this, "get".$n), array_slice(func_get_args(), 1));
        }
        return $o;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v){   
        // do nothing
    }
    ///<summary></summary>
    public function __sleep(){
        if(empty($this->_)){
            return array();
        }
        return array("\0".__CLASS__."\0_");
    }
    ///<summary>get media definition</summary>
    public function getCssDef(ICssStyleContainer $theme, 
        ICssStyleContainer $systheme,
        $minfile=true){
      
        $o=""; 
        $lineseparator=$minfile ? "": IGK_LF;
        $def=$this->getDef();
        if($def){
            foreach($def as $k=>$v){
                if (is_string($v)){
                    $kv=trim(igk_css_treat($v, $theme, $systheme));
                    if(!empty($kv)){
                        $o .= $k."{".$kv."}".$lineseparator; 
                    }
                } else {
                    $o .= $k."{ ".$v->getCssDef($theme, $systheme)." }".$lineseparator; 
                }
            }
        } 
        return $o;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    public function & getDef(){
        return $this->getFlag(self::DEFAULT_THEME);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<return refout="true">get flags</return>
    private function & getFlag($n){
        $g=null;
        if(isset($this->_[$n])){
            $g=& $this->_[$n];
        }
        return $g;
    }
    ///<summary></summary>
    public function getId(){
        return $this->getFlag(self::MEDIA_ID);
    }
    ///<summary>get if this media storage is empty</summary>
    /**
     * get if this media storage is empty
     * @return bool 
     */
    public function isEmpty(){
        return count($this->_) == 0;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    private function setFlag($n, $v){
        $this->_[$n]=$v;
        return $this;
    } 
}
