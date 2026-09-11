<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppBaseContainerService.php
// @date: 20260702 16:52:15
namespace IGK\System\Services;

use IGK\Services\IAppService;
use IGK\Services\IAppServiceContainer;
use IGK\System\Services\Traits\ServiceContainerTrait;

/**
* 
* @package IGK\System\Services
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Services
*/
class AppBaseContainerService implements IAppServiceContainer{
    use ServiceContainerTrait {
        register as traitRegister;
    }
    /**
    * auto generate doc.
    * @return array
    */
    public function getConfigurableProperties(): array
    {
        return [];
    }
    /**
    * auto generate doc.
    * @param null|mixed $configs
    * @return bool
    */
    public function init($configs = null): bool
    {
        return true;
    } 
}