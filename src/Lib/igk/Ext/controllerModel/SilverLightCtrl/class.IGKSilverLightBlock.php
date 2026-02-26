<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKSilverLightBlock.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\BaseController;
use IGK\Controllers\ExtraControllerProperty;
use IGK\System\Html\HtmlReader;

/**
* auto generate doc.
*/
abstract class IGKSilverLightBlockCtrl extends \IGK\Controllers\ControllerTypeBase
{
	/**
	 * Returns additional configuration properties for the Silverlight block.
	 *
	 * @return array
	 */
	public static function GetAdditionalConfigInfo()
	{
		return array(
			"clXabUri" => new ExtraControllerProperty("text", "file.xap"),
			"clPrimaryWidth" => new ExtraControllerProperty("text", "400px"),
			"clPrimaryHeight" => new ExtraControllerProperty("text", "300px")
		);
	}
	/**
	 * Indicates whether child elements can be added to this controller.
	 *
	 * @return bool
	 */
	public function getCanAddChild()
	{
		return false;
	}	 
	/**
	 * Renders the Silverlight object element into the target node.
	 *
	 * @return BaseController
	 */
	public function View():BaseController
	{

		$s = igk_uri(igk_io_baseuri() . "/" . $this->Configs->clXabUri);
		$w  = $this->Configs->clPrimaryWidth;
		$h  = $this->Configs->clPrimaryHeight;
		$t  = HtmlReader::Load(
			<<<OEF
<object id="SilverlightPlugin1" width="{$w}" height="{$h}"
    data="data:application/x-silverlight-2,"
    type="application/x-silverlight-2" >
    <param name="source" value="{$s}"/>

    <!-- Display installation image. -->
    <a href="http://go.microsoft.com/fwlink/?LinkID=149156&v=4.0.60310.0"
        style="text-decoration: none;">
        <img src="http://go.microsoft.com/fwlink/?LinkId=161376"
            alt="Get Microsoft Silverlight"
            style="border-style: none"/>
    </a>
</object>
OEF
		);
		$this->TargetNode->clearChilds();
		$this->TargetNode->Add($t);
		if (!$this->IsVisible) {
			igk_html_rm($this->TargetNode);
		}
		return $this;
	}

	/**
	 * Suppresses child rendering; no target node is used.
	 *
	 * @param mixed $targetnode
	 * @return void
	 */
	protected  function _showChild($targetnode = null)
	{
		//no target
	}
}
