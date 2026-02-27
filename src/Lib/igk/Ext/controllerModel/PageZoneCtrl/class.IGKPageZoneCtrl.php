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

    /**
    * Property: view zone.
    * @var mixed
    */
    private $m_viewZone;

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    protected function _showViewFile(){
        parent::_showViewFile();
    }

    /**
    * auto generate doc.
    */
    public static function GetAdditionalConfigInfo(){
        return null;
    }

    /**
    * auto generate doc.
    */
    public function getCanAddChild(){
        return true;
    }

    /**
    * auto generate doc.
    */
    public function getName(): string{
        return get_class($this);
    }

    /**
    * auto generate doc.
    */
    public function getViewZone(){
        return $this->m_viewZone;
    }

    /**
    * auto generate doc.
    */
    protected function initComplete($context=null){
        parent::initComplete();
    }
    //@@@ init target node

    /**
    * auto generate doc.
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $node=parent::initTargetNode();
        $node["class"]="alignc alignt dispb";
        $this->m_viewZone=$node->div();
        $this->m_viewZone["class"]="page_zone";
        return $node;
    }
    
} 