<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlXmlViewerNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\XML\XmlCDATA;
use IGK\XML\XMLNodeType;

///<summary>Represente class: IGKHtmlXmlViewerItem</summary>
/**
* Represente IGKHtmlXmlViewerItem class
*/
final class HtmlXmlViewerNode extends HtmlNode {
    private $m_cdata;
    ///<summary>.ctr</summary>
    /**
    * contruct xml viewer
    */
    public function __construct(){
        parent::__construct("div");
        $this["class"]="igk-xml-viewer";
        $this->m_cdata = new HtmlCommentNode();

    }
    public function getContent($options = null){ 
        return null;
    }
    /**
     * 
     * @param array|mixed $v 
     * @return $this 
     */
    public function setContent($v){
        $this->m_cdata->Content = $v; 
        return $this;
    }
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="depth"></param>
    /**
    * 
    * @param mixed $target
    * @param mixed $depth
    */
    private function __renderDepth($target, $depth){
        if($depth > 0){
            for($i=0; $i < $depth; $i++){
                $target->add("span")->setClass("t")->addSpace();
            }
        }
    }
    function getRenderedChilds($options = null)
    {
        return [$this->m_cdata];
    }
    function getCanAddChilds()
    {
        return false;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
    * 
    * @param mixed $t
    */
    public function initDemo($t){
        $t->div()->addSectionTitle(5)->Content="Samples ";
        $t->div()->addPhpCode()->Content="\$t->addXmlViewer()->Load('[xml_content]');";
        $this->ClearChilds();
        $this->Load(<<<EOF
<demo attr_1="attrib_definition" >The viewer<i >sample</i></demo>
EOF
        , HtmlContext::XML);
    }
    ///<summary></summary>
    ///<param name="content"></param>
    ///<param name="context" default="XML"></param>
    /**
    * 
    * @param mixed $content
    * @param mixed $context the default value is XML
    */
    public function load($content, $context=HtmlContext::XML, callable $creator=null){
        if(empty($content))
            return;
        
        $c= HtmlReader::Load($content, $context, $creator);
        $root=null;
        foreach($c->Childs as  $v){
            $c=$this->loadItem($v, $this);
            if(!$root && ($v->NodeType == XMLNodeType::ELEMENT)){
                $root=$v;
            }
        }
        $this["igk:loaded"]=1;
    }
    ///<summary></summary>
    ///<param name="r"></param>
    ///<param name="target"></param>
    ///<param name="depth"></param>
    /**
    * 
    * @param mixed $r
    * @param mixed $target
    * @param mixed $depth the default value is 0
    */
    public function loadItem($r, $target, $depth=0){
        $this->__renderDepth($target, $depth);
        $target->add("span")->setClass("s")->Content="&lt;".$r->TagName;
        if($r->HasAttributes){
            foreach($r->Attributes->to_array() as $k=>$v){
                $target->addSpace();
                $target->add("span")->setClass("attr")->Content=$k;
                $target->add("span")->setClass("o")->Content="=";
                $target->add("span")->setClass("attrv")->Content="\"".HtmlUtils::GetValue($v)."\"";
            }
        }
        $s=HtmlUtils::GetValue($r->Content);
        if($r->HasChilds || !empty($s)){
            $target->add("span")->setClass("s")->Content="&gt;";
            if(!empty($s)){
                $target->add("span")->setClass("tx")->Content=$s;
            }
            foreach($r->Childs as $k=>$v){
                $target->addBr();
                switch($v->NodeType){
                    case XMLNodeType::COMMENT:
                    $target->add("span")->setClass("c")->Content="&lt;!--".HtmlUtils::GetValue($v->Content)."--&gt;";
                    break;
                    case XMLNodeType::TEXT:
                    $target->add("span")->setClass("tx")->Content=HtmlUtils::GetValue($v->Content);
                    break;default:
                    $c=$this->loadItem($v, $this, $depth + 1);
                    break;
                }
            }
            if($r->HasChilds){
                $target->addBr();
                $this->__renderDepth($target, $depth);
            }
            $target->add("span")->setClass("e")->Content="&lt;/".$r->TagName."&gt;";
        }
        else{
            $target->add("span")->setClass("s")->Content="/&gt;";
        }
    }

    
}