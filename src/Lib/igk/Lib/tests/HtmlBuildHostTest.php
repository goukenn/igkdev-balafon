<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlBuildHostTest.php
// @date: 20250314 16:51:42
namespace IGK\Tests;

use IGK\Tests\BaseTestCase;
use function igk_html_host as _h;
/**
* 
* @package IGK\Tests
* @author C.A.D. BONDJE DOUE
*/
class HtmlBuildHostTest extends BaseTestCase{

    /**
    * auto generate doc.
    */
    public function test_use_loop(){
        $m = _h("div.card", _h('@loop', [range(0,2), function($t, $r){
            $t->span()->content = $r;
        }])); 
        $this->expectOutputString('<div class="card"><span>0</span><span>1</span><span>2</span></div>');
        $m->renderAJX();
    }
}