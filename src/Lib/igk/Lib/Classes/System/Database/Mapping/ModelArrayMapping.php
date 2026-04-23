<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelArrayMapping.php
// @date: 20241007 16:04:08
namespace IGK\System\Database\Mapping;
use Exception;
use IGK\Models\ModelBase;
use IGK\System\Database\SchemaMigrationInfo;
use IGKException;

/**
* auto generate doc.
* @package IGK\System\Database\Mapping
* @author C.A.D. BONDJE DOUE
*/
class ModelArrayMapping{
    /**
     * info
     * @var SchemaMigrationInfo|null
     */
    protected $info;
    /**
     * model
     * @var ModelBase
     */
    protected $model;
    /**
     * .ctrl
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
    /**
    * Called when an object is used as a function.
    * @param mixed $a
    */
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