<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCompileTest.php
// @date: 20220830 17:44:36
// @desc: 
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/tests/System/Compilers/BalafonCompileTest.php
namespace IGK\Tests\System\Compilers;

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
class BalafonCompileTest extends BaseTestCase
{

    static $sm_tempdir;
    public static function tearDownAfterClass(): void
    {
        IO::RmDir(self::$sm_tempdir);
    }
    public static function setUpBeforeClass(): void
    {
        $sdir = sys_get_temp_dir() . "/testCompiler";
        IO::CreateDir($sdir);
        self::$sm_tempdir = $sdir;
    }

    public function test_insert_string()
    {
        $g = StringUtility::Insert("data", "BB", 2);
        $this->assertEquals(
            "daBBta",
            $g,
        );
    }
    public function test_get_instructions(){
        $src = implode("\n", [
            '$x = 8;',
            '$y = 9;'
        ]);
        $this->assertEquals(
            json_encode([
                (object)["value"=>'$x = 8;'],
                (object)["value"=>'$y = 9;']
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src)),
            "failed to get instruction list;"
        );
    }
    public function test_get_class_instructions(){
        $src = implode("\n", [
            'class A(){ }',
            '$y = 9;'
        ]);
        $this->assertFalse(
            BalafonViewCompilerUtility::GetInstructionsList($src),
            "failed to get instruction list;"
        );
    }
    public function test_get_class_func_instructions(){
        $src = implode("\n", [
            '$x = function(){ };',            
        ]);
        $this->assertEquals(
            json_encode([
                (object)["value"=>'$x = function(){ };'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src)),
            "failed to get empty function list instruct"
        );
    }
    public function test_get_class_func_instructions_2(){
        $src = implode("\n", [
            '$x = function(){ $o= 8; };',            
        ]);
        $this->assertEquals(
            json_encode([
                (object)["value"=>'$x = function(){ $o= 8; };'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src)),
            "failed to get empty function list instruct"
        );
    }
    public function test_get_class_func_instructions_3(){
        // with try catch
        $src = implode("\n", [
            '$x = function(){ try{ $o= 8; } catch(\Exception $ex){ echo "error"; } finally {  echo "falback"; } };',            
        ]); 
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                '$x = function(){ try{ $o= 8; } catch(\Exception $ex){ echo "error"; } finally {  echo "falback"; } };'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src)),
            "failed to get empty function list instruct"
        );
    }
    public function test_get_class_func_instructions_4(){
        // with try catch at root 
        $src = implode("\n", [
            'try{ $o= 8; } catch(\Exception $ex){ echo "error"; } finally {  echo "falback"; }   ',            
        ]); 
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'try{ $o= 8; } catch(\Exception $ex){ echo "error"; } finally {  echo "falback"; }'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src)),
            "failed to get empty function list instruct"
        );
    }

    public function test_get_func_instructions_at_sub(){
        // with try catch at root 
        $src = implode("\n", [
            'function(){ echo "sub"; }',            
        ]); 
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'function(){ echo "sub"; };'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }
    public function test_get_func_instructions_at_sub_if(){
        // with try catch at root 
        $src = implode("\n", [
            'if (true){ function(){ echo "sub_if"; } }',            
        ]); 
        $this->assertEquals(
            json_encode([
                (object)["value"=>
                'if (true){ function(){ echo "sub_if"; } }'], 
            ]), json_encode(BalafonViewCompilerUtility::GetInstructionsList($src, false)),
            "failed to get empty function list instruct"
        );
    }
    public function _test_compile_in_string_expression()
    {
        $src = <<<'PHP'
<?php
$x = 180;
$t->div()->Content = "Hello : {$x}";
PHP;
// igk_debug(true);
        $compiler_result = $this->_get_compiler_result($src);
        $this->assertEquals(
            <<<'PHP'
<?php
$x = 180;
<div><div>Hello : {$___IGK_PHP_EXPRESS_VAR___('x')}</div></div>
PHP,
            $compiler_result,
            "failed to compile"
        );
    }
    /**
     * test replace at offset
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_replace_with_offset()
    {
        $g = StringUtility::ReplaceAtOffset("Hello Friend", "BB", 2, 2);
        $this->assertEquals(
            "HeBBo Friend",
            $g,
        );
    }

    /**
     * test to know if element modify in context
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function test_detect_node_modification()
    {
        $mod = new CompilerNodeModifyDetector;
        $this->assertFalse(
            $mod->getModify(),
            "node modify"
        );

        $mod = new CompilerNodeModifyDetector;
        $mod->Content = "estimation";
        $this->assertTrue(
            $mod->getModify(),
            "Content not modify"
        );


        $mod = new CompilerNodeModifyDetector;
        $mod->add('div');
        $this->assertTrue(
            $mod->getModify(),
            "node not modify"
        );

        $mod = new CompilerNodeModifyDetector;
        $n = igk_create_node("div");
        $this->assertFalse(
            $mod->getModify(),
            "create node outside and not used"
        );


        $mod = new CompilerNodeModifyDetector;
        $n = igk_create_node("div");
        $mod->add($n);
        $this->assertTrue(
            $mod->getModify(),
            "attached must modify node"
        );


        $mod = new CompilerNodeModifyDetector;
        $mod["class"] = "test";
        $this->assertTrue(
            $mod->getModify(),
            "attribute modify"
        );
        // by default will detect environment node creation 
        CompilerNodeModifyDetector::Init();
        igk_create_node("div");
        $this->assertTrue(
            CompilerNodeModifyDetector::SysModify(),
            "attribute modify"
        );
        CompilerNodeModifyDetector::UnInit();
    }

    public function test_BalafonViewCompileInstruction()
    {

        $n = new BalafonViewCompileInstruction;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$x = 100;"]
        ];
        $tab =  ["x" => null];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "\$x = 100;\n",
            $result
        );
    }
    public function test_BalafonViewCompileInstruction_2()
    {

        $n = new BalafonViewCompileInstruction;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$t->div()->Content = 'information';"]
        ];
        $tab =  ["x" => null, "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "?><div><div>information</div></div><?php\n",
            $result
        );
    }

    public function test_BalafonViewCompileInstruction_x()
    {

        $n = new BalafonViewCompileInstruction;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$t->div()->Content = 'information:'. igk_express_var('x')"]
        ];
        $tab =  ["x" => 8, "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "?><div><div>information:8</div></div><?php\n",
            $result
        );
    }

    public function test_BalafonViewCompileInstruction_x_2()
    {

        $n = new BalafonViewCompileInstruction;
        $n->extract = true;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$t->div()->Content = 'information:'. igk_express_var('x');"]
        ];
        $tab =  ["x" => 8, "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "?><div><div>information:<?= \$x ?></div></div><?php\n",
            $result
        );
    }
    /**
     * extract with argument call info
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_BalafonViewCompileInstruction_x_3()
    {

        $n = new BalafonViewCompileInstruction;
        $n->extract = true;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$t->div()->Content = 'information:'. igk_express_var('x')->info;"]
        ];
        $tab =  ["x" => (object)["info" => 88], "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "?><div><div>information:<?= \$x->info ?></div></div><?php\n",
            $result
        );
    }

    public function test_BalafonViewCompileInstruction_x_4()
    {

        $n = new BalafonViewCompileInstruction;
        $n->extract = true;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$t->div()->Content = 'information:'. igk_express_var('x')->call(\"base\") .'88';"]
        ];
        $tab =  ["x" => (object)["info" => 88], "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "?><div><div>information:<?= \$x->call('base') ?>88</div></div><?php\n",
            $result
        );
    }

    public function test_BalafonViewCompileInstruction_inside_string_x()
    {

        $n = new BalafonViewCompileInstruction;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$t->div()->Content = \"information: {\$___IGK_PHP_EXPRESS_VAR___('x')}\";"]
        ];
        $tab =  ["x" => 8, "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "?><div><div>information: 8</div></div><?php\n",
            $result
        );
    }
    public function test_BalafonViewCompileInstruction_eval_html()
    {

        $n = new BalafonViewCompileInstruction;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$m = 'presentation' ?>The html content<?php ;"]
        ];
        $tab =  ["x" => 8, "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        $this->assertEquals(
            "\$m = 'presentation' ?>The html content<?php ;\n",
            $result
        );
    }

    public function test_BalafonViewCompileInstruction_eval_html_2()
    {

        $n = new BalafonViewCompileInstruction;
        $ctrl = new CompileTestController;
        $n->instructions = [
            (object)["value" => "\$m = '8';"],
            (object)["value" => "\$t->div()->Content = 'ok';"]
        ];
        $tab =  ["x" => 8, "t" => igk_create_node("div")];
        $n->controller = $ctrl;
        $n->variables = &$tab;
        $result = $n->compile();
        // igk_wln_e("result", $result);
        $this->assertEquals(
            "\$m = '8';\n" .
                "?><div><div>ok</div></div><?php\n",
            $result
        );
    }


    public function test_detect_eval_block_modification()
    {
        $src = <<<'PHP'
    $n = igk_create_node("div");
    $n->Content= "presentation";
PHP;

        CompilerNodeModifyDetector::Init();
        $t = new CompilerNodeModifyDetector();
        ob_start();
        eval("?><?php" . $src);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(
            CompilerNodeModifyDetector::SysModify(),
            "block not raise the modification"
        );
        CompilerNodeModifyDetector::UnInit();
    }
    public function test_check_block_modification()
    {
        $src = <<<'PHP'
    $n = igk_create_node("div");
    $n->Content= "presentation";
PHP;
        $this->assertTrue(
            BalafonViewCompilerUtility::CheckBlockModify($src),
            "block not raise the modification"
        );
    }

    public function test_add_condition_block()
    {
        $c = new ConditionBlockNode;
        $c->type = "if";
        $c->condition = "(true)";
        $c->add("li")->Content = "Ok";
        $n = igk_create_node("div");
        $n->add($c);
        $this->assertEquals(
            <<<'PHP'
<div><?php if (true): ?><li>Ok</li><?php endif; ?></div>
PHP,
            $n->render(),
            "failed"
        );
    }
    public function _test_read_node()
    {
        $src = <<<'PHP'
<?php
$t->div()->Content = 'Hello';
PHP;
        $compiler_result = $this->_get_compiler_result($src);
        $this->assertEquals(
            <<<'PHP'
<div><div>Hello</div></div>
PHP,
            $compiler_result,
            "failed to compile"
        );
    }
    public function _test_read_condition_add_node()
    {
        $src = <<<'PHP'
<?php
if (8 == $params[0]){
    $t->div()->Content = 'Hello';
}
PHP;
        $compiler_result = $this->_get_compiler_result($src, [10]);
        $g = igk_create_node("div");
        $g->text($compiler_result);
        // igk_wln_e("result :::", $g->render());
        $this->assertEquals(
            <<<'PHP'
<div><?php
if (8 == $params[0]): 
?><div>Hello</div><?php
endif; ?></div>
PHP,
            $g->render(),
            "failed to compile"
        );
    }

    public function _test_read_condition_add_node_2()
    {
        $src = <<<'PHP'
<?php
if (8 == $params[0]){
    $x = 110;
    $t->div()->Content = 'Hello :'.$x;
}
PHP;
        $compiler_result = $this->_get_compiler_result($src, [9]);
        $g = igk_create_node("div");
        $g->text($compiler_result);
        $rep = $g->render();
        // igk_wln_e(" result: ", $compiler_result, $rep);

        $this->assertEquals(
            <<<'PHP'
<div><?php
if (8 == $params[0]): 
$x = 110;
?><div>Hello :<?= $x ?></div><?php
endif; ?></div>
PHP,
            $rep,
            "failed to compile"
        );
    }


    private function _get_compiler_result($src, $params = null): string
    {
        $ctrl = new CompileTestController();
        $ctrl->entryDir = self::$sm_tempdir;


        $args = new ViewEnvironmentArgs;
        $args->ctrl = $ctrl;
        $args->params = $params;
        $args->t = new HtmlNode("div");
        $args->doc = new HtmlDocumentNode();

        $opt = new BalafonViewCompilerOptions;
        $opt->layout = new PageLayout;
        $opt->layout->viewDir = self::$sm_tempdir;
        $opt->controller = $ctrl;
        $opt->view_args = $args;

         
        if ($data = BalafonViewCompiler::CompileSource($src, $opt)){
        // $buffer = BalafonViewCompiler::EvaluateCompiledSource($data->source, $ctrl, $args, $data->readOptions->detectVariables);
        $data->buffer = ""; //$buffer;
        // igk_wln_e("src:", $src, "data source: ", $data->source, "buffer:", $buffer);
        // $sb = new StringBuilder;
        // $sb->appendLine("<?php");
        // $sb->append("? >".$args->t->render());

        return $data->source;
        }
        return $data;
    }



    //     public function _test_read_if_block()
    //     {
    //         $src = <<<'PHPM'
    // <?php
    //   if ( true ) $t->Content = '1'; elseif (true) $data=88; else $t['class'] = "{22";
    // PHPM;
    //         $tokens = \token_get_all($src);
    //         $read_block_info = null;
    //         $buffers = "";
    //         $depth = 0;
    //         $block_list = [];
    //         while (count($tokens) > 0) {
    //             $tk = array_shift($tokens);
    //             $id = null;
    //             $value = $tk;
    //             if (is_array($tk)) {
    //                 $id = $tk[0];
    //                 $value = $tk[1];
    //             }

    //             igk_wl("\nTOKEN: " . ($id ? token_name($id) : "") . " \tValue:" . $value);

    //             if ($read_block_info) {
    //                 $read_block_info->value .= $value;
    //                 if ($read_block_info->conditionRead) {
    //                     $read_block_info->condition .= $value;
    //                 } else if (!$read_block_info->block) {
    //                     $read_block_info->instruct .= $value;
    //                 }
    //                 switch ($value) {
    //                     case "{":
    //                         $read_block_info->block = true;
    //                         $depth++;
    //                         break;
    //                     case "}":
    //                         $depth--;
    //                         if ($read_block_info->depth == $depth) {
    //                             $read_block_info = null;
    //                         }
    //                         break;
    //                     case ";":
    //                         if (!$read_block_info->block) {
    //                             // single instruction                             
    //                             $read_block_info = null;
    //                         }
    //                         break;
    //                     case "(":
    //                         if ($read_block_info->conditionRead) {
    //                             $read_block_info->conditionDepth++;
    //                         }
    //                         break;
    //                     case ")";
    //                         if ($read_block_info->conditionRead) {
    //                             $read_block_info->conditionDepth--;
    //                             if ($read_block_info->conditionDepth == -1) {
    //                                 $read_block_info->conditionRead = false;
    //                             }
    //                         }
    //                         break;
    //                 }
    //                 continue;
    //             }

    //             switch ($id) {
    //                 case T_IF:
    //                 case T_ELSE:
    //                 case T_ELSEIF:
    //                 case T_WHILE:
    //                 case T_DO:
    //                 case T_FOREACH:
    //                 case T_SWITCH:
    //                     $read_block_info = (object)[
    //                         "type" => $value,
    //                         "value" => $value,
    //                         "depth" => $depth,
    //                         "block" => false,
    //                         "instruct" => "",
    //                         "conditionRead" => in_array($value, explode("|", "if|elseif|while|switch")),
    //                         "condition" => "",
    //                         "conditionDepth" => -1,
    //                         "childs" => null
    //                     ];
    //                     $block_list[] = $read_block_info;
    //                     $buffers = &$read_block_info->value;
    //                     if (($id == T_ELSE) || ($id == T_ELSEIF)) {
    //                         array_pop($block_list);
    //                         $c = count($block_list);
    //                         if ($c == 0) {
    //                             igk_die("not allowed index");
    //                         }
    //                         if (!$block_list[$c - 1]->childs)
    //                             $block_list[$c - 1]->childs = [];
    //                         if ($block_list[$c - 1]->type != "if") {
    //                             igk_die("not a valid childs");
    //                         }
    //                         $block_list[$c - 1]->childs[] = $read_block_info;
    //                     }
    //                     break;
    //                 default:
    //                     switch ($value) {
    //                         case "{":
    //                             $depth++;
    //                             break;
    //                         case "}":
    //                             $depth--;
    //                             break;
    //                     }
    //                     break;
    //             }
    //         }

    //         $t = $block_list[0];
    //         if ($t->block) {
    //             $t->instruct = trim(substr($t->value, strpos($t->value, "{") + 1, -1));
    //         } else {
    //             $t->instruct = trim($t->instruct);
    //         }
    //         igk_wln_e("\n\n**************done ", $read_block_info, $block_list[0]);
    //         $this->fail("readblock");
    //     }
}

