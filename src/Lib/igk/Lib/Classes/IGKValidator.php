<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKValidator.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Constants;
use IGK\Helper\Activator;
use IGK\System\Html\Forms\FormFieldInfo;
use IGK\System\Html\Forms\Validations\FormFieldValidationInfo;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;
use IGK\System\Html\Forms\Validations\IFormValidationFieldHost;
use IGK\System\Html\Forms\Validations\IFormValidator;
use IGK\System\Html\Forms\Validations\PasswordValidator;
use IGK\System\Html\IFormFields;
use function igk_resources_gets as __;
/**
 * Represent IGKValidator class
 */
final class IGKValidator extends IGKObject
{

    /**
    * Constant: int regex.
    * @var mixed
    */
    const INT_REGEX = \IGK\System\Regex\RegexConstant::INT_REGEX;

    /**
    * Constant: pwd min length.
    * @var mixed
    */
    const PWD_MIN_LENGTH = IGK_PWD_LENGTH;

    /**
    * Constant: email regex.
    * @var mixed
    */
    const EMAIL_REGEX = '/^[a-z0-9\.\-_]+@[a-z0-9\.\-_]+\.[a-z]{2,6}$/i';

    /**
    * Property: cibling.
    * @var mixed
    */
    private $sm_cibling;

    /**
    * Property: enode.
    * @var mixed
    */
    private $sm_enode;

    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
     * 
     */
    private function __construct()
    {
        $this->sm_enode = igk_create_node_arg("div.error");
        $this->sm_cibling = array();
    }
    /**
     * 
     * @param mixed $name
     */

    public static function AddCibling($name)
    {
        $e = self::getInstance();
        $t = explode(',', $name);
        foreach ($t as $v) {
            $e->sm_cibling[$v] = 1;
        }
    }
    /**
     * 
     * @param bool $condition
     * @param bool * $error
     * @param mixed $node the default value is null
     * @param mixed $errormsg the default value is IGK_STR_EMPTY
     */

    public static function Assert(bool $condition, bool &$error, $node = null, $errormsg = IGK_STR_EMPTY)
    {
        if (!$condition) {
            $error = $error || true;
            if ($node != null) {
                $node->li()->Content = $errormsg;
            }
        }
    }
    /**
     * 
     */

    public static function Cibling()
    {
        return self::getInstance()->sm_cibling;
    }
    /**
     * 
     * @param mixed $name
     */

    public static function ContainCibling($name)
    {
        $e = self::getInstance();
        return isset($e->sm_cibling[$name]);
    }
    /**
     * retrieve validation error node 
     */

    public static function Error()
    {
        return self::getInstance()->sm_enode;
    }
    /**
     * global validation instance
     */

