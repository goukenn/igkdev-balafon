<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramVisitorEntity.php
// @date: 20231224 14:28:45
namespace IGK\Database\SchemaBuilder\Entities;

use IGK\Controllers\BaseController;
use IGK\System\Database\SchemaBuilderMigration;

///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder\Entities
*/
interface IDiagramVisitorEntity{
    /**
     * 
     * @param BaseController $controller 
     * @param object $schema schema info
     * @param mixed $props 
     * @return SchemaBuilderMigration 
     */
    function setup(BaseController $controller, $schema, $props):SchemaBuilderMigration;
    function updateSchema($schemaInfo, string $operation='up'); 
}