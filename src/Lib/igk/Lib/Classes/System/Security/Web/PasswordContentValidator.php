<?php
// @author: C.A.D. BONDJE DOUE
// @file: PasswordContentValidator.php
// @date: 20230129 12:28:57
namespace IGK\System\Security\Web;
use IGK\System\Html\Forms\Validations\PasswordValidator;
use function igk_resources_gets as __;

/**
 * Password Content validator 
 * @package IGK\System\Security\Web
 */
class PasswordContentValidator extends MapContentValidatorBase
{
    /**
    * Validates.
    * @param mixed & $value
    * @param mixed $key
    * @return bool
    */
    protected function validate(&$value, $key): bool
    {
        // + | PasswordRules  
        $terror = false;
        $error = [];
        if (is_null($value)) {
            $error[$key] = __("password can't be null");
            $terror = true;
        }
        else if (empty($value)) {
            $error[$key] = __('password is empty');
            $terror = true;
        } else {
            $pwd = new PasswordValidator;
            if ($pwd->validate($value, null, $error) != $value){
                $error[$key] = __('missing requirement');
                $terror = true;
            }
        }
        if ($error) {
            $this->notvalid_msg = $error;
        }
        return (!$terror) ? $value : false;
    }
}