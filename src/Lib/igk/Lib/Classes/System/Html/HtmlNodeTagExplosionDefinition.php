<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeTagExplosionDefinition.php
// @date: 20230328 13:47:42
namespace IGK\System\Html;

use Exception;
use IGK\System\ArrayMapKeyValue;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Exceptions\HtmlNodeTagExplosionTagNameAlreadyDefineException;
use IGK\System\Html\Traits\HtmlNodeTagExplosionTrait;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use ReflectionException;

/**
 * explode tag definition 
 * @package IGK\System\Html
 */
class HtmlNodeTagExplosionDefinition
{
    use HtmlNodeTagExplosionTrait;
    const split = '>';
    // + | --------------------------------------------------------------------
    // + | prefix definition 
    // + |
    const identifier = '#';
    const name = '%';
    const classes = '.';
    const DEF_METHOD = 'DefinitionArgs';
    /**
     * 
     * @var HtmlNodeBuilder
     */
    var $builder;
    /**
     * explode definition 
     */
    protected $split = self::split;
    private static $sm_static;
    public function __construct(HtmlNodeBuilder $builder)
    {
        $this->builder = $builder;
    }
    /**
     * get core builder 
     * @return static
     */
    public static function Core()
    {
        return self::$sm_static ?? self::$sm_static = new static(new HtmlNodeBuilder(igk_create_notagnode()));
    }
    /**
     * 
     * @param mixed $node 
     * @param array $data 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function setup($node, $data, $tnode = null)
    {
        $bck = $this->builder->t;
        if ($tnode) {
            $this->builder->t = $tnode;
        }
        $r = $this->builder->setup($node, $data);
        $this->builder->t = $bck;
        return $r;
    }
    /**
     * explode tag
     * @param string $tagname 
     * @param mixed $pnode 
     * @param mixed $context 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function explode(string $tagname, &$pnode, $context = null)
    {
        //  $context = $context ?? $this->getContext();
        // $v_root_tag = $tagname;
        $id =
            $classes =
            $args =
            $name =
            $attr = null;
        $defs = [];
        // $ln = strlen($tagname);
        // $pos = 0;
        $v = "";
        $this->explodeTagDefinition($tagname, $defs, $v);
        $n = &$pnode;
        $v_node_creates = [];
        while (count($defs) > 1) {
            $q = array_shift($defs);
            list($tagname, $id, $classes, $args, $name, $attr) = self::ExplodeTag2($q, $context);
            if (is_null($args)) {
                $args = [];
            }
            $n = $n->$tagname(...$args);
            if (!$n) {
                igk_die("failed to add . " . $tagname);
            }
            $this->builder->onCreate($n); //['node'=>$n,'root_tag'=>$v_root_tag]);
            array_unshift($v_node_creates, $n);
            if ($classes) {
                $n->setClass($classes);
            }
            if ($attr) {
                $n->setAttributes($attr);
            }
            if ($id) {
                $n->setAttribute('id', $id);
            }
            if ($name) {
                $n->setAttribute('name', $name);
            }
        }
        $tagname = array_shift($defs);
        list($tagname, $id, $classes, $args, $name, $attr) = self::ExplodeTag2($tagname, $context);
        return [trim($tagname), $id, $classes, $args, $name, $attr];
    }
    /**
     * map array definition 
     * @param mixed $i 
     * @return mixed 
     */
    public static function DefinitionArgs($i)
    {
        if (!is_string($i)) {
            return $i;
        }
        if ($i == '[[:@raw]]') {
            return $i;
        }
        if ($i == '[[:@ctrl]]') {
            return $i;
        }
        if (preg_match("/^\[.+\]/", $i)) {
            // convert array 
            return json_decode($i);
        }
        return $i;
    }
    /**
     * read active attribute per arg array
     * @param string $a 
     * @return array 
     */
    private static function _GetActiveAttribute(string &$a)
    {
        $active_attrib = [];
        // add active attribute 
        while (preg_match("/(@[a-z_\-]([a-z0-9_\-]+)?)((\\s+@[a-z_\-]([a-z0-9_\-]+)?)+)?(\\s*,)/i", $a, $tbm)) {
            $s = $tbm[0];
            $a = str_replace($s, '', $a);
            $s = array_map(function ($a) use (&$active_attrib) {
                $k = substr(trim($a), 1);
                return $k;
            }, explode(' ', trim($s, ', ')));
            $active_attrib = array_fill_keys(array_merge(array_keys($active_attrib), $s), 1);
        }
        return $active_attrib;
    }

