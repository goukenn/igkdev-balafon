<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlValidatorTest.php
// @date: 20230117 15:13:37
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/Tests/System/Validator/HtmlValidatorTest.php 
namespace IGK\Tests\System\Validator;

use IGK\System\Html\Forms\HtmlValidator;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Validator
*/
class HtmlValidatorTest extends BaseTestCase{
    public function test_remove_tag(){
        $validator = new HtmlValidator;
        $s = "<div>Hello </div><div />word!!!";
        $this->assertEquals("Hello word!!!",         
        $validator->validate($s));
    }

    public function test_remove_leave_one_tag(){
        $validator = new HtmlValidator;
        $validator->allowed_tags = ['p'];
        $validator->skip_all = false;
        $s = "<div>Hello </div><div /><p><div>word!!!</p>";
        $this->assertEquals("Hello word!!!",         
        $validator->validate($s));
    }
}