<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormFieldDataForm.php
// @date: 20240909 09:34:19
namespace IGK\System\WinUI\Forms;
use IGK\System\Html\IFormFieldContainer;
/**
* 
* @package IGK\System\WinUI\Forms
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\WinUI\Forms
*/
interface IFormFieldDataForm extends IFormFieldContainer{

    /**
    * auto generate doc.
    * @param mixed $context
    * @return mixed
    */
    function getFields($context=null) : array;
}