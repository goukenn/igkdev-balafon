<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20220422 09:31:53
// @desc: hosting document


namespace IGK\System\Html\Dom;

use IGK\System\Html\Css\CssUtils;

/**
 * for rendering inline css tempory file
 * @package IGK\System\Html\Dom
 */
class HtmlDocumentCssHostNode extends HtmlNode{
    protected $doc;
    public function __construct($doc){
        $this->doc = $doc;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function render($options = null)
    {
        if ($s = CssUtils::GetInlineStyleRendering($this->doc)){
            return $s;
        }
    }

   
}