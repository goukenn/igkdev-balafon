<?php
// @file: IGKHtmlSharedContentNode.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
/**
* Html shared content node.
* @package IGK\System\Html\Dom
*/
final class HtmlSharedContentNode extends HtmlNode{
    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;
    /**
     * Constructor.
     * @param mixed $ctrl The controller whose visible entities are rendered.
     */
    public function __construct($ctrl){
        parent::__construct("igk-shared-content");
        $this->m_ctrl=$ctrl;
    }
    /**
     * Returns the list of visible entities from the bound controller for rendering.
     * @param mixed $o Render options.
     * @return array
     */
    protected function _getRenderingChildren($o=null){
        $t=array();
        $entities=$this->m_ctrl->getEntities();
        if($entities){
            foreach($entities as $v){
                if($v->IsVisible){
                    $t[]=$v;
                }
            }
        }
        return $t;
    }
    /**
     * Indicates that the tag name should not be rendered.
     * @return bool
     */
    public function getIsRenderTagName(){
        return false;
    }
}