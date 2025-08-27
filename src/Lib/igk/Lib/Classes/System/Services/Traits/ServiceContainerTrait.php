<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServiceContainerTrait.php
// @date: 20250809 16:12:42
namespace IGK\System\Services\Traits;

use IGK\Services\IAppService;

/**
* 
* @package IGK\System\Services\Trait
* @author C.A.D. BONDJE DOUE
*/
trait ServiceContainerTrait{
    /**
     * list of items in this service container
     * @var array
     */
    protected $m_container;
    public function __construct()
    {
        $this->m_container = [];
    }
    public function get(string $name): ?IAppService {
        return igk_getv($this->m_container, $name);
     }

     /**
      * 
      * @param string $name 
      * @param IAppService $service 
      * @return bool 
      */
    public function register(string $name, IAppService $service): bool { 
        $this->m_container[$name] = $service;
        return true;
    }

    public function count(): int { 
        return count($this->m_container);
    }
}