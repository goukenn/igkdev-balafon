<?php
// @author: C.A.D. BONDJE DOUE
// @file: RefColumnMappingTest.php
// @date: 20240921 07:59:17
namespace IGK\Tests\Database;

use IGK\Database\DbColumnInfo;
use IGK\Database\RefColumnMapping;
use IGK\Models\ModelBase;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\Database
* @author C.A.D. BONDJE DOUE
*/
class RefColumnMappingTest extends BaseTestCase{
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

class RefColumnMappingMockingModel extends ModelBase{
    public function getDataTableDefinition(){
        return [];
    }
    protected function _getTableColumnInfo() : ?array{
        return [
            'id'=>new DbColumnInfo(['clName'=>'id','clAutoIncrement'=>true]),
            'name'=>new DbColumnInfo(['clName'=>'name', 'clType'=>'varchar(30)']),
            'test'=>new DbColumnInfo(['clName'=>'test', 'clType'=>'varchar(30)']),
        ];
    }
    public function getTable(){
        return 'mocking';
    }
}