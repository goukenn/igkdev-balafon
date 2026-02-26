<?php
// @author: C.A.D. BONDJE DOUE
// @file: SQLQueryFieldPrefixOperators.php
// @date: 20251113 09:06:46
namespace IGK\System\Database;

use IGK\Database\DbFieldOperator;

/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class SQLQueryFieldPrefixOperators extends DbFieldOperator{

    /**
    * auto generate doc.
    * @var mixed
    */
    const FIND = '@@';

    /**
    * auto generate doc.
    * @var mixed
    */
    const IN = '#';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NOT_IN = '!<>';

    /**
    * auto generate doc.
    * @var mixed
    */
    const IN_EXPRESS = '<>';

    /**
    * auto generate doc.
    * @var mixed
    */
    const IN_E = '!!';

    /**
    * auto generate doc.
    * @param string $column
    * @return string
    */
    public static function IN(string $column):string{
        return self::IN_EXPRESS.$column;
    }

    /**
    * auto generate doc.
    * @param string $column
    */
    public static function Find(string $column){
        return self::FIND.$column;
    }
}