<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlComponentNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\Controllers\ComponentManagerController;
use IGK\IHtmlComponent;
/**
* represent the base component node item
*/
abstract class HtmlComponentNode extends HtmlNode implements IHtmlComponent {

    /**
    * Constant: igk component ctrl flag.
    * @var mixed
    */
    const IGK_COMPONENT_CTRL_FLAG=0xc001;

    /**
    * auto generate doc.
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
        return $this->getController()->getUri($uri, $this);
    }

    /**
    * auto generate doc.
    */

    public function getController(){
        return $this->getFlag(self::IGK_COMPONENT_CTRL_FLAG);
    }
    /**
    *  override this to set component listner
    */

    public function setComponentListener($listener, $params=null){}
}