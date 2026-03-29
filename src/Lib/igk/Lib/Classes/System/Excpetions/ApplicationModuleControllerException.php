<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleControllerException.php
// @date: 20260320 08:14:30
namespace IGK\System\Excpetions;

use Error;
use IGK\Controllers\ApplicationModuleController;
use IGKException;
use Throwable;

/**
* auto generate doc.
* @package IGK\System\Excpetions
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Excpetions
*/
class ApplicationModuleControllerException extends IGKException{
    private $m_controller;

    /**
    * .ctr
    * @param ApplicationModuleController $controller
    * @param string $message
    * @param mixed $code
    * @param null|Throwable $throwable
    * @return
    */
    public function __construct(ApplicationModuleController $controller, string $message, $code = 500, ?Throwable $throwable = null)
    {
        $this->m_controller= $controller;
        parent::__construct($message, $code, $throwable); 
    }

    /**
    * auto generate doc.
    * @return ApplicationModuleController
    */
    public function getController(): ApplicationModuleController{
        return $this->m_controller;
    }
}