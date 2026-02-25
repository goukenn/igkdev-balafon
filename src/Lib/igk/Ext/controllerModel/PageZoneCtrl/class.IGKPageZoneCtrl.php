<?php
// @file: class.IGKPageZoneCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Represent IGKPageZoneCtrl class
*/
abstract class IGKPageZoneCtrl extends \IGK\Controllers\ControllerTypeBase {
    private $m_viewZone;
    /**
    * 
    * @param mixed $targetnode the default value is null
    */
    protected function _showChild($targetnode=null){
        $t=$targetnode ? $targetnode: $this->TargetNode;
        $t->add($this->m_viewZone);
        if($this->hasChild){
            foreach($this->getChilds() as  $v){
                if($v->getIsVisible()){                    
                    $this->m_viewZone->add($v->getTargetNode());
                    $v->View();
                }
                else{
                    $v->getTargetNode()->remove();
                }
            }
        }
    }
    /**
    * 
    */
    protected function _showViewFile(){
        parent::_showViewFile();
    }
    /**
    * 
    */
    public static function GetAdditionalConfigInfo(){
        return null;
    }
    /**
    * 
    */
    public function getCanAddChild(){
        return true;
    }
    /**
    * 
    */
    public function getName(): string{
        return get_class($this);
    }
    /**
    * 
    */
    public function getViewZone(){
        return $this->m_viewZone;
    }
    /**
    * 
    */
    protected function initComplete($context=null){
        parent::initComplete();
    }
    //@@@ init target node
    /**
    * 
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $node=parent::initTargetNode();
        $node["class"]="alignc alignt dispb";
        $this->m_viewZone=$node->div();
        $this->m_viewZone["class"]="page_zone";
        return $node;
    }
    
} 