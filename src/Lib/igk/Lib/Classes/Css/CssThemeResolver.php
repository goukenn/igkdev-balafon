<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssThemeResolver.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Css;

use IGK\Resources\R;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\SVG\SvgRenderer;
use IGKException;
use IGKResourceUriResolver;

class CssThemeResolver
{
    /**
     * 
     * @var ?bool
     */
    var $themeResolved;
    /**
     * current theme
     * @var mixed
     */
    var $theme;

    /**
     * parent theme
     * @var ?HtmlDocTheme
     */
    var $parent;

    var $last;

    var $designmode = false;

    var $resolv = [];


    var $start = false;

    var $colordef = null;

    /**
     * resource resolver
     * @var ?ICssResourceResolver
     */
    var $resolver;

    const ATTR_RESOLV = 'resolv';
    const ATTR_TRANS = 'trans';
    const ATTR_TRANSFORM = 'transform';
    const ATTR_ANIM = 'anim';
    const ATTR_ANIMATION = 'animation';
    const ATTR_COLOR= 'cl';
    const ATTR_VAR= 'var';
    const ATTR_BACKGROUND_COLOR= 'bgcl';
    const ATTR_FOREGROUND_COLOR= 'fcl';
    const ATTR_FIT = 'fit';
    const ATTR_VAR_PROPERTY = 'varp';
    const ATTR_FONT = 'ft';
    const ATTR_FONT_NAME = 'ftn';
    const ATTR_RESOURCE = 'res';
    const ATTR_BACKGROUND_RESOURCE= 'bgres';
    const ATTR_URI = 'uri';
    const ATTR_BORDER_COLOR = 'bcl';
    const ATTR_SVG = 'svg';
    const ATTR_FILTER = 'filter';
    const ATTR_PROP = 'prop';
    const ATTR_PROPERTY = 'pr';
    const ATTR_SYS_BGCL = 'sysbgcl';
    const ATTR_SYS_FCL = 'sysfcl';
    const ATTR_SYS_COLOR = 'syscl';
    const ATTR_SYS_BCL = 'sysbcl';

    const ATTR_G_RESOLV_MODE = 'sys';

