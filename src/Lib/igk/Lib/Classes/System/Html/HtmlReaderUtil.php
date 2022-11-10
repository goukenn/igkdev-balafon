<?php

///<summary></summary>
///<param name="xreader"></param>
///<param name="inf" ref="true"></param>

use IGK\System\Html\HtmlUtils;
use IGK\XML\XMLNodeType;

/**
 * 
 * @param mixed $xreader 
 * @param mixed $inf 
 * @deprecated not used
 */
// function igk_xml_xpath_objectcallback($xreader, &$inf)
// {
//     if (!isset($inf->cmode)) {
//         $inf->cmode = 0;
//     }
//     $n = $xreader->name;
//     switch ($xreader->nodetype) {
//         case XMLNodeType::ENDELEMENT:
//             $t = array_shift($inf->pathinfo);
//             if ($inf->start && ($inf->path == $t)) {
//                 $inf->start = 0;
//                 $inf->max--;
//                 $inf->current = null;
//                 if ($inf->max < 0) {
//                     return 0;
//                 }
//             } else {
//                 if ($inf->current) {
//                     $inf->current = igk_getv($inf->current, 'parent');
//                 }
//             }
//             break;
//         case XMLNodeType::ELEMENT:
//             array_unshift($inf->pathinfo, count($inf->pathinfo) == 0 ? $n : $inf->pathinfo[0] . "/{$n}");
//             if ($inf->start == 0) {
//                 if ($inf->pathinfo[0] == $inf->path) {
//                     if ($inf->min > $inf->item) {
//                         igk_xml_read_skip($xreader);
//                         $inf->item++;
//                         array_shift($inf->pathinfo);
//                         return 1;
//                     }
//                     $inf->start = 1;
//                     $o = igk_createobj($xreader->attribs);
//                     $inf->current = array(
//                         "o" => $o,
//                         "parent" => null,
//                         "path" => $inf->pathinfo[0],
//                         "name" => $n
//                     );
//                     $inf->objects[] = $o;
//                 }
//             } else {
//                 $o = null;
//                 if (igk_count($xreader->attribs) > 0) {
//                     $o = igk_createobj($xreader->attribs);
//                 }
//                 if ($inf->current['o'] === null) {
//                     $for = $inf->current['name'];
//                     $co = igk_createobj();
//                     $co->content = $inf->current["parent"]['o']->$for;
//                     $inf->current["parent"]['o']->$for = $co;
//                     $inf->current = array(
//                         "o" => $co,
//                         "parent" => $inf->current["parent"],
//                         "name" => $for
//                     );
//                 }
//                 $inf->current["o"]->$n = $o;
//                 $inf->current = array("o" => $o, "parent" => $inf->current);
//                 if ($xreader->isEmpty) {
//                     array_shift($inf->pathinfo);
//                     $inf->current = $inf->current["parent"];
//                 } else {
//                     $inf->current["name"] = $n;
//                 }
//             }
//             break;
//         case XMLNodeType::TEXT:
//         case XMLNodeType::CDATA:
//             if ($inf->start && $xreader->value && $inf->current) {
//                 $c = $inf->current["name"];
//                 if (!empty($c)) {
//                     $s = $xreader->value;
//                     $ii = igk_getv($inf->current['parent']['o'], $c);
//                     if (!isset($ii)) {
//                         $inf->current['parent']['o']->$c = $s;
//                     } else {
//                         if (is_object($ii)) {
//                             $ii->content = $s;
//                             $inf->current['parent']['o']->$c->content = $s;
//                         } else {
//                             if (is_string($ii)) {
//                                 $inf->cmode = 4;
//                                 $inf->current['parent']['o']->$c .= $s;
//                             } else {
//                                 $inf->current['parent']['o']->$c->content = $s;
//                             }
//                         }
//                     }
//                 }
//             }
//             break;
//     }
//     return 1;
// }


///<summary></summary>
///<param name="xreader"></param>
/**
 * 
 * @param mixed $xreader 
 * @deprecated use HtmlReader to read node
 */
