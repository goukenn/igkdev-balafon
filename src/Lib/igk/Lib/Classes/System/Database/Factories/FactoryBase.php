<?php

namespace IGK\System\Database\Factories;

use IGK\Models\ModelBase;

abstract class FactoryBase {
    protected $count;
    protected $model;
    
    public function __construct(ModelBase $model, int $count=1){
        $this->count = $count;
        $this->model = $model; 
    }

    /**
     * create model and return response
     * @return array 
     */
    public function create(){ 
        $response = [];
        for($i = 0; $i < $this->count; $i++){
            $def = $this->definition(); 
            $response[] = $this->model::create($def);
        }    
        return $response;
    }
    ///<summary>override definition</summary>
    /**
     * return new entry definition. Fake
     * @return array 
     */
    abstract function definition(): ?array;
}