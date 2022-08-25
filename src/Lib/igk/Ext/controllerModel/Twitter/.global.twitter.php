<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.twitter.php
// @date: 20220803 13:48:58
// @desc: 


function igk_html_node_twitterFollowUs($id, $showcount=0){
	//followus
	$n = igk_create_xmlnode("a");
	$n["class"]="twitter-follow-button";
	$n["data-show-count"]=$showcount?"true":"false";
	$n["href"] = "https://twitter.com/".$id;
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(<<<EOF
\$doc = igk_getv(\$extra[0], "Document");
if (\$doc){
	\$d = \$doc->addTempScript('https://platform.twitter.com/widgets.js',1);
	\$d["charset"]="utf-8";
	\$d->activate("async");
	return 1;
}
return 0;
EOF
,array("n"=>$n)));

	$n->add($b);
	return $n;
}


///<summary>twitter time line zone</summary>
function igk_html_node_twitterTimeLine($id, $theme=null, $color=null){
	igk_trace();
	igk_exit();
	$n = igk_create_xmlnode("a");
	$n["class"]="twitter-timeline";
	$n["href"] = "https://twitter.com/".$id."?ref_src=twsrc%5Etfw";
	$n["data-theme"] = $theme;
	$n["data-link-color"] = $color;
	$js = dirname(__FILE__)."/Scripts/.twitter.loader.js";
	$lib= "";
	$b = igk_html_node_onrendercallback(igk_create_expression_callback(<<<EOF
\$doc = igk_getv(\$extra[0], "Document");
if (\$doc){
	igk_doc_add_tempscript(\$doc,'{$js}', 1 , array('charset'=>'utf-8'));
	return 1;
}else{
	igk_doc_add_tempscript(null,'{$js}', 1 , array('charset'=>'utf-8','data-lib'=>'{$lib}'));
}
return 0;
EOF
,array("n"=>$n)));

	$n->add($b);
	return $n;
}
 