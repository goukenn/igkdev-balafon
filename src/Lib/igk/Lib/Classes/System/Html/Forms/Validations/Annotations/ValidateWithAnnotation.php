<?php
// @author: C.A.D. BONDJE DOUE
// @file: ValidateWithAttribute.php
// @date: 20240103 16:43:22
namespace IGK\System\Html\Forms\Validations\Annotations;

use IGK\Helper\Activator;
use IGK\System\Annotations\AnnotationBase;
use IGK\System\IAnnotation;
use IGK\System\Annotations\AnnotationInfo;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;

///<summary></summary>
/**
 * Use to validate a property on a fields list
 * @package IGK\System\Html\Forms\Validations\Annotations
 * @author C.A.D. BONDJE DOUE
 * @AnnotationInfo(isMutiple=false, type=property)
 */
class ValidateWithAnnotation extends AnnotationBase implements IAnnotation
{
    private $m_validator;
    /**
     * define max length
     * @var ?int
     */
    var $maxLength;
     /**
     * define max length
     * @var ?int
     */
    var $minLength;
    var $pattern;
    var $type;
     /**
     * field is required
     * @var ?bool
     */
    var $required;


    /**
     * define max length
     * @var ?bool
     */
    var $allowNull;

    /**
     * get the attribute validator
     * @return mixed 
     */
    public function getValidator()
    {
        return $this->m_validator;
    }
    public function setParams(array $params)
    {
        parent::setParams($params);
    }

    public function __construct(?string $validator = null)
    {
        if ($validator) {
            if (class_exists($validator, false) && (is_subclass_of($validator, FormFieldValidatorBase::class))) {
                $this->m_validator = Activator::CreateNewInstance($validator);
            } else {
                $this->m_validator = FormFieldValidatorBase::Factory($validator) ?? igk_die(sprintf('[%s] validator not found', $validator));
            }
        }
    }

    public function setType(?string $type=null){
        $type = $type ?? 'text';
        $this->type = $type;
    }
}
