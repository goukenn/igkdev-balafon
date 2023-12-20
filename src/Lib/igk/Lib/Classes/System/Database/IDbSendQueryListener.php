<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbSendQueryListener.php
// @date: 20231220 11:53:29
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
interface IDbSendQueryListener{
    function sendQuery(string $query);
}