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
    const FIND = '@@';
    const IN = '#';
    const NOT_IN = '!<>';
    const IN_EXPRESS = '<>';
    const IN_E = '!!'; 

    public static function IN(string $column):string{
        return self::IN_EXPRESS.$column;
    }
    public static function Find(string $column){
        return self::FIND.$column;
    }
}