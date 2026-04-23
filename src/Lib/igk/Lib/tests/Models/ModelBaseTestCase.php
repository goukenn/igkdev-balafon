<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModelBaseTestCase.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\Tests\Models;
use IGK\Tests\BaseTestCase;
use IGK\Tests\Utils;

/**
* Model base test case.
* @package IGK\Tests\Models
*/
abstract class ModelBaseTestCase extends BaseTestCase{
    /**
    * Sets up the test environment before each test.
    * @return void
    */
    protected function setUp():void{ 
        parent::setUp();
    }
    /**
    * Returns Default Model Name.
    */
    protected function getDefaultModelName(){
        return null;
    }
    /**
    * Returns Controller Class.
    */
    abstract protected function getControllerClass();
    /**
    * Returns Model.
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
    * Tests db schema.
    */
    public function test_db_schema(){
        Utils::CheckControllerDataBase($this, $this->getControllerClass());
    }
}