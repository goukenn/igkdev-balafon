<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaDiagramVisitor.php
// @date: 20221104 13:21:22
namespace IGK\Database\SchemaBuilder;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\Database\SchemaAddColumnMigration;
use IGK\System\Database\SchemaBuilderMigration;
use IGK\System\Database\SchemaDeleteTableMigration;
use IGK\System\Database\SchemaMigrationInfo;
use IGKEvents;
use IGKSysUtil;

///<summary></summary>
/**
* visit diagrams
* @package IGK\Database\SchemaBuilder
*/
class SchemaDiagramVisitor extends DiagramVisitor{
    private $m_data;
    private $m_controller;
    private $m_migrations = []; 
    private $operation = 'migrate';
    /**
     * init SchemaDiagram
     * @param BaseController $controller 
     * @param object $schemaInfo 
     * @return void 
     */
    public function __construct(BaseController $controller, $schemaInfo, $operation = 'migrate')
    {
        $this->m_controller = $controller;
        $this->m_data = $schemaInfo;
        $this->operation = $operation;
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

    /**
     * visit diagram migration 
     * @param DiagramMigration $migration 
     * @return void 
     */
    public function visitDiagramMigration(DiagramMigration $migration){
        /// TODO: add visit diagram migration 
        $mig = null;
        $r = & $this->m_data;
        $props = $migration->properties;
        if (in_array($migration->type, ['addIndex','dropIndex'])){
            $f = $migration->type;
            $mig = new SchemaBuilderMigration;
            $mig->controller = $this->m_controller;
            $index = $mig->$f();
            $index->setup($props['table'], $props['property']); 
        }else{

        switch($migration->type){ 
            case 'dropEntity':
        
                $migrations = new SchemaBuilderMigration;
                $migrations->controller = $this->m_controller;
                $mig = new SchemaDeleteTableMigration($migrations);
                $mig->tables = [$migration->properties['name']];
                $r->migrations[] = $mig;
                break;
            case 'addColumn':
                $mig = new SchemaBuilderMigration;
                $mig->controller = $this->m_controller; 
                 
                $cl = $mig->addColumn(); 
                $cl->setup(
                    $props['table'], 
                    $props['property']->getColumnInfo(), 
                    igk_getv($props, 'after')
                ); 

                $r->migrations[] = $mig;
                $this->m_migrations[] = $mig;
                break;
            // case 'dropColumn':
            //     break;
            // case 'changeColumn':
            //     break;
            default:
                throw new Exception('type not supported');
                break;
        } 
    }
        if ($mig){ 
            $r->migrations[] = $mig;
            $this->m_migrations[] = $mig;
        }
    }
    public function complete():?string 
    { 
        $fc = function($e)use(& $fc){
            $ctrl = $e->args['ctrl'];
            $type = $e->args['type'];
            if($ctrl === $this->m_controller)
            {
                foreach($this->m_migrations as $mig){
                    try{
                        if ($type == 'drop_tables'){
                            $mig->downgrade();
                        } else {
                            $mig->upgrade();
                        }
                    }
                    catch(Exception $ex){
                        //continu migration list 
                    }
                }
                igk_unreg_hook(IGKEvents::HOOK_DB_MIGRATE, $fc); 
            } 
        };
        if ($this->m_migrations){
            igk_reg_hook(IGKEvents::HOOK_DB_MIGRATE, $fc); 
        }
        return null;
    }
}