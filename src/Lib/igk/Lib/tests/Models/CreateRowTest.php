<?php
namespace IGK\Tests\Models;

use IGK\Controllers\SysDbControllerManager;
use IGK\Database\DbSchemas;
use IGK\Models\Subdomains;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\Tests\BaseTestCase;
use IGKException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
 

class CreateRowTest extends BaseTestCase{
    function setUp():void {
        require_once(dirname(__FILE__)."/dbMocTable.pinc");
    }
    function test_create_usergroup_row(){
        require_once IGK_LIB_CLASSES_DIR . "/IGKSysUtil.php"; 

        $table = igk_db_get_table_name(Usergroups::table());
        $ad = Usergroups::driver(); 
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

    // function test_create_if_not_exists(){
    //     Subdomains::initDb();
    //     $obj = new Subdomains();
    //     $obj->clName = "test";
    //     $g = Subdomains::createIfNotExists($obj->to_array());

    //     igk_wln_e($g);
    //     $this->assertIsObject($g);
    // }

    // function test_gram_column_info(){
    //     $gram = (new SupportNothingDataAdapter(null))->getGrammar();
    //     $d = $gram->get_column_info(Users::table(), "igkdev");
    //     igk_wln_e(var_dump($d));
    // }

    /**
     * test create query 
     * @return void 
     * @throws IGKException 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_user_query_create_query(){
        //$gram = Users::driver()->getGrammar();  
        $gram = (new SupportNothingDataAdapter(null))->getGrammar();
 
        $defs = SysDbControllerManager::GetDataTableDefinitionFormController(null , Users::table()); // getDataTableDefinition(); 

       // igk_wln_e("defs:", $defs);
        // $this->assertEquals(
        //     "data: is : ",
        //     $gram->createTableQuery(Users::table(), (object)$defs["ColumnInfo"])
        // );
        //var_dump((object)$defs["ColumnInfo"]);
        $defs = table_enum::getDataTableDefinition()->tableRowReference; 
        //  SysDbControllerManager::GetDataTableDefinition();
        $this->assertEquals(
            "CREATE TABLE IF NOT EXISTS `table_enum`(`clId` Int(11) AUTO_INCREMENT,`clName` Enum('1','2','3') NULL, PRIMARY KEY (`clId`)) ENGINE=InnoDB;",
            // "CREATE TABLE IF NOT EXISTS `table_enum`(`clId` text NOT NULL,`clName` text NULL, PRIMARY KEY (`clId`)) ENGINE=InnoDB;",
            $gram->createTableQuery("table_enum", $defs)
        ); 
    }
}

/** @package IGK\Tests\Models */
class SupportNothingDataAdapter extends \IGK\System\DataBase\MySQL\DataAdapter{
    public function isTypeSupported($type):bool{ 
        return false;
    } 
}