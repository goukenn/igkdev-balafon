<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaMigrationInfo.php
// @date: 20220804 08:20:25
// @desc: 

namespace IGK\System\Database;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
 * schema migration info
 * @package IGK\System\Database
 */
class SchemaMigrationInfo implements ArrayAccess{
    use ArrayAccessSelfTrait;
    var $defTableName;
    var $columnInfo; 
    var $controller;
    var $description;
    var $entries;
    var $tableRowReference;
    var $modelClass;
    var $tableName;
    var $definitionResolver;

    public function _access_OffsetGet($n){
        if (property_exists($this, $n)){
            return $this->$n;
        }
    }
}