<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeBuilder.php
// @date: 20230311 06:46:24
namespace IGK\System\Html;

use Closure;
use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlCommentNode;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlLooperNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\IO\StringBuilder;
use IGK\System\IToArray;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html
 */
class HtmlNodeBuilder
{
    var $t;
    const KEY_CONDITION = '@_if:';
    /**
     * get node property 
     */
    const KEY_NODE_PROPERTY_PREFIX = '@_n:';
    /**
     * to allow multiple tagname creation with same key
     */
    const KEY_CUSTOM_TARGET_PREFIX = '@_t:';
    /**
     * set argument to pass to invocation node key
     */
    const KEY_ARGS = '@';
    /**
     * set attributes key
     */
    const KEY_ATTRIBS = '_';
    /**
     * attribute activation key
     */
    const KEY_ATTRIBS_ACTIVATION = '_@';
    
    const KEY_CALLBACK_HOST = 'fn()';
    const KEY_CALLBACK_HOST_PREFIX = '::fn()';
    const KEY_INVOKE_ON_LAST = '::';
    const KEY_INVOKE_ON_PARENT_LAST = '::@';
    /**
     * should be use with string method name, \
     */
    const KEY_INVOKE_FUNC = '::fn()';

    protected $explode;

    /**
     * string builder
     * @return null|string 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function __toString()
    {
        return $this->t->render();
    }
    public function __construct(HtmlItemBase $node)
    {
        $this->t = $node;
        $this->explode = new HtmlNodeTagExplosionDefinition($this);
    }
    /**
     * build node an return the last rendered element 
     * @param mixed $data 
     * @param null|HtmlItemBase $target 
     * @return HtmlItemBase 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function build($data, ?HtmlItemBase $target = null)
    {
        return self::Init($target ?? $this->t, $data);
    }
    /**
     * build data a return the node
     * @param string|HtmlItemBase $node 
     * @param mixed|array|object $data 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function setup($node, $data, &$lastchild = null)
    {
        $tnode = $node;
        $first = false;
        if (is_string($node)) {
            // $node = igk_create_node($node);
            $notag = igk_create_notagnode();
            $tnode = $this([$node => []], $notag);
            $node = $notag;
            $first = true;
        }
        (!($node instanceof HtmlItemBase)) && igk_die("no a valid node");
        $lastchild = self::Init($tnode, $data);
        if ($first) {
            $node = $node->getChilds()[0];
        }
        return $node;
    }
    /**
     * 
     * @param array|string $def_or_tag_expression 
     * @param array|HtmlItemBase|null $target if def_or_tag_expression is string target must be an array
     * @param target $def_or_tag_expression 
     * @param null|HtmlItemBase $target 
     * @return HtmlItemBase 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function __invoke($def_or_tag_expression)
    {
        if (is_string($def_or_tag_expression)) {
            $data = func_get_arg(1);
            if (!is_array($data)) {
                igk_die("arg must be an array");
            }
            $data = [$def_or_tag_expression => $data];
            $target = func_num_args() == 3 ? func_get_arg(1) : null;
        } else {
            $data = $def_or_tag_expression;
            $target = func_num_args() >= 2 ? func_get_arg(1) : null;
        }
        return $this->build($data, $target);
    }
    private static function _GetKeys(&$q)
    {
        if (!is_array($q)) {
            $q = [$q];
        }
        return array_keys($q);
    }
    /**
     * explode tag
     * @param string $tag 
     * @param mixed $node 
     * @param mixed $context 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function explodeTag(string $tag, &$node, $context = null)
    {
        return $this->explode->explode($tag, $node, $context);
    }

    /**
     * parse item to node builder presentations
     * @param HtmlItemBase $n 
     * @return void 
     */
    public static function Generate(HtmlItemBase $n, bool $ignore_empty_string = true)
    {
        $g = new HtmlVisitor($n);
        $tab = [];
        $ctab = null;
        $skip_space = false;
        $g->startVisitorListener = function (HtmlItemBase $t, $first_childs, $has_child, $last_child) use (&$tab, &$ctab, &$skip_space, $ignore_empty_string) {
            // + | filter comment 
            if ($t instanceof HtmlCommentNode) {
                return null;
            }
            $content = $t->getContent() ?? '';
            if ($t instanceof HtmlTextNode) {
                $skip = empty(trim($content));
                if ($skip && ($first_childs || $last_child))
                    return null;
                if ($skip_space && $skip) {
                    return null;
                }
                $skip_space = $skip;
            } else {
                if (empty(trim($content))) {
                    $content = '';
                }
                $skip_space = false;
            }

            $tagname = $t->getTagName();
            $v_can_render_tag = $t->getCanRenderTag();
            $s = '';
            $g = null;
            if ($tagname && $v_can_render_tag) {
                $s .= $tagname;

                if ($l = $t['id']) {
                    $s .= '#' . $l;
                }
                if ($l = $t['name']) {
                    $s .= '%' . $l;
                }
                if ($l = $t['class'] . '') {
                    $s .= '.' . implode(".", explode(" ", $l));
                }
                $lt = [];
                $g = $t->getAttributes()->to_array();
                unset($g['name'], $g['id'], $g['class']);
                if ($content) {
                    if (!$ignore_empty_string || !empty(trim($content))) {
                        if (strpos($content, '"') !== false) {
                            $content = igk_str_surround($content, '`');
                        }
                        $lt[] = $content;
                    }
                }
            }
            if ($g) {
                $lt['_'] = $g;
            }
            if ($ctab) {
                if (is_null($ctab->key)) {
                    // + | reference tab in case of null;
                    $ktab = &$ctab->tab;
                } else {
                    $ktab = &$ctab->tab[$ctab->key];
                }
            } else
                $ktab = &$tab;
            if (isset($ktab[$s])) {
                $ktab[] = [self::KEY_CUSTOM_TARGET_PREFIX . $s => &$lt];
                $s =  array_key_last($ktab);
                // $ctab =(object)['tab'=> & $ktab, 'parent'=>$ctab, 'key'=>$s];
                $ktab = &$lt;
                $s = null;
            } else {
                if (empty($s)) {
                    if (!empty($lt))
                        $ktab[] = &$lt;
                    $s = null;
                } else {
                    $ktab[$s] = &$lt;
                }
            } 
            // $sb->appendLine($s);
            $ctab = (object)['tab' => &$ktab, 'parent' => $ctab, 'key' => $s];
            return true;
        };
        $g->endVisitorListener = function (HtmlItemBase $t) use (&$ctab) {
            if ($ctab) {
                $ctab = $ctab->parent;
            }
        };
        $g->visit();

        return sprintf('$builder(%s);', igk_array_dump_short($tab, function ($v, $rp) {
            $v = HtmlUtils::GetValue($v);
            $v = $rp->replace($v);
            if (preg_match("/^`.*`$/", $v)) {
                return $v;
            }
            return igk_str_surround($v);
        }));
    }
    /**
     * init default builder and return the last created item
     * @param $n node 
     * @param array $data definition 
     * @param array|string $visitor visitor to use
     * @return HtmlItemBase last created element node
     */
    public static function Init(HtmlItemBase $n, $data, $visitor = null)
    {
        if (!is_array($data) && is_string($data)) {
            $data = [$data];
        }
        $visitor = $visitor ?? new static($n);
        $v_chain_info = (object)["parent" => null, "n" => $n, "next" => null, 'fromkey' => null];
        $list = [['q' => $data, 'keys' => null, 'n' => $n, 'v_chain_info' => $v_chain_info]];
        $_last = $n;
        $_is_php8 = version_compare(PHP_VERSION, "8.0", "<");
        $context_container = [];
        $tcounter = 0;
        while (count($list) > 0) {
            extract(array_shift($list), EXTR_OVERWRITE);
            $keys = is_null($keys) ? self::_GetKeys($q) : $keys;
            $next = false;
            if ($keys) {
                // +  enqueue builder parent 
                igk_html_push_node_parent($n);
                if ($n instanceof IHtmlContextContainer) {
                    if (!$context_container || ($context_container[0] !== $n)) {
                        array_unshift($context_container, $n);
                        HtmlLoadingContext::PushContext($context_container[0]->getContext());
                    }
                }
                self::_Loop($visitor, $n, $q, $keys, $next, $list, $v_chain_info, $_last, $_is_php8, $context_container);
            }
            $tcounter++;

            if ($next) {
                continue;
            }
            // + | dequeue builder parent 
            self::_RemoveNode($n, $context_container);
            // igk_html_pop_node_parent();
            // if ($context_container && ($n instanceof IHtmlContextContainer)) {
            //     if ($context_container[0] === $n) {
            //         HtmlLoadingContext::PopContext();
            //         array_shift($context_container);
            //     }
            // }
        }

        if ($context_container) {
            if ((count($context_container) == 1) && ($context_container[0] === $visitor->t)) {
                self::_RemoveNode($visitor->t, $context_container);
            } else {
                igk_dev_wln_e("context_container not empty ... not allowed ... fix that", count($context_container));
            }
        }
        //clean chain info
        while ($v_chain_info && ($_p = $v_chain_info->parent)) {
            $_p->next->parent = null;
            $_p->next = null;
            $v_chain_info = $_p;
        }
        return $_last;
    }
    private static function _RemoveNode($n, &$context_container)
    {
        igk_html_pop_node_parent();
        if ($context_container && ($n instanceof IHtmlContextContainer)) {
            if ($context_container[0] === $n) {
                HtmlLoadingContext::PopContext();
                array_shift($context_container);
            }
        }
    }

