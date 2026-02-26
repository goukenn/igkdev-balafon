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
    * auto generate doc.
    * @var mixed
    */
    var $children;

    /**
    * auto generate doc.
    */
    public function getCanRenderTag()
    {
        return false;
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @param null|mixed $options
    */
    function getRenderedChilds($options = null)
    {
        return $this->children;
    }
}