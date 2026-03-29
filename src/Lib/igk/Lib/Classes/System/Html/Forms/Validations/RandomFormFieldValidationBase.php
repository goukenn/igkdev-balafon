<?php
// @author: C.A.D. BONDJE DOUE
// @file: RandomFormFieldValidationBase.php
// @date: 20240910 11:54:59
namespace IGK\System\Html\Forms\Validations;
use Error;
use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Forms\Helper\FormFieldHelper;
use IGKException;
use ReflectionException;
/**
* use to build random field validation
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
* - each public field will be use as form field input
* - decorate each field with IGK\System\Html\Forms\Validations\Annotations\FormFieldAnnotation
*/
class RandomFormFieldValidationBase extends InspectorFormFieldValidationBase{
    /**
     * get random fields
     * @param mixed $context 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException 
     */
    // public final function randFields($context=null){
    //     $field = $this->getFields($context);
    //     return FormFieldHelper::FormRandFieldName($field);
    // }
    /** inject the random fields */
    public function getFields($context=null): array{
        $v_fields = parent::getFields($context);
        return FormFieldHelper::FormRandFieldName($v_fields);
    }
    /**
    * Returns Validation Fields.
    */
    protected function getValidationFields(){
        return parent::getFields(__METHOD__);
    }
    /**
     * and randomise session field
     * @param array &$error 
     * @return bool 
     * @throws Exception 
     * @throws IGKException 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function  handleRandSessionRequest(array & $error){
        $obj = FormFieldHelper::HandleSessionRequestArgs();
        return  ($obj && parent::validate((array)$obj, $error));
    }
    /**
     * validate items 
     * @param object|array $data 
     * @param array &$error 
     * @return bool 
     * @throws Exception 
     * @throws IGKException 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function validate($data, ?array &$error = [])
    {
        // merge data with session argument then validate
        $obj = FormFieldHelper::HandleSessionRequestArgs($data); 
        return  ($obj && parent::validate($obj, $error));
    }
}