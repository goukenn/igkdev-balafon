<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClassDefinitionTest.php
// @date: 20240117 09:06:38
namespace IGK\Tests\System\Html;

use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html
* @author C.A.D. BONDJE DOUE
*/
class ClassDefinitionTest extends BaseTestCase{
    public function test_html_set_class_array(){
        $n = igk_create_node('div');
        $n['class'] = ['underline'=>true, 'litteral'=>false];
        $n->Content = 'Hello';
        $this->assertEquals('<div class="underline">Hello</div>', $n->render(), 'cannot load array class');
    }
    public function test_html_set_class_object(){
        $n = igk_create_node('div');
        $n['class'] = (object)['underline'=>true, 'litteral'=>false];
        $n->Content = 'Hello';
        $this->assertEquals('<div class="underline">Hello</div>', $n->render(), 'cannot load object class');
    }
}