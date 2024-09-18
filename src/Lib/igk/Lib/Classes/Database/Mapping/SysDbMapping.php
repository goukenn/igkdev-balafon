<?php
// @author: C.A.D. BONDJE DOUE
// @file: SysDbMapping.php
// @date: 20231005 11:49:46
namespace IGK\Database\Mapping;

use IGK\Controllers\SysDbController;
use IGK\Models\ModelBase;
use IGK\System\Database\Mapping\DefaultMap;
use IGK\System\Database\Mapping\ModelMappingBase;
use IGK\System\EntryClassResolution;
use IGKException;

///<summary></summary>
/**
* map database column field to object
* @package IGK\Database\Mapping
*/
class SysDbMapping extends ModelMappingBase{
    protected $m_info;


    public function __invoke($o){
        return $this->map($o);
    }
    /**
     * array of mode
     * @param array<ModelBase> $arr 
     * @return array<string|int, mixed> 
     */
    public function mapArray(array $arr){
        return array_map($this, $arr);
    }
    public function map(ModelBase $model){
        $this->m_info  = $this->m_info ?? $this->initInfoFromModel($model);        
        $prefix = $this->m_info['prefix'];
        $columns = $this->m_info['columns'];
        $ln = strlen($prefix);
        $tab = [];
        foreach(array_keys($columns) as $k){
            $v_nk = $this->resolveMapColumn($k, $prefix) ?? igk_die('not allowed'); 
            $tab[$v_nk]=$model->$k;
        }
        return (object)$tab;
    }
    /**
     * resolve map column 
     * @param string $column 
     * @param ?string $prefix 
     * @return string 
     */
    protected function resolveMapColumn($column, $prefix){        
        $v_nk = $prefix && igk_str_startwith($column, $prefix)? ltrim(substr($column, strlen($prefix)),' _') : $column;
        return lcfirst($v_nk);
    }
    /**
     * create a model db mapping 
     * @param ModelBase $model 
     * @return object 
     * @throws IGKException 
     */
    public static function CreateMapping(ModelBase $model){
        $n = basename(igk_uri(get_class($model)));
        $cl = null;
        $ctrl = $model->getController();
        if (($ctrl instanceof SysDbController)){
            $cl = __CLASS__."\\".$n;
        } else{
            $q = [$n];
            if (!igk_str_endwith($n, 'Mapping')){
                $q[] = igk_str_add_suffix($n, 'Mapping');
            }
            while((count($q)>0)&&!$cl){
                $n = array_shift($q);
                $cl = $ctrl->resolveClass(EntryClassResolution::DbClassMapping ."/".$n);
            }
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
   
        return ['columns'=>$v_columns, 'prefix'=>$v_prefix];
    }
   
}