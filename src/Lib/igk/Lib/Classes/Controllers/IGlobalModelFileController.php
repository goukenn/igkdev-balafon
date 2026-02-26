<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGlobalModelFileController.php
// @date: 20230526 00:15:42
namespace IGK\Controllers;
/**
* 
* @package IGK\Controllers
*/
interface IGlobalModelFileController{

    /**
    * auto generate doc.
    * @return bool
    */
    function injectBaseModel();
    function handleModelCreation($table_list):bool;
}