<?php


namespace IGK\System\Html\Dom;

use IGK\Controllers\ComponentManagerController;
use IIGKHtmlComponent;

///<summary>represent the base component node item</summary>
/**
* represent the base component node item
*/
abstract class HtmlComponentNode extends HtmlNode implements IIGKHtmlComponent {
    const IGK_COMPONENT_CTRL_FLAG=0xc001;
    ///<summary></summary>
    ///<param name="tagname"></param>
    ///<param name="controller" default="null"></param>
    /**
    * 
    * @param mixed $tagname
    * @param mixed $controller the default value is null
    */
    public function __construct($tagname, $controller=null){
        $ctrl=$controller ?? igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, false) ?? new ComponentManagerController();
        parent::__construct($tagname);
        if($ctrl){
            $this->setFlag(self::IGK_COMPONENT_CTRL_FLAG, $ctrl);
            $ctrl->Register($this);
        }
        else{
            igk_die("component failed");
        }
    }
    ///<summary>dispose component</summary>
    /**
    * dispose component
    */
    public function Dispose(){
        $c=$this->getController();
        if($c != null){
            $c->Unregister($this);
            $this->setFlag(self::IGK_COMPONENT_CTRL_FLAG, null);
        }
        parent::Dispose();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getComponentId(){
        return $this->getParam(get_class($this->m_controller).":id");
    }
    ///<summary></summary>
    ///<param name="uri"></param>
    /**
    * 
    * @param mixed $uri
    */
    public function getComponentUri($uri){
        return $this->getController()->getUri($uri, $this);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getController(){
        return $this->getFlag(self::IGK_COMPONENT_CTRL_FLAG);
    }
    ///<summary> override this to set component listner</summary>
    /**
    *  override this to set component listner
    */
    public function setComponentListener($listener, $params=null){}
}
