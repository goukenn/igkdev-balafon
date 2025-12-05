<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaIndexInfo.php
// @date: 20251204 20:45:45
namespace IGK\System\Database;


/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
class SchemaIndexInfo{
    /**
     * list of comma separated column of the index
     * @var ?string
     */
    var $columns;
    /**
     * name of the index
     * @var string
     */
    var $name;

    /**
     * is unique definition
     * @var ?bool
     */
    var $isUnique;
}