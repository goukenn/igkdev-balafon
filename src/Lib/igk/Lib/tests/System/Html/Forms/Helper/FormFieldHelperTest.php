<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldHelperTest.php
// @date: 20240910 07:48:08
namespace IGK\Tests\System\Html\Forms\Helper;

use IGK\System\Html\Forms\Helper\FormFieldHelper as HelperFormFieldHelper;
use IGK\System\Html\Forms\Validations\Annotations\FormFieldAnnotation as FormField;
use IGK\System\Html\Forms\Validations\InspectorFormFieldValidationBase;

use IGK\System\Http\Request;
use IGK\Tests\BaseTestCase;


///<summary></summary>
/**
 * 
 * @package IGK\Tests\System\Html\Forms\Helper
 * @author C.A.D. BONDJE DOUE
 */
class FormFieldHelperTest extends BaseTestCase
{
    public function test_formfieldhelper_handleformrequest()
    {
        $count = 0;
        $id1 = igk_get_unique_identifier(3, $list) . str_pad($count++, 2, "0", STR_PAD_LEFT);
        $id2 = igk_get_unique_identifier(3, $list) . str_pad($count, 2, "0", STR_PAD_LEFT);
        $guid = igk_create_guid();
        $v_form_sess_def = (object)[$guid => [
            $id1 => [0, 'title'],
            $id2 => [1, 'desc']
        ]];
        $request = [$id1 => "hello", $id2 => "moto", $guid => 1];

        $v_obj = HelperFormFieldHelper::HandleFormRequest($v_form_sess_def, $request);
        $this->assertTrue(!is_null($v_obj));

        $this->assertEquals('{"title":"hello","desc":"moto"}', json_encode($v_obj));

        $this->assertTrue(empty((array)$v_form_sess_def));
    }
    public function test_formfieldhelper_validate()
    {

        $dummy = new DummyDataForm;
        $request = Request::getInstance();
        $_REQUEST = ["title" => "Book of balafon", "desc" => "wiki", "age"=>18];
        $error = [];
        $c = $dummy->validateFromRequest($request, $error);
        $this->assertTrue($c);

        $error = [];
        $_REQUEST = ["title" => null, "desc" => "wiki", "age"=>12];
        $c = $dummy->validateFromRequest($request, $error);
        $this->assertTrue(!$c); 

        $error = [];
        $_REQUEST = ["title" => "Mr. Paul", "desc" => "wiki", "age"=>"undefined"]; 
        $c = $dummy->validateFromRequest($request, $error); 
        $this->assertTrue(!$c); 
        if ($c){
            $this->assertEquals('', json_encode($dummy));
        }
    }
}



class DummyDataForm extends InspectorFormFieldValidationBase
{
    /**
     * 
     * @var string
     * @FormField(type=text, placeholder="write title",required=true)
     */
    var $title;
    /**
     * 
     * @var ?string
     * @FormField(type=textarea, placeholder="enter description")
     */
    var $desc;

    /**
     * 
     * @var ?int
     * @FormField(type=number, allowNull=true, required=true)
     */
    var $age;
}
