<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaForeignConstraintInfo.php
// @date: 20230203 22:10:38
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class SchemaForeignConstraintInfo{
    /**
     * column to for column to operate on 
     * @var string comma separated column
     */
    var $on;
    /**
     * table name on foreign key
     * @var mixed
     */
    var $from;
    /**
     * refence keys
     * @var mixed
     */
    var $columns;

    /**
     * foreign key reference
     * @var ?string
     */
    var $foreignKeyName;
}