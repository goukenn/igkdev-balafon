<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbMigrationMethods.php
// @date: 20230617 10:18:39
namespace IGK\System\Database;

/**
* migration operation
* @package IGK\System\Database
*/
interface IDbMigrationMethods{
    /**
    * Db add column.
    * @param string $table
    * @param mixed $columnInfo
    * @param null|string $after
    */
    function db_add_column(string $table, $columnInfo, ?string $after=null);
    function db_rm_column(string $table, $columnInfo);
}