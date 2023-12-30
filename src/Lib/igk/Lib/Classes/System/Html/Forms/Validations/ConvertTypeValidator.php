<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConvertTypeValidator.php
// @date: 20231230 08:59:52
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* create a validator
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class ConvertTypeValidator extends ConvertTypeValidatorBase{
    private $m_fields;
    public function setFields(?array $fields){
        $this->m_fields = $fields;
    }
    public function getFields(): array {
        return $this->m_fields ?? [];
    }

}