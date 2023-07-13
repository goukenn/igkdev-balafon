<?php
// @author: C.A.D. BONDJE DOUE
// @file: PasswordContentValidator.php
// @date: 20230129 12:28:57
namespace IGK\System\Security\Web;

use IGKValidator;

use function igk_resources_gets as __;

///<summary>Password Content validator </summary>
/**
 * Password Content validator 
 * @package IGK\System\Security\Web
 */
class LoginAccessContentValidator extends MapContentValidatorBase
{
    /**
     * 
     * @param mixed $v 
     * @param mixed $n 
     * @param mixed $error 
     * @return bool 
     */
  public function validate( &$v, $n):bool{
         $error = null;
            if (is_null($v) || empty($v)){
                $error = __('{0} is empty', $n);
            } else {
                if (!IGKValidator::IsEmail($v)){
                    $v = $v."@".igk_configs()->website_domain;
                }
            } 
        if ($error){
            $this->notvalid_msg = $error;
            return false;
        }
        return true;
    }
}