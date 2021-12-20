<?php
// @file: IGKHtmlCtrlComponentNodeItemBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IIGKHtmlComponent;

abstract class HtmlCtrlComponentNodeItemBase extends HtmlCtrlNodeItemBase implements IIGKHtmlComponent{
    ///<summary></summary>
    ///<param name="tag"></param>
    public function __construct($tag){
        $this->m_controller=igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
        parent::__construct($tag);
        $this->m_controller->Register($this);
    }
    ///<summary></summary>
    public function Dispose(){
        $this->free();
    }
    ///<summary></summary>
    public function free(){
        $this->m_controller->Unregister($this);
    }
    ///<summary></summary>
    public function getComponentId(){
        return $this->getParam(get_class($this->m_controller).":id");
    }
    ///<summary></summary>
    ///<param name="uri"></param>
    public function getComponentUri($uri){
        return ($c=$this->getController()) ? $c->getUri($uri, $this): null;
    }
    ///<summary></summary>
    public function getController(){
        $c=$this->getParam("Controller");
        return $c;
    }
    ///<summary></summary>
    ///<param name="listener"></param>
    ///<param name="param" default="null"></param>
    public function setComponentListener($listener, $param=null){    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="context" default="null"></param>
    protected function setParentNode($n, $context=null){
        if(($n === null) && ($context && (strtolower($context) == 'clearchilds'))){
            $this->free();
        }
        parent::setParentNode($n, $context);
    }
}
