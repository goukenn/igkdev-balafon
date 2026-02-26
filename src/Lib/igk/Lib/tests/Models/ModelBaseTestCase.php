<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelBaseTestCase.php
// @date: 20220803 13:48:54
// @desc: 
 

namespace IGK\Tests\Models;
 
use IGK\Tests\BaseTestCase;
use IGK\Tests\Utils;

/**
* auto generate doc.
* @package IGK\Tests\Models
*/
abstract class ModelBaseTestCase extends BaseTestCase{
    // call before all launching test - and output is consider in return of the output string test.

    /**
    * auto generate doc.
    * @return void
    */
    protected function setUp():void{ 
        parent::setUp();
    }

    /**
    * auto generate doc.
    */
    protected function getDefaultModelName(){
        return null;
    }

    /**
    * auto generate doc.
    */
    abstract protected function getControllerClass();

    /**
    * auto generate doc.
    * @param null|mixed $modelName
    */
    protected function getModel($modelName=null){
        try{
            $controller = $this->CreateController($this->getControllerClass());
            if (
                $modelName = $modelName ?? $this->getDefaultModelName()){            
                $model = $controller->loader->model($modelName);
                return $model;
            }else {
                $model = $controller->getDb();
                return null;
            }
        } catch(\Exception $ex){
            $this->fail("model check failed: ".$ex->getMessage());
        }
    }

    /**
    * auto generate doc.
    */
    public function test_db_schema(){
        Utils::CheckControllerDataBase($this, $this->getControllerClass());
    }

   
}