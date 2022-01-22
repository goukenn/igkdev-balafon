<?php

namespace IGK\Tests;

use IGK\Helper\StringUtility;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDoc;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGKHtmlDoc;

class ThemeBuilderTest extends BaseTestCase
{
    private function _get_primary_theme(){
        $theme = new HtmlDocTheme(IGKHtmlDoc::CreateDocument(-1), "test");
        $theme[".info"] = "background-color:red;";
        $cl = &$theme->getCl();
        $cl["yellostyle"] = "#879874";

        $xsm_screen = $theme->get_media(HtmlDocThemeMediaType::XLG_MEDIA);
        $xsm_screen[".info"] = "background-color: indigo;";

        $xsm_screen = $theme->get_media(HtmlDocThemeMediaType::SM_MEDIA);
        $xsm_screen[".info"] = "background-color: red;";
        return $theme;
    }
    function test_theme_to_array()
    {
        $theme = new HtmlDocTheme(IGKHtmlDoc::CreateDocument(-1), "test");
        $theme[".info"] = "background-color:red;";
        $cl = &$theme->getCl();
        $cl["yellostyle"] = "#879874";

        $xsm_screen = $theme->get_media(HtmlDocThemeMediaType::XLG_MEDIA);
        $xsm_screen[".info"] = "background-color: indigo;";

        $xsm_screen = $theme->get_media(HtmlDocThemeMediaType::SM_MEDIA);
        $xsm_screen[".info"] = "background-color: red;";

        // igk_wln($xsm_screen);  
        $tab = $theme->to_array();

        //igk_wln_e($tab, "definition : ". $theme->get_css_def());


        $this->assertTrue($tab !== null);

        $this->assertEquals(
            <<<EOF
/* <!-- Attributes --> */
.info{background-color:red;}
@media (min-width:321px) and (max-width:710px){
.info{background-color: red;}
}
@media (min-width:1025px) and (max-width:1300px){
.info{background-color: indigo;}
}
EOF,
            $theme->get_css_def(),
            "style definition not matching"
        );
    }
    public function test_serialize_unserialize_theme(){

        $theme = $this->_get_primary_theme();
        $src = $theme->to_array();
        $g = serialize($src);

        //igk_wln($g);

        $theme->load_data(unserialize($g));
        $new = $theme->to_array();
        // var_dump($src);
        // echo "new \n";
        // var_dump($new);
        // igk_wln($src, $new);

        $this->assertEquals($src, $new, "Serialize/Unserialize theme failed");

    }
}
