<?php
// @author: C.A.D. BONDJE DOUE
// @file: SysDbMapping.php
// @date: 20231005 11:49:46
namespace IGK\Database\Mapping;

use IGK\Controllers\SysDbController;
use IGK\Models\ModelBase;

///<summary></summary>
/**
* map database column field to object
* @package IGK\Database\Mapping
*/
class SysDbMapping{
    protected $m_info;
    public function __invoke($o){
        return $this->map($o);
    }
    public function map(ModelBase $model){
        $this->m_info  = $this->m_info ?? $this->initInfoFromModel($model);        
        $prefix = $this->m_info['prefix'];
        $columns = $this->m_info['columns'];
        $ln = strlen($prefix);
        $tab = [];
        foreach(array_keys($columns) as $k){
            $v_nk = igk_str_startwith($k, $prefix)? ltrim(substr($k, $ln),' _') : $k;
            $tab[$v_nk]=$model->$k;
        }
        return (object)$tab;
    }
    public static function CreateMapping(ModelBase $model){
        $n = basename(igk_uri(get_class($model)));
        $cl = null;
        $ctrl = $model->getController();
        if (($ctrl instanceof SysDbController)){
            $cl = __CLASS__."\\".$n;
        } else{
            $cl = $ctrl->resolveClass("Database/Mapping/".$n);
        }
        if ($cl && class_exists($cl)){
            $o = new $cl();
        }else{
            $o = new static;
        }
        $o->m_info = $o->initInfoFromModel($model);
        return $o;
    }
    protected function initInfoFromModel($model){
        $v_tabInfo = $model->getTableInfo();//->columns();
        $v_prefix = $v_tabInfo->prefix ?? 'cl';
        $v_columns = $v_tabInfo->columnInfo;
        $ln = strlen($v_prefix);
        return ['columns'=>$v_columns, 'prefix'=>$v_prefix];
    }
}