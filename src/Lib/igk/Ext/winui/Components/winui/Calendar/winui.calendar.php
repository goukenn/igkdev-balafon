<?php
// @author: C.A.D. BONDJE DOUE
// @filename: winui.calendar.php
// @date: 20220803 13:48:58
// @desc: 

/**
 * Creates and returns a WinUI calendar HTML node.
 *
 * @return mixed
 */
function igk_html_node_Calendar(){
	$d = igk_create_node("div");
	$d["class"] = "igk-winui-calendar";
	return $d;
}
/**
 * Renders the calendar component demo (not yet implemented).
 *
 * @param mixed $t The target HTML node.
 * @return void
 */
function igk_html_demo_Calendar($t){
	die("not implement");
}
igk_html_reg_component_demo("Calendar", function($n){
});