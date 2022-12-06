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
    /**
     * html tag that need to be closed with a closed tag
     */
    const HtmlAutoCloseTag = "a|html|body|span|code|ul|li|ol|pre|p|button|videos|audio|select|option|head|script|style|div|form|frame|iframe|nav|tr|td|th|table|textarea|noscript|i|b|u";
    /**
     * html tag that denied a close tag
     */
    const EmptyTags =  "br|input|hr|img|source|link|meta|base|col|embed|param|track|wbr";

    protected function __construct(){        
    }
    public static function GetEmptyTagArray(){
        static $clTag = null;
        if ($clTag === null){
            $clTag = explode("|", self::EmptyTags );
        }
        return $clTag;
    }
    public static function GetCloseTagArray(){
        static $clTag = null;
        if ($clTag === null){
            $clTag = explode("|", self::HtmlAutoCloseTag );
        }
        return $clTag;
    }
}