<?php

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
    var $ColumnInfo;

    /**
     * column init entries
     * @var ?array
     */
    var $Entries;

    /**
     * 
     * @var ?string table description
     */
    var $Description;

    /**
     * controller that reference this model definition
     * @var mixed
     */
    var $Controller;

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