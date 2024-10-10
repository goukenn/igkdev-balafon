<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelMapping.php
// @date: 20230117 14:15:41
namespace IGK\Mapping;

use IGK\Helper\MapHelper;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;


/**
 * default model mapping
 */
class ModelMapping implements IDataMapper{
    /**
     * model to use
     * @var ModelBase
     */
    var $model;

    /**
     * map references
     * @var ?array
     */
    var $references;

    private $m_mapkey;

    public function __construct($model_or_model_class)
    {
        (is_string($model_or_model_class) && is_subclass_of($model_or_model_class, \IGK\Models\ModelBase::class)) || 
        (is_object($model_or_model_class) && $model_or_model_class instanceof ModelBase) || igk_die("not a valid parameter");

        $this->model = $model_or_model_class::model();
    }
    /**
     * map model result data
     * @param string $key 
     * @param mixed $value 
     * @return null|array 
     * @throws IGKException 
     */
    public function map($key, $value): ?array{        

        $map_ref= $key;
        if ($tabinfo = $this->model->getTableInfo()){ 
            $prefix = $tabinfo->prefix;
            if ($prefix){
                if (strpos($key, $prefix) === 0){
                    $key = igk_str_lwfirst(trim(substr($key, strlen($prefix)), ' _'));
                }
            }else{
                $rf = explode("_", $key);
                if (count($rf)>1){
                    $key = igk_str_lwfirst(implode("", array_slice($rf,1)));
                }
            } 
        }
        if (is_object($value)){
            // map submethod
            if ($this->references)
            {
                $tkey = implode(".",array_filter([$this->m_mapkey, $map_ref]));
                if (isset($this->references[$tkey])){
                    $g = $this->references[$tkey];                    
                    $value = MapHelper::Map($value, $g);
                }
            }
        }
        return [$key, $value]; 
    }
    public function __invoke($row)
    {   
        return array_map([$this, 'map'], $row->to_array());
    }
}
