<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileValidator.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Forms\Validations;
use IGK\Helper\Activator;
use IGK\System\Html\Forms\RequestFormFileData;
use IGK\System\Number;
use IGK\System\Text\RegexMatcherContainer;
use function igk_resources_gets as __;

/**
 * file fields validator
 * @package IGK\System\Html\Forms
 */
class FileValidator extends FormFieldValidatorBase implements IFormValidator
{
    /**
    * auto generate doc.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool
    { 
        return file_exists($value);
    }
    /**
    * Validate.
    * @param mixed $value
    * @param null|mixed $default
    * @param mixed & $error
    * @param null|object $options
    */
    protected function _validate($value, $default = null, &$error = [], ?object $options = null)
    {
        $name = $maxSize = $fieldinfo = null;
        if ($options) {
            list($name, $fieldinfo) =  igk_extract($options, sprintf('name|%s', FormFieldValidatorBase::FIELD_INFO_PROPERTY));
        } else {
            igk_die("required options");
        }
        list($v_size, $v_error) = igk_extract($value, 'size|error');
        list($maxSize, $multiple, $accept) = igk_extract($fieldinfo, 'maxSize|multiple|accept');
        if ($accept) {
            $accept = str_replace(",", "|", $accept);
            $accept = str_replace("/", "\\/", $accept);
            $accept = str_replace("*", ".+", $accept);
            $accept = "/".$accept."/";
        }
        if ($maxSize){
            $maxSize = intval(Number::MemoryToBytes($maxSize));
        }
        $ts = $value['name'];
        $is_array = is_array($ts);
        if ($is_array){
            $count = count($ts);
            $v_result = [];
            for($i = 0; $i < $count; $i++){
                $def = [];
                foreach(['name','tmp_name','error','size', 'type'] as $k){
                    $def[$k] = $value[$k][$i];
                }
                $v_error = $def['error'];
                $v_size = $def['size'];
                $v_type = $def['type']; 
                if (null === self::_ValidateEntry($error,$maxSize, $accept,  $v_error, $v_size, $v_type)){
                    return null;
                }
                $v_result[] = Activator::CreateNewInstance(RequestFormFileData::class, $def);  
            } 
            return $v_result;
        }
        else{
            $v_type = $value['type'];
            if (null === self::_ValidateEntry($error,$maxSize, $accept,  $v_error, $v_size, $v_type)){
                return null;
            } 
            $value = Activator::CreateNewInstance(RequestFormFileData::class, $value);  
            return $value;
        }
    }
    /**
     * validate entries
     * @param mixed &$error 
     * @param mixed $maxSize 
     * @param mixed $accept 
     * @param mixed $v_error 
     * @param mixed $v_size 
     * @param mixed $v_type 
     * @return null|void 
     */
    private static function _ValidateEntry(& $error, ?int $maxSize, $accept,  $v_error, $v_size, $v_type){
        if ($v_error) {
            $error[] = $v_error;
            return null;
        }
        if ($maxSize && ($v_size > $maxSize)) {
            $error[] = 'exceed size limit : '.$maxSize.' < '.$v_size;
            return null;
        }
        if ($accept) { 
            if (!preg_match($accept, $v_type)){
                $error[] = 'mime_type unauthorized';
                return null;
            }   
        }
        return 1;
    }
}