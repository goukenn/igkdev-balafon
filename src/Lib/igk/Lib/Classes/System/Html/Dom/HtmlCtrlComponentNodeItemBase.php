<?php
// @file: IGKHtmlCtrlComponentNodeItemBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use IIGKHtmlComponent;
abstract class HtmlCtrlComponentNodeItemBase extends HtmlCtrlNodeItemBase implements IIGKHtmlComponent{
    public function __construct($tag){
        $this->m_controller=igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
        parent::__construct($tag);
        $this->m_controller->Register($this);
    }
    public function Dispose(){
        $this->free();
    }
    public function free(){
        $this->m_controller->Unregister($this);
    }
    public function getComponentId(){
        return $this->getParam(get_class($this->m_controller).":id");
    }
    public function getComponentUri($uri){
        return ($c=$this->getController()) ? $c->getUri($uri, $this): null;
    }
    public function getController(){
        $c=$this->getParam("Controller");
        return $c;
    }
    public function setComponentListener($listener, $param=null){    }
    protected function setParentNode($n, $context=null){
        if(($n === null) && ($context && (strtolower($context) == 'clearchilds'))){
            $this->free();
        }
        parent::setParentNode($n, $context);
    }
}