    public static function getInstance()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = igk_get_class_instance(__CLASS__, function () {
                return new IGKValidator();
            });
        }
        return self::$sm_instance;
    }
    /**
     * 
     * @param mixed $n
     */

    public static function GetPattern($n)
    {
        static $patterns = null;
        if ($patterns == null) {
            $patterns = array(
                "email" => IGK_HTML_EMAIL_PATTERN,
                "phone" => IGK_HTML_PHONE_PATTERN
            );
        }
        return igk_getv($patterns, $n);
    }
    /**
     * represent initilalize the validator node
     */

    public static function Init()
    {
        $e = self::getInstance();
        $e->sm_enode->clearChilds();
        $e->sm_cibling = array();
        return $e->sm_enode;
    }
    /**
     * 
     * @param mixed $v
     */

    public static function IsDate($v) {}
    /**
     * 
     * @param mixed $v
     */

    public static function IsDouble($v)
    {
        return is_Double($v);
    }
    /**
     * 
     * @param mixed $mail
     */

    public static function IsEmail($mail)
    {
        if (self::IsStringNullOrEmpty($mail))
            return false;
        return preg_match(self::EMAIL_REGEX, $mail);
    }
    /**
     * 
     * @param mixed $v
     */

    public static function IsFloat($v)
    {
        return is_float($v);
    }
    /**
     * check for guid 
     * @param null|string $v 
     * @return bool 
     */

    public static function IsGUID(?string $v = null)
    {
        return !is_null($v) && (strlen($v) == Constants::GUID_LENGTH) && preg_match("/^\{[0-9a-f\-]+\}$/i", $v);
    }
    /**
     * check password validity confirmation
     */

    public static function ValidatePassword($pwd, $rpwd): bool
    {
        if ($pwd && ($pwd == $rpwd)) {
            return self::IsValidPwd($pwd);
        }
        return false;
    }
    /**
     * 
     * @param mixed $v
     */

    public static function IsInt($v)
    {
        return is_numeric($v);
    }
    /**
     * 
     * @param mixed $p
     */

    public static function IsIpAddress(string $p)
    {
        if (is_null($p)) {
            return false;
        }
        return preg_match(IGK_IPV4_REGEX, trim($p));
    }
    /**
     * 
     * @param mixed $v
     */

    public static function IsString($v)
    {
        return is_string($v);
    }
    /**
     * check is null or empty.
     */

    public static function IsStringNullOrEmpty($v, $cibling = null, $msg = "error...")
    {
        $v = (($v == null) || (is_string($v) && (strlen($v) == 0)));
        if ($v && $cibling) {
            $cibling->addError($msg);
        }
        return $v;
    }
    /**
     * check if full uri
     */

    public static function IsUri($v)
    {
        if (empty($v))
            return false;
        $r = preg_match('/^(((http(s){0,1}):)?\/\/([\w\.0-9]+)|(\?))/i', $v);
        // +-------------------------------------------
        // detect core matching - component tempory uri 
        // +-------------------------------------------
        if (!$r && preg_match("#^/(index\.php/)?\{[^\}]+\}#i", $v)) {
            return true;
        }
        // $r = !$r || preg_match( "#^/(index\.php/)?\{[^\}]+]\}#i", $v);
        return $r;
    }
    /**
     * 
     * @param mixed $o
     */

    public static function IsValidPwd($o)
    {
        static $validator;
        if (is_null($validator)) {
            $validator = new PasswordValidator;
        }
        return $validator->validate($o) == $o;
    }
    /**
     * 
     * @param object $o object to validate 
     * @param mixed $fields [key=>['f'=>callback, 'e'=>error_message, 'required'=>true|false, 'd'=>default value in calse of missing]] - \
     * callback is validation fonction and 'e' error message
     * callback can't be a validator method IFormValidator 
     * @param mixed * $error
     * @return bool|object  
     */

    public static function Validate($o, $fields, &$error, bool $validate = true)
    {
        $g = self::getInstance()->sm_enode;
        $g->clearChilds();
        $e = false;
        $ro = (object)[]; // real output object
        if (empty($o)) {
            return false;
        }
        $o = (object)$o;
        if (is_array($fields)) {
            foreach ($fields as $k => $v) {
                if (is_numeric($k) && is_string($v) && !empty($v)){
                    $kk = $v;
                    $ro->$kk = igk_getv($o, $kk);
                    continue;
                }
                $is_obj = is_object($v);
                $v_validator = null;
                $v_field_info = null;
                if ($is_obj && ($v instanceof FormFieldValidatorBase)) {
                    $v_def = new FormFieldValidationInfo;
                    $v_def->validator = $v;
                    $v = $v_def;
                    $v_field_info = new FormFieldInfo;
                    $v_field_info->type = 'text';
                    $v_field_info->validator = $v;
                } else if (((!$is_obj && is_array($v)) || !($v instanceof FormFieldValidationInfo))) {
                    list($validator, $error)  = igk_extract($v, 'f|e');
                    $v_field_info = $v = Activator::CreateNewInstance(FormFieldInfo::class, $v);
                    // create a FormFieldValidationInfo                     
                    $tv = Activator::CreateNewInstance(FormFieldValidationInfo::class, $v);
                    // + | validate with field 
                    $v = $tv;
                    $v->validator = $validator;
                }
                if ($v instanceof FormFieldValidationInfo) {
                    $v_validator = $v->validator ?? igk_die(sprintf(__('missing validator for [%s]'), $k)); // sprintf(__()))
                    if (is_string($v_validator)) {
                        //+ create a validator from class name
                        $v_validator = FormFieldValidatorBase::Factory($v_validator);
                    }
                    if (!($v_validator instanceof IFormValidator)) {
                        igk_die(sprintf(
                            __('validator is not satisfied %s, %s'),
                            IFormValidator::class,
                            (string)$v->validator
                        ));
                    }
                    if ($v->required && (!isset($o->$k) || empty($o->$k))) {
                        // required a value
                        // igk_wln_e(__FILE__.":".__LINE__ , !isset($o->$k),  $o->$k); 
                        $m = sprintf(__('property %s is required'), $k);
                        $error[$k][] = $m;
                        self::Assert(false, $e, $g, $m);
                        continue;
                    }
                    $v_v = igk_getv($o, $k);
                    if (!$validate) {
                        //+ | just check value but not transfrom
                        if (!$v_validator->assertValidate($v_v)) {
                            $error[$k] = sprintf(__('%s is not a valid data'), $k);
                        } else {
                            $ro[$k] = $v_v;
                        }
                    } else {
                        $v_e = [];
                        if ($v_validator instanceof IFormValidationFieldHost) {
                            if (is_null($v_field_info)) {
                                $v_field_info = Activator::CreateNewInstance(FormFieldInfo::class, (array)$v);
                            }
                            $v_validator->setFieldInfo($v_field_info);
                        } 
                        // + | passing extra parameter
                        $v_new = $v_validator->validate($v_v, $v->default, $v_e, $k, $v->required, $v->allowNull, $v->allowEmpty, $v->field);
                        if (empty($v_e)) {
                            $ro->$k = $v_new;
                        } else {
                            $error[$k] = $v_e;
                        }
                    }
                } else {
                    igk_die(sprintf(__('missing FormFieldValidationInfo for %s'), $k));
                }
            }
        }
        if ($error && count($error)) {
            // $g->div()->text('some data');
            if ($g->childCount()>0){
                $error[] = $g->render();
            }
            return false;
        }
        return $ro;
    }
}