<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextareaValidator.php
// @date: 20240104 16:24:18
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class TextareaValidator extends DefaultValidator{ 
    public function assertValidate($value): bool { 
        return !empty($value);
    } 
}