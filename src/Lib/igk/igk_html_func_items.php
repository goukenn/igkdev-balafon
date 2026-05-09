<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_html_func_items.php
// @date: 20130101 20:35:58
// @desc: define core compoent
use IGK\Controllers\BaseController;
use IGK\Database\IDbArrayResult;
use IGK\Helper\Activator;
use IGK\Helper\BalafonJSHelper;
use IGK\Helper\ViewHelper;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\Resources\R;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\CallableConstants;
use IGK\System\Html\Dom\Component\ActionGroupComponent;
use IGK\System\Html\Dom\HtmlANode;
use IGK\System\Html\Dom\HtmlAssertNode;
use IGK\System\Html\Dom\HtmlCommentNode;
use IGK\System\Html\Dom\HtmlComponents;
use IGK\System\Html\Dom\HtmlConditionNode;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlLayoutViewInclusion;
use IGK\System\Html\Dom\HtmlLooperNode;
use IGK\System\Html\Dom\HtmlMemoryUsageInfoNode;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;
use IGK\System\Html\Dom\HtmlSpaceNode;
use IGK\System\Html\Dom\HtmlWebComponentNode;
use IGK\System\Html\HtmlAttribExpressionNode;
use IGK\System\Html\HtmlHeaderLinkHost;
use IGK\System\Html\HtmlJsOptionDefinition;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlUsageCondition;
use IGK\System\Html\IFormFieldContainer;
use IGK\System\Html\IFormFields;
use IGK\System\Html\XML\XmlNode;
use IGK\System\Http\Mail\MailPreviewNode;
use IGK\System\Number;
use IGK\System\Services\LoginServiceEvents;
use IGK\System\UriResolver;
use function igk_resources_gets as __;

