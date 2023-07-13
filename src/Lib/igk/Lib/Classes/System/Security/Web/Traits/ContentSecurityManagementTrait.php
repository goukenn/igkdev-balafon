<?php
// @author: C.A.D. BONDJE DOUE
// @file: ContentSecurityManagementTrait.php
// @date: 20230126 21:56:10
namespace IGK\System\Security\Web\Traits;

use IGK\System\Security\Web\MapContentValidatorBase;

///<summary></summary>
/**
* wrapper to get content security management
* @package IGK\System\Security\Web\Traits
*/
trait ContentSecurityManagementTrait{
    /**
     * get mapper content security
     * @param string $name 
     * @return MapContentValidatorBase 
     */
    public function getContentSecurity(string $name):?MapContentValidatorBase{
        return MapContentValidatorBase::Get($name);
    }
}