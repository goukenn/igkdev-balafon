<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlCloneNode.php
// @date: 20230329 11:52:57
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* host for rendering element 
* @package IGK\System\Html\Dom
*/
class HtmlCloneNode extends HtmlNode{
    var $target;
    var $tagname ='igk-clone-node';
    var $children= false;

    public function __construct(HtmlItemBase $c){
        if ($c instanceof static){
            igk_die("not allowed to clone a clone. ");
        }
        $this->target = $c;
        parent::__construct();
    }
    public function getTargetNode(){
        return $this->target; 
    }
    public function getCanRenderTag()
    {
        return false;
    }
    public function setForChildren(bool $children){
        $this->children = $children; 
        return $this;
    }
    public function getForChildrend(){
        return $this->children;
    }
    public function getRenderedChilds($options = null)
    {
        if ($this->children){
            return $this->target->getChilds()->to_array();
        }
        return [$this->target];
    }
    /**
     * passing attribute definition to childs
     * @param mixed $key 
     * @param mixed $value 
     * @return $this 
     */
    public function setAttribute($key, $value)
    {
        $this->target->setAttribute($key, $value);
        return $this;
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function getIsVisible()
    {
        return $this->target->getIsVisible();
    }
}