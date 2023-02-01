<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FactoryBase.php
// @date: 20220728 18:17:44
// @desc: 

namespace IGK\System\Database\Factories;

use Exception;
use IGK\Models\ModelBase;

/**
 * p
 * @package IGK\System\Database\Factories
 */
abstract class FactoryBase {
    protected $count;
    protected $model;
    protected $index;
    protected $data;
    protected $m_errors = [];
    protected function getErrors(){
        return $this->m_errors;
    }

    public function __set($n, $v){
        igk_die("Not allowed: ".$n);
    }

    public function __get($n){
        igk_die("Not allowed: ".$n);
    }
    
    public function __construct(ModelBase $model, int $count=1, ?array $data=null){
        $this->count = $count;
        $this->model = $model; 
        $this->index = -1;
        $this->data = $data;
    }
    /**
     * override to reset factory
     * @return void 
     */
    protected function reset(){

    }
    /**
     * set error handler
     * @param null|array $error 
     * @return $this 
     */
    public function setError(?array & $error){
        $this->m_errors = & $error;
        return $this;
    }

    /**
     * create model and return response
     * @return ?array 
     */
    public function create(): ?array{ 
        $response = null;
        for($i = 0; $i < $this->count; $i++){
            $this->index = $i;
            $def = $this->definition($i); 
            if (empty($def)){
                
                continue;
            }
            try{
                if ($v_r = $this->model::create($def)){
                    if (is_null($response)){
                        $response = [];
                    }
                    $response [] = $v_r;
                }

            } catch(Exception $ex){
                $this->m_errors[] = $ex->getMessage();
            }
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