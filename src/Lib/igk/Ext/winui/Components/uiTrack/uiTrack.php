<?php
// @author: C.A.D. BONDJE DOUE
// @filename: uiTrack.php
// @date: 20220803 13:48:58
// @desc: 

$CF = igk_ctrl_zone_init(__FILE__);

/**
 * Create a UI track slider HTML node of the given type.
 *
 * @param string $type The track style type to apply.
 * @return mixed The created UI track HTML node.
 */
function igk_html_node_uiTrack($type='default'){
	$CF = igk_ctrl_zone(__FILE__);
	$dv = igk_create_node("div");
	$dv["class"]="igk-winui-uitrack ".$type;

	$callback["_trackoption.func"]=<<<EOF
\$this["igk:uitrack-options"]=igk_getv(\$param,0);
return \$this;
EOF;

	$dv->setCallback("setOption", $callback["_trackoption.func"]);

	$dv->addOnRenderCallback(igk_create_expression_callback(
	igk_io_read_allfile(dirname(__FILE__)."/.style.func"),
	array(
	"node"=>$dv,
	"CF"=>$CF,
	"type"=>$type))
	);
	return $dv;

}
/**
 * Render a demo form with multiple UI track sliders into the given container.
 *
 * @param mixed $t The target container node to render the demo into.
 * @return void
 */
function igk_html_demo_uiTrack($t){
	$frm = $t->div()->addForm();

	$frm->setStyle("width:300px; padding:2em;");

	$frm->addUiTrack()->setId("red"); 
	$frm->addUiTrack()->setId("sepia")->setOption("{min:-128, max:128, update:function(x){return parseInt(((this.max-this.min) * x) + this.min); }}");
	$frm->addUiTrack()->setId("blur")->setOption("{min:0, max:255, update:function(x){return parseInt(((this.max-this.min) * x) + this.min); }}");
} 