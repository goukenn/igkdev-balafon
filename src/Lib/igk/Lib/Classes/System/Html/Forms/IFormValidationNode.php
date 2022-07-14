<?php

namespace IGK\System\Html\Forms;

/**
 * validateion node
 * @package IGK\System\Html\Forms
 */
interface IFormValidationNode{
    public function validateRequest(& $outputdata, & $errors);
}