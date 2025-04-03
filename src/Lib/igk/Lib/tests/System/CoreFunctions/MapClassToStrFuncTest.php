<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapClassToStrFuncTest.php
// @date: 20250403 11:01:32
namespace IGK\Tests\System\CoreFunctions;

use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\CoreFunctions
* @author C.A.D. BONDJE DOUE
*/
class MapClassToStrFuncTest extends BaseTestCase{
    public function test_maptostr_bool(){
        $r = trim(igk_map_array_to_str(['info'=>true]));
        $this->assertEquals('"info"=>true,', $r);

        $r = trim(igk_map_array_to_str(['info'=>false]));
        $this->assertEquals('"info"=>false,', $r);
    }
}