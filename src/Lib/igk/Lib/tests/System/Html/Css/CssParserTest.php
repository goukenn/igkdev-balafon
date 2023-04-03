<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CssParserTest.php
// @date: 20220310 11:27:25
// @desc: css parser test

namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssParser;
use IGK\Tests\BaseTestCase;

class CssParserTest extends BaseTestCase{
    public function test_css_parse(){
        $c = CssParser::Parse("background-color:red")["background-color"];      
        $this->assertEquals(
            "red",
            $c
        ); 
    }
    public function test_css_parse_2(){        
        $this->assertEquals(
            "indigo",
            CssParser::Parse("body:first-child{ background-color:red; color: yellow }html{ content: 'infor;data:presenation'; background-color:indigo; }")["html"]["background-color"]
        );
    }
    public function test_css_parse_3(){        
        $this->assertEquals(
            json_encode((object)[".igk-def"=>["background-color"=>"red"]], JSON_PRETTY_PRINT),
            CssParser::Parse(".igk-def{background-color:red; }")->to_json()
        );
    }
    public function test_css_parse_to_css(){        
        $this->assertEquals(
            ".igk-def{\nbackground-color:red;\n}",
            CssParser::Parse(".igk-def{background-color:red; }")->to_css()
        );
    }
    public function test_css_parse_to_css_2(){   
        
        $g = CssParser::Parse("width:30; height: 0; background-color:indigo;");     
        $this->assertEquals(
            "width: 30;\nheight: 0;\nbackground-color: indigo;",
            $g->to_css()
        );
    }
}
