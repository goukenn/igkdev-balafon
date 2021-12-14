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

class HtmlOptions{
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
}
