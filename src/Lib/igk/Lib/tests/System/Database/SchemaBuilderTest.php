<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaBuilderTest.php
// @date: 20220814 09:19:42
// @desc: 
namespace IGK\Test\System\Database;
use IGK\System\Database\SchemaBuilder;
use IGK\Tests\BaseTestCase;

/**
* Schema builder test.
* @package IGK\Test\System\Database
*/
class SchemaBuilderTest extends BaseTestCase{
    /**
    * Tests add comment.
    */
    public function test_add_comment(){
        $n = new SchemaBuilder();
        $n->comment("info");
        $this->assertEquals("<data-schemas><!-- info --></data-schemas>", 
            $n->render(), "Info not correctly rendered");
    }
}