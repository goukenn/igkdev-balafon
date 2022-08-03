<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConnexionStringTest.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Tests\BaseTestCase;

use IGK\Helper\Activator;
use IGK\System\Html\Css\CssParser;
use IGK\System\Database\MySQL\MySQLConnexionString;
use IGK\Tests\BaseTestCase; 

class ConnexionStringTest extends BaseTestCase{
    public function test_mysql_connexion_string(){

        $g = Activator::CreateNewInstance(MySQLConnexionString::class, CssParser::Parse("dbserver: 'localhost'; dbuser: 'root'; dbpasswd:'admin'; dbname:'igkdev'")->to_array());
        $this->assertIsObject($g, "not created");
        $this->isTrue($g->dbserver == 'localhost', 'not loaded');
    }
}