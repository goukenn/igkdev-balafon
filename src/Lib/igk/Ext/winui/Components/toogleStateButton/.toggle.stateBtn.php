<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .toggle.stateBtn.php
// @date: 20220803 13:48:58
// @desc: 

$CF = igk_ctrl_zone_init(__FILE__);
/**
 * Create a toggle state button HTML node.
 *
 * @param string $id      The name/identifier of the toggle button.
 * @param string $value   The value assigned to the button.
 * @param int    $checked Whether the button is checked (1) or not (0).
 * @param string $type    The visual style type of the button.
 * @return mixed The created toggle state button HTML node.
 */
function igk_html_node_ToggleStateButton($id,$value='on', $checked=0,$type="window10"){
		$src_expression = igk_io_read_allfile(dirname(__FILE__)."/.statebtn.func");
	$CF = igk_ctrl_zone(__FILE__);
	$n = igk_create_node("div");
	$n["class"] = "igk-winui-btn-toggle-state";
	$n->addOnRenderCallback(igk_create_expression_callback($src_expression,
		["node"=>$n,
		"CF"=>$CF,
		"type"=>$type,
		"name"=>$id,
		"i_value"=>array("v"=>$value,"c"=>$checked)
		]
	));
	return $n;
}
/**
 * Render a demo of the toggle state button into the given container.
 *
 * @param mixed $tg The target container node to render the demo into.
 * @return void
 */
function igk_html_demo_ToggleStateButton($tg){
	$tg->div()->Content = "<b>window10</b> style state button";
	$n = igk_html_node_ToggleStateButton('marche',"window10");
	$tg->add($n);
}