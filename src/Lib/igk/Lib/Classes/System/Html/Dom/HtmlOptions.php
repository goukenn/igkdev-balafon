<?php
// @file: IGKHtmlOptions.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

abstract class HtmlOptions{
    static $CloseWithCloseTags=array(
            "style"=>"style",
            "label"=>"label",
            "i"=>"i",
            "b"=>"b",
            "canvas"=>"canvas",
            "div"=>"div",
            "iframe"=>"iframe",
            "select"=>"select",
            "abbr",
            "button"=>"button",
            "tr"=>"tr",
            "td"=>"td",
            "a"=>"a",
            "ul"=>"ul",
            "li"=>"li",
            "ol"=>"ol",
            "form"=>"form",
            "script"=>"script",
            "code"=>"code",
            "noscript"=>"noscript",
            "html"=>"html",
            "body"=>"body",
            "head"=>"head",
            "video"=>"video",
            "option"=>"option",
            "object"=>"object",
            "textarea"=>"textarea",
            "quote"=>"quote",
            "p"=>"p",
            "span"=>"span",
            "igk-img"=>"igk-img",
            "igk-anim-img"=>"igk-img",
            "table"=>"table"
        ), $EmptyTag=array(
            "br"=>"br",
            "base"=>"base",
            "link"=>"link",
            "input"=>"input",
            "meta"=>"meta",
            "img"=>"img",
            "source"=>"source",
            "embed"=>"embed"
        );

    public static function IsAllowedAttribute(string $name){
        // ---------------------------------------------------------------------------------
        // ignore igk:param cause have a special meaning for loading template - it will call setParam methoe- will pass data
        // use igk:data to pass data to js 
        // use igk:args to pass data to opener
        // ---------------------------------------------------------------------------------
        if (in_array($name, ["param"])){
            // use set param insteed
            return false;
        }
        return true;
    }
}
