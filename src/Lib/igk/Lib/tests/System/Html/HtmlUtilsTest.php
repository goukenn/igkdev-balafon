<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlUtilsTest.php
// @date: 20260912 10:36:56
namespace IGK\Tests\System\Html;

use IGK\System\Html\HtmlUtils;
use IGK\Tests\BaseTestCase;

/**
 * 
 * @package IGK\Tests\System\Html
 * @author C.A.D. BONDJE DOUE
 */
class HtmlUtilsTest extends BaseTestCase
{

    /**
     * 
     * @return void 
     */
    public function test_htmlutils_treat_request_value()
    {

        $t = HtmlUtils::TreatRequestArgumentValue([44, null, 'a<script></script><div>tag</div>', 'b', [
            "basic",
            "ok",
            (object)[
                'list' => 555,
                'x' => 444,
                'y' => '<img src=x> <p>data </p>',
            ],
            true,
            "false"
        ]]);
        $this->assertEquals('[44,null,"a<div>tag</div>","b",["basic","ok",{"list":555,"x":444,"y":" <p>data </p>"},true,"false"]]', json_encode($t, JSON_UNESCAPED_SLASHES));
    }

    public function test_htmlutils_font_value()
    {
        $t = HtmlUtils::TreatRequestArgumentValue('<font color="red">red value</font> #FF0000');
        $this->assertEquals('red value #FF0000', $t);
    }
    public function test_htmlutils_strip_html_comment()
    {
        $t = HtmlUtils::TreatRequestArgumentValue('<!-- font color="red">red value</font> #FF0000 -->');
        $this->assertEquals('', $t);
    }
}
