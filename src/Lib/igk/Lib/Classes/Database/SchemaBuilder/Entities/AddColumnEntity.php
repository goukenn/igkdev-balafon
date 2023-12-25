<?php
// @author: C.A.D. BONDJE DOUE
// @file: AddColumnEntity.php
// @date: 20231224 14:29:52
namespace IGK\Database\SchemaBuilder\Entities;

use IGK\Controllers\BaseController;
use IGK\System\Database\SchemaBuilderMigration;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\Database\SchemaBuilder\Entities
 */
class AddColumnEntity extends Factory implements IDiagramVisitorEntity
{


    public function updateSchema($schemaInfo, $operation = 'up')
    {
        // add manually update 
        $schema = $schemaInfo;
        $v_info = $this->_props['property']->getColumnInfo();
        $v_rtable = igk_db_get_table_name($this->_table, $this->_controller);
        if (isset($schema->tables[$v_rtable])) {
            if ($operation == 'up') {
                $schema->tables[$v_rtable]->columnInfo[$v_info->clName] = $v_info;
            } else {
                unset($schema->tables[$v_rtable]->columnInfo[$v_info->clName]);
            }
        }
    }
    /**
     * create a migration builder 
     * @param BaseController $controller 
     * @param mixed $props 
     * @return SchemaBuilderMigration 
     * @throws IGKException 
     */
    public function setup(BaseController $controller, $schema, $props): SchemaBuilderMigration
    {
        $this->_controller = $controller;
        $this->_props = $props;
        $this->_schema = $schema;

        $mig = new SchemaBuilderMigration;
        $mig->controller = $controller;
        $mig->listener = $this;
        $cl = $mig->addColumn();
        $v_info =  $props['property']->getColumnInfo();
        $v_table = $props['table'];
        $cl->setup(
            $v_table,
            $v_info,
            igk_getv($props, 'after')
        );
        $this->_mig = $mig;
        $this->_table = $v_table;
        return $mig;
    }
}
