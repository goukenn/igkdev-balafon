<?php
// @author: C.A.D. BONDJE DOUE
// @filenamer: ModelBaseInjector.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Models\Injectors;

use IGK\Models\ModelBase;
use IGKValidator;

class ModelBaseInjector{
    protected $model;

    public function __construct(?ModelBase $model=null)
    {
        $this->model = $model;
    }
    /**
     * resolv from request type
     * @param mixed $i 
     * @return mixed 
     */
    public function resolv($i){
        if (is_numeric($i)){
            return $this->model::select_row($i);
        }
        if (IGKValidator::IsGUID($i)){
            return $this->model::fromGuid($i);
        }

    }
}
