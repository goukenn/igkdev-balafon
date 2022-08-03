<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKSitemapGeneratorTools.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\ToolControllerBase;
//method 1: site map generator
final class IGKSitemapGeneratorTools extends ToolControllerBase
{

	public function getImageUri(){
		$uri = igk_html_resolv_img_uri(igk_io_baseDir("Lib/igk/Default/R/Img/pics_48x48/tool_sitemapgen.png"));
		return $uri;
	}
	public function doAction()
	{
		igk_sys_gen_global_sitemap(1);

		igk_notifyctrl()->addMsgr("msg.sitemapgenerated");
		$this->refreshToolView();
		igk_navtocurrent(); 
	}
}
