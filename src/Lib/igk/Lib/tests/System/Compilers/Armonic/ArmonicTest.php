<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArmonicTest.php
// @date: 20221019 16:02:24
// @desc: amonic test file
// @args : phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/tests/System/Compilers/Armonic/ArmonicTest.php

namespace IGK\Test\System\Compilers\Armonic;

use IGK\System\Runtime\Compiler\Armonic\ArmonicCompiler;
use IGK\Tests\BaseTestCase;
use IGKException;

/**
 * @group disabled
 * @package IGK\Test\System\Compilers\Armonic
 */
class ArmonicTest  extends BaseTestCase
{
    public function test_armonic_global_var()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false;
        $g = $armonic->compileSource(<<<'PHP'
<?php
$ab;
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$ab;
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_var_1()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
$a = 15;
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$a = 15;
PHP,
            $g,
            "failed global"
        );
    }

    public function test_armonic_global_var_2()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
$a = 15;
$b = 485;
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$a = 15;
$b = 485;
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_var_3()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
$a = 15, $b = 485;
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$a = 15;
$b = 485;
PHP,
            $g,
            "failed global"
        );
    }

    public function test_armonic_global_var_4()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
$a = "15 - presentation";
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$a = "15 - presentation";
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_dual_var()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false;

        $g = $armonic->compileSource(<<<'PHP'
<?php
$a = "presentation {$y} ; sample";
$b = 13; 
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$a = "presentation {$y} ; sample";
$b = 13;
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_with_expression()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false;         
        $g = $armonic->compileSource(<<<'PHP'
<?php
igk_demo("de presentation et d'action $x de jour"."presentation $y comme de nuit");
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
igk_demo("de presentation et d'action $x de jour" . "presentation $y comme de nuit");
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_with_expression_1()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        
        $g = $armonic->compileSource(<<<'PHP'
<?php
igk_demo("de presentation et d'action {$x->presentation()} de jour", function(){
    $a += $b;
    $c = "presentation $x";
});
PHP);

        $this->assertEquals(
            <<<'PHP'
<?php
igk_demo("de presentation et d'action {$x->presentation()} de jour", function(){
$a += $b;
$c = "presentation $x";
});
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_var_152()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = true;
        $g = $armonic->compileSource(<<<'PHP'
<?php
$a , $c = 15, $b;
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$a, $b, $c = 15;
PHP,
            $g,
            "failed global"
        );
    }


    public function test_armonic_global_var_143()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = true;  
            
        $g = $armonic->compileSource(<<<'PHP'
<?php
igk_expression($x=     456, $d);
PHP); 
        $this->assertEquals(
            <<<'PHP'
<?php
igk_expression($x = 456, $d);
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_var_124()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = true;   
        $g = $armonic->compileSource(<<<'PHP'
<?php
$x->test("la vie $hey");
PHP);
        $this->assertEquals(
            <<<'PHP'
<?php
$x->test("la vie $hey");
PHP,
            $g,
            "failed global"
        );
    }
    public function test_armonic_global_function()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
function A(){
}
PHP);
 

        $this->assertEquals(
            <<<'PHP'
<?php

///<summary></summary>
/**
* 
* @return mixed
*/
function A(){
}
PHP,
            $g,
            "failed global"
        );
    }


    public function test_armonic_global_function_2()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false;    
 
        $g = $armonic->compileSource(<<<'PHP'
<?php
function A($ax , $bx=10, $cx=80){
return $ax + $bx;
}
PHP);
 
        $this->assertEquals(
<<<'PHP'
<?php

///<summary></summary>
/**
* 
* @param mixed $ax
* @param mixed $bx
* @param mixed $cx
* @return mixed
*/
function A($ax, $bx=10, $cx=80){
return $ax + $bx;
}
PHP,
            $g,
            "failed global"
        );
    }



    public function test_armonic_interface_trait_and_class()
    {
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false;
        $this->expectException(IGKException::class);   
   
        $armonic->compileSource(<<<'PHP'
<?php 
interface A{
   var $info; 
}
PHP);
    }

    public function test_armonic_interface_trait_and_class_2()
    {

        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false;
    
        $g = $armonic->compileSource(<<<'PHP'
<?php 
class A{
    var $info;
}
final class B{
    protected $z = 0;
    var $x;
    public $y = 0; 
}
PHP);
// igk_wln_e(__FILE__.":".__LINE__, $g);
        $this->assertEquals(
            <<<'PHP'
<?php

///<summary></summary>
/**
* 
*/
class A{

var $info;

}

///<summary></summary>
/**
* 
*/
final class B{

var $x;
public $y = 0;
protected $z = 0;

}
PHP,
            $g,
            "failed global"
        );
    } 
    public function _test_armonic_interface_trait_and_class_3()
    {
        // TODO : TEST ARMONIC

        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = false; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
class A{
var $info;
function AM(){
// if ($data){
print_r("data");
$x = [12    ,   45   , 85];
// }
return "return - trait - and - class 3";
}
} 
PHP);
// igk_debug_wln_e(__FILE__.":".__LINE__, $g);
    $this->assertEquals(
<<<'PHP'
<?php

///<summary></summary>
/**
* 
*/
class A{

var $info;


///<summary></summary>
/**
* 
* @return mixed
*/
function AM(){
$x = [12, 45, 85];

print_r("data");
return "return - trait - and - class 3";
}
}
PHP,  $g, "not ok");
    
    }


    function test_static_var_in_function(){
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = true;
        $armonic->noComment = true; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
$x = 85;
function VarIn($a){
static $g;
global $x;$a += $x . "presentation";
$a["baseic"]="data";
}
PHP); 
        $this->assertEquals(<<<'PHP'
<?php

function VarIn($a){
static $g;
global $x;

$a += $x . "presentation";
$a["baseic"] = "data";
}


$x = 85;
PHP,
        $g,
        "var in not complete");

    }



    function test_public_function(){
        $armonic = new ArmonicCompiler;
        $armonic->mergeVariable = true;
        $armonic->noComment = true; 
        $g = $armonic->compileSource(<<<'PHP'
<?php
class Job{
    public function doSomething(){
        $data = new Job();
        return $data;
    } 
}
PHP); 

        $this->assertEquals(<<<'PHP'
<?php

class Job{

public function doSomething(){
$data = new Job();
return $data;
}
}
PHP,
        $g,
        "var in not complete");

    }
}
 