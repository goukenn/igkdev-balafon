<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IApplicationControllerManager.php
// @date: 20220831 19:56:09
// @desc: 

namespace IGK\Manager;

use IGK\Controllers\BaseController;
use IGK\Controllers\IControllerManagerObject;

/**
 * represent application manager interface
 * @package IGK\Manager
 */
interface IApplicationControllerManager extends IControllerManagerObject{
    /**
     * get default controller
     * @return null|BaseController 
     */
    function getDefaultController(): ?BaseController;
    /**
     * set the default controller
     * @param null|BaseController $controller 
     * @return mixed 
     */
    function setDefaultController(?BaseController $controller);
   
}