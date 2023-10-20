<?php
// @author: C.A.D. BONDJE DOUE
// @filename: factory-helper.funcs.php 
// @desc: helper dom factory helper initiator

/**
 * factory helpe
 */

use IGK\Helper\Activator;
use IGK\System\Html\Dom\Factory;

Factory::form("initfield", function () {
	if ($f = igk_html_parent_node()) {
		igk_html_form_initfield($f);
	}
	return $f;
});
Factory::form("ajx", function ($target = null) {
	if ($f = igk_html_parent_node()) {
		$f["igk-ajx-form"] = 1;
		$f["igk-ajx-form-target"] = $target;
	}
	return $f;
});
Factory::form("multipart", function () {
	if ($f = igk_html_parent_node()) {
		$f["enctype"] = IGK_HTML_ENCTYPE;
	}
	return $f;
});
Factory::form("hiddenFields", function (array $fields) {
	if ($f = igk_html_parent_node()) {
		foreach ($fields as $k => $v) {
			$f->addInput($k, "hidden", $v);
		}
	}
	return $f;
});
Factory::tr("td_cell", function ($c, $attr = null) {
	if ($f = igk_html_parent_node()) {
		$td = $f->td();
		$td->setAttributes($attr ?? []);
		if (!empty($c)) {
			$td->setContent($c);
		} else {
			$td->nbsp();
		}
	}
	return $f;
});
Factory::table("header", function (...$header) {
	if ($f = igk_html_parent_node()) {
		$f->tr()->loop($header)->host(function ($n, $v) {
			if (empty($v)) {
				$n->th()->nbsp();
			} else {
				if (is_array($v)) {
					$text = igk_getv($v, "text", igk_getv($v, 0));
					$attribs = null;
					foreach (["attribs", "attributes"] as $s) {
						if (key_exists($s, $v)) {
							$attribs = igk_getv($v, $s);
							break;
						}
					}
					$th = $n->th();
					if ($attribs)
						$th->setAttributes($attribs);
					$th->Content = $text;
					return;
				}
				$n->th()->Content = $v;
			}
		});
	}
	return $f;
});
Factory::table("row", function (array $item) {
	if ($f = igk_html_parent_node()) {
		$c = null;
		foreach ($item as $v) {
			if ($c === null)
				$c = $f->tr();
			$c->td()->Content = $v;
		}
	}
});
Factory::form("cref", function () {
	if ($f = igk_html_parent_node()) {
		$f->addObData("igk_html_form_cref", null);
	}
	return $f;
});

/**
 * add field to form tag 
 */
Factory::form("fields", function ($fields, ?array $datasource = null, ?object $engine = null, ?string $tag = null) {
	if ($f = igk_html_parent_node()) {
		$f->addFields(...func_get_args());
	} 
	return $f;
});


/**
 * help build table definition 
 */
Factory::table('build', function ($data, $headers = null, $captions = null) {
	$f = igk_html_parent_node();
	if (empty($data)) {
		$f->comment('empty result');
		return;
	}
	if (is_null($headers)) {
		$headers = [];
		$rf = array_reverse($data);
		$g = array_pop($rf); // array_reverse($data));
		// $g = array_first_value($data);
		foreach (array_keys((array)$g) as $a) {
			$inf = new \IGK\System\Html\Dom\HtmlTableHeaderInfo;
			$inf->key = $a;
			$headers[] = $inf;
		}
	} else {
		foreach ($headers as $k => $a) {
			if (($a instanceof \IGK\System\Html\Dom\HtmlTableHeaderInfo)) {
				continue;
			}
			if (empty($a)) {
				$inf = new \IGK\System\Html\Dom\HtmlTableHeaderInfo();
				$headers[$k] = $inf;
				continue;
			}
			$inf =  null;
			$l  = null;
			if (is_numeric($k)) {
				if (is_string($a)) {
					$l = $a . '';
				} else {
					$inf = Activator::CreateNewInstance(\IGK\System\Html\Dom\HtmlTableHeaderInfo::class, $a);
				}
			} else {
				$l = $k;
			}
			$inf = $inf ?? new \IGK\System\Html\Dom\HtmlTableHeaderInfo;
			if ($l) {
				$inf->title = $l;
				$inf->key = $l;
			}
			// destroy keys because in foreach
			$headers[$k] = $inf;
		}
	}
	if ($headers) {
		$trow = $f->thead()->tr();
		$rows = [];
		foreach ($headers as $k) {
			$trow->th()->Content = $k->title;
			$rows[] = $k;
		}
		$b = $f->tbody();
		foreach ($data as $k) {
			$tr = $b->tr();
			$pos = 0;
			foreach ($rows as $m) {
				if ((is_string($m) && empty($m)) || $m->isEmpty()) {
					$m->fillEmpty($tr->td(), $k, $pos);
				} else {
					$vv = $k;
					if (!is_numeric($k) && !is_string($k)) {
						$vv = igk_getv($k, $m->key);
					}
					$m->fillContent($tr->td(), $vv, $k, $pos);
				}
			}
		}
	}
});



// + | button
Factory::button('setValue', function($v){
    if ($q = igk_html_parent_node()){
        $q->setAttribute('value', $v);
        $q->setContent($v);
    }
    return $q;
});

/**
 * attach uri to button 
 */
Factory::button('setUri', function($v){
    if ($q = igk_html_parent_node()){		
		$q['onclick'] = $v ? 'javascript: document.location=\''.$v.'\';return false;' : null;		
    }
    return $q;
});