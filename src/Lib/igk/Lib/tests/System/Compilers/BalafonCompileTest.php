<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCompileTest.php
// @date: 20220830 17:44:36
// @desc: 
namespace IGK\Tests\System\Compilers;

use IGK\Helper\StringUtility;
use IGK\System\Runtime\Compiler\BalafonViewCompiler;
use IGK\Tests\BaseTestCase;

class BalafonCompileTest extends BaseTestCase
{
/*
    public function test_compilation_expression()
    {
        $src = <<<'PHP'
<?php
echo 'bonjour';
PHP;

        $g = BalafonViewCompiler::CompileSource($src, []);
        $this->assertEquals(
            <<<'PHP'
<?php
echo 'bonjour';
PHP,
            $g->source,
            "failed to compile expression"
        );
    }

    public function test_compilation_remove_single_line_comment()
    {
        $src = <<<'PHP'
<?php
// remove this 
echo 'bonjour';
PHP;

        $g = BalafonViewCompiler::CompileSource($src, []);
        $this->assertEquals(
            <<<'PHP'
<?php
echo 'bonjour';
PHP,
            $g->source,
            "failed to compile expression"
        );
    }*/


    public function test_read_identifier(){
        $offset = 1;
        $this->assertEquals("bonjour", 
            StringUtility::ReadIdentifier("@bonjour", $offset),
            "not correct"
        );
    }

    public function test_compilation_remove_single_line_comment()
    {
        $src = <<<'PHP'
<?php
// remove this
#{{% @MainLayout }} 
echo 'bonjour';
PHP;
 igk_debug(true);
$g = BalafonViewCompiler::CompileSource($src, (object)[
    "layout"=>new \IGK\System\WinUI\PageLayout
]); 
        $this->assertEquals(
            <<<'PHP'
<?php
echo 'bonjour';
PHP,
            $g->source,
            "failed to compile expression"
        );
    }




    //     public function test_compile_file(){
    //         $s = "";
    //         $s .= <<<'PHP'
    // if (!function_exists('__')){
    //     function __($n){
    //         return igk_resources_gets(...func_get_args());
    //     };
    // }else {
    //     if (!class_exists(translator::class, false)){
    //         require_once IGK_LIB_CLASSES_DIR.'/IGKTranslator.php';

    //         class translator extends \IGK\IGKTranslator{        
    //         }
    //     }
    // }
    // $params = ['code'=>413];
    // include('/Volumes/Data/wwwroot/core/Projects/igk_default/Views/.error/413.phtml');
    // PHP;
    //         //$s .= "include('/Volumes/Data/wwwroot/core/Projects/igk_default/Views/.error/413.phtml');";
    //         ob_start();
    //         eval($s);
    //         $ob = ob_get_contents();
    //         ob_end_clean();
    //         $this->assertEquals("response...", 
    //         $ob, "failed");
    //     }
}
