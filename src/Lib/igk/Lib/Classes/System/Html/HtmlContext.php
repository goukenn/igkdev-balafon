<?php
// @file: IGKHtmlContext.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
/**
 * represent rendering context constant;
 * @package IGK\System\Html
 */
abstract class HtmlContext extends HtmlRenderingContext{ 
    /**
     * html tag that need to be closed with a closed tag
     */
    const HtmlAutoCloseTag = "a|html|body|title|span|code|ul|li|ol|pre|p|button|video|audio|select|option|head|script|style|div|form|header|main|footer|frame|iframe|nav|tr|td|th|table|textarea|noscript|i|b|u|h1|h2|h3|h4|h5|h6";
    /**
     * html tag that denied a close tag
     */
    const EmptyTags =  "br|hr|img|input|source|link|meta|base|col|embed|param|track|wbr";

    /**
    * .ctr
    */
    protected function __construct(){        
    }
    /**
     * empty tag list 
     * @return string[] 
     */

    public static function GetEmptyTagArray(){
        static $clTag = null;
        if ($clTag === null){
            $clTag = explode("|", self::EmptyTags );
        }
        return $clTag;
    }

    /**
    * auto generate doc.
    */
    public static function GetCloseTagArray(){
        static $clTag = null;
        if ($clTag === null){
            $clTag = explode("|", self::HtmlAutoCloseTag );
        }
        return $clTag;
    }
}