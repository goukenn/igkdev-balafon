<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldAnnotation.php
// @date: 20240103 19:08:45
namespace IGK\System\Html\Forms\Validations\Annotations;

use Exception;
use IGK\System\Html\Forms\IFormInternalIDSupport;
use IGK\System\Html\Validations\IFormFieldValidationStoreError;
use IGKException;

///<summary></summary>
/**
* use to annotate a fields 
* @package IGK\System\Html\Forms\Validations\Annotation
* @author C.A.D. BONDJE DOUE
*/
class FormFieldAnnotation extends ValidateWithAnnotation implements IFormInternalIDSupport, IFormFieldValidationStoreError, IFormFieldFile{
    /**
     * 
     * @var mixed
     */
    var $id;

    var $placeholder;

    var $label_text;

    var $default;

    /**
     * data used to store 
     * @var mixed
     */
    var $data;

    /**
     * allow null value
     * @var ?bool 
     */
    var $allowNull;

    /**
     * internal identification in case of use in form builder
     * @var ?string
     */
    private $m_internal_id;

    private $m_validation_error;

    
    /**
     * store valiateion error 
     * @param mixed $error 
     * @return void 
     */
    public function setError($error):void{
        $this->m_validation_error = $error;
    }
    public function getError(){
        return $this->m_validation_error;
    }
    

    /**
     * set internal identification
     * @return null|string 
     */
    public function getInternalId(){
        return $this->m_internal_id;
    }
    /**
     * get internal identification 
     * @param mixed $v 
     * @return void 
     */
    public function setInternalId($v){
        $this->m_internal_id = $v;
    }
    /**
     * 
     * @param null|string $value 
     * @return void 
     */
    public function setLabel_Text(?string $value){
        $this->label_text = $value;
    }

    /**
     * 
     * @param mixed $reader 
     * @param mixed &$contentTab 
     * @return void 
     */
    public static function BeforeCreateInstance($reader, & $contentTab){
        $tab = explode('|', 'allowNull|required|allowEmpty');
        foreach($tab as $k){
            if (key_exists($k, $contentTab)){
                $contentTab[$k] = igk_bool_val($contentTab[$k]);
            }
        } 
    }
    /**
     * .ctr
     * @param null|string $validator 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     */
    public function __construct(?string $validator=null)
    {
        parent::__construct($validator);
    }
}