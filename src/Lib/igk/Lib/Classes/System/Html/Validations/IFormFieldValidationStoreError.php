<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormFieldValidationStoreError.php
// @date: 20241108 16:28:08
namespace IGK\System\Html\Validations;

/**
* 
* @package IGK\System\Html\Validations
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Html\Validations
*/
interface IFormFieldValidationStoreError{
    /**
     * set error 
     * @param string|array|null $error 
     * @return void 
     */
    function setError($error):void;
    /**
    * auto generate doc.
    * @return mixed
    */
    function getError();
}