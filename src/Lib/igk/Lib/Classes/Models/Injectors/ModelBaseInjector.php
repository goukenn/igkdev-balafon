<?php
// @author: C.A.D. BONDJE DOUE
// @filenamer: ModelBaseInjector.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Models\Injectors;

use IGK\Models\ModelBase;
use IGK\System\IInjector;
use IGKValidator;

class ModelBaseInjector implements IInjector{
    protected $model;

    public function __construct(?ModelBase $model=null)
    {
        $this->model = $model;
    }
    /**
     * resolv from request type
     * @param mixed $id
     * @return mixed 
     */
    public function resolve($id, ?string $type=null){
        if (is_null($id)){ 
            igk_die("failed to resolve from id can not be null");
        } 
        if (is_numeric($id)){
            return $this->model::select_row($id);
        }
        if (IGKValidator::IsGUID($id)){
            return $this->model::fromGuid($id);
        }
        try{
            return $this->model::resolve($id);
        }
        catch(\Exception $ex) {
           if (igk_environment()->isDev()){
                throw $ex;
           }
        }
        return null;
    }
}
