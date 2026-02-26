<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbColumnInfoPropertyConstants.php
// @date: 20221114 14:08:20
namespace IGK\Database;
use IGK\System\Exceptions\OperationNotAllowedException;
/**
* 
* @package IGK\Database
*/
abstract class DbColumnInfoPropertyConstants{

    /**
    * Constant: column info.
    * @var mixed
    */
    const ColumnInfo = 'columnInfo';

    /**
    * Constant: def table name.
    * @var mixed
    */
    const DefTableName = 'defTableName';

    /**
    * Constant: display.
    * @var mixed
    */
    const Display = 'Display';

    /**
    * Constant: description.
    * @var mixed
    */
    const Description = 'Description';

    /**
    * Constant: table.
    * @var mixed
    */
    const Table = 'table';

    /**
    * Triggered when calling an inaccessible or undefined static method.
    * @param mixed $name
    * @param mixed $arguments
    */
    public static function __callStatic($name, $arguments){
        throw new OperationNotAllowedException('constant property used');
    }    
}