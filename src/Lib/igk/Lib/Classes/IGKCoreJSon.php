<?php
// @file: IGKCoreJSon.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\HtmlUtils;

final class IGKCoreJSon extends IGKObject
{
    const ExpressionRegex = "\\{(?<expression>(.)+)\\}";
    ///<summary></summary>
    ///<param name="n"></param>
    // private static function json_key($n){
    //     if(preg_match_all("/^(?P<delimiter>('|\")*)(?P<key>(.)+)\\1$/i", $n, $tab)){
    //         return $tab["key"][0];
    //     }
    //     return $n;
    // }

    ///<summary></summary>
    ///<param name="expression"></param>
    public function ToDictionary($expression, $strict = true){
        $h = self::GetExpression($expression, $strict);
        if (is_int($h) && ($h <= 0))
            return null;
        return (igk_count($h) == 1) && is_object($m = igk_getv($h, 0)) ? $m : $h;
    }
    public static function GetExpression($exp, $strict = true)
    {
        if (is_string($exp) == false)
            return -2;
        $o = null;
        $m = 0;
        $ch = '';
        $ln = 0;
        $pos = 0;
        $tab = [];
        $v = '';
        $k = '';
        $q = null;
        $Tpos = 0;
        if ($strict)
            $valid_identifier = "/^[_a-z][_a-z0-9]*$/i";
        else
            $valid_identifier = "/^[_a-z][_a-z0-9\-]*$/i";
        array_push($tab, ["exp" => $exp, "pos" => $pos, "q" => $q, "m" => $m, "k" => $k, "v" => $v]);
        $rp = 0;
        $_json_value = function ($v) {
            return is_array($v) ? $v : igk_json_value($v);
        };
        while ($cpop = array_pop($tab)) {
            $exp = $cpop["exp"];
            $pos = $cpop["pos"];
            $q = $cpop["q"];
            $ln = strlen($exp);
            $m = $cpop["m"];
            $k = $cpop["k"];
            $v = $cpop["v"];
            while ($pos < $ln) {
                $ch = $exp[$pos];
                switch ($ch) {
                    case '{':
                        if (($m == 0) || ($m == 4) || ($m == 1)) {
                            if ($q == null) {
                                $q = new StdClass();
                                $o = $q;
                            } else {
                                if ($m == 4) {
                                    $t = igk_str_read_brank($exp, $pos, '}', $ch, null, 1);
                                    array_push($tab, ["exp" => $exp, "pos" => $pos + 1, "q" => $q, "v" => "", "k" => $k, "m" => $m]);
                                    $exp = $t;
                                    $pos = 0;
                                    $ln = strlen($exp);
                                    $qv = new StdClass();
                                    $q->$k = $qv;
                                    $s = $q;
                                    unset($q);
                                    $q = $qv;
                                    $m = 2;
                                    break;
                                } else if (empty($v = trim($v))) {
                                    igk_wln("error: identifier is empty: " . $m . " - " . $v . " . " . $pos, " k : " . $k, $q);
                                    return -1;
                                }
                                $k = $v;
                                $q->$v = new StdClass();
                            }
                            $m = 2;
                        } else if ($m == 6) {
                            if (!empty($v = trim($v))) {
                                igk_wln_e("value is not empty and {:" . $v);
                                return -1;
                            }
                            $t = igk_str_read_brank($exp, $pos, '}', $ch, null, 1);
                            array_push($tab, ["exp" => $exp, "pos" => $pos + 1, "q" => $q, "v" => "", "k" => $k, "m" => $m]);
                            $exp = $t;
                            $ln = strlen($exp);
                            $pos = 0;
                            unset($q);
                            $q = new StdClass();
                            $o[] = $q;
                            $m = 2;
                            break;
                        } else {
                            igk_set_env('error://' . __FUNCTION__, "error: { not correctly detected: " . $m . "pos : " . $pos);
                            return -1;
                        }
                        break;
                    case '[':
                        $t = igk_str_read_brank($exp, $pos, ']', $ch, null, 1);
                        if (($m == 0) || ($m == 4) || ($m == 6)) {
                            if ($m == 4) {
                                if (!empty(trim($v))) {
                                    $v .= $t;
                                    break;
                                }
                            }
                            array_push($tab, ["exp" => $exp, "pos" => $pos + 1, "q" => $q, "v" => "", "k" => $k, "m" => $m]);
                            $exp = $t;
                            $pos = 0;
                            $ln = strlen($exp);
                            $m = 6;
                            if ($o == null) {
                                $o = [];
                                $q = &$o;
                            } else {
                                $qv = [];
                                if (is_array($q)) {
                                    $q[] = $qv;
                                    unset($q);
                                    $q = [];
                                } else {
                                    $q->$k = $qv;
                                    unset($q);
                                    $q = &$qv;
                                }
                            }
                            break;
                        } else {
                            igk_wln_e("IGKHtmlRead Error: starting array not starting mode:{$m} Expression: " . $exp);
                        }
                        break;
                    case ',':
                        if ($m == 4) {
                            if (!empty($v)) {
                                $q->$k = $v;
                            }
                            $m = 2;
                            $v = '';
                            $k = '';
                        } else if ($m == 6) {
                            $q[] = is_string($v) ? igk_str_strip_surround($v) : $v;
                            $v = "";
                            $k = "";
                        } else {
                            igk_set_env('error://' . __FUNCTION__, "error: comma found outsite a module context. mode:" . $m . " pos:" . $pos);
                            return -1;
                        }
                        break;
                    case ':':
                        if ($m == 3) {
                            $m = 4;
                        } else if ($m == 4) {
                            $v .= $ch;
                        } else if ($m == 2) {
                            if (empty($v = trim($v)) || !preg_match($valid_identifier, $v)) {
                                igk_set_env('error://' . __FUNCTION__, "error: not a valid identifier " . $v);
                                return -1;
                            }
                            $k = $v;
                            $v = "";
                            $m = 4;
                        } else {
                            igk_set_env('error://' . __FUNCTION__, "error: ':' detected not on good expression: mode:" . $m . " pos:" . $pos);
                            return -1;
                        }
                        break;
                    case '}':
                        $v = $_json_value($v);
                        if ($m == 4) {
                            if (!isset($q->$k) || !is_object($q->$k)) {
                                $q->$k = $v;
                            }
                            $m = 2;
                        } else {
                            if ($m == 2) {
                                return $q;
                            }
                            return -1;
                        }
                        $k = "";
                        $v = "";
                        break;
                    case ']':
                        if ($m == 6) {
                            if (!empty($v)) {
                                $q[] = is_string($v) ? igk_str_strip_surround($v) : $v;
                            }
                            $v = "";
                            if (($_ct = count($tab)) > 0) {
                                $tab[$_ct -
                                    1]["v"] = $q;
                            } else {
                                $v = $q;
                            }
                        } else {
                            igk_set_env('error://' . __FUNCTION__, "error: not valid char detected ]");
                            return -1;
                        }
                        break;
                    case '"':
                    case "'":
                        $t = trim(igk_str_read_brank($exp, $pos, $ch, $ch, null, 1));
                        if ($m == 2) {
                            $v = '';
                            $k = $t;
                            if (($id = substr($k, 1, strlen($k) - 2)) && preg_match($valid_identifier, $id)) {
                                $k = $id;
                                $t = $id;
                                unset($id);
                            } else {
                                $id = str_replace('\"', "", $id);
                                $t = $id;
                                $k = $id;
                            }
                            $q->$t = null;
                            $m = 3;
                        } else if ($m == 4) {
                            $v .= stripslashes(str_replace('\"', "", substr($t, 1, strlen($t) - 2)));
                            $q->$k = $v;
                        } else if ($m == 6) {
                            $v .= $t;
                        } else {
                            igk_set_env('error://' . __FUNCTION__, "error: string not managed " . $pos);
                            return -1;
                        }
                        break;
                    default:
                        if ($m != 4)
                            $v .= $ch;
                        else if (!empty(trim($ch))) {
                            $s = $ch;
                            $pos++;
                            $dec = 0;
                            while ($pos < $ln) {
                                $ch = $exp[$pos];
                                if (($ch == ".")) {
                                    if ($dec)
                                        return -1;
                                    $dec = 1;
                                } else {
                                    $nch = ord($ch);
                                    if (($nch < 48) || ($nch > 57)) {
                                        $pos--;
                                        break;
                                    }
                                }
                                $pos++;
                                $s .= $ch;
                            }
                            if ($dec) {
                                $v = floatval($s);
                            } else
                                $v .= $s;
                        }
                        break;
                }
                $pos++;
            }
            if ($m == 6) {
                if (!empty($v)) {
                    $q[] = trim($v);
                    $v = "";
                    $k = "";
                }
                unset($q);
            }
        }
        if (($m != 0) && !empty($v)) {
            igk_set_env('error://' . __FUNCTION__, "some data remain : " . $v);
            return -1;
        }
        return $o;
    }
}
