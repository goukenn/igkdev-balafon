<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKTestTools.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\Controllers\ToolControllerBase;

/**
* Igktest tools.
*/
final class IGKTestTools extends ToolControllerBase
{
	/**
	 * Return the URI of the tool image icon.
	 *
	 * @return string URI of the test tool image.
	 */
	public function getImageUri(){
		$uri = igk_html_resolv_img_uri(igk_io_baseDir("Lib/igk/Default/R/Img/pics_48x48/tool_sitemapgen.png"));
		return $uri;
	}
	/**
	 * Execute the test tool action.
	 *
	 * @return void
	 */
	public function DoAction(){
	}
}
igk_tool_reg("testing",array("ImageUri"=>"", "Action"=>function(){
})); 