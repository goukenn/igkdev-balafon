<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKValidator.php
// @date: 20220803 13:48:54
// @desc: 



///<summary>Represente class: IGKValidator</summary>

use IGK\Helper\Activator;
use IGK\System\Html\Forms\FormFieldInfo;
use IGK\System\Html\Forms\Validations\FormFieldValidationInfo;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;
use IGK\System\Html\Forms\Validations\IFormValidator;
use IGK\System\Html\Forms\Validations\PasswordValidator;
use IGK\System\Html\IFormFields;

use function igk_resources_gets as __;
/**
* Represente IGKValidator class
*/
final class IGKValidator extends IGKObject {
    const INT_REGEX= \IGK\System\Regex\RegexConstant::INT_REGEX; 
    const PWD_MIN_LENGTH = IGK_PWD_LENGTH;
    private $sm_cibling;
    private $sm_enode;
    private static $sm_instance;
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        $this->sm_enode=igk_create_node("error");
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
    * @param bool $condition
    * @param bool * $error
    * @param mixed $node the default value is null
    * @param mixed $errormsg the default value is IGK_STR_EMPTY
    */
    public static function Assert(bool $condition,bool & $error, $node=null, $errormsg=IGK_STR_EMPTY){
        if(!$condition){ 
            $error=$error || true;
            if($node != null){
                $node->li()->Content=$errormsg;
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
    /**
     * check for guid 
     * @param null|string $v 
     * @return bool 
     */
    public static function IsGUID(?string $v=null){        
        return !is_null($v) && (strlen($v) == IGKConstants::GUID_LENGTH) && preg_match("/^\{[0-9a-f\-]+\}$/i", $v);
    }
    /**
     * check password validity confirmation
     */
    public static function ValidatePassword($pwd, $rpwd): bool{
        if ($pwd && ($pwd == $rpwd)){
            return self::IsValidPwd($pwd);
        }
        return false;
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
    public static function IsIpAddress(string $p){
        if (is_null($p)){
            return false;
        }
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

        static $validator;
        if (is_null($validator)){
            $validator = new PasswordValidator;
        }
        return $validator->validate($o) == $o; 
    }
    ///<summary></summary>
    ///<param name="o"></param>
    ///<param name="fields"></param>
    ///<param name="error" ref="true"></param>
    /**
    * 
    * @param object $o object to validate 
    * @param mixed $fields [key=>['f'=>callback, 'e'=>error_message, 'required'=>true|false, 'd'=>default value in calse of missing]] - \
    * callback is validation fonction and 'e' error message
    * callback can't be a validator method IFormValidator 
    * @param mixed * $error
    * @return bool|object  
    */
    public static function Validate($o, $fields, & $error, bool $validate=true){

        $g=self::getInstance()->sm_enode;
        $g->clearChilds();
        $e=false;
        $ro = (object)[]; // real output object
        if (empty($o)){

            return false;
        }
        if(is_array($fields)){

            foreach($fields as $k=>$v){
                $is_obj = is_object($v); 
                $v_validator = null;
        
                if ($is_obj && ($v instanceof FormFieldValidatorBase)){
                    $v_def = new FormFieldValidationInfo;
                    $v_def->validator = $v;
                    $v = $v_def;
                } 
                else if(((!$is_obj && is_array($v)) || !($v instanceof FormFieldValidationInfo))){
                    $v = Activator::CreateNewInstance(FormFieldInfo::class, $v);
                    // create a FormFieldValidationInfo 
                    $v = Activator::CreateNewInstance(FormFieldValidationInfo::class, $v);  
                    // + | validate with field 
                } 
                if ($v instanceof FormFieldValidationInfo){ 
                    $v_validator = $v->validator ?? igk_die(sprintf(__('missing validator for %s'), $k));// sprintf(__()))
                    if (is_string($v_validator)){
                        //+ create a validator from class name
                        $v_validator = FormFieldValidatorBase::Factory($v_validator);
                    }
                    if (!($v_validator instanceof IFormValidator)){ 
                        igk_die(sprintf(__('validator is not satisfied %s, %s'),
                         IFormValidator::class, (string)$v->validator));
                    }
                    if ($v->required && (!isset($o->$k) || empty($o->$k))){
                        // required a value 
                        $m = sprintf(__('property %s is required'), $k);
                        $error[$k][] = $m;
                        self::Assert(false, $e, $g, $m);
                        continue;
                    }

                    $v_v = igk_getv($o, $k);
                    if (!$validate){
                        //+ | just check value but not transfrom
                       if (!$v_validator->assertValidate($v_v)){
                            $error[$k] = sprintf(__('%s is not a valid data'), $k);
                       } else {
                            $ro[$k] = $v_v;
                       }
                    } else {
                        $v_e = [];
                        $v_new = $v_validator->validate($v_v, $v->default,$v_e);
                        if (empty($v_e)){
                            $ro->$k = $v_new;
                        } else 
                        {
                            $ce = false;
                            self::Assert(false, $ce, $g);
                            if ($ce){
                                $error[$k] = $ce;
                            }
                        }
                    }
                } else {
                    igk_die(sprintf(__('missing FormFieldValidationInfo for %s'), $k));
                }
            }

            // foreach($fields as $k=>$v){
            //     $f= null;
            //     $require = false;
            //     $def_error = sprintf('error on field %s', $k);

            //     if ($v instanceof FormFieldValidationInfo){
            //         $f = $v->validator;
            //         $require = $v->required;
            //         $error_text = $v->error ?? $def_error;

            //     }
            //     else {

            //         $f = igk_getv($v, 'f');
            //         $require = igk_getv($v, 'required');
            //         $error_text = igk_getv($v, 'e', $def_error);
            //     }
            //     $cond = false;
            //     $s = null;
            //     if (!property_exists($o, $k)){
            //         $require = $require || ($f instanceof IFormValidator) ? $f->isRequire() : false;                    
            //         if ($require){
            //             $error_text = sprintf('missing field %s', $k);                        
            //         }
            //     } else {
            //         $s = $o->$k;                
            //         $call = null;
            //         if ($f instanceof IFormValidator){
            //             $call = [$f, 'assertValidate'];
            //         }
            //         else if (!is_string($f) && is_callable($f) ){
            //             $call = [$f];
            //         }else{                    
            //             if (is_string($f) && ($f!= __FUNCTION__) && method_exists(static::class, $f)){
            //                 $s = trim($s ?? '');
            //                 $call = [static::class, $f];
            //             }
            //         }
            //         if (is_null($call)){
            //             igk_die(sprintf(__('missing validation for %s'), $k));
            //         }
            //         $cond=call_user_func_array($call, [$s]);
            //         self::Assert($cond, $e, $g, $error_text); 

            //         // if ($cond && ($f instanceof IFormValidator)){
            //         //     $s = $f->validate($s, $v->default, $v, $error);
            //         // }
            //     }
            //     !$cond && $error[] = $error_text;
            //     $o->$k = $s; // change the object data definition
            //     $ro->$k = $s; // real object output
            // }
        } 
        if ($error && count($error)){
            $error[] = $g->render();
            return false;
        }
        return $ro;
    }

}