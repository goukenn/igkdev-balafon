<?php


// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationReaderTest.php
// @date: 20220830 09:50:18
// @desc: 

namespace IGK\Test\System\IO\Configuration;

use IGK\System\IO\Configuration\ConfigurationEncoder; 
use IGK\Tests\BaseTestCase;

/**
 * test configuration reader function 
 * @package IGK\Test\System\IO\Configuration
 */
class ConfigurationReaderTest extends BaseTestCase {
   
    public function test_connexion_string_encode(){
        $encoder = new ConfigurationEncoder;
        $this->assertEquals(
            'x=1,y=3',
            $encoder->encode(["x"=>1, "y"=>3]),
            "the str mark test faile 4"
        );
    }
}
