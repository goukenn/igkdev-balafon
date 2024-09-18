<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidationInfoTrait.php
// @date: 20240910 10:23:19
namespace IGK\System\Html\Forms\Validations\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations\Traits
* @author C.A.D. BONDJE DOUE
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
     * 
     * @var ?bool
     */
    var $allowEmpty = false; 

}