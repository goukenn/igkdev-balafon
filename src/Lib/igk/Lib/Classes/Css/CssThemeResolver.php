<?php

namespace IGK\Css;

use IGK\Resources\R;
use IGK\System\Html\SVG\SvgRenderer;
use IGKResourceUriResolver;

class CssThemeResolver
{

    /**
     * main theme
     * @var mixed
     */
    var $theme;

    /**
     * parent theme
     * @var mixed
     */
    var $parent;

    var $last;

    var $designmode = false;

    var $resolv = [];

    var $count = 0;

    var $start = false;

    var $colordef = null;

    /**
     * treat value
     * @param string $value 
     * @return string 
     */
    public function treat(string $value)
    {
        // check not expression 
        if ((strpos($value, "{") === false) &&
            (strpos($value, "(") === false) &&
            (strpos($value, "[") === false)
        )
            return $value;
        // if already resolved - return the resolved value
        if (isset($this->resolv[$value])) {
            return $this->resolv[$value];
        }

        if (!$this->start) {
            // initialize 
            $this->start = igk_sys_request_time();
            $this->cldef = &igk_css_get_treat_colors($this->parent ? $this->parent->getDef()->getCl() : []);
        } else {
            $duration = (($mt = igk_sys_request_time()) - $this->start);
        }

        $reg = IGK_CSS_TREAT_REGEX;
        $reg3 = IGK_CSS_CHILD_EXPRESSION_REGEX;
        $match = array();
        $gtheme = $theme = $this->theme;
        $systheme = $this->parent;
        $this->last = $value;

        $v = $v_def = $value;

        while (($c = preg_match_all($reg3, $v, $match))) {
            for ($i = 0; $i < $c; $i++) {
                $n = $match[0][$i];
                $name = $match["name"][$i];
                $type = $match["type"][$i];
                $deftheme = $match["def"][$i];
                $rv = IGK_STR_EMPTY;
                if (empty($deftheme)) {
                    $deftheme = "def";
                }
                if (empty($type)) {
                    $rv = $gtheme->$deftheme[$name];
                } else {
                    if (isset($v_resolv_names[$name])) {
                        igk_ilog(["css loop - detection", $v_resolv_names, $name], __FUNCTION__);
                        break;
                    }
                    switch ($type) {
                        case "sys":
                            $v_resolv_names[$name] = 1;
                            $rv = $systheme->$deftheme[$name];
                            break;
                        case "th":
                            $v_resolv_names[$name] = 1;
                            $rv = $theme->$deftheme[$name];
                            break;
                        default:
                            igk_ilog("css type not define: " . $name . " on " . $type . " " . $deftheme, __FUNCTION__);
                            break;
                    }
                }
                $v = str_replace($n, $rv, $v);
            }
        }
        $vresolv = 1;
        $v = trim($v);
        $vsrc = $v;
        while ($vresolv) {
            $vresolv = 0;
            $qlist = [];
            $qlist[] = $v;
            $roots = [];
            while ($g = array_pop($qlist)) {
                $rtv = null;
                $pos = 0;
                if (is_array($g)) {
                    $rtv = $g["parent"];
                    $g = $g["value"];
                }
                while (($pos = strpos($g, "[", $pos)) !== false) {
                    $tv = igk_str_read_brank($g, $pos, "]", "[");
                    if (empty($tv))
                        continue;
                    if ($rtv == null) {
                        $roots[$tv] = $tv;
                    }
                    if (($tl = strpos($tv, "[", 1)) !== false) {
                        $q = array("parent" => $tv, "value" => substr($tv, $tl));
                        array_push($qlist, $q);
                    } else {
                        // $c = new self();
                        // $c->parent = $this->parent;
                        // $c->theme = $this->theme;
                        // $c->resolv = & $this->resolv;
                        $sv = $this->treat_value($tv, false); 
                        // igk_wln_e($tv);

                        if (($rtv == null) || !isset($roots[$rtv]))
                            $roots[$tv] = $sv;
                        else {
                            $nvalue = str_replace($tv, $sv, $roots[$rtv]);
                            $roots[$rtv] = $nvalue;
                            if (($tl = strpos($nvalue, "[", 0)) !== false) {
                                $q = array("parent" => $rtv, "value" => $nvalue);
                                array_push($qlist, $q);
                            }
                            unset($roots[$tv]);
                        }
                    }
                }
            }
            foreach ($roots as $k => $tv) {
                if ($k == $tv) {
                    $tv = "";
                }
                if (strpos($v, $k) === false) {
                    throw new \IGK\System\Exceptions\CssParserException("{$k} not found in {$v}");
                } else
                    $v = str_replace($k, $tv, $v);
            }
            $v = igk_css_treat_bracket($v, $theme, $systheme);
            $g = 0;
            if (!empty($v) && ($v != $vsrc) && (strpos($v, "[") !== false)) {
                $vresolv = 1;
                $vsrc = $v;
            }
        }
        $this->resolv[$v_def] = $v;
        $this->count--;


        return $v;
    }
    /**
     * treat value
     */
    public function treat_value(string $v, $themeexport = true)
    {
        $reg = IGK_CSS_TREAT_REGEX;
        $pos = 0;
        $tab = [];
        $theme = $this->theme;
        $systheme = $this->parent;
        while (($pos = strpos($v, "{", $pos)) !== false) {
            $ob = igk_str_read_brank($v, $pos, "}", "{");
            $tab[$ob] = igk_css_treat_bracket($ob, $theme, $systheme);
        }
        foreach ($tab as $k => $vi) {
            $v = str_replace($k, $vi, $v);
        }
        if (!preg_match($reg, $v, $match))
            return "";
        $type = $match["name"];
        $value = $match["value"];
        $stop = "";
        if (isset($match["stop"])) {
            $stop = $match["stop"];
        }
        $v = $this->_treat_entries($v, $type, $value, "", $stop, $themeexport);
        return $v;
    }
    /**
     * reset theme treatment
     * @return void 
     */
    public function reset()
    {
        $this->count = 0;
        $this->resolv = [];
        $this->start = null;
    }

