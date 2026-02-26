<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlHostChildren.php
// @date: 20230418 15:40:03
namespace IGK\System\Html\Dom;
/**
* 
* @package IGK\System\Html\Dom
*/
final class HtmlHostChildren extends HtmlItemBase{

    /**
    * Property: children.
    * @var mixed
    */
    var $children;

    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }

    /**
    * Returns Can Add Childs.
    */
    public function getCanAddChilds()
    {
        return false;
    }

    /**
    * .ctr
    * @param array $children
    */
    public function __construct(array $children)
    {
        parent::__construct();
        $this->children = $children;
    }

    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
    function getRenderedChilds($options = null)
    {
        return $this->children;
    }
}