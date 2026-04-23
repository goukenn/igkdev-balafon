<?php
// @author: C.A.D. BONDJE DOUE
// @file: EngineReadArgsTest.php
// @date: 20251022 09:32:15
namespace IGK\Tests\System\Core;
use IGK\System\Core\EngineReadArgs;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\Core
* @author C.A.D. BONDJE DOUE
*/
class EngineReadArgsTest extends BaseTestCase{
    /**
    * Tests engine read arg read global arg.
    */
    public function test_engine_read_arg_read_global_arg(){
        $this->assertEquals('8 - info - 8', 
        EngineReadArgs::TreatGlobalArgs('[[:@raw]] - info - [[:@raw]]', [
            'raw'=>8
        ]));
    }
}