<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssUtilsTest.php
// @date: 20230316 17:28:12
namespace IGK\Tests\System\Css;

use IGK\System\Html\Css\CssUtils;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Css
*/
class CssUtilsTest extends BaseTestCase{
    public function test_detected_operator(){
        $tabs = CssUtils::GetClassValues("-value +info");

        $this->assertEquals([
            ["value", "-"],
            ["info", "+"],
        ], $tabs);
    }
}