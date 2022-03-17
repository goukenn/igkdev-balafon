<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlXmlViewerTest.php
// @date: 20220314 11:52:37
// @desc: 
namespace IGK\Tests\System\Html\Dom;

use IGK\Tests\BaseTestCase;

class HtmlXmlViewerTest extends BaseTestCase{
    public function test_load_expression(){
        $n = igk_create_node("div");
        $n->xmlviewer()->Content = "<a *title=\"\$raw->title\">title</a>";

        $this->assertEquals(
            '<div><div class="igk-xml-viewer"><a *title="$raw->title">title</a></div></div>',
            $n->render()
        );
    }
}