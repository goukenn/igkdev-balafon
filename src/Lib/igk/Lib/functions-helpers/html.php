<?php

// @author: C.A.D. BONDJE DOUE
// @filename: html.php
// @date: 20220831 14:41:26
// @desc: html helper



///<summary>detect that a node must be an empty node</summary>

use IGK\System\Html\Dom\HtmlOptions;

/**
 * detect that a node must be an empty node
 */
function igk_html_emptynode($n)
{
    if (get_class($n) == \IGK\System\Html\XML\XmlNode::class) {
        return 0;
    }
    return igk_html_emptytag($n->TagName);
}
///<summary>detect that a tag must be an empty tag</summary>
/**
 * detect that a tag must be an empty tag
 */
function igk_html_emptytag($tagname)
{
    return isset(HtmlOptions::$EmptyTag[strtolower($tagname)]);
}
