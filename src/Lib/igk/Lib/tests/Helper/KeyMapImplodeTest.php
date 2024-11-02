<?php
// @author: C.A.D. BONDJE DOUE
// @file: KeyMapImplodeTest.php
// @date: 20230307 11:59:11
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/HelperFunctions/Array/KeyMapImplodeTest.php
namespace IGK\Tests\Helper\Array;

use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\HelperFunctions\Array
*/
class KeyMapImplodeTest extends BaseTestCase{
    public function test_normal(){
        $r = igk_array_key_map_implode(["one"=>1, "offert"=>2]);
        $this->assertEquals(
            "one:1; offert:2;",
            $r
        );
    }
    public function test_normal_width_array(){
        $r = igk_array_key_map_implode(["one"=>1, "offert"=>["jump"=>"ok"]]);
        $this->assertEquals(
            "one:1; offert:{ jump:ok };",
            $r
        );
    }
    public function test_normal_width_index_array(){
        $r = igk_array_key_map_implode(["one"=>1, "offert"=>["jump:ok"]]);
        $this->assertEquals(
            "one:1; offert:\"jump:ok\";",
            $r
        );
    }
}