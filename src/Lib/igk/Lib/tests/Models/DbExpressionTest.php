<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbExpressionTest.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\Tests\Models;
use IGK\Controllers\BaseController;
use IGK\Database\DbExpression;
use IGK\Models\Users;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\Tests\BaseTestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;

/**
* Db expression test.
* @package IGK\Tests\Models
*/
class DbExpressionTest extends BaseTestCase{
    /**
    * Sets up the test environment before each test.
    * @return void
    */
    public function setUp(): void {
        require_once dirname(__FILE__)."/dbMocTable.pinc";
    }
    /**
    * Returns Controller Class.
    */
    protected function getControllerClass() {
        return DbTestController::class;
     }
    /**
    * Tests create user.
    */
    public function test_create_user(){   
        $g = new \IGK\Models\Users();
        $this->assertIsArray($g->to_array(), "user not an array"); 
    }
    /**
    * Tests query expression.
    */
    public function test_query_expression(){
        $query = "";
        $ad = igk_configs()->get("default_dataadapter");
        if ($ad != IGK_MYSQL_DATAADAPTER){
            $this->markTestSkipped();
            return;
        }
        $p = Table1Test::prepare()->join([
            Table2Test::table()=>[
                sprintf('%s=%s',Table1Test::column("clName"), Table2Test::column("clName")),
                "type"=>"Left"
            ]
        ])->conditions([
            "clId"=>1
        ])->distinct(true);
        $query = $p->get_query();
        $this->assertEquals("SELECT DISTINCT * FROM `dummy_table1` LEFT JOIN dummy_table2 on (dummy_table1.clName=dummy_table2.clName) WHERE `clId`='1';", 
        $query); 
    }
    /**
    * Tests update query.
    */
    public function test_update_query(){
        $gram = Table1Test::driver()->getGrammar(); 
        $this->assertEquals(
            "UPDATE `dummy_table1` SET `clName`='info';",
            $gram->createUpdateQuery(
                Table1Test::table(), ["clName"=>"info"], null,
                Table1Test::model()->getModelDefinition()->tableRowReference
            )
        );
    }
    /**
    * Tests update query 2.
    */
    public function test_update_query_2(){
        $gram = Table1Test::driver()->getGrammar(); 
        $this->assertEquals(
            "UPDATE `dummy_table1` SET `clName`='8' WHERE `clName` IS NOT NULL;",
            $gram->createUpdateQuery(
                Table1Test::table(),  
                    ["clName"=>'8'],
                    ["!clName"=>null], 
                    Table1Test::model()->getModelDefinition()->tableRowReference
            )
        );
    }
    /**
     * test query with db condition builder
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_update_query_with_db_condition_builder(){
        $gram = Table1Test::driver()->getGrammar();  
        $this->assertEquals(
            "UPDATE `dummy_table1` SET `clName`='8' WHERE `clName` IS NOT NULL OR `clName`!='1';",
            $gram->createUpdateQuery(
                Table1Test::table(),  
                    ["clName"=>'8'],
                    [(new DbConditionExpressionBuilder("OR"))
                            ->add("!clName",null)
                            ->add("!clName",1)
                    ],
                    Table1Test::model()->getModelDefinition()->tableRowReference               
            )
        );
    }
    /**
    * Tests json empty json query.
    */
    public function test_json_empty_json_query(){
        $gram = Table1Test::driver()->getGrammar(); 
        $this->assertEquals(
            "INSERT INTO `dummy_table3`(`clId`,`clName`,`clData`) VALUES (NULL,'testing','{}');",
            $gram->createInsertQuery(
                Table3Test::table(),  
                ["clName"=>'testing', "clData"=>""]  ,
                Table3Test::model()->getModelDefinition()->tableRowReference             
            )
        );
    }
    /**
    * Tests date query.
    */
    public function test_date_query(){
        $gram = Table1Test::driver()->getGrammar();  
        $this->assertEquals(
            "INSERT INTO `dummy_table4`(`clId`,`clDate`) VALUES (NULL,'2021-01-13 10:37:31');",
            $gram->createInsertQuery(
                Table4Test::table(),  
                ["clName"=>'testing', "clDate"=>"2021-01-13 10:37:31"]  ,
                Table4Test::model()->getModelDefinition()->tableRowReference             
            )
        );
    }
    /**
    * Tests create table query.
    */
    public function test_create_table_query(){
        $ad = Table1Test::model()->getDataAdapter();
        $version = $ad->getVersion();
        $query = version_compare($version, "8.0",  ">=") ?
        "CREATE TABLE IF NOT EXISTS `dummy_table1`(`clId` Int NOT NULL AUTO_INCREMENT,`clName` varchar(30),`clDescription` text, PRIMARY KEY (`clId`)) ENGINE=InnoDB;":
        "CREATE TABLE IF NOT EXISTS `dummy_table1`(`clId` Int(11) NOT NULL AUTO_INCREMENT,`clName` varchar(30),`clDescription` text, PRIMARY KEY (`clId`)) ENGINE=InnoDB;"
         ;
        $gram = $ad->getGrammar(); 
        $tableinfo = igk_getv(Table1Test::model()->getModelDefinition(), "tableRowReference"); 
        $q = $gram->createTableQuery(Table1Test::table(), $tableinfo);      
        $this->assertEquals($query,
           $q);
    }
    /**
    * Tests query fetch prepare.
    */
    public function test_query_fetch_prepare(){
        Table1Test::createTable();
        Table2Test::createTable();
        $g = Table1Test::prepare()->join([]
        )->conditions([Table1Test::column("clName")=>"testing"])
        ->query_fetch();
        $this->assertIsObject($g);  
        Table1Test::drop();
        Table2Test::drop();
    }
    /**
    * Tests column definition.
    */
    public function test_column_definition(){
        $gram = Users::driver()->getGrammar();         
        $table = Users::table();
        $this->assertEquals(
            "SELECT CONCAT(clFirstName,' ', clLastName) as name, clLogin AS bsi, clStatut AS stat FROM `{$table}`;",
            $gram->createSelectQuery(Users::table(), null,
                [
                    "Columns"=>[
                        new DbExpression("CONCAT(clFirstName,' ', clLastName) as name"),
                        "clLogin"=>"bsi",
                        [
                            "key"=>"clStatut",
                            "as"=>"stat"
                        ]
                    ]
                ]
            )
        );
    }
    /**
     * testing json query
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_json_query(){
        $gram = Table5Test::driver()->getGrammar();  
        $data = json_encode((object)["one"=>"1", "to"=>"2"]);
        $this->assertEquals(
            'INSERT INTO `dummy_table5`(`clId`,`clOptions`) VALUES (NULL,\'{\"one\":\"1\",\"to\":\"2\",\"info\":\"<a href=\\\\\"/data\\\\\">present</a>\"}\');',
            $gram->createInsertQuery(
                Table5Test::table(),  
                ["clName"=>'testing', "clOptions"=>json_encode((object)["one"=>"1", "to"=>"2", "info"=>"<a href=\"/data\">present</a>"],
                 JSON_UNESCAPED_SLASHES)] ,
                Table5Test::model()->getModelDefinition()->tableRowReference             
            )
        );
    }
    /**
    * Tests with query.
    */
    public function test_with_query(){
        $s = Table6Test::with(Table7Test::table())
            ->get_query();
        $this->assertEquals(
            "SELECT * FROM `dummy_table6`;",
            $s,
            "test with table query failed"
        );
    }
}
/**
* Db test controller.
* @package IGK\Tests\Models
*/
class DbTestController extends BaseController{
}