// function igk_xml_read_node($xreader)
// {
//     igk_die(__FUNCTION__);
//     $n = $xreader->name;
//     $n = igk_create_xmlnode($n);
//     $n->setAttributes($xreader->attribs);
//     $g = $xreader->offset;
//     igk_xml_read_skip($xreader);
//     $d = $xreader->offset;
//     $txt = substr($xreader->text, $g, $d - $g + 1);
//     $n->load($txt);
//     return $n;
// }

///<summary>read xml object from file memory stream</summary>
/**
 * read xml object from file memory stream
 */
// function igk_xml_read_stream($f, $callback, $inf)
// {
//     if (!file_exists($f))
//         return;
//     if ($hf = fopen($f, "r")) {
//         $inf = igk_xml_create_readinfo($inf);
//         $xreader = (object)[];
//         $xreader->offset = $inf->offset;
//         $xreader->text = $hf;
//         $xreader->length = -1;
//         $xreader->name = "";
//         $xreader->value = "";
//         $xreader->type = "";
//         $xreader->parent = null;
//         $xreader->context = "stream";
//         $xreader->nodetype = 0;
//         $xreader->inf = $inf;
//         $xreader->attribs = null;
//         while (igk_xml_read($xreader)) {
//             if (!$callback($xreader, $inf)) {
//                 break;
//             }
//         }
//         $inf->offset = $xreader->offset;
//         if ($inf->count === null) {
//             $inf->count = igk_count($inf->objects);
//         }
//         fclose($hf);
//         igk_xml_unset_read_info($inf);
//     }
// }

///<summary>skiping reader</summary>
/**
 * skiping reader
 */
// function igk_xml_read_skip($xreader)
// {
//     $q = $xreader;
//     if ($q->nodetype == XMLNodeType::ELEMENT) {
//         if (!$q->isEmpty) {
//             $n = strtolower($q->name);
//             $depth = 0;
//             $end = false;
//             while (!$end && igk_xml_read($q)) {
//                 switch ($q->nodetype) {
//                     case XMLNodeType::ELEMENT:
//                         if (!$q->isEmpty) {
//                             $depth++;
//                         }
//                         break;
//                     case XMLNodeType::ENDELEMENT:
//                         if (($depth == 0) && (strtolower($q->name) == $n)) {
//                             $end = true;
//                         } else if ($depth > 0)
//                             $depth--;
//                         break;
//                 }
//             }
//             return $end;
//         }
//     }
//     return false;
// }

///<summary>xml reader info</summary>
/**
 * xml reader info
 * @deprecated use HtmlReader class  
 */
