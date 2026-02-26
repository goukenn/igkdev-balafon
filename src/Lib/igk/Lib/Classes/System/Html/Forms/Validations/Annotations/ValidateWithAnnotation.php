<?php
// @author: C.A.D. BONDJE DOUE
// @file: ValidateWithAttribute.php
// @date: 20240103 16:43:22
namespace IGK\System\Html\Forms\Validations\Annotations;
use IGK\Helper\Activator;
use IGK\System\Annotations\AnnotationBase;
use IGK\System\IAnnotation;
use IGK\System\Annotations\AnnotationInfoAnnotation as Info;
use IGK\System\Html\Forms\Validations\FormFieldValidatorBase;
/**
 * Use to validate a property on a fields list
 * @package IGK\System\Html\Forms\Validations\Annotations
 * @author C.A.D. BONDJE DOUE
 * @Info(isMutiple=false, type=property)
 */
class ValidateWithAnnotation extends AnnotationBase implements IAnnotation
{

    /**
    * Property: validator.
    * @var mixed
    */
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
    /**
     * 
     * @var mixed
     */
    var $pattern;
    /**
     * 
     * @var string
     */
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
     * file max size
     * @var ?string
     */
    var $maxSize;
    /**
     * file accept
     * @var ?string
     */
    var $accept;
    /**
     * multiple value
     * @var ?bool
     */
    var $multiple;
    /**
     * get the attribute validator
     * @return mixed 
     */

    public function getValidator()
    {
        return $this->m_validator;
    }

    /**
    * Sets Params.
    * @param array $params
    */
    public function setParams(array $params)
    {
        parent::setParams($params);
    }

    /**
    * .ctr
    * @param null|string $validator
    */
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

    /**
    * Sets Type.
    * @param null|string $type
    */
    public function setType(?string $type=null){
        $type = $type ?? 'text';
        $this->type = $type;
    }
}