<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapContentValidatorBase.php
// @date: 20230125 13:19:41
namespace IGK\System\Security\Web;

use IGK\System\IO\Path;
use function igk_resources_gets as __;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
abstract class MapContentValidatorBase
{
    private static $sm_validators;
    protected $notvalid_msg = 'not a valid number.';
    protected $missingDefaultValue = null;
    protected $defaultValue = null;
    protected $allowNullValue = false;

    /**
     * check if can update setting
     * @return bool 
     */
    public function canUpdateSetting():bool{
        return true;
    }
    public function updateSetting($defaultValue, $missingDefault, bool $allowNullValue){
        if (!$this->canUpdateSetting()){
            return false;
        }
        $this->missingDefaultValue = $missingDefault;
        $this->defaultValue = $defaultValue;
        $this->allowNullValue = $allowNullValue;
    }

    public final function __invoke($value, $key, &$error, bool $missing, bool $required )
    {
        return $this->map($value, $key, $error, $missing, $required);
    }
    /**
     * map value 
     * @param mixed $value value to validate
     * @param mixed $key key of the value
     * @param mixed $error error to update 
     * @param mixed $missing key not provider in request
     * @return mixed 
     */
    public function map($value, $key, &$error, bool $missing, bool $required = true){
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
    protected abstract function validate(& $value, $key) : bool;

    /**
     * 
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