<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormValidationNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Forms\Validations;
/**
 * validateion node
 * @package IGK\System\Html\Forms
 */
interface IFormValidationNode{
    /**
    * Validates Request.
    * @param mixed & $outputdata
    * @param mixed & $errors
    */
    public function validateRequest(& $outputdata, & $errors);
}