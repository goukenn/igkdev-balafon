<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormFields.php
// @date: 20230929 17:58:32
namespace IGK\System\Html;

/**
* auto generate doc.
* @package IGK\System\Html
*/
interface IFormFields extends IFormFieldContainer{
    /**
    * Returns Data Source.
    * @return ?array
    */
    function getDataSource(): ?array;
    /**
    * Returns Tag.
    * @return ?string
    */
    function getTag(): ?string;
    /**
    * Returns Engine.
    * @return ?object
    */
    function getEngine(): ?object;
}