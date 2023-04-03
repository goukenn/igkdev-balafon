<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClassSettingBehaviourTest.php
// @date: 20230315 10:37:48
namespace IGK\Tests\System\Html\Dom;

use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Dom
*/
class ClassSettingBehaviourTest extends BaseTestCase{
    public function test_remove_class(){
        $t = igk_create_node('div');
        $t['class'] = 'cl1 cl2';
        $t->setClass('-cl1');
        $this->assertEquals('cl2', $t['class']->getValue());
    }
    public function test_remove_class_attr(){
        $t = igk_create_node('div');
        $t['class'] = 'cl1 cl2';
        $t['class']  = '-cl2';
        $this->assertEquals('cl1', $t['class']->getValue());
    }
    

}