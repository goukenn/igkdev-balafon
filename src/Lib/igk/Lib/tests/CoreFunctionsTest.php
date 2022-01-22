<?php

namespace IGK\Tests;

use IGK\Helper\StringUtility;

class CoreFunctionsTest extends BaseTestCase
{
    public function testRelativePath()
    {
        $this->assertEquals(
            "./",
            igk_io_get_relativepath("/A/B/C", "/A/B/C"),
            "Value not maching ..."
        );
        $this->assertEquals(
            "./",
            igk_io_get_relativepath("/A/B/C/", "/A/B/C"),
            "Value not maching ..."
        );

        $this->assertEquals(
            "./C",
            igk_io_get_relativepath("/A/B/", "/A/B/C"),
            "trailing path not matching..."
        );

        $this->assertEquals(
            "./C/D/E",
            igk_io_get_relativepath("/A/B", "/A/B/C/D/E"),
            "Value not maching ... binding cde"
        );
        $this->assertEquals(
            "../../C/D",
            igk_io_get_relativepath("/A/B/C", "/A/C/D"),
            "Value not maching ..."
        );
        $this->assertEquals(
            "../../../../C/D",
            igk_io_get_relativepath("/A/B/C/M/X", "/A/C/D")
        );
        $this->assertEquals(
            "../../../C/B/O",
            igk_io_get_relativepath("/A/B/C", "/C/B/O")
        );
        $this->assertEquals(
            null,
            igk_io_get_relativepath("c:/A/B/C", "d:/C/B/O")
        );
    }

    public function testStringUtilityCamelCase(){
        $this->assertEquals("Default",
        StringUtility::CamelClassName("default"));

        $this->assertEquals("DefaultAction",
        StringUtility::CamelClassName("default____action"));


        $this->assertEquals("DefaultAction",
        StringUtility::CamelClassName("default-_action"));

        // with at 
        $this->assertEquals("DefaultAction",
        StringUtility::CamelClassName("@default-_action"));
    }
}
