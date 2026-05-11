<?php
// @author: C.A.D. BONDJE DOUE
// @filename: html.php
// @date: 20220831 14:41:26
// @desc: html helper
use IGK\System\Html\Dom\HtmlOptions;
/**
* detect that a node must be an empty node
* @param mixed $n
* @return mixed
*/
function igk_html_emptynode($n)
{
    if (get_class($n) == \IGK\System\Html\XML\XmlNode::class) {
        return 0;
    }
    return igk_html_emptytag($n->TagName);
}
/**
* detect that a tag must be an empty tag
* @param mixed $tagname
* @return mixed
*/
function igk_html_emptytag($tagname)
{
    return isset(HtmlOptions::$EmptyTag[strtolower($tagname)]);
}