    private function _treat_entries(string & $v, $type, $value, $a = "", $stop = "", $themeexport = 0)
    {
        $themeexport = 0;
        $theme = $this->theme;
        $systheme = $this->parent;
        $gtheme = $theme;
        $v_m = $v;
        if (!is_object($systheme)) {
            igk_trace();
            igk_wln_e("parent theme not an object");
        }
        $d = &$systheme->getDef()->getCl();
        $gcl = ($d) ? $d : array();
        $stop = trim($stop);

        $v_designmode =  $this->designmode;
        $chainColors = array();
        if (($theme != null) && ($gtheme !== $theme) && ($theme !== $systheme)) {
            $colors = $theme->getCl();
            if ($colors) {
                $chainColors[] = $colors;
            }
        }
        if ($colors = $gtheme->getCl()) {
            $chainColors[] = $colors;
        }
        if ($gcl) {
            $chainColors[] = array_merge($gcl, []);
        }
        $chainColorCallback = function ($value) use (&$chainColors, $v_designmode, $gtheme, $systheme, $theme) {
            $tab = explode(",", $value);
            $v = trim($tab[0]);
            $def = count($tab) > 1 ? implode(",", array_slice($tab, 1)) : 'transparent';
            if (!($s = igk_css_treatcolor($chainColors, $v)) || ($v == $s)) {
                $s = igk_css_design_color_value($v, null, $v_designmode);
            }
            if ((empty($s) || ($s == $v)) && (igk_count($tab) > 1)) {
                $s = trim($def);
            }
            return $s;
        };
        switch (strtolower($type)) {
            case "resolv":
                $v = str_replace($v_m, igk_css_get_resolv_stylei($value) ?? "", $v);
                break;
            case "varp":
                if (!igk_css_var_support()) {
                    $tab = explode(":", $value);
                    $prop = $tab[0];
                    $name = implode(":", array_slice($tab, 1));
                    igk_set_env_keys("sys://css/vars", $prop, $name);
                    $v_r = "";
                } else {
                    $v_r = $value . ";";
                }
                $v = str_replace($v_m, $v_r, $v);
                break;
            case "varf":
                if (!igk_css_var_support()) {
                    $v = str_replace($v_m, $value, $v);
                } else
                    $v = str_replace($v_m, "", $v);
                break;
            case "var":
                if (igk_css_var_support()) {
                    $v_r = "var(" . $value . ")" . $a;
                } else {
                    $tab = array_slice(explode(",", $value), 1);
                    if (igk_count($tab) > 0) {
                        $v_r = trim(implode(",", $tab));
                    } else {
                        $v_r = ($t = igk_get_env("sys://css/vars")) ? igk_getv($t, $value) : null;
                    }
                    if (!empty($v_r))
                        $v_r .= $a;
                }
                $v = str_replace($v_m, $v_r, $v);
                break;
            case "fit":
                if (preg_match("/^(fill|contain|cover|none|scale-down)/i", $value)) {
                    $v = str_replace($v_m, "-webkit-object-fit: {$value};-ms-object-fit:{$value}; -o-object-fit: {$value}; object-fit: {$value};", $v);
                } else {
                    $v = str_replace($v_m, "", $v);
                }
                break;
            case "trans":
                $v = str_replace($v_m, "-webkit-transition: {$value};-ms-transition:{$value}; -moz-transition:{$value}; -o-transition: {$value}; transition: {$value};", $v);
                break;
            case "lingrad":
                $v_stand = $value;
                if (preg_match("/^(left|top|right|bottom)/i", trim($v_stand))) {
                    $v_stand = "to " . $v_stand;
                }
                $v = str_replace($v_m, "background: -webkit-linear-gradient({$value}); background:-ms-linear-gradient({$value}); background:-moz-linear-gradient({$value});background:-o-linear-gradient({$value}); background:linear-gradient({$v_stand});", $v);
                break;
            case "trans-prop":
                $v = str_replace($v_m, "-webkit-transition-property: {$value};-ms-transition-property:{$value}; -moz-transition-property:{$value}; -o-transition-property: {$value}; transition-property: {$value};", $v);
                break;
            case "transform":
                $v = str_replace($v_m, "-webkit-transform: {$value};-ms-transform:{$value}; -moz-transform:{$value}; -o-transform: {$value}; transform: {$value};", $v);
      
                break;
            case "transform-o":
                $v = str_replace($v_m, "-webkit-transform-origin: {$value};-ms-transform-origin:{$value}; -moz-transform-origin:{$value}; -o-transform-origin: {$value}; transform-origin: {$value};", $v);
                break;
            case "anim":
            case "animation":
                $v = str_replace($v_m, "-webkit-animation: {$value};-ms-animation:{$value}; -moz-animation:{$value}; -o-animation: {$value}; animation: {$value};", $v);
                break;
            case "filter":
                // "-moz-filter not available
                // $v = str_replace($v_m, "-webkit-filter: {$value};-ms-filter:{$value}; -moz-filter:{$value}; -o-filter: {$value}; filter: {$value};", $v);
                $v = str_replace($v_m, "-webkit-filter: {$value};-ms-filter:{$value}; -o-filter: {$value}; filter: {$value};", $v);
                break;
            case "res":
                if (is_file($value)) {
                    $v = str_replace($v_m, "background-image: url('" . igk_io_baseuri($value) . "')" . $stop, $v);
                } else {
                    $vimg = R::GetImgResUri($value);
                    $v = str_replace($v_m, (!empty($vimg) && !$themeexport ? "background-image: url('" . $vimg . "'){$stop}" : ""), $v);
                }
                break;
            case "bgres":
                $v = str_replace($v_m, (!$themeexport ? "background-image: url('" . igk_io_baseuri() . "/" . igk_html_uri($value) . "');" : ""), $v);
                break;
            case "uri":
                $v = str_replace($v_m, (!$themeexport ? "url('" . igk_io_baseuri() . "/" . igk_html_uri($value) . "')" : ""), $v);
                break;
            case "sysbgcl":
                $tv = explode(",", $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);
                $b = ($ncl != $value) || (($ncl == $value) && igk_css_is_webknowncolor($ncl)) ? igk_css_get_bgcl($ncl, $systheme, null) : "";
                $v = str_replace($v_m, $b, $v);
                break;
            case "sysfcl":
                $tv = explode(",", $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);
                if ($b = $this->_detect_color($tv, $cl, $ncl)){
                    $b = igk_css_get_fcl($b);
                }
                $v = str_replace($v_m, $b, $v);
                break;
            case "sysbcl":
                $tv = explode(",", $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);

                $b = ($ncl != $value) || (($ncl == $value) && igk_css_is_webknowncolor($ncl)) ? igk_css_get_bordercl($ncl) : "";
                $v = str_replace($v_m, $b, $v);
                break;
            case "syscl":
                $tv = explode(",", $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);
                // dectect that new design color is and bind default value
                if (($ncl == $cl) && !igk_css_is_webknowncolor($ncl)) {
                    $cl = &$systheme->def->getCl();
                    if ($defcl = igk_getv($tv, 1)) {
                        $ncl = trim($defcl);
                    } else {
                        if (igk_sys_env_production()) {
                            $ncl = 'initial';
                        } else {
                            $ncl = "var(--{$ncl})";
                        }
                    }                   
                    $cl[$ncl] = $ncl;
                }
                $v = str_replace($v_m, $ncl . $a, $v);
                break;
            case "fcl":
                $v = str_replace($v_m, igk_css_get_fcl($chainColorCallback($value)), $v);
                break;
            case "bgcl":
                $ncl = $chainColorCallback($value);
                $v = str_replace($v_m, igk_css_get_bgcl($ncl, $gtheme, $systheme), $v);
                break;
            case "bcl":
                $ncl = $chainColorCallback($value);
                $v = str_replace($v_m, igk_css_get_bordercl($ncl), $v);
                break;
            case "cl":
                $rp = igk_str_rm_last($v_m, ';');
                $nv = $chainColorCallback($value);
                $t = $nv;
                $v = str_replace($rp, $t, $v);
                break;
            case "ft":
                $v = str_replace($v_m, ($theme !== $gtheme) && $theme->ft[$value] ? igk_css_get_font($value) : null, $v);
                break;
            case "ftn":
                $h = $theme->ft[$value] ? $theme->ft[$value] : null;
                if ($h)
                    $v = str_replace($v_m, "\"" . $h . "\"", $v);
                else
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                break;
            case "pr":
            case "prop":
                $p = &$systheme->getProperties();
                $v_r = igk_css_design_property_value($value, $theme->properties, $v_designmode);
                if (!empty($v_r))
                    $v_r .= $stop;
                $v = str_replace($v_m, $v_r, $v);
                break;
            case "palcl":
                $r = igk_get_palette();
                $v = str_replace($v_m, $r ? igk_getv($r, $value) : null, $v);
                break;
            case "palbgcl":
                $r = igk_get_palette();
                if ($r) {
                    $s = $r[$value];
                    if (!empty($s))
                        $v = str_replace($v_m, "background-color: " . $s . ";", $v);
                    else
                        $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                } else {
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                }
                break;
            case "palfcl":
                $r = igk_get_palette();
                $s = igk_getv($r, $value);
                if (!empty($s))
                    $v = str_replace($v_m, "color: " . $s . ";", $v);
                else
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                break;

            // TODO: Add SVG import support
            case "svg":
                    $n = $value;
                    $s = "";
                    if ($p = SvgRenderer::GetPath($n)){
                        $uri = IGKResourceUriResolver::getInstance()->resolve($p);                         
                        $s= "url(".$uri.")";
                    }else{
                    }
                    $v = str_replace($v_m, $s, $v); 
                break; 
            default:
                if ((strlen($type) > 0) && ($type[0] == "-")) {
                    $type = substr($type, 1);
                    $v = str_replace($v_m, "-webkit-{$type}: {$value};-ms-{$type}:{$value}; -moz-{$type}:{$value}; -o-{$type}: {$value}; {$type}: {$value};", $v);
                } else
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                break;
        }
        return $v;
    }
    /**
     * 
     * @param mixed $array 
     * @param IGK\Css\definition #Parameter#d5a734a8 
     * @param IGK\Css\newColor #Parameter#d4a73315 
     * @return string 
     */
    private function _detect_color(array $tv, $cl, $ncl){
        $systheme = $this->parent;
        
        if (($ncl == $cl) && !igk_css_is_webknowncolor($ncl)) {
            if ($defcl = igk_getv($tv, 1)) {
                $ncl = trim($defcl);
            } else {
                if (igk_sys_env_production()) {
                    $ncl = 'initial';
                }
                return "";
            }
            $cl = & $systheme->def->getCl();
            $cl[$ncl] = $ncl;
            return $ncl;
        }
        return trim($ncl);
    }
}
