<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbRetrieveColumnInfoDriver.php
// @date: 20231221 06:58:20
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
*/
interface IDbRetrieveColumnInfoDriver{

    /**
    * auto generate doc.
    * @param string $table
    * @param null|string $column
    * @return array
    */
    function getColumnInfo(string $table, ?string $column=null): array;
}