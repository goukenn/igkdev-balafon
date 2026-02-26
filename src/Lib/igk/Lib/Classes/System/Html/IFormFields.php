<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormFields.php
// @date: 20230929 17:58:32
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
*/
interface IFormFields extends IFormFieldContainer{

    /**
    * auto generate doc.
    * @return ?array
    */
    function getDataSource(): ?array;

    /**
    * auto generate doc.
    * @return ?string
    */
    function getTag(): ?string;

    /**
    * auto generate doc.
    * @return ?object
    */
    function getEngine(): ?object;
}