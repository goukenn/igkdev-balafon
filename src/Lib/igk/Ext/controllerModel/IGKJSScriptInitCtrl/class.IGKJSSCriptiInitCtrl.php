<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKJSSCriptiInitCtrl.php
// @date: 20220803 13:48:59
// @desc: 

/*
controller to load inistialization script on document
*/

use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;

/**
* Igkjsscript init ctrl.
*/
abstract class IGKJSScriptInitCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * Property: script.
    * @var mixed
    */
    private $m_script;

    /**
    * Support multiple.
    */

    public static function SupportMultiple(){//return false to indicate that an element of this type must be unique
		return false;
	}

    /**
    * Returns Can Add Child.
    */

    public function getCanAddChild(){
		return false;
	}

    /**
    * Returns Can Edit Data Base.
    */

    public function getCanEditDataBase()
	{
		return false;
	}

    /**
    * Returns Can Edit Data Table Info.
    */

    public function getCanEditDataTableInfo(){
		return false;
	}

    /**
    * Initializes Target Node.
    * @return ?HtmlNode
    */

    protected function initTargetNode(): ?HtmlNode
	{
		$n =  HtmlNode::CreateWebNode("script");
		$this->m_script = $n;
		return null;
	}

    /**
    * Returns Is Visible.
    * @return bool
    */

    public function getIsVisible():bool{
		return !igk_is_confpagefolder();
	}

    /**
    * Page folder changed.
    */

    public function pageFolderChanged()
	{
		$this->View();
	}

    /**
    * View.
    * @return BaseController
    */

    public function View():BaseController
	{
		if ($this->IsVisible)
		{
			$v_main_js = ScriptConfigData::GetControllerMainScript($this);
			$this->m_script->Content  ="";
			$v = $this->getArticleContent($v_main_js);
			$this->m_script->Content =$v;
			igk_app()->getDoc()->getBody()->add($this->m_script);
		}
		else {
			$this->m_script->remove();
		}
		return $this;
	}
} 