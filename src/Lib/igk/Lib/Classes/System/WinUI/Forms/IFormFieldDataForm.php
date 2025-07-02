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
interface IFormFieldDataForm extends IFormFieldContainer{
    /** 
     * @param mixed $context 
     * @return mixed 
     */
    function getFields($context=null) : array;
}