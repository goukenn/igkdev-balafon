<?php

use igk\js\VueJS\IO\JSExpression;
use igk\js\VueJS\Route;
use IGK\Tests\BaseTestCase;

class JSStringifyTest extends BaseTestCase{
    protected function setUp():void{
        parent::setUp();
        igk_require_module(igk\js\VueJS::class);
    }
    public function test_stringify_data(){
        $g = JSExpression::Stringify([
            "info(){}", "data(){ return true; }"], (object)[
                "objectNotation"=>1
            ]
        );
        $this->assertEquals("{info(){}, data(){ return true; }}", $g);
    }
    public function test_method_declaration_expression(){
        $data = [
            "template"=>"the template",
            // component GUARD
            "beforeRouteEnter(to, from, next)"=>"{ /*meth one*/} ",              
            "beforeRouteLeave(to, from)"=>"{ /*meth two*/}"  
        ];
        $g = JSExpression::Stringify($data, (object)[
            "objectNotation"=>1
        ]);
        $this->assertEquals('{"template":"the template", beforeRouteEnter(to, from, next){ /*meth one*/}, beforeRouteLeave(to, from){ /*meth two*/}}', $g,
        "rule: method definition as key => method_expression"
        );
    }

    public function test_template_no_escapse(){
        $data = [
            "template"=>"<div>the template</div>", 
        ];
        $g = JSExpression::Stringify($data, (object)[
            "objectNotation"=>1
        ]);
        $this->assertEquals('{"template":"<div>the template</div>"}', $g,
        "rule: use expression"
        );
    }
    public function test_expression_use(){
        /// unique name definie the proprieries
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

    public function test_express_route(){
        $route = new Route(["path"=>'/home', 
                "name"=>"home", 
                "beforeEnter"=>"console.debug('enter');",  
                "component"=>[
                "template"=>"<div> The home page</div>"
            ]]);

            $this->assertEquals('{"name":"home", "path":"/home", "component":{"template":"<div> The home page</div>"}, "beforeEnter":"console.debug(\'enter\');"}', 
                $route->stringify(),
            "Epress route data ");

        $g = JSExpression::Stringify([
            "route"=>$route
        ], (object)[
            "objectNotation"=>1
        ]);
        $this->assertEquals('{"route":{"name":"home", "path":"/home", "component":{"template":"<div> The home page</div>"}, "beforeEnter":"console.debug(\'enter\');"}}', 
            $g,
        "by function ");            
    }
}