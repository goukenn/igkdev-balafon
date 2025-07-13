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
abstract class HtmlCtrlNodeItemBase extends HtmlWebComponentNode{
    private $m_ctrl;
    public function __construct($tag){
        parent::__construct($tag);
    }
    public function getCtrl(){
        return igk_getctrl($this->m_ctrl);
    }
    public function setCtrl($v){
        $this->m_ctrl=$v;
        return $this;
    }
}