<?php
// @author: C.A.D. BONDJE DOUE
// @file: StringDisplayTest.php
// @date: 20251226 13:47:33
namespace IGK\Tests\Helper;

use IGK\Helper\StringDisplay;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\Helper
* @author C.A.D. BONDJE DOUE
*/
class StringDisplayTest extends BaseTestCase{

    /**
    * Tests stringdisplay litteral.
    */
    public function test_stringdisplay_litteral(){
    $l = StringDisplay::Display('hi!, ", ", login', ['login'], (object)['login'=>IGK_AUTHOR]);
    $this->assertEquals(sprintf('hi!, %s', IGK_AUTHOR), $l);
}
}