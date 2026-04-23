<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlRenderer;

if (!function_exists('igk_sys_gen_global_sitemap')) {
    /**
    * auto generate doc.
    * @param int $store
    * @return void
    */
	function igk_sys_gen_global_sitemap($store = 0)
	{
		$n = igk_html_node_IGKSiteMap();
		$pages = igk_sys_pagelist();
		$buri = igk_str_rm_last(igk_io_baseUri(), '/');
		$ac = igk_getctrl(IGK_SYSACTION_CTRL);
		$actions = $ac->getActions();
		foreach ($actions as $k => $v) {
			$url = $n->addNode("url");
			$s = igk_pattern_get_uri_from_key($k, $buri);
			$url->add("loc")->Content = $s;
		}
		$options = HtmlRenderer::CreateRenderOptions();
		$options->Indent = 1;
		$uri = igk_io_baseUri() . "/Lib/igk/Styles/sitemap.xsl";
		igk_wl(igk_xml_header());
		igk_wl(<<<EOF
<?xml-stylesheet type="text/xsl" href="{$uri}"?>
EOF		);
		if ($store) {
			$o = $n->render($options);
			igk_io_save_file_as_utf8(igk_io_baseDir("sitemap.xml"), $o);
			igk_xml($o);
		} else
			$n->RenderXML($options);
	}
}
igk_tool_reg("sitemap", array("ImageUri" => "", "Action" => function () {
	if (func_num_args() > 0)
		igk_sys_gen_global_sitemap(func_get_arg(0));
	else
		igk_sys_gen_global_sitemap();
	return true;
}));