<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKPdfViewerCtrl.php
// @date: 20220803 13:48:58
// @desc: 


/*
file : class.IGKPDFViewerCtrl.php
author:C.A.D. BONDJE DOUE
*/

use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;

igk_bind_attribute("class","IGKPDFViewerCtrl", new IGKControllerTypeAttribute());
/*
represent a IGKPDFViewerCtrl
*/

/**
* Igkhtml pdf view node.
*/
final class IGKHtmlPdfViewNode extends HtmlNode
{

    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;
	/**
	 * Constructor.
	 *
	 * @param mixed $ctrl The parent PDF viewer controller.
	 */

    public function __construct($ctrl)
	{
		parent::__construct("iframe");
		$this->m_ctrl = $ctrl;
		$this["class"]="noborder dispb fitw fith cliframe";

	}
	/**
	 * Renders the iframe node with the PDF Ajax URI as its source.
	 *
	 * @param mixed $xmloption Optional XML render options.
	 * @return string
	 */

    public function render($xmloption=null)
	{
		$uri = $this->m_ctrl->getUri("render_pdf_ajx");
		$this["src"] = igk_io_baseuri().$uri;
		return parent::Render($xmloption);
	}
	/**
	 * Returns the inner HTML of the node.
	 *
	 * @param mixed $xmloption Optional XML render options passed by reference.
	 * @return string
	 */

    public function innerHTML (& $xmloption =null)
	{

			$o = parent::innerHTML($xmloption);
			//$c  =  HtmlNode::CreateWebNode("script");


			// $c->Content = <<<EOF
// (function(p){ var q = \$igk(p).add('div'); window.igk.ajx.aget('{$uri}', null, new igk.ajx.targetResponse(q).update);})(window.igk.getParentScript());
// EOF;
			// $o .= $c->render();
			return $o;
	}
}

/**
* Igkpdfviewer ctrl.
*/
abstract class IGKPDFViewerCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * Property: pdf.
    * @var mixed
    */
    private $m_pdf;
	/**
	 * Constructor.
	 */

    public function __construct(){
		parent::__construct();
	}
	/**
	 * Completes the controller initialization.
	 *
	 * @param mixed $context Optional initialization context.
	 * @return void
	 */

    protected function initComplete($context=null){
		parent::initComplete();
	}
	/**
	 * Returns whether child controllers can be added to this controller.
	 *
	 * @return bool
	 */

    public function getCanAddChild(){
		return false;
	}
	/**
	 * Initializes the target node and embeds the PDF iframe view node.
	 *
	 * @return \IGK\System\Html\Dom\HtmlNode|null
	 */

    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
		$n = parent::initTargetNode();
		$pdf = new IGKHtmlPdfViewNode($this);
		$n->add(	$pdf);
		$this->m_pdf = $pdf;

		return $n;
	}
	/**
	 * Renders the PDF viewer, removing the target node when not visible.
	 *
	 * @return BaseController
	 */

    public function View():BaseController{
		if (!$this->IsVisible)
		{
			igk_html_rm($this->TargetNode);
		}
		return $this;
	}
	/**
	 * Handles the Ajax request to render and output the PDF content.
	 *
	 * @return void
	 */

    public function render_pdf_ajx()
	{
		$pdf = new IGKPdf();
		include(dirname(__FILE__)."/".IGK_DATA_FOLDER."/temp.iwpdfsrc");

		igk_wl($pdf->render());
	}
} 