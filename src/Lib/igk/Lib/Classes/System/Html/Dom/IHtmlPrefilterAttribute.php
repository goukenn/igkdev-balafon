<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlPrefilterAttribute.php
// @date: 20221107 17:55:41
namespace IGK\System\Html\Dom;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
interface IHtmlPrefilterAttribute{
    /**
    * Filters.
    * @param mixed $attrib
    */
    function filter($attrib);
}