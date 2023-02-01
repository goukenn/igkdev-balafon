<?php
// @author: C.A.D. BONDJE DOUE
// @file: EmailContentValidator.php
// @date: 20230129 12:26:01
namespace IGK\System\Security\Web;

use IGKValidator;
use function igk_resources_gets as __;

///<summary></summary>
/**
* validate email 
* @package IGK\System\Security\Web
*/
class EmailContentValidator  extends MapContentValidatorBase{

    public function map($value, $key, &$error) {
        if (!IGKValidator::IsEmail($value)){
            $error[$key] = sprintf(__('not valid email'));
            return false;
        }
        return $value;
     }

}