<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAJXReplacementNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

/**
* Represent IGKHtmlAJXReplacementNode class
*/
final class HtmlAJXReplacementNode extends HtmlNode{
    /**
    * Property: nodes.
    * @var mixed
    */
    private $m_nodes;
    /**
    * auto generate doc.
    */
    public function __construct(){
        parent::__construct("igk:replace-ctrl");
        $this->m_nodes=array();
        $this["type"] = "node";
    }
    /**
    * auto generate doc.
    * @param mixed $tag the default value is null
    */
    public function addNode($n, $tag=null){
        $this->m_nodes[]=$n;
        return $this;
    }
    /**
    * auto generate doc.
    */
    public function getChildCount(){
        return igk_count($this->m_nodes);
    }
    /**
    * auto generate doc.
    */
    public function getChilds(){
        return $this->m_nodes;
    }
    /**
    * auto generate doc.
    * @param * $o the default value is null
    */
    protected function innerHTML(& $o=null){
        $so="";
        foreach($this->m_nodes as  $v){
            if($v->IsVisible)
                $so .= $v->render($o);
        }
        return $so;
    }
    /**
    * Get rendering children.
    * @param null|mixed $options
    */
    protected function _getRenderingChildren($options = null)
    {
        return $this->m_nodes;
    }
}