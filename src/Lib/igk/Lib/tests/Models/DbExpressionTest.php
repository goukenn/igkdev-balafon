<?php
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
        $ad = igk_sys_configs()->get("default_dataadapter");

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

        // igk_wln_e("table info : ", Table1Test::getDatatableDefinition());

        $this->assertEquals(
            "UPDATE `dummy_table1` SET `clName`='info';",
            $gram->createUpdateQuery(
                Table1Test::table(), ["clName"=>"info"], null,
                Table1Test::getDatatableDefinition()->tableRowReference
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
                    Table1Test::getDatatableDefinition()->tableRowReference
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
                    Table1Test::getDatatableDefinition()->tableRowReference               
            )
        );
    }
    public function test_json_empty_json_query(){
        $gram = Table1Test::driver()->getGrammar(); 
        
        $this->assertEquals(
            "INSERT INTO `dummy_table3`(`clId`,`clName`,`clData`) VALUES (NULL,'testing','{}');",
            $gram->createInsertQuery(
                Table3Test::table(),  
                ["clName"=>'testing', "clData"=>""]  ,
                Table3Test::getDatatableDefinition()->tableRowReference             
            )
        );
    }
    public function test_json_date_query(){
        $gram = Table1Test::driver()->getGrammar();  
        $this->assertEquals(
            "INSERT INTO `dummy_table4`(`clId`,`clDate`) VALUES (NULL,'2021-01-13 10:37:31');",
            $gram->createInsertQuery(
                Table4Test::table(),  
                ["clName"=>'testing', "clDate"=>"2021-01-13 10:37:31"]  ,
                Table4Test::getDatatableDefinition()->tableRowReference             
            )
        );
    }
    public function test_create_table_query(){
 
        $gram = Table1Test::model()->getDataAdapter()->getGrammar(); 
        $tableinfo = igk_getv(Table1Test::getDatatableDefinition(), "tableRowReference"); 
        $q = $gram->createTableQuery(Table1Test::table(), $tableinfo);      
        $this->assertEquals(
            "CREATE TABLE IF NOT EXISTS `dummy_table1`(`clId` Int(11) AUTO_INCREMENT,`clName` varchar(30),`clDescription` text NULL, PRIMARY KEY (`clId`)) ENGINE=InnoDB;",
            $q);
    }

    public function test_query_fetch_prepare(){
       
        // igk_wln_e("dbname", igk_sys_configs()->db_name, igk_sys_configs()->db_server);
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
        //         Table4Test::getDatatableDefinition()->tableRowReference             
        //     )
        // );
        Table1Test::drop();
        Table2Test::drop();
    }

    public function test_column_definition(){
        $gram = Users::driver()->getGrammar();  
        $this->assertEquals(
            "SELECT CONCAT(clFirstName,' ', clLastName) as name, clLogin AS bsi, clStatut AS stat FROM `tbigk_users`;",
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
}

class DbTestController extends BaseController{

}