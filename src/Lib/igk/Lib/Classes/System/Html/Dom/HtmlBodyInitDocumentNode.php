<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBodyInitDocumentNode.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Dom;

/**
* Html body init document node.
* @package IGK\System\Html\Dom
*/
class HtmlBodyInitDocumentNode extends HtmlNode{
    /**
     * Indicates that this node does not accept child nodes.
     * @return bool
     */
    public function getCanAddChilds()
    {
        return false;
    }
    /**
     * Renders the document initialization script if core script is not disabled.
     * @param mixed $options Render options containing the Document context.
     * @return string|null
     */
    public function render($options=null){
        if (!$options){
            return null;
        }
        $doc = $options->Document;

        if ($doc && !$doc->noCoreScript)
            return  "if(window.ns_igk)ns_igk.init_document();";
    }
}