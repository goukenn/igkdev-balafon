<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ValidationTest.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Tests\System\Html\Forms;

use IGK\Helper\Activator;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\Html\Forms\Validations\ConvertTypeValidator;
use IGK\System\Html\Forms\Validations\ConvertTypeValidatorBase;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;
use IGK\System\Html\Forms\Validations\FormValidation;
use IGK\Tests\BaseTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms
*/
class ValidationTest extends BaseTestCase
{

    /**
    * auto generate doc.
    */
    function test_validation_class_exist()
    {
        $this->assertTrue(class_exists(FormValidation::class));
        $validation = new FormValidation();
    }

    /**
    * auto generate doc.
    */
    function test_empty_validation()
    {
        $this->assertFalse((new FormValidation())->validate([]));
    }

    /**
    * auto generate doc.
    */
    function test_validation_request_with_html_content()
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
        // by default skip null value
        $this->assertEquals([
            "filename" => "/sample&lt;script&gt;alert&lt;/script&gt;",
            // "firstname" => null,
            // "lastname" => null
        ], $validation->validate($request), "html entities stransform");

/*
        $this->assertEquals($validation->fields([
            "x" => ["type" => "int", "default" => 0]
        ])->validate(["x" => "8985bondj"]), [
            "x" => 0,
        ], "html entities stransform");/*

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

        */
    }

    /**
    * auto generate doc.
    */
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

    /**
    * auto generate doc.
    */
    public function test_pattern_validator()
    {
        //custom type validate
        $validation = new FormValidation();
        $validation->storage = false;
        $this->assertEquals(
            false,
            $validation->fields([
                "x" => ["type" => "text", "maxLength" => 4, "default" => null, "error" => "x not defined"]
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

    /**
    * auto generate doc.
    */
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


        // $q = parse_url("https://igkdev.com?f=sample ok");



        $g = $validation->fields([
            "x" => ["type" => "url",  "default" => "https://data.com", "error" => "x not defined"]
        ])->validate(["x" => "https://igkdev.com"]);

        $this->assertEquals(
            ["x" => "https://igkdev.com"],$g,
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

    /**
    * auto generate doc.
    */
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

    /**
    * auto generate doc.
    */
    public function test_file_validation(){
        $validation = new FormValidation();
        $validation->storage = false;
        $r =  $validation->fields([
            "x" => ["type" => "file", "required" => 1, "default" => null, "error" => "x not defined"]
        ])->files(["x" => ["type"=>"text/octet-stream", "name"=>"myfile", "size"=>0, "default" => true]]);
       $r = JSon::Encode($r, JSonEncodeOption::IgnoreEmpty());

        $this->assertEquals(
'{"x":{"name":"myfile","type":"text/octet-stream","size":0}}',
            $r,
            "test file validation "
        );  
    }

    /**
    * auto generate doc.
    */
    public function test_validation_convert_to_type()
    {
        $validation = new FormValidation;
        $validation->storage = false;
        $validator = new DummyConvertValidator;
        $validator->setTargetClass(ValidationConvert::class);
        $g = $validation->fields([
            'dummy'=>['type'=>'object','validator'=>$validator]
        ])->validate([
            'dummy'=>(object)[
                'x'=>12,
                'y'=>100
            ]
        ]);
        $v_r = [
            'dummy'=>Activator::CreateNewInstance( ValidationConvert::class,["x"=>12,"y"=>100])
        ];
        $this->assertEquals($v_r,
            $g
        );
    }

    /**
    * auto generate doc.
    */
    public function test_validation_convert_with_validator()
    {
        $validation = new FormValidation;
        $validation->storage = false;
        $validator = new ValidationConvertValidator; 
        $validator->returnType(ValidationConvert::class);
        $g = $validation->fields([
            'dummy'=>['type'=>'object','validator'=>$validator]
        ])->validate([
            'dummy'=>(object)[
                'x'=>12,
                'y'=>"x100"
            ]
        ]);
        $v_r = [
            'dummy'=>Activator::CreateNewInstance( ValidationConvert::class,["x"=>12,"y"=>100])
        ];
        $this->assertEquals($v_r,
            $g
        );
    }
}

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms
*/
class ValidationConvert{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $x;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $y;
}

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms
*/
class ValidationConvertValidator extends ConvertTypeValidatorBase{

    /**
    * auto generate doc.
    * @return array
    */

    public function getFields():array{
        return [
            'x'=>['type'=>'int', 'required'=>1],
            'y'=>['type'=>'float', 'required'=>1,'default'=>100],
        ];
    } 
}