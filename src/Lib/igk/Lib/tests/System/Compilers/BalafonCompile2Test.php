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
use IGK\System\Runtime\Compiler\BalafonViewCompiler2;
use IGK\System\Runtime\Compiler\BalafonViewCompilerUtility;
use IGK\System\Runtime\Compiler\Html\ViewDocumentHandler;
use IGK\System\Runtime\Compiler\ReadBlockInstructionInfo;
use IGK\System\Runtime\Compiler\ViewExpressionEval;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\WinUI\PageLayout;
use IGK\Tests\System\Html\HtmlReaderTest;
use IGKException;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
// compiler 2 missing
// class AttribHandler {
//     private $m_attribs = [];
//     public function setAttributes($attribs){
//         $this->m_attribs = array_merge($this->m_attribs, $attribs);
//         return $this;
//     }
//     public function getAttributeString(){
//         return implode(" ", array_map(function($a, $k){
//             return sprintf("%s=%s", $k, self::GetAttributeValue($a));
//         }, $this->m_attribs, array_keys($this->m_attribs)));
//     }
//     static function GetAttributeValue($v){
//         $v = '"'.htmlentities($v).'"';
//         return $v;
//     }
// }

// function igk_express_bind($ctrl, $src){
//     $tempfile = tempnam (sys_get_temp_dir(), ".export");
//     $t = new AttribHandler;
//     $t->setAttributes(["id"=>igk_css_str2class_name(strtolower($ctrl->getName()))]);
//     $y = 45;
//     igk_io_w2file($tempfile, $src);

//     igk_wln(__FILE__.":".__LINE__,  
//     "before: ", 
//     $src
//     );
//     $x = range(1, 1000); // ["45", 25, "hello every one"];

//     $start = igk_sys_request_time();
//     ob_start();
//     include ($tempfile);
//     $c = ob_get_contents();
//     ob_end_clean();
//     unlink($tempfile);
//     // $end_block = "//%{{_ATTRIBS_END}}";
//     // $end_ln = strlen($end_block);
//     // $pos = 0;
//     // while( ($pos = strpos($src, "//%{{_ATTRIBS_BEGIN}}", $pos)) !== false){
        
//     //     $end = strpos($src, $end_block) ;
//     //     if ($end!==false){
//     //         $src = substr_replace($src, "", $pos, ($end - $pos) + $end_ln + 1);
//     //     }else 
//     //         $src = substr($src, $p);
//     // } 
//     $output =  "<div %__attribs__%>".$c."</div>"; 
//     $output = str_replace("%__attribs__%", $t->getAttributeString(), $output);
//     $output = str_replace("%execution_time%", igk_sys_request_time() - $start, $output);

//     $start = igk_sys_request_time();
//     $t  = igk_create_node("p");
//     $t->loop($x)->div()->Content = "ONE - {{ \$raw }}";
//     $t->div()->Content = "%execution_time%";
//     echo  "end data : \n" . $t->render();
//     $end = igk_sys_request_time() - $start;
    
//     echo "data:\n".$output;
//     // echo "end : ".$end;
//     //echo "\nresult: \n".$c;
//     exit;
// }

 
// /**
//  * test compiler ... 
//  * @package IGK\Tests\System\Compilers
//  */
// class BalafonCompile2Test extends BalafonCompileBase
// {

//     public static function setUpBeforeClass(): void
//     {
//         $sdir = sys_get_temp_dir() . "/testCompiler";
//         IO::CreateDir($sdir);
//         self::$sm_tempdir = $sdir;
//         igk_io_w2file($sdir . "View/test.pinc", file_get_contents(__DIR__ . "/.testfiles/test.pinc"));
//     }

//     private function __compiler_source(string $src, ?array $variables=null)
//     {
//         $compiler = new BalafonViewCompiler2;
//         $compiler->options = new ViewEnvironmentArgs;
//         $compiler->options->ctrl = new CompileTestController;
//         $compiler->options->ctrl->entryDir = self::$sm_tempdir;
//         $layout = new PageLayout;
//         $layout->viewDir = self::$sm_tempdir . "/Views";
//         $compiler->options->layout = $layout;
//         // define compiler variable
//         $compiler->variables = $variables ?? [
//             "sx" => "defined-X",
//             "ix" => "23"
//         ];
//         // $compiler->variables = ["v" => "19-83", "params" => [5]];
//         BalafonViewCompilerUtility::GetInstructionsList($src, true, $compiler); 
//         return $compiler->output();
//     }


