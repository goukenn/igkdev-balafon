<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaDiagramVisitor.php
// @date: 20221104 13:21:22
namespace IGK\Database\SchemaBuilder;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemasConstants;
use IGK\Database\SchemaBuilder\Entities\Factory;
use IGK\Helper\Activator;
use IGK\System\Console\Logger as ConsoleLogger;
use IGK\System\Database\SchemaAddColumnMigration;
use IGK\System\Database\SchemaBuilderMigration;
use IGK\System\Database\SchemaDeleteTableMigration;
use IGK\System\Database\SchemaMigrationInfo;
use IGKEvents;
use IGKSysUtil;
use Logger;

///<summary></summary>
/**
 * visit diagrams
 * @package IGK\Database\SchemaBuilder
 */
class SchemaDiagramVisitor extends DiagramVisitor
{
    private $m_data;
    private $m_controller;
    private $m_migrations = []; 
    private $m_entityHandler;
    private $m_operation;
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
        $this->m_operation = $operation;
    }
    /**
     * visit diagram 
     * @param mixed $entity 
     * @return void 
     */
    public function visitDiagramEntity($entity)
    {
        $defTableName = $entity->getName();
        $tb = IGKSysUtil::DBGetTableName($defTableName, $this->m_controller);
        // + | --------------------------------------------------------------------
        // + | init schema migration info
        // + | 
        if (!isset($this->m_data->tables[$tb])) {
            $t = new SchemaMigrationInfo;
        } else {
            $t = Activator::CreateNewInstance(SchemaMigrationInfo::class, (array)$this->m_data->tables[$tb]);
        }
        $t->columnInfo =  $entity->getProperties();
        // + | resolv link type 
        foreach ($t->columnInfo as $cl) {
            if ($cl->clLinkType) {
                $cl->clLinkType = IGKSysUtil::DBGetTableName($cl->clLinkType, $this->m_controller);
            }
        }
        // + | copy definition 
        $t->description = $entity->getDescription() ?? $t->description;
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
    public function visitDiagramMigration(DiagramMigration $migration)
    {
        /// TODO: add visit diagram migration 
        $mig = null;
        $r = &$this->m_data;
        $props = $migration->properties;
        if (in_array($migration->type, ['addIndex', 'dropIndex'])) {
            $f = $migration->type;
            $mig = new SchemaBuilderMigration;
            $mig->controller = $this->m_controller;
            $index = $mig->$f();
            $index->setup($props['table'], $props['property']);
        } else {
            if ($p = Factory::Create($migration->type)) { 
                $mig = $p->setup($this->m_controller, $this->m_data, $props);
                $this->m_entityHandler[] = $p;
                //update schema
                //$p->updateSchema($this->m_data);
            } else { 
                switch ($migration->type) {
                    case 'dropEntity':
                        $migrations = new SchemaBuilderMigration;
                        $migrations->controller = $this->m_controller;
                        $mig = new SchemaDeleteTableMigration($migrations);
                        $mig->tables = [$migration->properties['name']];
                        $r->migrations[] = $mig;
                        break; 
                    case 'dropColumn':
                        $mig = new SchemaBuilderMigration;
                        $mig->controller = $this->m_controller;
                        $cl = $mig->dropColumn();
                        $cl->setup(
                            $props['table'],
                            $props['property']
                        );
                        break;
                        // case 'changeColumn':
                        //     break;
                    default:
                        throw new Exception('type not supported');
                        break;
                }
            }
        }
        if ($mig) {
            $r->migrations[] = $mig;
            $this->m_migrations[] = $mig;
        }
    }
    var  $callback;

    /**
     * finish diagram visit 
     * @return null|string 
     */
    public function complete(): ?string
    {
        if ($this->m_migrations) {
            $this->callback = function ($e) {
                // update database with migration - setting 
                $ctrl = $e->args['ctrl'];
                $type = $e->args['type']; 

                $r = DbSchemasConstants::Downgrade;
                if ($ctrl === $this->m_controller) {
                    foreach ($this->m_migrations as $mig) {
                        try {
                            if ($type == $r) {
                                $mig->downgrade();
                            } else {
                                $mig->upgrade();
                            }
                        } catch (Exception $ex) {
                            //continu migration list 
                            ConsoleLogger::warn('some error:' . $ex->getMessage());
                        }
                    }
                    if ($type != $r)
                        igk_unreg_hook(IGKEvents::HOOK_DB_MIGRATE, $this->callback);
                }
            };
            igk_reg_hook(IGKEvents::HOOK_DB_MIGRATE, $this->callback);
        }
        return null;
    }
}

class funcHandler
{
    var $callback;
}
