<?php
// @author: C.A.D. BONDJE DOUE
// @file: ParseLitteralTest.php
// @date: 20231224 15:51:04
namespace IGK\Tests\System\Configurations;

use IGK\System\IO\Configuration\ConfigurationReader;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Configurations
*/
class ParseLitteralTest extends BaseTestCase{
    public function test_parse_configuration(){
        $g= ConfigurationReader::ParseEnumLitteralValue("home=1,3,basic=4");
        $this->assertEquals((object)[
            "home"=>1,
            "3"=>null,
            "basic"=>4
        ], $g);
    }
}