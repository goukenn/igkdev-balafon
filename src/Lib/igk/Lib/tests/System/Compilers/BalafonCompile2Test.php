<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCompile2Test.php
// @date: 20220830 17:44:36
// @desc: 
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/tests/System/Compilers/BalafonCompileTest.php
namespace IGK\Tests\System\Compilers;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\BalafonViewCompileInstruction;
use IGK\System\Runtime\Compiler\BalafonViewCompiler;
use IGK\System\Runtime\Compiler\BalafonViewCompiler2;
use IGK\System\Runtime\Compiler\BalafonViewCompilerOptions;
use IGK\System\Runtime\Compiler\BalafonViewCompilerUtility;
use IGK\System\Runtime\Compiler\Html\CompilerNodeModifyDetector;
use IGK\System\Runtime\Compiler\Html\ConditionBlockNode;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\WinUI\PageLayout;
use IGK\Tests\BaseTestCase;
use IGK\Tests\Controllers\TestController;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use IGKException;


/**
 * test compiler ... 
 * @package IGK\Tests\System\Compilers
 */
class BalafonCompile2Test extends BalafonCompileBase
{
    private function __compiler_source(string $src){
        $compiler = new BalafonViewCompiler2;
        $compiler->options = new ViewEnvironmentArgs;
        $compiler->options->ctrl = new CompileTestController;
        $compiler->options->ctrl->entryDir = self::$sm_tempdir;
        $g = BalafonViewCompilerUtility::GetInstructionsList($src, true, $compiler);
        igk_wln_e("instruction list ", $g);
        return $compiler->output();
    }


    public function test_file_11()
    {
        igk_debug(true);
        $g = $this->__compiler_source(file_get_contents(__DIR__."/.testfiles/test.11.php"));
        $this->assertEquals(
            "view directory .....",
            $g ,
            "failed :".__METHOD__
        );
    }

    public function _test_compile_loop(){
        // single test
        // igk_debug(true);
        foreach([
            "if"=>["<?php\n if(true) return true;", "<?php if(true):\nreturn true;\nendif; ?>"],
            "foreach"=>[
            "<?php\n foreach(\$data as \$k=>\$v) return true;", 
            "<?php foreach(\$data as \$k=>\$v):\nreturn true;\nendforeach; ?>"] 
            ,"while"=>[
                "<?php\n while(\$data) return true;", 
                "<?php while(\$data):\nreturn true;\nendwhile; ?>"]   
            ,"for"=>[
                "<?php\n for(\$data) return true;", 
                "<?php for(\$data):\nreturn true;\nendfor; ?>"]   
            ,"swith"=>[
                "<?php\n switch(\$data) return true; ", 
                "<?php switch(\$data):\nreturn true;\nendswitch; ?>"]   

        ] as $k=>$v){
            $g = $this->__compiler_source($v[0]);
            // igk_wln_e(__FILE__.":".__LINE__, 
            //  "the g: ", 
            //  $g);
            $this->assertEquals(
                $v[1],
                $g ,
                "failed : block 1 ".$k
            );
        }
    }

