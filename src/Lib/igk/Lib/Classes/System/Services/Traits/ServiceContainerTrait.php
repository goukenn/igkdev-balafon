<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServiceContainerTrait.php
// @date: 20250809 16:12:42
namespace IGK\System\Services\Traits;

use IGK\Services\IAppService;
use IGKServices;

/**
 * 
 * @package IGK\System\Services\Trait
 * @author C.A.D. BONDJE DOUE
 */

/**
* auto generate doc.
* @package IGK\System\Services\Traits
*/
trait ServiceContainerTrait
{
    /**
     * list of items in this service container
     * @var array
     */
    protected $m_container;

    /**
    * Name of name.
    * @var mixed
    */
    protected $m_name;

    /**
    * Sets Name.
    * @param null|string $name
    */
    public function setName(?string $name)
    {
        $this->m_name = $name;
    }

    /**
    * Returns Name.
    * @return ?string
    */
    public function getName(): ?string
    {
        return $this->m_name;
    }

    /**
    * auto generate doc.
    * @return array
    */

    public function listServicesKeys(): array
    {
        $c = [];
        $l = IGKServices::getInstance()->services();
        $fn = implode('', [$this->getName(), IGKServices::PATH_SEPARATOR]);
        foreach (array_keys($l) as $m) {
            if (igk_str_startwith($m, $fn)) {
                $n = substr($m, strlen($fn));
                $c[] = $n;
            }
        }
        sort($c);
        return $c;
    }
    /**
     * init all services 
     * @return array 
     */

    public function initAllSevices(): array
    {
        $all = [];
        foreach ($this->listServicesKeys() as $m) {
            $all[] = IGKServices::Get($this->getName() . '.' . $m) ?? igk_die('failed to initialize service');
        }
        return $all;
    }

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_container = [];
    }

    /**
    * Returns.
    * @param string $name
    * @return ?IAppService
    */
    public function get(string $name): ?IAppService
    {
        return igk_getv($this->m_container, $name);
    }

    /**
    * auto generate doc.
    * @param IAppService $service
    * @return bool
    */

    public function register(string $name, IAppService $service): bool
    {
        $this->m_container[$name] = $service;
        return true;
    }

    /**
    * Returns count of.
    * @return int
    */
    public function count(): int
    {
        return count($this->m_container);
    }
}
