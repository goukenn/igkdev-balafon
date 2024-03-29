<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKArticleViewerCtrl.php
// @date: 20220803 13:48:59
// @desc: 

/*
*file: class.IGKArticleViewerCtrl.php
*author: C.A.D. Bondje doue
*script :
*/

use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;

igk_js_bind_script_folder(dirname(__FILE__)."/".IGK_SCRIPT_FOLDER);

abstract class IGKArticleViewerCtrl extends \IGK\Controllers\ControllerTypeBase
{

	protected function initComplete($context=null){
		parent::initComplete($context);
	}
	public function View():BaseController{
		$t = $this->TargetNode;
		$t->clearChilds();
		if ($this->isVisible)
		{
			$o = $this->getAllArticlesByCurrentLang();
			$i = 0;
			foreach($o as $k)
			{
				igk_html_article($this, basename($k), $t->div()->setAttributes(array("class"=>"igk-article-viewer-box node_".$i)));
				$i++;
			}
			$t->script()->Content = "window.igk.winui.articleviewer.init();";
		}
		else{
			$t->TargetNode->div()->Content = "No target item";
		}
		return $this;
	}

}

//article viewer extension function

function igk_js_av_bind_initarticle($classname, $updatesize=true, $initanimate=true){//article viewer
	$s =  HtmlNode::CreateWebNode("script");
	$r  = igk_parsebool($updatesize);
	$h = igk_parsebool($initanimate);
	$s->Content = <<<EOF
window.igk.winui.articleviewer.initViewBox(window.igk.getParentScript(), '{$classname}', {$r},{$h});
EOF;
	return $s->render();
} 