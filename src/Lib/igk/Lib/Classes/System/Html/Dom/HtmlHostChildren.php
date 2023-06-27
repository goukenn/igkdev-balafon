<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlHostChildren.php
// @date: 20230418 15:40:03
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
final class HtmlHostChildren extends HtmlItemBase{
    var $children;
    public function getCanRenderTag()
    {
        return false;
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function __construct(array $children)
    {
        parent::__construct();
        $this->children = $children;
    }
    function getRenderedChilds($options = null)
    {
        return $this->children;
    }

}