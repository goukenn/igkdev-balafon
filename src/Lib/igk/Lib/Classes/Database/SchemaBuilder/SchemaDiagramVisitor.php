<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaDiagramVisitor.php
// @date: 20221104 13:21:22
namespace IGK\Database\SchemaBuilder;

use IGK\Models\DbModelDefinitionInfo;
use IGK\System\Database\SchemaMigrationInfo;
use IGKSysUtil;
use stdClass;

///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder
*/
class SchemaDiagramVisitor extends DiagramVisitor{
    private $m_data;
    public function __construct($controller, $schemaInfo)
    {
        $this->m_ctrl = $controller;
        $this->m_data = $schemaInfo;
    }
    /**
     * visit diagram 
     * @param mixed $entity 
     * @return void 
     */
    public function visitDiagramEntity($entity){
        $tb = $entity->getName();
        $tb = IGKSysUtil::DBGetTableName($tb, $this->m_ctrl);
        // + | --------------------------------------------------------------------
        // + | init schema migration info
        // + |
        
        $t = new SchemaMigrationInfo;
        $t->columnInfo =  $entity->getProperties();
        $t->description = $entity->getDescription();
        $t->defTableName = $tb;
        $t->tableName = $tb;
        $t->controller = $this->m_ctrl;
        // $this->m_data->tables[$entity->getName()] = $t;
        $this->m_data->tables[$tb] = $t;
    }
}