<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IRegisterOnInitController.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;
use IGK\IController;
interface IRegisterOnInitController extends IController{
    /**
     * get if the controller 
     * @return bool 
     */
    function getCanRegisterOnInit(): bool;
}