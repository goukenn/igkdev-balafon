<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAppServiceContainer.php
// @date: 20250809 15:27:20
namespace IGK\Services;

/**
* represent a service c ontainer 
* @package IGK\Services
* @author C.A.D. BONDJE DOUE
*/
interface IAppServiceContainer extends IAppService{
    /**
    * Sets Name.
    * @param null|string $name
    * @return ?string
    */
    function setName(?string $name);
    function getName():?string;
    /**
    * Returns.
    * @param string $name
    * @return ?IAppService
    */
    function get(string $name) : ?IAppService ;
    /**
    * Registers.
    * @param string $name
    * @param IAppService $service
    * @return bool
    */
    function register(string $name, IAppService $service): bool;
    /**
    * Returns count of.
    * @return int
    */
    function count():int;
    /**
    * Lists Services Keys.
    * @return array
    */
    function listServicesKeys():array;
}