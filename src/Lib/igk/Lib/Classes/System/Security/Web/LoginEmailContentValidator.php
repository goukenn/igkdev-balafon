<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoginEmailContentValidator.php
// @date: 20250610 17:21:46
namespace IGK\System\Security\Web;
/**
* 
* @package IGK\System\Security\Web
* @author C.A.D. BONDJE DOUE
*/
class LoginEmailContentValidator extends EmailContentValidator{
    public function validate(&$value, $key, $missing = false): bool
    {
        if (!preg_match('/@[^\.]+(\.[^\.]+)+$/', $value)){
            $value .= '@'.igk_configs()->website_domain;
        }
        return parent::validate($value, $key, $missing);
    }
}