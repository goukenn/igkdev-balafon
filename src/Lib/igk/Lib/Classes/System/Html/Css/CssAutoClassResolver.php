<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssAutoClassResolver.php
// @date: 20260901 11:21:32
namespace IGK\System\Html\Css;

use IGK\System\Console\Logger;
/**
* auto generate doc.
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Html\Css
*/
class CssAutoClassResolver
{
    /**
     * auto detect prefix sequence 
     */
    const AUTO_PREFIX_DETECT = 'text|br|bg|pad|mar|rd|fit|no|gap|w|h|anim|transform|trans';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const UNIT_LENGTH = 'pt|cm|mm|in|px|pc|em|rem|vw|vh|vmin|vmax|ch|ex|%';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const TIME_LENGTH = 's|ms';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const ANGLE = 'deg|rad|turn|grad';
    /**
     * get or set the color spaces 
     * @var mixed
     */
    var $colorspace;
    /**
     * detect auto prefix element  
     * @param string $class 
     * @param mixed &$tab 
     * @return bool 
     */
    public function detect(string $class, &$tab): bool
    {
        $check_theme = function (&$tab, $theme, $screen) {
            if (isset($tab['def'])) {
                $def = $tab['def'];
                if (preg_match('/' . $theme . '/', $def)) {
                    $tab['theme'] = 1;
                }
                if (preg_match('/' . $screen . '/', $def)) {
                    $tab['screen'] = 1;
                }
                unset($tab['def']);
                $tab['separator'] = $tab[4];
            }
        };
        $theme = '(?:dark|light)';
        $screen = '(?:(?:x)?sm|(?:x(?:x)?)?lg)';
        $st = '(?P<def>(?:(?:' . $theme . '(-|:)?)?' . $screen . '|(?:' . $screen . '(-|:)?)?' . $theme . ')(-|:))?';
        $value = '(?P<value>[^:]+)(?P<pseudo>:.+)?';
        $regex = '/^' . $st . '(?P<prefix>' . self::AUTO_PREFIX_DETECT . ')\\b-' . $value . '$/';
        if (preg_match($regex, $class, $tab)) {

            $check_theme($tab, $theme, $screen);

            return true;
        } else if (preg_match('/^' . $st . '(?P<prefix>[a-zA-Z]+[a-z0-9A-Z]*)\\b-' . $value . '$/', $class, $tab)) {
            if (method_exists($this, '_visit_' . $tab['prefix'])) {
                $check_theme($tab, $theme, $screen);
                return true;
            }
        }
        // igk_wln_e(__FILE__.":".__LINE__ , preg_last_error_msg());
        return false;
    }
    /**
    * resolve data presentation
    * @param mixed $prefix
    * @param mixed $value
    * @param mixed $options
    * @return ?string
    */
    public function resolveCode($prefix, $value, $options = null): ?string
    {
        $this->colorspace = igk_getv($options, 'colorspace', 'hsl');
        if (method_exists($this, $fc = '_visit_' . $prefix)) {
            return call_user_func_array([$this, $fc], [$value]);
        } else {
            igk_environment()->isDev() && Logger::warn('missing visitor for ' . $value);
        }
        return null;
    }
    /**
    * auto generate doc.
    * @return void
    */
    public function getColorPal()
    {
        $type = $this->colorspace;
        $lg = json_decode(file_get_contents(IGK_LIB_DIR . '/Default/assets/css/colorspaces/' . $type . '.json'));
        return $lg ?? ['red' => '#ff0000'];
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return string|false
    */
    protected function _visit_text($value)
    {
        $rf = $this->getColorPal();
        $tv = igk_getv($rf, $value);
        return json_encode(['color' => $tv]);
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return void
    */
    protected function _visit_sel($value)
    {
        $rf = $this->getColorPal();
        $tv = igk_getv($rf, $value);
        return json_encode(['::selection', ['color' => $tv]]);
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return string|false
    */
    protected function _visit_bg(string $value)
    {
        $rf = $this->getColorPal();
        $tv = igk_getv($rf, $value);
        return json_encode(['background-color' => $tv]);
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return mixed
    */
    protected function _visit_no(string $value)
    {
        $g = igk_getv(['overflow' => '{overflow: hidden}'], $value);

        return $g;
    }
    /**
     * get unit regex
     * @return string 
     */
    protected function _unit_regex()
    {
        return '/(\d+(?:\.\d+)?)(' . self::UNIT_LENGTH . ')?/';
    }
    /**
    * auto generate doc.
    * @return string
    */
    protected function _unit_regex_with_negate()
    {
        return '/(?:m)?(\d+(?:\.\d+)?)(' . self::UNIT_LENGTH . ')?/';
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return string|false
    */
    protected function _visit_br(string $value)
    {
        $g = explode('-', $value);
        $r = [];
        $v_unit_regex = $this->_unit_regex();
        if (count($g) == 1) {
            // + | only size is allowed 
            if (preg_match($v_unit_regex, $g[0], $tab))
                $r['border-width'] = $g[0] . (empty($tab[2]) ? 'px' : '');
        } else {
            $v_style_regex = '/\\b(solid|dashed|double)\\b/';
            $style = [];
            $rf = $this->getColorPal();
            $style = ['width' => null, 'style' => null, 'color' => null];
            foreach ($g as $c) {
                if (preg_match($v_style_regex, $c)) {
                    $style['style'] = $c;
                    continue;
                }
                if (preg_match($v_unit_regex, $c, $tab)) {
                    $style['width'] = $c . (empty($tab[2]) ? 'px' : '');
                    continue;
                }
                if ($v = igk_getv($rf, $c)) {
                    $style['color'] = $v;
                }
            }

            $r['border'] = implode(' ', array_filter($style));
        }
        return json_encode($r);
    }
    /**
    * auto generate doc.
    * @param mixed $v
    * @param mixed $tab
    * @return string
    */
    protected function _unitValueFromRegexRegex($v, $tab)
    {
        return $v . (empty($tab[2]) ? 'px' : '');
    }

    /**
     * get size 
     * @param string $value 
     * @param bool $negate
     * @return array 
     */
    private function _parse_side_unit(string $value, bool $negate = false)
    {
        $g = explode('-', $value, 2);
        $r = array_fill_keys(['side', 'unit'], null);
        $v_unit_regex = $negate ? $this->_unit_regex_with_negate() :  $this->_unit_regex();
        if (count($g) == 1) {
            if (preg_match($v_unit_regex, $g[0], $tab)) {
                $sb = $negate && ($tab[0][0] == 'm') ? '-' : '';
                $r['unit'] = $sb . $this->_unitValueFromRegexRegex($tab[1], $tab);
            }
        } else {
            if (preg_match('/\\b(t|l|r|b)\\b/', $g[0], $tab)) {
                $r['side'] = $g[0];
            }
            if (preg_match($v_unit_regex, $g[1], $tab)) {
                $sb = $negate && ($tab[0] == 'm') ? '-' : '';
                $r['unit'] = $sb . $this->_unitValueFromRegexRegex($g[1], $tab);
            }
        }
        return array_values($r);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_mar(string $value)
    { // + | margin allow negate value 
        $side = null;
        $v = null;
        if (preg_match('/^((l|t|b|r)-)?(m)?\\d+(\.\\d+)?(' . self::UNIT_LENGTH . ')?$/', $value)) {
            list($side, $v) = $this->_parse_side_unit($value, true);
        } else {
            if (count($rc = explode('-', $value)) > 1) {
                $v = $this->_auto_positive_value($rc, true);
            }
        }
        return $this->_side_dim('margin', $v, $side);
    }
    /**
    * auto generate doc.
    * @param mixed $property
    * @param mixed $value
    * @param mixed $side
    * @return string|false
    */
    protected function _side_dim($property, $value, $side = null)
    {
        if (!$side) {
            return json_encode([$property => $value]);
        }
        $d = igk_getv(['t' => 'top', 'l' => 'left', 'r' => 'right', 'b' => 'bottom'], $side);
        return json_encode([$property . '-' . $d => $value]);
    }
    /**
    * auto generate doc.
    * @param mixed $property
    * @param mixed $value
    * @param mixed $side
    * @return string|false
    */
    protected function _corner_dim($property, $value, $side = null)
    {
        if (!$side) {
            return json_encode([sprintf($property, '') => $value]);
        }
        $d = igk_getv(['tl' => 'top-left', 'tr' => 'top-right', 'br' => 'bottom-right', 'bl' => 'bottom-left'], $side);
        return json_encode([sprintf($property, '-' . $d) => $value]);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_pad(string $value)
    {
        $side = null;
        $v = null;
        if (preg_match('/^((l|t|b|r)-)?\\d+(\.\\d+)?(' . self::UNIT_LENGTH . ')?$/', $value)) {
            list($side, $v) = $this->_parse_side_unit($value);
        } else {
            if (count($rc = explode('-', $value)) > 1) {
                $v = $this->_auto_positive_value($rc);
            }
        }



        return $this->_side_dim('padding', $v, $side);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_rd(string $value)
    {
        list($side, $v) = $this->_parse_side_unit($value);
        return $this->_corner_dim('border%s-radius', $v, $side);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_gap(string $value)
    {
        // column-gap
        // row-gap 
        $tv = explode('-', $value, 2);
        $v_unit_regex = $this->_unit_regex();
        $r = 'inherit';
        if (count($tv) == 1) {
            if (preg_match($v_unit_regex, $value, $tab)) {
                $r = $this->_unitValueFromRegexRegex($value, $tab);
            }
        } else {
            $r = [];
            while (count($tv) > 0) {
                $value = array_shift($tv);
                if (preg_match($v_unit_regex, $value, $tab)) {
                    $r[] = $this->_unitValueFromRegexRegex($value, $tab);
                } else {
                    igk_die("invalid data value");
                }
            }
            $r = implode(' ', $r);
        }
        return json_encode(['gap' => $r]);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_ls(string $value)
    {
        return $this->_set_single_length('letter-spacing', $value, true);

        // $v_unit_regex = $this->_unit_regex_with_negate($value);
        // if (preg_match($v_unit_regex, $value, $tab)) {
        //     $u = $this->_unitValueFromRegexRegex($tab[1], $tab);
        //     if ($value[0] == 'm') {
        //         $u = '-' . $u;
        //     }
        //     return json_encode(['letter-spacing' => $u]);
        // }
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_lh(string $value)
    {
        return $this->_set_single_length('line-height', $value);
    }
    /**
    * auto generate doc.
    * @param string $property
    * @param string $value
    * @param bool $negate
    * @return void
    */
    protected function _set_single_length(string $property, string $value, bool $negate = false)
    {
        $v_unit_regex = $negate ? $this->_unit_regex_with_negate() : $this->_unit_regex();
        if (preg_match($v_unit_regex, $value, $tab)) {
            $u = $this->_unitValueFromRegexRegex($tab[1], $tab);
            if ($negate) {
                $u = $this->_sign($tab[0]) . $u;
            }
            return json_encode([$property => $u]);
        }
    }
    /**
    * auto generate doc.
    * @param string $value
    * @param bool $negate
    * @return string
    */
    public function _get_unit_length(string $value, $negate = false)
    {
        $v_unit_regex = $negate ? $this->_unit_regex_with_negate() : $this->_unit_regex();
        $u = '';
        if (preg_match($v_unit_regex, $value, $tab)) {
            $u = $this->_unitValueFromRegexRegex($tab[1], $tab);
            if ($negate) {
                $u = $this->_sign($tab[0]) . $u;
            }
        }
        return $u;
    }
    /**
    * auto generate doc.
    * @param array $tab
    * @param mixed $negate
    * @return void
    */
    protected function _auto_positive_value(array $tab, $negate = false)
    {
        $v_unit_regex = $negate ? $this->_unit_regex_with_negate() :  $this->_unit_regex();
        $r = [];
        while (count($tab) > 0) {
            $q = array_shift($tab);
            if (preg_match($v_unit_regex, $q, $ctab)) {
                $sb = $negate ? $this->_sign($q[0]) : '';
                $r[] = $sb . $this->_unitValueFromRegexRegex($ctab[1], $ctab);
            }
        }
        return implode(' ', $r);
    }
    /**
     * check sign
     * @param string $m 
     * @return string 
     */
    private function _sign(string $m)
    {
        return $m[0] == 'm' ? '-' : '';
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    public function _visit_sz(string $value)
    {
        list($width, $height) = igk_extract(explode('-', $value, 2), '0|1');
        if (!$height)
            $height = $width;
        if ($width) {
            return json_encode([
                'width' => $this->_get_unit_length($width),
                'height' => $this->_get_unit_length($height)
            ]);
        }
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    public function _visit_w(string $value)
    {
        return $this->_mark_single_unit('width', $value, false);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_h(string $value)
    {
        return $this->_mark_single_unit('height', $value, false);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_fs(string $value)
    {
        return $this->_mark_single_unit('font-size', $value, false);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_z(string $value)
    {
        return $this->_mark_single('z-index', $value, false);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _visit_opacity(string $value)
    {
        if ($g = intval($value)){
            $value = round($g/100, 2);
            return $this->_mark_single('opacity', $value, false);
        }
    }
    /**
     * visit ransition 
     * @param string $value 
     * @return string|false 
     */
    protected function _visit_transition(string $value)
    {
        $value = igk_getv([
            'colors'=>'color, background-color, border-color, text-decoration-color, fill, stroke',
        ], $value, $value);
        return json_encode(['transition-property'=>$value]);
    }
    /**
    * auto generate doc.
    * @param mixed $property
    * @param mixed $value
    * @param bool $negate
    * @return string|false
    */
    protected function _mark_single(string $property, string $value, $negate = false)
    {
        return json_encode([
            $property => $this->_protect_expression($value) ?? intval($value)
        ]);
    }
    /**
    * auto generate doc.
    * @param mixed $property
    * @param mixed $value
    * @param bool $negate
    * @return string|false|null
    */
    protected function _mark_single_unit($property, $value, $negate = false)
    {
        return json_encode([
            $property => $this->_protect_expression($value) ?? $this->_get_unit_length($value, $negate)
        ]);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _protect_expression(string $value)
    {
        $vv = null;
        if (preg_match('/^\[.*\]$/', $value)) {
            $vv = substr($value, 1, -1);
        }
        return $vv;
    }
}