    /**
     * load - 
     */
    private static function _Loop($visitor, HtmlItemBase &$n, $q, &$keys, &$next, &$list, &$v_chain_info, &$_last, $_is_php8, &$context_container)
    {

        $tpnode = null;/* will store top node */
        while (!$next && (count($keys) > 0)) {
            $k = array_shift($keys);
            $v = $q[$k];
            $v_from_key = !is_numeric($k);
            // + | check for boolean value : if false continue
            if (is_bool($v)) {
                if (!$v) {
                    continue;
                }
                $v = "";
            }
            if ($v instanceof HtmlItemBase) {
                if ($v_from_key) {
                    $v = $visitor->setup($k, [$v]);
                }
                $n->add($v);
                continue;
            }

            if (!$v_from_key) {
                if (is_string($v)) {
                    if (strpos($v, self::KEY_CUSTOM_TARGET_PREFIX) === 0) {
                        $k = trim(substr($v, 4));
                        $v = null;
                    } else {
                        // condiser as a content litteral for the last inserted item 
                        if ($_last) {
                            $cn = $v_chain_info->n;
                            if ($cn) {
                                $cn->text($v);
                            } else {
                                $n->Content = $v;
                            }
                            continue;
                        }
                    }
                } else {
                    $k = 'div';
                }
            } else {
                if ((strpos($k, self::KEY_CUSTOM_TARGET_PREFIX) === 0)) {
                    $k = trim(substr($k, 4));
                    if ($_last) {
                        $n = $_last->getParentNode();
                        if ($_last) {
                            self::_RemoveNode($_last, $context_container);
                            $_last->remove();
                            $_last = null;
                        }
                    }
                } else if (strpos($k, self::KEY_INVOKE_ON_LAST) === 0) {
                    // :: invoke the function on the last create item. 
                    $k = trim(substr($k, 2));
                    $target_fc = $_last;
                    if ($k[0] == '@') {
                        $k = ltrim($k, '@');
                        if ($_last) {
                            $target_fc = $_last->getParentNode();
                            $_last->remove();
                            $_last = $n;
                        }
                    }
                    // invoke function methods in last inserted item
                    if ($target_fc) {
                        call_user_func_array([$target_fc, $k], is_array($v) ? $v : [$v]);
                        continue;
                    }
                } else if (strpos($k, self::KEY_NODE_PROPERTY_PREFIX) === 0) {
                    $tag = substr($k, strlen(self::KEY_NODE_PROPERTY_PREFIX));
                    if ($v_tn = $n->$tag) {
                        if ($v_tn instanceof HtmlItemBase) {
                            //create a new chain
                            //before passing get retrieve "_" to update attributes
                            if ($attribs = igk_getv($v, self::KEY_ATTRIBS)) {
                                $v_tn->setAttributes($attribs);
                                unset($v[self::KEY_ATTRIBS]);
                            }

                            $v_new_chain_info = (object)['next' => null, "n" => $v_tn, 'parent' => $v_chain_info, 'formkey' => $v_from_key];
                            array_unshift($list, ['q' => $q, 'keys' => $keys, 'n' => $n, 'v_chain_info' => $v_chain_info]);
                            array_unshift($list, ['q' => $v, 'keys' => null, 'n' => $v_tn,  'v_chain_info' => $v_new_chain_info]);
                            $_last = $n;
                            $next = true;
                            continue;
                        } else {
                            igk_die(sprintf('binding node %s is not a node', $k));
                        }
                    }
                    continue;
                }
            }

            $args = [];
            $attribs = [];
            $conds = null;
            $fn_c = null;
            $activate = null;

            if (is_array($v) && (count($v) > 0)) {
                if (key_exists($v_key = self::KEY_INVOKE_FUNC, $v)) {
                    $fn_call_intarget = $v[$v_key];
                    unset($v[$v_key]);
                    if ($_last) {
                        $v_fc_call = null;
                        $args = null;
                        if ($fn_call_intarget instanceof Closure) {
                            $v_fc_call = $fn_call_intarget;
                            $args = [$v_chain_info->n];
                        } else {
                            if (is_string($fn_call_intarget))
                                $fn_call_intarget = [$fn_call_intarget];
                            $method = $args = null;
                            $method = igk_getv($fn_call_intarget, 0) ?? igk_die('missing method name');
                            !is_string($method) && igk_die('method key provided not valid');
                            $args = igk_getv($fn_call_intarget, 1, []);
                            $v_fc_call = [$_last, $method];
                        }
                        call_user_func_array($v_fc_call, $args);
                        if (!empty($v)) {
                            // passing the rest to object 
                            array_unshift($list, ['q' => $v, 'keys' => null, 'n' => $_last]);
                            $next = true;
                        }
                        continue;
                    }
                }
                if (key_exists('_', $v)) {
                    $attribs = $v['_'];
                    unset($v['_']);
                }
                if (key_exists('@', $v)) {
                    $args = $v['@'];
                    if (!is_array($args)) {
                        $args = [$args];
                    }
                    unset($v['@']);
                }
                if (key_exists($v_ck = self::KEY_CONDITION, $v)) {
                    $conds = $v[$v_ck];
                    if (!is_array($args)) {
                        $conds = [$args];
                    }
                    unset($v[$v_ck]);
                }
                if (key_exists($v_ck = self::KEY_CALLBACK_HOST, $v)) {
                    $fn_c = $v[$v_ck];
                    unset($v[$v_ck]);
                }
                if (key_exists($v_ck = self::KEY_ATTRIBS_ACTIVATION, $v)) {
                    $activate = $v[$v_ck];

                    unset($v[$v_ck]);
                }
            }
            // + | phhp 7.3 not allow key to be upkaced
            if ($_is_php8) {
                $args = array_values($args ?? []);
            } else {
                $args = $args ?? [];
            }

            $tpnode = $n;
            list($tagname, $id, $class, $iargs, $v_name, $iattr) = $visitor->explodeTag($k, $n); //HtmlUtils::ExplodeTag($k);
            if ($tpnode === $n) {
                $tpnode = null;
            }
            if (!is_null($iargs)) {
                if (empty($args)) {
                    $args = $iargs;
                }
            }
 
            // special case handler
            $c = $n->$tagname(...$args);

            if (($c instanceof HtmlItemBase) && ($n !== $c)) {
                // + | for new created items .
                if ($id) {
                    $c['id'] = $id;
                }
                if ($class) {
                    $c['class'] = $class;
                    HtmlUtils::UpdateCoreAttribute($class, $attribs);
                }
                if ($v_name) {
                    $c["name"] = $v_name;
                }
                if ($iattr) {
                    $c->setAttributes($iattr);
                }
                if ($attribs) {
                    $c->setAttributes($attribs);
                }
            } else {
                continue;
            }
            //add($k, $attribs, $args);
            if (!is_null($conds)) {
                $c["*if"] = $conds;
            }
            if (!is_null($activate)) {
                $visitor->activateAttribute($c, $activate);
            }
            if ($fn_c instanceof Closure) {
                $c->host($fn_c);
            }
            $_last = $c;
            $v_new_chain_info = (object)['next' => null, "n" => $c, 'parent' => $v_chain_info, 'formkey' => $v_from_key];


            if (!$v) {
                continue;
            }
            if ($v instanceof HtmlNode) {
                $c->add($v);
                continue;
            }
            if (is_callable($v) && !is_string($v)) {
                // + bind node detected 
                $_c = $c;
                if ($_p = $v_new_chain_info->parent) {
                    if (!$v_from_key) {
                        $_c = $_p->n;
                        $v_new_chain_info->n->remove();
                        $v_chain_info = $_p;
                    }
                }
                $_c->host($v);
            } else if ($v && (is_array($v) || is_object($v))) {
                if (is_object($v)) {
                    // get array
                    if ($v instanceof IToArray) {
                        $v = $v->to_array();
                    } else {
                        $v = (array)$v;
                    }
                }
                if ($tpnode) {
                    $n = $tpnode;
                }
                // walk thru 
                array_unshift($list, ['q' => $q, 'keys' => $keys, 'n' => $n, 'v_chain_info' => $v_chain_info]);
                array_unshift($list, ['q' => $v, 'keys' => null, 'n' => $c,  'v_chain_info' => $v_new_chain_info]);
                $next = true;
                continue;
            } else {
                $c->Content = $v;
            }
        }
        if ($tpnode) {
            $n = $tpnode;
        }
    }

    /**
     * activate attributes
     * @param mixed $node 
     * @param mixed $attribute 
     * @return void 
     */
    public function activateAttribute($node, $attribute)
    {
        if (is_string($attribute)) {
            $attribute = explode(',', $attribute);
        }
        foreach ($attribute as $k) {
            $node->activate(trim($k));
        }
    }
}
