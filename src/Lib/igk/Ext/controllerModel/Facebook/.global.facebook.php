<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.facebook.php
// @date: 20220803 13:48:59
// @desc: 


use IGK\Resources\R;

// igk_set_env("sys://facebook/settings", array(
// "lib"=>"https://connect.facebook.net",
// "lang"=>"en_GB",
// "version"=>"v2.11",
// "appId"=>null
// ));

function igk_fb_init($conf){
	igk_fb_set_appId(igk_conf_get($conf, "app.Followus/facebookAppID"));
}
function igk_fb_set_appId($appId){
	$h = igk_get_env("sys://facebook/settings");
	$h["appId"] = $appId;
	igk_set_env("sys://facebook/settings",$h);
}
function igk_fb_lang($k){
	$tab = array("fr"=>"fr_FR","en"=>"en_GB");
	return igk_getv($tab, strtolower($k), igk_getv(igk_get_env("sys://facebook/settings"), "lang"));
}
function igk_fb_LibExpression(){
	$h = igk_get_env("sys://facebook/settings");
	$fb_js = realpath(dirname(__FILE__)."/Scripts/.fb.js");
	$lang = igk_fb_lang(R::GetCurrentLang());
	$v = $h["version"];
	$lib = $h["lib"]."/".$lang."/sdk.js#xfbml=1&version={$v}&appId=".$h["appId"];	
	return <<<EOF
\$doc = igk_getv(\$extra[0], "Document");
igk_doc_add_tempscript(\$doc, '{$fb_js}',1, array('data-lib'=>'{$lib}', 'data-locale'=>'{$lang}'));
return 1;
EOF;
}
///theme : light or dark
///layout: standard|button_count|box_count
function igk_html_node_FacebookFollowUsButton($id,$layout=null,$theme=null){
	$uri = "https://www.facebook.com/plugins/follow.php?href=".
	htmlentities("https://www.facebook.com/{$id}");
	if($layout)
		$uri.="&layout={$layout}";
	if ($theme)
		$uri.="&colorscheme={$theme}";
	$lang = igk_fb_lang(R::GetCurrentLang());
	$uri.="&locale={$lang}";
	$n = igk_create_node("iframe");
	$n["src"]=$uri;
	$n["allowTransparency"]="true";
	$n["scrolling"]="no";
	$n["frameborder"]="no";
	$n["class"]="fb-i-follow";
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(igk_fb_LibExpression(),array("n"=>$n)));
	$n->add($b);
	return $n;

}

function igk_html_node_faceBookTimeLine($id){


	$n = igk_create_node("div");

	$n["class"]="fb-like";
	$n["data-href"] = "https://www.facebook.com/".$id;
	$n["data-layout"] = "light";
	$n["data-size"] = "small";
	$n["data-show-faces"]="true";
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(igk_fb_LibExpression(),array("n"=>$n)));
	$n->add($b);
	return $n;
}

function igk_html_node_faceBookLikeButton($showface=false){
	$n = igk_create_node("div");
// <div
// class="fb-like"
// data-share="false"
// data-width="450"
// data-show-faces="true">dddd
// </div>
	$n["class"]="fb-like";
	// $n["class"]="fb-share-button";
	$n["data-share"] = "false";//
	$n["data-width"] = "150";
	$n["data-show-faces"]=igk_parsebool($showface);

	//$n->Content = "Facebook - The like button";
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(igk_fb_LibExpression(),array("n"=>$n)));
	$n->add($b);
	return $n;
}

function igk_html_node_faceBookShareButton(){
		$n = igk_create_node("div");
// <div
// class="fb-like"
// data-share="false"
// data-width="450"
// data-show-faces="true">dddd
// </div>
	//$n["class"]="fb-like";
	$n["class"]="fb-share-button";
	$n["data-share"] = "false";//
	$n["data-width"] = "150";
	$n["data-show-faces"]="false";



	//$n->Content = "Facebook - The like button";
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(igk_fb_LibExpression(),array("n"=>$n)));
	$n->add($b);
	return $n;
}

function igk_html_node_faceBookComments($uri){
	$n = igk_create_node("div");
	$n["class"]="fb-comments";
	$n["data-href"] = $uri;
	$n["data-numposts"] = "5";
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(igk_fb_LibExpression(),array("n"=>$n)));
	$n->add($b);
	return $n;
}



// igk_community_register_followus_service("facebook", function($cmd,$t,$v=null){
// 	switch($cmd){
// 		case "edit":
// 			$name = igk_getv(func_get_args(),3);
// 			$ul = $t->add("ul");
// 			igk_html_build_form($ul,array(
// 			IGK_FIELD_PREFIX.$name."Id"=>array("attribs"=>array("value"=>igk_conf_get($v,'facebookId'))),
// 			IGK_FIELD_PREFIX.$name."AppID"=>array("attribs"=>array("value"=>igk_conf_get($v,'facebookAppID'))),
// 			IGK_FIELD_PREFIX.$name."Version"=>array("attribs"=>array("value"=>igk_conf_get($v,'facebookVersion')))
// 			));
// 		break;
// 		case "getlink":
// 		if (isset($v->facebookId))
// 			return "https://facebook.com/".$v->facebookId;
// 			break;
// 		case "view":
// 		default:
// 		break;
// 	}
// 	return null;
// });