// function igk_xml_read($xreader)
// {
//     $eof = 0;
//     $v_c = 0;
//     $v_enter = 0;
//     $q = $xreader;
//     $v = "";
//     $canread = null;
//     $init = "::init";
//     $xreader->attribs = null;
//     if (!isset($xreader->$init)) {
//         if ($xreader->context == "stream") {
//             fseek($xreader->text, $xreader->offset);
//             $hfile = $xreader->text;
//             $xreader->offset = 0;
//             $canread = function () use (&$xreader, &$hfile, &$eof) {
//                 if ($eof)
//                     return 0;
//                 $buffsize = $xreader->inf->bufferSize;
//                 if ($xreader->length == -1) {
//                     $b = @fread($hfile, $buffsize);
//                     if ($b) {
//                         $xreader->length = strlen($b);
//                         $xreader->text = $b;
//                         return 1;
//                     }
//                     return 0;
//                 } else {
//                     if ($xreader->offset < $xreader->length)
//                         return 1;
//                     $b = fread($hfile, $buffsize);
//                     $xreader->length += strlen($b);
//                     $xreader->text .= $b;
//                     return ($xreader->offset < $xreader->length);
//                 }
//             };
//         } else {
//             if ($xreader->offset >= $xreader->length)
//                 return 0;
//             $canread = function () use (&$xreader, &$eof) {
//                 return !$eof && ($xreader->offset < $xreader->length);
//             };
//         }
//         $readnext = function ($count) use ($q, &$canread) {
//             $readsub = "";
//             $ics = $q->offset;
//             $q->offset++;
//             $icx = $q->offset;
//             $icc = $count;
//             $ico = $icc;
//             while ($canread() && ($icc > 0) && ($m = $q->text[$icx])) {
//                 $icc--;
//                 $readsub .= $m;
//                 $icx++;
//                 $q->offset++;
//             }
//             $q->offset = $ics;
//             return $readsub;
//         };
//         $readname = function () use (&$xreader, $canread) {
//             $v = IGK_STR_EMPTY;
//             $s = "/" . IGK_TAGNAME_CHAR_REGEX . "/i";
//             while ($canread() && preg_match($s, $k = $xreader->text[$xreader->offset])) {
//                 $v .= $k;
//                 $xreader->offset++;
//             }
//             return $v;
//         };
//         $__readTextValue = function () use (&$xreader, $canread) {
//             $q = $xreader;
//             $v = IGK_STR_EMPTY;
//             $p = $q->offset;
//             while ($canread()) {
//                 $v .= $q->text[$q->offset];
//                 $q->offset++;
//                 if (substr($v, -1, 1) == "<") {
//                     $v = substr($v, 0, strlen($v) - 1);
//                     if (empty(trim($v))) {
//                         $q->offset -= 2;
//                         $q->name = null;
//                         $q->value = null;
//                         return false;
//                     }
//                     $q->offset--;
//                     $q->name = null;
//                     $q->value = $v;
//                     $q->nodetype = XMLNodeType::TEXT;
//                     return true;
//                 }
//             }
//             $q->offset = $p;
//             return false;
//         };
//         $xreader->{"can:read"} = $canread;
//         $xreader->{"can:readt"} = $__readTextValue;
//         $xreader->{"can:readn"} = $readname;
//         $xreader->{"can:readnx"} = $readnext;
//         $xreader->$init = 1;
//     } else {
//         $canread = $xreader->{"can:read"};
//         $__readTextValue = $xreader->{"can:readt"};
//         $readname = $xreader->{"can:readn"};
//         $readnext = $xreader->{"can:readnx"};
//     }
//     $i = $xreader->offset;
//     $xreader->context = "xml";
//     $r = 0;
//     while ($r = $canread()) {
//         $c = $q->text[$q->offset];
//         switch ($c) {
//             case "<":
//                 $v_enter = true;
//                 break;
//             case "?":
//                 if ($v_enter) {
//                     $q->offset++;
//                     $v = IGK_STR_EMPTY;
//                     while ($canread()) {
//                         $v .= $q->text[$q->offset];
//                         $q->offset++;
//                         if (substr($v, -2, 2) == "/*") {
//                             igk_die("multi", $v);
//                         } else {
//                             if (substr($v, -2, 2) == "?>") {
//                                 $v = substr($v, 0, strlen($v) - 3);
//                                 $q->name = null;
//                                 $q->value = $v;
//                                 $q->nodetype = XMLNodeType::PROCESSOR;
//                                 return true;
//                             }
//                         }
//                     }
//                 } else {
//                     return $__readTextValue();
//                 }
//                 return false;
//             case "!":
//                 if ($v_enter) {
//                     $lf = substr($q->text, $q->offset + 1, 2);
//                     $readsub = $readnext(2);
//                     if ($readsub == "--") {
//                         $q->offset += 3;
//                         $v = IGK_STR_EMPTY;
//                         while ($canread()) {
//                             $v .= $q->text[$q->offset];
//                             $q->offset++;
//                             if (substr($v, -3, 3) == "-->") {
//                                 $v = substr($v, 0, strlen($v) - 3);
//                                 $q->name = null;
//                                 $q->value = $v;
//                                 $q->nodetype = XMLNodeType::COMMENT;
//                                 return true;
//                             }
//                         }
//                     } else {
//                         $readsub = $readnext(7);
//                         if (strlen($readsub) == 7) {
//                             $readsub = strtoupper($readsub);
//                             switch ($readsub) {
//                                 case "[CDATA[":
//                                     $q->offset += 8;
//                                     $v = IGK_STR_EMPTY;
//                                     while ($canread()) {
//                                         $v .= $q->text[$q->offset];
//                                         $q->offset++;
//                                         if (substr($v, -3, 3) == "]]>") {
//                                             $v = substr($v, 0, strlen($v) - 3);
//                                             $q->name = null;
//                                             $q->value = $v;
//                                             $q->nodetype = XMLNodeType::CDATA;
//                                             return true;
//                                         }
//                                     }
//                                     break;
//                                 case "DOCTYPE":
//                                     $q->offset += 8;
//                                     $v = IGK_STR_EMPTY;
//                                     while ($canread()) {
//                                         $v .= $q->text[$q->offset];
//                                         $q->offset++;
//                                         if (substr($v, -1, 1) == ">") {
//                                             $v = substr($v, 0, strlen($v) - 1);
//                                             $q->name = null;
//                                             $q->value = $v;
//                                             $q->nodetype = XMLNodeType::DOCTYPE;
//                                             return true;
//                                         }
//                                     }
//                                     break;
//                             }
//                         }
//                         return false;
//                     }
//                     return false;
//                 }
//                 break;
//             case "/":
//                 if ($v_enter) {
//                     $q->offset += 1;
//                     $q->nodetype = XMLNodeType::ENDELEMENT;
//                     $q->name = $readname($q);
//                     $q->value = null;
//                     $v_enter = false;
//                     while (($v_c > $q->offset) && ($q->text[$q->offset] !== '>')) {
//                         $q->offset++;
//                     }
//                     return true;
//                 }
//                 $v .= $c;
//                 break;
//             default:
//                 if (!$v_enter) {
//                     if ($q->nodetype == XMLNodeType::ELEMENT) {
//                         $match = array();
//                         switch (strtolower($q->name)) {
//                             case "script":
//                             case "code":
//                                 $tag = strtolower($q->name);
//                                 while ($canread()) {
//                                     $v .= $q->text[$q->offset];
//                                     $q->offset++;
//                                     if (preg_match("/\<\/([\s]*)" . $tag . "([\s]*)\>$/i", $v, $match)) {
//                                         $q->offset -= strlen($match[0]);
//                                         $v = substr($v, 0, strlen($v) - strlen($match[0]));
//                                         break;
//                                     }
//                                 }
//                                 $q->name = null;
//                                 $q->value = $v;
//                                 $q->nodetype = XMLNodeType::TEXT;
//                                 return true;
//                             case "style":
//                                 while ($canread()) {
//                                     $v .= $q->text[$q->offset];
//                                     $q->offset++;
//                                     if (preg_match("/(\<\/([\s]*)style([\s]*)>)$/i", $v, $match)) {
//                                         $q->offset -= strlen($match[0]);
//                                         $v = substr($v, 0, strlen($v) - strlen($match[0]));
//                                         break;
//                                     }
//                                 }
//                                 $q->name = null;
//                                 $q->value = $v;
//                                 $q->nodetype = XMLNodeType::TEXT;
//                                 return true;
//                             default: {
//                                     if ($__readTextValue()) {
//                                         return true;
//                                     }
//                                 }
//                                 break;
//                         }
//                     } else {
//                         $v = IGK_STR_EMPTY;
//                         if ($q->nodetype == XMLNodeType::ENDELEMENT)
//                             $q->offset++;
//                         while ($canread()) {
//                             $v .= $q->text[$q->offset];
//                             $q->offset++;
//                             if (substr($v, -1, 1) == "<") {
//                                 $v = substr($v, 0, strlen($v) - 1);
//                                 if (empty(trim($v))) {
//                                     $q->offset -= 2;
//                                     break;
//                                 }
//                                 $q->offset--;
//                                 $q->name = null;
//                                 $q->value = $v;
//                                 $q->nodetype = XMLNodeType::TEXT;
//                                 return true;
//                             }
//                         }
//                     }
//                 } else {
//                     $q->name = $readname();
//                     $q->value = null;
//                     $q->nodetype = XMLNodeType::ELEMENT;
//                     $q->isEmpty = false;
//                     $q->hasAttrib = false;
//                     $v_end = false;
//                     $v = IGK_STR_EMPTY;
//                     $v_readname = false;
//                     $v_readvalue = false;
//                     $v_attribname = null;
//                     $v_ch = null;
//                     $v_startattribvalue = false;
//                     $v_attribmatch = IGK_STR_EMPTY;
//                     $v_bracketstart = false;
//                     $v_bracketch = "";
//                     $v_expressions = array();
//                     while ($canread()) {
//                         $v_ch = $q->text[$q->offset];
//                         $q->offset++;
//                         if (preg_match("/(\[)/", $v_ch)) {
//                             if (!$v_startattribvalue) {
//                                 $exp = $v_ch . igk_str_read_brank($q->text, $q->offset, ']', '[');
//                                 $cout = igk_count($v_expressions);
//                                 $v_expressions[] = $exp;
//                                 $v .= "@igk:expression{$cout}=\"{$cout}\"";
//                                 continue;
//                             }
//                         }
//                         $v .= $v_ch;
//                         if (preg_match("('|\")", $v_ch)) {
//                             if ($v_startattribvalue) {
//                                 if ($v_attribmatch == $v_ch)
//                                     $v_startattribvalue = false;
//                             } else {
//                                 $v_startattribvalue = true;
//                                 $v_attribmatch = $v_ch;
//                             }
//                         }
//                         if ($v_startattribvalue)
//                             continue;
//                         if (substr($v, -2, 2) == "/>") {
//                             $v = trim(substr($v, 0, strlen($v) - 2));
//                             $q->isEmpty = true;
//                             break;
//                         } else if (substr($v, -1, 1) == ">") {
//                             $v = substr($v, 0, strlen($v) - 1);
//                             $q->isEmpty = false;
//                             break;
//                         }
//                         if ($v_readname == false) {
//                             if (preg_match("/([\s])/", $v_ch)) {
//                                 $v_attribname = $v_ch;
//                                 $v_readname = true;
//                                 $v_readvalue = false;
//                             }
//                         } else {
//                             if (preg_match("/[\s]/", $v_ch))
//                                 $v_attribname .= $v_ch;
//                             else {
//                                 $v_readname = false;
//                             }
//                         }
//                     }
//                     if (!empty($v)) {
//                         $q->hasAttrib = true;
//                         $m = null;
//                         $machv = "(?P<value>";
//                         $machv .= "([\"](([^\"]*(')?(\"\")?)+)[\"])";
//                         $machv .= "|([\'](([^']*(\")?('')?)+)[\'])";
//                         $machv .= "|(([^\s]*)+)";
//                         $machv .= ")";
//                         $acount = preg_match_all("/(?P<name>(@?" . IGK_TAGNAME_CHAR_REGEX . "+))[\s]*=[\s]*(" . $machv . ")/i", $v, $m);
//                         $q->attribs = array();
//                         for ($cc = 0; $cc < $acount; $cc++) {
//                             $k = $m["name"][$cc];
//                             if (preg_match("/^@igk:expression/", $k)) {
//                                 $q->attribs[$k] = $v_expressions[HtmlUtils::GetAttributeValue($m["value"][$cc], $q->context)];
//                             } else
//                                 $q->attribs[$k] = HtmlUtils::GetAttributeValue($m["value"][$cc], $q->context);
//                         }
//                     }
//                     return true;
//                 }
//                 break;
//         }
//         ++$q->offset;
//     }
//     return $r;
// }