<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaDiagramVisitor.php
// @date: 20221104 13:21:22
namespace IGK\Database\SchemaBuilder;

use IGK\Helper\Activator;
use IGK\System\Database\SchemaMigrationInfo;
use IGKSysUtil;

///<summary></summary>
/**
* visit diagrams
* @package IGK\Database\SchemaBuilder
*/
class SchemaDiagramVisitor extends DiagramVisitor{
    private $m_data;
    private $m_controller;
    public function __construct($controller, $schemaInfo)
    {
        $this->m_controller = $controller;
        $this->m_data = $schemaInfo;
    }
    /**
     * visit diagram 
     * @param mixed $entity 
     * @return void 
     */
    public function visitDiagramEntity($entity){
        $defTableName = $entity->getName();
        $tb = IGKSysUtil::DBGetTableName($defTableName, $this->m_controller);
        // + | --------------------------------------------------------------------
        // + | init schema migration info
        // + | 
        if (!isset($this->m_data->tables[$tb])){
            $t = new SchemaMigrationInfo;
        } else{
            $t = Activator::CreateNewInstance(SchemaMigrationInfo::class, (array)$this->m_data->tables[$tb]);
        }
        $t->columnInfo =  $entity->getProperties();
        // + | resolv link type 
        foreach($t->columnInfo as $cl){
            if ($cl->clLinkType){
                $cl->clLinkType = IGKSysUtil::DBGetTableName($cl->clLinkType, $this->m_controller);
            }
        }
        // + | copy definition 
        $t->description = $entity->getDescription();
        $t->defTableName = $defTableName;
        $t->tableName = $tb;
        $t->controller = $this->m_controller; 
  
        $this->m_data->tables[$tb] = $t;
    }
}