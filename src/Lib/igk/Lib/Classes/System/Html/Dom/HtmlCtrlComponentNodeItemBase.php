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
use IGK\IHtmlComponent;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
abstract class HtmlCtrlComponentNodeItemBase extends HtmlCtrlNodeItemBase implements IHtmlComponent{

    /**
    * .ctr
    * @param mixed $tag
    */
    public function __construct($tag){
        $this->m_controller=igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
        parent::__construct($tag);
        $this->m_controller->Register($this);
    }

    /**
    * auto generate doc.
    */

    public function Dispose(){
        $this->free();
    }

    /**
    * auto generate doc.
    */

    public function free(){
        $this->m_controller->Unregister($this);
    }

    /**
    * auto generate doc.
    */

    public function getComponentId(){
        return $this->getParam(get_class($this->m_controller).":id");
    }

    /**
    * auto generate doc.
    * @param mixed $uri
    */

    public function getComponentUri($uri){
        return ($c=$this->getController()) ? $c->getUri($uri, $this): null;
    }

    /**
    * auto generate doc.
    */

    public function getController(){
        $c=$this->getParam("Controller");
        return $c;
    }

    /**
    * auto generate doc.
    * @param mixed $listener
    * @param null|mixed $param
    */

    public function setComponentListener($listener, $param=null){    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param null|mixed $context
    */

    protected function setParentNode($n, $context=null){
        if(($n === null) && ($context && (strtolower($context) == 'clearchilds'))){
            $this->free();
        }
        parent::setParentNode($n, $context);
    }
}