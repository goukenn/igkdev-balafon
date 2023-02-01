<?php
// @author: C.A.D. BONDJE DOUE
// @file: IActivatorMandatory.php
// @date: 20230112 04:06:33
namespace IGK\Helper;


///<summary>check for mandatory data</summary>
/**
* check for mandatory data
* @package IGK\Helper
*/
interface IActivatorMandatory{
    function getMandatory():array;
}