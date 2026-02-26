<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormValidationTest.php
// @date: 20231229 08:48:28
// @test: phpunit -c phpunit.xml.dist ./src/application/Lib/igk/Lib/Tests/System/Html/Forms/Validations/FormValidationTest.php
namespace IGK\Tests\System\Html\Forms\Validations;

use IGK\System\Html\Forms\Validations\AssocArrayValidator;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;
use IGK\System\Html\Forms\Validations\FormFieldValidatorContainerBase;
use IGK\System\Html\Forms\Validations\FormValidation;
use IGK\System\Html\Forms\Validations\InspectorFormFieldValidationBase;
use IGK\System\Html\Forms\Validations\JsonValidator;
use IGK\Tests\BaseTestCase;
use Symfony\Component\Form\Extension\Validator\Constraints\FormValidator;

/**
* 
* @package IGK\Tests\System\Html\Forms\Validations
*/
class FormValidationTest extends BaseTestCase{

    /**
    * auto generate doc.
    */
    public function test_form_validation(){
        $d = ['name'=>'Hello'];

        
        $fvalidator = new FormValidation;
        $fvalidator->storage = false;
        $data = (object)[
            'name'=>'demo',
            'scripts'=>[
                'bootstrap'=>'^1.0',                
            ]
        ];
        $g = $fvalidator->fields([            
            'name'=>['type'=>'text', 'required'=>true],
            'scripts'=>[
                'type'=>'object',
                'validator'=>new AssocArrayValidator
            ]
        ]
        )->validate(
            (array)$data
        );


        $this->assertEquals(            
            (object)$data,
            (object)$g
        );
    }

    /**
    * auto generate doc.
    */
    public function test_form_complex_validation(){
        $d = ['name'=>'Hello'];

        
        $fvalidator = new FormValidation;
        $fvalidator->storage = false;
        $data = (object)[
            'name'=>'demo',
            'scripts'=>[
                'bootstrap'=>'^1.0',                
            ],
            'balafon-test'=>[
                'version'=>'1.0',
                'name'=>null,
                'author'=>'C.A.D BONDJE'
            ]
        ];
        $fvalidator->skipNullValue = false;
        $g = $fvalidator->fields([            
            'name'=>['type'=>'text', 'required'=>true],
            'scripts'=>[
                'type'=>'object',
                'validator'=>new AssocArrayValidator
            ],
            'balafon-test'=>[
                'type'=>'object',
                'validator'=>new BalafonObjectValidator
            ]
        ])->validate(
            (array)$data
        );


        $this->assertEquals(            
            (object)$data,
            (object)$g
        );
    }
}

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class BalafonObjectValidator extends FormFieldValidatorContainerBase{

    /**
    * auto generate doc.
    * @param mixed $data
    * @param null|mixed $default
    * @param array & $error
    * @param null|object $options
    */
    protected function _validate($data, $default=null, array &$error=[], ?object $options = null) { 
        // parent::_validate()
        if ($this->assertValidate($data)){
            return $data;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool { 
        $inspector = new ObjectInspector;
        $inspector->source = BalafonScriptDefinitionForm::class;

        if ($r = $inspector->validate($value, $error)){

            return true;
        }
        return false;
    }

    /**
    * auto generate doc.
    * @return array
    */
    public function getFields():array{  
        $r = new BalafonScriptDefinitionForm;
        return $r->getFields();
    }
}

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class BalafonScriptDefinitionForm extends InspectorFormFieldValidationBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $version;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @param null|mixed $context
    * @return array
    */
    public function getFields($context=null): array {
        return [
            'version'=>['validator'=>'StrictVersion'],
            'name'=>['type'=>'string', 'required'=>true],
            'author'=>['type'=>'string', 'required'=>true]
        ];
    }
}

/**
* auto generate doc.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class ObjectInspector{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $source;

    /**
    * auto generate doc.
    * @param mixed $data
    * @param mixed & $error
    */
    public function validate($data, & $error=[]){
        $g = new BalafonScriptDefinitionForm;
        return $g->validate($data, $error);
    }
}