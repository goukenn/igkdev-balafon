<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormHelperTest.php
// @date: 20240921 07:06:22
namespace IGK\Tests\System\Html\Forms;

use IGK\System\Html\Forms\FormHelper;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms
* @author C.A.D. BONDJE DOUE
*/
class FormHelperTest extends BaseTestCase{

    /**
    * Tests formhelper converttoinputdatetimelocal.
    */
    public function test_formhelper_converttoinputdatetimelocal(){

        $this->assertEquals('1983-08-04T20:00', FormHelper::ConvertToInputDateTimelocal("1983-08-04 20:00:00"));
    }
}   