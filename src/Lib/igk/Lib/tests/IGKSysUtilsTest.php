<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IGKSysUtilsTest.php
// @date: 20220804 06:28:22
// @desc: testing sys util function 

namespace IGK\Tests;

use IGK\Controllers\BaseController;
use IGKSysUtil;

/**
* auto generate doc.
* @package IGK\Tests
*/
class IGKSysUtilsTest extends BaseTestCase  {

    /**
    * auto generate doc.
    */
    public function test_resolv_type_name(){
       $t= IGKSysUtil::GetModelTypeName( "%prefix%_dummy", DummySysUtilController::ctrl());
       $this->assertEquals(
            $t,
            \IGK\Tests\Models\Dummy::class,
            "resolving type failed"
       );
       //testing sys prefix and return the name 
       $bck = igk_configs()->db_prefix;
       igk_configs()->db_prefix = "tbigk_test_";
       $t= IGKSysUtil::GetModelTypeName( "%sysprefix%_dummy_%year%", DummySysUtilController::ctrl());
      
       $this->assertEquals(
            $t,
            \IGK\Tests\Models\Dummy::class,
            "resolving 2 with sys prefix type failed"
       );
       igk_configs()->db_prefix = $bck;
   }

    /**
    * auto generate doc.
    */
    public function test_resolveTableName(){
    $t= IGKSysUtil::DBGetTableName(
        "%prefix%_dummy_%year%", DummySysUtilController::ctrl());
    $this->assertEquals(
         $t,
         "tbjojo_dummy_".date('Y'),
         "resolving type failed"
    );
   }
}

/**
* auto generate doc.
* @package IGK\Tests
*/
class DummySysUtilController extends BaseController{

    /**
    * auto generate doc.
    */
    protected function getEntryNameSpace(){
        return __NAMESPACE__;
    }
    public function __construct()
    {
        $this->getConfigs()->clDataTablePrefix = "tbjojo";
    }
}