<?php
// @author: C.A.D. BONDJE DOUE
// @filename: conf.php
// @date: 20220831 14:14:06
// @desc: configuration function helpers

use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\Html\HtmlNodeType;
use IGK\System\IO\Path;
use IGK\XML\XMLNodeType;

require_once __DIR__ . '/io.php';
require_once __DIR__ . '/xml.php';

///<summary>used to load configuration file.</summary>
///<doc>configuration file are xml file that store primary </doc>
///<param name="file">xml file to load</param>
///<param name="tag">root name tag</param>
///<param name="obj">object where to load</param>
/**
 * used to load configuration file.
 * @param mixed $file xml file to load
 * @param mixed $tag root name tag
 * @param mixed $obj object where to load
 */
function igk_conf_load_file($file, $tag = IGK_CNF_TAG, $obj = null)
{
    $s = igk_io_read_allfile($file);
    $o = igk_conf_load_content($s, $tag);
    return $o;
}

///<summary></summary>
///<param name="s"></param>
///<param name="tag" default="configs"></param>
///<param name="deftext" default="text"></param>
/**
 * 
 * @param mixed $s 
 * @param mixed $tag 
 * @param mixed $deftext 
 */
function igk_conf_load_content($s, $tag = "configs", $deftext = "text")
{
    $div = igk_create_xmlnode("dummy");
    $div->Load($s);
    $h = ($div->getElementsByTagName($tag));
    $d = igk_getv($h, 0);
    if ($d) {
        $t = array();
        igk_conf_load_attribs($t, $d);
        $childs = $d->getChilds();
        if ($childs)
            foreach ($d->Childs as $k) {
                if ($k->getNodeType() == XMLNodeType::TEXT)
                    continue;
                $n = $k->getTagName();
                if (empty($n)){
                    continue;
                }
                $o = null;
                if ($k->getChildCount() <= 0) {
                    $sk = $k->getInnerHtml();

                    if ($k->getHasAttributes()) {
                        $o = igk_createobj();
                        igk_conf_load($o, $k);
                    }
                    if (isset($t[$n])) {
                        if (!is_array($t[$n])) {
                            $t[$n] = array($t[$n]);
                        }
                        $t[$n][] = $o;
                    } else {
                        if ($o) {
                            if (!empty($sk))
                                $o->{$deftext} = $k;
                            $t[$n] = $o;
                        } else if (!empty($sk))
                            $t[$n] = $sk;
                    }
                } else {
                    $v_ob = igk_createobj();
                    igk_conf_load($v_ob, $k);
                    if (isset($t[$n])) {
                        if (!is_array($t[$n])) {
                            $t[$n] = array($t[$n]);
                        }
                        $t[$n][] = $v_ob;
                    } else
                        $t[$n] = $v_ob;
                }
            }
        return (object)$t;
    }
    return null;
}


///<summary></summary>
///<param name="t" ref="true"></param>
///<param name="d"></param>
/**
 * 
 * @param mixed $t 
 * @param mixed $d 
 */
function igk_conf_load_attribs(&$t, $d)
{
    if ($d->HasAttributes) {
        foreach ($d->Attributes as $k => $s) {
            $t[$k] = $s;
        }
    }
}


///<summary>used to load configuration settings</summary>
///<param name="obj">output object</param>
///<param name="n">igk html node to load</param>
/**
 * used to load configuration settings
 * @param mixed $obj output object
 * @param mixed $n igk html node to load
 */
function igk_conf_load($obj, $n, ?callable $attr_filter = null)
{
    if (!isset($n))
        return null;
    $tab = array();
    array_push($tab, (object)array("t" => $obj, "n" => $n, 'path' => '/'));
    while (count($tab) > 0) {
        $q = array_pop($tab);
        if (!$q) continue;
        $sv = false;
        $ct = $q->n->getContent();
        if ($q->n->getHasAttributes()) {
            $v_attrs = $q->n->getAttributes()->to_array();
            if ($attr_filter) {
                $v_attrs = $attr_filter($v_attrs, $q->path);
            }
            if ($v_attrs) {
                $sv = true;
                foreach ($v_attrs as $m => $mc) {
                    $q->t->{$m} = $mc;
                }
                if ($ct && !empty(trim($ct))) {
                    $q->t->value = $ct;
                }
            }
        }
        if ($q->n->ChildCount == 1) {
            if ($q->n->Childs[0]->TagName == "!CDATA") {
                $h = $q->n->TagName;
                $q->p->$h = $q->n->Childs[0]->Content;
                continue;
            }
        }
        if ($q->n->hasChilds() && ($attr = $q->n->Childs)) {
            foreach ($attr as $v) {
                if (($v instanceof HtmlTextNode) || ($v->getNodeType() == XMLNodeType::TEXT)){
                    continue;
                }
                if (($v->ChildCount <= 0) && !$v->HasAttributes) {
                    $q->t->{$v->TagName} = $v->getInnerHtml();
                } else {
                    $cb = igk_createobj();
                    $tn = $v->TagName;
                    array_push($tab, (object)array("t" => $cb, "n" => $v, "p" => $q->t, "path" => Path::Combine($q->path, $tn)));
                    if (isset($q->t->{$tn})) {
                        if (!is_array($q->t->{$tn})) {
                            $q->t->{$tn} = array($q->t->{$tn});
                        }
                        $q->t->{$tn}[] = $cb;
                    } else
                        $q->t->{$tn} = $cb;
                }
            }
        } else if (!$sv){
            // setting value 
            if (is_object($q->t)){
                unset($q->t);
                if (($n = basename($q->path)) != '/'){
                    $q->p->{$n} = $ct;
                }
            }
        }
    }
}
