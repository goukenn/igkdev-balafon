<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FactoryBase.php
// @date: 20220728 18:17:44
// @desc: 

namespace IGK\System\Database\Factories;

use Exception;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;

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
     * initilize dependency and return the number of max created element
     * @return void 
     */
    protected function dependOn(int $max){
        return $max;
    }

    /**
     * create model and return response
     * @return ?array|mixed
     */
    public function create(): ?array{ 
        $response = null;
        $g = $this->dependOn($this->count);
        for($i = 0; $i < $g; $i++){
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
                Logger::danger('failed: '.$ex->getMessage());
            }
        }    
        $this->reset();
        return $response;
    }
    ///<summary>override definition</summary>
    /**
     * return new entry definition. Fake
     * @return ?array 
     */
    abstract function definition(): ?array;
}