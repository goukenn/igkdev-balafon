<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderTest.php
// @date: 20230309 21:36:19
namespace IGK\Tests\System\TamTam;

use IGK\System\TamTam\Helper\ProjectBuilderHelper;
use IGK\System\TamTam\ProjectSettingValidationData;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\TamTam
*/
class ProjectBuilderTest extends BaseTestCase{
    function test_validate_data(){
        $json_data = json_decode('{"version":"1.0"}');
        $this->assertTrue( ProjectBuilderHelper::ValidateConfigData($json_data, ProjectSettingValidationData::class) != null );
        
    }
}