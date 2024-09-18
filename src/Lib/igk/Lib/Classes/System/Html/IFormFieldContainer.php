<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormFieldContainer.php
// @date: 20231230 10:55:10
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IFormFieldContainer{
     /**
     * retrieve form fields data
     * @return array array of form fields data definition
     */
    function getFields($context=null): array;
}