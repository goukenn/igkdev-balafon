<?php
// @author: C.A.D. BONDJE DOUE
// @file: RefColumnMappingTest.php
// @date: 20240921 07:59:17
namespace IGK\Tests\Database;
 
use IGK\Database\RefColumnMapping;
use IGK\Tests\BaseTestCase;

/**
* test reference column mapping 
* @package IGK\Tests\Database
* @author C.A.D. BONDJE DOUE
*/
class RefColumnMappingTest extends BaseTestCase{

    /**
    * Tests refcolumnmapping check load.
    */
    public function test_refcolumnmapping_check_load(){


        $col = new RefColumnMapping(["rc_data"=>"ok", "test"=>"sample", "name"=>"in_name"], []);
        $raw = new RefColumnMappingMockingModel($col,1,true);
        $this->assertEquals(null, $raw->to_json());


        $col = new RefColumnMapping(["rc_data"=>"ok", "test"=>"sample"], ["name"=>"rc_data"]);

        $raw = new RefColumnMappingMockingModel($col,1,true); 
        $this->assertEquals('{"name":"ok"}', $raw->to_json());

        $raw = new RefColumnMappingMockingModel($col,1,false);
        $this->assertEquals('{"id":null,"name":"ok","test":null}', $raw->to_json());
        
        $col = new RefColumnMapping(["rc_data"=>"ok", "test"=>"sample", "name"=>"in_name"], ["name","test"]);
        $raw = new RefColumnMappingMockingModel($col,1,false);
        $this->assertEquals('{"id":null,"name":"in_name","test":"sample"}', $raw->to_json());

    }
}

