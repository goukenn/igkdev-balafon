<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormBuilderTest.php
// @date: 20260209 15:45:17
namespace IGK\Tests\System\Html;

use IGK\Tests\BaseTestCase;

/**
* 
* @package IGK\Tests\System\Html
* @author C.A.D. BONDJE DOUE
*/
class FormBuilderTest extends BaseTestCase{

    /**
    * auto generate doc.
    */
    public function test_formfieldmethod_build_form(){
        $n = igk_create_node('form');
        $n->fields([]);
        $this->assertEquals(
            '<div class="content"></div>',
            $n->getBodyContent()->render()
        );
    }

    /**
    * auto generate doc.
    */
    public function test_formfieldmethod_build_multi_field(){
        $n = igk_create_node('form');
        $n->fields(['i[]']);
        $this->assertEquals(
            '<div class="content"><div class="igk-form-group text i"><label for="i" class="igk-form-label">I</label><input class="igk-form-control text" id="i" name="i[]" placeholder="i" type="text"/></div></div>',
            $n->getBodyContent()->render()
        );
    }

    /**
    * auto generate doc.
    */
    public function test_formfieldmethod_build_multi_field_def(){
        $n = igk_create_node('form');
        $n->fields(['i[]'=>[
            ["label"=>'one'],
            ["label"=>'tow']
        ]]);
        $this->assertEquals(
            '<div class="content"><div class="igk-form-group text i"><label for="i00" class="igk-form-label">I</label><input class="igk-form-control text" id="i00" label="one" name="i[]" placeholder="i" type="text"/></div><div class="igk-form-group text i"><label for="i01" class="igk-form-label">I</label><input class="igk-form-control text" id="i01" label="tow" name="i[]" placeholder="i" type="text"/></div></div>',
            $n->getBodyContent()->render()
        );
    }
}