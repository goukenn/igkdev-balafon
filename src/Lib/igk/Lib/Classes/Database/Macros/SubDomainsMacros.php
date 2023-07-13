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
* subdomain macros helper 
* @package IGK\Database\Macros
*/
abstract class SubDomainsMacros{

    public static function GetAllActivateDomain(SubDomains $model){
        $driver = $model->getDataAdapter();
        $cond = $model->createCondition();
        $cond->set(
            [
            
                        $model::FD_CL_DEACTIVATE_AT =>null,
                        ">".$model::FD_CL_DEACTIVATE_AT => date($driver->getDateTimeFormat())
            ] 
        );        
        $cond->operand = 'OR';
        return $model->select_all(
            $cond
        );
    }
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