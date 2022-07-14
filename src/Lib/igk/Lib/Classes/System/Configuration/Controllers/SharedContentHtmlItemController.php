<?php
// @file: IGKSharedContentHtmlItemCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;

use IGK\System\Html\Dom\HtmlSharedContentNode;
use IGKHtmlSharedNotifyDialog;

final class SharedContentHtmlItemController extends BaseController{
    const notifybox="notifybox";
    ///<summary></summary>
    public function __construct(){
        parent::__construct();
    }
    ///<summary></summary>
    public function getEntities(){
        return $this->m_entity;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function getEntity($n){
        $g=igk_getv($this->m_entity, $n);
        if(($g == null) && ($n == self::notifybox)){
            $g=new HtmlSharedContentNode($this);
            $this->regEntity("notifybox", $g);
        }
        return $g;
    }
    ///<summary></summary>
    public function getm_entity(){
        return $this->getEnvParam("entities");
    }
    ///<summary></summary>
    public function getName(){
        return IGK_SHARED_CONTENT_CTRL;
    }
    ///<summary></summary>
    protected function initComplete($context=null){
        parent::initComplete();
    }
    ///<summary></summary>
    protected function initTargetNode(){
        $c=new HtmlSharedContentNode($this);
        return $c;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="node"></param>
    public function regEntity($name, $node){
        $this->m_entity[$name]=$node;
    }
}
