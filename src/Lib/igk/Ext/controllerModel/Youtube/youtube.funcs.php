<?php
// @author: C.A.D. BONDJE DOUE
// @filename: youtube.funcs.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\System\Html\Dom\HtmlItemBase;

/**
 * create youtube video tag
 * @param string $uri 
 * @param null|array $param 
 * @return HtmlItemBase<mixed, mixed> 
 * @throws IGKException 
 */
function igk_html_node_youtubevideo(string $uri, ?array $param=null){
	$n = igk_create_node("iframe");
	$n["src"] = $uri;
	$n["allowFullScreen"]="1";
	$n["title"]= igk_getv($param, "title", "YouTube video player" );
	$n["class"]= igk_getv($param, "class", "igk-winui-youtubevideo youtube-player");
	$n["frameborder"]= 0;
	$n["type"]="text/html";
	return $n;
}
/**
* Igk html demo youtubevideo.
* @param mixed $tg
* @return mixed
*/
function igk_html_demo_youtubevideo($tg){
	$n = igk_create_node();
	$n->addyoutubeVideo("https://www.youtube.com/embed/YqeW9_5kURI");
	$tg->div()->Content = "You tube demonstration";
	$tg->add($n); 
}
/**
* Igk html desc youtube video.
* @param mixed $tg
* @return mixed
*/
function igk_html_desc_youtubeVideo($tg){
	$n = igk_create_node();
	$n->div()->Content = "Usage in PHP Script";
	$n->addCode()->Content = (<<<EOF
\$node->addyoutubeVideo("https://www.youtube.com/embed/YqeW9_5kURI");
EOF);
	$tg->add($n);
}