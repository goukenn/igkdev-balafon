<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHtmlAttributeHandler.php
// @date: 20230713 09:38:45
// @desc: attribute value handler

namespace IGK\System\Html;
interface IHtmlAttributeHandler{
    function getAttributeValue(string $attribute_name) : ?string;
}