//     public function test_use_function(){ 
//         $g = $this->__compiler_source(
//             implode("\n",[
//                 "<?php",
//                 "use function igk_resources as __, IGK\\System as sys;"
//             ])
//         );
//         $this->assertEquals(
//             implode("\n",[
//                 "<?php",
//                 "use IGK\\System as sys;",
//                 "use function igk_resources as __;"
//             ]), $g, "not valid"
//         );
//     }

//     public function test_use_namespace(){ 
//         $g = $this->__compiler_source(
//             implode("\n",[
//                 "<?php",
//                 "namespace hello;"
//             ])
//         ); 
//         $this->assertEquals(
//             implode("\n",[
//                 "<?php",
//                 "namespace hello;",
//             ]), $g, "not valid"
//         );
//     }
//     public function test_use_favicon(){ 
   
//         $ctrl = CompileTestController::ctrl();
//         $ctrl->entryDir = self::$sm_tempdir;
//         $g = $this->__compiler_source(
//             implode("\n",[
//                 "<?php",
//                 '$favicon = $ctrl->getResourcesDir()."/Img/favicon.ico";'
//             ]), [
//                 "ctrl"=>CompileTestController::ctrl()
//             ]
//         ); 
//         $this->assertEquals(
//             implode("\n",[
//                 "<?php",
//                 "namespace hello;",
//             ]), $g, "not valid"
//         );
//     }
//     public function _test_use_default_project(){ 
  
//         $g = $this->__compiler_source(
//             file_get_contents( IGK_PROJECT_DIR. "/igk_default/Views/default.phtml"),
//             [
//                 "doc"=>new ViewDocumentHandler,
//                 "ctrl"=>CompileTestController::ctrl()
//             ]
//         ); 
//         igk_wln_e(__FILE__.":".__LINE__, $g);
//         $this->assertEquals(
//             implode("\n",[
//                 "<?php",
//                 "namespace hello;",
//             ]), $g, "not valid"
//         );
//     }
//     //ok
//     public function _test_expression_logic()
//     {

//         $n = igk_create_node("div");
//         $n->div()->Content = new ViewExpressionEval('$a . "-info"');
//         $this->assertEquals(
//             '<div><div><?= $a . "-info" ? ></div></div>',
//             $n->render(),
//             "logic expression failed"
//         );


//         // $n = igk_create_node("div");
//         // $n->add(new ViewExpressionEval('$a . "-my-node"'))->setAttributes(["class" => "information"])->Content = "presentation";
//         // $this->assertEquals(
//         //     '<div><<?= $a . "-my-node" ? > class="information">presentation</<?= $a . "-my-node" ? >></div>',
//         //     $n->render(),
//         //     "logic expression failed"
//         // );
//     }
//     public function _test_compile()
//     {
 

//         $src = <<<'EOF'
// <?php 
// // $___IGK_PHP_SETTER___['x'] = $x = igk_create_node('li');
// $___IGK_PHP_SETTER___['x'] = $x = $___IGK_PHP_EXPRESSION___[igk_express_eval('"Bonjour: ".$x', ['x'=>null])];
// // $x->div()->information()->setContent('48');
// // echo " ---- : ---- ".$___IGK_PHP_SETTER___->contains('x')."\n";
// // // $___IGK_PHP_SETTER___['y'] = "Hello" .$___IGK_PHP_GETTER___['x'];
// // $___AGRG___ =  $___IGK_PHP_EXPRESSION___['"Hello" .$x'];
// $___AGRG___ =  $___IGK_PHP_EXPRESSION___['$x']; 
// $___IGK_PHP_SETTER___['t']->setClass("information")->div()->Content =  $___AGRG___; 
// // // igk_wln_e('data, ', $y);
// EOF;

//         $compiler = new BalafonViewCompileInstruction;
//         $compiler->variables = [
//             "x"=>8
//         ];
//         $compiler->extract = true;
//         $compiler->instructions = [
//             (object)["value" => $src]
//         ];
        
//         $_output = $compiler->compile();
//         // $_output = $compiler->output; 

//         $this->assertEquals(
//             '? ><div><?= "Bonjour: ".$x ? ></div><?php'."\n",
//             $_output,
//             "logic expression failed"
//         );
//     }



//     public function _test_file_11()
//     {
//         $file = __DIR__ . "/.testfiles/test.11.php";
    
