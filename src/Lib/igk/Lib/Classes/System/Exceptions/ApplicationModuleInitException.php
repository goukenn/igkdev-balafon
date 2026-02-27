<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleInitException.php
// @date: 20221113 12:02:22
namespace IGK\System\Exceptions;
use IGK\Controllers\ApplicationModuleController; 
use IGKException;
use Throwable;

/**
* auto generate doc.
* @package IGK\System\Exceptions
*/
class ApplicationModuleInitException extends IGKException{

    /**
    * Property: module.
    * @var mixed
    */
    var $module;

    /**
    * .ctr
    * @param ApplicationModuleController $module
    * @param mixed $code
    * @param null|Throwable $throwable
    */
    public function __construct(ApplicationModuleController $module, $code, ?Throwable $throwable)
    {
        $this->module = $module;
        parent::__construct(
            sprintf("module error : %s - %s\n%s", 
            get_class($module),
            $module->getName(),
            $throwable ? $throwable->getMessage() : null
        ), $code, $throwable);
    }
}