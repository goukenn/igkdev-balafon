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
    * auto generate doc.
    * @var mixed
    */
    const ColumnInfo = 'columnInfo';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DefTableName = 'defTableName';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Display = 'Display';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Description = 'Description';

    /**
    * auto generate doc.
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