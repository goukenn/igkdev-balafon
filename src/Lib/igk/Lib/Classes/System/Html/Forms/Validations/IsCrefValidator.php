<?php
// @author: C.A.D. BONDJE DOUE
// @file: IsCrefValidator.php
// @date: 20230427 11:00:32
namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\IFormFieldOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms
*/
class IsCrefValidator extends FormFieldValidatorBase{

    public function assertValidate($value): bool {
        $cref = igk_app()->getSession()->getCref();
        if ($cref == $value){
            return $cref;
        }
        return false;
    }

    protected function _validate($value, $default=null,  & $error=[], ?object $options=null){    
        if ($this->assertValidate($value)){
            return $value;
        }
        $error[] = 'not a valid cref';
        return false;
    }

}