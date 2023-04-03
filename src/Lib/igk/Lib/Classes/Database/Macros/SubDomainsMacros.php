<?php
// @author: C.A.D. BONDJE DOUE
// @file: SubDomains.phtml
// @desc: macros for model SubDomains
// @date: 20230224 12:39:16
namespace IGK\Database\Macros;

use IGK\Controllers\BaseController;
use IGK\Models\SubDomains;

///<summary></summary>
/**
* 
* @package IGK\Database\Macros
*/
abstract class SubDomainsMacros{
    /**
     * register submains
     * @param SubDomains $model 
     * @param string $domain 
     * @param BaseController $controller 
     * @param null|string $view 
     * @return null|SubDomains 
     */
    public static function RegisterSubDomain(SubDomains $model, string $domain, BaseController $controller, ?string $view = null){
        return $model::createIfNotExists([
            SubDomains::FD_CLNAME=>$domain,
            SubDomains::FD_CLCTRL=>$controller->getName()
            ],[
            SubDomains::FD_CLVIEW=>$view
        ]);
    }
}