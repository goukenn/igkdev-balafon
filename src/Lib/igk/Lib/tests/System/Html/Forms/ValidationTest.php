<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ValidationTest.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Tests;
 
use IGK\System\Html\Forms\Validations\FormValidation;
 

class ValidationTest extends BaseTestCase
{
    function test_validation_class_exist()
    {
        $this->assertTrue(class_exists(FormValidation::class));
        $validation = new FormValidation();
    }
    function test_empty_validation()
    {
        $this->assertFalse((new FormValidation())->validate([]));
    }
    function test_request()
    {



        $validation = new FormValidation();
        $validation->storage = false;
        $validation
            ->fields([
                "filename" => ["type" => "text", "required" => 1, "error" => "missing text"],
                "firstname" => ["type" => "text",  "error" => "missing firstname"],
                "lastname" => ["type" => "text", "error" => "missing lastname"],
            ]);

        $request = [
            "filanme" => "/sample<script>alert</script>"
        ];
        $tab = $validation->validate($request);
        $error = $validation->getErrors();
        $this->assertEquals($tab, false, implode($validation->getErrors()));

        $request = [
            "filename" => "/sample<script>alert</script>",
        ];
        $this->assertEquals($validation->validate($request), [
            "filename" => "/sample&lt;script&gt;alert&lt;/script&gt;",
            "firstname" => null,
            "lastname" => null
        ], "html entities stransform");


        $this->assertEquals($validation->fields([
            "x" => ["type" => "int", "default" => 0]
        ])->validate(["x" => "8985bondj"]), [
            "x" => 0,
        ], "html entities stransform");

        $this->assertEquals($validation->fields([
            "x" => ["type" => "pattern", "pattern" => "/a[0-9]+/i", "default" => 0]
        ])->validate(["x" => "z8985"]), [
            "x" => 0,
        ], "pattern validation failed");


        $this->assertEquals($validation->fields([
            "x" => ["type" => "pattern", "pattern" => "/a[0-9]+/i", "default" => 0]
        ])->validate(["x" => "a8985"]), [
            "x" => "a8985",
        ], "pattern validation failed");

        $this->assertEquals($validation->fields([
            "x" => ["type" => "array", "default" => []]
        ])->validate(["x" => "a8985"]), [
            "x" => ["a8985"],
        ], "array validation failed");


        // return false default value
        $this->assertEquals($validation->fields([
            "x" => ["type" => "bool", "default" => false]
        ])->validate(["x" => "", "default" => true]), [
            "x" => false,
        ], "bool validation failed");

        // converto bool value if default is null
        $this->assertEquals($validation->fields([
            "x" => ["type" => "bool", "default" => null]
        ])->validate(["x" => "basic", "default" => true]), [
            "x" => true,
        ], "bool validation failed");
    }

    public function test_custom_validator()
    {
        //custom type validate
        $validation = new FormValidation();
        $validation->storage = false;

        // using custom validation registration 
        $validation->registerValidator("custom", function ($value, $default = null) {
            return "handle-custom:" . $value;
        });

        $this->assertEquals($validation->fields([
            "x" => ["type" => "custom", "default" => null]
        ])->validate(["x" => "basic", "default" => true]), [
            "x" => "handle-custom:basic",
        ], "bool validation failed");
    }

    public function _test_password_validator()
    {
        // TODO: test_password_validator
        //custom type validate
        $validation = new FormValidation();
        $validation->storage = false;
        // $g =    $validation->fields([
        //     "x" => ["type" => "password", "default" => null]
        // ])->validate(["x" => "basic", "default" => true]);

        // igk_wln_e($g);

        // $this->assertEquals(
        //     false,
        //     $g ,
        //     "password return value"
        // );

        // $this->assertEquals(
        //     ["x" => "basic@Host123"],
        //     $validation->fields([
        //         "x" => ["type" => "password", "default" => null]
        //     ])->validate(["x" => "basic@Host123", "default" => true]),
        //     "password return value"
        // );
    }

    public function test_pattern_validator()
    {
        //custom type validate
        $validation = new FormValidation();
        $validation->storage = false;
        $this->assertEquals(
            false,
            $validation->fields([
                "x" => ["type" => "text", "maxlength" => 4, "default" => null, "error" => "x not defined"]
            ])->validate(["x" => "basics", "default" => true]),
            "pattern validation "
        );


        $this->assertEquals(
            ["x" => "basi"],
            $validation->fields([
                "x" => ["type" => "text", "maxlength" => 4, "default" => null, "error" => "x not defined"]
            ])->validate(["x" => "basi", "default" => true]),
            "pattern validation "
        );
    }
    public function test_url_validator()
    {
        //custom type validate
        $validation = new FormValidation();
        $validation->storage = false;
        $b = $validation->fields([
            "x" => ["type" => "url",  "required" => 1, "default" => null, "error" => "x not defined"]
        ])->validate(["x" => "basics", "default" => true]);
        $this->assertEquals(
            false,
            $b,
            "url validation: must return false"
        );


        $b = $validation->fields([
            "x" => ["type" => "url", "default" => null, "error" => "x not defined"]
        ])->validate(["x" => "basics", "default" => true]);
        $this->assertEquals(
            ["x" => null],
            $b,
            "url validation: must return an empty not required"
        );


        $q = parse_url("https://igkdev.com?f=sample ok");


        $this->assertEquals(
            ["x" => "https://igkdev.com"],
            $g = $validation->fields([
                "x" => ["type" => "url",  "default" => "https://data.com", "error" => "x not defined"]
            ])->validate(["x" => "https://igkdev.com"]),
            "url validation failed"
        );

        $this->assertEquals(
            ["x" => "https://igkdev.com?version=1.0"],
            $validation->fields([
                "x" => ["type" => "url",  "default" => "https://data.com", "error" => "x not defined"]
            ])->validate(["x" => "https://igkdev.com?version=1.0"]),
            "url validation failed"
        );

        // server pass a query to script and receive a dump data
        $this->assertEquals(            
            ["x" => "https://igkdev.com?version=1.0&data=%3Cscript%3Ealert%28%27ok%27%29%3C%2Fscript%3E"],
            $validation->fields([
                "x" => ["type" => "url",  "default" => "https://data.com", "error" => "x not defined"]
            ])->validate(["x" =>
             "https://igkdev.com?version=1.0&data=<script>alert('ok')</script>"]),
            "url validation failed"
        );
    }

    public function test_json_validator()
    {
        //custom type validate
        $validation = new FormValidation();
        $validation->storage = false;
        $this->assertEquals(
            false,
            $validation->fields([
                "x" => ["type" => "json",  "required" => 1, "default" => null, "error" => "x not defined"]
            ])->validate(["x" => "{basics:'45'}", "default" => true]),
            "json validation: must return false"
        ); 

        $this->assertEquals(
            ["x" => "{\"basics\":\"45\"}"],
            $validation->fields([
                "x" => ["type" => "json",  "required" => 1, "default" => null, "error" => "x not defined"]
            ])->validate(["x" => "{\"basics\":\"45\"}", "default" => true]),
            "json validation: test 1"
        );
    }

    public function test_file_validation(){
        $validation = new FormValidation();
        $validation->storage = false;

        $this->assertEquals(
            ["x"=>["name"=>"myfile", "size"=>0, "default" => true]],
            $validation->fields([
                "x" => ["type" => "file", "required" => 1, "default" => null, "error" => "x not defined"]
            ])->files(["x" => ["name"=>"myfile", "size"=>0, "default" => true]]),
            "test file validation "
        ); 

    }
}
