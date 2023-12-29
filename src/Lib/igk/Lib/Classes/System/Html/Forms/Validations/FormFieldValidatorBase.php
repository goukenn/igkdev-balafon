<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidatorBase.php
// @date: 20230427 10:47:00
namespace IGK\System\Html\Forms\Validations;
 

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms
*/
abstract class FormFieldValidatorBase implements IFormValidator{
    protected $_allowNull = false;
    protected $_required = false;

    /**
     * allow null value
     * @param bool $value 
     * @return $this 
     */
    public function allowNull(bool $value){
        $this->_allowNull = $value;
        return $this;
    }
    /**
     * set required
     * @param bool $value 
     * @return $this 
     */
    public function require(bool $value){
        $this->_required = $value;
        return $this;
    }
    public function isRequire(): bool
    {
        return $this->_required;
    }
    /**
     * init field info
     * @return ?FieldInfo 
     */
    protected function _initFieldRequirement(){        
    }

}