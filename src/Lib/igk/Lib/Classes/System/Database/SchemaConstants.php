<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaConstants.php
// @date: 20260524 13:05:52
namespace IGK\System\Database;

use PhpMyAdmin\Utils\ForeignKey;

/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class SchemaConstants{
    /**
     * index  tag name 
     */
    const IndexTagName = 'Index';
    /**
     * foreign contraint 
     */
    const ForeignKeyTagName = IGK_FOREIGN_CONSTRAINT;
}