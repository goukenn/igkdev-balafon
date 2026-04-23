<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidationInfoTrait.php
// @date: 20240910 10:23:19
namespace IGK\System\Html\Forms\Validations\Traits;

/**
* 
* @package IGK\System\Html\Forms\Validations\Traits
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations\Traits
*/
trait FormFieldValidationInfoTrait{
 /**
     * is required
     * @var ?bool
     */
    var $required = false; 
    /**
     * the default value
     * @var mixed
     */
    var $default;
    /**
     * allow null value
     * @var ?bool
     */
    var $allowNull = false;
    /**
    * auto generate doc.
    * @var ?bool
    */
    var $allowEmpty = false; 
}