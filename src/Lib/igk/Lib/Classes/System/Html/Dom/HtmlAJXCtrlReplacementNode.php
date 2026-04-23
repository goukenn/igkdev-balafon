<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAJXCtrlReplacementNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

/**
 * used in ajx context. Replace controller view
 */
final class HtmlAJXCtrlReplacementNode extends HtmlNode {
    /**
    * Property: ctrls.
    * @var mixed
    */
    private $m_ctrls;
    /**
    * auto generate doc.
    */
    public function __construct(){
        parent::__construct("igk:replace-ctrl");
        $this["type"] = "controlller";
        $this->m_ctrls=array();
    }
    /**
    * auto generate doc.
    * @param mixed $option the default value is null
    */
    protected function _getRenderingChildren($option=null){
        $tab=array();
        foreach($this->m_ctrls as  $v){
            $t=$v->target ?? $v->ctrl->TargetNode;
            if($t->IsVisible){
                $tab[]=$t;
            }
        }
        return $tab;
    }
    /**
    * auto generate doc.
    * @param mixed $target the default value is null
    */
    public function addCtrl($b, $target=null){
        $this->m_ctrls[$b->Name]=(object)["ctrl"=>$b, "target"=>$target];
    }
    /**
    * auto generate doc.
    */
    public function getCanAddChild(){
        return false;
    }
    /**
    * auto generate doc.
    * @param * $o the default value is null
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