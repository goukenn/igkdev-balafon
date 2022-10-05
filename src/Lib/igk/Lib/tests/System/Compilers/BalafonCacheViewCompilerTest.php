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
        error_clear_last();
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

    public function test_compile_with_litteral_quote(){
        $temp = @tempnam( "tutest","test");
        // $t->section()->article($ctrl, "styles/corecss.template", [(object)[ 
        //     "description"=>
        $g_src = <<<'PHP'
<?php
$x = <<<HTML
    <div>hello the 'bbb </div>
HTML;
PHP;
error_clear_last();
        igk_io_w2file($temp, $g_src);
 
        $out = BalafonCacheViewCompiler::Compile(Dummy::ctrl(), $temp, null, true);
        unlink($temp);
 
        $this->assertEquals(<<<EDF
<?php
\$x = <<<HTML
    <div>hello the 'bbb </div>
HTML;
EDF, rtrim($out), "failed");
    }

    public function test_compile_with_litteral_nowdoc(){
        $temp = @tempnam( "tutest","test");
        error_clear_last();
        // $t->section()->article($ctrl, "styles/corecss.template", [(object)[ 
        //     "description"=>
        $g_src = <<<'PHP'
<?php
$x = <<<'HTML'
    <div>hello the 'bbb </div>
HTML;
PHP;
        igk_io_w2file($temp, $g_src);
 
        $out = BalafonCacheViewCompiler::Compile(Dummy::ctrl(), $temp, null, true);
        unlink($temp);
 
        $this->assertEquals(<<<EDF
<?php
\$x = <<<'HTML'
    <div>hello the 'bbb </div>
HTML;
EDF, rtrim($out), "failed");
    }

}


class Dummy extends TestController{

}