<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenExpressionConverterTest.php
// @date: 20221021 08:33:14
namespace IGK\Tests\System\Runtime\Compiler;

use IGK\System\Runtime\Compiler\ViewCompiler\ViewTokenExpressionConverter;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Runtime\Compiler
*/
class ViewTokenExpressionConverterTest extends BaseTestCase{
    public function test_convert_affectation(){
        $src = implode("\n",[
            "<?php",
            '$x =     igk_create_node("div");'
        ]); 
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = igk_create_node("div");'
        ]), $g, "failed to convers");
    }
    public function test_convert_affectation_depend_on(){
        $src = implode("\n",[
            "<?php",
            '$x = $y;'
        ]);
        
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = $___IGK_PHP_GETTER_VAR___[\'y\'];'
        ]), $g, "failed to convers");
    }

    public function test_convert_affectation_depend_on_expression(){
        $src = implode("\n",[
            "<?php",
            '$x = $y.    "Hello";'
        ]);
        
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = $___IGK_PHP_EXPRESSION___[igk_express_eval(\'$y. "Hello"\')];'
        ]), $g, "failed to convers");
    }

    public function test_convert_atomic_expresison(){
        $src = implode("\n",[
            "<?php",
            'define("sample", 1);'
        ]); 
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            'define("sample", 1);'
        ]), $g, "failed to convers");
    }


    public function test_convert_atomic_collapse(){
        $src = implode("\n",[
            "<?php",
            '$a = function( $x ){ $x = "data"; ?>Base DE JOUR<?php };'
        ]);
       
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            '$___IGK_PHP_SETTER_VAR___[\'a\'] = $a = function( $x ){ $x = "data"; ?>Base DE JOUR<?php };'
        ]), $g, "failed to convers");
    } 

    public function test_block_if_single(){
        $src = implode("\n",[
            "<?php",
            'if ((true)) $x = 8;'
        ]); 
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            'if ((true)):', // need to be reduced
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = 8;',
            'endif;' 
        ]), $g, "failed to convers");
    } 

    public function test_block_if_multi_1(){
        $src = implode("\n",[
            "<?php",
            'if (true){ $x = 8; }'
        ]);
        
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            'if (true):',
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = 8;',
            'endif;'
            // 'echo "Bonjour"',
            // 'endif:'
        ]), $g, "failed to convers");
    } 
    public function test_block_if_multi_2(){
        $src = implode("\n",[
            "<?php",
            'if (true){ $x = 8; $y = 9; }'
        ]); 
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src);        
        $this->assertEquals(implode("\n",[
            "<?php",
            'if (true):',
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = 8;',
            '$___IGK_PHP_SETTER_VAR___[\'y\'] = $y = 9;',
            'endif;'
            // 'echo "Bonjour"',
            // 'endif:'
        ]), $g, "failed to convers");
    } 

 

    public function test_block_if_multi_3(){
        $src = implode("\n",[
            "<?php",
            'if (true){ $x = 88; if (false) $y = 99; }'
        ]); 
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src); 
        // igk_wln_e(
        //     __FILE__.":".__LINE__, 
        //     $g);
        $this->assertEquals(implode("\n",[
            "<?php",
            'if (true):',
            '$___IGK_PHP_SETTER_VAR___[\'x\'] = $x = 88;',
            'if (false):',
            '$___IGK_PHP_SETTER_VAR___[\'y\'] = $y = 99;',
            'endif;',
            'endif;'
            // 'echo "Bonjour"',
            // 'endif:'
        ]), $g, "failed to convers");
    } 


    public function test_block_if_class_condition(){
        $src = implode("\n",[
            "<?php",
            'if (defined("demo")){ class Demo{} $x = 88; }'
        ]); 
        $converter = new ViewTokenExpressionConverter;
        $g = $converter->convert($src); 
     
        $this->assertEquals(<<<'PHP'
<?php
if (defined("demo")):
$___IGK_PHP_SETTER_VAR___['x'] = $x = 88;
endif;
PHP,
 preg_replace("/^\\t/im","", $g), "failed to convert");
    } 

    // $this->assertEquals(<<<'PHP'
    // <?php
    // if (defined("demo")):
    // if (!class_exists(Demo::class)){
    // ///<summary></summary>
    // /**
    // *
    // */
    // class Demo{
    // }
    // }
    
    // $___IGK_PHP_SETTER_VAR___['x'] = $x = 88;
    // endif;
    // PHP,
    //  preg_replace("/^\\t/im","", $g), "failed to convert");
}