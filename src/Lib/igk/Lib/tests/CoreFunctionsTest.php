<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CoreFunctionsTest.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Tests;

use IGK\Helper\StringUtility;
use IGK\System\Html\HtmlUtils;

class CoreFunctionsTest extends BaseTestCase
{

    public function test_parse_bool(){

        $this->assertEquals(
            "false",
            igk_parsebool(false)
        );
        $this->assertEquals(
            "true",
            igk_parsebool(true)
        );

        $this->assertEquals(
            "true",
            igk_parsebool("true")
        );
        $this->assertEquals(
            "false",
            igk_parsebool("false")
        );
    }
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

    public function test_str_remove_line(){
        $str = "la vie\n \n \n est\n belle";
        $this->expectOutputString("la vie est belle");
        echo igk_str_remove_lines($str);

        $this->expectException(\TypeError::class);
        igk_str_remove_lines(null);
    }

    public function test_html_is_html_content(){   
        $this->assertFalse(
            HtmlUtils::IsHtmlContent("Hello jour <body"),
            "condition 1"
        );     
        $this->assertTrue(
            HtmlUtils::IsHtmlContent("<body>"),
            "condition 2"
        );
        $this->assertTrue(
            HtmlUtils::IsHtmlContent("<body />"),
            "condition 3"

        );
        $this->assertTrue(
            HtmlUtils::IsHtmlContent("<igk:info />"),
            "condition 4"
        );
        $this->assertTrue(
            HtmlUtils::IsHtmlContent("<igk:info-p />"),
            "condition 5"
        );
        $this->assertTrue(
            HtmlUtils::IsHtmlContent("<igk:info-p>"),
            "condition 6"
        );
        $this->assertFalse(
            HtmlUtils::IsHtmlContent("&gt;body &lt;"),
            "special case failed"
        );
 
    }
    
}
