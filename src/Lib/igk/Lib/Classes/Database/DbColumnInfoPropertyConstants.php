<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbColumnInfoPropertyConstants.php
// @date: 20221114 14:08:20
namespace IGK\Database;

use IGK\System\Exceptions\OperationNotAllowedException;

///<summary></summary>
/**
* 
* @package IGK\Database
*/
abstract class DbColumnInfoPropertyConstants{
    const ColumnInfo = 'columnInfo';
    const DefTableName = 'defTableName';
    const Description = 'Description';
    const Table = 'table';

    public static function __callStatic($name, $arguments){
        throw new OperationNotAllowedException('constant property used');
    }    
}