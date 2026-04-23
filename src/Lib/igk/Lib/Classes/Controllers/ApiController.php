<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApiController.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;
use IGK\System\Applications\WebApplication;
use IGK\System\Exceptions\ArgumentTypeNotValidException; 
use IGKException;
use ReflectionException;

/**
 * represent api controller
 * @package IGK\Controllers
 */
abstract class ApiController extends ControllerTypeBase{
    /**
    * Bootstrap.
    * @param mixed $app
    */
    protected function bootstrap($app){
        WebApplication::InitWebAppLibrary($app); 
    }
    /**
     * default index controller 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    /**
     * invoke base controller
     */
}