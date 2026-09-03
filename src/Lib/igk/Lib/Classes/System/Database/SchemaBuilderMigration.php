<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaBuilderMigration.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Database;

use igk;
use IGK\System\Caches\DBCaches;
use IGKException;

/**
 * update schema migrations
 * @package IGK\System\Database
 */
class SchemaBuilderMigration
{
    /**
     * Property: controller.
     * @var mixed
     */
    var $controller;
    /**
     * listerner call after
     * @var mixed
     */
    var $listener;
    /**
     * auto generate doc.
     * @var mixed
     */
    private $items;
    /**
     * migration info listener 
     * @var ?IMi
     */
    var $migrationListener;
    /**
     * use method to add migrations data 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return object 
     * @throws IGKException 
     */
    public function __call($name, $arguments)
    {
        $cl = __NAMESPACE__ . "\\Schema" . ucfirst($name) . "Migration";
        if (class_exists($cl) && is_subclass_of($cl, SchemaMigrationItemBase::class)) {
            if (!$this->items) {
                $this->items = [];
            }
            $c = new $cl($this);
            $this->items[] = $c;
            return $c;
        }
        throw new IGKException("schema builder not allowed : $cl:: for :: " . $name);
    }
    /**
     * auto generate doc.
     * @return bool
     */
    public function upgrade()
    {
        if (!$this->items) return false;
        foreach ($this->items as $c) {
            $c->up();
        }
        if ($this->listener) {
            $this->listener->up();
        }
        return true;
    }
    /**
     * Downgrade.
     */
    public function downgrade()
    {
        if (!$this->items)
            return false;
        foreach ($this->items as $c) {
            $c->down();
        }
        if ($this->listener) {
            $this->listener->down();
        }
        return true;
    }
    /**
     * .ctr
     */
    public function __construct() {}

    private function _mig(){
        if (($m = $this->migrationListener) instanceof ISchemaMigrationInfoListener){

            return $m;
        }
    }
    /**
     * retrieve prefix from migration profile 
     * @param string $table 
     * @return mixed|null 
     */
    public function getTablePrefix(string $table)
    {
        if ($mig = $this->_mig()){
            $v_defTable = $mig->getTableSchemaFileDefinition($table);
            return $v_defTable->prefix;
        }
        if ($r = DBCaches::Get($table)){
            return $r->prefix;
        }
    }
    public function getLinkTablePrefix(string $table)
    {
        if ($mig = $this->_mig()){
            $ltab = $mig->getTableSchemaFileDefinition($table);
            return igk_getv($ltab, 'prefix');
        }
        if ($r = DBCaches::Get($table)){
            return $r->prefix;
        }
    }
}
