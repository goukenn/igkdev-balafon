<?php
namespace IGK\System\Html\Dom;
 
///<summary>Represente class: IGKHtmlAJXReplacementNode</summary>
/**
* Represente IGKHtmlAJXReplacementNode class
*/
final class HtmlAJXReplacementNode extends HtmlNode{
    private $m_nodes;
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("igk:replace-ctrl");
        $this->m_nodes=array();
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="tag" default="null"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $tag the default value is null
    */
    public function addNode($n, $tag=null){
        $this->m_nodes[]=$n;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCanAddChild(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getChildCount(){
        return igk_count($this->m_nodes);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getChilds(){
        return $this->m_nodes;
    }
    ///<summary></summary>
    ///<param name="o" default="null" ref="true"></param>
    /**
    * 
    * @param  * $o the default value is null
    */
    protected function innerHTML(& $o=null){
        $so="";
        foreach($this->m_nodes as  $v){
            if($v->IsVisible)
                $so .= $v->render($o);
        }
        return $so;
    }
}