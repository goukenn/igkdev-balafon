<?php
// @author: C.A.D. BONDJE DOUE
// @file: PasswordContentValidator.php
// @date: 20230129 12:28:57
namespace IGK\System\Security\Web;

use function igk_resources_gets as __;

///<summary>Password Content validator </summary>
/**
 * Password Content validator 
 * @package IGK\System\Security\Web
 */
class PasswordContentValidator extends MapContentValidatorBase
{

    protected function validate(&$value, $key): bool
    {

        // system password rules
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
            if (strlen($value) < 8) {
                $error[$key] = __('password length empty');
                $terror = true;
            }
            if (!preg_match('/[^a-z0-9\s]/i', $value) && !preg_match('/[#@+_~\*\-]/i', $value)) {
                $error[$key] = __('special char missing');
                $terror = true;
            }
            if (!preg_match('/[0-9]/i', $value)) {
                $error[$key] = __('missing number');
                $terror = true;
            }
        }
        if ($error) {
            $this->notvalid_msg = $error;
        }
        return (!$terror) ? $value : false;
    }
}
