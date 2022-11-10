<?php

// @author: C.A.D. BONDJE DOUE
// @filename: MakeProjectTest.php
// @date: 20220914 11:29:45
// @desc: 
namespace IGK\Test\System\Console\Commands;

use IGK\System\Database\SchemaBuilder;
use IGK\Tests\BaseTestCase;

class MakeProjectTest extends BaseTestCase{
    function test_make_comment(){
        $build=new SchemaBuilder();
        $build["version"] = "1.0";
        $build["author"] = "C.A.D BONDJE DOUE";
        $build["createAt"] = '20130101 12:00:00';
        $c = $build->comment("data schema"); 
        $out = $build->render((object)["Context"=>"XML", "Indent"=>true]); 
        $this->assertEquals(
            <<<XML
<data-schemas version="1.0" author="C.A.D BONDJE DOUE" createAt="20130101 12:00:00">
\t<!-- data schema -->
</data-schemas>
XML,
$out,
        "value not equal"
        );

    }
}