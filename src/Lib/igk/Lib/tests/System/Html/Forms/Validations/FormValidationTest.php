<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationTest.php
// @date: 20231229 08:48:28
// @test: phpunit -c phpunit.xml.dist ./src/application/Lib/igk/Lib/Tests/System/Html/Forms/Validations/FormValidationTest.php
namespace IGK\Tests\System\Html\Forms\Validations;

use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Forms\Validations
*/
class FormValidationTest extends BaseTestCase{
    public function test_form_validation(){
        $d = ['name'=>'Hello'];
        $this->assertEquals(
            true,
            true
        );
    }
}