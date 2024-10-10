<?php
// @author: C.A.D. BONDJE DOUE
// @file: InspectorFormFieldTest.php
// @date: 20240923 10:15:28
namespace IGK\Tests\System\Html\Forms\Validations;

use IGK\System\Html\Forms\Validations\InspectorFormFieldValidationBase;
use IGK\Tests\BaseTestCase;
use IGK\System\Html\Forms\Validations\Annotations\FormFieldAnnotation as FormField;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class InspectorFormFieldTest extends BaseTestCase{
    public function test_inspectorformfield_test_list(){
        $r = new DummyValidator; 
        $this->assertTrue(1== $r->validate(["x"=>45, "y"=>88, "z"=>0])); 
        $this->assertTrue($r->x == 45); 
    }
    public function test_inspectorformfield_number(){
        $r = new DummyNumberValidator; 
        $g = $r->getFields();

        $this->assertTrue(1== $r->validate(["x"=>"45.0"])); 
        $this->assertFalse($r->x === "45"); 
        $this->assertTrue($r->x === 45); 
    }
    public function test_inspectorformfield_required(){
        $r = new DummyRequiredValidator; 
        $gt = $r->getFields();
        $g = $r->validate(["y"=>"45.0"]);

        $this->assertTrue(false === $g, 'required field not check.');  
    }
}

class DummyValidator extends InspectorFormFieldValidationBase{
    var $x;
    var $y;
}

class DummyNumberValidator extends InspectorFormFieldValidationBase{
    /**
     * 
     * @var int
     */
    var $x;
    /**
     * 
     * @var string
     * @FormField(int)
     */
    var $y;
}

class DummyRequiredValidator extends InspectorFormFieldValidationBase{
    /**
     * @FormField(string, required=true)
     */
    var $x;
}



//use IGK\System\Html\Forms\Validations\Annotations\FormFieldAnnotation as FormField;