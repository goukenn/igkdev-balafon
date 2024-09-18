<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbColumnInfoTrait.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Database\Traits;

/**
 * use to definie property to load 
 */
trait DbDataDefinitionTrait{
    /**
     * table name
     * @var string
     */
    var $TableName;
    /**
     * 
     * @var ?string
     */
    var $RefKey;
    /**
     * table's description 
     * @var ?string
     */
    var $Description;
    /**
     * prefix used to generation column name
     * @var ?string
     */
    var $Prefix;
}