    /**
     * explode tag definitions 
     * @param string $tagname 
     * @param mixed $context 
     * @return array [trim($tagname), $id, $classes, $args, $name, $attr];
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @deprecated
     */
    public static function ExplodeTag(string $tagname, $context = null): array
    {
        $id = null;
        $classes = null;
        $args = null;
        $name = null;
        $attr = null;
        if (strpos($tagname, '(') !== false) {
            !preg_match("/\((?P<name>[^\)]+)/i", $tagname, $tab) && igk_die("argument not valid. " . $tagname);
            //+| get args to setup
            $start = $pos = strpos($tagname, '(');
            $g = igk_str_read_brank($tagname, $pos, ')', '(');
            $a = substr($g, 1, -1);
            $args = igk_engine_get_attr_arg($a, $context);
            if ($args) {
                $args = array_map([self::class, self::DEF_METHOD], $args);
            }
            $tagname = igk_str_rm($tagname, $start,  $pos - $start + 1);
            //  igk_debug_wln("current context ", $tagname, $args, HtmlLoadingContext::GetCurrentContext());
        }
        if (strpos($tagname, '[') !== false) {
            !preg_match("/\[(?P<name>[^\[\]]+)/i", $tagname, $tab) && igk_die("argument not valid. " . $tagname);
            // get args to setups
            $start = $pos = strpos($tagname, '[');
            $g = igk_str_read_brank($tagname, $pos, ']', '[');
            $a = substr($g, 1, -1);
            // $attr = igk_engine_get_attr_arg($a, $context);
            $tagname = igk_str_rm($tagname, $start,  $pos - $start + 1);
            $r = self::InitConfigurationReader();
            $v_activa_attrib = self::_GetActiveAttribute($a);
            $attr = ArrayMapKeyValue::Map(function ($k, $v) {
                if (is_null($v)) {
                    if (strpos($k, "@") === 0) {
                        return [$k = ltrim($k, '@'), new HtmlActiveAttrib];
                    }
                    return null;
                }
                return [$k, $v];
            }, (array)$r->read($a));
            if ($v_activa_attrib) {
                foreach (array_keys($v_activa_attrib) as $m) {
                    if (isset($attr[$m])) continue;
                    $attr[$m] = new HtmlActiveAttrib;
                }
            }
        }
        // + | identify id : #
        if (strpos($tagname, '#') !== false) {
            $c = preg_match_all("/#(?P<name>[^\%\.#\\s\(\)!]+)/i", $tagname, $tab);
            for ($i = 0; $i < $c; $i++) {
                // get id last id and remove it from tag
                $id = $tab['name'][$i];
                // $tagname = str_replace($tab[0][$i], '', $tagname);
                self::_StrRmValue($tagname, $tab[0][$i]);
            }
        }
        // + | active attribute in tagname selection tagname
        if (strpos($tagname, '!') !== false) {
            $c = preg_match_all("/!(?P<name>[^!\%\.#\\s\(\)]+)/i", $tagname, $tab);
            for ($i = 0; $i < $c; $i++) {
                // get id last id and remove it from tag
                $ac = $tab['name'][$i];
                $attr[$ac] = new HtmlActiveAttrib();
                // $tagname = str_replace($tab[0][$i], '', $tagname);
                self::_StrRmValue($tagname, $tab[0][$i]);
            }
        }
        // + | identify class : .
        if (($v_pos = strpos($tagname, '.')) !== false) {
            $tclasses = [];
            if ($c = preg_match_all("/\.(?P<name>[^\%\.\\s#\(\)]+)/i", $tagname, $tab)) {
                for ($i = 0; $i < $c; $i++) {
                    // get id last id and remove it from tag
                    $tclasses[$tab['name'][$i]] = 1;
                    self::_StrRmValue($tagname, $tab[0][$i]);
                }
            } else {
                if (igk_environment()->isDev()) {
                    igk_die("not a valid class specification.2 : " . $tagname);
                }
                $tagname = substr($tagname, $v_pos, 1);
            }
            $classes = implode(' ', array_keys($tclasses));
        }
        // + | identify name : % identify 
        if (strpos($tagname, '%') !== false) {
            $c = preg_match_all("/\%(?P<name>[^\.#\\s\(\)]+)/i", $tagname, $tab);
            for ($i = 0; $i < $c; $i++) {
                // get id last id and remove it from tag
                $name = $tab['name'][$i];
                self::_StrRmValue($tagname, $tab[0][$i]);
            }
        }
        return [trim($tagname), $id, $classes, $args, $name, $attr];
    }



