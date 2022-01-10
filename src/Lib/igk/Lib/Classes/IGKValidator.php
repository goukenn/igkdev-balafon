<?php

///<summary>Represente class: IGKValidator</summary>
/**
* Represente IGKValidator class
*/
final class IGKValidator extends IGKObject {
    const INT_REGEX="/^[0-9]+$/i";
    private $sm_cibling;
    private $sm_enode;
    private static $sm_instance;
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        $this->sm_enode=igk_createnode("error");
        $this->sm_cibling=array();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
    * 
    * @param mixed $name
    */
    public static function AddCibling($name){
        $e=self::getInstance();
        $t=explode(",", $name);
        foreach($t as $v){
            $e->sm_cibling[$v]=1;
        }
    }
    ///<summary></summary>
    ///<param name="condition"></param>
    ///<param name="error" ref="true"></param>
    ///<param name="node" default="null"></param>
    ///<param name="errormsg" default="IGK_STR_EMPTY"></param>
    /**
    * 
    * @param mixed $condition
    * @param mixed * $error
    * @param mixed $node the default value is null
    * @param mixed $errormsg the default value is IGK_STR_EMPTY
    */
    public static function Assert($condition, & $error, $node=null, $errormsg=IGK_STR_EMPTY){
        if($condition){
            $error=$error || true;
            if($node != null){
                $node->addLi()->Content=$errormsg;
            }
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function Cibling(){
        return self::getInstance()->sm_cibling;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
    * 
    * @param mixed $name
    */
    public static function ContainCibling($name){
        $e=self::getInstance();
        return isset($e->sm_cibling[$name]);
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function Error(){
        $e=self::getInstance();
        return $e->sm_enode;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function getInstance(){
        if(self::$sm_instance == null){
            self::$sm_instance=igk_get_class_instance(__CLASS__, function(){
                return new IGKValidator();
            });
        }
        return self::$sm_instance;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public static function GetPattern($n){
        static $patterns=null;
        if($patterns == null){
            $patterns=array(
                "email"=>IGK_HTML_EMAIL_PATTERN,
                "phone"=>IGK_HTML_PHONE_PATTERN
            );
        }
        return igk_getv($patterns, $n);
    }
    ///<summary>represent initilalize the validator node</summary>
    /**
    * represent initilalize the validator node
    */
    public static function Init(){
        $e=self::getInstance();
        $e->sm_enode->clearChilds();
        $e->sm_cibling=array();
        return $e->sm_enode;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public static function IsDate($v){}
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public static function IsDouble($v){
        return is_Double($v);
    }
    ///<summary></summary>
    ///<param name="mail"></param>
    /**
    * 
    * @param mixed $mail
    */
    public static function IsEmail($mail){
        if(self::IsStringNullOrEmpty($mail))
            return false;
        return preg_match('/[a-z0-9\.\-_]+@[a-z0-9\.\-_]+\.[a-z]{2,6}$/i', $mail);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public static function IsFloat($v){
        return is_float($v);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public static function IsInt($v){
        return is_numeric($v);
    }
    ///<summary></summary>
    ///<param name="p"></param>
    /**
    * 
    * @param mixed $p
    */
    public static function IsIpAddress($p){
        return preg_match(IGK_IPV4_REGEX, trim($p));
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public static function IsString($v){
        return is_string($v);
    }
    ///<summary>check is null or empty.</summary>
    /**
    * check is null or empty.
    */
    public static function IsStringNullOrEmpty($v, $cibling=null, $msg="error..."){
        $v=(($v == null) || (is_string($v) && (strlen($v) == 0)));
        if($v && $cibling){
            $cibling->addError($msg);
        }
        return $v;
    }
    ///<summary>check if full uri</summary>
    /**
    * check if full uri
    */
    public static function IsUri($v){
        if(empty($v))
            return false;
        $r=preg_match('/^(((http(s){0,1}):)?\/\/([\w\.0-9]+)|(\?))/i', $v);
        // +-------------------------------------------
        // detect core matching - component tempory uri 
        // +-------------------------------------------
        if (!$r && preg_match( "#^/(index\.php/)?\{[^\}]+\}#i", $v)){           
            return true;
        }
        // $r = !$r || preg_match( "#^/(index\.php/)?\{[^\}]+]\}#i", $v);
        return $r;
    }
    ///<summary></summary>
    ///<param name="o"></param>
    /**
    * 
    * @param mixed $o
    */
    public static function IsValidPwd($o){
        if((is_string($o) && strlen($o)>=6)){
            return true;
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="o"></param>
    ///<param name="fields"></param>
    ///<param name="error" ref="true"></param>
    /**
    * 
    * @param mixed $o
    * @param mixed $fields
    * @param mixed * $error
    */
    public static function Validate($o, $fields, & $error){
        $g=self::getInstance()->sm_enode;
        $g->clearChilds();
        $e=false;
        if(is_array($fields)){
            foreach($fields as $k=>$v){
                $s=$o->$k;
                $cond=call_user_func_array(array(__CLASS__, $v["f"]), array($s));
                self::Assert($cond, $e, $g, $v["e"]);
            }
        }
        return !$e;
    }
}