<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbModelDefinitionInfo.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Models;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
 * represent column info description. use in a spécif model. without use data schema
 * @package IGK\Models
 */
class DbModelDefinitionInfo implements ArrayAccess{
    use ArrayAccessSelfTrait; 

    /**
     * column info reference
     * @var array \
     *      array = [[columnId => DbColumnInfo]...]
     */
    var $tableRowReference;

    /**
     * basic column info
     * @var mixed
     */
    var $columnInfo;

    /**
     * column init entries
     * @var ?array
     */
    var $entries;

    /**
     * 
     * @var ?string table description
     */
    var $description;

    /**
     * controller that reference this model definition
     * @var mixed
     */
    var $controller;

    function _access_OffsetSet($n, $v){
        $this->$n = $v;
    }
    function _access_OffsetGet($n){
        return $this->$n;
    }
    function _access_OffsetUnset($n){
        // do nothing
    }
    function _access_offsetExists($n){
        return property_exists($this, $n);
    }

}