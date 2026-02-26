<?php
// @file: IGKHtmlCtrlNodeItemBase.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

/**
* Html ctrl node item base.
* @package IGK\System\Html\Dom
*/
abstract class HtmlCtrlNodeItemBase extends HtmlWebComponentNode{

    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;
    /**
     * Constructor.
     * @param string $tag The HTML tag name for this node.
     */

    public function __construct($tag){
        parent::__construct($tag);
    }
    /**
     * Returns the controller instance associated with this node.
     * @return mixed
     */

    public function getCtrl(){
        return igk_getctrl($this->m_ctrl);
    }
    /**
     * Sets the controller reference for this node.
     * @param mixed $v The controller identifier or instance to associate.
     * @return static
     */

    public function setCtrl($v){
        $this->m_ctrl=$v;
        return $this;
    }
}
