<?php
namespace IGK\Tests\Models;

use IGK\Database\DbSchemas;
use IGK\Tests\BaseTestCase;

class CreateRowTest extends BaseTestCase{
    function test_create_usergroup_row(){
        require_once IGK_LIB_CLASSES_DIR . "/IGKSysUtil.php";
        $table = igk_db_get_table_name(IGK_TB_USERGROUPS);
        $ad = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        // igk_wln_e("table : ", $table);
        if (!$def = $ad->getDataTableDefinition($table)){
            $this->fail("Can't get table definition");
        }
       if (!($row = DbSchemas::CreateRow($table, $def["Controller"]))){
            $this->fail("failed to create row");
       } 
       $this->assertEquals(
           array_keys($def["ColumnInfo"]), 
           array_keys((array)$row),
           "column definition mismatch" 
        ); 
    }
}