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
* auto generate doc.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class FormValidationTest extends BaseTestCase{
    /**
    * Tests form validation.
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
    * Tests form complex validation.
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
* Balafon object validator.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class BalafonObjectValidator extends FormFieldValidatorContainerBase{
    /**
    * Validate.
    * @param mixed $data
    * @param null|mixed $default
    * @param array & $error
    * @param null|object $options
    */
    protected function _validate($data, $default=null, array &$error=[], ?object $options = null) { 
        if ($this->assertValidate($data)){
            return $data;
        }
    }
    /**
    * Asserts Validate.
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
    * Returns Fields.
    * @return array
    */
    public function getFields():array{  
        $r = new BalafonScriptDefinitionForm;
        return $r->getFields();
    }
}
/**
* Balafon script definition form.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class BalafonScriptDefinitionForm extends InspectorFormFieldValidationBase{
    /**
    * Property: version.
    * @var mixed
    */
    var $version;
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Returns Fields.
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
* Object inspector.
* @package IGK\Tests\System\Html\Forms\Validations
*/
class ObjectInspector{
    /**
    * Property: source.
    * @var mixed
    */
    var $source;
    /**
    * Validates.
    * @param mixed $data
    * @param mixed & $error
    */
    public function validate($data, & $error=[]){
        $g = new BalafonScriptDefinitionForm;
        return $g->validate($data, $error);
    }
}