<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGlobalModelFileController.php
// @date: 20230526 00:15:42
namespace IGK\Controllers;


///<summary></summary>
/**
* 
* @package IGK\Controllers
*/
interface IGlobalModelFileController{
    function injectBaseModel();
    function handleModelCreation($table_list):bool;
}