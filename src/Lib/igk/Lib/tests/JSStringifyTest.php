<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JSStringifyTest.php
// @date: 20220803 13:48:54
// @desc: 
use igk\js\common\JSExpression;
use IGK\Tests\BaseTestCase;

/**
* Jsstringify test.
*/
class JSStringifyTest extends BaseTestCase{
    /**
     * Set up the test environment by loading the JS common module.
     *
     * @return void
     */
    protected function setUp():void{
        parent::setUp();
        igk_require_module(igk\js\common::class);
    }
    /**
     * Test that an array of JS method strings is stringified into object notation.
     *
     * @return void
     */
    public function test_stringify_data(){
        $g = JSExpression::Stringify([
            "info(){}", "data(){ return true; }"], (object)[
                "objectNotation"=>1
            ]
        );
        $this->assertEquals("{info(){}, data(){ return true; }}", $g);
    }
    /**
     * Test that method declaration strings are correctly stringified with mixed key-value entries.
     *
     * @return void
     */
    public function test_method_declaration_expression(){
        $data = [
            "template"=>"the template",
            "beforeRouteEnter(to, from, next){ /*meth one*/}",              
            "beforeRouteLeave(to, from){ /*meth two*/}"  
        ];
        $g = JSExpression::Stringify($data, (object)[
            "objectNotation"=>1
        ]);
        $this->assertEquals('{template:"the template", beforeRouteEnter(to, from, next){ /*meth one*/}, beforeRouteLeave(to, from){ /*meth two*/}}', 
        $g,
        "rule: method definition as key => method_expression"
        );
    }
    /**
    * Tests template no escapse.
    */
    public function test_template_no_escapse(){
        $data = [
            "template"=>"<div>the template</div>", 
        ];
        $g = JSExpression::Stringify($data, (object)[
            "objectNotation"=>1
        ]);
        $this->assertEquals('{template:"<div>the template</div>"}', $g,
        "rule: use expression"
        );
    }
    /**
    * Tests expression use.
    */
    public function test_expression_use(){
        $data = [
             JSExpression::Property("data", [
                "post"=>null,
            ])
        ];
        $g = JSExpression::Stringify($data, (object)[
            "objectNotation"=>1
        ]);
        $this->assertEquals('{data(){return {"post":null};}}', $g,
        "rule: method definition as key => method_expression"
        );
    }
}