if (!function_exists("igk_css_link_callback")) {
	/**
	 * function igk_css_link_callback
	 * @param mixed $p
	 * @param mixed $key
	 * @param mixed $href
	 */
	function igk_css_link_callback($p, $key, $href)
	{
		$g = $p->getParam($key);
		if ($g && $href) {
			if (is_object($href) && isset($g[$href->refName])) {
				unset($g[$href->refName]);
			} else if (is_string($href) && isset($g[$href])) {
				unset($g[$href]);
			}
		}
		$p->setParam($key, $g);
	}
}
if (!function_exists("igk_file_content")) {
	/**
	 * encapsulate file_get_contents
	 * @param mixed $file
	 */
	function igk_file_content($file)
	{
		return file_get_contents($file);
	}
}
if (!function_exists("igk_html_node_cdata")) {
	/**
	 * create a cdata node
	 */
	function igk_html_node_cdata($value = null)
	{
		$v = (new XmlNode("!CDATA"));
		$v->setContent($value);
		return $v;
	}
}
if (!function_exists("igk_html__tabbutton_add")) {
	/**
	 * function igk_html__tabbutton_add
	 * @param mixed $q
	 */
	function igk_html__tabbutton_add($q)
	{
		$n = igk_create_node("div");
		$q->add($n);
		return $n;
	}
}
if (!function_exists("igk_html_add_context_menu_item")) {
	/**
	 * function igk_html_add_context_menu_item
	 * @param mixed $n
	 * @param mixed $uri
	 * @param mixed $display
	 */
	function igk_html_add_context_menu_item($n, $uri, $display)
	{
		$n->li()->addA($uri)->Content = $display;
	}
}
if (!function_exists("igk_html_callback_ajx_lnksettarget")) {
	/**
	 * function igk_html_callback_ajx_lnksettarget
	 * @param mixed $n
	 * @param mixed $p
	 */
	function igk_html_callback_ajx_lnksettarget($n, $p)
	{
		$n["igk-lnk-target"] = $p;
	}
}
if (!function_exists("igk_html_callback_alinktn")) {
	/**
	 * function igk_html_callback_alinktn
	 * @param mixed $n
	 */
	function igk_html_callback_alinktn($n)
	{
		$n->setStyle("width: " . $n->getParam('data')->w . "px; height: " . $n->getParam('data')->h . "px;");
		$i = $n->getParam('data')->img;
		$i["src"] = $n->getParam('data')->src;
		$i["srcdata"] = $n->getParam('data')->src;
		return $n->IsVisible;
	}
}
if (!function_exists("igk_html_callback_ctrlview_acceptrender")) {
	/**
	 * function igk_html_callback_ctrlview_acceptrender
	 * @param mixed $n
	 * @param mixed $s
	 * @param mixed $clear
	 */
	function igk_html_callback_ctrlview_acceptrender($n, $s, $clear = 1)
	{
		if ($clear) {
			$n->clearChilds();
		}
		$d = $n->getParam("data");
		if ($d) {
			$c = $d->ctrl;
			$v = $d->view;
			if ($c) {
				try {
					ob_start();
					$c->getViewContent($v, $n, 0, $d->params);
					$ss = ob_get_contents();
					ob_end_clean();
					if (!empty($ss)) {
						$n->addText($ss);
					}
					return true;
				} catch (Exception $ex) {
					igk_ilog("some error:" . $ex->getMessage());
				}
			}
		} else {
			igk_ilog('no data provide for node', __FILE__ . "::" . __FUNCTION__ . "::" . __LINE__);
		}
		return false;
	}
}
if (!function_exists("igk_html_callback_replacecontent_acceptrender")) {
	/**
	 * function igk_html_callback_replacecontent_acceptrender
	 * @param mixed $n
	 */
	function igk_html_callback_replacecontent_acceptrender($n)
	{
		if (!$n->isVisible)
			return 0;
		$n->clearChilds();
		$n->addBalafonJS()->Content = <<<EOF
(function(q){igk.ready( function(){ ns_igk.ajx.fn.scriptReplaceContent('{$n->method}', '{$n->uri}', q);}); })(igk.getParentScript());
EOF;
		return 1;
	}
}
if (!function_exists("igk_html_code_copyright_callback")) {
	/**
	 * function igk_html_code_copyright_callback
	 * @param mixed $ctrl
	 */
	function igk_html_code_copyright_callback($ctrl = null)
	{
		$s = ($ctrl ? igk_getv($ctrl->Configs, 'copyright') : null) ?? igk_configs()->get('copyright', IGK_COPYRIGHT);
		if (!empty($s)) {
			$s = igk_html_databinding_treatresponse($s, null, null);
		}
		return $s;
	}
}
if (!function_exists("igk_html_community_view")) {
	/**
	 * function __desc__
	 */
	function igk_html_community_view($n, $v, $k)
	{
		if (is_object($v) || is_array($v)) {
			$uri = igk_getv($v, "uri");
			if (($c = igk_getv($v, "auth")) && (is_callable($c) && (!$c()))) {
				return;
			}
		} else {
			$uri = $v;
		}
		$n->add("li")->addA($uri)->setAttribute("target", "__blank")->setClass($k)->Content = igk_svg_use($k);
	}
}
if (!function_exists("igk_html_create_container_section")) {
	/**
	 * utility to create a container section
	 */
	function igk_html_create_container_section($t)
	{
		$dv = $t->div();
		$ct = $dv->container();
		$r = $ct->addRow();
		return $r;
	}
}
if (!function_exists("igk_html_handle_cssstyle")) {
	/**
	 * handle used to render css style
	 */
	function igk_html_handle_cssstyle($n)
	{
		$s = $n->Content;
		$tab = array();
		$s = preg_replace_callback(
			"/@(?P<name>" . IGK_IDENTIFIER_RX . "):(?P<value>[^;]+);/i",
			function ($m) use (&$tab) {
				$tab[trim($m["name"])] = $m["value"];
				return "";
			},
			$s
		);
		if (igk_count($tab) > 0) {
			foreach ($tab as $k => $v) {
				$s = str_replace("@" . $k, $v, $s);
			}
		}
		return $n->minfile ? igk_min_script($s) : $s;
	}
}
if (!function_exists("igk_html_node_ViewCallback")) {
	/**
	 * auto generate doc.
	 * @param mixed $callback callback to call
	 * @return mixed
	 */
	function igk_html_node_ViewCallback(callable $callback)
	{
		$n = igk_html_parent_node();
		ob_start();
		$callback($n);
		$c = ob_get_contents();
		ob_end_clean();
		if (!empty($c)) {
			$n->addText($c);
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_a")) {
	/**
	 * create winui-a
	 * @param mixed $href
	 * @param mixed $attributes
	 * @param mixed $index
	 */
	function igk_html_node_a($href = "#", $attributes = null, $index = null, $content = null)
	{
		$a = new HtmlANode();
		$a["href"] = $href;
		$a->setIndex($index);
		if (is_string($attributes)) {
			$attributes = ['target' => $attributes];
		}
		if ($content) {
			$a->Content =  $content;
		}
		if ($attributes && is_array($attributes)) {
			$a->setAttributes($attributes);
		}
		if ($href != "#") {
			$a->EmptyContent = $href;
		}
		return $a;
	}
}
if (!function_exists("igk_html_node_a_get")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_a_get($uri, $complete = '')
	{
		$a = igk_html_node_a("#");
		$a->on("click", "igk.ajx.get('" .
			$uri
			. "',null,'" . $complete . "'); return false;");
		return $a;
	}
}
if (!function_exists("igk_html_node_a_post")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_a_post($uri, $complete = '')
	{
		$a = igk_html_node_a();
		if (empty($complete)) {
			$complete = 'null';
		}
		$a->on("click", "igk.ajx.post('" .
			$uri
			. "',null, " . $complete . "); return false;");
		return $a;
	}
}
if (!function_exists("igk_html_node_abbr")) {
	/**
	 * create winui-abbr
	 * @param mixed $title
	 */
	function igk_html_node_abbr($title = null)
	{
		$n = new HtmlNode("abbr");
		$n['class'] = 'igk-abbr';
		$n['title'] = $title;
		return $n;
	}
}
if (!function_exists("igk_html_node_abtn")) {
	/**
	 * auto generate doc.
	 * @param string $role role
	 * @return HtmlItemBase<mixed
	 */
	function igk_html_node_abtn($uri = "#", $type = "default", $role = "button")
	{
		$n = igk_create_node("a");
		$n['class'] = "igk-btn";
		$n['href'] = $uri;
		$n['role'] = $role;
		$n->type = $type;
		return $n;
	}
}
if (!function_exists("igk_html_node_accordeon_menus")) {
	/**
	 * auto generate doc.
	 * @param string $item
	 * @return HtmlItemBase<mixed
	 */
	function igk_html_node_accordeon_menus($items, $engine = null, $tag = "ul", $item = "li")
	{
		$n = igk_html_node_menus($items, $engine, $tag, $item);
		$n->balafonjs()->Content = "igk.winui.menu.accordeonMenu.init(igk.getParentScript());";
		return $n;
	}
}
if (!function_exists("igk_html_node_aclearsandreload")) {
	/**
	 * create winui-aclearsandreload
	 */
	function igk_html_node_aclearsandreload()
	{
		if (is_null($curi = igk_io_currentUri())) {
			return null;
		}
		$ctrl = igk_getctrl(IGK_SESSION_CTRL);
		$n = igk_create_node('abtn');
		$n["class"] = "igk-btn";
		$n["href"] = $ctrl ? $ctrl->getUri("ClearS") . "&r=" . base64_encode($curi) : null;
		$n['igk-app-action'] = true;
		$n->Content = __("Clear session and reload");
		return $n;
	}
}
if (!function_exists("igk_html_node_actionbar")) {
	/**
	 * create winui-actionbar
	 * @param array|callable $actions array or 
	 */
	function igk_html_node_actionbar($actions = null)
	{
		$n = igk_create_node("div");
		$n->setClass("igk-action-bar");
		if ($actions) {
			if (is_callable(($actions))) {
				$tab = [$n];
				$tab = array_merge($tab, array_slice(func_get_args(), 1));
				$actions(...$tab);
			} else if (is_array($actions)) {
				foreach ($actions as $l => $v) {
					if (is_object($v) || is_array($v)) {
						$n->addABtn(igk_getv($v, "uri"))->setClass("igk-btn-default")->Content = __(igk_getv($v, "k"));
					} else {
						$n->addABtn('#')->setClass("igk-btn-default")->Content = __($v);
					}
				}
			}
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_actiongroup")) {
	/**
	 * utility helper create an action group .
	 * @return ActionGroupComponent 
	 */
	function igk_html_node_actiongroup()
	{
		return new \IGK\System\Html\Dom\Component\ActionGroupComponent();
	}
}
if (!function_exists("igk_html_node_actions")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_actions($actionlist)
	{
		$p = igk_html_parent_node() ?? igk_create_notagnode();
		$actionBar = $p->addActionBar();
		foreach ($actionlist as $k => $v) {
			$i = $actionBar->add("input");
			$i->setId($k);
			$t = igk_getv($v, "type", "button");
			$m = igk_getv($v, "value", $k);
			switch ($t) {
				default:
					$i["type"] = igk_getv($v, "type", "button");
					$i["class"] = "igk-btn";
					$i["value"] = $m;
					break;
			}
		}
		return $p;
	}
}
if (!function_exists("igk_html_node_address")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_address()
	{
		return new \IGK\System\Html\Dom\HtmlNode("address");
	}
}
if (!function_exists("igk_html_node_ajsbutton")) {
	/**
	 * create winui-ajsbutton
	 * @param mixed $code
	 * @param mixed $type
	 */
	function igk_html_node_ajsbutton($code, $type = 'default')
	{
		$n = igk_create_node("a");
		$n["class"] = "igk-btn igk-btn-" . $type;
		$n["onclick"] = "javascript: var igk=ns_igk; {$code}; return false;";
		$n["href"] = "#";
		return $n;
	}
}
if (!function_exists("igk_html_node_ajspickfile")) {
	/**
	 * auto generate doc.
	 * @param mixed $optionsJSON Options
	 */
	function igk_html_node_ajspickfile($u, $options = null)
	{
		$n = igk_create_node("a");
		$n["class"] = "igk-js-pickfile";
		$js = "{uri:'" . $u . "'";
		if ($options) {
			$js .= ", options:" . $options . "";
		}
		$js .= "}";
		$n["href"] = "#";
		$n["igk:data"] = $js;
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxa")) {
	/**
	 *  represent an ajx link. use to retrieve view from framework
	 * @param mixed $replacemode the content mode . value (content|node)
	 */
	function igk_html_node_ajxa($lnk = null, string $target = "", string $replacemode = 'content', string $method = "GET")
	{
		$dn = new HtmlNode("a");
		$dn->setAttribute("igk-ajx-lnk", 1);
		$dn["href"] = $lnk;
		$dn["igk:replacemode"] = !preg_match("/^(content|node)$/", $replacemode) ? null : (($replacemode == 'content') ? null : $replacemode);
		$dn["igk:method"] = $method != "GET" ? "POST" : null;
		$dn["igk:target"] = $target;
		return $dn;
	}
}
if (!function_exists("igk_html_node_ajxabutton")) {
	/**
	 * create winui-ajxabutton
	 * @param mixed $link
	 */
	function igk_html_node_ajxabutton($link)
	{
		$n = igk_html_node_ajxa($link);
		$n["class"] = "igk-btn";
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxappendto")) {
	/**
	 * append async content
	 * @param mixed $cibling
	 */
	function igk_html_node_ajxappendto($cibling)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-ajx-append-view";
		$n["igk:target"] = $cibling;
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxdoctitle")) {
	/**
	 * change the document code in ajx context
	 */
	function igk_html_node_ajxdoctitle($title)
	{
		$n = igk_create_node("balafonJS");
		$n->Content = "document.title = \"{$title}\";";
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxform")) {
	/**
	 * represent ajx form
	 */
	function igk_html_node_ajxform($uri = null, $target = null)
	{
		$f = igk_create_node("form");
		$f["action"] = $uri;
		$f["igk-ajx-form"] = 1;
		$f["igk-ajx-form-target"] = $target;
		return $f;
	}
}
if (!function_exists("igk_html_node_ajxlnkreplace")) {
	/**
	 * create winui-ajxlnkreplace
	 * @param mixed $target
	 */
	function igk_html_node_ajxlnkreplace($target = "::")
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-ajx-lnk-replace";
		$n["igk-lnk-target"] = $target;
		$n->setCallback("setTarget", "igk_html_callback_ajx_lnksettarget");
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxpaginationview")) {
	/**
	 * create winui-ajxpaginationview
	 * @param mixed $baseuri url 
	 * @param mixed $total total number of items
	 * @param mixed $perpage items per page
	 * @param mixed $selected selected page
	 * @param mixed $target cibling
	 */
	function igk_html_node_ajxpaginationview($baseuri, $total, $perpage, $selected = 1, $target = null)
	{
		return igk_html_node_paginationView($baseuri, $total, $perpage, $selected, 1, null, $target);
	}
}
if (!function_exists("igk_html_node_ajxpickfile")) {
	/**
	 * ajx div component used to load a file
	 * @param mixed $paramjson data. {accept:'image/*, text/xml', start:callback, progress:callback}
	 */
	function igk_html_node_ajxpickfile(string $uri, ?string $param = null)
	{
		$u = igk_create_node('input');
		$u["type"] = "file";
		$u->setClass("-cltext igk-ajx-pickfile");
		$u["igk:uri"] = $uri;
		$param && $u->setAttributes(["igk:data" => $param]);
		return $u;
	}
}
if (!function_exists("igk_html_node_ajxreplacecontent")) {
	/**
	 * create winui-ajxreplacecontent
	 * @param mixed $uri
	 * @param mixed $method
	 */
	function igk_html_node_ajxreplacecontent($uri, $method = "GET")
	{
		$n = igk_create_notagnode();
		$n->method = $method;
		$n->uri = $uri;
		$n->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, "igk_html_callback_replacecontent_acceptrender");
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxreplacesource")) {
	/**
	 * create winui-ajxreplacesource
	 * @param mixed $selection
	 */
	function igk_html_node_ajxreplacesource($selection)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-ajx-replace-source";
		$n["igk:data"] = $selection;
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxtabcomponent")) {
	/**
	 * add tab component
	 */
	function igk_html_node_ajxtabcomponent($host, $name)
	{
		$n = igk_create_node(
			HtmlComponents::Component,
			null,
			[
				$host,
				HtmlComponents::AJXTabControl,
				$name
			]
		);
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxtabcontrol")) {
	/**
	 * create an ajx tab control component
	 */
	function igk_html_node_ajxtabcontrol()
	{
		return new \IGK\System\Html\Dom\HtmlAJXTabControlNode();
	}
}
if (!function_exists("igk_html_node_ajxupdateview")) {
	/**
	 * create winui-ajxupdateview
	 * @param mixed $cibling
	 */
	function igk_html_node_ajxupdateview($cibling)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-ajx-update-view";
		$n["igk:target"] = $cibling;
		return $n;
	}
}
if (!function_exists("igk_html_node_ajxuriloader")) {
	/**
	 * Igk html node ajxuriloader.
	 * @param mixed $uri
	 * @param mixed $append
	 */
	function igk_html_node_ajxuriloader($uri, $append = 0)
	{
		$n = igk_create_node("div");
		$n->setAttribute("igk:href", $uri);
		$n["class"] = "igk-ajx-uri-loader";
		if ($append) {
			$n["igk:append"] = $append;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_apost")) {
	/**
	 * add a link that will do a post request
	 * @param mixed $uri 
	 * @return HtmlItemBase<mixed, mixed> 
	 * @throws IGKException 
	 */
	function igk_html_node_apost($uri)
	{
		$n = igk_create_node("a");
		$n["href"] = $uri;
		$n["class"] = "igk-winui-aform";
		$n["onclick"] = "javascript: ns_igk.form.posturi(this.href); return false;";
		return $n;
	}
}
if (!function_exists("igk_html_node_app_hearder_bar")) {
	/**
	 * application header bar
	 * @param BaseController $controller 
	 * @return HtmlNode 
	 */
	function igk_html_node_app_hearder_bar(BaseController $controller)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-app-header-bar displfex pad-4";
		$n->h1()->Content = $controller::title();
		return $n;
	}
}
if (!function_exists("igk_html_node_app_login_form")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_app_login_form(BaseController $controller, ?string $entryfname)
	{
		return $controller->getLoader()->getLayout()->loginForm();
	}
}
if (!function_exists("igk_html_node_apploginform")) {
	/**
	 * auto generate doc.
	 * @param mixed $goodUri the default value is null
	 */
	function igk_html_node_apploginform($app, $baduri = null, $goodUri = null)
	{
		igk_load_library("app_ctrl");
		$n = igk_create_node("div");
		igk_app_login_form($app, $n, $baduri, $goodUri);
		return $n;
	}
}
if (!function_exists("igk_html_node_arraydata")) {
	/**
	 * used to render data
	 */
	function igk_html_node_arraydata($tab)
	{
		if (!is_array($tab)) {
			igk_die("\$data must must be an array");
		}
		$n = igk_create_node("div");
		$n["class"] = "+igk-array-data";
		foreach ($tab as $k => $v) {
			$cv = $n->div()->setClass("r")->setStyle("display:table-row");
			$cv->div()->setClass("k")->setStyle("display:table-cell")->Content = $k;
			$cv->div()->setClass("v")->setStyle("display:table-cell")->addObData($v);
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_arraylist")) {
	/**
	 * create winui-arraylist
	 * @param mixed $list
	 * @param mixed $tag
	 * @param mixed $closurecallback
	 */
	function igk_html_node_arraylist($list, $tag = "li", $callback = null)
	{
		$n = igk_create_notagnode();
		if (!is_array($list))
			igk_die(__("list is not an array"), __FUNCTION__);
		foreach ($list as $k => $v) {
			$i = $n->add($tag);
			if (!$callback || !$callback($i, $k, $v))
				$i->Content = $v;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_article")) {
	/**
	 * auto generate doc.
	 * @param int $showAdminOption
	 * @return HtmlNode
	 */
	function igk_html_node_article(?BaseController $ctrl = null, ?string $name = null,  $raw = [], $showAdminOption = 1)
	{
		if (is_null($ctrl) && is_null($name)) {
			return new HtmlNode("article");
		}
		if ($ctrl === null) {
			$ctrl = igk_getctrl(\IGK\Controllers\SysDbController::class);
		}
		return igk_html_node_loadArticle($ctrl, $name, $raw, $showAdminOption);
	}
}
if (!function_exists("igk_html_node_loadArticle")) {
	/**
	 * Igk html node load article.
	 * @param BaseController $controller
	 * @param string $article_path
	 * @param mixed $raw
	 * @param bool $show_admin_option
	 */
	function igk_html_node_loadArticle(BaseController $controller, string $article_path, $raw = [], bool $show_admin_option = true)
	{
		$n = igk_html_node_notagnode();
		if (is_object($raw)) {
			$raw = (array)$raw;
		}
		igk_html_article($controller, trim($article_path), $n, $raw, null, true, true, $show_admin_option);
		return $n;
	}
}
if (!function_exists("igk_html_node_assertnode")) {
	/**
	 * auto generate doc.
	 * @param mixed $args
	 * @return HtmlAssertNode
	 */
	function igk_html_node_assertnode(bool $condition,  ...$args)
	{
		if (!($p = igk_html_parent_node())) {
			igk_die("assert node must be set to a parent");
		}
		return new \IGK\System\Html\Dom\HtmlAssertNode($condition, $p, ...$args);
	}
}
if (!function_exists("igk_html_node_attr_expression")) {
	// + attribute expression only use for child node
	/**
	 * Igk html node attr expression.
	 * @param null|mixed $p
	 */
	function igk_html_node_attr_expression($p = null)
	{
		if ($p == null) {
			$p = igk_html_parent_node();
		}
		$c = (array)HtmlReader::GetOpenerContext();
		$n = new HtmlAttribExpressionNode($p, $c);
		return $n;
	}
}
if (!function_exists("igk_html_node_author_community")) {
	/**
	 * render autocommunity node - system community link
	 * @return HtmlItemBase 
	 * @throws IGKException 
	 */
	function igk_html_node_author_community(?array $options = null)
	{
		$n = igk_create_node("div");
		$n->setClass("com-host")->CommunityLinks(IGK_AUTHOR_COMMUNITY_INFO, $options);
		return $n;
	}
}
if (!function_exists("igk_html_node_backgroundlayer")) {
	/**
	 * create winui-backgroundlayer
	 */
	function igk_html_node_backgroundlayer($imgPath = null)
	{
		$n = igk_create_node();
		$n["class"] = "igk-background-layer";
		if ($imgPath) {
			$n->addImg($imgPath);
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_badge")) {
	/**
	 * auto generate doc.
	 * @param mixed $v
	 */
	function igk_html_node_badge($v)
	{
		$n = igk_create_node("span");
		$n["class"] = "igk-winui-badge";
		$n->setContent($v);
		return $n;
	}
}
if (!function_exists("igk_html_node_balafonComponentJS")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_balafonComponentJS()
	{
		return new \IGK\System\Html\Dom\HtmlBalafonJSComponentNode();
	}
}
if (!function_exists("igk_html_node_balafonjs")) {
	/**
	 * create winui-balafonjs
	 * @param mixed $autoremove
	 */
	function igk_html_node_balafonjs($autoremove = 0)
	{
		$c = new \IGK\System\Html\Dom\HtmlBalafonJSNode();
		return $c;
	}
}
if (!function_exists("igk_html_node_bar")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_bar()
	{
		return new \IGK\System\Html\Dom\HtmlBarNode();
	}
}
if (!function_exists("igk_html_node_beforeRenderNextSibling")) {
	/**
	 * use to configure node before next cibling rendering 
	 * @param callback #Parameter#6fbcd033 
	 * @return \IGK\System\Html\Dom\HtmlBeforeRenderNextSiblingChildrenCallbackNode 
	 */
	function igk_html_node_beforeRenderNextSibling(callable $callback)
	{
		return new \IGK\System\Html\Dom\HtmlBeforeRenderNextSiblingChildrenCallbackNode($callback);
	}
}
if (!function_exists("igk_html_node_bindMenu")) {
	/**
	 * target the menuu display
	 * @param mixed $target 
	 * @return object 
	 * @throws ReflectionException 
	 * @throws Exception 
	 */
	function igk_html_node_bindMenu($target)
	{
		$m = igk_create_node('div');
		$m["igk-data-menu"] = 1;
		$m["igk-data-menu-binding"] = $target;
		return $m;
	}
}
if (!function_exists("igk_html_node_bindarticle")) {
	/**
	 * create winui-bindarticle
	 * @param mixed $ctrl
	 * @param mixed $name
	 * @param mixed $data
	 * @param mixed $showAdminOption
	 */
	function igk_html_node_bindarticle($ctrl, $name, $data = null, $showAdminOption = 1)
	{
		$n = igk_create_node();
		igk_html_binddata($ctrl, $n, $name, $data, true, true, $showAdminOption);
		return $n;
	}
}
if (!function_exists("igk_html_node_bindcontent")) {
	/**
	 * create winui-bindcontent
	 * @param mixed $content
	 * @param mixed $entries
	 * @param mixed $ctrl
	 */
	function igk_html_node_bindcontent($content, $entries, $ctrl = null)
	{
		$n = igk_html_node_notagnode();
		$n->Content = igk_html_bind_content($ctrl, $content, $entries);
		return $n;
	}
}
if (!function_exists("igk_html_node_bindscript")) {
	/**
	 * auto generate doc.
	 * @param null|bool $production
	 * @return null|HtmlItemBase
	 */
	function igk_html_node_bindscript($data, $uri, $name, ?bool $production = null)
	{
		$p = igk_html_parent_node();
		if (is_object($data) && ($data instanceof \IGK\Controllers\BaseController)) {
			$data = [
				$data->getScriptsDir()
			];
		}
		$fc = function () use ($data, $uri, $name, $production) {
			echo \IGK\System\Html\Dom\HtmlScriptLoader::LoadScripts(
				$data,
				$uri,
				$name,
				$production == null ? igk_environment()->isOPS() : $production
			);
		};
		if ($p) {
			$p->obdata($fc);
			igk_html_skip_add();
			return null;
		}
		return igk_create_node("obdata", null, $fc);
	}
}
if (!function_exists("igk_html_node_blocknode")) {
	/**
	 * create winui-blocknode
	 */
	function igk_html_node_blocknode()
	{
		$n = igk_create_xmlnode("igk-block-viewitem");
		$n->setCallback(CallableConstants::IS_VISIBLE_METHOD, "return \$this->HasChilds ;");
		$n->setCallback(CallableConstants::CAN_RENDER_TAG_METHOD, "return false;");
		return $n;
	}
}
if (!function_exists("igk_html_node_bodybox")) {
	/**
	 * create winui-bodybox
	 */
	function igk_html_node_bodybox()
	{
		$n = igk_create_node();
		$n["class"] = "igk-bodybox fit igk-parentscroll igk-powered-viewer overflow-y-a";
		return $n;
	}
}
if (!function_exists("igk_html_node_boxdialog")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_boxdialog()
	{
		$n = igk_create_node("div");
		$n->setClass("igk-dialog");
		return $n;
	}
}
if (!function_exists("igk_html_node_btn")) {
	/**
	 * create winui-btn
	 * @param mixed $name
	 * @param mixed $value
	 * @param mixed $type
	 * @param mixed $attributes
	 */
	function igk_html_node_btn($name, $value, $type = "submit", $attributes = null)
	{
		$btn = new HtmlNode("input");
		$btn["id"] = $btn["name"] = $name;
		$btn["value"] = $value;
		$btn["type"] = $type;
		$btn["class"] = "cl" . $type;
		return $btn;
	}
}
if (!function_exists("igk_html_node_buildselect")) {
	/**
	 * build select node
	 */
	function igk_html_node_buildselect($name, $rows, $idk, $callback = null, $selected = null)
	{
		$sl = igk_create_node("select")->setId($name)->setClass("igk-form-control");
		foreach ($rows as  $v) {
			$opt = $sl->add("option");
			$opt["value"] = $v->$idk;
			$opt->Content = $callback ? $callback($v) : $v;
			if ($selected && ($v->$idk == $selected)) {
				$opt["selected"] = 1;
			}
		}
		return $sl;
	}
}
if (!function_exists("igk_html_node_bullet")) {
	/**
	 * create winui-bullet
	 */
	function igk_html_node_bullet()
	{
		$n = igk_create_node();
		$n->setClass("igk-bullet");
		return $n;
	}
}
if (!function_exists("igk_html_node_button")) {
	/**
	 * create a button
	 * @var string $id id for the button
	 * @var string $buttontype type submit|button
	 * @var string $type class type
	 */
	function igk_html_node_button(?string $id = null, $button_type_or_content = 0, ?string $type = null)
	{
		$n = new HtmlNode("button");
		$n["class"] = "igk-btn";
		if ($type)
			$n["class"] = "+igk-btn-{$type}";
		if (is_int($button_type_or_content))
			$n["type"] = igk_getv([1 => 'submit'], $button_type_or_content, 'button');
		else {
			$n->setContent($button_type_or_content);
		}
		// + | set id and name
		$n->setId($id);
		return $n;
	}
}
if (!function_exists("igk_html_node_canvabalafonscript")) {
	/**
	 * create winui-canvabalafonscript
	 * @param mixed $uri
	 */
	function igk_html_node_canvabalafonscript($uri = null)
	{
		$n = igk_create_node();
		$n["class"] = "igk-canva-gkds-obj";
		$n["uri"] = $uri;
		$n["igk-canva-gkds-obj-data"] = "{uri:'{$uri}',init:function(ctx){ctx.strokeStyle = 'transparent';ctx.fillStyle = this.getComputedStyle('color'); }}";
		$n->Content = "&nbsp;";
		return $n;
	}
}
if (!function_exists("igk_html_node_canvaeditorsurface")) {
	/**
	 * create a canva editor surface
	 */
	function igk_html_node_canvaeditorsurface()
	{
		$n = igk_create_node();
		$n->setClass("igk-canva-editor");
		return $n;
	}
}
if (!function_exists("igk_html_node_cardid")) {
	/**
	 * create winui-cardid
	 * @param mixed $src
	 * @param mixed $ctrl
	 */
	function igk_html_node_cardid($src = null, $ctrl = null)
	{
		$n = igk_create_node("div");
		$n->setClass("igk-card-id");
		if ($src) {
			if (!IGKValidator::IsUri($src)) {
				if (igk_io_file_exists($src)) {
					$src = new IGKHtmlRelativeUriValueAttribute(igk_io_baseRelativeUri($src));
				} else
					$src = new IGKHtmlRelativeUriValueAttribute(igk_io_baseRelativeUri(dirname($ctrl->getDeclaredFileName()) . "/" . $src));
			}
		}
		$n["igk:link"] = $src;
		return $n;
	}
}
if (!function_exists("igk_html_node_carousel")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_carousel()
	{
		$n = new  \IGK\System\Html\Dom\HtmlCarouselNode("div");
		$n["class"] = "igk-winui-carousel";
		if (igk_environment()->isDev()) {
			$nav = $n->nav();
			$nav->li();
			$nav->li()->setClass("igk-active");
			$nav->li();
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_cell")) {
	/**
	 * create winui-cell
	 */
	function igk_html_node_cell()
	{
		$n = igk_create_node();
		$n["class"] = "disptabc";
		return $n;
	}
}
if (!function_exists("igk_html_node_cellrow")) {
	/**
	 * create winui-cellrow
	 */
	function igk_html_node_cellrow()
	{
		$n = igk_create_node();
		$n["class"] = "igk-cell-row";
		return $n;
	}
}
if (!function_exists("igk_html_node_centerbox")) {
	/**
	 * create winui-centerbox
	 * @param mixed $content
	 */
	function igk_html_node_centerbox($content = null)
	{
		return new IGK\System\Html\Dom\HtmlCenterBoxNode($content);
	}
}
if (!function_exists("igk_html_node_checkbox")) {
	/**
	 * create a web checkbox
	 * @param string $id id that identify the checkbox
	 * @param mixed $value value that need to referer the when checkbox is active
	 */
	function igk_html_node_checkbox(string $id, $value = null)
	{
		$n = igk_create_node('input');
		$n["type"] = "checkbox";
		$n->setId($id);
		$n["value"] = $value;
		return $n;
	}
}
if (!function_exists("igk_html_node_circlewaiter")) {
	/**
	 * create winui-circlewaiter
	 */
	function igk_html_node_circlewaiter()
	{
		$n = igk_create_node();
		$n->setClass("igk-circle-waiter");
		return $n;
	}
}
if (!function_exists("igk_html_node_clearboth")) {
	/**
	 * auto generate doc.
	 */	function igk_html_node_clearboth()
	{
		$n = igk_create_node("div");
		$n["style"] = "clear:both;";
		return $n;
	}
}
if (!function_exists("igk_html_node_clearfloatbox")) {
	/**
	 * create winui-clearfloatbox
	 * @param mixed $t
	 */
	function igk_html_node_clearfloatbox($t = 'b')
	{
		$n = igk_create_node("br");
		$n->setClass("clear" . $t);
		return $n;
	}
}
if (!function_exists("igk_html_node_cleartab")) {
	/**
	 * create winui-cleartab
	 */
	function igk_html_node_cleartab()
	{
		$n = igk_create_node();
		$n["class"] = "igk-cleartab";
		return $n;
	}
}
if (!function_exists("igk_html_node_clone")) {
	/**
	 * helper: class dom element at location 
	 */
	function igk_html_node_clone(string $target)
	{
		$n = igk_create_node();
		$n['class'] = 'igk-winui-clone';
		$n["igk-target"] = $target;
		return $n;
	}
}
if (!function_exists("igk_html_node_clonenode")) {
	/**
	 * create winui-clonenode
	 * @param mixed $node
	 */
	function igk_html_node_clonenode(HtmlItemBase $node, $children = false)
	{
		$n = new \IGK\System\Html\Dom\HtmlCloneNode($node);
		$n->setForChildren($children);
		return $n;
	}
}
if (!function_exists("igk_html_node_code")) {
	/**
	 * create base php code
	 */
	function igk_html_node_code($type = 'php')
	{
		$n = new \IGK\System\Html\Dom\HtmlCodeNode();
		$n["class"] = "igk-code " . ($type ? "code-" . $type : "");
		return $n;
	}
}
if (!function_exists("igk_html_node_col")) {
	/**
	 * create winui-col
	 * @param mixed $clname
	 */
	function igk_html_node_col($clname = null)
	{
		if ($clname) {
			$clname = " " . $clname;
		}
		return igk_create_node("div")->setAttributes(array("class" => "igk-col" . $clname));
	}
}
if (!function_exists("igk_html_node_colviewbox")) {
	/**
	 * column view item
	 */
	function igk_html_node_colviewbox()
	{
		$n = igk_create_node("div");
		$n->setClass("igk-col-view-box");
		return $n;
	}
}
if (!function_exists("igk_html_node_combobox")) {
	/**
	 * auto generate doc.
	 * @param mixed $options the default value is null
	 */
	function igk_html_node_combobox($id, $tab, $options = null)
	{
		$n = igk_create_node("select")->setId($id);
		$n["class"] = "igk-winui-combobox";
		igk_html_build_select_option($n, $tab, $options ?? (object)[
			"valuekey" => "value",
			"displaykey" => "text"
		]);
		return $n;
	}
}
if (!function_exists("igk_html_node_comment")) {
	/**
	 * create comment node
	 * @return HtmlCommentNode 
	 */
	function igk_html_node_comment(?string $content = null)
	{
		return new HtmlCommentNode($content);
	}
}
if (!function_exists("igk_html_node_communitylink")) {
	/**
	 * create winui-communitylink
	 * @param mixed $name
	 * @param mixed $link
	 */
	function igk_html_node_communitylink($name, $link)
	{
		$s = igk_create_node("div");
		$s["class"] = "igk-comm-lnk";
		$s["igk:title"] = $name;
		$s["href"] = $link;
		return $s;
	}
}
if (!function_exists("igk_html_node_communitylinks")) {
	/**
	 * create winui-communitylinks
	 * @param mixed $tab
	 */
	function igk_html_node_communitylinks($tab, ?array $options = null)
	{
		$symbols = igk_getv($options, "symbols");
		$ul = igk_create_node("ul")->setClass("igk-com-links");
		if ($tab) {
			foreach ($tab as $k => $v) {
				$svg_symbol = $k;
				if ($symbols) {
					$svg_symbol = igk_getv($symbols, $k, $k);
				}
				if (is_object($v) || is_array($v)) {
					$uri = igk_getv($v, "uri");
					if (($c = igk_getv($v, "auth")) && (is_callable($c) && (!$c()))) {
						continue;
					}
					$svg_symbol = igk_getv($v,  "icons");
				} else {
					$uri = $v;
				}
				$a = $ul->li()->a($uri)
					->setAttribute("target", "__blank")
					->setAttribute("alt", sprintf(__("%s social community"), $k));
				$a->setClass($k)->Content = igk_svg_use($svg_symbol);
			}
		}
		return $ul;
	}
}
if (!function_exists("igk_html_node_component")) {
	/**
	 * used to create component
	 */
	function igk_html_node_component($listener, $typename, $regName, $unregister = 0)
	{
		if ($unregister) {
			$b = $listener->getParam(IGK_NAMED_NODE_PARAM);
			$h = igk_getv($b, $regName);
			if ($h) {
				$h->dispose();
				unset($b[$regName]);
				$listener->setParam(IGK_NAMED_NODE_PARAM, $b);
			}
		}
		return igk_html_node_livenodecallback($listener, $regName, function ($l, $n) use ($typename) {
			$c = HtmlWebComponentNode::CreateComponent($typename);
			$c->setComponentListener($l, $l->getParam("sys://component/params/{$n}"));
			return $c;
		});
	}
}
if (!function_exists("igk_html_node_conditionalnode")) {
	/**
	 * create a node that will only be visible on conditional callback is evaluted to true
	 */
	function igk_html_node_conditionalnode($conditioncallback)
	{
		$n = igk_create_node(__FUNCTION__);
		$n->setCallback(CallableConstants::CAN_RENDER_TAG_METHOD, "return false;");
		$n->setCallback(CallableConstants::IS_VISIBLE_METHOD, "igk_html_visibleConditionalNode");
		return $n;
	}
}
if (!function_exists("igk_html_node_configsubmenu")) {
	/**
	 * auto generate doc.
	 * @param mixed $selected
	 */
	function igk_html_node_configsubmenu($menuList, $selected)
	{
		$ul = igk_create_node("ul")->setClass("igk-cnf-content_submenu");
		foreach ($menuList as $k => $v) {
			$li = $ul->li();
			$li->add("a", array("href" => $v))->Content = __("cl" . $k);
			if ($selected == $k) {
				$li["class"] = "+igk-cnf-content_submenu_selected";
			} else {
				$li["class"] = "-igk-cnf-content_submenu_selected";
			}
		}
		return $ul;
	}
}
if (!function_exists("igk_html_node_container")) {
	/**
	 * create winui-container
	 */
	function igk_html_node_container()
	{
		$n = igk_create_node('div');
		$n["class"] = "igk-container";
		return $n;
	}
}
if (!function_exists("igk_html_node_containerRowCol")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_containerRowCol($style = "")
	{
		$p = igk_html_parent_node();
		$n = $p->container()->addRow()->addCol($style);
		return ["node" => $n];
	}
}
if (!function_exists("igk_html_node_cookiewarning")) {
	/**
	 * create winui-cookiewarning
	 * @param mixed $warnurl
	 */
	function igk_html_node_cookiewarning($warnurl = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-cookie-warn";
		$n["igk-domain-ewarn"] = "local.com.ewarn";
		$n->Content = __("By using this site, you agree with cookies usage. you can erase or stop them in your navigation parameters." . "<a href=\"" . $warnurl . "\">" . __("For more infos") . "</a>");
		$n->div()->setClass("close")->addABtn("#")->Content = igk_svg_use("drop");
		$n->setCallback(CallableConstants::IS_VISIBLE_METHOD, "return igk_get_global_cookie(\"local.com.warn\", 0)!=0;");
		return $n;
	}
}
if (!function_exists("igk_html_node_copyright")) {
	/**
	 * create winui-copyright
	 * @param mixed $ctrl
	 */
	function igk_html_node_copyright($ctrl = null)
	{
		$n = igk_create_node("div");
		$n->setClass("igk-copyright");
		$n->setCallback("getCopyright", igk_create_func_callback("igk_html_code_copyright_callback", [$ctrl]));
		$n->Content = new \IGK\ValueListener($n, "getCopyright");
		return $n;
	}
}
if (!function_exists("igk_html_node_csscomponentstyle")) {
	/**
	 * create winui-csscomponentstyle
	 * @param mixed $file
	 * @param mixed $host
	 */
	function igk_html_node_csscomponentstyle($file, $host = null)
	{
		$n = IGKCssComponentStyle::getInstance()->regFile($file, $host);
		return $n;
	}
}
if (!function_exists("igk_html_node_csslink")) {
	/**
	 *  add css link
	 * @param mixed $hrefmixed : string, object(that implement getValue function and refName properties) or array
	 */
	function igk_html_node_csslink($href, $temp = 0, $defer = 0)
	{
		$p = igk_html_parent_node();
		if ($p && (strtolower($p->TagName) !== "head"))
			igk_die("/!\\ can't add css link to non header. " . strtolower($p->TagName) . " " . get_class($p));
		$key = "sys://cssLink/" . __FUNCTION__;
		$g = null;
		$g = $p->getParam($key) ?? new HtmlHeaderLinkHost();
		$key_ref = $href;
		$b = $href && !is_string($href) ? igk_getv($href, "callback") : null;
		if (is_callable($b)) {
			($key_ref = igk_getv($href, "refName")) || igk_die(__("refName required"));
			$m = $g->getLink($key_ref);
			if ($m)
				return $m;
			$mtrep = igk_getv($href, "params");
			$m = new HtmlNode("link");
			$mtrep[] = $m;
			$uri = call_user_func_array($b, $mtrep);
			$m->setAttribute("href", $uri);
			$m->setAttribute("rel", "stylesheet");
			if ($defer)
				$m->activate("defer");
			$g->add($key_ref, $m, $temp);
			$p->setParam($key, $g);
			return $m;
		} else {
			if (is_array($href)) {
				$key_ref = igk_getv($href, "refName") ?? igk_die(__("Css Link reference 'refName' not found in array"));
			} else if (is_object($href)) {
				$key_ref = igk_getv($href, "refName") ?? igk_die(__("Css Link reference 'refName' not found not found in object"));
			}
			$m = $g->getLink($key_ref);
			if ($m)
				return $m;
		}
		$m = new HtmlNode("link");
		$m->setAttribute("href", new IGKHtmlRelativeUriValueAttribute($key_ref));
		$m->setAttribute("rel", "stylesheet");
		if ($defer)
			$m->activate("defer");
		$g->add($key_ref, $m, $temp);
		$p->setParam($key, $g);
		return $m;
	}
}
if (!function_exists("igk_html_node_cssstyle")) {
	/**
	 * represent a css style element
	 */
	function igk_html_node_cssstyle($id, $minfile = 1)
	{
		$o = igk_html_parent_node();
		$k = __FUNCTION__ . ":/{$id}";
		if ($o) {
			$g = $o->getParam($k);
			if ($g != null)
				return $g;
		}
		$s = igk_create_node("style");
		$s["type"] = "text/css";
		$s->minfile = $minfile;
		$s->setCallback("handleRender", "igk_html_handle_cssStyle");
		$o->setParam($k, $s);
		return $s;
	}
}
if (!function_exists("igk_html_node_ctrlview")) {
	/**
	 * create winui-ctrlview
	 * @param mixed $viewview to show
	 * @param mixed $ctrlcontroller that will handle the view
	 * @param mixed $paramsparams to pas to views
	 */
	function igk_html_node_ctrlview($view, $ctrl, $params = null)
	{
		$n = igk_create_notagnode();
		$n->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, "igk_html_callback_ctrlview_acceptrender");
		$n->setParam("data", (object)array("view" => $view, "ctrl" => $ctrl, "params" => $params));
		return $n;
	}
}
if (!function_exists("igk_html_node_dataschema")) {
	/**
	 * create data schema
	 * @return XmlNode 
	 */
	function igk_html_node_dataschema()
	{
		$n = new XmlNode(IGK_SCHEMA_TAGNAME);
		return $n;
	}
}
if (!function_exists("igk_html_node_dbTableView")) {
	/**
	 * create table view node
	 * @param mixed db results
	 * @param mixed $theader 
	 */
	function igk_html_node_dbTableView($tabResult, $theader = null, $header_prefix = "header.")
	{
		$n = igk_create_notagnode();
		$is_filter = $theader instanceof \IGK\System\Views\IDbTableViewFilter;
		$is_def = !is_null($theader);
		if (empty($tabResult)) {
			$n->div()->Content = __("No Result");
		} else {
			if (!is_array($tabResult)) {
				$tabResult = [$tabResult];
			}
			$table = $n->table();
			$header = null;
			$header_node = null;
			foreach ($tabResult as $r) {
				if (!$r)
					continue;
				if ($r instanceof IDbArrayResult) {
					$r = $r->to_array(true);
				} else if (!is_array($r)) {
					$r = (array)$r;
				}
				if (is_null($header)) {
					$header = [];
					$header_node = $table->tr();
					if (empty($theader)) {
						$_lheader = array_keys($r);
					} else if ($is_filter) {
						$_lheader = $theader->getHeaderList($r);
					} else {
						if (is_string($theader)) {
							$theader = explode('|', $theader);
						}
						$_lheader = $theader;
					}
					foreach ($_lheader as $k => $v) {
						if (empty($k)) {
							$header[] = $k;
							$header_node->th()->nbsp();
							continue;
						}
						if (is_array($v)) {
							if (($ctr = Activator::CreateNewInstance(\IGK\System\Html\Components\DbTableViewHeaderInfo::class, $v))
								instanceof  \IGK\System\Html\Components\DbTableViewHeaderInfo
							) {
								if (is_numeric($k)) {
									$k = $ctr->key;
								}
								$header[$k] = $r->title_label ?? $k;
								$header_node->th()->Content =  $ctr->title_label;
							}
						} else {
							$header[$k] = $k;
							$header_node->th()->Content = $is_def ? $k : __($header_prefix . $k);
						}
					}
				}
				$c = $table->tr();
				foreach ($header as $k => $v) {
					if (empty($v)) {
						$c->td()->nbsp();
						continue;
					}
					$m = igk_getv($r, $k);
					if ($is_filter) {
						$theader->filter($k, $m, $c->td());
					} else {
						if (is_object($m)) {
							if ($m  instanceof ModelBase) {
								$c->td()->Content = " [model]";
							} else {
								$c->td()->Content = " [object] ";
							}
						} else {
							$c->td()->Content = $m;
						}
					}
				}
			}
			return $table;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_dbdataschema")) {
	/**
	 * create a data base schema node 
	 */
	function igk_html_node_dbdataschema()
	{
		$rep = igk_create_xmlnode(IGK_SCHEMA_TAGNAME);
		$rep["date"] = date('Y-m-d');
		$rep["version"] = "1.0";
		return $rep;
	}
}
if (!function_exists("igk_html_node_dbentriescallback")) {
	/**
	 * create winui-dbentriescallback
	 * @param mixed $target
	 * @param mixed $closurecallback
	 * @param mixed $queryResult
	 * @param mixed $fallback
	 */
	function igk_html_node_dbentriescallback($target, $callback, $queryResult, $fallback = null)
	{
		if ($queryResult && ($queryResult->RowCount > 0)) {
			foreach ($queryResult->Rows as $v) {
				$target->add($callback($v));
			}
		} else {
			if ($fallback) {
				$c = $fallback();
				if ($c)
					$target->add($c);
			}
		}
		return $target;
	}
}
if (!function_exists("igk_html_node_dbresult")) {
	/**
	 * create winui-dbresult
	 * @param mixed $r
	 * @param mixed $max
	 */
	function igk_html_node_dbresult($r, $uri, $selected, $max = -1, $target = null)
	{
		$n = igk_create_notagnode();
		if ($r)
			$n->add(igk_db_view_result_node($r, $uri, $selected, $max, $target));
		return $n;
	}
}
if (!function_exists("igk_html_node_dbselect")) {
	/**
	 * DataBase select component
	 */
	function igk_html_node_dbselect($name, $result, $callback = null, $valuekey = IGK_FD_ID)
	{
		$n = new HtmlNode("select");
		$n->setClass("clselect");
		$n->setId($name);
		$callback = $callback === null ? igk_get_env("sys://table/" . igk_getv(array_keys($result->getTables()), 0)) : $callback;
		foreach ($result->Rows as  $v) {
			$g = $n->add("option");
			$g->setAttribute("value", $v->$valuekey);
			if ($callback !== null) {
				if (is_callable($callback)) {
					$g->setContent($callback($v, $g));
					continue;
				}
			}
			$g->setContent(igk_display($v));
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_defercsslink")) {
	/**
	 * defer css link loading
	 * @param mixed $href 
	 * @return null 
	 * @throws IGKException 
	 */
	function igk_html_node_defercsslink($href)
	{
		$p = igk_html_parent_node();
		$key = "sys://cssLink/" . __FUNCTION__;
		$b = $href && !is_string($href) ? igk_getv($href, "callback") : null;
		$uri = "";
		$v_m_keys = __FUNCTION__;
		if (!($scriptLoading = $p->getParam($key))) {
			$scriptLoading = igk_html_node_onrendercallback(function ($options = null) use (&$p, $key, $v_m_keys) {
				$i = $p->getParam($key);
				$sc = $i->getParam($v_m_keys);
				$o = "";
				if ($tm = array_keys($sc)) {
					$o .= "<script type=\"text/javascript\" language=\"javascript\">";
					$o .= "(function(igk){ igk && igk.ready(function(){ if(!igk ||!igk.css.loadLinks)return;igk.ready(function(){ igk.css.loadLinks(" .
						json_encode($tm, JSON_UNESCAPED_SLASHES)
						. ");});}); })(window.igk)";
					$o .= "</script>";
				}
				$i->load($o);
				return 1;
			});
			$p->setParam($key, $scriptLoading);
			$p->add($scriptLoading);
		}
		if (!($lm = $scriptLoading->getParam($v_m_keys))) {
			$lm = [];
		}
		if (is_callable($b)) {
			$mtrep = igk_getv($href, "params");
			if ($uri = call_user_func_array($b, $mtrep)) {
				$s = $uri->getValue();
				$lm[$s] = $uri;
			}
		} else {
			$lm[$href] = $href;
		}
		$scriptLoading->setParam($v_m_keys, $lm);
		return null;
	}
}
if (!function_exists("igk_html_node_definition")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_definition($title, $def)
	{
		$d = igk_create_node("dl");
		$d->dt()->Content = $title;
		$d->dd()->Content = $def;
		return $d;
	}
}
if (!function_exists("igk_html_node_definitions")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_definitions($args)
	{
		if ($q = igk_html_parent_node()) {
			foreach ($args as $defs) {
				if (($t = igk_getv_fallback($defs, "t|title")) &&
					($def = igk_getv_fallback($defs, "d|def"))
				) {
					$q->definition($t, $def);
				}
			}
		}
		return $q;
	}
}
if (!function_exists("igk_html_node_winui_dialog")) {
	/**
	 *  create a dialog host that will not being displayed<
	 * @param mixed $title
	 */
	function igk_html_node_winui_dialog($title = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-dialog dispn";
		$n["igk:title"] = $title;
		return $n;
	}
}
if (!function_exists("igk_html_node_dialog_circle_waiter")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_dialog_circle_waiter()
	{
		$bar = igk_create_node("boxdialog")->setClass("igk-dialog");
		$bar->div()->setClass("flex fit flex-a-center")->circlewaiter();
		return $bar;
	}
}
if (!function_exists("igk_html_node_dialogbox")) {
	/**
	 * create a dialog box
	 * @param mixed $title
	 */
	function igk_html_node_dialogbox($title)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-dialogbox";
		$t = $n->div()->setClass("title");
		$t->div()->setClass("cls")->addSvgSymbol("close_btn_2");
		$t->addSectionTitle(4)->Content = $title;
		$t->div()->setClass("opts")->addSvgSymbol("v_dot_3");
		return $n;
	}
}
if (!function_exists("igk_html_node_dialogboxcontent")) {
	/**
	 *  create a dialog box content
	 */
	function igk_html_node_dialogboxcontent()
	{
		$n = igk_create_node("div");
		$n["class"] = "dialog-c";
		return $n;
	}
}
if (!function_exists("igk_html_node_dialogboxoptions")) {
	/**
	 * create dialogbox options node
	 */
	function igk_html_node_dialogboxoptions()
	{
		$o = igk_html_parent_node();
		$k = "sys://component/" . __FUNCTION__;
		$s = $o->getParam($k);
		if ($s === null) {
			$n = igk_create_node("ul");
			$n["class"] = "d-opts dispn";
			$o->getParam($k, $n);
			return $n;
		}
		return $s;
	}
}
if (!function_exists("igk_html_node_divcontainer")) {
	/**
	 * create winui-divcontainer
	 * @param mixed $attribs
	 */
	function igk_html_node_divcontainer($attribs = null)
	{
		$n = igk_create_node();
		$n->container();
		return $n;
	}
}
if (!function_exists("igk_html_node_dl")) {
	/**
	 * helper: create a Document list node 
	 */
	function igk_html_node_dl()
	{
		return new \IGK\System\Html\Dom\HtmlDocumentListNode();
	}
}
if (!function_exists("igk_html_node_domainlink")) {
	/**
	 * create winui-domainlink
	 * @param mixed $src
	 */
	function igk_html_node_domainlink($src)
	{
		$n = igk_create_node("a");
		$n->domainLink = 1;
		$n["href"] = $src;
		$n->setParam("lnk", $src);
		return $n;
	}
}
if (!function_exists("igk_html_node_dumpdata")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_dumpdata($data)
	{
		$n = igk_create_notagnode();
		$n->obdata(function () use ($data) {
			var_dump($data);
		});
		return $n;
	}
}
if (!function_exists("igk_html_node_enginecontrol")) {
	/**
	 * engine control editor
	 */
	function igk_html_node_enginecontrol($name, $type)
	{
		$p = igk_html_parent_node();
		$engine = igk_get_builder_engine($name, $p);
		$c = "add" . $type;
		call_user_func_array(array($engine, $c), array_slice(func_get_args(), 2));
		return $engine;
	}
}
if (!function_exists("igk_html_node_error404")) {
	/**
	 * create winui-error404
	 * @param mixed $title
	 * @param mixed $m
	 */
	function igk_html_node_error404($title, $m)
	{
		$n = igk_create_node("div");
		$n["class"] = "error404";
		igk_html_title($n, $title);
		$box = $n->addPanelBox();
		$box->add($m);
		$n->Box = $box;
		return $n;
	}
}
if (!function_exists("igk_html_node_expo")) {
	/**
	 * create winui-expo
	 */
	function igk_html_node_expo()
	{
		$n = igk_create_node("span");
		$n["class"] = "igk-expo";
		return $n;
	}
}
if (!function_exists("igk_html_node_expression_node")) {
	/**
	 * auto generate doc.
	 * @param mixed $ctrl the default value is null
	 */
	function igk_html_node_expression_node($raw, $ctrl = null)
	{
		$ctx = HtmlReader::GetOpenerContext();
		if ($ctrl === null) {
			if ($g = igk_get_env("sys:://expression_context")) {
				$ctrl = $g->ctrl;
			}
		}
		$n = new \IGK\System\Html\Dom\HtmlExpressionNode($raw, $ctrl, $ctx);
		return $n;
	}
}
if (!function_exists("igk_html_node_fields")) {
	/**
	 * load field list to parent
	 * @param array|IFormFields $fielddata
	 * @param mixed $datasource 
	 * @param null|object $engine to use 
	 * @param string $tagname
	 * @return mixed 
	 * @throws IGKException 
	 */
	function igk_html_node_fields($fielddata, $datasource = null, ?object $engine = null, ?string $tag = null)
	{
		if ($fielddata instanceof IFormFields) {
			$v_f = $fielddata;
			$fielddata = $v_f->getFields();
			$datasource = $datasource ?? $v_f->getDataSource();
			$engine = $engine ?? $v_f->getEngine();
			$tag = $tag ?? $v_f->getTag();
		} else if ($fielddata instanceof IFormFieldContainer) {
			$fielddata = $fielddata->getFields();
		}
		$o = igk_html_parent_node() ?? igk_die('require parent node context');
		$a = $fielddata;
		$o->addObData(function () use ($a, $datasource, $engine, $tag) {
			igk_html_form_fields($a, $datasource, 1, $engine, $tag);
		}, IGK_HTML_NOTAG_ELEMENT);
		return $o;
	}
}
if (!function_exists("igk_html_node_fixedactionbar")) {
	/**
	 *  a BJS's class control. used to show on scroll visibility.
	 */
	function igk_html_node_fixedactionbar($targetid = "", $offset = 1)
	{
		$n = igk_create_node("div");
		$n->setClass("igk-fixed-action-bar");
		$n->setAttribute("igk-offset", $offset);
		$n["igk-target"] = $targetid;
		return $n;
	}
}
if (!function_exists("igk_html_node_fontsymbol")) {
	/**
	 * create a font symbol.
	 */
	function igk_html_node_fontsymbol($name, $code)
	{
		$n = igk_create_node("span");
		$n["class"] = "+igk-font-symbol " . "ft-" . $name;
		$o = "";
		if (is_string($code)) {
			$code = trim($code);
			if (preg_match("/^(0x|#)/", $code)) {
				$code = preg_replace_callback(
					"/^(0x|#)/",
					function () {
						return "";
					},
					$code
				);
			}
			$o = '&#x' . $code . ';';
		} else {
			$o = '&#x' . Number::ToBase($code, 16, 4) . ';';
		}
		$n->Content = $o;
		return $n;
	}
}
if (!function_exists("igk_html_node_form")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_form(?string $uri = ".", string $method = "POST", bool $notitle = false, bool $nofoot = false)
	{
		$c = new \IGK\System\Html\Dom\HtmlFormNode($uri, $method, $notitle, $nofoot);
		return $c;
	}
}
if (!function_exists("igk_html_node_form_post")) {
	/**
	 * post urit using data form
	 * @param string $uri 
	 * @return HtmlANode<mixed, mixed> 
	 * @throws IGKException 
	 */
	function igk_html_node_form_post(string $uri)
	{
		$a = igk_html_node_a();
		if (empty($complete)) {
			$complete = 'null';
		}
		$a->on("click", "igk.form.posturi('" .
			$uri
			. "'); return false;");
		return $a;
	}
}
if (!function_exists("igk_html_node_formactionbutton")) {
	/**
	 * create winui-formactionbutton
	 * @param mixed $id
	 * @param mixed $value
	 * @param mixed $uri
	 * @param mixed $method
	 * @param mixed $text
	 */
	function igk_html_node_formactionbutton($id, $value, $uri, $method = "GET", $text = null)
	{
		$f = igk_create_node("form");
		$f["action"] = $uri;
		$f->addButton($id, 1)->Content = $text ?? __("btn." . $id);
		return $f;
	}
}
if (!function_exists("igk_html_node_formcref")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_formcref()
	{
		$n = igk_create_notagnode();
		$n->addNoTagObData(function () {
			igk_html_form_init();
		});
		return $n;
	}
}
if (!function_exists("igk_html_node_formfields")) {
	/**
	 * auto generate doc.
	 * @param mixed $engine the default value is null
	 */
	function igk_html_node_formfields($formfields, $engine = null)
	{
		$n = igk_html_node_notagnode();
		if ($engine == null) {
			$o = igk_html_utils_buildformfield($formfields);
			$n->addText($o);
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_formgroup")) {
	/**
	 * auto generate doc.
	 */	function igk_html_node_formgroup()
	{
		$n = igk_create_node('div');
		$n["class"] = "igk-form-group";
		return $n;
	}
}
if (!function_exists("igk_html_node_formusagecondition")) {
	/**
	 * create winui-formusagecondition
	 */
	function igk_html_node_formusagecondition()
	{
		$dd = igk_create_node();
		$dd->setClass("disptable fitw");
		$dd->div()->setClass("disptabc")->addInput("clAcceptCondition", "checkbox")->setAttribute("checked", 1);
		$dd->div()->setClass("disptabc fitw")->div()->add("label")->setAttribute("for", "clAcceptCondition")->setStyle("padding-left:10px")->Content = new HtmlUsageCondition();
		return $dd;
	}
}
if (!function_exists("igk_html_node_frame")) {
	/**
	 * create winui-frame
	 */
	function igk_html_node_frame()
	{
		return igk_create_node("div")->setAttributes(array("class" => "igk-ui-frame frame"));
	}
}
if (!function_exists("igk_html_node_framedialog")) {
	/**
	 * create winui-framedialog
	 * @param mixed $id
	 * @param mixed $ctrl
	 * @param mixed $closeuri
	 * @param mixed $reloadcallback
	 */
	function igk_html_node_framedialog($id, $ctrl, $closeuri = ".", $reloadcallback = null)
	{
		$frame = igk_getctrl(IGK_FRAME_CTRL)->createFrame($id, $ctrl, $closeuri, $reloadcallback);
		return $frame;
	}
}
if (!function_exists("igk_html_node_galleryfolder")) {
	/**
	 * auto generate doc.
	 * @param mixed $ignorethumb the default value is 1
	 */
	function igk_html_node_galleryfolder($ctrl, $folder, $ignorethumb = 1)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-gallery-folder";
		if (is_dir($folder)) {
			$thumb = igk_uri($folder . "/.thumb");
			$resolver = IGKResourceUriResolver::getInstance();
			foreach (igk_io_getfiles($folder) as $img) {
				if ($ignorethumb && strstr($img, $thumb)) {
					continue;
				}
				$i = $n->div()->setClass("igk-col no-overflow item")->setStyle("height: 210px; padding-bottom: 20px;")->addImg()->setSrc($img);
				$i["alt"] = basename($img);
				$i["class"] = "fit fitc";
			}
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_grid")) {
	/**
	 * create a grid node
	 */
	function igk_html_node_grid()
	{
		$n = igk_create_node("div");
		$n["class"] = "+igk-grid +grid";
		return $n;
	}
}
if (!function_exists("igk_html_node_headerbar")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_headerbar()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-header";
		return $n;
	}
}
if (!function_exists("igk_html_node_hiddenFields")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_hiddenFields(array $fields)
	{
		if ($f = igk_html_parent_node()) {
			foreach ($fields as $k => $v) {
				$f->addInput($k, "hidden", $v);
			}
		}
		return $f;
	}
}
if (!function_exists("igk_html_node_hlineseparator")) {
	/**
	 * create winui-hlineseparator
	 */
	function igk_html_node_hlineseparator()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-hline-sep";
		return $n;
	}
}
if (!function_exists("igk_html_node_hook")) {
	/**
	 * call hook to render content on node 
	 * @param mixed $hook 
	 * @param mixed $args 
	 * @return HtmlNoTagNode 
	 */
	function igk_html_node_hook($hook, ...$args)
	{
		$n = igk_html_node_notagnode();
		$n->addObData(function () use ($hook, $args) {
			igk_hook($hook, ...$args);
		}, null);
		return $n;
	}
}
if (!function_exists("igk_html_node_hooknode")) {
	/**
	 * create hook node to update content on render
	 */
	function igk_html_node_hooknode($hook, ?string $context = null)
	{
		$n = new IGK\System\Html\Dom\HtmlHookNode($hook, $context);
		return $n;
	}
}
if (!function_exists("igk_html_node_horizontalpageview")) {
	/**
	 * create  winui-horizontalpageview
	 */
	function igk_html_node_horizontalpageview()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-hpageview";
		return $n;
	}
}
if (!function_exists("igk_html_node_host")) {
	/**
	 * host callable to 
	 * @param callable|string $callback . if string custom function of 'igk_html_'+$callback name
	 * @return mixed 
	 * @throws Exception 
	 * @throws IGKException 
	 */
	function igk_html_node_host($callback, ...$args)
	{
		if (!($callback instanceof \Closure)){
			if (!is_callable($callback) && is_string($callback)){
				if (function_exists($fc = 'igk_html_'.$callback)){
					$callback = Closure::fromCallable($fc);
				}else{
					igk_die('callback is not a valid callback');
				}
			}
		}
		if (!($p = igk_html_parent_node()))
			throw new IGKException("Parent Node not found");
		ob_start();
		array_unshift($args, $p);
		if (($response = $callback(...$args)) && ($p !== $response)) {
			if (is_array($response)) {
				$p->text(igk_ob_get($response));
			} else {
				if ($response instanceof HtmlItemBase) {
					$p->add($response);
				} else
					$p->text($response);
			}
		}
		if (!empty($response = ob_get_clean())) {
			if (is_array($response)) {
				$p->text(igk_ob_get($response));
			} else {
				$p->text($response);
			}
		}
		return $p;
	}
}
if (!function_exists('igk_html_host_wln')) {
	/**
	 * 
	 * @return void 
	 */
	function igk_html_host_wln($t)
	{
		$n = igk_create_notagnode();
		$args = array_slice(func_get_args(), 1);
		ob_start();
		igk_wln(...$args);
		$p = ob_get_contents();
		ob_end_clean();
		$n->setTextContent($p);
		$t->add($n);
		return $t;
	}
}
if (!function_exists("igk_html_node_hostobdata")) {
	/**
	 * Hosted object data. will pass the current node to callback as first argument
	 */
	function igk_html_node_hostobdata($callback, $host = null)
	{
		$p = $q = igk_html_parent_node();
		$index = 1;
		if ($q === null) {
			$p = $host;
			$index = 2;
		}
		if ($p == null)
			igk_die("no parent to host");
		$tab = array_merge(array($p), array_slice(func_get_args(), $index));
		$n = igk_create_notagnode();
		IGKOb::Start();
		call_user_func_array($callback, $tab);
		$s = IGKOb::Content();
		IGKOb::Clear();
		$n->addText()->Content = $s;
		return $n;
	}
}
if (!function_exists("igk_html_node_hscrollbar")) {
	/**
	 * create winui-hscrollbar
	 */
	function igk_html_node_hscrollbar()
	{
		$n = igk_create_node();
		$n["class"] = "igk-hscroll";
		return $n;
	}
}
if (!function_exists("igk_html_node_hsep")) {
	/**
	 * create winui-hsep
	 */
	function igk_html_node_hsep()
	{
		return igk_html_node_Separator("horizontal");
	}
}
if (!function_exists("igk_html_node_htmlnode")) {
	/**
	 * create winui-htmlnode
	 * @param mixed $tag
	 */
	function igk_html_node_htmlnode($tag)
	{
		return new HtmlNode($tag);
	}
}
if (!function_exists("igk_html_node_huebar")) {
	/**
	 * used to render a pick a huebar value
	 */
	function igk_html_node_huebar()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-huebar";
		$n->div()->setClass("cur");
		$n->addBalafonJS()->Content = "igk.winui.huebar.init(); ";
		return $n;
	}
}
if (!function_exists("igk_html_node_if")) {
	/**
	 * use to create if node
	 * @param string $condition 
	 * @return HtmlConditionNode<mixed, string> 
	 */
	function igk_html_node_if(string $condition)
	{
		$g = new HtmlConditionNode;
		$g['*visible'] = $condition;
		return $g;
	}
}
if (!function_exists("igk_html_node_if_condition")) {
	/**
	 * helper to create igk:if-condition node
	 */
	function igk_html_node_if_condition()
	{
		$g = new HtmlConditionNode;
		return $g;
	}
}
if (!function_exists("igk_html_node_igkcopyright")) {
	/**
	 * create winui-igkcopyright
	 */
	function igk_html_node_igkcopyright(?string $title = null)
	{
		$n = igk_create_node();
		$n->setClass("igk-copyright");
		if (is_null($title)) {
			$n->setCallback("getCopyright", "return igk_sys_copyright();");
			$g = new \IGK\ValueListener($n, "getCopyright");
			$n->Content = $g;
		} else {
			$n->Content = $title;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_igkgloballangselector")) {
	/**
	 * create winui-igkgloballangselector
	 */
	function igk_html_node_igkgloballangselector()
	{
		$dv = igk_create_node("div");
		$sl = $dv->add("select")->setId("lang")->setClass("-igk-control -igk-form-control -clselect");
		$gt = igk_configs()->default_lang;
		$uri = \IGK\Helper\UriHelper::GetCmdAction(igk_sys_ctrl(), "changeLang_ajx");
		$sl["onchange"] = " if (window.ns_igk){ ns_igk.ajx.get('{$uri}/'+this.value, null, ns_igk.ajx.fn.replace_or_append_to_body); } return false;";
		$sl->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, igk_io_get_script(IGK_LIB_DIR . "/Inc/html/globallang_accept_render.pinc"));
		return $dv;
	}
}
if (!function_exists("igk_html_node_igkglobalthemeselector")) {
	/**
	 * create winui-igkglobalthemeselector
	 */
	function igk_html_node_igkglobalthemeselector()
	{
		$dv = igk_create_node("div");
		$sl = $dv->addSelect("theme")->setClass("-igk-control -clselect");
		$sl->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, "return igk_init_renderingtheme_callback(\$this); ");
		$uri = \IGK\Helper\UriHelper::GetCmdAction(igk_sys_ctrl(), "changeTheme");
		$sl["onchange"] = " if (window.ns_igk) { ns_igk.css.changeTheme('{$uri}', this.value);} return false;";
		return $dv;
	}
}
if (!function_exists("igk_html_node_igkheaderbar")) {
	/**
	 * create winui-headerbar
	 * @param string $title
	 * @param mixed $baseuri
	 */
	function igk_html_node_igkheaderbar($title, $baseuri = null)
	{
		$baseuri = $baseuri ? $baseuri : igk_io_baseDomainUri();
		$n = igk_create_node("div");
		$r = $n->addRow()->setClass("no-margin");
		$h1 = $r->div()->setClass(" igk-col-lg-12-2 fith presentation");
		$t = $h1->div()->addA($baseuri)->setClass("dispb no-decoration");
		$t->add("span")->setClass("dispib posr")->setStyle("left:10px; top:12px;")->Content = igk_web_get_config("company_name");
		$t->div()->setClass("igk-title-4")->Content = $title;
		$n->m_Box = $r->div();
		$n->m_Box->setClass("igk-col-lg-12-10 .ibox");
		return $n;
	}
}
if (!function_exists("igk_html_node_igksitemap")) {
	/**
	 * create winui-igksitemap
	 */
	function igk_html_node_igksitemap()
	{
		$n = igk_create_xmlnode("urlset");
		$n["xmlns"] = "http://www.sitemaps.org/schemas/sitemap/0.9";
		$n["xmlns:sitemap"] = "http://www.sitemaps.org/schemas/sitemap/0.9";
		$n->setCallback("lUri", igk_create_func_callback("igk_site_map_add_uri", array($n)));
		return $n;
	}
}
if (!function_exists("igk_html_node_imagenode")) {
	/**
	 * create winui-imagenode
	 */
	function igk_html_node_imagenode()
	{
		$n = igk_create_node("img");
		return $n;
	}
}
if (!function_exists("igk_html_node_img")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_img($src = null)
	{
		if (is_string($src)) {
			$src = UriResolver::ResolveControllerResource($src);
		}
		return new \IGK\System\Html\Dom\HtmlImgNode($src);
	}
}
if (!function_exists("igk_html_node_imglnk")) {
	/**
	 * create winui-imglnk
	 */
	function igk_html_node_imglnk()
	{
		return new \IGK\System\Html\Dom\HtmlImgLnkNode(...func_get_args());
	}
}
if (!function_exists("igk_html_node_include")) {
	/**
	 * auto generate doc.
	 * @param mixed|null $params
	 * @return mixed
	 */
	function igk_html_node_include($ctrl, $view, $params = null)
	{
		$bind = function () {
			if (func_num_args() != 2) {
				die("function expect exactly 2 parameter. ");
			}
			extract(func_get_arg(1));
			include(func_get_arg(0));
		};
		$bind = $bind->bindTo($ctrl);
		if ($f = igk_html_parent_node()) {
			$f->host(function () use ($ctrl, $view, $params) {
				$ck = "include_file";
				$cf = $ctrl->getViewFile($view, 1);
				if (igk_environment()->checkInArray($ck, $cf)) {
					throw new IGKException($view . " already included");
				}
				igk_environment()->setInArray($ck, $cf);
				$ctrl::ViewInContext($cf, $params);
				igk_environment()->unsetInArray($ck, $cf);
			});
		}
		return $f;
	}
}
if (!function_exists("igk_html_node_include_js")) {
	/**
	 * include local file as javascript
	 */
	function igk_html_node_include_js(string $file)
	{
		if (($f = igk_html_parent_node()) && is_file($file)) {
			$d = igk_create_xmlnode("script");
			$d["type"] = "balafon/js-include";
			$d["class"] = "igk-winui-balafon-js-inc";
			$d->Content = implode("", [
				"//<![CDATA[",
				implode("", explode(
					"\n",
					file_get_contents($file)
				)),
				"]]>"
			]);
			$f->add($d);
		} else {
			igk_die("include js not allowed not allowed");
		}
		return $f;
	}
}
if (!function_exists("igk_html_node_innerimg")) {
	/**
	 * create winui-innerimg
	 */
	function igk_html_node_innerimg()
	{
		$n = new IGK\System\Html\Dom\HtmlImgNode();
		return $n;
	}
}
if (!function_exists("igk_html_node_input")) {
	/**
	 * create input node helper
	 * @param string|array|null $id id or arg of the input or arg
	 */
	function igk_html_node_input($id = null, $type = 'text', $value = null, $attributes = null)
	{
		$i = new HtmlNode('input');
		$is_string = is_string($id);
		$attributes = $attributes ?? (is_array($id) ? $id : null);
		$i["type"] = $type;
		$i["value"] = ($value === null) && $is_string ? igk_getr($id, null) : $value;
		if ($is_string)
			$i["id"] = $i["name"] = $id;
		else {
			$type = igk_getv($id, 'type') ?? $type;
		}
		if ($type) {
			$i["class"] = "+cl" . $type;
			switch ($type) {
				case "button":
				case "submit":
				case "reset":
					$i["class"] = "-cltext +igk-btn";
					break;
			}
		}
		$attributes && $i->setAttributes($attributes);
		return $i;
	}
}
if (!function_exists("igk_html_node_jombotron")) {
	/**
	 * auto generate doc.
	 * @param Jombotron
	 */
	function igk_html_node_jombotron($text = 'Jombotron')
	{
		$n = igk_create_node("div");
		$col = $n->container()->addRow()->addCol();
		$dv = $col->div()->setClass("igk-jombotron");
		$dv->addSectionTitle(4)->Content = __("Welcome");
		$dv->div()->Content = $text;
		return $n;
	}
}
if (!function_exists("igk_html_node_js_autofix_width")) {
	/**
	 * mark parent node with autofixing with. 
	 */
	function igk_html_node_js_autofix_width()
	{
		$n = new HtmlNode('igk:auto-fix-width');
		$n->setStyle("display:none;")
			->balafonJS()->Content = "igk.winui.layout.autofix_width(this.getParentNode());";
		return $n;
	}
}
if (!function_exists("igk_html_node_jsaextern")) {
	/**
	 * create winui-jsaextern
	 * @param mixed $method
	 * @param mixed $args
	 */
	function igk_html_node_jsaextern($method, $args = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winuin-jsa-ex dispn";
		if ($args)
			$args = ", args:'" . $args . "'";
		else
			$args = "";
		$n["igk:data"] = "{m:'{$method}' {$args}}";
		return $n;
	}
}
if (!function_exists("igk_html_node_jsbindNS")) {
	/**
	 * inject namespace with properties js namespace
	 */
	function igk_html_node_jsbindNS(string $namespace, array $data, $coredef = "igk")
	{
		$s = igk_create_node("script");
		$data = json_encode($data, JSON_UNESCAPED_SLASHES);
		$_w = "window.{$coredef}";
		$s->Content = igk_js_minify(implode("", [
			"{$_w} = {$_w} || {}; (function(d, n, p){ var tab = n.split('.'), q = null, i = null; ",
			"while(q = tab.shift()){if (!(q in d)){d[q] = {};} d=d[q];} for( i in p){ if (! (i in d)) { d[i] = p[i]; }",
			"else {",
			"for(var j in p[i]){ d[i][j] = p[i][j];}",
			"}",
			"} })({$_w}, ",
			"'{$namespace}', {$data});",
		]));
		return $s;
	}
}
if (!function_exists("igk_html_node_jsbtn")) {
	/**
	 * create winui-jsbtn
	 * @param mixed $script
	 * @param mixed $value
	 */
	function igk_html_node_jsbtn($script, $value = null)
	{
		$n = igk_create_node("input");
		$n["type"] = "button";
		$n["class"] = "igk-btn";
		$n["onclick"] = "javascript: " . $script . " return false";
		$n["value"] = $value;
		return $n;
	}
}
if (!function_exists("igk_html_node_jsbtnshowdialog")) {
	/**
	 * create winui-jsbtnshowdialog
	 * @param mixed $id
	 */
	function igk_html_node_jsbtnshowdialog($id)
	{
		$n = igk_create_node();
		$n->setClass("igk-btn igk-js-btn-show-dialog");
		$n->setAttribute("igk:dialog-id", $id);
		return $n;
	}
}
if (!function_exists("igk_html_node_jsbutton")) {
	/**
	 * create winui-jsbutton
	 * @param mixed $js
	 */
	function igk_html_node_jsbutton($js)
	{
		$n = igk_create_node("a");
		$n["href"] = "#";
		$n["class"] = "igk-btn igk-js-button";
		$n["igk:js-action"] = $js;
		return $n;
	}
}
if (!function_exists("igk_html_node_jsclone")) {
	/**
	 * use to close node on client side
	 * @return IGK\System\Html\Dom\HtmlNode 
	 */
	function igk_html_node_jsclone(string $target, ?string $complete = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-clonenode";
		$n["igk:target"] = $target;
		$n["igk:complete"] = $complete;
		return $n;
	}
}
if (!function_exists("igk_html_node_jsclonenode")) {
	/**
	 * create winui-jsclonenode
	 * @param mixed $node
	 */
	function igk_html_node_jsclonenode($node)
	{
		if (($node == null) || !is_object($node))
			throw new IGKException("Not valid");
		if (!is_subclass_of(get_class($node), HtmlItemBase::class)) {
			throw new IGKException("not a valid item");
		}
		$n = igk_create_node("igk-js-clone-node");
		$n["igk-js-cn"] = new \IGK\ValueListener($n, 'getTargetId');
		$n->setParam("self::targetnode", $node);
		$n->setCallback(CallableConstants::CAN_RENDER_TAG_METHOD, "return true;");
		$n->setCallback("getTargetId", "return \$this->getParam('self::targetnode'); ");
		return $n;
	}
}
if (!function_exists("igk_html_node_jsclonetarget")) {
	/**
	 * create igk-winui-clone-target class node 
	 * @param string $selector
	 * @param string $tag default tag
	 */
	function igk_html_node_jsclonetarget(string $selector, ?string $tag = 'div')
	{
		$n = igk_create_node($tag);
		$n["class"] = "igk-winui-clone-target";
		$n["igk-data"] = $selector;
		return $n;
	}
}
if (!function_exists("igk_html_node_jslogger")) {
	/**
	 * create winui-jslogger
	 */
	function igk_html_node_jslogger()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-js-logger";
		return $n;
	}
}
if (!function_exists("igk_html_node_jsreadyscript")) {
	/**
	 * used to call ready invoke
	 */
	function igk_html_node_jsreadyscript($script)
	{
		$n = igk_html_node_script();
		$n->Content = "if (window.ns_igk)ns_igk.readyinvoke('{$script}');";
		return $n;
	}
}
if (!function_exists("igk_html_node_jsreplaceuri")) {
	/**
	 * create winui-jsreplaceuri
	 * @param string $uri
	 */
	function igk_html_node_jsreplaceuri(string $uri)
	{
		$n = igk_create_node('balafonJS');
		$n["autoremove"] = 1;
		$n->Content = "ns_igk.winui.history.replace('{$uri}', null); ";
		return $n;
	}
}
if (!function_exists("igk_html_node_jsscript")) {
	/**
	 * used to load manually script tag
	 */
	function igk_html_node_jsscript($file, $minify = false)
	{
		if (igk_io_file_exists($file)) {
			$d = igk_create_node("script");
			$s = file_get_contents($file);
			$d->Content = $s;
			return $d;
		}
		return null;
	}
}
if (!function_exists("igk_html_node_jsscript_options")) {
	/**
	 * inject options. corejs must be loader in order to work
	 * @param mixed $name 
	 * @param string|array|closure $options js_inline script to pass
	 * @return HtmlItemBase 
	 * @throws IGKException 
	 * require \js\js\common
	 */
	function igk_html_node_jsscript_options(string $name, $options)
	{
		if (is_array($options)) {
			$options = BalafonJSHelper::Stringify($options);
		}
		$n = igk_create_node('script');
		$src = HtmlJsOptionDefinition::GetJsScript($name, $options);
		$n->Content = $src;
		return $n;
	}
}
if (!function_exists("igk_html_node_jsview")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_jsview()
	{
		$n = igk_create_node("script");
		$n["type"] = "balafon/js-view";
		$n["class"] = "igk-balafon-js-view";
		return $n;
	}
}
if (!function_exists("igk_html_node_jswaiter")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_jswaiter()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-js-waiter";
		return $n;
	}
}
if (!function_exists("igk_html_node_jumbotron")) {
	/**
	 * create a jumbotron element
	 * @return HtmlItemBase<mixed, string> 
	 * @throws IGKException 
	 */
	function igk_html_node_jumbotron(?string $title = null, $desc = null)
	{
		$n = igk_create_node('div');
		$n['class'] = 'igk-jumbotron';
		if ($title)
			$n->h1()->Content = $title;
		if ($desc)
			$n->div()->Content = $desc;
		return $n;
	}
}
if (!function_exists("igk_html_node_label")) {
	/**
	 * create winui-label
	 * @param ?string $for
	 * @param ?string $key
	 */
	function igk_html_node_label(?string $for = null, ?string $key = null)
	{
		$n = new HtmlNode("label");
		$n["for"] = $for;
		$n["class"] = "cllabel";
		if ($for || $key) {
			$n->Content = (($key == null) ? R::ngets("lb." . $for) : R::ngets($key));
		}
		$n->setTempFlag("replaceContentLoading", 1);
		return $n;
	}
}
if (!function_exists("igk_html_node_labelinput")) {
	/**
	 * create winui-labelinput
	 * @param mixed $id
	 * @param mixed $text
	 * @param mixed $type
	 * @param mixed $value
	 * @param mixed $attributes
	 * @param mixed $require
	 * @param mixed $description
	 */
	function igk_html_node_labelinput($id, $text, $type = "text", $value = null, $attributes = null, $require = false, $description = null)
	{
		$o = igk_create_notagnode(); 
		$o->setCallback(CallableConstants::CAN_RENDER_TAG_METHOD, "return false;");
		$o->setCallback("getinput", "return \$this->input;");
		$i = $o->add("label");
		$i["for"] = $id;
		$i->Content = $text;
		if ($attributes && is_string($attributes)) {
			$attributes = (array)igk_json_parse($attributes);
		}
		if ($require) {
			$i["class"] = "clrequired";
		}
		$h = $o->addInput($id, $type, $value, $attributes);
		$h["class"] = "+igk-form-control";
		switch ($type) {
			case "checkbox":
			case "radio":
				if ($value) {
					$h["checked"] = "true";
				}
				break;
		}
		$desc = null;
		if ($description) {
			$desc = $o->add("span");
			$desc->Content = $description;
		}
		$o->input = $h;
		return $o;
	}
}
if (!function_exists("igk_html_node_lborder")) {
	/**
	 * create winui-lborder
	 */
	function igk_html_node_lborder()
	{
		$n = igk_create_node();
		$n->setClass("igk-lborder");
		return $n;
	}
}
if (!function_exists("igk_html_node_linewaiter")) {
	/**
	 * create winui-linewaiter
	 */
	function igk_html_node_linewaiter()
	{
		$n = igk_create_node();
		$n["class"] = "igk-line-waiter";
		$n["igk:count"] = "3";
		$n->div()->setClass("igk-line-waiter-cur");
		$n->div()->setClass("igk-line-waiter-cur");
		$n->div()->setClass("igk-line-waiter-cur");
		$n->addBalafonJS()->Content = <<<EOF
ns_igk.readyinvoke('igk.winui.lineWaiter.init');
EOF;
		return $n;
	}
}
if (!function_exists("igk_html_node_linkbtn")) {
	/**
	 * create winui-linkbtn
	 * @param mixed $uri
	 * @param mixed $img
	 * @param mixed $width
	 * @param mixed $height
	 */
	function igk_html_node_linkbtn($uri, $img, $width = 16, $height = 16)
	{
		$n = igk_create_node("a");
		$img = $n->add("img");
		$n->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, "igk_html_callback_alinktn");
		$n->setCallback(
			"setUri",<<<EOF
\$this->getParam('data')->src=\$value;
EOF		);
		$n->setParam("data", (object)array("img" => $img, "w" => $width, "h" => $height, "src" => $uri));
		return $n;
	}
}
if (!function_exists("igk_html_node_list")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_list($items, $callback = null, $ordered = 0)
	{
		if ($callback == null) {
			$callback = function ($i, $v) {
				$i->Content = $v;
			};
		}
		$n = igk_create_node($ordered ? "ol" : "ul");
		if (is_array($items)) {
			foreach ($items as $v) {
				$callback($n->li(), $v);
			}
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_livenodecallback")) {
	/**
	 * create winui-componentnodecallback
	 * @param mixed $listener
	 * @param mixed $name
	 * @param mixed $closurecallback
	 */
	function igk_html_node_livenodecallback($listener, $name, $callback)
	{
		static $livenode = null;
		if ($livenode === null) {
			$livenode = [];
		}
		$f = null; 
		$c = $listener->getParam(IGK_NAMED_NODE_PARAM, array());
		if (isset($c[$name])) {
			$f = $c[$name];
		}
		if (!is_callable($callback)) {
			if (!is_string($callback) || (strtolower($callback) == "componentnodecallback"))
				igk_die("callback not valid");
			$hc = $callback;
			$callback = function ($listener, $name) use ($hc) {
				$t = igk_create_node($hc, null, array_slice(func_get_args(), 2));
				return $t;
			};
		}
		$args = array_merge(array($listener, $name), array_slice(func_get_args(), 3));
		$h = call_user_func_array($callback, $args);
		if ($h) {
			$c[$name] = []; 
			$h->setParam(IGK_NAMED_ID_PARAM, $name);
			$listener->setParam(IGK_NAMED_NODE_PARAM, $c);
			return $h;
		}
		igk_die("failed to created component");
		return null;
	}
}
if (!function_exists("igk_html_node_load_array")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_load_array(array $items, string $tag = 'div')
	{
		$n = igk_create_notagnode();
		$n->loop($items, function ($n, $i, $index) use ($tag) {
			$b = $n->add($tag);
			if (is_array($i) && is_callable($i)) {
				$b->content = $i($b);
			} else {
				$b->Content = $i;
			}
		});
		return $n;
	}
}
if (!function_exists("igk_html_node_localizabletext")) {
	/**
	 * auto generate doc.
	 * @param mixed $data the default value is null
	 */
	function igk_html_node_localizabletext($expression, $data = null)
	{
		$c = igk_html_initbindexpression($expression);
		$out = igk_str_format($expression, $data);
		$n = igk_createtextnode($out);
		return $n;
	}
}
if (!function_exists("igk_html_node_login_form")) {
	/**
	 * create a login form with service
	 * @param null|string $fname 
	 * @param null|BaseController $controller 
	 * @return HtmlNoTagNode 
	 * @throws IGKException 
	 * @throws ArgumentTypeNotValidException 
	 * @throws ReflectionException 
	 */
	function igk_html_node_login_form(?string $fname = null, ?BaseController $controller = null)
	{
		$ctrl = $controller ?? ViewHelper::CurrentCtrl() ?? igk_die('required controller');
		$fname = $fname ?? ViewHelper::GetViewArgs("fname") ?? igk_die('view arg fname is missing');
		$t = igk_create_notagnode();
		$form = $t->form(
			$ctrl->getAppUri("login")
		)->setClass("dispib");
		$form->host(ViewHelper::Form('login', [
			"good_uri" => $ctrl::uri(""),
			"bad_uri" => $ctrl::uri($fname),
		]));
		return $form;
	}
}
if (!function_exists("igk_html_node_loop")) {
	/**
	 * helper: loop thru array . or template binding
	 * @param mixed|int|Iterable|IViewExpressionArg $array iterable
	 * @param ?callable $callback  
	 */
	function igk_html_node_loop($array, ?callable $callback = null)
	{
		require_once IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlLooperNode.php';
		if (is_integer($array)) {
			($array <= 0) && igk_die("range not valid");
			$array = range(0, $array - 1);
		}
		$p = igk_html_parent_node() ?? igk_die("loop : parent node required");
		$c = new HtmlLooperNode($array, $p);
		if ($callback) {
			$c->host($callback);
		}
		return $c;
	}
}
if (!function_exists("igk_html_node_loremipsum")) {
	/**
	 * represent the loremIpSum zone
	 * @param mixed $modeverbose node
	 */
	function igk_html_node_loremipsum($mode = 1)
	{
		$n = igk_create_notagnode();
		switch ($mode) {
			default:
				$n->Content = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum
EOF;
				break;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_mailto")) {
	/**
	 * auto generate doc.
	 */	function igk_html_node_mailto($href, $text = "")
	{
		$n = igk_create_node("a");
		$n["href"] = "mailto: {$href}";
		$n->Content = empty($text) ? $href : $text;
		return $n;
	}
}
if (!function_exists("igk_html_node_memoryusageinfo")) {
	/**
	 * create winui-memoryusage-info tag
	 * @return HtmlMemoryUsageInfoNode 
	 */
	function igk_html_node_memoryusageinfo()
	{
		return new \IGK\System\Html\Dom\HtmlMemoryUsageInfoNode();
	}
}
if (!function_exists("igk_html_node_menukey")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_menukey($menus, $ctrl = null, $root = "ul", $item = "li", $callback = null)
	{
		$n = igk_create_node("ul");
		igk_html_load_menu_array($n, $menus, $item, $root, $ctrl, $callback);
		return $n;
	}
}
if (!function_exists("igk_html_node_menulist")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_menulist($menuTab)
	{
		$b = igk_create_notagnode();
		igk_html_build_menu($b, $menuTab);
		return $b;
	}
}
if (!function_exists("igk_html_node_menus")) {
	/**
	 * build menus node item
	 * @param mixed $items 
	 * @param mixed $callback subitem menu initialize callback
	 * @param string $subtag default subitem group tag 'ul'
	 * @param string $item default 'li' tag
	 * @return HtmlItemBase<mixed, string> 
	 * @throws IGKException 
	 */
	function igk_html_node_menus($items, $callback = null, $subtag = "ul", $item = "li", ?object $option = null)
	{
		$node = igk_create_node($subtag);
		$node["class"] = "igk-menu menu";
		igk_html_build_menu($node, $items, $callback, null, null, $item, $subtag);
		return $node;
	}
}
if (!function_exists("igk_html_node_moreview")) {
	/**
	 * create winui-moreview
	 * @param mixed $hide
	 */
	function igk_html_node_moreview($hide = 1)
	{
		$n = igk_create_node("span");
		$n["class"] = "igk-winui-more-view igk-hide";
		$n["igk:hide"] = $hide;
		$n->Content = "...";
		return $n;
	}
}
if (!function_exists("igk_html_node_msdialog")) {
	/**
	 * create winui-msdialog
	 * @param mixed $id
	 */
	function igk_html_node_msdialog($id = null)
	{
		$n = igk_create_node();
		$n->setClass("igk-ms-dialog");
		$n->setId($id);
		$n->addA("#")->setClass("igk-btn-close");
		return $n;
	}
}
if (!function_exists("igk_html_node_mstitle")) {
	/**
	 * create winui-mstitle
	 * @param mixed $key
	 */
	function igk_html_node_mstitle($key)
	{
		$n = igk_create_node();
		$n->setClass("igk-ms-dialog-title");
		$n->Content = R::ngets($key);
		return $n;
	}
}
if (!function_exists("igk_html_node_navigationlink")) {
	/**
	 * create winui-navigationlink
	 * @param mixed $target
	 */
	function igk_html_node_navigationlink($target)
	{
		$n = igk_create_node("a");
		$n->setAttribute("igk-nav-link", $target);
		return $n;
	}
}
if (!function_exists("igk_html_node_nbsp")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_nbsp()
	{
		$c = igk_createtextnode("&nbsp;");
		if ($f = igk_html_parent_node()) {
			$f->add($c);
			return $f;
		}
		return $c;
	}
}
if (!function_exists("igk_html_node_newsletterregistration")) {
	/**
	 * create winui-newsletterregistration
	 * @param mixed $uri
	 * @param mixed $type
	 * @param mixed $ajx
	 */
	function igk_html_node_newsletterregistration($uri, $type = "email", $ajx = 1)
	{
		$n = igk_create_node();
		$frm = $n->addForm();
		$frm["action"] = $uri;
		$frm["igk-ajx-form"] = $ajx;
		$frm->addInput("clEmail", $type)->setClass("igk-form-control")->setAttribute("placeholder", R::ngets("tip.yourmail"));
		$frm->addInput("btn.send", "submit")->setClass("igk-btn igk-btn-default");
		return $n;
	}
}
if (!function_exists("igk_html_node_notagnode")) {
	/**
	 * create winui-notagnode
	 */
	function igk_html_node_notagnode()
	{
		$n = new HtmlNoTagNode();
		return $n;
	}
}
if (!function_exists("igk_html_node_notagobdata")) {
	/**
	 * shortcut to create ObData node with noTag to display
	 */
	function igk_html_node_notagobdata($content)
	{
		return igk_html_node_obdata($content, IGK_HTML_NOTAG_ELEMENT);
	}
}
if (!function_exists("igk_html_node_notification")) {
	/**
	 * used to add notification node
	 */
	function igk_html_node_notification($nodeType = "div", $notifyName = null)
	{
		$n = igk_create_node($nodeType);
		igk_notify_sethost($n, $notifyName);
		return $n;
	}
}
if (!function_exists("igk_html_node_notifyhost")) {
	/**
	 * used to bind notify global ctrl message
	 */
	function igk_html_node_notifyhost($name = "::global", ?bool $autohide = null)
	{
		return new \IGK\System\Html\Dom\HtmlNotifyResponse($name, $autohide);
	}
}
if (!function_exists("igk_html_node_notifyhostbind")) {
	/**
	 * create winui-notifyhostbind
	 * @param mixed $name
	 * @param mixed $autohide
	 */
	function igk_html_node_notifyhostbind($name = null, $autohide = 1)
	{
		$o = igk_create_node('div');
		$o["class"] = "igk-notify-host-bind";
		$o->addOnRenderCallback(igk_create_func_callback("igk_notifyhostbind_callback", array($o, $name, $autohide)));
		return $o;
	}
}
if (!function_exists("igk_html_node_notifyzone")) {
	/**
	 * create winui-notifyzone
	 * @param mixed $name
	 * @param mixed $autohide
	 * @param mixed $tag
	 */
	function igk_html_node_notifyzone($name = null, $autohide = 1, $tag = "div")
	{
		$n = igk_create_node($tag);
		$n->setClass("igk-notify-z")->addNotifyhost($name, $autohide);
		return $n;
	}
}
if (!function_exists("igk_html_node_obdata")) {
	/**
	 * used to add a node with buffer content
	 */
	function igk_html_node_obdata($data, $nodeType = "div")
	{
		if (($nodeType == null) || ($nodeType === false))
			$nodeType = IGK_HTML_NOTAG_ELEMENT;
		$n = igk_create_node($nodeType);
		if (is_callable($data)) {
			IGKOb::Start();
			$s = $data($n);
			$g = IGKOb::Content();
			IGKOb::Clear();
			$s = $g;
		} else if (is_object($data) || is_array($data)) {
			if (igk_is_callback_obj($data)) {
				igk_invoke_callback_obj(null, $data);
			} else
				$s = igk_wln_ob_get($data);
		} else
			$s = $data;
		if (!empty($s)) {
			$t = new HtmlSingleNodeViewerNode(igk_html_node_notagnode());
			$t->targetNode->setTextContent($s);
			$n->add($t);
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_obscript")) {
	/**
	 * bind object scripting for callable
	 * @param callable $callback 
	 * @return \IGK\System\Html\Dom\HtmlScriptNode
	 */
	function igk_html_node_obscript(callable $callback)
	{
		ob_start();
		$r = $callback();
		$r .= ob_get_clean();
		$n = new \IGK\System\Html\Dom\HtmlScriptNode();
		$n->Content = $r;
		return $n;
	}
}
if (!function_exists("igk_html_node_onrendercallback")) {
	/**
	 *  create node on callback. create a callback object to send to this
	 */
	function igk_html_node_onrendercallback($callbackObj)
	{
		if (!igk_is_callable($callbackObj)) {
			return null;
		}
		$n = new \IGK\System\Html\Dom\HtmlRenderCallbackNode($callbackObj);
		return $n;
	}
}
if (!function_exists("igk_html_node_page")) {
	/**
	 * create winui-page
	 */
	function igk_html_node_page()
	{
		return igk_create_node("div")->setAttributes(array("class" => "igk-ui-page page"));
	}
}
if (!function_exists("igk_html_node_pageCenterBox")) {
	/**
	 * Igk html node page center box.
	 * @param null|callable $host
	 */
	function igk_html_node_pageCenterBox(?callable $host = null)
	{
		$box = null;
		$_o = null;
		if ($f = igk_html_parent_node()) {
			$dv = $f->div();
			$_o = $f;
		} else {
			$dv = igk_create_node("div");
			$_o = $dv;
		}
		$box = $dv->container()->row()->col("no-margin fitw")->div()->setClass("igk-page-center fitvh")->addCenterBox();
		if ($host != null) {
			$host($box);
		}
		return $_o;
	}
}
if (!function_exists("igk_html_node_paginationview")) {
	/**
	 *  build pagination settings
	 */
	function igk_html_node_paginationview($baseuri, $total, $perpage, $selected = 1, $ajx = 0, $cookiepath = null, $target = "::")
	{
		$e = "";
		if ($ajx)
			$e .= ", ajx:1";
		if ($cookiepath)
			$e .= ", cookie:'{$cookiepath}'";
		if ($selected)
			$e .= ", selected:'{$selected}'";
		$s_o = (object)["total" => 0, "selected" => 0, "maxButton" => 10];
		$settings = is_object($total) ? igk_create_filterObject($total, $s_o) : $s_o;
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-pagination";
		$n["igk:data"] = "{baseuri:'{$baseuri}',min:1,max:10 {$e}, target:'" . $target . "'}";
		$n->addObData(
			function () use ($baseuri, $total, $perpage, $selected) {
				$min = 0;
				$cmax = ceil($total / ($perpage));
				$max = min(10, $cmax);
				if ($selected <= $cmax) {
					$min = max(0, $selected - 5);
					$max = min($cmax, $selected + 5);
					if (($max - $min) < 10) {
						if ($min == 0) {
						} else {
							$max = $cmax;
							$min = max(0, $cmax - 10);
						}
					}
				}
				$s = igk_create_node("div");
				if ($min > 0)
					$s->add("span")->setClass("igk-btn")->Content = 1;
				if ($selected > 1)
					$s->add("span")->setAttribute("rol", "prev")->Content = __("Previous Page");
				for ($i = $min; $i < $max; $i++) {
					$p = ($i + 1);
					$st = $s->add("span");
					$st["class"] = "+igk-btn";
					if ($p == $selected) {
						$st["class"] = "+igk-selected";
					}
					$st->Content = ($i + 1);
				}
				if ($selected != $cmax)
					$s->add("span")->setAttribute("rol", "next")->Content = __("Next Page");
				if ($max < $cmax)
					$s->add("span")->setClass("igk-btn")->Content = $cmax;
				$s->renderAJX();
			},
			"div",
			array_slice(func_get_args(), 1)
		);
		$n->addBalafonJS()->Content = "igk.winui.paginationview.init()";
		return $n;
	}
}
if (!function_exists("igk_html_node_panelbox")) {
	/**
	 * create winui-panelbox
	 */
	function igk_html_node_panelbox()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-panel-box";
		return $n;
	}
}
if (!function_exists("igk_html_node_paneldialog")) {
	/**
	 * create winui-paneldialog
	 * @param mixed $title
	 * @param mixed $content
	 * @param mixed $settings
	 */
	function igk_html_node_paneldialog($title, $content = null, $settings = null)
	{
		if (is_string($settings)) {
			$settings = igk_json_parse($settings);
		}
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-panel-dialog";
		$box = $n->div()->setClass("box");
		$tl = $box->div()->setClass("igk-title");
		$tl->add("span")->Content = $title;
		$ctn = $box->div()->setClass("igk-content");
		if ($content) {
			if (is_string($content))
				$ctn->load($content);
			else
				$ctn->add($content);
		}
		if ($settings) {
			if ($svgBtn = igk_getv($settings, "closeBtn")) {
				if (is_numeric($svgBtn)) {
					$svgBtn = "drop";
				}
				$tl->addABtn("#")->setClass("close")->Content = igk_svg_use($svgBtn);
			}
			if ($attribs = igk_getv($settings, "attribs")) {
				if ($cl = igk_getv($attribs, "class")) {
					$n["class"] = $cl;
				}
				unset($attribs["class"]);
				$n->setAttributes($attribs);
			}
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_parallaxnode")) {
	/**
	 *  parallax node view
	 */
	function igk_html_node_parallaxnode($uri = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-parallax";
		$n["igk:data"] = $uri;
		return $n;
	}
}
if (!function_exists("igk_html_node_picker_zone")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_picker_zone($uri, $accepts = "", $complete = null)
	{
		$dv = igk_create_node("div");
		$dv->setClass("igk-picker-zone")
			->setAttribute("igk:picker-zone-data", json_encode([
				"uri" => $uri,
				"accept" => $accepts,
				"complete" => $complete
			]));
		return $dv;
	}
}
if (!function_exists("igk_html_node_popupmenu")) {
	/**
	 * create winui-popupmenu
	 */
	function igk_html_node_popupmenu()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-popup-menu";
		return $n;
	}
}
if (!function_exists("igk_html_node_pre")) {
	/**
	 * create winui-pre tag
	 * @param mixed $data 
	 * @return HtmlNode 
	 */
	function igk_html_node_pre($data = null)
	{
		$p = new HtmlNode("pre");
		if ($data !== null) {
			if (is_callable($data)) {
				$p->Content = igk_ob_get_func($data);
			} else {
				ob_start();
				print_r($data);
				$p->Content = ob_get_clean();
			}
		}
		return $p;
	}
}
if (!function_exists("igk_html_node_printbtn")) {
	/**
	 * print button
	 */
	function igk_html_node_printbtn($uri = null)
	{
		$s = igk_create_node("div");
		$s["class"] = "igk-btn igk-ptr-btn";
		$s["igk:data"] = $uri;
		return $s;
	}
}
if (!function_exists("igk_html_node_progressbar")) {
	/**
	 * create winui-progressbar
	 */
	function igk_html_node_progressbar()
	{
		$n = igk_create_node();
		$n["class"] = "igk-progressbar";
		$n["data"] = "0";
		$n->m_cur = $n->div()->setClass("igk-progressbar-cur igk-progress-0");
		return $n;
	}
}
if (!function_exists("igk_html_node_radiobutton")) {
	/**
	 * create radio button
	 * @param null|string $id 
	 * @return HtmlNode<mixed, string> 
	 */
	function igk_html_node_radiobutton(?string $id = null)
	{
		$i = new \IGK\System\Html\Dom\HtmlNode("input");
		$i["class"] = "igk-radio";
		$i["type"] = "radio";
		$i->setId($id);
		return $i;
	}
}
if (!function_exists("igk_html_node_readonlytextzone")) {
	/**
	 * create winui-readonlytextzone
	 * @param mixed $file
	 */
	function igk_html_node_readonlytextzone($file)
	{
		$n = igk_create_node();
		$n->setClass("igk-ro-txt-z");
		$c = $n->addTextArea()->setAttribute("readonly", true)->setClass("fitw fith")->setStyle("resize:none;white-space:pre;overflow-x:auto;word-wrap: break-word;")->setAttribute("onfocus", "javascript:event.preventDefault(); event.stopPropagation(); this.blur(); return false;")->Content = igk_create_func_callback("igk_file_content", array($file));
		$n->area = $c;
		return $n;
	}
}
if (!function_exists("igk_html_node_registermailform")) {
	/**
	 * create winui-registermailform
	 */
	function igk_html_node_registermailform()
	{
		$n = igk_create_node("form");
		$n["action"] = igk_getctrl(IGK_MAIL_CTRL)->getUri("register");
		$n["method"] = "POST";
		igk_notify_sethost($n->addRow()->addCol()->div(), "sys://mailregisterform");
		$n->addRow()->addCol()->div()->addInput("clEmail", "text")->setAttribute("placeholder", R::ngets("lb.yourmail"));
		$n->addRow()->addCol()->div()->addInput("clSubmit", "submit", R::ngets("btn.send"));
		$n->addInput("cref", "hidden", IGKApp::getInstance()->Session->getCRef());
		return $n;
	}
}
if (!function_exists("igk_html_node_renderingexpression")) {
	/**
	 * renderging Expression
	 */
	function igk_html_node_renderingexpression($callback)
	{
		if (!igk_is_callable($callback))
			return null;
		$n = igk_create_notagnode();
		$n->__callback = $callback;
		$n->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, "igk_invoke_callback_obj(\$this, \$this->__callback,\$param);  return true;");
		return $n;
	}
}
if (!function_exists("igk_html_node_repeatcontent")) {
	/**
	 * create winui-repeatcontent
	 * @param mixed $number
	 */
	function igk_html_node_repeatcontent($number)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-rc";
		$n["igk-repeat"] = $number;
		return $n;
	}
}
if (!function_exists("igk_html_node_replace_uri")) {
	/**
	 * auto generate doc.
	 */	function igk_html_node_replace_uri($uri = null)
	{
		$c = igk_create_notagnode();
		$rp = $uri;
		if ($rp || ($rp = igk_get_env("replace_uri"))) {
			$c->addObData(function () use ($rp) {
				igk_ajx_replace_uri($rp);
			});
		}
		return $c;
	}
}
if (!function_exists("igk_html_node_resimg")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_resimg($name, $desc = "", $width = 16, $height = 16)
	{
		$n = new HtmlNode("img");
		$n->setAttributes(array(
			"width" => $width,
			"height" => $height,
			"src" => R::GetImgUri(trim($name)),
			"alt" => R::ngets($desc)
		));
		return $n;
	}
}
if (!function_exists("igk_html_node_responsenode")) {
	/**
	 * create winui-responsenode
	 */
	function igk_html_node_responsenode()
	{
		$n = igk_create_node('div');
		$n["class"] = "igk-response";
		return $n;
	}
}
if (!function_exists("igk_html_node_rollin")) {
	/**
	 * create winui-rollin
	 */
	function igk_html_node_rollin()
	{
		$n = igk_create_node();
		$n["class"] = "igk-roll-in";
		return $n;
	}
}
if (!function_exists("igk_html_node_roundbullet")) {
	/**
	 * create winui-roundbullet
	 */
	function igk_html_node_roundbullet()
	{
		$n = igk_create_node("span");
		$n->setClass("badge igk-rd-bullet");
		return $n;
	}
}
if (!function_exists("igk_html_node_row")) {
	/**
	 * create winui-row
	 */
	function igk_html_node_row()
	{
		$n = igk_create_node('div');
		$n->setClass("igk-row");
		$n->setCallback(
			"addCell",
			implode("\n", [
				"\$d = \$this->div();",
				"\$d->setClass(\"igk-row-cell\");",
				"return \$d;"
			])
		);
		$n->setCallback(
			"addCol",
			implode("\n", [
				"\$v_n = igk_getv(\$param, 0);",
				"return \$this->add(igk_html_node_RowColumn(\$v_n));",
			])
		);
		return $n;
	}
}
if (!function_exists("igk_html_node_rowcolumn")) {
	/**
	 *  add a row column
	 * @param mixed $classLevel css classname that init the column level
	 */
	function igk_html_node_rowcolumn($classLevel = null)
	{
		$n = igk_create_node("div");
		$n->setClass("igk-col" . (($classLevel) ? " " . $classLevel : ""));
		return $n;
	}
}
if (!function_exists("igk_html_node_rowcontainer")) {
	/**
	 * create winui-rowcontainer
	 */
	function igk_html_node_rowcontainer()
	{
		$n = igk_create_node();
		$n["class"] = "igk-row-container";
		return $n;
	}
}
if (!function_exists("igk_html_node_script")) {
	/**
	 * function __desc__
	 * @var ?string $script
	 */
	function igk_html_node_script($script = null, $version = null)
	{
		return new \IGK\System\Html\Dom\HtmlScriptNode($script, $version);
	}
}
if (!function_exists("igk_html_node_scrollimg")) {
	/**
	 * create winui-scrollimg
	 * @param mixed $src
	 */
	function igk_html_node_scrollimg($src)
	{
		$n = igk_create_node("igk-img-js");
		$n["data"] = igk_create_attr_callback('igk_get_image_uri', array(null, $src));
		return $n;
	}
}
if (!function_exists("igk_html_node_scrollloader")) {
	/**
	 * used to load scroll Loader Item
	 */
	function igk_html_node_scrollloader($src)
	{
		if ($p = igk_html_parent_node()) {
			$p["class"] = "igk-scroll-loader_container";
		}
		$n = igk_create_node("igk-scroll-loader");
		$n["data"] = $src;
		return $n;
	}
}
if (!function_exists("igk_html_node_searchField")) {
	/**
	 * add search field
	 * @param string $id 
	 * @return HtmlItemBase<mixed, string> 
	 * @throws IGKException 
	 */
	function igk_html_node_searchField($id = "search")
	{
		$n = igk_create_node("input");
		$n["class"] = "igk-winui-search-field dispib";
		$n->setAttribute("name", $id);
		return $n;
	}
}
if (!function_exists("igk_html_node_searchbox")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_searchbox(string $uri, $id = "search")
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-searchbox";
		$n->add(igk_html_node_searchbutton($uri, $id));
		$n->input($id, "text", igk_getr($id));
		return $n;
	}
}
if (!function_exists("igk_html_node_searchbutton")) {
	/**
	 * search button view
	 * @param mixed $uri
	 */
	function igk_html_node_searchbutton(string $uri, string $id = "search")
	{
		$n = igk_create_node("span");
		$n["class"] = "igk-winui-searchbtn";
		$n["igk:target-uri"] = $uri;
		$n["igk:target-id"] = $id;
		$n->Content = igk_svg_use("search");
		$n->on("click", "return igk.winui.searchbox.search(this);");
		return $n;
	}
}
if (!function_exists("igk_html_node_sectiontitle")) {
	/**
	 * create winui-sectiontitle
	 * @param mixed $level
	 */
	function igk_html_node_sectiontitle($level = null)
	{
		$n = igk_create_node();
		$n["class"] = "igk-section-title";
		if ($level)
			$n->setClass("igk-title-" . $level);
		else
			$n->setClass("igk-title");
		return $n;
	}
}
if (!function_exists("igk_html_node_select")) {
	/**
	 * help create a select node
	 * @param mixed $id 
	 * @return HtmlNode 
	 */
	function igk_html_node_select($id = null)
	{
		$n = new HtmlNode("select");
		$n->setId($id);
		$n["title"] = $id;
		$n["class"] = "+clselect";
		return $n;
	}
}
if (!function_exists("igk_html_node_select_options")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_select_options($optionsList, $options = null)
	{
		($p = igk_html_parent_node()) || igk_die("required a parent list");
		if (is_string($options)) {
			$options = igk_json_parse($options);
		}
		$options =  igk_createobj_filter(
			$options ? $options : [],
			[
				"isEmpty" => 1,
				"display" => "text",
				"selected" => 0,
				"value" => "value",
				"allowEmpty" => 0,
				"emptyValue" => 0
			]
		);
		$s = $options->selected;
		if ($options->allowEmpty) {
			$o = $p->add("option");
			$v =  $options->emptyValue;
			$o["value"] = $v;
			$o->setContent("");
			if ($s == $v) {
				$o["class"] = "igk-active";
				$o->activate("selected");
			}
		}
		foreach ($optionsList as $m) {
			$o = $p->add("option");
			$t = igk_getv($m, $options->display);
			$v = igk_getv($m, $options->value);
			$o["value"] = $v;
			$o->setContent($t);
			if ($s == $v) {
				$o["class"] = "igk-active";
				$o->activate("selected");
			}
		}
		return null;
	}
}
if (!function_exists("igk_html_node_selecttag")) {
	/**
	 * create a select tag node JS requirement
	 * @param mixed $id 
	 * @param mixed|null $data 
	 * @param mixed|null $option array of settings show_tag|debug|click|click|require
	 * @return object 
	 * @throws ReflectionException 
	 * @throws IGKException 
	 */
	function igk_html_node_selecttag($id, $data = null, $options = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-selecttag";
		$n["igk:id"] = $id;
		$n["igk:data"] = is_array($data) ? htmlentities(json_encode($data)) : $data;
		$n["igk:options"] = is_array($options) ? htmlentities(json_encode($options)) : $options;
		return $n;
	}
}
if (!function_exists("igk_html_node_separator")) {
	/**
	 * create winui-separator
	 * @param mixed $type
	 */
	function igk_html_node_separator($type = 'horizontal')
	{
		$n = igk_create_node();
		switch ($type) {
			case "horizontal":
				$n["class"] = "igk-horizontal-separator";
				break;
			case "vertical":
				$n["class"] = "igk-vertical-separator";
				break;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_sidemenunavigation")) {
	/**
	 * auto generate doc.
	 * @param mixed $menulist
	 */
	function igk_html_node_sidemenunavigation($menulist)
	{
		$ul = igk_create_node("ul")->setClass("side-navigation");
		foreach ($menulist as $c => $g) {
			$li = $ul->add("li")->setContent(__($c));
			if (!empty($g)) {
				$_ul = $li->add("ul");
				foreach ($g as $k => $v) {
					$_li = $_ul->add("li");
					$_a = $_li->addA($v["url"]);
					if (isset($v["target"]))
						$_a->setAttribute("target", "__blank");
					$_a->Content = __($k);
				}
			}
		}
		return $ul;
	}
}
if (!function_exists("igk_html_node_singlenodeviewer")) {
	/**
	 * mixed create a shortcut to single node viewer
	 * @param mixed  node tag name or html item
	 */
	function igk_html_node_singlenodeviewer($node = null)
	{
		$s = 0;
		if ($node != null) {
			if (is_string($node)) {
				$s = igk_create_node($node);
			} else if (is_object($node)) {
				$s = $node;
			}
		} else
			$s = igk_html_node_noTagNode();
		return new HtmlSingleNodeViewerNode($s);
	}
}
if (!function_exists("igk_html_node_singlerowcol")) {
	/**
	 * shortcut to call node->addRow()->addCol()-> and return the column
	 */
	function igk_html_node_singlerowcol($col = null)
	{
		$d = igk_html_parent_node();
		if ($d) {
			$n = $d->row()->col($col);
			igk_html_skip_add();
			return $n;
		}
		return null;
	}
}
if (!function_exists("igk_html_node_singleviewnode")) {
	/**
	 * single node view
	 * @return IGKHtmlSingleNodeViewer 
	 * @throws ReflectionException 
	 * @throws IGKException 
	 */
	function igk_html_node_singleviewnode()
	{
		if ($f = igk_html_parent_node()) {
			$div = igk_create_notagnode();
			$node = new HtmlSingleNodeViewerNode($div);
			$f->add($div);
			return $div;
		}
		return $f;
	}
}
if (!function_exists("igk_html_node_slabelcheckbox")) {
	/**
	 * create winui-slabelcheckbox
	 * @param mixed $id
	 * @param mixed $value
	 * @param mixed $attributes
	 * @param mixed $require
	 */
	function igk_html_node_slabelcheckbox($id, $value = false, $attributes = null, $require = false)
	{
		$n = igk_html_node_notagnode();
		$tab = $n->addLabelInput($id, R::ngets("lb." . $id), $type = "checkbox", $value, $attributes, $require);
		if ($value) {
			$tab->input["checked"] = true;
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_slabelinput")) {
	/**
	 * create winui-slabelinput
	 * @param mixed $id
	 * @param mixed $type
	 * @param mixed $value
	 * @param mixed $attributes
	 * @param mixed $require
	 * @param mixed $description
	 */
	function igk_html_node_slabelinput($id, $type = "text", $value = null, $attributes = null, $require = false, $description = null)
	{
		return igk_html_node_labelinput($id, R::ngets("lb." . $id), $type, $value, $attributes, $require, $description);
	}
}
if (!function_exists("igk_html_node_slabelselect")) {
	/**
	 * create winui-slabelselect
	 * @param mixed $id
	 * @param mixed $values
	 * @param mixed $valuekey
	 * @param mixed $defaultCallback
	 * @param mixed $required
	 */
	function igk_html_node_slabelselect($id, $values, $valuekey = false, $defaultCallback = null, $required = false)
	{
		$p = igk_html_parent_node();
		$i = $p->add("label");
		$i["for"] = $id;
		if ($required) {
			$i["class"] = "clrequired";
		}
		$i->Content = R::ngets("lb." . $id);
		$h = $p->add("select");
		$h->setId($id);
		if (is_array($values)) {
			foreach ($values as $k => $v) {
				$opt = $h->add("option");
				$opt["value"] = IGK_STR_EMPTY . $k;
				$opt->Content = $valuekey ? R::ngets("option." . $v) : $v;
				if (($defaultCallback) && $defaultCallback($k, $v))
					$opt["selected"] = true;
			}
		}
		return (object)array("label" => $i, "input" => $h);
	}
}
if (!function_exists("igk_html_node_slabeltextarea")) {
	/**
	 * create winui-slabeltextarea
	 * @param mixed $id
	 * @param mixed $attributes
	 * @param mixed $require
	 * @param mixed $description
	 */
	function igk_html_node_slabeltextarea($id, $attributes = null, $require = false, $description = null)
	{
		$i = igk_create_node("label");
		$i["for"] = $id;
		$i->Content = R::ngets("lb." . $id);
		if ($require) {
			$i->setClass("clrequired");
		}
		$h = igk_html_node_TextArea($id);
		$h->setAttributes($attributes);
		$desc = null;
		if ($description) {
			$desc = $i->add("span");
			$desc->Content = $description;
		}
		return (object)array("label" => $i, "textarea" => $h, "desc" => $desc);
	}
}
if (!function_exists("igk_html_node_space")) {
	/**
	 * create winui-space node
	 * @return HtmlSpaceNode 
	 */
	function igk_html_node_space()
	{
		return new \IGK\System\Html\Dom\HtmlSpaceNode();
	}
}
if (!function_exists("igk_html_node_span_label")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_span_label($title, $text)
	{
		$skip = false;
		if ($n = igk_html_parent_node()) {
			$skip = true;
		} else {
			$n = igk_html_node_notagnode();
		}
		$h = $n->add("div");
		$h["class"] = "igk-span-label";
		$h->add("label")->Content = $title;
		$h->add("span")->Content = $text;
		$skip && igk_html_skip_add();
		return $skip ? null : $n;
	}
}
if (!function_exists("igk_html_node_spangroup")) {
	/**
	 * create winui-spangroup
	 */
	function igk_html_node_spangroup()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-span-group";
		return $n;
	}
}
if (!function_exists("igk_html_node_style")) {
	/**
	 * create winui-style
	 */
	function igk_html_node_style()
	{
		$s = new HtmlNode("style");
		return $s;
	}
}
if (!function_exists("igk_html_node_submit")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_submit($name = null, $value = null, $type = "submit")
	{
		$n = igk_html_node_input($name, $type, $value);
		$n["class"] = "igk-form-control igk-btn";
		$n["type"] = $type;
		if ($name) {
			$n->setAttribute("name", $name);
		}
		if (!$value) {
			$value = __("submit");
		}
		$n["value"] = $value;
		return $n;
	}
}
if (!function_exists("igk_html_node_submitbtn")) {
	/**
	 * create winui-submitbtn
	 * @param mixed $name
	 * @param mixed $key
	 */
	function igk_html_node_submitbtn($name = "btn_", $key = "btn.add")
	{
		$n = igk_create_node("input");
		$n->setId($name);
		$n["value"] = R::ngets($key);
		$n["type"] = "submit";
		$n->setClass("igk-btn");
		return $n;
	}
}
if (!function_exists("igk_html_node_svg_container")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_svg_container(?array $containerlist)
	{
		$n = igk_create_node("div")->setClass("igk-svg-container dispn");
		if ($containerlist) {
			foreach ($containerlist as $l) {
				$n->add(igk_svg_use($l));
			}
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_svga")) {
	/**
	 * create winui-svga
	 * @param mixed $uri
	 * @param mixed $svgname
	 */
	function igk_html_node_svga($uri, $svgname)
	{
		$n = igk_html_node_a($uri);
		$n->setClass("svg-a");
		$n->addSvgSymbol($svgname);
		return $n;
	}
}
if (!function_exists("igk_html_node_svgajxformbtn")) {
	/**
	 * create winui-svgajxformbtn
	 * @param mixed $uri
	 * @param mixed $svgname
	 */
	function igk_html_node_svgajxformbtn($uri, $svgname)
	{
		$n = igk_html_node_a($uri);
		$n->setClass("svg-a igk-from-sbtn-ajx");
		$n->addSvgSymbol($svgname);
		return $n;
	}
}
if (!function_exists("igk_html_node_svglnkbtn")) {
	/**
	 * create winui-svglnkbtn
	 * @param mixed $uri
	 * @param mixed $svgname
	 */
	function igk_html_node_svglnkbtn($uri, $svgname)
	{
		$n = igk_html_node_a($uri);
		$n->setClass("svg-a igk-from-sbtn");
		$n->addSvgSymbol($svgname);
		return $n;
	}
}
if (!function_exists("igk_html_node_svgsymbol")) {
	/**
	 * create winui-svgsymbol
	 * @param mixed $name
	 */
	function igk_html_node_svgsymbol($name = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-svg-symbol";
		$n["igk:svg-name"] = $name;
		return $n;
	}
}
if (!function_exists("igk_html_node_svguse")) {
	/**
	 * create winui-svguse
	 * @param mixed $name
	 */
	function igk_html_node_svguse($name)
	{
		$n = igk_html_node_notagnode();
		$n->Content = igk_svg_use($name);
		return $n;
	}
}
if (!function_exists("igk_html_node_symbol")) {
	/**
	 * create winui-symbol
	 * @param mixed $code
	 * @param mixed $w
	 * @param mixed $h
	 * @param mixed $name
	 */
	function igk_html_node_symbol($code, $w = 16, $h = 16, $name = 'default')
	{
		$n = igk_create_node();
		$n["class"] = "igk-symbol";
		$n->Content = is_integer($code) ? "&#" . $code . ";" : $code;
		$g = $name == 'default' || ($name == null) ? '' : ", name:'$name'";
		$n["igk-symbol-data"] = "{w:'$w', h:'$h' $g}";
		return $n;
	}
}
if (!function_exists("igk_html_node_sysarticle")) {
	/**
	 * used to add system article
	 */
	function igk_html_node_sysarticle($name)
	{
		$f = igk_io_get_article($name);
		$n = igk_create_node();
		igk_html_article(igk_sys_ctrl(), $f, $n);
		return $n;
	}
}
if (!function_exists("igk_html_node_tabbutton")) {
	/**
	 * create winui-tabbutton
	 */
	function igk_html_node_tabbutton()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-tab-button";
		$n->setCallback('add', "igk_html__tabbutton_add");
		return $n;
	}
}
if (!function_exists("igk_html_node_table")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_table(?string $id = null)
	{
		$i = new \IGK\System\Html\Dom\HtmlTableNode();
		$i["class"] = "igk-table";
		$i->setId($id);
		return $i;
	}
}
if (!function_exists("igk_html_node_tableheader")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_tableheader($headers, $filter = null)
	{
		$tr = igk_create_node("tr");
		foreach ($headers as $k) {
			$th = $tr->add("th");
			if (!$filter) {
				if (empty($k))
					$k = "&nbsp;";
				$th->Content = $k;
			} else
				$filter($k, $th);
		}
		return $tr;
	}
}
if (!function_exists("igk_html_node_tablehost")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_tablehost()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-table-host";
		return $n;
	}
}
if (!function_exists("igk_html_node_td")) {
	/**
	 * create winui-td
	 * @param mixed $for
	 * @param mixed $key
	 */
	function igk_html_node_td($for = null, $key = null)
	{
		$n = new HtmlNode("td");
		return $n;
	}
}
if (!function_exists("igk_html_node_template")) {
	/**
	 * use to add a template node
	 */
	function igk_html_node_template($ctrl, $name, $row = null)
	{
		$d = igk_create_node();
		igk_html_binddata($ctrl, $d, $name, $row, false, true);
		return $d;
	}
}
if (!function_exists("igk_html_node_text")) {
	/**
	 * create text node
	 */
	function igk_html_node_text($txt = null)
	{
		return igk_createtextnode($txt);
	}
}
if (!function_exists("igk_html_node_textarea")) {
	/**
	 * create winui-textarea
	 * @param mixed $name
	 * @param mixed $content
	 * @param mixed $attributes
	 */
	function igk_html_node_textarea($name = null, $content = null, $attributes = null)
	{
		$tx = new HtmlNode("textarea");
		if ($name)
			$tx->setId($name);
		$tx["class"] = "+cltextarea";
		$tx->setAttributes($attributes);
		$tx->setParam("p:_useWTiny", true);
		$tx->setParam("p:_escapeChar", false);
		$tx->setCallback("setContent", "igk_html_TextAreaV_Callback");
		if ($content == null) {
			$tx->Content = igk_getr($name);
		} else
			$tx->Content = $content;
		return $tx;
	}
}
if (!function_exists("igk_html_node_textedit")) {
	/**
	 * represent a zone node for text edition
	 */
	function igk_html_node_textedit($id, $uri, $c = null)
	{
		$n = igk_create_node("span");
		$n["class"] = "igk-textedit";
		$n["igk:data"] = "{uri:'{$uri}', id:'{$id}'}";
		$n->Content = $c;
		return $n;
	}
}
if (!function_exists("igk_html_node_thumbnaildocument")) {
	/**
	 * create a thumbnail document
	 */
	function igk_html_node_thumbnaildocument($id)
	{
		$d = igk_get_document($id, 1);
		$d->body->setClass("+thumbnail-doc +thumbnail");
		return $d;
	}
}
if (!function_exists("igk_html_node_time")) {
	/**
	 * helper: create a time tag node 
	 * @param null|string $datetime 
	 * @return HtmlItemBase<mixed, null|string> 
	 * @throws IGKException 
	 */
	function igk_html_node_time(?string $datetime = null)
	{
		$n = igk_create_node("time");
		$n['datetime'] = $datetime;
		return $n;
	}
}
if (!function_exists("igk_html_node_tip")) {
	/**
	 *  represent a tip panel
	 */
	function igk_html_node_tip()
	{
		$n = igk_create_node("p");
		$n["class"] = "igk-tip";
		return $n;
	}
}
if (!function_exists("igk_html_node_titlelevel")) {
	/**
	 * create winui-titlelevel
	 * @param mixed $level
	 */
	function igk_html_node_titlelevel($level = 1)
	{
		$n = igk_create_node();
		$n["class"] = "igk-title-" . $level;
		return $n;
	}
}
if (!function_exists("igk_html_node_titlenode")) {
	/**
	 * create winui-titlenode
	 * @param mixed $class
	 * @param mixed $text
	 */
	function igk_html_node_titlenode($class, $text)
	{
		if (!$class)
			$class = "igk-title";
		$n = igk_create_node("div");
		$n->setClass($class)->setContent($text);
		return $n;
	}
}
if (!function_exists("igk_html_node_toast")) {
	/**
	 * for toast message
	 * attribute : 
	 */
	function igk_html_node_toast()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-winui-toast";
		return $n;
	}
}
if (!function_exists("igk_html_node_toast_notify")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_toast_notify($name)
	{
		$node = igk_html_node_notagnode();
		if ($d = igk_notifyctrl($name)) {
			$cnode = new \IGK\System\Html\Dom\HtmlNotifyToastResponse($name);
			$node->add($cnode);
		}
		return $node;
	}
}
if (!function_exists("igk_html_node_toggleThemeButton")) {
	/**
	 * create a toggle button theme
	 * @param (null|string)|null $tag 
	 * @return HtmlItemBase<mixed, string> 
	 * @throws IGKException 
	 */
	function igk_html_node_toggleThemeButton(?string $tag = null)
	{
		$c = igk_create_node($tag ?? 'div');
		$c['class'] = 'igk-toggle-theme-button';
		$c->on('click', 'igk.css.changeDocumentTheme()');
		return $c;
	}
}
if (!function_exists("igk_html_node_togglebutton")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_togglebutton()
	{
		return new \IGK\System\Html\Dom\HtmlToggleButtonNode();
	}
}
if (!function_exists("igk_html_node_tooltip")) {
	/**
	 * create winui-tooltip
	 */
	function igk_html_node_tooltip()
	{
		$n = igk_create_xmlnode("igk:tooltip")->setAttribute("style", "display:none;");
		return $n;
	}
}
if (!function_exists("igk_html_node_topnavbar")) {
	/**
	 * create winui-topnavbar
	 */
	function igk_html_node_topnavbar()
	{
		$n = igk_create_node("div");
		$n["igk-top-nav-bar"] = "1";
		$n["class"] = "igk-navbar igk-top-nav-bar";
		return $n;
	}
}
if (!function_exists("igk_html_node_trackbarnode")) {
	/**
	 * create winui-trackbarnode
	 * @param mixed $id
	 * @param mixed $value
	 * @param mixed $min
	 * @param mixed $max
	 */
	function igk_html_node_trackbarnode($id, $value, $min = 0, $max = 100)
	{
		$n = igk_create_node("input");
		$n->setId($id);
		$n["type"] = "range";
		$n["class"] = "igk-winui-trackbar";
		$n["min"] = $min;
		$n["max"] = $max;
		$n["value"] = $value;
		return $n;
	}
}
if (!function_exists("igk_html_node_transitionblock")) {
	/**
	 *  create a transition block node
	 */
	function igk_html_node_transitionblock()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-transition-block";
		return $n;
	}
}
if (!function_exists("igk_html_node_underconstructionpage")) {
	/**
	 * create winui-underconstructionpage
	 */
	function igk_html_node_underconstructionpage()
	{
		$n = igk_create_node();
		$n->setCallback("getCommunityNode", "return null;");
		$n->setClass("fitw fith igk-under-construction");
		$src = igk_get_env("sys://underconstruction.bg");
		if (!$src)
			$src = igk_io_baseDir(igk_db_get_config("sys://underconstruct.bg", ""));
		if ($src) {
			$n->addBackgroundLayer()->addImg()->setSrc($src);
		}
		$c = $n->container();
		$c->setStyle("padding-top: 64px; padding-bottom; ;");
		$r = $c->addRow();
		$r->addCol()->div()->setStyle("font-size:3em;color:#eee")->Content = R::ngets("title.pageUnderConstruction");
		$c = $r->addCol()->div();
		$c->Content = "Vous souhaitez être informer lors de l'ouverture du site";
		$r->addCol()->div()->addRegisterMailForm();
		return $n;
	}
}
if (!function_exists("igk_html_node_userinfo")) {
	/**
	 * auto generate doc.
	 * @param Users $user
	 * @return HtmlNode<mixed
	 */
	function igk_html_node_userinfo($user)
	{
		$n = new \IGK\System\Html\Dom\HtmlNode("div");
		$n["class"] = "userinfo";
		$n->ul()->li()->Content = implode(" ", array_filter([$user->clFirstName, strtoupper($user->clLastName)]));
		$r = $n->div();
		$r->div()->Content = $user->clLogin;
		$r->address()->Content = "data";
		return $n;
	}
}
if (!function_exists("igk_html_node_usesvg")) {
	/**
	 * shortcut to load svg document 
	 */
	function igk_html_node_usesvg(string $name)
	{
		$s = igk_create_node("span");
		$s->Content = igk_svg_use($name);
		return $s;
	}
}
if (!function_exists("igk_html_node_videofilestream")) {
	/**
	 * create winui-videofilestream
	 * @param mixed $location
	 * @param mixed $auth
	 */
	function igk_html_node_videofilestream($location, $auth = false)
	{
		$n = igk_create_node("video");
		$n["controls"] = 1;
		$n["preload"] = "auto";
		$n["src"] = $location;
		return $n;
	}
}
if (!function_exists("igk_html_node_view_code")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_view_code(string $file, int $startLine, int $endLine)
	{
		if (!igk_io_file_exists($file)) {
			return null;
		}
		$str = implode("\n", array_slice(explode("\n", file_get_contents($file)), $startLine, $endLine));
		$n = igk_createtextnode($str);
		return $n;
	}
}
if (!function_exists("igk_html_node_view_include")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_view_include(string $path, ?BaseController $ctrl = null)
	{
		if (is_null($ctrl)) {
			$ctrl = ViewHelper::CurrentCtrl() ?? igk_die("require controller.");
		}
		$n = new HtmlLayoutViewInclusion($path, $ctrl);
		return $n;
	}
}
if (!function_exists("igk_html_node_viewcontent")) {
	/**
	 * used to evaluate the content. in xpthml file the content will be evaluated
	 */
	function igk_html_node_viewcontent($listener, $data = null)
	{
		$d = igk_html_node_noTagNode();
		$d->listener = $listener;
		$d->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, "igk_html_viewContentAcceptRender");
		return $d;
	}
}
if (!function_exists("igk_html_node_visible")) {
	/**
	 *  add a visibility server node
	 * @param mixed cond mixed callback or evaluable condition expression
	 */
	function igk_html_node_visible($cond)
	{
		return new \IGK\System\Html\Dom\HtmlVisibleNode($cond);
	}
}
if (!function_exists("igk_html_node_vscrollbar")) {
	/**
	 * create winui-vscrollbar
	 * @param mixed $cibling
	 * @param mixed $initTarget
	 */
	function igk_html_node_vscrollbar($cibling = null, $initTarget = null)
	{
		$n = igk_create_node();
		$n["class"] = "igk-vscroll";
		$n["igk:cibling"] = $cibling;
		$n["igk:target"] = $initTarget;
		return $n;
	}
}
if (!function_exists("igk_html_node_vsep")) {
	/**
	 * create winui-vsep
	 */
	function igk_html_node_vsep()
	{
		return igk_html_node_Separator("vertical");
	}
}
if (!function_exists("igk_html_node_walk")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_walk($tagname, $items, $callback)
	{
		$p = igk_html_parent_node();
		if (is_array($items)) {
			array_walk($items, function ($v) use ($p, $callback, $tagname) {
				$callback($p->add($tagname), $v);
			});
		}
		return $p;
	}
}
if (!function_exists("igk_html_node_webarticle")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_webarticle()
	{
		return igk_create_node("article");
	}
}
if (!function_exists("igk_html_node_webglgamesurface")) {
	/**
	 * create winui-webglgamesurface
	 * @param mixed $listener
	 */
	function igk_html_node_webglgamesurface($listener = null)
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-webgl-game-surface";
		if ($listener)
			$n["igk-webgl-game-attr-listener"] = $listener;
		return $n;
	}
}
if (!function_exists("igk_html_node_webmasternode")) {
	/**
	 * create a node that will only be visible on webmaster mode context
	 */
	function igk_html_node_webmasternode()
	{
		$n = igk_create_node("webmaster-node");
		$n->setCallback(CallableConstants::CAN_RENDER_TAG_METHOD, "return false;");
		$n->setCallback(CallableConstants::IS_VISIBLE_METHOD, "igk_html_callback_is_webmaster");
		return $n;
	}
}
if (!function_exists("igk_html_node_widget")) {
	/**
	 * create 
	 * @param null|string $tagname 
	 * @return HtmlWidgetNode 
	 */
	function igk_html_node_widget(?string $tagname = null)
	{
		return new \IGK\System\Html\Dom\HtmlWidgetNode($tagname);
	}
}
if (!function_exists("igk_html_node_word")) {
	/**
	 * create winui-word
	 * @param mixed $v
	 * @param mixed $cl
	 */
	function igk_html_node_word($v, $cl)
	{
		$n = igk_create_node("span");
		$n->Content = $v;
		$n["class"] = "wd w-" . $cl;
		return $n;
	}
}
if (!function_exists("igk_html_node_wordcasesplitter")) {
	/**
	 * create winui-wordcasesplitter
	 * @param mixed $v
	 * @param mixed $split
	 */
	function igk_html_node_wordcasesplitter($v, $split = 5)
	{
		$n = igk_create_node();
		$n->setClass("igk-wc-splitter");
		if (is_string($v)) {
			$o = igk_str_explode_upperCase($v);
			$w = 1;
			foreach ($o as  $sv) {
				if (empty($sv))
					continue;
				$n->add("span")->setClass("w_" . $w)->setContent($sv);
				$w = (++$w % $split);
			}
		}
		return $n;
	}
}
if (!function_exists("igk_html_node_wordsplitview")) {
	/**
	 * create winui-wordsplitview
	 */
	function igk_html_node_wordsplitview()
	{
		$n = igk_create_node("div");
		$n["class"] = "igk-ui-wplitview";
		return $n;
	}
}
if (!function_exists("igk_html_node_xmlnode")) {
	/**
	 * create xml node
	 */
	function igk_html_node_xmlnode($tag)
	{
		return igk_create_xmlnode($tag);
	}
}
if (!function_exists("igk_html_node_xmlviewer")) {
	/**
	 * function __desc__
	 */
	function igk_html_node_xmlviewer()
	{
		return new \IGK\System\Html\Dom\HtmlXmlViewerNode();
	}
}
if (!function_exists("igk_html_node_xslt")) {
	/**
	 * create winui-xslt
	 * @param mixed $xml
	 * @param mixed $xslt
	 * @param mixed $global
	 * @param mixed $options
	 */
	function igk_html_node_xslt($xml, $xslt, $global = 0, $options = null)
	{
		$header = igk_xml_header();
		$o = $global ? $xslt : <<<EOF
{$header}
<xsl:stylesheet version="1.0"
xmlns="http://www.w3.org/1999/xhtml"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
<xsl:template match="/">{$xslt}</xsl:template>
</xsl:stylesheet>
EOF;
		$n = igk_create_notagnode();
		$n->addObData("<!--" . $xml . "-->", 'div')->setClass("xml")->setStyle("display:none");
		$n->addObData("<!--" . $o . "-->", 'div')->setClass("xslt")->setAttribute("xslt:data", $options)->setStyle("display:none");
		$n->addBalafonJS()->Content = "igk.dom.xslt.initTransform();";
		return $n;
	}
}
if (!function_exists("igk_html_node_xsltranform")) {
	/**
	 * create winui-xsltranform
	 * @param mixed $xmluri
	 * @param mixed $xsluri
	 * @param mixed $target
	 */
	function igk_html_node_xsltranform($xmluri, $xsluri, $target = null)
	{
		if (!isset($xmluri) || empty($xmluri))
			throw new IGKException("xmluri not specified");
		if (!isset($xmluri) || empty($xmluri))
			throw new IGKException("xsluri not specified");
		$n = igk_create_node('div');
		$n->setClass("igk-xsl-node");
		if ($target)
			$target = ", target:'$target'";
		$n->Attributes->Set("igk:xslt-data", "{xml:'$xmluri', xsl:'$xsluri'{$target} }");
		return $n;
	}
}
if (!function_exists("igk_html_node_yield")) {
	/**
	 * auto generate doc.
	 * @param mixed $args
	 * @return HtmlNoTagNode
	 */
	function igk_html_node_yield(string $hook, ...$args)
	{
		$n = igk_html_node_notagnode();
		$n->addObData(
			function () use ($hook, $args) {
				igk_hook($hook, ...$args);
			},
			null
		);
		return $n;
	}
}
if (!function_exists("igk_html_set_tooltip")) {
	/**
	 * set placehost for input
	 */
	function igk_html_set_tooltip($n, $m)
	{
		$n->input->setAttribute("placeholder", $m);
		return $n;
	}
}
if (!function_exists("igk_html_textareav_callback")) {
	/**
	 * function igk_html_textareav_callback
	 */
	function igk_html_textareav_callback()
	{
		igk_die("value not implement");
		return;
	}
}
if (!function_exists("igk_html_viewcontentacceptrender")) {
	/**
	 * function igk_html_viewcontentacceptrender
	 * @param mixed $a
	 * @param mixed $b
	 */
	function igk_html_viewcontentacceptrender($a, $b)
	{
		$a->clearChilds();
		if ($a->listener) {
			$a->add($a->listener);
			return 1;
		}
		return 0;
	}
}
if (!function_exists("igk_html_visibleconditionalnode")) {
	/**
	 * function igk_html_visibleconditionalnode
	 */
	function igk_html_visibleconditionalnode()
	{
		return igk_is_conf_connected();
	}
}
if (!function_exists("igk_init_renderinglang")) {
	/**
	 * function igk_init_renderinglang
	 * @param mixed $sl
	 */
	function igk_init_renderinglang($sl)
	{
		$sl->clearChilds();
		$gt = R::GetCurrentLang();
		$tab = R::GetSupportedLangs();
		if (count($tab) > 0) {
			$tab = array_merge($tab);
			foreach ($tab as  $v) {
				$op = $sl->add("option");
				$op["value"] = $v;
				$op->Content = igk_lang_display($v);
				if ($v == $gt) {
					$op["selected"] = "true";
				}
			}
			return true;
		}
		return false;
	}
}
if (!function_exists("igk_init_renderingtheme_callback")) {
	/**
	 * function igk_init_renderingtheme_callback
	 * @param mixed $n
	 */
	function igk_init_renderingtheme_callback($n)
	{
		$n->clearChilds();
		if (!$n->IsVisible) {
			return 0;
		}
		$gt = igk_web_get_config("globaltheme", 'default');
		foreach (igk_io_getfiles(igk_io_baseDir() . "/R/Themes/", "/\.theme$/i") as  $v) {
			$op = $n->add("option");
			$r = igk_io_basenamewithoutext($v);
			$op->Content = $r;
			if ($r == $gt) {
				$op["selected"] = "true";
			}
		}
		return 1;
	}
}
if (!function_exists("igk_lang_display")) {
	/**
	 * get language display utility
	 */
	function igk_lang_display($v)
	{
		static $display = null;
		if ($display == null) {
			$display = [];
			$display["fr"] = "Français";
			$display["en"] = "English";
			$display["nl"] = "Neederlands";
		}
		return igk_getv($display, $v, $v);
	}
}
if (!function_exists("igk_min_script")) {
	/**
	 * helper minify script
	 * @param mixed $s
	 */
	function igk_min_script(string $s)
	{
		$s = preg_replace("/(\n|\t|\r)/i", "", $s);
		return $s;
	}
}
if (!function_exists("igk_notifyhostbind_callback")) {
	/**
	 * function igk_notifyhostbind_callback
	 * @param mixed $host
	 * @param mixed $name
	 * @param mixed $autohide
	 * @param mixed $options
	 * @param mixed $bind
	 */
	function igk_notifyhostbind_callback($host, $name, $autohide, $options = null, $bind = null)
	{
		$c = igk_notifyctrl();
		$n = $c->getNotification($name) ?? $c->TargetNode;
		if ($n && $bind) {
			$bind->addObData(function () use ($n, $autohide) {
				$n->setAutohide($autohide);
				$n->renderAJX();
				$n->setAutohide(1);
			});
			return 1;
		}
		return 0;
	}
}
if (!function_exists("igk_pic_zone")) {
	/**
	 * function igk_pic_zone
	 * @param mixed $n
	 * @param mixed $r
	 * @param mixed $c
	 * @param mixed $base
	 * @param mixed $tab
	 * @param mixed $offset
	 */
	function igk_pic_zone($n, $r, $c, $base = 4, $tab = null, $offset = 0)
	{
		$tr = $r;
		$ct = 0;
		while ($r > 0) {
			$r--;
			$t = $n->addRow();
			$j = $c;
			while ($j > 0) {
				$j--;
				$cl = $t->addCol();
				$cl->setClass("igk-col-{$base}-1");
				$cl->div()->setClass("pic")->Content = igk_getv($tab, $ct, IGK_HTML_SPACE);
				$ct++;
			}
		}
	}
}
if (!function_exists("igk_site_map_add_uri")) {
	/**
	 * function igk_site_map_add_uri
	 * @param mixed $n
	 * @param mixed $uri
	 */
	function igk_site_map_add_uri($n, $uri = null)
	{
		$c = $n->addNode("url");
		$c->addNode("loc")->Content = igk_getv($uri, 0);
		$c->addNode("priority")->Content = 1;
	}
}
if (!function_exists('igk_html_node_logo')) {
	/**
	 * Igk html node logo.
	 * @param null|BaseController $ctrl
	 */
	function igk_html_node_logo(?BaseController $ctrl = null)
	{
		$ctrl = $ctrl ?? ViewHelper::CurrentCtrl() ?? igk_die("require controller.");
		$n = igk_create_node('div');
		$n["class"] = "logo";
		$n->img()->setSrc($ctrl->asset("/img/logo.jpg"));
		return $n;
	}
}
if (!function_exists('igk_html_node_button_group')) {
	/**
	 * add a form button group
	 * @return HtmlItemBase<mixed, string> 
	 * @throws IGKException 
	 */
	function igk_html_node_button_group()
	{
		$n = igk_create_node('div');
		$n["class"] = "igk-form-button-group";
		return $n;
	}
}
if (!function_exists('igk_html_node_connection_community')) {
	/**
	 * Igk html node connection community.
	 * @param null|string $appName
	 * @param null|string $redirectUri
	 * @param null|BaseController $ctrl
	 */
	function igk_html_node_connection_community(?string $appName = null, ?string $redirectUri = null, ?BaseController $ctrl = null)
	{
		$n = igk_create_node('div');
		$ctrl = $ctrl ?? ViewHelper::CurrentCtrl();
		$fname = ViewHelper::GetArgs('fname');
		$redirectUri = $redirectUri ?? $ctrl->getAppUri($fname . "/connect");
		$appName = $appName ?? $ctrl->getConfig('community.appName') ?? $ctrl->getConfig('appName', 'app');
		$n->yield(
			LoginServiceEvents::LoginWithSocialButton,
			[
				"controller" => $ctrl,
				"redirect_uri" => $redirectUri,
				"app_name" => $appName
			]
		);
		return $n;
	}
}
if (!function_exists('igk_html_node_bind')) {
	/**
	 * binding node to 
	 * @param mixed ...$arg 
	 * @return void 
	 */
	function igk_html_node_bind(...$arg)
	{
		$p = igk_html_parent_node() ?? igk_die('missing parent node');
		array_map(function ($a) use ($p) {
			$q = [$a];
			while (count($q) > 0) {
				$a = array_shift($q);
				if ($a instanceof HtmlItemBase) {
					$p->add($a);
				} else if ($a instanceof Closure) {
					$a($p);
				} else if (is_array($a)) {
					$q = array_merge($a, $q);
				} else if (is_string($a)) {
					$p->text($a);
				} else if (is_callable($a)) {
					$n = $a($p);
					if ($n && ($p !== $n)) {
						$p->add($n);
					}
				}
			}
		}, $arg);
		return $p;
	}
}
/**
 * create a centered grid box node
 */
function igk_html_node_centerbox_grid()
{
	$n = igk_create_node('div');
	$n['class'] = 'igk-centerbox-grid';
	return $n;
}
/**
 * create a centerd box flex box node 
 */
function igk_html_node_centerbox_flex()
{
	$n = igk_create_node('div');
	$n['class'] = 'igk-centerbox-flex';
	return $n;
}
if (!function_exists('igk_html_node_listitem')) {
	/**
	 * list items 
	 * @param array $list 
	 * @return HtmlItemBase 
	 * @throws IGKException 
	 */
	function igk_html_node_listitem(array $list)
	{
		$ul = igk_create_node('ul');
		while (count($list) > 0) {
			$q = array_shift($list);
			$ul->li()->setContent($q);
		}
		return $ul;
	}
}
if (!function_exists('igk_html_node_flex')) {
	/**
	 * Igk html node flex.
	 * @param mixed $tag
	 */
	function igk_html_node_flex($tag = 'div')
	{
		$n = igk_create_node($tag);
		$n['class'] = 'dispflex';
		return $n;
	}
}
if (!function_exists('igk_html_node_grid')) {
	/**
	 * Igk html node flex.
	 * @param mixed $tag
	 */
	function igk_html_node_flex($tag = 'div')
	{
		$n = igk_create_node($tag);
		$n['class'] = 'dispgrid';
		return $n;
	}
}
require_once IGK_LIB_CLASSES_DIR . "/System/Html/Dom/Factory.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Html/HtmlHeaderLinkHost.php";
require_once IGK_LIB_DIR . "/igk_html_utils.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Html/Helpers/factory-helper.funcs.php";
if (!function_exists('igk_html_node_balafonlogo')) {
	/**
	 * node that contant balafon logo
	 * @param string $name 
	 * @return HtmlNoTagNode 
	 */
	function igk_html_node_balafonlogo(string $name = 'balafon_logo')
	{
		$n = igk_create_notagnode();
		return $n->usesvg($name);
	}
}
if (!function_exists('igk_html_node_spacer')) {
	/**
	 * create a spacer container with content 
	 * @param string $_content
	 * @param ?string $extra_class_definition additional class to subscribe 
	 * @return HtmlNoTagNode 
	 */
	function igk_html_node_spacer($_content, ?string $extra_class_definition = null): HtmlNoTagNode
	{
		$n = igk_create_notagnode();
		$n['class'] = 'spacer-container';
		$t = $n->div()->setClass('spacer');
		if (!$extra_class_definition)
			$t['class'] = $extra_class_definition;
		if ($_content instanceof HtmlItemBase) {
			$t->add($_content);
		} else {
			$t->Content = $_content;
		}
		return $n;
	}
}
if (!function_exists('igk_html_node_mailpreview')) {
	/**
	 * helper: create an mail preview node component
	 * @return MailPreviewNode 
	 */
	function igk_html_node_mailpreview()
	{
		return new \IGK\System\Http\Mail\MailPreviewNode();
	}
}
if (!function_exists('igk_html_node_dotwaiter')) {
	/**
	 * Igk html node dotwaiter.
	 */
	function igk_html_node_dotwaiter()
	{
		$n = igk_create_node('div');
		$n['class'] = 'igk-dotwaiter';
		$n->div()->setClass('dot');
		$n->div()->setClass('dot');
		$n->div()->setClass('dot');
		return $n;
	}
}
if (!function_exists('igk_html_node_breadcrumbs')) {
	/**
	 * auto generate doc.
	 * @param mixed $ctrl
	 * @return HtmlItemBase
	 */
	function igk_html_node_breadcrumbs($menus, ?BaseController $ctrl = null, $selected = null)
	{
		$ctrl = $ctrl ?? ViewHelper::CurrentCtrl();
		$root = $ul = igk_create_node('ul')->setAttributes([
			'class' => 'igk-winui-breadcrumb',
			'aria-label' => 'Breadcrumbs'
		]);
		$tq = [[$ul, $menus]];
		$item_c = 1;
		while (count($tq) > 0) {
			$q = array_shift($tq);
			list($ul, $menus) = $q;
			foreach ($menus as $k => $v) {
				$li = $ul->li();
				if (is_string($v)) {
					$v = ['text' => is_numeric($k) ? 'item_' . ($item_c++) : $k, 'uri' => $v];
				}
				list($text, $uri, $childs, $current_page, $id) = igk_extract($v, 'text|uri|childs|current_page|id');
				if (igk_str_startwith($uri, '@/')) {
					$uri = $ctrl::uri(substr($uri, 1));
				}
				$attr = [];
				if ($current_page) {
					$attr['aria-current'] = 'page';
				}
				$li->a($uri)->setAttributes($attr)->Content = $text;
				if ($childs) {
					$cul = $li->ul();
					array_unshift($tq, [$cul, $childs]);
				}
			}
		}
		return $root;
	}
}
if (!function_exists('igk_html_node_markdown')) {
	/**
	 * Igk html node markdown.
	 * @param string $content
	 * @param null|mixed $options
	 */
	function igk_html_node_markdown(string $content, $options = null)
	{
		list(
			$allowlinks,
			$allowBreakLine,
			$formatCodeBlock,
			$baseURL,
			$mentionBaseURL
		) = igk_extract($options, 'allowLinkDocument|allowBreakLine|formatCodeBlock|baseURL|mentionBaseURL');
		$conv = new \IGK\System\IO\Markdown\MarkdownConverter;
		$conv->allowLinkDocument = $allowlinks ?? false;
		$conv->allowBreakLine = $allowBreakLine ?? true;
		$conv->encapsulateTextInTag = true;
		$conv->mentionBaseURL = $mentionBaseURL;
		$conv->baseURL = $baseURL;
		$conv->formatCodeBlock = $formatCodeBlock ?? false;
		$n = igk_html_host('div.md-doc');
		$g = $conv->transformToHtml($content);
		$n->text($g);
		return $n;
	}
}

if (!function_exists('igk_html_node_markdown_file')){
	/**
	 * create a markdown node from file
	 */
	function igk_html_node_markdown_file(string $file, $options=null){
		$src = file_get_contents($file);
		return igk_html_node_markdown($src, $options);
	}
}



if (!function_exists('igk_html_node_x_template')) {
	/**
	 * Igk html node x template.
	 */
	function igk_html_node_x_template()
	{
		$n = new HtmlNode('template');
		return $n;
	}
}
if (!function_exists('igk_html_node_inflate')) {
	/**
	 * inflate view dans data 
	 * @param string $file 
	 * @param mixed $data 
	 * @return void 
	 */
	function igk_html_node_inflate(string $file, $data = null) {}
}
require_once __DIR__.'/Inc/html/forms/actionbar.pinc';