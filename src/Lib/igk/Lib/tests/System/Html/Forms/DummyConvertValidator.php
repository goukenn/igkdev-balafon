<?php

namespace IGK\Tests\System\Html\Forms;

use IGK\Helper\Activator;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;
use IGK\System\Html\Forms\Validations\FormFieldValidatorContainerBase;

class DummyConvertValidator extends FormFieldValidatorContainerBase{
    /**
     * target class name 
     * @var ?string 
     */
    private $m_target;
    protected function _validate($data, $default=null, array &$error=[], ?object $options = null) { 
        if ($this->assertValidate($data)){
            $cl = $this->getTargetClass();
            if ($cl){
                $o = Activator::CreateNewInstance($cl, $data);
                return $o;
            }
        }

    }
    /**
     * expected value 
     * @param mixed $value 
     * @return bool 
     */
    public function assertValidate($value): bool { 
        return is_null($value) || is_object($value) || is_array($value); 
    }
    public function getFields():array{
        return [
            'dummy'=>[]
        ];
    }
    public function getTargetClass(){
        return $this->m_target;
    }
    /**
     * set target class 
     * @param null|string $class_name 
     * @return void 
     */
    public function setTargetClass(?string $class_name){
        $this->m_target = $class_name;
    }
}