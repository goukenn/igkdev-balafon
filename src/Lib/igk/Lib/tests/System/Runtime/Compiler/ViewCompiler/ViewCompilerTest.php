<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerTest.php
// @date: 20221025 11:26:36
// @cmd: phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/tests/System/Runtime/Compiler/ViewCompiler/ViewCompilerTest.php
namespace IGK\Tests\System\Runtime\Compiler\ViewCompiler;

use IGK\Controllers\SysDbController;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompiler;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Runtime\Compiler\ViewCompiler
*/
class ViewCompilerTest extends BaseTestCase{
    private function _source(...$args){
        $t = array_merge(["<?php"],  $args);
        return implode("\n", $t);
    }
    public function test_expression_1(){

        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('$a->div()->Content = "OK";'));
      
        $this->assertEquals(
            $this->_source("\$___IGK_PHP_SETTER_VAR___['a']->div()->Content = \"OK\";"),
            $src,
            "failed to get data"
        );
    }

    public function test_expression_2(){

        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('$a->div()->Content = "OK".$x;'));
        $this->assertEquals(
            $this->_source('$___IGK_PHP_SETTER_VAR___[\'a\']->div()->Content = $___IGK_PHP_GETTER_VAR___[igk_express_eval(\'"OK" . $x\',["x"])];'),
            $src,
            "failed to get data"
        );
    }

    public function test_expression_3(){

        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('$a->div()->Content = ["one", $y];'));
        $this->assertEquals(
            $this->_source('$___IGK_PHP_SETTER_VAR___[\'a\']->div()->Content = $___IGK_PHP_GETTER_VAR___[igk_express_eval(\'["one", $y]\',["y"])];'),
            $src,
            "failed to get data"
        );
    }
    public function test_expression_4(){
        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('$a->div()->Content = (function(){ $x   = 8; return 9; })($y);'));
        $this->assertEquals(
            $this->_source('$___IGK_PHP_SETTER_VAR___[\'a\']->div()->Content = $___IGK_PHP_GETTER_VAR___[igk_express_eval(\'(function(){ $x = 8; return 9; })($y)\',["y"])];'),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_expression_5(){
        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('$a->div()->Content = (function(){ $x   = 8; return 9; })() + $b;'));
        $this->assertEquals(
            $this->_source('$___IGK_PHP_SETTER_VAR___[\'a\']->div()->Content = $___IGK_PHP_GETTER_VAR___[igk_express_eval(\'(function(){ $x = 8; return 9; })() + $b\',["b"])];'),
            $src,
            "failed to get data: ".__METHOD__
        );
    }

    public function test_expression_6(){
        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('$invoke($x);'));
        $this->assertEquals(
            $this->_source('$___IGK_PHP_SETTER_VAR___[\'invoke\']($___IGK_PHP_GETTER_VAR___[\'x\']);'),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_expression_1(){
        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('if (true) return 1;'));
        $this->assertEquals(
            $this->_source('if (true):', '    return 1;', 'endif;'),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_expression_do(){
        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('do return 1; while(true);'));
        $this->assertEquals(
            $this->_source('do{', '    return 1;', '}', 'while (true);'),
            $src,
            "failed to get data: ".__METHOD__
        );
    }

    public function test_block_expression_if_litteral(){
        $compiler = new ViewCompiler;
        $compiler->variables = [];
        $src = $compiler->compileSource($this->_source('if (true): return 1; endif;'));
        $this->assertEquals(
            $this->_source('if (true):', '    return 1;', 'endif;'),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_build_cache_litteral(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = true;
        $src = $compiler->compileSource($this->_source(
            '$a = igk_create_node("div");',
            '$a["class"] = "presentation";',
            '$a->ul()->li()->Content = "Present";',
            '$t->div()->add($a);'
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source( 
                '?><div%__igk_attribute__%><?php',
                '?><div><div class="presentation"><ul><li>Present</li></ul></div></div><?php',
                '?></div>'
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_build_cache_condition(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = true;
        $src = $compiler->compileSource($this->_source(
            'if (false){',
            '    $y = 128;',
            '    $t->add("item-".$y)->Content = "Sample".$y;',
            '}'
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source( 
                '?><div%__igk_attribute__%><?php',
                'if (false):',
                '$y = 128;',
                '?><item-<?= $y ?>><?= "Sample" . $y ?></item-<?= $y ?>><?php',
                'endif;',
                '?></div>'
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_build_cache_condition_2(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = true;

        $src = $compiler->compileSource($this->_source(
            'if (false){',
            '    $y = (object)["name"=>"condition-2"];',
            '    $t->add("item-".$y->name)->Content = "Sample".$y->name;',
            '}'
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source( 
                '?><div%__igk_attribute__%><?php',
                'if (false):',
                '$y = (object)["name"=>"condition-2"];',
                '?><item-<?= $y->name ?>><?= "Sample" . $y->name ?></item-<?= $y->name ?>><?php',
                'endif;',
                '?></div>'
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function _st_block_loop_template(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = true; 
        $src = $compiler->compileSource($this->_source(
            'if (false){',
            '    $t->div()->loop(3)->div()->Content = " welcome {{ \$raw }} ";',
            '}'
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source( 
                '?><div%__igk_attribute__%><?php',
                'if (false):', 
                '?><div><div> welcome 0 </div><div> welcome 1 </div><div> welcome 2 </div></div><?php',
                'endif;',
                '?></div>'
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }


    public function test_block_build_cache_condition_3(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = false;
        $src = $compiler->compileSource($this->_source(
            'if (function_exists("igk_google_addfont")){',
            '    igk_google_addfont($doc, "Roboto");',
            '    igk_google_addfont($doc, "Anton");' ,
            '}'
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source(                  
                'if (function_exists("igk_google_addfont")):',
                '    igk_google_addfont($___IGK_PHP_GETTER_VAR___[\'doc\'], "Roboto");',
                '    igk_google_addfont($___IGK_PHP_GETTER_VAR___[\'doc\'], "Anton");',
                'endif;'
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_build_cache_condition_4(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = false;
        $src = $compiler->compileSource($this->_source(
            '($cl = igk_create_node("div")) && $cl->clear();', 
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source(                  
                '($___IGK_PHP_SETTER_VAR___[\'cl\'] = $cl = igk_create_node("div")) && $___IGK_PHP_SETTER_VAR___[\'cl\']->clear();',                
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
    public function test_block_build_cache_condition_5(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ];
        $compiler->forCache = false;
        $src = $compiler->compileSource($this->_source(
            '($cl);', 
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source(                  
                '($___IGK_PHP_SETTER_VAR___[\'cl\']);',                
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }

    public function test_comment_specifics(){
        $compiler = new ViewCompiler;
        $compiler->variables = [           
        ];
        $compiler->options = (object)[
            "t"=>igk_create_node("div"),
            "ctrl"=>SysDbController::ctrl()
        ]; 
        $compiler->forCache = false;
        $src = $compiler->compileSource($this->_source(
            '// + | ----',                
            '// + | demo',                
            '// + | ',            
            'echo "Hello";',      
        ));
        //igk_wln_e("the source ", $src);
        $this->assertEquals(
            $this->_source(                  
                '// + | ----',                
                '// + | demo',                
                '// + |',                
                '',                
                'echo "Hello";',                
            ),
            $src,
            "failed to get data: ".__METHOD__
        );
    }
}