<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbQuerySelectColumnBuilder.php
// @date: 20230614 20:57:23
namespace IGK\System\Database;

use Exception;
use IGK\Database\DbExpression;
use IGK\Database\DbQueryCondition;

///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class DbQuerySelectColumnBuilder{
    private $m_tab = [];
    private $m_uniques = [];
    public function getColumns(bool $filter_null){
        $cp =null; 
        $res = [];
        foreach($this->m_tab as $k=>$v){
            $res[$k] = $v;
        }
        foreach($this->m_uniques as $tab){
            $res[] = DbQueryCondition::Create($tab);
        } 
        if ($filter_null){
            $res = array_filter($res);
        }
        return $res;
    }
    public function addUnique($cl, $value){
        $this->m_tab[$cl] = $value;
    }
    public function addUniqueColumn($index, $cl, $value ){
        if (!$index){
            $index = -1;
        }
        if (!isset($this->m_uniques[$index])){
            $this->m_uniques[$index] = [];
        }
        $this->m_uniques[$index][$cl] = $value;
    }
    private function __construct(){
    }

    public static function Build($info, $conditions, bool $filter_null = false){
        if (is_null($info))
        {
            return;
        }
        if ($info instanceof SchemaMigrationInfo){
            $info = $info->columnInfo;
        }
        $i = new static;
        foreach($info as $k=>$cl){
            if (is_string($cl)){
                igk_wln_e("not and object", $cl);
            }
            $v = igk_getv($conditions, $k);
            if ($cl->clIsUnique){
                if (is_null($v) && !$cl->clNotNull ){
                    igk_wln('condition : ', $conditions);
                    throw new Exception('null value not allowed for : '. $k);
                }
                $i->addUnique($k, $v); 
            }
            if ($cl->clIsUniqueColumnMember){
                $i->addUniqueColumn($cl->clColumnMemberIndex, $k, $v);
            }
        }
        return $i->getColumns($filter_null);
    }
}