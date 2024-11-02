<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbColumnInfoTest.php
// @date: 20240920 16:03:12
namespace IGK\Tests\Database;

use IGK\Database\DbColumnInfo;
use IGK\Database\DbDataTypes;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\Database
* @author C.A.D. BONDJE DOUE
*/
class DbColumnInfoTest extends BaseTestCase{
    function test_dbcolumninfo_createlength(){
        $g = new DbColumnInfo([
            "clType"=>"varchar(30)"
        ]);
        $this->assertEquals(30, intval($g->clTypeLength));
        $this->assertEquals('varchar', strtolower($g->clType));

        $g = new DbColumnInfo([
            "clType"=>"int"
        ]);
        $this->assertEquals(11, $g->clTypeLength);
        $this->assertEquals('int', strtolower($g->clType));

        $g = new DbColumnInfo([
            "clType"=>"phone_number(32)"
        ]);
        $this->assertEquals( DbDataTypes::PHONE_NUMBER_MAX_LENGTH, $g->clTypeLength);
        $this->assertEquals('varchar', strtolower($g->clType));

    }
    public function test_dbcolumninfo_link_column_definition(){
        $g = new DbColumnInfo([
            "clType"=>"int",
            "clLinkType"=>"targetTable,clguid"
        ]);
        $this->assertEquals('targetTable', $g->clLinkType);
        $this->assertEquals('clguid', strtolower($g->clLinkColumn));

    }
    public function test_dbcolumninfo_column_not_null(){
        $g = new DbColumnInfo([
            "clType"=>"int",
            "clNotNull"=>"true"
        ]);
        $this->assertTrue($g->clNotNull);

        $g = new DbColumnInfo([
            "clType"=>"int",
            "clNotNull"=>"false"
        ]);
        $this->assertFalse($g->clNotNull);
    }
}