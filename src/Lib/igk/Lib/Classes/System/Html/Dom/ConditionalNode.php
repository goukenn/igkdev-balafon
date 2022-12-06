<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConditionalNode.php
// @date: 20221130 13:37:59
namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Dom
 */
class ConditionalNode extends HtmlNode
{
    const LOWER_THAN_IE9 = 'if lt IE 9';
    const LOWER_OR_EQUAL_TO_IE9 = 'if lte IE 9';
    var $tagname = "igk:condition";
    var $condition = "";

    public function getCanRenderTag()
    {
        return false;
    }
    /**
     * render conditional node
     * @param mixed $options 
     * @return string 
     */
    public function render($options = null)
    {
        $sb = new StringBuilder;
        $sb->appendLine(sprintf("<!--[%s]>", $this->condition));
        $childs = $this->getRenderedChilds();
        if ($childs)
            $sb->appendLine(implode("", array_map(function ($a) use ($options) {
                return HtmlRenderer::Render($a, $options);
            }, $childs)));

        $sb->append(sprintf("<![endif]-->"));
        return $sb . '';
    }
}
