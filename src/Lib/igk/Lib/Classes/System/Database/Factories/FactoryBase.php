<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FactoryBase.php
// @date: 20220728 18:17:44
// @desc: 

namespace IGK\System\Database\Factories;

use IGK\Models\ModelBase;

/**
 * p
 * @package IGK\System\Database\Factories
 */
abstract class FactoryBase {
    protected $count;
    protected $model;
    protected $index;
    
    public function __construct(ModelBase $model, int $count=1){
        $this->count = $count;
        $this->model = $model; 
        $this->index = -1;
    }
    /**
     * override to reset factory
     * @return void 
     */
    protected function reset(){

    }

    /**
     * create model and return response
     * @return array 
     */
    public function create(){ 
        $response = [];
        for($i = 0; $i < $this->count; $i++){
            $this->index = $i;
            $def = $this->definition($i); 
            $response[] = $this->model::create($def);
        }    
        $this->reset();
        return $response;
    }
    ///<summary>override definition</summary>
    /**
     * return new entry definition. Fake
     * @return array 
     */
    abstract function definition(): ?array;
}