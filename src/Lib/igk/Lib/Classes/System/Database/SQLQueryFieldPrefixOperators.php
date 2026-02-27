<?php
// @author: C.A.D. BONDJE DOUE
// @file: SQLQueryFieldPrefixOperators.php
// @date: 20251113 09:06:46
namespace IGK\System\Database;

use IGK\Database\DbFieldOperator;

/**
* auto generate doc.
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class SQLQueryFieldPrefixOperators extends DbFieldOperator{

    /**
    * Constant: find.
    * @var mixed
    */
    const FIND = '@@';

    /**
    * Constant: in.
    * @var mixed
    */
    const IN = '#';

    /**
    * Constant: not in.
    * @var mixed
    */
    const NOT_IN = '!<>';

    /**
    * Constant: in express.
    * @var mixed
    */
    const IN_EXPRESS = '<>';

    /**
    * Constant: in e.
    * @var mixed
    */
    const IN_E = '!!';

    /**
    * In.
    * @param string $column
    * @return string
    */
    public static function IN(string $column):string{
        return self::IN_EXPRESS.$column;
    }

    /**
    * Finds.
    * @param string $column
    */
    public static function Find(string $column){
        return self::FIND.$column;
    }
}