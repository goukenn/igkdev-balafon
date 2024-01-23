<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbResultType.php
// @date: 20231221 06:21:39
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
interface IDbResultType{
    /**
     * get if result type is boolean
     * @return bool 
     */
    function resultTypeIsBoolean() : bool;
}