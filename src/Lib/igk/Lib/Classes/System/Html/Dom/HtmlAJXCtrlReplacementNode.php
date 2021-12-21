<?php
namespace IGK\System\Html\Dom;
 

///</summary>used in ajx context. Replace controller view</summary>
/**
 * used in ajx context. Replace controller view
 */
final class HtmlAJXCtrlReplacementNode extends HtmlNode {
    private $m_ctrls;
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("igk:replace-ctrl-2");
        $this->m_ctrls=array();
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    /**
    * 
    * @param mixed $option the default value is null
    */
    protected function __getRenderingChildren($option=null){
        $tab=array();
        foreach($this->m_ctrls as  $v){
            $t=$v->target ?? $v->ctrl->TargetNode;
            if($t->IsVisible){
                $tab[]=$t;
            }
        }
        return $tab;
    }
    ///<summary></summary>
    ///<param name="b"></param>
    ///<param name="target" default="null"></param>
    /**
    * 
    * @param mixed $b
    * @param mixed $target the default value is null
    */
    public function addCtrl($b, $target=null){
        $this->m_ctrls[$b->Name]=(object)["ctrl"=>$b, "target"=>$target];
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCanAddChild(){
        return false;
    }
    ///<summary></summary>
    ///<param name="o" default="null" ref="true"></param>
    /**
    * 
    * @param  * $o the default value is null
    */
    protected function innerHTML(& $o=null){
        $so="";
        foreach($this->m_ctrls as  $v){
            $t=$v->target ?? $v->ctrl->TargetNode;
            if($t->IsVisible){
                $so .= $t->render($o);
            }
        }
        return $so;
    }
}