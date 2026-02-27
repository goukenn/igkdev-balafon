<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClassAttributeArrayValueEncoderTest.php
// @date: 20230316 11:30:01
namespace IGK\Tests\System\Html\Encoding;

use IGK\System\Html\Encoding\ClassAttributeArrayValueEncoder;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Encoding
*/
class ClassAttributeArrayValueEncoderTest extends BaseTestCase{

    /**
    * Tests encode litteral.
    */
    public function test_encode_litteral(){
        $e = (new ClassAttributeArrayValueEncoder)->encode("[".htmlentities('"one","two"')."]");
        $this->assertEquals("['one','two']", $e);
        $dec = (array)igk_json_parse($e);
        $this->assertIsArray(
           $dec
        );
    }

    /**
    * Tests encode litteral expression.
    */
    public function test_encode_litteral_expression(){

        $e = (new ClassAttributeArrayValueEncoder)->encode("[".htmlentities('$raw == 1? "one" : 0')."]");
        $this->assertEquals("[\$raw == 1? 'one' : 0]", $e);        
        $raw = 1;
        $g = eval("?><?php return ".$e.";");
        $this->assertEquals(['one'],$g);
    }

    /**
    * Tests encode litteral expression 1.
    */
    public function test_encode_litteral_expression_1(){

        $e = (new ClassAttributeArrayValueEncoder)->encode("[".htmlentities('"one\'" => 0')."]");
        $this->assertEquals("['one\'' => 0]", $e);        
        $raw = 1;
        $g = eval("?><?php return ".$e.";");
        $this->assertEquals(['one\''=>0],$g);
    }
}