<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationServiceLoginProviderTrait.php
// @date: 20221109 22:52:02
namespace IGK\System\Controllers\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Controllers\Traits
*/
trait ApplicationServiceLoginProviderTrait{
    /**
     * login service
     * @return string 
     */
    protected function loginUri():string{
        return $this->uri('ServiceLogin');
    }
    /**
     * register service
     * @return string 
     */
    protected function registerUri():string{
        return $this->uri('registerLogin');
    }
    /**
     * change password
     * @return string 
     */
    protected function resetUserPwdUri():string{
        return $this->uri('changePassword');
    }
}