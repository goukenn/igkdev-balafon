<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 


$CF = igk_ctrl_zone_init(__FILE__);


///<summary> create a video controls list</summary>
///options: JS object
///{controls: for available control , btns
function igk_html_node_videoControls($model='default', $options=null){
	$CF = igk_ctrl_zone(__FILE__);
	$n = igk_create_node("div");
	$f = igk_dir(dirname(__FILE__)."/.style.func");
	if(!file_exists($f))
		igk_die("style file not exists " , __FUNCTION__);

	$n["class"]="igk-video-controls";
	$n->addOnRenderCallback(igk_create_expression_callback(
	file_get_contents($f),
	array(
	"node"=>$n,
	"CF"=>$CF,
	"type"=>$model))
	);
	if ($options){
		$n->setAttribute("igk:data", $options);
	}
	return $n;
} 