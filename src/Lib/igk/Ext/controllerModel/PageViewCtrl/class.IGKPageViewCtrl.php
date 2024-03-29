<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKPageViewCtrl.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\BaseController;

abstract class IGKPageViewCtrl extends \IGK\Controllers\ControllerTypeBase
{

	/** @var HtmlNode$m_viewZone */
	private $m_viewZone;
	public function getName(){return get_class($this);}
	public function getViewZone(){return $this->m_viewZone;}

	protected function initComplete($context=null){
		parent::initComplete();
		//please enter your controller declaration complete here

	}
	public static function GetAdditionalConfigInfo()
	{
		return null;
	}
	//@@@ init target node
	protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
		$node =  parent::initTargetNode();
		$this->m_viewZone = $node->div();
		igk_css_regclass(".pageview", "{sys:dispib,alignl,alignt,fitw} max-width:1024px; padding:4px;");
		$this->m_viewZone["class"]="pageview";
		return $node;
	}
	public function getCanAddChild(){
		return true;
	}
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