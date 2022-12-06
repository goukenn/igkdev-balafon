<?php
// @author: C.A.D. BONDJE DOUE
// @file: GuidTest.php
// @date: 20221206 11:03:13

// phpunit -c phpunit.xml.dist ./src/Lib/igk/Lib/Tests/GuidTest.php
namespace IGK\Tests;

use IGK\System\Regex\RegexConstant;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests
*/
class GuidTest extends BaseTestCase{
    public function test_guid(){ 
        $d = preg_match(RegexConstant::GUID_REGEX, '{0b3a8f0c-9030-fb31-e150-5f1f2e224a39}', $tab);
        $this->assertTrue($d==1);
    }
}