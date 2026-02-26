<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeRenderingTest.php
// @date: 20250403 11:48:00
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssMinifier;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\Tests\BaseTestCase;

/**
* 
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssThemeRenderingTest extends BaseTestCase{

    /**
    * Tests csstheme minify content rendering.
    */
    public function test_csstheme_minify_content_rendering(){
        $d = new HtmlDocTheme(null, 'testing', false);
        $d[] = 'body:after{content:""; background-color:red;}';
        $rep = $d->get_css_def(true, true);
        $min = new CssMinifier;
        $rep = $min->minify($rep);
        $this->assertEquals('body:after{content:\'\';background-color:red;}', $rep);
    }
}