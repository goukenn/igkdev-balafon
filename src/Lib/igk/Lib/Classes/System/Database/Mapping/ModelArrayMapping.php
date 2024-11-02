<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelArrayMapping.php
// @date: 20241007 16:04:08
namespace IGK\System\Database\Mapping;

use Exception;
use IGK\Models\ModelBase;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Mapping
* @author C.A.D. BONDJE DOUE
*/
class ModelArrayMapping{
    protected $info;
    protected $model;
    /**
     * 
     * @param ModelBase $model base model
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     */
    public function __construct(ModelBase $model)
    {
        $this->info = $model->getTableInfo();
        $this->model = $model;
    }
    public function __invoke($a){
        $info = $this->info;
        $g = igk_createObj();
        foreach($info->columnInfo as $col){
            $k = $col->clName;
            $n = $col->clMap ?? igk_str_rm_start($k, $info->prefix??'');
            $g->{$n} = $a->{$k};
        } 
        return $g; 
    }
}