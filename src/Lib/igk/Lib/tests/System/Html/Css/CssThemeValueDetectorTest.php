<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeValueDetectorTest.php
// @date: 20241030 06:45:11
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssThemeValueDetector;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;
use IGKHtmlDoc;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssThemeValueDetectorTest extends BaseTestCase{
    public function test_csstheme_remove_litteral(){
        $exp = 'background-color    : red;  content:\'{ marge    du roi}\'  {sys: posfix, fitw}  [bgcl:   --info] color: [cl:--marge];';
        $d = CssThemeValueDetector::RemoveTransformLitteralFrom($exp);
        $this->assertEquals(
            'background-color : red; content:\'{ marge    du roi}\' [bgcl: --info] color: [cl:--marge];',
            $d
        );
    }

    public function test_csstheme_remove_global(){
        $exp = 'background-color    : [cl:   --bg-color]; [trans:.4s indigan ease-inout]';
        $d = CssThemeValueDetector::RemoveTransformLitteralFrom($exp, true);
        $this->assertEquals(
            'background-color : [cl: --bg-color];',
            $d
        );
    }
    public function test_csstheme_remove_property(){
        $exp = 'color:red; background-color    : [cl:   --bg-color]; [trans:.4s indigan ease-inout]';
        $d = CssThemeValueDetector::RemoveTransformLitteralFrom($exp, true,true);
        $this->assertEquals(
            'background-color:[cl: --bg-color];',
            $d
        );
    }
    public function test_csstheme_treat_global(){ 
        $detector = new CssThemeValueDetector;
        $v ="{sys:posab, fitw} margin-top:-10px; visibility: hidden; [trans: .5s all ease-out] opacity:0; left:0px; right:0px; z-index: 100; min-height: 80px; background-color: [cl:menuLayerBackground,#222a];"; 
        $v = $detector->treat($v);
        $this->assertEquals('background-color:[cl:menuLayerBackground,#222a];', $v);

    }

    public function test_csstheme_render_d(){
        $v_doc = IGKHtmlDoc::CreateCoreDocument('temp');
        $systheme = new HtmlDocTheme(null,'sys-temp-global'); 
        HtmlUtils::InitSystemTheme($systheme); 
        $systheme->initGlobalDefinition(true); 
        //$b = $systheme->get_css_def();
        $s = new HtmlDocTheme;
        $s['sample'] = "{sys:dispgrid, fitw, alignc}"; // width: 32px; {sys:dispgrid fit alignc} align-items:center;";

        $n = $s->get_css_def(true, true, null,null, $systheme);
        $this->assertEquals('sample{display:grid;text-align:center !important;width:100%;}', $n);

    }
}