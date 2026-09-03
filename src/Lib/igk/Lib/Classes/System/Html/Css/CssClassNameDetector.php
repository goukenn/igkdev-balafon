<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameDetector.php
// @date: 20240913 09:08:04
namespace IGK\System\Html\Css;

use IGK\Css\CssSupport;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssClassNameDetector
{
    /**
     * auto generate doc.
     * @var mixed
     */
    var $source;
    /**
     * contain list information 
     * @var array<ICssClassList>
     */
    var $list;

    /**
     * define the color space to use
     * @var mixed
     */
    var $colorspaces;

    /**
     * tag refrence list 
     * @var mixed
     */
    var $tags;
    /**
     * Constant: cl regex.
     * @var mixed
     */
    const CL_REGEX = "/(\*|\.\b[a-z][a-z0-9\-]*\b((?::(:)?\w+|(?:\[[a-z][a-z0-9\-]*\])))?)/i";
    /**
     * Constant: media key.
     * @var mixed
     */
    const MEDIA_KEY = '@media';

    /**
     * 
     * @var mixed
     */
    private $m_auto_prefixResolver;

    /**
     * Property: references.
     * @var mixed
     */
    private $m_references;
    /**
     * Map of mapped.
     * @var mixed
     */
    private $m_mapped;
    /**
     * Property: frames.
     * @var mixed
     */
    private $m_frames;
    /**
     * media flag in use
     * @var ?string
     */
    private $m_media;
    /**
     * .ctr
     */
    function __construct()
    {
        $this->list = [];
        $this->m_references = [];
        $this->m_frames = [];
        $this->m_media = null;
    }
    /**
     * retrieve frames 
     * @return array 
     */
    public function getFrames()
    {
        return $this->m_frames;
    }
    /**
     * auto generate doc.
     * @param string $d
     * @param string $k
     * @return mixed
     */
    private static function _join_css_tab(string $d, string $k)
    {
        if (trim($d))
            return $k . ":" . $d;
    }
    /**
     * trim right 
     * @param mixed $a 
     * @param mixed $sep 
     * @return array<string|int, string> 
     */
    private static function _TrimCssR($a, $sep)
    {
        return array_map(function ($a, $k) use ($sep) {
            return rtrim($k . ':' . $a, $sep);
        }, $a, array_keys($a));
    }
    /**
     * auto generate doc.
     * @param array $resolv_definition
     * @param mixed $option
     * @return string
     */
    public function renderToCss(array $resolv_definition, $option = null)
    {
        $option = $option ?? (object)[
            'lf' => "\n",
        ];
        $_tout = null;
        $option->frames = [];
        $option->medias = [];
        $detector = $this;
        $_out = [];
        if ($this->tags) {
            ksort($this->tags);
            $_out[] = implode($option->lf, array_map(function ($a, $key) {
                return $key . '{' . implode(';', self::_TrimCssR($a, ';')) . '}';
            }, $this->tags, array_keys($this->tags)));
        }
        ksort($resolv_definition);
        $_out[] = implode($option->lf, array_map(function ($d, $c) use ($option, $detector) {
            $lf = $option->lf;
            if ($c == self::MEDIA_KEY) {
                $tc = [];
                ksort($d);
                foreach ($d as $k => $v) {
                    $g = [];
                    foreach ($v as $tk => $tv) {
                        $g[] = self::_RenderList($tv, $tk, $option, $detector);
                    }
                    $k = CssUtils::TreatMediaCondition($k);
                    $tc[] = sprintf('@media %s{%s}', $k, implode($option->lf, $g));
                }
                $option->medias[] = implode($lf, $tc);
                return null;
            } else {
                return self::_RenderList($d, $c, $option, $detector);
            }
        }, $resolv_definition, array_keys($resolv_definition)));
        if ($option->frames) {
            foreach ($option->frames as $i) {
                $_tout[] = $i->getDefinition($option);
            }
            array_unshift($_out, implode($option->lf, $_tout));
        }
        if ($option->medias) {
            array_push($_out, implode($option->lf, $option->medias));
        }
        return implode($option->lf, $_out);
    }
    /**
     * auto generate doc.
     * @param mixed $d
     * @param mixed $c
     * @param mixed $option
     * @param static $detector
     * @return string
     */
    static function _RenderList($d, $c, $option, $detector)
    {
        $lf = $option->lf;
        if (is_object($d)) {
            $d = (array)$d;
        }
        $v = '';
        $detector->_detectRerenderAnimationName($d, $option->frames);
        if (is_array($d)) {     
            $num = array_filter($d)   ;
            if (count($num) != count($d)){
                igk_wln_e("failed :::: ", $d);
            }
        
            $bc = implode(";", array_filter(array_map([self::class, "_join_css_tab"], $d, array_keys($d))));
            if ($bc) {
                $v = $c . "{" . $lf . $bc . ";" . $lf . "}";
            }
        } else {
            $v =  $c . ": " . $d . ";";
        }
        return $v;
    }
    /**
     * auto generate doc.
     * @param mixed $d
     * @return mixed
     */
    private function _detectAnimGlobalName($d)
    {
        $n = igk_getv($d, 'animation');
        if ($n && preg_match("/\b\w+\b/", $n, $tab)) {
            return $tab[0];
        }
        return null;
    }
    /**
     * detect render animation frames 
     * @param mixed $d 
     * @param mixed &$frames 
     * @return mixed 
     * @throws \Exception 
     */
    private function _detectRerenderAnimationName($d, &$frames)
    {
        $n = igk_getv($d, 'animation-name') ?? $this->_detectAnimGlobalName($d);
        if ($n && ($i = igk_getv($this->m_frames, $n))) {
            $frames[$n] = $i;
            return true;
        }
        return false;
    }
    /**
     * resolve css definition by detecting source code
     * @param string $src expression or separated space list of detected word
     * @param ?array & $references
     * @throws \Exception
     */
    public function resolv(string $src, ?array &$references = null) {}
    /**
     * load used class 
     * @param array $data reference class to load without the starting '.' 
     * @param mixed &$references modifider references
     * @return array new detected list 
     */
    public function loadReferences(array $data, &$references): array
    {
        $v_c_mkey = self::MEDIA_KEY;
        $merge_medias = null;
        if (!is_null($references) && isset($references[$v_c_mkey])) {
            $merge_medias = &$references[$v_c_mkey];
        } else
            $merge_medias = [];
        $ld  = null;
        $ld = [];
        if (!is_null($references)) {
            unset($references[$v_c_mkey]);
        }
        while (count($data) > 0) {
            $q = array_shift($data);
            if ($tc = $this->getReference($q)) {
                if (isset($tc[$v_c_mkey])) {
                    $cl = $tc[$v_c_mkey];
                    foreach ($cl as $k => $v) {
                        if (!isset($merge_medias[$k])) {
                            $merge_medias[$k] = $v;
                        } else
                            $merge_medias[$k] = array_merge($merge_medias[$k], $v);
                    }
                    unset($tc[$v_c_mkey]);
                }
                if (count($tc) > 0) {
                    $ld[] = $tc;
                }
            }
        }
        $ld = array_merge(array_merge(...$ld), $merge_medias ? [$v_c_mkey => $merge_medias] : []);
        $references = array_merge($references ? $references : [], $ld);
        return $ld;
    }
    /**
     * auto generate doc.
     * @param int $index
     * @return mixed
     */
    public function getReferencedByIndex(int $index)
    {
        // if (empty($this->m_mapped) || (count($this->m_references) != $this->m_mapped)){
        //     $this->m_mapped = [array_values($this->m_references), array_keys($this->m_references)];
        // }
        list($v, $k) = $this->m_mapped ?? $this->m_mapped = [array_values($this->m_references), array_keys($this->m_references)];
        // list($v, $k) = [array_values($this->m_references), array_keys($this->m_references)];
        $l = igk_getv($v, $index);
        if ($l) {
            return ["classes" => $l, "defs" => igk_getv($k, $index)];
        }
        igk_environment()->isDev() && igk_die("mapped not found at index ".$index);
        return null;
    }

    /**
     * set prefix resolver 
     * @param mixed $resolver 
     * @return void 
     */
    public function setAutoPrefixResolver($resolver)
    {
        $this->m_auto_prefixResolver = $resolver;
    }

    /**
     * resolver 
     * @return mixed
     */
    public function getAutoPrefixResolver()
    {
        if (is_null($this->m_auto_prefixResolver)){
            $this->m_auto_prefixResolver = $this->_initAutoPrefixResolver() ?? igk_die('required an autoprefix resolver');
        }
        return $this->m_auto_prefixResolver;
    }
    /**
     * 
     * @return CssAutoClassResolver 
     */
    protected function _initAutoPrefixResolver(): CssAutoClassResolver{
        return new CssAutoClassResolver;
    }
    /**
     * 
     * @param mixed $tab 
     * @return void 
     */
    protected function _loadResolverDefinition($tab)
    {

        list($prefix, $value, $screen, $theme) = igk_extract($tab, 'prefix|value|screen|theme');
        $code = $this->m_auto_prefixResolver->resolveCode($prefix, $value, [
            'colorspace'=>$this->colorspaces
        ]);
        if (is_null($code)){
            return null;
        }
        $mt = null;
        $inject_base = false;
        if ($code[0]=='[')
        {
            $tcode = json_decode($code);
            $mt = $prefix.'-'.$value.$tcode[0];
            $code = json_encode($tcode[1]);
            $tab[0] = $mt;
            // $inject_base = true;
        }
        /// ?? igk_die("value can't be resolved - for ".$prefix.'-'.$value);

        $id = $this->_identifyCodeReference($code);
        $mt = $mt ?? $prefix . "-" . $value;
        $v_tref = [$mt];
        if ($tab[0] !== $mt) {
            $v_tref[] = $tab[0];
        }
        $t = '';
        while (count($v_tref) > 0) {
            $mt = array_shift($v_tref); 
            $t = '.' . $mt;
            if (!$inject_base){
                $this->_registerReference($t, null, $code, $id);
                $inject_base = !$inject_base && ($screen || $theme);
            }
            if ($screen) {
                $this->_registerReference('.sm .sm-' . $prefix . "-" . $value, $t, $code, $id);
                $this->_registerReference('.xsm .xsm-' . $prefix . "-" . $value, $t, $code, $id);
                $this->_registerReference('.lg .lg-' . $prefix . "-" . $value, $t, $code, $id);
                $this->_registerReference('.xlg .xlg-' . $prefix . "-" . $value, $t, $code, $id);
                $this->_registerReference('.xxlg .xxlg-' . $prefix . "-" . $value, $t, $code, $id);
            }
            if ($theme) {
                $this->_registerReference('html[data-theme="dark"] .dark-' . $prefix . "-" . $value, $t, $code, $id);
                $this->_registerReference('html[data-theme="light"] .light-' . $prefix . "-" . $value, $t, $code, $id);
            }
            if ($theme && $screen){
                foreach(['dark','light'] as $theme){
                    foreach(['xsm','sm','lg','xlg','xxlg'] as $sm){
                        $this->_registerReference('html[data-theme="'.$theme.'"] .'.$sm.' .'.$theme.'-'.$sm.'-' . $prefix . "-" . $value, $t, $code, $id);
                        $this->_registerReference('html[data-theme="'.$theme.'"] .'.$sm.' .'.$sm.'-'.$theme.'-' . $prefix . "-" . $value, $t, $code, $id);
                    }
                }
            }
        }
    }
    /**
     * auto generate doc.
     * @param string $sourcekey
     * @return mixed
     */
    public function getReference(string $sourcekey)
    {
        // auto source key 
        $v_autoResolver = $this->getAutoPrefixResolver();
        $tab = [];
        if ($v_autoResolver && !isset($this->list['.' . $sourcekey]) && $v_autoResolver->detect($sourcekey, $tab) ) {
            $this->_loadResolverDefinition($tab);
        }


        if ($sourcekey[0] != '.') {
            $sourcekey = '.' . $sourcekey;
        }
        if (($r = igk_getv($this->list, $sourcekey)) instanceof CssItemInfo) {
            $t = [];
            $media = [];
            foreach ($r->source as $k => $v) {
                $source_index = igk_getv($r->references, $k);
                if ($g = $this->getReferencedByIndex($source_index)) {
                    $scr = json_decode($g['defs']);
                    if ($r->isReferenceMedia($k)) {
                        $media_key = key($r->mediaReferences[$k]);
                        $media[$media_key][$v] = $scr;
                    } else {
                        $t[$v] = $scr;
                    }
                }
            }
            return count($media) > 0 ? array_merge($t, ['@media' => $media]) : $t;
        }
        return null;
    }
    /**
     * get regex definition 
     * @return string 
     */
    public function getMatchRegex()
    {
        if ($r = array_keys($this->list)) {
            sort($r);
            return str_replace("\\[", '[', sprintf('\b(?:%s)\b', addslashes(implode('|', array_map(function ($a) {
                $m = substr($a, 1);
                $m = preg_replace("/(\[|\])/", "\\\\$1", $m);
                return $m;
            }, $r)))));
        }
        return null;
    }
    /**
     * auto generate doc.
     * @return mixed
     */
    private function _clear()
    {
        $this->list = [];
        $this->m_frames = [];
    }
    /**
     * load parsed definition 
     * @param array $tab 
     * @param bool $clear 
     * @return array<string|int,ICssClassList> 
     */
    public function map(array $tab, bool $clear = false)
    {
        if ($clear) {
            $this->_clear();
        }
        $this->m_mapped = null;
        array_map(function ($a, $key) {
            self::_MapList($a, $this, $key);
        }, $tab, array_keys($tab));
        // $fit = igk_getv($this->list, '.fit');
        // if (igk_env_count(__FUNCTION__)>1){
        // $g = array_keys($this->m_references)[$fit->references[1]];
        // }
        return $this->list;
    }
    /**
     * auto generate doc.
     * @param array $a
     * @param CssClassNameDetector $detector
     * @param mixed $key
     * @return void
     */
    private static function _MapArray(array $a, CssClassNameDetector $detector, string $key)
    {
        if (is_numeric($key))
            return;
        $q = $detector;
        $i = $key;
        if (preg_match('/(^[a-z][a-z\-0-9]*(:[[a-z][a-z\-0-9]+])?$)|(#)/i', trim($i))) {
            // tag only list, contains id 
            if (!isset($q->tags[$i])) {
                $q->tags[$i] = [];
            }
            $q->tags[$i] = array_merge($q->tags[$i], $a);
            return;
        }
        // if (!is_numeric($key))
        //     igk_wln($key);

        if ($c = preg_match_all(self::CL_REGEX, $key, $tab)) {
            $v_code_key = json_encode($a);
            $v_id_key = $detector->_identifyCodeReference($v_code_key);
            $_media = $detector->m_media;
            for ($i = 0; $i < $c; $i++) {
                $n = $tab[0][$i];
                $detector->_registerReference($key, $n, $v_code_key, $v_id_key);
                
            }
        } else {
            if (!is_numeric($key) && igk_is_debug())
                Logger::info('skip: ' . $key);
        }
    }
    /**
     * identifyCodeReference  from code 
     * @param string $code 
     * @return int|string|false 
     */
    protected function _identifyCodeReference(string $code)
    {
        $detector = $this;
        $v_code_key = $code;
        $v_id_key = -1;
        if (!isset($detector->m_references[$v_code_key])) {
            $detector->m_references[$v_code_key] = [];
            $v_id_key = count($detector->m_references) - 1;
        } else {
            $v_id_key = array_search($v_code_key, array_keys($detector->m_references));
        }
        return $v_id_key;
    }
    /**
     * register reference
     * @param string $key 
     * @param null|string $class_code 
     * @param string $code 
     * @param int $refid 
     * @return void 
     */
    protected function _registerReference(string $key, ?string $class_code, string $code, int $refid)
    {
        $class_code = $class_code ?? $key;
        $n = $class_code;
        $v_code_key = $code;
        $v_id_key = $refid;
        $detector = $this;
        $_media = $detector->m_media;

        if (false !== strpos($n, ':')) {
            $n = explode(':', $n, 2)[0];
        }
        $id = $n;
        if (!isset($detector->list[$id])) {
            $l = new CssItemInfo($id);
            $l->count = 1;
            $l->references = [$v_id_key];
            $l->source = [$key];
            $detector->list[$id] = $l;
        } else {
            $l = $detector->list[$id];
            $l->count++;
            $l->references[] = $v_id_key;
            $l->source[] = $key;
        }
        if ($_media) {
            $l_idx = count($detector->list[$id]->references) - 1;
            if (!isset($detector->list[$id]->mediaReferences[$l_idx])) {
                $detector->list[$id]->mediaReferences[$l_idx] = [];
            }
            $detector->list[$id]->mediaReferences[$l_idx][$_media] = 1;
        }
        if (!in_array($id, $detector->m_references[$v_code_key]))
            $detector->m_references[$v_code_key][] = $id;
    }
 
 
    /**
     * auto generate doc.
     * @param mixed $a
     * @param CssClassNameDetector $detector
     * @param string $key
     * @return void
     */
    private static function _MapList($a, CssClassNameDetector $detector, string $key)
    {
        if (is_array($a)) {
            self::_MapArray($a, $detector, $key);
            return;
        }
        if ($a instanceof CssKeyFrame) {
            $detector->m_frames[$a->name] = $a;
            return;
        }
        if ($a instanceof CssMedia) {
            $condition = $a->condition;
            $detector->m_media = $condition;
            array_map(function ($a, $key) use ($detector) {
                self::_MapArray($a, $detector, $key, true);
            }, $a->def, array_keys($a->def));
            $detector->m_media = null;
            return;
        }
        if ($a instanceof CssComment) {
            return;
        }
        if ($a instanceof CssSupport) {
            $condition = $a->rule;
            $detector->m_media = $condition;
            array_map(function ($a, $key) use ($detector) {
                self::_MapArray($a, $detector, $key, true);
            }, $a->def, array_keys($a->def));
            $detector->m_media = null;
            return;
        }
        if ($a instanceof CssOptions) {
            return;
        }
        if (is_object($a) && igk_environment()->isDev())
            igk_wln(__FILE__ . ":" . __LINE__, "not handle : css class ", get_class($a));
    }
    /**
     * auto generate doc.
     * @param array $a
     * @param null|CssClassNameDetector $detector
     * @return null
     */
    public static function Detect(array $a, ?CssClassNameDetector  $detector = null)
    {
        $q = $detector ?? new static;
        $q->source = $a;
        $tlist = array_keys($a);
        array_map(function ($i) use ($q) {
            // if (preg_match('/^[a-z][a-z\-]+(:[[a-z][a-z\-]+])?$/i', $i)){
            //     // tag only list
            //     if (isset($q->tags[$i])){
            //         $q->tags[$i] = [];
            //     }
            //     $q->tags[$i][] = $q->source[$i];
            //     return;
            // }

            if ($c = preg_match_all("/\.\b[a-z][a-z0-9\-]*\b(\[[a-z][a-z0-9\-]*\])?/i", $i, $a)) {
                $ii = 0;
                while ($ii > $c) {
                    $t = $a[$ii];
                    $ii++;
                    $id = $t[0];
                    if (!isset($q->list[$id])) {
                        $q->list[$id] = [];
                    }
                    $q->list[$id][] = $q->source[$i];
                }
            }
            return null;
        }, $tlist);
        return null;
    }

    /**
     * detector list 
     * @var ?array
     */
    protected $m_detectors;

    /**
     * 
     * @param string $n 
     * @return mixed|object 
     */
    public function getDetector(string $n)
    {
        if ($r = igk_getv($this->m_detectors, $n)) {
            return $r;
        }
        $detector = __NAMESPACE__ . '\\Css' . ucfirst($n) . 'Detector';
        $g = new $detector;
        $this->m_detectors[$n] = $g;
        $g->list = $this->list;
        return $g;
    }
}
