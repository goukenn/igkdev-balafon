<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameDetector.php
// @date: 20240913 09:08:04
namespace IGK\System\Html\Css;


///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssClassNameDetector
{
    /**
     * 
     * @var mixed
     */
    var $source;
    /**
     * contain list information 
     * @var array<ICssClassList>
     */
    var $list;

    const CL_REGEX = "/\.\b[a-z][a-z0-9\-]*\b((?::\w+|(?:\[[a-z][a-z0-9\-]*\])))?/i";
    const MEDIA_KEY = '@media';
    private $m_references;
    private $m_mapped;
    private $m_frames;
    /**
     * media flag in use
     * @var ?string
     */
    private $m_media;
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
    private static function _join_css_tab($d, $k){
        if (trim($d))
        return $k . ":" . $d;
    }
    /**
     * 
     * @param array $resolv_definition 
     * @param mixed $option 
     * @return string 
     */
    public function renderToCss(array $resolv_definition, $option = null)
    {
        $option = $option ?? (object)[
            'lf' => "\n",
        ];
        $option->frames = [];
        $option->medias = [];
        $detector = $this;
        $_out = [];
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
            //$_tout[] = "/* frames */";
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
     * 
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
            $bc = implode(";", array_filter(array_map([self::class, "_join_css_tab"], $d, array_keys($d))));
            if ($bc){
                $v = $c . "{" . $lf . $bc. ";" . $lf . "}";
            }
        } else {
            $v =  $c . ": " . $d . ";";
        }
        return $v;
    }
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
     * @return void 
     * @throws Exception 
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
     * @param array|null $references 
     * @throws Exception 
     */
    public function resolv(string $src, array &$references = null)
    {
        $v_c_mkey = self::MEDIA_KEY;
        if ($dc = $this->getMatchRegex()) { 
            $v_dc = sprintf('/(?<!-)%s(?!-)/', $dc); // str_replace("\\[",'[', substr($dc,8127, 45)));
            $c = preg_match_all($v_dc, $src, $tab); 
            if ($c) {
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

                while (count($tab[0]) > 0) {
                    $q = array_shift($tab[0]);
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
        }
    }

    /**
     * 
     * @param int $index 
     * @return mixed 
     * @throws Exception 
     */
    public function getReferencedByIndex(int $index)
    {
        list($v, $k) = $this->m_mapped ?? $this->m_mapped = [array_values($this->m_references), array_keys($this->m_references)];
        $l = igk_getv($v, $index);
        if ($l) {
            return ["classes" => $l, "defs" => igk_getv($k, $index)];
        }
        return null;
    }


    /**
     * 
     * @param string $sourcekey 
     * @return mixed 
     * @throws Exception 
     */
    public function getReference(string $sourcekey)
    {
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
                        //replace with definition 
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
            //$r = [".info[data245]"];
            return str_replace("\\[",'[', sprintf('\b(?:%s)\b', addslashes(implode('|', array_map(function ($a) {
                $m = substr($a, 1);
               // $m = 'accordeon[sample]';
                $m = preg_replace("/(\[|\])/", "\\\\$1", $m);
                // $m = str_replace("[", "\[", $m);
                return $m;
            }, $r)))));
        }
        return null;
    }
    private function _clear()
    {
        $this->list = [];
        $this->m_frames = [];
    }
    /**
     * load parsed definition 
     * @param array $tab 
     * @param bool $clear 
     * @return array<string|int, \ICssClassList> 
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
        return $this->list;
    }
    /**
     * 
     * @param array $a 
     * @param mixed $detector 
     * @param mixed $key 
     * @return void 
     */
    private static function _MapArray(array $a, CssClassNameDetector $detector, string $key)
    {
        if ($c = preg_match_all(self::CL_REGEX, $key, $tab)) {
            // class only refrence detections
            $v_code_key = json_encode($a);
            $v_id_key = -1;
            if (!isset($detector->m_references[$v_code_key])) {
                $detector->m_references[$v_code_key] = [];
                $v_id_key = count($detector->m_references) - 1;
            } else {
                $v_id_key = array_search($v_code_key, array_keys($detector->m_references));
            }
            $_media = $detector->m_media;
            for ($i = 0; $i < $c; $i++) {
                $n = $tab[0][$i];
                $id = $n;
                if (!isset($detector->list[$id])) {
                    $l = new CssItemInfo($id);
                    $l->count = 1;
                    $l->references = [$v_id_key];
                    $l->source = [$key];
                    $detector->list[$id] = $l;
                } else {
                    $detector->list[$id]->count++;
                    $detector->list[$id]->references[] = $v_id_key;
                    $detector->list[$id]->source[] = $key;
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
        }
    }
    /**
     * 
     * @param mixed $a 
     * @param CssClassNameDetector $detector 
     * @param string $key 
     * @return void 
     * @throws Error 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _MapList($a, CssClassNameDetector $detector, string $key)
    {
        if (is_array($a)) {
            self::_MapArray($a, $detector, $key);
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
        if (is_object($a) && igk_environment()->isDev())
            igk_wln(__FILE__ . ":" . __LINE__, "not handle : css class ", get_class($a));
    }
    /**
     * 
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
}
