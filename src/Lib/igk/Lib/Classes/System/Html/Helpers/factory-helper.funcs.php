<?php

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
Factory::form("fields", function ($fields, ?array $datasource = null, ?object $engine = null, ?string $tag = null) {
	if ($f = igk_html_parent_node()) {
		$f->addFields(...func_get_args());
	}
	return $f;
});
