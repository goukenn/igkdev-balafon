<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHtmlResourceData.php
// @date: 20230713 11:45:54
// @desc: resource data
// + | --------------------------------------------------------------------
// + | 
// + |
namespace IGK\System\Html;

/**
 * protocol to handle resource string data
 * @package 
 */
interface IHtmlResourceData{
    /**
    * get string presentation.
    * @return string
    */
    function __toString():string;
}