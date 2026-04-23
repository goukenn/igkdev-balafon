<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ThemeBuilderTest.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\Tests;
use IGK\Helper\StringUtility;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDoc;
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGKHtmlDoc;

/**
* Theme builder test.
* @package IGK\Tests
*/
class ThemeBuilderTest extends BaseTestCase
{
    /**
    * auto generate doc.
    * @param mixed $id
    * @return
    */
    private static function _CreateTheme($id){
        return new HtmlDocTheme(IGKHtmlDoc::CreateDocument(-1), $id, false);
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _get_primary_theme(){
        $theme = self::_CreateTheme('test');
        $theme[".info"] = "background-color:red;";
        $cl = &$theme->getCl();
        $cl["yellostyle"] = "#879874";
        $xsm_screen = $theme->getMedia(HtmlDocThemeMediaType::XLG_MEDIA);
        $xsm_screen[".info"] = "background-color: indigo;";
        $xsm_screen = $theme->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
        $xsm_screen[".info"] = "background-color: red;";
        return $theme;
    }
    /**
    * Tests theme to array.
    */
    function test_theme_to_array()
    {
        $theme = self::_CreateTheme('test');
        $theme[".info"] = "background-color:green;";
        $cl = &$theme->getCl();
        $cl["yellostyle"] = "#879874";
        $xsm_screen = $theme->getMedia(HtmlDocThemeMediaType::XLG_MEDIA);
        $xsm_screen[".info"] = "background-color: red;";
        $sm_screen = $theme->getMedia(HtmlDocThemeMediaType::SM_MEDIA);
        $sm_screen[".info"] = "background-color: yellow;";
        $tab = $theme->to_array();       
        $this->assertTrue($tab !== null);
        $this->assertEquals(<<<EOF
/* <!-- Attributes --> */
.info{background-color:green;}
/* <!-- end:Attributes --> */
@media (min-width:321px) and (max-width:710px){
.info{background-color: yellow;}
}
@media (min-width:1025px) and (max-width:1300px){
.info{background-color: red;}
}
EOF,
            $theme->get_css_def(),
            "style definition not matching"
        );
    }
    /**
    * Tests serialize unserialize theme.
    */
    public function test_serialize_unserialize_theme(){
        $theme = $this->_get_primary_theme();
        $src = $theme->to_array();
        $g = serialize($src);
        $theme->load_data(unserialize($g));
        $new = $theme->to_array();
        $this->assertEquals($src, $new, "Serialize/Unserialize theme failed");
    }
}