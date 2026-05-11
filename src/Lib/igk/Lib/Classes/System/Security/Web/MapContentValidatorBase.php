<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapContentValidatorBase.php
// @date: 20230125 13:19:41
namespace IGK\System\Security\Web;
use IGK\System\IO\Path;
use function igk_resources_gets as __;

/**
* 
* @package IGK\System\Security\Web
*/
/**
* auto generate doc.
* @package IGK\System\Security\Web
*/
abstract class MapContentValidatorBase
{
    /**
    * Property: validators.
    * @var mixed
    */
    private static $sm_validators;
    /**
    * Property: notvalid msg.
    * @var mixed
    */
    protected $notvalid_msg = 'not a valid number.';
    /**
    * Property: missing default value.
    * @var mixed
    */
    protected $missingDefaultValue = null;
    /**
    * Property: default value.
    * @var mixed
    */
    protected $defaultValue = null;
    /**
    * Property: allow null value.
    * @var mixed
    */
    protected $allowNullValue = false;
    /**
     * allow null value
     * @return bool 
     */
    public function getAllowNullValue(){
        return $this->allowNullValue;
    }
    /**
     * chain list to create map content definition 
     * @param bool $allowNull 
     * @return $this 
     */
    public function allowNull(bool $allowNull){
        $this->allowNullValue = $allowNull;
        return $this;
    }
    /**
     * check if can update setting
     * @return bool 
     */
    public function canUpdateSetting():bool{
        return true;
    }
    /**
    * Updates Setting.
    * @param mixed $defaultValue
    * @param mixed $missingDefault
    * @param bool $allowNullValue
    */
    public function updateSetting($defaultValue, $missingDefault, bool $allowNullValue){
        if (!$this->canUpdateSetting()){
            return false;
        }
        $this->missingDefaultValue = $missingDefault;
        $this->defaultValue = $defaultValue;
        $this->allowNullValue = $allowNullValue;
    }
    public final
    /**
    * Called when an object is used as a function.
    * @param mixed $value
    * @param mixed $key
    * @param mixed & $error
    * @param bool $missing
    * @param bool $required
    */
    function __invoke($value, $key, &$error, bool $missing, bool $required )
    {
        return $this->map($value, $key, $error, $missing, $required);
    }
    /**
    * map value
    * @param mixed $value value to validate
    * @param mixed $key key of the value
    * @param mixed & $error
    * @param mixed $error error to update
    * @param bool $required
    * @return mixed
    */
    public function map($value, $key, &$error, bool $missing=false, bool $required = true){
        if (is_null($value)){
            if (!$this->allowNullValue && $this->defaultValue && $required ){
                return $this->defaultValue;
            }
        }
        if ($this->validate($value, $key)){
            return $value;
        }
        if ($this->allowNullValue && is_null($value)){
            return null;
        }
        $cvalue = $this->handleError($value, $key, $error, $missing, $required, $error_value);
        if ($error_value)
            return false;
        return $cvalue;
    }
    /**
    * Validates.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    protected abstract function validate(& $value, $key) : bool;
    /**
    * auto generate doc.
    * @param string $t
    * @return static
    */
    public static function Get(string $t)
    {
        $cl = igk_str_ns( Path::Combine(__NAMESPACE__, sprintf('%sContentValidator', $t)));
        if (!isset(self::$sm_validators[$cl])) {
            if (!is_subclass_of($cl, self::class)){
                igk_die(sprintf(__("%s class not an subclass of %s "),$cl, self::class));
            }
            $g = new $cl();
            self::$sm_validators[$cl] = $g;
            return $g;
        }
        return self::$sm_validators[$cl];
    }
    /**
     * create a new instance of the validator
     * @return object 
     */
    public function createNewInstance(){
        $cl = static::class;
        return new $cl();
    }
    /**
    * Handles Error.
    * @param mixed $value
    * @param mixed $key
    * @param mixed & $error
    * @param mixed $missing
    * @param bool $required
    * @param null|bool & $error_value
    */
    protected function handleError($value, $key, &$error, $missing , bool $required, ?bool & $error_value){
        $error_value = false;
        if (!$required){
            if ($missing){
                return $this->missingDefaultValue;
            }
            return $this->defaultValue;
        }
        if ($missing){
            $error = 'missing value.';
        }else {
            $error = $this->notvalid_msg;
        } 
        $error_value = true; 
    }
}