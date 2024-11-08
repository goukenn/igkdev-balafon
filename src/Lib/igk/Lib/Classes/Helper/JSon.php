<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSon.php
// @date: 20230103 23:37:50
namespace IGK\Helper;

use Exception;
use IGK\System\IToArrayResolver;
use IGK\System\Regex\Replacement;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use JsonSerializable;
use stdClass;

///<summary></summary>
/**
 * helper to encode in json 
 * @package IGK\Helper
 */


class JSon
{
    const _map_to_object_method = '_map_to_object';
    const JSON_PRETTY_VIEW = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
    /**
     * encoding option
     * @var JSonEncodeOption
     */
    protected $m_options;

    /**
     * 
     * @var mixed
     */
    protected $m_data;


    protected $m_path;


    /**
     * encode
     * @param int $encode 
     * @return string|false 
     */
    public function enc(int $encode)
    {
        $root = $this->get_root_data($this->m_data);
        return $root ? json_encode($root, $encode) : null;
    }
    protected function _filter_array(&$tv)
    {
        if ($fc = $this->m_options->filter_array_listener) {
            $tv = array_values(array_filter(array_map($fc, $tv)));
        } else if ($this->m_options->ignore_empty) {
            // preserving string keys -
            $tv = array_filter(array_map([$this, 'filter_array'], $tv));
        }
    }
    /**
     * 
     * @param mixed $data 
     * @return mixed 
     * @throws Exception 
     */
    public function get_root_data($data)
    {
        $root = $keys = $c = null;
        if (is_array($data)) {
            $is_assoc = false;
            $mkeys = array_keys($data);
            $c = [];
            while (count($mkeys) > 0) {
                $k = array_shift($mkeys);
                $tv = $data[$k];
                if (!is_numeric($tv) && $this->m_options->ignore_empty && empty($tv)) {
                    continue;
                }
                if (!is_numeric($k)) {
                    $is_assoc = true;
                    $root = (object)$c;
                    $c =  $root;
                    array_unshift($mkeys, $k);
                    break;
                }
                if (is_object($tv)) {
                    if ($tv instanceof IToArrayResolver) {
                        $tv = $tv->to_array();
                    } else {
                        $tv = (array)$tv;
                    }
                }
                if (is_array($tv)) {
                    $this->_filter_array($tv);
                    $tv = array_map([$this, self::_map_to_object_method], $tv);
                }
                $c[] = $tv;
            }
            if (!$is_assoc) {
                return $c;
            }
            $keys = $mkeys;
            $c = (object)$c;
            if (empty($keys)) {
                $root = $c;
                return $root;
            }
        } else if (($data instanceof IToArrayResolver) || method_exists($data, 'to_array')) {
            $data = $data->to_array();
            $keys = array_keys($data);
        }
        $this->_filter_array_map($data, $keys, $c, $root);
        $root = $data;
        return $root;
    }
    protected function _map_to_object($data)
    {
        if (is_object($data) && (($data instanceof IToArrayResolver) || method_exists($data, 'to_array'))) {
            // recurcivity - possibility of infinity loop;
            $data = array_map([$this, __FUNCTION__], $data->to_array());
        }
        return $data;
    }
    private static function _ConvertItemObject($a)
    {
        if ($a instanceof JsonSerializable) {
            $a = $a->jsonSerialize();
        }
        if ($a instanceof IToArrayResolver) {
            $a = $a->to_array();
        } else if (!($a instanceof stdClass)) {
            $a = (object)(array)$a;
        }
        return $a;
    }
    /**
     * filter array 
     * @param mixed $a 
     * @return mixed 
     * @throws IGKException 
     */
    public function filter_array($a)
    {
        if (is_object($a)) {
            $a = self::_ConvertItemObject($a);
            $c = $this->get_root_data($a);
            return $c;
        } else if (is_array($a)) {
            if ($this->m_options->ignore_empty) {
                $this->_filter_array_map($a);
            }
        }
        return $a;
    }
    private function _filter_array_map(&$tv, $keys = null, $c = null, $root = null)
    {
        $root =  $root;
        $is_object = false;
        $tq = [['d' => $tv, 'keys' => $keys, 'c' => $c]];
        // $path = & $this->m_path;
        while (count($tq) > 0) {
            $q = array_shift($tq);
            extract($q);
            $v = $d;
            $keys = $keys ?? array_keys((array)$d);
            $is_object = (isset($is_object) ? $is_object : null) ?? is_object($v);
            $end = false;
            while (!$end  && (count($keys) > 0)) {
                $k = array_shift($keys);
                if (strpos($k, "\0") === 0) {
                    continue;
                }
                $tv = igk_getv($v, $k);
                if ((!is_bool($tv) && !is_numeric($tv)) && $this->m_options->ignore_empty && empty($tv)) {
                    continue;
                }
                if (is_null($tv) && $this->m_options->ignore_null) {
                    continue;
                }
                if (is_null($root)) {
                    $root = (object)[];
                    $c = $root;
                }
                if ($tv instanceof IToArrayResolver) {
                    $tv = $tv->to_array();
                }
                if (is_array($tv)) {
                    if ($fc = $this->m_options->filter_array_listener) {
                        $tv = array_values(array_filter(array_map($fc, $tv)));
                    } else if ($this->m_options->ignore_empty) {
                        $tv = array_filter(array_map([$this, 'filter_array'], $tv));
                    } else {
                        // transform item to native object 
                        $tv = array_map(function ($a) {
                            if (is_object($a)) {

                                $a = self::_ConvertItemObject($a);
                            }
                            return $a;
                        }, $tv);
                    }
                } else if (is_object($tv)) {
                    if ($tv instanceof IJSonLitteral) {
                        $c->$k = $tv;
                        continue;
                    }

                    if ($tv instanceof IJSonEncodeArrayDefinition) {
                        if ($tv->isEmpty()) {
                            if ($tv->isRequired()) {
                                $c->$k = [];
                                continue;
                            }
                        }
                    }

                    if ($tv instanceof JsonSerializable) {
                        if ((($m = $tv->jsonSerialize()) === null) && ($this->m_options->ignore_null)) {
                            continue;
                        }
                        $tv = $m;
                    }

                    array_unshift($tq, ['d' => $d, 'keys' => $keys, 'c' => $c, 'is_object' => $is_object]);
                    $c->$k = new stdClass;
                    array_unshift($tq, ['d' => $tv, 'keys' => null, 'c' => $c->$k, 'is_object' => true]);
                    $end = true;
                    break;
                }

                $c->$k = $tv;
            }
        }
        if (!$is_object) {
            $root = (array)$root;
        }
        $tv = $root;
    }
    /**
     * encode data
     * @param mixed $data 
     * @param mixed|JSonEncodeOption $options 
     * @param int $encode 
     * @return string|false 
     */
    public static function Encode($data, $options = null, int $encode = JSON_UNESCAPED_SLASHES)
    {

        if (is_null($options)) {
            $options = new JSonEncodeOption;
        } else if (!($options instanceof JSonEncodeOption)) {
            $options = Activator::CreateNewInstance(JSonEncodeOption::class, $options);
        }
        $e = new static;
        $e->m_options = $options;
        $e->m_data = $data;
        $e->m_path = '/';
        $r = $e->enc($encode);
        // + | --------------------------------------------------------------------
        // + | treat js data
        // + | 
        // + |
        if ($r) {
            $container = new RegexMatcherContainer;
            $container->begin("\"\[\[js-data:", "}]]\"", "block");
            $pos = 0;
            $o = $r;
            while ($g = $container->detect($r, $pos)) {
                $g = $container->end($g, $r, $pos);

                switch ($g->tokenID) {
                    case 'block':
                        $rp = new Replacement;
                        $rp->add("/\"\[\[js-data:\{(.+)\}\]\]\"/m", "{\\1}");
                        $t = $rp->replace($g->value);
                        $t = str_replace("\\\"", "\"", $t);
                        $t = JSTreatment::RemoveOutsideSymbol($t);

                        // $t = str_replace("\\n", "", $t);
                        $o = str_replace($g->value, $t, $o);
                        break;
                }
            }
            $r = $o;
        }
        return $r;
    }
    protected function __construct() {}

    /**
     * code for html attribute
     * @param mixed $data 
     * @param mixed $encode_options 
     * @param int $js_options 
     * @return string 
     */
    public static function EncodeForHtmlAttribute($data, $encode_options, int $js_options = JSON_UNESCAPED_SLASHES)
    {
        return htmlentities(self::Encode($data, $encode_options));
    }
}
