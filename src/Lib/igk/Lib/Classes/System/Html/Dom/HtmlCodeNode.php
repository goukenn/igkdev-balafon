<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCodeNode.php
// @date: 20220706 16:12:34
// @desc: code node
namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlRenderer;
/**
* Html code node.
* @package IGK\System\Html\Dom
*/
class HtmlCodeNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "code";
    /**
    * Sets Content.
    * @param mixed $v
    */
    public function setContent($v){
        if (is_string($v)){
            $v = self::TreatContent($v);
        }
        $this->content = $v;
        return $this;
    }
    /**
     * treat inner content presentation
     */
    static function TreatContent(string $content){
        // transform outisze string litterl 
        $sb = $content;
        $sb = preg_replace('/&/', '&amp;', $sb);
        $sb = preg_replace('/>/', '&gt;', $sb);
        $sb = preg_replace('/</', '&lt;', $sb);
        return $sb;
    }
    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
    public function getRenderedChilds($options = null)
    {
        $childs = parent::getRenderedChilds($options);
        if ($childs){
            $sb = '';
            foreach($childs as $k){
                $sb.= HtmlRenderer::Render($k, $options); 
            }
            $sb = self::TreatContent($sb); 
            return [$sb];
        } 
    }
}