<?php

namespace IGK\Tests;

use IGK\Helper\StringUtility;
use IGK\System\Html\Forms\Validation;
use IGK\System\Html\Dom\HtmlDoc;
use IGKHtmlDoc;

class ValidationTest extends BaseTestCase
{
    function test_validation_class_exist()
    {
        $this->assertTrue(class_exists(Validation::class));
        $validation = new Validation();
    }
    function test_empty_validation()
    {
        $this->assertFalse((new Validation())->validate([]));
    }
    function test_request()
    {



        $validation = new Validation();
        $validation
            ->validator([
                "filename" => ["type" => "text", "required" => 1, "error" => "missing text"],
                "firstname" => ["type" => "text",  "error" => "missing firstname"],
                "lastname" => ["type" => "text", "error" => "missing lastname"],
            ]);

        $request = [
            "filanme" => "/sample<script>alert</script>"
        ];
        $this->assertEquals($validation->validate($request), false, implode($validation->getErrors()));

        $request = [
            "filename" => "/sample<script>alert</script>",
        ];
        $this->assertEquals($validation->validate($request), [
            "filename" => "/sample&lt;script&gt;alert&lt;/script&gt;",
            "firstname" => null,
            "lastname" => null
        ], "html entities stransform");


        $this->assertEquals($validation->validator([
            "x" => ["type" => "int", "default" => 0]
        ])->validate(["x" => "8985bondj"]), [
            "x" => 0,
        ], "html entities stransform");

        $this->assertEquals($validation->validator([
            "x" => ["type" => "pattern", "pattern" => "/a[0-9]+/i", "default" => 0]
        ])->validate(["x" => "z8985"]), [
            "x" => 0,
        ], "pattern validation failed");


        $this->assertEquals($validation->validator([
            "x" => ["type" => "pattern", "pattern" => "/a[0-9]+/i", "default" => 0]
        ])->validate(["x" => "a8985"]), [
            "x" => "a8985",
        ], "pattern validation failed");

        $this->assertEquals($validation->validator([
            "x" => ["type" => "array", "default" => []]
        ])->validate(["x" => "a8985"]), [
            "x" => ["a8985"],
        ], "array validation failed");


        // return false default value
        $this->assertEquals($validation->validator([
            "x" => ["type" => "bool", "default" => false]
        ])->validate(["x" => "", "default" => true]), [
            "x" => false,
        ], "bool validation failed");

        // converto bool value if default is null
        $this->assertEquals($validation->validator([
            "x" => ["type" => "bool", "default" => null]
        ])->validate(["x" => "basic", "default" => true]), [
            "x" => true,
        ], "bool validation failed");
    }

    public function test_custom_validator()
    {
        //custom type validate
        $validation = new Validation();
        $validation->registerValidator("custom", function($value, $default=null) {
            return "handle:".$value;
        });

        $this->assertEquals($validation->validator([
            "x" => ["type" => "custom", "default" => null]
        ])->validate(["x" => "basic", "default" => true]), [
            "x" => "handle:basic",
        ], "bool validation failed");
    }

    public function test_password_validator()
    {
        //custom type validate
        $validation = new Validation();        

        $this->assertEquals(false, $validation->validator([
            "x" => ["type" => "password", "default" => null]            
        ])->validate(["x" => "basic", "default" => true]),
         "password return value");

         $this->assertEquals(["x"=>"basic@Host123"], $validation->validator([
            "x" => ["type" => "password", "default" => null]            
        ])->validate(["x" => "basic@Host123", "default" => true]),
         "password return value");
    }

    public function test_pattern_validator()
    {
        //custom type validate
        $validation = new Validation();        

        $this->assertEquals(false, $validation->validator([
            "x" => ["type" => "text", "maxlength"=>4, "default" => null, "error"=>"x not defined"]            
        ])->validate(["x" => "basics", "default" => true]),
         "pattern validation ");


         $this->assertEquals(["x"=>"basi"], $validation->validator([
            "x" => ["type" => "text", "maxlength"=>4, "default" => null, "error"=>"x not defined"]            
        ])->validate(["x" => "basi", "default" => true]),
         "pattern validation ");
    }
    public function test_url_validator()
    {
        //custom type validate
        $validation = new Validation();        

        $b = $validation->validator([
            "x" => ["type" => "url",  "required"=>1, "default" => null, "error"=>"x not defined"]            
        ])->validate(["x" => "basics", "default" => true]); 
        $this->assertEquals(false, $b,
         "url validation: must return false");


         $b = $validation->validator([
            "x" => ["type" => "url", "default" => null, "error"=>"x not defined"]            
        ])->validate(["x" => "basics", "default" => true]); 
        $this->assertEquals(["x"=>null], $b,
         "url validation: must return an empty not required");


         $q = parse_url("https://igkdev.com?f=sample ok");
 

         $this->assertEquals(["x"=>"https://igkdev.com"], $g = $validation->validator([
            "x" => ["type" => "url",  "default" => "https://data.com", "error"=>"x not defined"]            
        ])->validate(["x" => "https://igkdev.com"]),
         "url validation failed");

         $this->assertEquals(["x"=>"https://igkdev.com?version=1.0"], $validation->validator([
            "x" => ["type" => "url",  "default" => "https://data.com", "error"=>"x not defined"]            
        ])->validate(["x" => "https://igkdev.com?version=1.0"]),
         "url validation failed");

         $this->assertEquals(["x"=>"https://igkdev.com?version=1.0&data=%26lt%3Bscript%26gt%3Balert%28%26%23039%3Bok%26%23039%3B%29%26lt%3B%2Fscript%26gt%3B"], $validation->validator([
            "x" => ["type" => "url",  "default" => "https://data.com", "error"=>"x not defined"]            
        ])->validate(["x" => "https://igkdev.com?version=1.0&data=<script>alert('ok')</script>"]),
         "url validation failed"); 

    }
}
