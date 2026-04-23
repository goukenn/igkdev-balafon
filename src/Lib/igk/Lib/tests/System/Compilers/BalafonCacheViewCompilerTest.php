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

/**
* Balafon cache view compiler test.
* @package IGK\Tests\System\Compilers
*/
class BalafonCacheViewCompilerTest extends BaseTestCase{
    /**
    * Tests compile empty string.
    */
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
    /**
    * Tests compile with litteral quote.
    */
    public function test_compile_with_litteral_quote(){
        $temp = @tempnam( "tutest","test");
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
    /**
    * Tests compile with litteral nowdoc.
    */
    public function test_compile_with_litteral_nowdoc(){
        $temp = @tempnam( "tutest","test");
        error_clear_last();
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
/**
* Dummy.
* @package IGK\Tests\System\Compilers
*/
class Dummy extends TestController{
}