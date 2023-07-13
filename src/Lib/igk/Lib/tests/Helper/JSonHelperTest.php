<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonHelperTest.php
// @date: 20230209 09:16:58
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/Helper/JSonHelperTest.php
namespace IGK\Tests\Helper;

use IGK\Helper\JSon;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
 * 
 * @package IGK\Tests\Helper
 */
class JSonHelperTest extends BaseTestCase
{
    public function test_render()
    {
        $d = '{"name":"Charles","email":"cbondje@igkdev.com","locale":"en","authorizations": null,"roles": ["AdminOK","ClientOK"]}';
        $m = json_decode($d);
        $this->assertEquals(
            '{"name":"Charles","email":"cbondje@igkdev.com","locale":"en","roles":["AdminOK","ClientOK"]}',
            JSon::Encode($m, (object)["ignore_empty" => true])
        );
    }
    public function test_encoding_with_null()
    {
        $d = [
            (object)[
                "x" => 10,
                "y" => null
            ],
            'plan' => (object)[
                'z' => null,
                't' => 7
            ]
        ]; 
    
        $this->assertEquals(
            '{"0":{"x":10},"plan":{"t":7}}', 
            JSon::Encode($d, (object)["ignore_empty" => true])
        ); 
    }

    public function test_encoding_with_db_cache()
    {
        $d = [
            ["MYSQL"=>[

                (object)["clName" => "clId",
                "clAutoIncrement" => null
                ]
            ]
            ],
            'plan' => (object)[
                'z' => null,
                't' => 7
            ]
        ];   
        $s = JSon::Encode($d, (object)["ignore_empty" => true]);
        $this->assertEquals(
            '{"0":{"MYSQL":[{"clName":"clId"}]},"plan":{"t":7}}', 
            $s
        );  
    }
}
