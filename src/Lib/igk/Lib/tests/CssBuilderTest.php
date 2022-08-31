<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssBuilderTest.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Tests;

use IGK\Helper\StringUtility;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDoc;
use IGKHtmlDoc;

class CssBuilderTest extends BaseTestCase
{
    
    function test_render_transform(){
        $theme = new HtmlDocTheme(  IGKHtmlDoc::CreateDocument("test"), "test");

        $cv =  "[transform: scale(1.0)]";    
        $r = igk_css_treat($cv, false, $theme, $theme);
        $this->assertEquals(
            "-webkit-transform: scale(1.0);-ms-transform:scale(1.0); -moz-transform:scale(1.0); -o-transform: scale(1.0); transform: scale(1.0);",
            $r, 
            "render transform not resolved"
        );

        $cv =  "[transform: rotate(90)]";    
        $r = igk_css_treat($cv, false, $theme, $theme);
        $this->assertEquals(
            "-webkit-transform: rotate(90);-ms-transform:rotate(90); -moz-transform:rotate(90); -o-transform: rotate(90); transform: rotate(90);",
            $r, 
            "render rotate not resolved"
        );
        $cv =  "[transform: scale(1.5), rotate(90deg)]";    
 
        $r = igk_css_treat($cv, false, $theme, $theme);
     
        $this->assertEquals(
            "-webkit-transform: scale(1.5), rotate(90deg);-ms-transform:scale(1.5), rotate(90deg); -moz-transform:scale(1.5), rotate(90deg); -o-transform: scale(1.5), rotate(90deg); transform: scale(1.5), rotate(90deg);",
            $r, 
            "render rotate not resolved"
        );
    }
    function test_rendergin()
    { 
        $theme = new HtmlDocTheme(  IGKHtmlDoc::CreateDocument("test"), "test");

        $cv =  "[bgcl: actionBarButtonHoverBackgroundColor, #333] color:yellow; box-shadow: 0px 2px 6px [cl:ationBarButtonShadowColor, #111]; [transform:scale(1.1)]";
    
        $r = igk_css_treat($cv, false, $theme, $theme);
        $this->assertEquals(
            "background-color: #333; color:yellow; box-shadow: 0px 2px 6px #111; -webkit-transform: scale(1.1);-ms-transform:scale(1.1); -moz-transform:scale(1.1); -o-transform: scale(1.1); transform: scale(1.1);",
            $r, 
            "css_test_evaluatation: not resolved data"
        );
        $this->assertTrue(true);
    }

    function test_css_theme_definition(){

        $theme = new HtmlDocTheme(  IGKHtmlDoc::CreateDocument("test"), "test");

        $systheme = igk_app()->getDoc()->getSysTheme();
        $systheme[".igk-fs-n"] = "background-color:red;";
        $theme[".igk-def-c"] = "{(sys:.igk-fs-n); line-height:1.25;}";
 
        $this->assertEquals(
            "{background-color:red; line-height:1.25;}",
            igk_css_treat($theme[".igk-def-c"], false, $theme, $systheme),
            "styling not valid"
        );
    }

    function test_css_sysfcolor(){
        $theme = new HtmlDocTheme(  IGKHtmlDoc::CreateDocument("test"), "test");
        $systheme = igk_app()->getDoc()->getSysTheme();


        $v =" [sysfcl:tableHead]";
        $cl = & $systheme->getCl();
        $cl["tableHead"] = "blue";
        // no sys color provided
        $this->assertEquals(
            "color: blue;",
            igk_css_treat($v, false, $theme, $systheme),
            "sysfcl: defined not valid"
        );


        $v =" [sysfcl:tableHeadKKK, #222] ";
        // no sys color provided
        $this->assertEquals(
            "color: #222;",
            igk_css_treat($v, false, $theme, $systheme),
            "sysfcl: defined not valid"
        );


        $v =" [sysfcl:tableHeadKKK] ";
        // no sys color provided
        $this->assertEquals(
            "",
            igk_css_treat($v, false, $theme, $systheme),
            "sysfcl: no default color provided must be empty removed"
        );
    }
    function test_css_syscl(){
        $theme = new HtmlDocTheme( IGKHtmlDoc::CreateDocument("test"), "test");
        $systheme = igk_app()->getDoc()->getSysTheme();


        $v ="border-left: 4px solid [syscl:pickfileBorder];";
        $cl = & $systheme->def->getCl();
        $cl["tableHead"] = "orange";
        // no color provided
        $this->assertEquals(
            "border-left: 4px solid var(--pickfileBorder);",
            igk_css_treat($v, false, $theme, $systheme),
            "syscl: defined not valid"
        );
        $cl = & $systheme->getCl();
        $cl["pickfileBorder"] = "blue"; 
        $v ="border-left: 4px solid [syscl:pickfileBorder];";
        $this->assertEquals(
            "border-left: 4px solid blue;",
            igk_css_treat($v, false, $theme, $systheme),
            "syscl: color define but not resolved not valid"
        ); 
    }

    function test_joining(){
        $theme = new HtmlDocTheme( IGKHtmlDoc::CreateDocument("test"), "test");
        $systheme = igk_app()->getDoc()->getSysTheme();
        $v = "border-left-color:[cl:confUpdateButtonBdrColor, indigo]; [bgcl:confUpdateButtonBgColor, #444] [fcl:confUpdateButtonFgColor, #fff]";
        $this->assertEquals(
            "border-left-color:indigo; background-color: #444; color: #fff;",
            igk_css_treat($v, false, $theme, $systheme),
            "syscl: color define but not resolved not valid"
        );
    }
}
