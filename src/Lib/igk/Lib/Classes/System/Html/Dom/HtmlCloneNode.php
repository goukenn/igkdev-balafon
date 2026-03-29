<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlCloneNode.php
// @date: 20230329 11:52:57
namespace IGK\System\Html\Dom;
/**
* host for rendering element 
* @package IGK\System\Html\Dom
*/
class HtmlCloneNode extends HtmlNode{
    /**
    * Property: target.
    * @var mixed
    */
    var $target;
    /**
    * Name of tagname.
    * @var mixed
    */
    var $tagname ='igk-clone-node';
    /**
    * Property: children.
    * @var mixed
    */
    var $children= false;
    /**
    * .ctr
    * @param HtmlItemBase $c
    */
    public function __construct(HtmlItemBase $c){
        if ($c instanceof static){
            igk_die("not allowed to clone a clone. ");
        }
        $this->target = $c;
        parent::__construct();
    }
    /**
    * Returns Target Node.
    */
    public function getTargetNode(){
        return $this->target; 
    }
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
    * Sets For Children.
    * @param bool $children
    */
    public function setForChildren(bool $children){
        $this->children = $children; 
        return $this;
    }
    /**
    * Returns For Childrend.
    */
    public function getForChildrend(){
        return $this->children;
    }
    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
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
    /**
    * Returns Can Add Childs.
    */
    public function getCanAddChilds()
    {
        return false;
    }
    /**
    * Returns Is Visible.
    */
    public function getIsVisible()
    {
        return $this->target->getIsVisible();
    }
}