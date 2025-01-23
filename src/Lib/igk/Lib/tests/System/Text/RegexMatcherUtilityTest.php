<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherUtilityTest.php
// @date: 20241107 11:38:21
namespace IGK\Tests\System\Text;

use IGK\System\Text\RegexMatcherUtility;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherUtilityTest extends BaseTestCase{
    public function test_regexmatcher_utility_begin_end(){
        $this->assertEquals('bonjoto',
            RegexMatcherUtility::TreatBeginEndCapture('bonjour', '', 'to',0,5)
        );
    }
    public function test_regexmatcher_utility_begin_hello(){
        $this->assertEquals('hello',
            RegexMatcherUtility::TreatBeginEndCapture('bonjour', 'hello', null,null)
        );
    }
}