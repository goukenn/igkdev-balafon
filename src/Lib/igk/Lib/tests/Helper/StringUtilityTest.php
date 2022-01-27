<?php

namespace IGK\Tests\Helper;

use IGK\Helper\StringUtility;
use IGK\Tests\BaseTestCase;

class StringUtilityTest extends BaseTestCase
{
    function test_identifier(){

        $this->assertEquals(null, 
        StringUtility::Identifier("45698"),
        "identifier must return null value"
        );

        $this->assertEquals('__45698', 
        StringUtility::Identifier("__45698"),
        "identifier must return null value"
        );

        $this->assertEquals('__4569_m8', 
        StringUtility::Identifier("__4569_m8"),
        "identifier : test 3"
        );
        $this->assertEquals('__4569_M_8', 
        StringUtility::Identifier("__4569_m/8"),
        "identifier : test 4"
        );
    }
}