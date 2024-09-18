<?php
// @author: C.A.D. BONDJE DOUE
// @file: TreatColumnNameTest.php
// @date: 20240916 13:33:08
namespace IGK\Tests\System\DBUtilityTests;

use IGK\System\Database\Helper\DbUtility;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\DBUtilityTests
* @author C.A.D. BONDJE DOUE
*/
class TreatColumnNameTest extends BaseTestCase{
    public function test_dbutest_prefixname(){
        $this->assertEquals("id", DbUtility::TreatColumnName("id", null));
        $this->assertEquals("rds_id", DbUtility::TreatColumnName("id", "rds_"));
        $this->assertEquals("rds_", DbUtility::TreatColumnName("rds_", "rds_"));
        $this->assertEquals("rds_id", DbUtility::TreatColumnName("rds_id", "rds_"));
    }
}