    /**
     * treat theme value
     * @param string $value 
     * @param mixed $theme_export 
     * @return null|string 
     * @throws IGKException 
     * @throws CssParserException 
     */
    public function treatThemeValue(string $value, $theme_export){
        if (!empty($v = $this->treat($value, $theme_export))){
            return $this->treatInlineValue($v);
        }
        return $v;

    }
    /**
     * treat parser for inline value
     * @param string $value 
     * @return null|string 
     * @throws IGKException 
     */
    public function treatInlineValue(string $value){
        $v = $value;
        if (!empty($v) && ($gp = CssParser::Parse($v))){
            $value = $gp->render();
        }
        return $value;
    }
    /**
     * treat value
     * @param string $value 
     * @return string 
     */
    public function treat(string $value, bool $themeexport = false)
    {
        // + | check not expression 
        // + | contains litteral exemple: {sys:fitw},
        // + | contains link expression : (sys|th: ) 
        // + | contains data:  [cl:red]
        if ((strpos($value, "{") === false) &&
            (strpos($value, "(") === false) &&
            (strpos($value, "[") === false)
        ){ 
            return $value;
        }
        // if already resolved - return the resolved value
        if (isset($this->resolv[$value])) {
            return $this->resolv[$value];
        }

        if (!$this->start) {
            // initialize 
            $this->start = igk_sys_request_time();
            $this->colordef = &igk_css_get_treat_colors($this->parent ? $this->parent->getDef()->getCl() : []);
        }  
        $reg3 = IGK_CSS_CHILD_EXPRESSION_REGEX;
        $match = array();
        $gtheme = $theme = $this->theme;
        $systheme = $this->parent;
        $this->last = $value;
        $v = $v_def = $value;
        $v_resolv_names = [];
        // + | --------------------------------------------------------------------
        // + | resolve link expression 
        // + |

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
                        igk_ilog(["css loop - detection - ", $name], __FUNCTION__);
                        break;
                    }
                    switch ($type) {
                        case self::ATTR_G_RESOLV_MODE:
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
                $v = str_replace($n, $rv ?? "", $v);
            }
        }
        $vresolv = 1;
        $v = trim($v);
        $vsrc = $v;
        while ($vresolv && !empty($v)) {
            $vresolv = 0;
            $qlist = [$v];
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
                    if (empty($tv) || isset($roots[$tv]))
                        continue;
                    // check for next separator
                    $pos++;
                    if ($rtv == null) {
                        $roots[$tv] = $tv;
                    }
                    $stop = '';
                    if ($this->_nextSplitter($g, $pos)){
                        $stop = ';';
                    }
                    

                    if (($tl = strpos($tv, "[", 1)) !== false) {
                        $q = array("parent" => $tv, "value" => substr($tv, $tl));
                        array_push($qlist, $q);
                    } else {
                        // $c = new self();
                        // $c->parent = $this->parent;
                        // $c->theme = $this->theme;
                        // $c->resolv = & $this->resolv;
                        $sv = $this->treat_value($tv.$stop, $themeexport);
                        if ($stop && $v){
                            $sv = rtrim($sv,$stop);
                            if (empty($sv)){
                                $v = str_replace($tv.$stop,'', $v);
                                unset($roots[$tv]);
                                continue;
                            }
                        } 
                        

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
                        // if (empty($m = trim(str_replace($tv, $sv, $g), '; '))){
                        //     // $v = str_replace($g, $m, $v);
                        // }
                    }
                }
            }
            if (!empty($v)){
                foreach ($roots as $k => $tv) {
                    if ($k == $tv) {
                        $tv = "";
                    }
                    if (strpos($v, $k) === false) {
                        throw new \IGK\System\Exceptions\CssParserException("{$k} not found in {$v}");
                    } else{
                        $v = str_replace($k, $tv, $v);
                        if (empty(trim($v, "; "))){
                            $v = "";
                        }
                    }
                }
            }
            if (!empty($v)){
                $v = igk_css_treat_bracket($v, $theme, $systheme);
            }
            $g = 0;
            if (!empty($v) && ($v != $vsrc) && (strpos($v, "[") !== false)) {
                $vresolv = 1;
                $vsrc = $v;
            }
        }
     
        $this->resolv[$v_def] = $v;
        return $v;
    }
    private function _nextSplitter(string $v, & $pos){
        $ln = strlen($v);
        $tpos = $pos;
        while($tpos  < $ln ){
            $ch = $v[$tpos];
            if ($ch==";"){
                $pos = $tpos+1;
                return true;
            }
            $tpos++;
            if ($ch == ' '){
                continue;
            }
            break; 
        }
        return false; 
    }
    /**
     * treat css  value
     */
    public function treat_value(string $v, bool $themeexport)
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
        $this->resolv = [];
        $this->start = null;
    }

    private function _treat_entries(string & $v, $type, $value, $a = "", $stop = "", bool $themeexport = false)
    { 
        $theme = $this->theme;
        $systheme = $this->parent;
        $gtheme = $theme;
        $v_m = $v;
        $d = null;
        if (!is_object($systheme)) {            
            // igk_dev_wln_e(__FILE__.":".__LINE__,  "parent theme not an object");
        } else {
            $d = &$systheme->getDef()->getCl();
        }
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
        $chainColorCallback = 
        //function ($value) use (&$chainColors, $v_designmode, $gtheme, $systheme, $theme) {
        function ($value) use (&$chainColors, $v_designmode) { 
            $resolved = & $this->themeResolved;
            // detect color function or var prop
            if (preg_match("/\s*(?P<name>(rgb(a)|var|hsl))\s*\(/i", $value,$data)){
                if ($data["name"]=="var"){
                    $p = explode(',', rtrim(substr($value, strpos($value,"(") +1), ')'));
                    $root = & $this->theme->getRootReference();
                    $root[trim($p[0])] = trim(igk_getv($p, 1, "transparent"));
                    
                }
                return $value;
            }

            $tab = explode(',', $value);
            $v = trim($tab[0]);
            if ($this->resolver && ($s = $this->resolver->resolveColor($v))){
                $resolved = true;
                return $s;
            }

         

            $def = count($tab) > 1 ? implode(",", array_slice($tab, 1)) : 'transparent';
            if (!($s = igk_css_treatcolor($chainColors, $v)) || ($v == $s)) {
                if (defined('IGK_TEST_INIT')){
                    // + | in case of testing just return the requested color vlaue 
                    return trim($v);
                }
                $s = igk_css_design_color_value($v, null, $v_designmode);
            }
            if ((empty($s) || ($s == $v)) && (igk_count($tab) > 1)) {
                $s = trim($def);
            } else {
                $resolved = true;
            }
            return $s;
        };
        switch (strtolower($type)) {
            case self::ATTR_RESOLV:
                $v = str_replace($v_m, igk_css_get_resolv_stylei($value) ?? "", $v);
                break;
            case self::ATTR_VAR_PROPERTY:
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
            case self::ATTR_VAR:
                if (igk_css_var_support()) {
                    $v_r = "var(" . $value . ")" . $a;
                } else {
                    $tab = array_slice(explode(',', $value), 1);
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
            case self::ATTR_FIT :
                if (preg_match("/^(fill|contain|cover|none|scale-down)/i", $value)) {
                    $v = str_replace($v_m, "-webkit-object-fit: {$value};-ms-object-fit:{$value}; -o-object-fit: {$value}; object-fit: {$value};", $v);
                } else {
                    $v = str_replace($v_m, "", $v);
                }
                break;
            case self::ATTR_TRANS:
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
            case self::ATTR_TRANSFORM:
                $v = str_replace($v_m, "-webkit-transform: {$value};-ms-transform:{$value}; -moz-transform:{$value}; -o-transform: {$value}; transform: {$value};", $v);
                break;
            case "transform-o":
                $v = str_replace($v_m, "-webkit-transform-origin: {$value};-ms-transform-origin:{$value}; -moz-transform-origin:{$value}; -o-transform-origin: {$value}; transform-origin: {$value};", $v);
                break;
            case self::ATTR_ANIM:
            case self::ATTR_ANIMATION: 
                $v = str_replace($v_m, "-webkit-animation: {$value};-ms-animation:{$value}; -moz-animation:{$value}; -o-animation: {$value}; animation: {$value};", $v);
                break;
            case self::ATTR_FILTER:
                // "-moz-filter not available
                // $v = str_replace($v_m, "-webkit-filter: {$value};-ms-filter:{$value}; -moz-filter:{$value}; -o-filter: {$value}; filter: {$value};", $v);
                $v = str_replace($v_m, "-webkit-filter: {$value};-ms-filter:{$value}; -o-filter: {$value}; filter: {$value};", $v);
                break;
            case self::ATTR_RESOURCE: 
                if ($themeexport){
                   //  $r = ($tf = $this->_resolve_res($value)) ? "background-image: url('" . $tf. "')" . $stop : null;
                    $v = str_replace($v_m, 
                        ($tf = $this->_resolve_res($value)) ? "background-image: url('" . $tf. "')" . $stop : "", 
                        $v);

                }else{
                    if (is_file($value)) {
                        $v = str_replace($v_m, "background-image: url('" . igk_io_baseuri($value) . "')" . $stop, $v);
                    } else {
                        $vimg = R::GetImgResUri($value);
                        $v = str_replace($v_m, (!empty($vimg) && !$themeexport ? "background-image: url('" . $vimg . "'){$stop}" : ""), $v);
                    }
                } 
                break;
            case self::ATTR_BACKGROUND_RESOURCE:  
                $v = str_replace($v_m, (!$themeexport ? "background-image: url('" . igk_io_baseuri() . "/" . igk_uri($value) . "');" : ""), $v);
                break;
            case self::ATTR_URI: 
                $v = str_replace($v_m, (!$themeexport ? "url('" . igk_io_baseuri() . "/" . igk_uri($value) . "')" : ""), $v);
                break;
            case self::ATTR_SYS_BGCL: 
                $tv = explode(',', $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);
                $b = (($ncl != $value) || (($ncl == $value) && igk_css_is_webknowncolor($ncl)) ? $this->_get_bgcl($ncl, $themeexport): null) ?? "";
                $v = str_replace($v_m, $b, $v);
                break;
            case self::ATTR_SYS_FCL:
                $tv = explode(',', $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);
                if ($b = $this->_detect_color($tv, $cl, $ncl)){
                    $b = $this->_get_fcl($b);
                }
                $v = str_replace($v_m, $b, $v);
                break;
            case self::ATTR_SYS_BCL : 
                $tv = explode(',', $value);
                $cl = trim($tv[0]);
                $ncl = igk_css_design_color_value($cl, $gcl, $v_designmode);

                $b = ($ncl != $value) || (($ncl == $value) && igk_css_is_webknowncolor($ncl)) ? igk_css_get_bordercl($ncl) : "";
                $v = str_replace($v_m, $b, $v);
                break;
            case self::ATTR_SYS_COLOR: 
                $tv = explode(',', $value);
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
            case self::ATTR_FOREGROUND_COLOR:
                $resolved = false;
                $v = str_replace($v_m, $this->_get_fcl($chainColorCallback($value, $resolved)), $v);
                break;
            case self::ATTR_BACKGROUND_COLOR:
                $ncl = $chainColorCallback($value);
                $v = str_replace($v_m, $this->_get_bgcl($ncl, $themeexport), $v);
                break;
            case self::ATTR_BORDER_COLOR: 
                $ncl = $chainColorCallback($value);
                $v = str_replace($v_m, igk_css_get_bordercl($ncl), $v);
                break;
            case self::ATTR_COLOR:
                $rp = igk_str_rm_last($v_m, ';');
                $nv = $chainColorCallback($value);
                $t = $nv;
                $v = str_replace($rp, $t, $v);
                break;
            case self::ATTR_FONT:
                $v = str_replace($v_m, ($theme !== $gtheme) && $theme->ft[$value] ? igk_css_get_font($value) : null, $v);
                break;
            case self::ATTR_FONT_NAME:
                $h = $theme->ft[$value] ? $theme->ft[$value] : null;
                if ($h)
                    $v = str_replace($v_m, "\"" . $h . "\"", $v);
                else
                    $v = str_replace($v_m, IGK_STR_EMPTY, $v);
                break;
            case self::ATTR_PROPERTY:
            case self::ATTR_PROP: 
                // $g = $theme->getProperties();
                // $p = & $systheme->getProperties();
                $v_r = igk_css_design_property_value($value, $theme->getProperties(), $v_designmode);
                if (!empty($v_r))
                    $v_r .= $stop;
                $v = str_replace($v_m, $v_r ?? "", $v); 
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
            case self::ATTR_SVG:
                    $n = $value;
                    $s = "";
                    if ($p = SvgRenderer::GetPath($n)){
                        $uri = IGKResourceUriResolver::getInstance()->resolve($p);                         
                        $s= "url(".$uri.")";
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
     * resolvee resource in order to get system resource path 
     * @param string $value 
     * @return null|string 
     * @throws IGKException 
     */
    private function _resolve_res(string $value):?string {
        $tf = null;
        if ($this->resolver){
            if (is_file($value)){
                $tf = $value;                     
            }
            else{
                if ($tf = R::GetImgResUri($value, $path)){
                    $tf = $path;
                }
            }
            $tf = $this->resolver->resolve($tf);   
        } else {
            if ($tf = R::GetImgResUri($value, $path)){
                $tf = "/".igk_str_rm_start($tf, "../");
            }
        }
        return $tf;
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

    protected function _get_bgcl($ncl, bool $themeexport){
        // igk_css_get_bgcl($ncl, $gtheme, $systheme),
        return igk_css_get_bgcl($ncl, $themeexport, $this->theme, $this->parent);
    }
    protected function _get_fcl($value, $resolved=false){   
      
        if ($resolved){
            return sprintf("color: %s;", $value);
        }
        return igk_css_get_fcl($value, $this->theme, $this->parent);
    }
}
