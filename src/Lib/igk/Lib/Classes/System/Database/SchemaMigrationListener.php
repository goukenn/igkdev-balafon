<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaMigrationListener.php
// @date: 20250124 15:23:18
namespace IGK\System\Database;

use IGK\Helper\Database;

///<summary></summary>
/**
 * 
 * @package IGK\System\Database
 * @author C.A.D. BONDJE DOUE
 */
class SchemaMigrationListener implements ISchemaMigrationInfoListener
{
    /**
     * source controller 
     */
    var $controller;
    /**
     * source file 
     * @var ?string
     */
    var $file;
    /**
     * 
     * @var ?ISchemaMigrationLoadingList
     */
    var $definition;

    public function getTableSchemaFileDefinition(string $tablename)
    {
        return igk_getv($this->definition->tables, $tablename);
    }

    private $m_changes = [];
    /**
     * 
     * @param string $tablename 
     * @return void 
     */
    public function regDefTableChanged(string $tablename)
    {
        $this->m_changes[$tablename] = 1;
    }
    public function didMigrationComplete()
    {
        if ($this->m_changes) {
            $ctrl = $this->controller;

            $keys = array_keys($this->m_changes);
            while (0 < count($keys)) {

                $uniques_column = [];
                $tb = array_shift($keys);
                $tabinfo = $this->getTableSchemaFileDefinition($tb);
                foreach ($tabinfo->columnInfo as $cl) {
                    if ($cl->clIsUniqueColumnMember) {
                        $n = Database::AutoPrefixColumn($cl->clName, $tabinfo->prefix);
                        $uniques_column[$n] = $cl;
                    }
                } 
                if ($uniques_column) {
                    $list = [];
                    foreach ($uniques_column as $u) {
                        $id = $u->clColumnMemberIndex ?? 0;
                        if (is_string($id)) {
                            $r = SQLGrammar::SplitColumnMemberRereference($id);  
                            while (count($r) > 0) {
                                $q = array_shift($r);
                                $id = intval($q);
                                $list[$id][$u->clName] = 1;
                            }
                        } else {
                            $id = intval($id);
                            $list[$id][$u->clName] = 1;
                        }
                    }
                    // 
                    $ctrl->db_drop_uniques($tb);
                    foreach ($list as $t=>$v) {
                        $id = Database::AutoPrefixColumn('UC_unique_'.$t.'_index', $tabinfo->prefix);
                        $ctrl->db_add_unique($tb, array_keys($v), $id);
                    }
                }
            }
        }
    }
}
