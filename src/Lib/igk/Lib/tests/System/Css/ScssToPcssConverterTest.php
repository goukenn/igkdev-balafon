<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScssToPcssConverterTest.php
// @date: 20230124 15:18:52
namespace IGK\Tests\System\Css;

use IGK\Css\CssConverter;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
 * 
 * @package IGK\Tests\System\Css
 */
class ScssToPcssConverterTest extends BaseTestCase
{
    public function test_reader()
    {

        $content = <<<'JS'
@import '../../base/base'; // Base Variables
$info: #123100;
html {
    height: 100%;
    width: 450px;
    &#item:focus.li {
        height: 88px;
        color: $info;
        background-color: red;
        content: ' presentation: du jour';
        background-image: url(https://cdn.pixabay.com/photo/2015/12/10/16/39/shield-1086703_960_720.png);
    }
}
JS;
$conv = new CssConverter;
$data = $conv->parseScssContent($content);

        $this->assertEquals(
            '{"html":{"height":"100%","width":"450px"},"html#item:focus.li":{"height":"88px","color":"#123100","background-color":"red","content":"\' presentation: du jour\'","background-image":"url(https:\/\/cdn.pixabay.com\/photo\/2015\/12\/10\/16\/39\/shield-1086703_960_720.png)"},"@variables":{"info":"#123100"}}', 
        json_encode($data));
    }
}
