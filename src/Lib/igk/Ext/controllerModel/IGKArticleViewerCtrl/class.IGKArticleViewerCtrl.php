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

/**
* auto generate doc.
*/
abstract class IGKArticleViewerCtrl extends \IGK\Controllers\ControllerTypeBase
{

	/**
	 * Completes the controller initialization.
	 *
	 * @param mixed $context Optional initialization context.
	 * @return void
	 */
	protected function initComplete($context=null){
		parent::initComplete($context);
	}
	/**
	 * Renders all articles for the current language into the target node.
	 *
	 * @return BaseController
	 */
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

/**
 * Generates the JavaScript initialization script for an article viewer box.
 *
 * @param string $classname   CSS class name used to identify the viewer box.
 * @param bool   $updatesize  Whether to update the box size on initialization.
 * @param bool   $initanimate Whether to run the open animation on initialization.
 * @return string
 */
function igk_js_av_bind_initarticle($classname, $updatesize=true, $initanimate=true){//article viewer
	$s =  HtmlNode::CreateWebNode("script");
	$r  = igk_parsebool($updatesize);
	$h = igk_parsebool($initanimate);
	$s->Content = <<<EOF
window.igk.winui.articleviewer.initViewBox(window.igk.getParentScript(), '{$classname}', {$r},{$h});
EOF;
	return $s->render();
} 