    public function _test_eval_code_soure(){
        $src = implode("\n", [
            // 'if (true){ $x = $g    . "--"; }',
            // 'if (true){ $x=8; $t->div()->Content = "   $x---llml"; }',
            // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}---llml"; }',
            // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}" . $x; }',
            // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}" . ( 8 + $x + 1); }',
            // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}"; }'
            //'if (true){ $x=8; $t->div()->Content = "---{$x->value}". $x; }'
            // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}". ($x + 8); }' // Unsupported operand types
            // 'if ($x === (true ||false) ){ $x=8; $t->div()->Content = "---{$x->value}". (8 + $x ); }' // Unsupported operand types
            'if ($x === (true ||false) ){ $x="jav8"; $t->add($x)->setClass("intro")->Content = "---{$x}". (8 . $x ); }' // Unsupported operand types
            // ' ?  478 : $y = 88; ',
            // 'if (true){ $x = $g. "":  ?  478 : $y = 88; ',
            // '$quota = 999; ',
            // '$defd = "info ".$x; ',
            // '$t->div()->Content = "top block 1:{$x}"; ',
            // '$t->div()->Content = "top block 2:".$x; ',
            // 'if (8) { $t->div()->Content = "middle"; }  $t->div()->Content = "end block"; }',            
        ]);  

        //igk_wln($src);
        $compiler = new BalafonViewCompiler2;
        $compiler->options = new ViewEnvironmentArgs;
        $compiler->options->ctrl = new CompileTestController;
        $compiler->options->ctrl->entryDir = self::$sm_tempdir;

        // igk_debug(true);
        // try{
        $g = BalafonViewCompilerUtility::GetInstructionsList($src, true, $compiler);


        // igk_wln_e("entry directory .... ", $g, "output: ",  $compiler->output());
        // }
        // catch(\Error $ex){
        //     igk_wln_e("the error: ".$ex->getMessage());
        // }

        $this->assertEquals(
            implode("\n",[
                '<?php if ($x=== (true ||false) ): ',
                '$x ="jav8";',
                '?><<?= $x ?> class="intro">---<?= $x ?>8<?= $x ?></<?= $x ?>><?php',
                'endif; ?>'
            ])
            , $compiler->output(),
            "failed to get empty function list instruct"
        );

    }
    public function _test_func_name_token(){

        $src = implode("\n", [
            'if (true){ function myAnonymous(){ echo "sub_if"; } }',            
        ]);  
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'if (true){ function myAnonymous(){ echo "sub_if"; } }'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }

    public function _test_func_name_token_2(){

        $src = implode("\n", [
            'if (true){ function(){ echo "sub_if"; } }',            
        ]);  
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'if (true){ function(){ echo "sub_if"; };}'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }
    public function _test_func_instruction_loop(){

        $src = implode("\n", [
            'for ($i=0; $i<10;$i++) $data.=$i;',            
        ]);   
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'for ($i=0; $i<10;$i++) $data.=$i;'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }
    public function _test_func_instruction_loop_2(){

        $src = implode("\n", [
            'for ($i=0; $i<10;$i++) { $data.=$i; }',
        ]);   
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'for ($i=0; $i<10;$i++) { $data.=$i; }'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }
    public function _test_func_instruction_switch(){

        $src = implode("\n", [
            'switch($i){ case 1: echo 1; break; }',
        ]);    
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'switch($i){ case 1: echo 1; break; }'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }

    public function _test_func_instruction_foreach(){

        $src = implode("\n", [
            'foreach($i as $k=>$v){ echo "foreach"; }',
        ]);    
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'foreach($i as $k=>$v){ echo "foreach"; }'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed : ".__FUNCTION__
        );
    }
    public function _test_func_instruction_entry(){

        $src = implode("\n", [
            '$a = "information " ?> Entrer <?php',
            '$b = "de jour ";',
            'echo $a." : ".$b;',
        ]);    
        $g = BalafonViewCompilerUtility::GetInstructionsList($src, false); 
        $this->assertEquals(
            json_encode([
                (object)["value"=>'$a = "information " ?> Entrer <?php'."\n".'$b = "de jour ";'], 
                (object)["value"=>'echo $a." : ".$b;'], 
            ]), json_encode($g), 
            "failed : ".__FUNCTION__
        );
    }

    public function _test_func_instruction_namespace(){

        $src = implode("\n", [
            '   namespace test\\igkd;',
            //'final class A{}',
        ]);    
        // igk_debug(true);
        $g = BalafonViewCompilerUtility::GetInstructionsList($src, false); 
        // igk_wln_e($options);
        $this->assertEquals(
            json_encode([
                (object)["value"=>'namespace test\\igkd;'], 
            ]), json_encode($g), 
            "failed : ".__FUNCTION__
        );
    }
}