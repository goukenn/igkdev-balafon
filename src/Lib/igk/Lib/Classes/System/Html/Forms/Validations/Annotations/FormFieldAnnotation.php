<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldAnnotation.php
// @date: 20240103 19:08:45
namespace IGK\System\Html\Forms\Validations\Annotations;

use IGK\System\Html\Forms\IFormInternalIDSupport;

///<summary></summary>
/**
* use to annotate a fields 
* @package IGK\System\Html\Forms\Validations\Annotation
* @author C.A.D. BONDJE DOUE
*/
class FormFieldAnnotation extends ValidateWithAnnotation implements IFormInternalIDSupport{
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
    public function setLabel_Text(?string $value){
        $this->label_text = $value;
    }

    public static function BeforeCreateInstance($reader, & $contentTab){
        $tab = explode('|', 'allowNull|required|allowEmpty');
        foreach($tab as $k){
            if (key_exists($k, $contentTab)){
                $contentTab[$k] = igk_bool_val($contentTab[$k]);
            }
        } 
    }
    public function __construct(?string $validator=null)
    {
        parent::__construct($validator);
    }
}