<?php
// @file: IGKSharedContentHtmlItemCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\System\Html\Dom\HtmlSharedContentNode;
use IGKHtmlSharedNotifyDialog;
final class SharedContentHtmlItemController extends BaseController{
    const notifybox="notifybox";
    public function __construct(){
        parent::__construct();
    }
    public function getEntities(){
        return $this->m_entity;
    }
    public function getEntity($n){
        $g=igk_getv($this->m_entity, $n);
        if(($g == null) && ($n == self::notifybox)){
            $g=new HtmlSharedContentNode($this);
            $this->regEntity("notifybox", $g);
        }
        return $g;
    }
    public function getm_entity(){
        return $this->getEnvParam("entities");
    }
    public function getName(){
        return IGK_SHARED_CONTENT_CTRL;
    }
    protected function initComplete($context=null){
        parent::initComplete();
    }
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $c=new HtmlSharedContentNode($this);
        return $c;
    }
    public function regEntity($name, $node){
        $this->m_entity[$name]=$node;
    }
}