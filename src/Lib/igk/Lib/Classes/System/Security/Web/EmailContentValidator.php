<?php
// @author: C.A.D. BONDJE DOUE
// @file: EmailContentValidator.php
// @date: 20230129 12:26:01
namespace IGK\System\Security\Web;
use IGKValidator;
use function igk_resources_gets as __;

/**
* field content security to validate an email 
* @package IGK\System\Security\Web
*/
class EmailContentValidator  extends MapContentValidatorBase{
    /**
    * auto generate doc.
    * @param mixed $error
    * @return mixed
    */
    public function validate(&$value, $key, $missing=false) : bool{      
        if (!IGKValidator::IsEmail($value)){
            if ($missing){
                $this->notvalid_msg = sprintf(__('missing %s'), $key);    
            }else{
                $this->notvalid_msg = sprintf(__('not valid email'));
            } 
            return false;
        }
        return true;
     }
}