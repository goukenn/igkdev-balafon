<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaAddConstraintMigration.php
// @date: 20260823 17:28:51
namespace IGK\System\Database;

use IGK\Controllers\BaseController;
use IGK\Helper\StringUtility;
use IGKSysUtil;

// + | --------------------------------------------------------------------
// + | <addConstraint table>
// + |


/**
 * 
 * @package IGK\System\Database
 * @author C.A.D. BONDJE DOUE
 */
class SchemaAddConstraintMigration extends SchemaMigrationItemBase
{
    protected $fill_properties = ["table"];
    var $table;
    var $constraints;
    var $columns;

    public function load($node)
    {
        $this->table = $node['table'] ?? igk_die('missing required attribute `table`');
        return parent::load($node);
    }
    public function loadChilds($childs)
    {
        foreach ($childs as $c) {
            $tb = $c->getTagName();
            if (method_exists($this, $fc = '_load_' . $tb)) {
                call_user_func_array([$this, $fc], [$c->getChilds()]);
            }
        }
        return parent::loadChilds($childs);
    }
    public function up()
    {
       $ctrl = $this->getMigration()->controller;
       $tb = igk_db_get_table_name($this->table, $ctrl);
       $ctrl::db_add_unique($tb, $this->columns) ;
    }
    public function down()
    {
        // igk_wln_e(__FILE__ . ":" . __LINE__, 'call down');
    }
    /**
     * 
     * @param mixed &$tables 
     * @param BaseController $ctrl 
     * @return void 
     */
    public function doUpgrade(&$tables, BaseController $ctrl)
    {
        $tb = IGKSysUtil::DBGetTableName($this->table, $ctrl);
        $info = igk_getv($tables, $tb);
        $prefix = $info->prefix;
        $uniques = igk_getv($this->constraints, 'unique');

        $max = self::_GetMaxColumnIndex($info->columnInfo);
        $tc =     array_keys($uniques);
        $rc = [];
        foreach ( $tc as $c) {
            $p = StringUtility::AutoPrefix($c, $prefix);
            $p = $info->columnInfo[$p] ?? igk_die('missing column info');
            $rc[] = $p->clName;
            if ($p->clIsUniqueColumnMember) {
                if (!is_array($p->clColumnMemberIndex)) {
                    $p->clColumnMemberIndex = [$p->clColumnMemberIndex];
                }
                $p->clColumnMemberIndex[] = $max;
            } else {
                $p->clIsUniqueColumnMember = true;
                $p->clColumnMemberIndex = $max;
            }
        }
        $this->columns = $rc;
    }
    /**
     * 
     * @param mixed $info 
     * @return mixed 
     */
    private static function _GetMaxColumnIndex($info)
    {
        $max = 0;
        $tmax = [];
        foreach ($info as $v) {
            if ($v->clIsUniqueColumnMember) {
                $i = $v->clColumnMemberIndex;
                if (is_array($i)) {
                    $tmax = array_merge($tmax, $i);
                } else {
                    if (!in_array($i, $tmax)) {
                        $tmax[] = $i;
                    }
                }
            }
        }
        if ($m = array_unique($tmax)){
            rsort($m);
            $max = $m[0] + 1;
        }

        return $max;
    }

    /**
     * 
     * @param mixed $childs 
     * @return void 
     */
    private function _load_UniqueColumns($childs)
    {
        foreach ($childs as $c) {
            $tb = $c->getTagName();
            if ($tb == 'Column') {
                $n = $c['clName'] ?? igk_die('missing `name`');
                $this->constraints['unique'][$n] = $n;
            }
        }
    }
    /**
     * 
     * @param mixed $childs 
     * @return void 
     */
    private function _load_Index($childs)
    {
        foreach ($childs as $c) {
            $tb = $c->getTagName();
            if ($tb == 'Column') {
                $n = $c['clName'] ?? igk_die('missing `name`');
                $this->constraints['index'][$n] = $n;
            }
        }
    }
}
