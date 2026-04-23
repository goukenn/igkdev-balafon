<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlValidatorTest.php
// @date: 20230117 15:13:37
namespace IGK\Tests\System\Validator;
use IGK\System\Html\Forms\Validations\HtmlValidator;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\Validator
*/
class HtmlValidatorTest extends BaseTestCase{
    /**
    * Tests remove tag.
    */
    public function test_remove_tag(){
        $validator = new HtmlValidator;
        $s = "<div>Hello </div><div />word!!!";
        $this->assertEquals("Hello word!!!",         
        $validator->validate($s));
    }
    /**
    * Tests remove leave one tag.
    */
    public function test_remove_leave_one_tag(){
        $validator = new HtmlValidator;
        $validator->allowed_tags = ['p'];
        $validator->skip_all = false;
        $s = "<div>Hello </div><div /><p><div>word!!!</p>";
        $this->assertEquals("Hello word!!!",         
        $validator->validate($s));
    }
}