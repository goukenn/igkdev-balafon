<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormFields.php
// @date: 20230929 17:58:32
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
interface IFormFields extends IFormFieldContainer{
   

    function getDataSource(): ?array;

    function getTag(): ?string;

    function getEngine(): ?object;

}