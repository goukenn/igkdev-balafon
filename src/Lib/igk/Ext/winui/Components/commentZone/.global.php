<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\Resources\R;
use IGK\System\IO\Path;
use IGK\Constants;

igk_ctrl_zone_init(__FILE__);
/**
 * Creates and returns a comment zone HTML node.
 *
 * @return mixed
 */
function igk_html_node_CommentZone(){
$n = igk_create_node("div");
$n["class"] = "igk-comment-z";
$n->addOnRenderCallback(igk_create_node_callback('igk_comment_init', array($n)));
return $n;
}
/**
 * Returns a localized human-readable comment time string from time data.
 *
 * @param array $data Array containing time value and unit key.
 * @return string
 */
function igk_comment_time($data){
	$s = $data[0]>1;
	return R::ngets("btv.comment_time_2", $data[0], R::ngets('time.i.'.$data[1].($s?'s':'')
	)); 
}
/**
 * Renders a comment zone block with title, message, timestamp, and action buttons.
 *
 * @param mixed       $ctrl       The controller instance providing URIs.
 * @param mixed       $n          The parent HTML node to append to.
 * @param string      $title      The comment title.
 * @param string|null $msg        The comment message body.
 * @param mixed|null  $since      Timestamp indicating when the comment was posted.
 * @param mixed|null  $id_reply   ID used to build the reply action link.
 * @param int         $likes      Whether to show like/unlike buttons.
 * @param bool        $have_child Whether to show a "more" button for child comments.
 * @param int         $can_drop   Whether to show a drop/delete button.
 * @return mixed
 */
function igk_comment_zone($ctrl, $n, $title="", $msg=null, $since=null, $id_reply=null,
	$likes=1,
	$have_child=false,
	$can_drop=0
){
	$c = $n->div();
	$c["class"]  = "i";
	$h = $c->div();
	$h["class"]="t";
	$dtc = igk_get_env(__FUNCTION__."://time", function(){
		return igk_time_span("Ymd His",date("Ymd His"));
	});
	$p = $h->div()->setClass("p  igk-bg-comment");
	$p->addSpan()->Content = "logo picture";
	$p->div()->setClass("dispib posab fit loc_l loc_t")->Content= igk_svg_use("comment");
	$ctn = $h->div();
	$hd = $ctn->div()->setClass("cm-header");
	$hd->addSpan()->setClass("dispib")->addSectionTitle(6)->Content= $title;
	if ($since){
		$data = igk_time_max_info($dtc, $since);
		$hd->addSpan()->Content = igk_comment_time($data);
	}
	$ctn->setClass("c")->div()->setClass("m")->Content = $msg!=null?$msg:<<<EOF
--------------------- Nothing to Comment ----------------
EOF;
	$a = $ctn->div();
	if ($id_reply){
		$a =   $a->div()->setClass("a");
		$a->addA($ctrl->getAppUri("comment_add_ajx"))
		->setAttribute('options', "{id:'{$id_reply}'}")
		->setClass("igk-cm-btn")->Content = R::ngets("btn.answer");
	}
	if ($likes){
		$a->addSpan()->setClass("cm-btn lk")->Content  = igk_svg_use("like");
		$a->addSpan()->setClass("cm-btn ulk")->Content  = igk_svg_use("unlike"); 
	}
	if ($have_child){
		$a->addSpan()->setClass("cm-btn more")->Content  = igk_svg_use("morev");
	}
	if ($can_drop)
	$a->addSpan()->setClass("cm-btn drop")->Content  = igk_svg_use("drop");
	return $c;
}
/**
 * Initialises the comment zone by binding CSS and JS assets to the document.
 *
 * @param mixed $a The no-tag node passed from the render callback.
 * @param mixed $b The current rendering node.
 * @param mixed $c The rendering options/context.
 * @return int
 */
function igk_comment_init($a,$b,$c){
	$CF = igk_ctrl_zone(__FILE__);
	if (igk_is_ajx_demand()){
		igk_close_session();
	}
	$path = Path::Combine($CF->getStylesDir(), Constants::DEFAULT_THEME_STYLE);
	igk_css_bind_wuistyle_file($c->Document, $path);
	igk_js_bind_wuiscript($c->Document, $CF, ".commentZone.js", $a);
	return 1;
}
/**
 * Renders a comment zone wrapper node and invokes the given callback with params.
 *
 * @param mixed       $n        The parent HTML node to append the wrapper to.
 * @param callable    $callback The callback to invoke inside the zone.
 * @param array|null  $params   Optional parameters passed to the callback.
 * @return mixed
 */
function igk_comment_zone_callback($n, $callback, $params=null){
	$c = $n->div();
	$c["class"]  = "i";
	$c["style"] = "margin: 2em 0px; padding:10px; ";
	return call_user_func_array($callback, $params);
}