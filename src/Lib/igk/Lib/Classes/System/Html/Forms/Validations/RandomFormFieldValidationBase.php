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

///<summary></summary>
/**
* use to build random field validation
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class RandomFormFieldValidationBase extends InspectorFormFieldValidationBase{

    /**
     * get random fields
     * @param mixed $context 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException 
     */
    public final function randFields($context=null){
        $field = $this->getFields($context);
        return FormFieldHelper::FormRandFieldName($field);
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
        return  ($obj && $this->validate((array)$obj, $error));
    }
}