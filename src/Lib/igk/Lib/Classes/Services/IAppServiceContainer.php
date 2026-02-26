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
    * auto generate doc.
    * @param null|string $name
    * @return ?string
    */
    function setName(?string $name);
    function getName():?string;

    /**
    * auto generate doc.
    * @param string $name
    * @return ?IAppService
    */
    function get(string $name) : ?IAppService ;

    /**
    * auto generate doc.
    * @param string $name
    * @param IAppService $service
    * @return bool
    */
    function register(string $name, IAppService $service): bool;

    /**
    * auto generate doc.
    * @return int
    */
    function count():int;

    /**
    * auto generate doc.
    * @return array
    */
    function listServicesKeys():array;
}