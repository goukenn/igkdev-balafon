<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHtmlAttributeHandler.php
// @date: 20230713 09:38:45
// @desc: attribute value handler
namespace IGK\System\Html;
/**
* Interface for html attribute handler.
* @package IGK\System\Html
*/
interface IHtmlAttributeHandler{
    /**
    * Returns Attribute Value.
    * @param string $attribute_name
    * @return ?string
    */
    function getAttributeValue(string $attribute_name) : ?string;
}