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
    public function validateRequest(& $outputdata, & $errors);
}