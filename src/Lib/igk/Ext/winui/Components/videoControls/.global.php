<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 

$CF = igk_ctrl_zone_init(__FILE__);
/**
 * Create a video controls HTML node with the given model and options.
 *
 * @param string $model   The control model/style to use.
 * @param mixed  $options JS options object for available controls and buttons.
 * @return mixed The created video controls HTML node.
 */
function igk_html_node_videoControls($model='default', $options=null){
	$CF = igk_ctrl_zone(__FILE__);
	$n = igk_create_node("div");
	$f = igk_dir(dirname(__FILE__)."/.style.func");
	if(!igk_io_file_exists($f))
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