    /**
     * explode tag 2
     * @param string $tagname 
     * @param mixed $context 
     * @return array 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function ExplodeTag2(string $tagname, $context = null): array
    {
        $regex = new RegexMatcherContainer;
        $v_s = $regex->appendStringDetection('string', true)->last();
        $v_b = $regex->begin('\[', '\]', 'cbrank')->last();
        $pbrank = $regex->begin('\(', '\)', 'pbrank')->last();
        $regex->begin('\{', '\}', 'curl-brank');
        $regex->match('([#\\.%!@])[a-zA-Z_][a-zA-Z0-9_\-]*(:[a-zA-Z_][a-zA-Z0-9_\-]*)?', 'litteral');
        $regex->match('[a-zA-Z_][a-zA-Z0-9_\-]*(:[a-zA-Z_][a-zA-Z0-9_\-]*)?', 'tag-litteral');
        $regex->resetTreatment();

        $pbrank->patterns = [

            $pbrank
        ];


        $v_b->patterns = [
            $v_b,
            $v_s
        ];
        $pos = 0;
        $definition = [
            'classes' => [],
            'id' => null, // ids 
            'name' => null,
            'tagname' => null,
            'attr' => null, // active attribute ,
            'args' => null,
        ];
        $list = [
            'tag-litteral' => function (&$def, $e) {
                self::_SetTagName($e, $def);
            },
            'litteral' => function (&$def, $e) {
                if ($c = igk_getv($e->beginCaptures, 1)) {
                    $t = substr($e->value, 1);
                    switch ($c[0]) {
                        case '.':
                            $def['classes'][$t] = 1;
                            break;
                        case '!':
                            $def['attr'][$t] = new HtmlActiveAttrib();
                            break;
                        case '%':
                        case '@':
                            $def['name'] = $t;
                            break;
                        case '#':
                            $def['id'] = $t;
                            break;
                    }
                } else {
                    self::_SetTagName($e, $def);
                }
            },
            'pbrank' => function (&$def,  $e) use ($context) {
                $def['args'] && igk_die('params already passed');
                $a = substr($e->value, 1, -1);
                $args = igk_engine_get_attr_arg($a, $context);
                if ($args) {
                    $args = array_map([HtmlNodeTagExplosionDefinition::class, self::DEF_METHOD], $args);
                }
                $def['args'] = $args;
            },
            'cbrank' => function (&$def, $e) use ($context) {
                $a = substr($e->value, 1, -1);
                if ($e->parentInfo) {
                    if ($e->parentInfo->match->tokenID == 'cbrank') {
                        return;
                    }
                }
                $r = self::InitConfigurationReader();
                $attr = ArrayMapKeyValue::Map(function ($k, $v) use (&$v_active_attrib) {
                    if (strpos($k, "@") === 0) {
                        $nk = ltrim($k, '@');
                        if (is_null($v)) {
                            return [$nk, new HtmlActiveAttrib];
                        } else {
                            if ($v instanceof HtmlActiveAttrib) {
                                return [$nk, $v];
                            }
                            return null;
                        }
                    }
                    return [$k, $v];
                }, (array)$r->read($a));
                $def['attr'] = array_merge($def['attr'] ?? [], $attr);
            }
        ];
        $s = $tagname;
        while ($g = $regex->detect($s, $pos)) {
            if ($e = $regex->end($g, $s, $pos)) {
                igk_is_debug() && Logger::info($e->tokenID . ':' . $e->value);
                if ($fc = igk_getv($list, $e->tokenID)) {
                    $fc($definition, $e);
                }
            }
        }
        return igk_extract($definition, 'tagname|id|classes|args|name|attr');
    }
    /**
     * 
     * @return ConfigurationReader 
     */
    protected static function InitConfigurationReader()
    {
        $r = new ConfigurationReader();
        $r->activeAttribute = new HtmlActiveAttrib;
        $r->separator = ':';
        $r->delimiter = ',';
        $r->escape_start = '[';
        $r->escape_end = ']';
        return $r;
    }
    private static function _SetTagName($e, &$def)
    {
        $t = $e->value;
        if (isset($def['tagname']))
            throw new HtmlNodeTagExplosionTagNameAlreadyDefineException($t);
        $def['tagname'] = $t;
    }
    /**
     * 
     * @param string &$tagname 
     * @param mixed $value 
     * @return void 
     */
    private static function _StrRmValue(string &$tagname, $value)
    {
        $ln  = strlen($value);
        $pos = strpos($tagname, $value);
        $tagname = substr($tagname, 0, $pos) . substr($tagname,  $pos + $ln);
    }
    /**
     * create node wwith date deifnition 
     * @param string $tag_def 
     * @return array 
     * @throws IGKException 
     * @throws Exception 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function CreateNodes(string $tag_def, ...$args)
    {
        $ctn = new RegexMatcherContainer;
        $ctn->appendStringDetection();
        $ctn->match(self::split, 'split');
        $offset = 0;
        $rf = [];
        $p = 0;
        while ($g = $ctn->detect($tag_def, $offset)) {
            if ($e = $ctn->end($g, $tag_def, $offset)) {
                if ($e->tokenID == 'split') {
                    $rf[] = trim(substr($tag_def, $p,  $e->from - $p));
                    $p = $e->to;
                }
            }
        }
        if ($s = trim(substr($tag_def, $p)))
            $rf[] = $s;
        $root = $last = $parent = null;
        // $bck_parent = igk_html_parent_node();
        while (count($rf) > 0) {
            $q = array_shift($rf);
            $targ = empty($rf) ? $args : [];
            if ($parent) {
                igk_html_push_node_parent($parent);
            }
            $n = self::CreateNodeArg($q, ...$targ);
            if (is_null($root)) {
                $root = $last = $parent = $n;
            } else {
                if ($last) {
                    $last->add($n);
                }
                $last = $n;
                $parent = $n;
            }
            igk_html_pop_node_parent();
        }
        return [$root, $last];
    }
    /**
     * create node args 
     * @param string $tagname 
     * @param mixed ...$index_or_args 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function CreateNodeArg(string $tagname, ...$index_or_args)
    {
        list($tagname, $id, $classes, $args, $name, $attr) = self::ExplodeTag2($tagname);
        $n = HtmlNode::CreateWebNode($tagname, null, $index_or_args);
        if ($attr) {
            $n->setAttributes($attr);
        }
        if ($classes) {
            $n['class'] = $classes;
        }
        if ($id) {
            $n['id'] = $id;
        }
        return $n;
    }
}
