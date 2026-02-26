<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConvertTypeValidator.php
// @date: 20231230 08:59:52
namespace IGK\System\Html\Forms\Validations;
/**
* create a validator
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class ConvertTypeValidator extends ConvertTypeValidatorBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_fields;

    /**
    * auto generate doc.
    * @param null|array $fields
    */
    public function setFields(?array $fields){
        $this->m_fields = $fields;
    }

    /**
    * auto generate doc.
    * @return array
    */
    public function getFields(): array {
        return $this->m_fields ?? [];
    }
}