//         $g = $this->__compiler_source(
//             file_get_contents($file),
//             [
//                 "x"=>85,
//                 "y"=>10
//             ]
//         );

//         $target = CompileTestController::ctrl();  
//         igk_express_bind($target, $g);

//         igk_wln_e(CompileTestController::ctrl()->render());

//         $this->assertEquals(
//             "view directory .....",
//             $g,
//             "failed :" . __METHOD__
//         );
//     }

//     public function _test_compile_loop()
//     {
//         // single test 
//         foreach ([
//             "if" => ["<?php\n if(true) return true;", "<?php\nif(true):\nreturn true;\nendif;"],
//             "foreach" => [
//                 "<?php\n foreach(\$data as \$k=>\$v) return true;",
//                 "<?php\nforeach(\$data as \$k=>\$v):\nreturn true;\nendforeach;"
//             ], "while" => [
//                 "<?php\n while(\$data) return true;",
//                 "<?php\nwhile(\$data):\nreturn true;\nendwhile;"
//             ], "for" => [
//                 "<?php\n for(\$data) return true;",
//                 "<?php\nfor(\$data):\nreturn true;\nendfor;"
//             ], "swith" => [
//                 "<?php\n switch(\$data) return true; ",
//                 "<?php\nswitch(\$data):\nreturn true;\nendswitch;"
//             ]

//         ] as $k => $v) {
//             $g = $this->__compiler_source($v[0]);
//             // igk_wln_e(__FILE__.":".__LINE__, 
//             //  "the g: ", 
//             //  $g);
//             $this->assertEquals(
//                 $v[1],
//                 $g,
//                 "failed : block 1 " . $k
//             );
//         }
//     }

//     public function _test_eval_code_soure()
//     {
//         $src = implode("\n", [
//             // 'if (true){ $x = $g    . "--"; }',
//             // 'if (true){ $x=8; $t->div()->Content = "   $x---llml"; }',
//             // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}---llml"; }',
//             // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}" . $x; }',
//             // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}" . ( 8 + $x + 1); }',
//             // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}"; }'
//             //'if (true){ $x=8; $t->div()->Content = "---{$x->value}". $x; }'
//             // 'if (true){ $x=8; $t->div()->Content = "---{$x->value}". ($x + 8); }' // Unsupported operand types
//             // 'if ($x === (true ||false) ){ $x=8; $t->div()->Content = "---{$x->value}". (8 + $x ); }' // Unsupported operand types
//             'if ($x === (true ||false) ){ $x="jav8"; $t->add($x)->setClass("intro")->Content = "---{$x}". (8 . $x ); }' // Unsupported operand types
//             // ' ?  478 : $y = 88; ',
//             // 'if (true){ $x = $g. "":  ?  478 : $y = 88; ',
//             // '$quota = 999; ',
//             // '$defd = "info ".$x; ',
//             // '$t->div()->Content = "top block 1:{$x}"; ',
//             // '$t->div()->Content = "top block 2:".$x; ',
//             // 'if (8) { $t->div()->Content = "middle"; }  $t->div()->Content = "end block"; }',            
//         ]);
//         //igk_wln($src);
//         $compiler = new BalafonViewCompiler2;
//         $compiler->options = new ViewEnvironmentArgs;
//         $compiler->options->ctrl = new CompileTestController;
//         $compiler->options->ctrl->entryDir = self::$sm_tempdir;
    
//         $g = BalafonViewCompilerUtility::GetInstructionsList($src, true, $compiler);
       
//         $this->assertEquals(
//             implode("\n", [
//                 '<?php',
//                 'if($x === (true ||false) ):',
//                 '$x = \'jav8\';',
//                 '? ><<?= $x ? > class="intro"><?= "---{$x}". (8 . $x) ? ></<?= $x ? >><?php',
//                 'endif; '
//             ]),
//             $compiler->output(),
//             "failed to get empty function list instruct"
//         );
//     }
//     public function _test_func_name_token()
//     {

//         $src = implode("\n", [
//             'if (true){ function myAnonymous(){ echo "sub_if"; } }',
//         ]);
//         $this->assertEquals(
//             json_encode([
//                 (object)["value" =>
//                 'if (true){ function myAnonymous(){ echo "sub_if"; } }'],
//             ]),
//             json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
//             "failed to get empty function list instruct"
//         );
//     } 
// }
