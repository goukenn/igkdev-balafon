<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbRetrieveColumnInfoDriver.php
// @date: 20231221 06:58:20
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
interface IDbRetrieveColumnInfoDriver{
    function getColumnInfo(string $table, ?string $column=null): array;
}