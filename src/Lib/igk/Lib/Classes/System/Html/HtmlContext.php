<?php




// @file: IGKHtmlContext.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

/**
 * represent rendering context constant;
 * @package IGK\System\Html
 */
abstract class HtmlContext{
    const Html="Html";
    const XML="XML";
    const AJX = "ajx";
    const Mail = "mail";
    const HtmlAutoCloseTag = ["img", "hr", "br", "input"];
    protected function __construct(){        
    }
}