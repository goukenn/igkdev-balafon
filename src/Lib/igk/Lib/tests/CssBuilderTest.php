<?php

namespace IGK\Tests;

use IGK\Helper\StringUtility;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDoc;
use IGKHtmlDoc;

class CssBuilderTest extends BaseTestCase
{

    function test_rendergin()
    { 
        $theme = new HtmlDocTheme(  IGKHtmlDoc::CreateDocument("test"), "test");

        $cv =  "[bgcl: actionBarButtonHoverBackgroundColor, #333] color:yellow; box-shadow: 0px 2px 6px [cl:ationBarButtonShadowColor, #111]; [transform:scale(1.1)]";
        
        $r = igk_css_treat_gtheme($cv, $theme, $theme);
        $this->assertEquals(
            "background-color: #333; color:yellow; box-shadow: 0px 2px 6px #111; -webkit-transform: scale(1.1);-ms-transform:scale(1.1); -moz-transform:scale(1.1); -o-transform: scale(1.1); transform: scale(1.1);",
            $r, 
            "css_test_evaluatation"
        );
        $this->assertTrue(true);
    }
}
