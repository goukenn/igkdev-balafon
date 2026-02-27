<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKPageViewCtrl.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\BaseController;

/**
* Igkpage view ctrl.
*/
abstract class IGKPageViewCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * auto generate doc.
    * @var HtmlNode$m_viewZone
    */
	private $m_viewZone;

    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{return get_class($this);}

    /**
    * Returns View Zone.
    */
    public function getViewZone(){return $this->m_viewZone;}

    /**
    * Initializes Complete.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
		parent::initComplete();
		//please enter your controller declaration complete here

	}

    /**
    * Returns Additional Config Info.
    */
    public static function GetAdditionalConfigInfo()
	{
		return null;
	}
	//@@@ init target node

    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
		$node =  parent::initTargetNode();
		$this->m_viewZone = $node->div();
		igk_css_regclass(".pageview", "{sys:dispib,alignl,alignt,fitw} max-width:1024px; padding:4px;");
		$this->m_viewZone["class"]="pageview";
		return $node;
	}

    /**
    * Returns Can Add Child.
    */
    public function getCanAddChild(){
		return true;
	}

    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController
	{
		if ($this->IsVisible)
		{
			 $n = $this->TargetNode;
			 $t = $this->m_viewZone;
			 if ($t !== null)
			 {
			 $this->setTargetNode($t);

			 $t->clearChilds();
			//view article
			//---------------------------------
			$this->_showViewFile();
			$this->setTargetNode($n); //restore
			$this->_showChild();
			}
		}
		else{
			igk_html_rm($this->TargetNode);
		}
		return $this;
	}

    /**
    * Show child.
    * @param null|mixed $targetnode
    */
    protected function _showChild($targetnode=null)
	{
		//maintain the view
		$t = $targetnode? $targetnode: $this->TargetNode;
		$t->add($this->m_viewZone);

		if ($this->hasChild)
		{
			foreach($this->getChilds() as  $v)
			{
				if ($v->isVisible)
				{
					$this->m_viewZone->add($v->TargetNode);
					$v->View();
				}
				else {
					igk_html_rm($v->TargetNode);
				}
			}
		}
	}
} 