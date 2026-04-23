<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConditionalNodeTest.php
// @date: 20221130 13:26:52
namespace IGK\Tests\System\Html\Dom;
use IGK\System\Html\Dom\ConditionalNode;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Dom
*/
class ConditionalNodeTest extends BaseTestCase{
    /**
    * Tests render conditional.
    */
    public function test_render_conditional(){
        $d = new ConditionalNode;
        $d->condition = 'if lt IE 9';
        $d->script()->Content = 'console.dummy';
        $this->assertEquals(<<<'HTML'
<!--[if lt IE 9]>
<script language="javascript" type="text/javascript">console.dummy</script>
<![endif]-->
HTML,
$d->render()
        );
    }
}