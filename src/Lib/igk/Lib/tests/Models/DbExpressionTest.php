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

class DbExpressionTest extends BaseTestCase{
    public function setUp(): void {
        require_once dirname(__FILE__)."/dbMocTable.pinc";
    }
    protected function getControllerClass() {
        return DbTestController::class;
     }
    public function test_create_user(){   
        $g = new \IGK\Models\Users();
        $this->assertIsArray($g->to_array(), "user not an array"); 
    }
    public function test_query_expression(){
        $query = "";

        // $c = new Table1Test();
        // igk_wln_e($c->to_array());
        $ad = igk_configs()->get("default_dataadapter");

        if ($ad != IGK_MYSQL_DATAADAPTER){
            $this->markTestSkipped();
            return;
        }
        $p = Table1Test::prepare()->join([
            Table2Test::table()=>[
                Table1Test::column("clName")."=".Table2Test::column("clName"), 
                "type"=>"Left"
            ]
        ])->conditions([
            "clId"=>1
        ])->distinct(true);
        $query = $p->get_query();
        $this->assertEquals("SELECT DISTINCT * FROM `dummy_table1` LEFT JOIN dummy_table2 on (dummy_table1.clName=dummy_table2.clName) WHERE `clId`='1';", 
        $query); 
    }

    public function test_update_query(){
        $gram = Table1Test::driver()->getGrammar(); 
        // igk_wln_e("table info : ", Table1Test::model()->getModelDefinition());

        $this->assertEquals(
            "UPDATE `dummy_table1` SET `clName`='info';",
            $gram->createUpdateQuery(
                Table1Test::table(), ["clName"=>"info"], null,
                Table1Test::model()->getModelDefinition()->tableRowReference
            )
        );
    }
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
    public function test_json_empty_json_query(){
        $gram = Table1Test::driver()->getGrammar(); 
        
        $this->assertEquals(
            "INSERT INTO `dummy_table3`(`clId`,`clName`,`clData`) VALUES (0,'testing','{}');",
            $gram->createInsertQuery(
                Table3Test::table(),  
                ["clName"=>'testing', "clData"=>""]  ,
                Table3Test::model()->getModelDefinition()->tableRowReference             
            )
        );
    }
    public function test_date_query(){
        $gram = Table1Test::driver()->getGrammar();  
        $this->assertEquals(
            "INSERT INTO `dummy_table4`(`clId`,`clDate`) VALUES (0,'2021-01-13 10:37:31');",
            $gram->createInsertQuery(
                Table4Test::table(),  
                ["clName"=>'testing', "clDate"=>"2021-01-13 10:37:31"]  ,
                Table4Test::model()->getModelDefinition()->tableRowReference             
            )
        );
    }
    public function test_create_table_query(){
 
        $gram = Table1Test::model()->getDataAdapter()->getGrammar(); 
        $tableinfo = igk_getv(Table1Test::model()->getModelDefinition(), "tableRowReference"); 
        $q = $gram->createTableQuery(Table1Test::table(), $tableinfo);      
        $this->assertEquals(
            "CREATE TABLE IF NOT EXISTS `dummy_table1`(`clId` Int(11) NOT NULL AUTO_INCREMENT,`clName` varchar(30),`clDescription` text NULL, PRIMARY KEY (`clId`)) ENGINE=InnoDB;",
            $q);
    }

    public function test_query_fetch_prepare(){
       
        Table1Test::createTable();
        Table2Test::createTable();
        $g = Table1Test::prepare()->join([]
            // [Table2Test::table()=>[
            //     Table1Test::column("clId")." = ".Table2Test::column("clId")
            // ]]
        )->conditions([Table1Test::column("clName")=>"testing"])
        ->query_fetch();
        $this->assertIsObject($g);
        // $this->assertEquals(
        //     "INSERT INTO `dummy_table4`(`clId`,`clDate`) VALUES (NULL,'2021-01-13 10:37:31');",
        //     $gram->createInsertQuery(
        //         Table4Test::table(),  
        //         ["clName"=>'testing', "clDate"=>"2021-01-13 10:37:31"]  ,
        //         Table4Test::model()->getModelDefinition()->tableRowReference             
        //     )
        // );
        Table1Test::drop();
        Table2Test::drop();
    }

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
            'INSERT INTO `dummy_table5`(`clId`,`clOptions`) VALUES (0,\'{\"one\":\"1\",\"to\":\"2\",\"info\":\"<a href=\\\\\"/data\\\\\">present</a>\"}\');',
            $gram->createInsertQuery(
                Table5Test::table(),  
                ["clName"=>'testing', "clOptions"=>json_encode((object)["one"=>"1", "to"=>"2", "info"=>"<a href=\"/data\">present</a>"],
                 JSON_UNESCAPED_SLASHES)] ,
                Table5Test::model()->getModelDefinition()->tableRowReference             
            )
        );
    }

    public function test_with_query(){
        // $gram = Table6Test::driver()->getGrammar();  
        $s = Table6Test::with(Table7Test::table())
            ->get_query();
        $this->assertEquals(
            "SELECT * FROM `dummy_table6`;",
            $s,
            "test with table query failed"
        );
    }
}

class DbTestController extends BaseController{

}