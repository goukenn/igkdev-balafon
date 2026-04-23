<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramVisitorEntity.php
// @date: 20231224 14:28:45
namespace IGK\Database\SchemaBuilder\Entities;
use IGK\Controllers\BaseController;
use IGK\System\Database\SchemaBuilderMigration;

/**
* 
* @package IGK\Database\SchemaBuilder\Entities
*/
/**
* auto generate doc.
* @package IGK\Database\SchemaBuilder\Entities
*/
interface IDiagramVisitorEntity{
    /**
    * auto generate doc.
    * @param mixed $props
    * @return SchemaBuilderMigration
    */
    function setup(BaseController $controller, $schema, $props):SchemaBuilderMigration;
    /**
    * Updates Schema.
    * @param mixed $schemaInfo
    * @param string $operation
    */
    function updateSchema($schemaInfo, string $operation='up'); 
}