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
    function setName(?string $name);
    function getName():?string;
    function get(string $name) : ?IAppService ;
    function register(string $name, IAppService $service): bool;
    function count():int;
    function listServicesKeys():array;
}