<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCacheViewCompilerTest.php
// @date: 20220513 13:01:40
// @desc: 
namespace IGK\Tests\System\Compilers;

use IGK\Controllers\ApplicationController;
use IGK\System\Compilers\BalafonCacheViewCompiler;
use IGK\Tests\BaseTestCase;
use IGK\Tests\Controllers\TestController;

class BalafonCacheViewCompilerTest extends BaseTestCase{

    public function test_compile_empty_string(){
        $temp = @tempnam( "tutest","test");
$g_src = <<<EOF
<?php
\$c = [ "" ];
EOF;

        igk_io_w2file($temp, $g_src);
        $s = 0; 
        $g = explode("\n", BalafonCacheViewCompiler::Compile(Dummy::ctrl(), $temp));        
        array_pop($g);
        array_pop($g);
        unlink($temp);
        $s =  implode("\n", $g);  
        $this->assertEquals($g_src, $s,  "failed .... ".$temp);
        error_clear_last();
    }

}


class Dummy extends TestController{

}