<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKTestTools.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\ToolControllerBase;
final class IGKTestTools extends ToolControllerBase
{

	public function getImageUri(){
		$uri = igk_html_resolv_img_uri(igk_io_baseDir("Lib/igk/Default/R/Img/pics_48x48/tool_sitemapgen.png"));
		return $uri;
	}
	public function DoAction(){
	}
}


igk_tool_reg("testing",array("ImageUri"=>"", "Action"=>function(){

})); 