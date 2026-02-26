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
* auto generate doc.
*/
abstract class IGKJSScriptInitCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_script;

    /**
    * auto generate doc.
    */

    public static function SupportMultiple(){//return false to indicate that an element of this type must be unique
		return false;
	}

    /**
    * auto generate doc.
    */

    public function getCanAddChild(){
		return false;
	}

    /**
    * auto generate doc.
    */

    public function getCanEditDataBase()
	{
		return false;
	}

    /**
    * auto generate doc.
    */

    public function getCanEditDataTableInfo(){
		return false;
	}

    /**
    * auto generate doc.
    * @return ?HtmlNode
    */

    protected function initTargetNode(): ?HtmlNode
	{
		$n =  HtmlNode::CreateWebNode("script");
		$this->m_script = $n;
		return null;
	}

    /**
    * auto generate doc.
    * @return bool
    */

    public function getIsVisible():bool{
		return !igk_is_confpagefolder();
	}

    /**
    * auto generate doc.
    */

    public function pageFolderChanged()
	{
		$this->View();
	}

    /**
    * auto generate doc.
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