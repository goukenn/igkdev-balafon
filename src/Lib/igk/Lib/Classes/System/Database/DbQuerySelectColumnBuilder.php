<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbQuerySelectColumnBuilder.php
// @date: 20230614 20:57:23
namespace IGK\System\Database;

use Exception;
use IGK\Database\DbExpression;

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
        foreach($this->m_uniques as $index => $tab){
            $res[] = QueryCondition::Create($tab);
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
        $i = new static;
        foreach($info as $k=>$cl){
            $v = igk_getv($conditions, $k);
            if ($cl->clIsUnique){
                if (is_null($v) && !$cl->clNotNull ){
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