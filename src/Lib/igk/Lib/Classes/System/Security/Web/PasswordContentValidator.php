<?php
// @author: C.A.D. BONDJE DOUE
// @file: PasswordContentValidator.php
// @date: 20230129 12:28:57
namespace IGK\System\Security\Web;
use function igk_resources_gets as __;

///<summary></summary>
/**
* 
* @package IGK\System\Security\Web
*/
class PasswordContentValidator extends MapContentValidatorBase{

    public function map($value, $key, &$error) { 
        // system password rules
        // + | PasswordRules
      
        if (is_null($value)){
            $error[$key] = __("password can't be null");
            return false;
        }
        if (empty($value)){
            $error[$key] = __('password is empty');
            return false;
        }
        if (strlen($value)<8){
            $error[$key] = __('password length empty');
            return false;
        }
        if (!preg_match('/[^a-z0-9\s]/i', $value) && !preg_match('/[#@+_~\*\-]/i', $value)){
            $error[$key] = __('special char missing');
            return false;
        }
        if (!preg_match('/[0-9]/i', $value)){
            $error[$key] = __('caractère missing');
            return false;
        } 
        return $value;
    }

}