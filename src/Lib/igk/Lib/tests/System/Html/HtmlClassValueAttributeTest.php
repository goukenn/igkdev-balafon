<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlClassValueAttributeTest.php
// @date: 20230313 14:45:53
namespace IGK\Tests\System\Html;

use IGK\System\Html\Dom\HtmlNode;
use IGK\Tests\BaseTestCase;

/**
* 
* @package IGK\Tests\System\Html
*/
class HtmlClassValueAttributeTest extends BaseTestCase{

    /**
    * Tests add class.
    */
    public function test_add_class(){
        $t = igk_create_node('div');
        $this->assertEquals(
            '', $t['class']?->getValue()
        );
    }

    /**
    * Tests add class array.
    */
    public function test_add_class_array(){
        $t = new HtmlNode('div');
        $t->setClass( ['info', 'data'=>true]);
        $s = $t['class']->getValue();
        $this->assertEquals(
            'info data', $s
        );
    }
}