<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeBuilder.php
// @date: 20230311 06:46:24
namespace IGK\System\Html;
use Closure;
use Error;
use Exception;
use IGK\System\Core\EngineReadArgs;
use IGK\System\DataArgs;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlCommentNode;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlLooperNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\IToArray;
use IGKException;
use ReflectionClass;
use ReflectionException;

/**
 * 
 * @package IGK\System\Html
 */
/**
* auto generate doc.
* @package IGK\System\Html
*/
class HtmlNodeBuilder implements IHtmlNodeBuilderVisitor
{
    /**
    * Property: context tab.
    * @var mixed
    */
    private $m_context_tab = [];
    /**
    * Property: template.
    * @var mixed
    */
    private $m_template = [];
    /**
    * Name of fallback tag name.
    * @var mixed
    */
    var $fallbackTagName = 'div';
    /**
    * Constant: raw context field.
    * @var mixed
    */
    const RAW_CONTEXT_FIELD = 'raw';
    /**
     * class name user to handle data view args context 
     * @var ?string
     */
    var $contextDataArgsClass;
    /**
    * Property: t.
    * @var mixed
    */
    var $t;
    /**
     * building context
     */
    private $m_context;
    /**
     * preserve loading tag case
     * @var false
     */
    public $preserveTagCase = false;
    /**
    * Constant: key condition.
    * @var mixed
    */
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
     * attribute activation key ["_@"]=[]
     */
    const KEY_ATTRIBS_ACTIVATION = '_@';
    /**
    * Constant: key callback host.
    * @var mixed
    */
    const KEY_CALLBACK_HOST = 'fn()';
    /**
    * Constant: key invoke on last.
    * @var mixed
    */
    const KEY_INVOKE_ON_LAST = '::';
    /**
    * Constant: key invoke on parent last.
    * @var mixed
    */
    const KEY_INVOKE_ON_PARENT_LAST = '::@';
    /**
     * should be use with string method name, ['::fn()'=>function()]
     */
    const KEY_INVOKE_FUNC = '::fn()';
    /**
    * Constant: tag key.
    * @var mixed
    */
    const TAG_KEY = ':tag';
    /**
     * tag exploder
     */
    protected $explode;
    /**
    * Pushes Context.
    * @param mixed $new_context
    */
    public function pushContext($new_context)
    {
        $l_context = $this->m_context;
        if ($l_context) {
            array_push($this->m_context_tab, $l_context);
        }
        $this->m_context = $new_context;
        return $l_context;
    }
    /**
    * Pops Context.
    */
    public function popContext()
    {
        $l_context = $this->m_context;
        $this->m_context = array_pop($this->m_context_tab);
        return $l_context;
    }
    /**
     * set context object 
     */
    public function setContext(?object $context)
    {
        $this->m_context = $context;
    }
    /**
    * auto generate doc.
    */
    public function getContext(): ?object
    {
        return $this->m_context;
    }
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
    /**
    * Returns true if In Template Definition.
    */
    public function isInTemplateDefinition()
    {
        return count($this->m_template) > 0;
    }
    /**
     * create an html builder 
     * @param HtmlItemBase $node core node
     * @return void 
     */
    public function __construct(?HtmlItemBase $node = null)
    {
        $this->t = $node ?? igk_create_notagnode();
        $this->explode = new HtmlNodeTagExplosionDefinition($this);
    }
    /**
     * build node an return the last rendered element 
     * @param mixed $data 
     * @param null|HtmlItemBase $target 
     * @param null|HtmlItemBase $target 
     * @param ?object $context 
     * @return HtmlItemBase 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function build($data, ?HtmlItemBase $target = null, ?IHtmlNodeBuilderVisitor $visitor = null,  $context = null)
    {
        $this->m_context = $context ?? $this->m_context;
        if ($visitor) {
            $visitor->setContext($context);
        }
        if (!($this->m_context instanceof DataArgs)){
            if (($raw = igk_getv($this->m_context, $k = self::RAW_CONTEXT_FIELD)) && !($raw instanceof DataArgs)) {
                igk_setv($this->m_context, $k, new DataArgs($raw));
            }
        }
        return self::Init($target ?? $this->t, $data, $visitor ?? $this);
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
     * build node definition 
     * @param array|string $def_or_tag_expression 
     * @param array|HtmlItemBase|null $target if def_or_tag_expression is string target must be an array
     * @param ?object $context context defintion 
     * @return HtmlItemBase last create node
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function __invoke($def_or_tag_expression)
    {
        $v_context = null;
        $v_visitor = null;
        $v_num_args = func_num_args();
        if (is_string($def_or_tag_expression)) {
            $data = func_get_arg(1);
            if (!is_array($data)) {
                igk_die("arg must be an array");
            }
            $data = [$def_or_tag_expression => $data];
            $target = func_num_args() == 3 ? func_get_arg(1) : null;
        } else {
            $data = $def_or_tag_expression;
            $target = $v_num_args >= 2 ? func_get_arg(1) : null;
            $v_context = $v_num_args >= 3 ? func_get_arg(2) : null;
            if ($v_context instanceof IHtmlNodeBuilderVisitor) {
                $v_visitor = $v_context;
                $v_context = $v_num_args >= 4 ? func_get_arg(2) : null;
            } else if (is_array($v_context)) {
                if (!isset($v_context[$v_raw = self::RAW_CONTEXT_FIELD])){
                    $v_context = [$v_raw=>$v_context];
                }
                $v_context = (object)$v_context;
            }
        }
        if (($cl = $this->contextDataArgsClass) && !($v_context instanceof DataArgs) &&
            (($cl == DataArgs::class) ||
                is_subclass_of($cl, DataArgs::class))
        ) {            
            $v_context->context = new $cl((array)$v_context);
        }
        return $this->build($data, $target, $v_visitor,  $v_context);
    }
    /**
    * auto generate doc.
    * @param mixed & $q
    * @return
    */
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
        return $this->explode->explode($tag, $node, $context ?? $this->getContext());
    }
    /**
     * parse item to node builder presentations
     * @param HtmlItemBase $n 
     * @return string 
     */
    public static function Generate(HtmlItemBase $n, bool $ignore_empty_string = true): string
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
     * @param ?object $visitor visitor to use
     * @return HtmlItemBase last created element node
     */
    public static function Init(HtmlItemBase $n, $data, ?IHtmlNodeBuilderVisitor $visitor = null)
    {
        if (!is_array($data) && is_string($data)) {
            $data = [$data];
        }
        $visitor = $visitor ?? new static($n);
        $v_chain_info = (object)["parent" => null, "n" => $n, "next" => null, 'fromkey' => null];
        $list = [['q' => $data, 'keys' => null, 'n' => $n, 'v_chain_info' => $v_chain_info]];
        $v_root = $n;
        $_last = $n;
        $_is_php8 = version_compare(PHP_VERSION, "8.0", ">=");
        $context_container = [];  
        $tcounter = 0;
        $_fallbackTagName = ($visitor instanceof static ? $visitor->fallbackTagName : null) ?? 'div';
        $b_counter = HtmlLoadingContext::CountCountext();
        while (count($list) > 0) {
            extract(array_shift($list), EXTR_OVERWRITE);
            // + | when start $keys is null. empty for reach to end section end
            $keys = is_null($keys) ? self::_GetKeys($q) : $keys;
            $next = false;
            if ($keys) {
                // +  enqueue builder parent 
                if ($n instanceof IHtmlContextContainer) {
                    if (!$context_container || ($context_container[0] !== $n)) {
                        array_unshift($context_container, $n);
                    }
                }
                self::_Loop($visitor, $n, $q, $keys, $next, $list, $v_chain_info, $_last, $_is_php8, $context_container, $_fallbackTagName);
            }
            $tcounter++;
            if ($next) {
                continue;
            }
            // + | dequeue builder parent 
            self::_RemoveNode($n, $context_container);
            $visitor->onClose($n);
        }
        $ref_count = HtmlLoadingContext::CountCountext();
        if ($b_counter != $ref_count) {
            igk_die("counter context not the same " . $b_counter . " vs " . $ref_count);
        }
        if ($context_container) {
            if ((count($context_container) == 1) && ($context_container[0] === $visitor->t)) {
                self::_RemoveNode($visitor->t, $context_container);
            } else {
                igk_dev_wln_e("context_container not empty ... not allowed ... fix that", count($context_container));
            }
        }
        while ($v_chain_info && ($_p = $v_chain_info->parent)) {
            $_p->next->parent = null;
            $_p->next = null;
            $v_chain_info = $_p;
        }
        return $_last;
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed & $context_container
    * @return
    */
    private static function _RemoveNode($n, &$context_container)
    {
        if ($context_container && ($n instanceof IHtmlContextContainer)) {
            if ($context_container[0] === $n) {
                array_shift($context_container);
            }
        }
    }
    /**
    * auto generate doc.
    * @param HtmlItemBase & $n
    * @param array & $v
    * @param mixed & $k
    * @param mixed & $attribs
    * @param mixed & $args
    * @param mixed & $conds
    * @param mixed & $activate
    * @param mixed & $fn_c
    * @return
    */
    private static function _DetectDefinition(
        HtmlItemBase &$n,
        array &$v,
        &$k,
        &$attribs,
        &$args,
        &$conds,
        &$activate,
        &$fn_c
    ) {
        if (key_exists(self::KEY_ATTRIBS, $v)) {
            $attribs = $v[self::KEY_ATTRIBS];
            unset($v[self::KEY_ATTRIBS]);
        }
        if (key_exists(self::TAG_KEY, $v)) {
            $k = $v[self::TAG_KEY];
            unset($v[self::TAG_KEY]);
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
    /**
    * auto generate doc.
    * @param HtmlItemBase & $n
    * @param array & $v
    * @param mixed & $k
    * @return
    */
    private static function _HandleDefinition(HtmlItemBase &$n, array &$v, &$k)
    {
        $attribs = $args = $fn_c = $attribs = $activate = $conds = null;
        $tag = null;
        self::_DetectDefinition($n, $v, $tag, $attribs, $args, $conds, $activate, $fn_c);
        if ($tag) {
            $n = call_user_func_array([$n, $tag], $args ?? []) ?? igk_die(sprintf('failed to create a tag node [%s]', $tag));
        }
        if ($attribs) {
            if (is_callable($attribs)) {
                $attribs = $attribs($n, $k);
            }
            if (is_array($attribs))
                $n->setAttributes($attribs);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $visitor
    * @param HtmlItemBase & $n
    * @param array $v
    * @return
    */
    private static function _BindArray($visitor, HtmlItemBase &$n, array $v)
    {
        $s = null;
        self::_HandleDefinition($n, $v, $s);
        if (!empty($v)) {
            if ($s) {
                $v[] = [self::TAG_KEY => $s];
            }
            $v_pg = new static($n);
            $v_pg->m_context = $visitor->getContext();
            $v_pg($v);
        }
    }
    /**
     * load - 
     */
    private static function _Loop($visitor, HtmlItemBase &$n, $q, &$keys, &$next, &$list, &$v_chain_info, &$_last, $_is_php8, &$context_container, string $fallbackTagName)
    {
        /**
         * @var mixed $v_chain_info 
         */
        $tpnode = null;/* will store top node */
        $v_new_chain_info = null;
        $glue = '';
        while (!$next && (count($keys) > 0)) {
            $k = array_shift($keys);
            $v = $q[$k];
            $v_from_key = !is_numeric($k);
            $v_invoke_func  = false;
            // + | array list item detection - append to current node 
            if (!$v_from_key && is_array($v) && $n) {
                self::_BindArray($visitor, $n, $v);
                continue;
            }
            // + | check for boolean value : if false continue
            if (is_bool($v)) {
                if (!$v) {
                    continue;
                }
                $v = "";
            }
            if ($v instanceof IHtmlResourceData) {
                $v = '' . $v;
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
                        if ($_last && $v_chain_info) {
                            $cn = $v_chain_info->n;
                            if ($cn) {
                                $cn->text($glue . $v);
                                $glue = ' ';
                                continue;
                            }
                        }
                        $n->text($v);
                        continue;
                    }
                } else {
                    $k  = $fallbackTagName; 
                }
            } else {
                if ((strpos($k, self::KEY_CUSTOM_TARGET_PREFIX) === 0)) {
                    $k = trim(substr($k, 4));
                    if ($_last && ($n !== $_last)) {
                        $n = $_last->getParentNode();
                        if ($_last) {
                            self::_RemoveNode($_last, $context_container);
                            self::_RemoveTarget($_last);
                            $_last = null;
                        }
                    }
                } else if (strpos($k, self::KEY_INVOKE_ON_LAST) === 0) {
                    if ($k == self::KEY_INVOKE_FUNC) {
                        $v_invoke_func = true;
                    } else {
                        $k = trim(substr($k, 2));
                        $target_fc = $_last;
                        if ($k[0] == '@') {
                            $k = ltrim($k, '@');
                            if ($_last) {
                                $target_fc = $_last->getParentNode();
                                self::_RemoveTarget($_last);
                                $_last = $n;
                            }
                        }
                        if ($target_fc) {
                            call_user_func_array([$target_fc, $k], is_array($v) ? $v : [$v]);
                            continue;
                        }
                    }
                } else if (strpos($k, self::KEY_NODE_PROPERTY_PREFIX) === 0) {
                    $tag = substr($k, strlen(self::KEY_NODE_PROPERTY_PREFIX));
                    if ($v_tn = $n->$tag) {
                        if ($v_tn instanceof HtmlItemBase) {
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
            if ($v_invoke_func) {
                $fn_call_intarget = $v;
                if ($_last) {
                    self::_InvokeInLast($_last, $fn_call_intarget, $list, $v_chain_info, $next);
                }
                continue;
            }
            if (is_array($v) && (count($v) > 0)) {
                if (key_exists($v_key = self::KEY_INVOKE_FUNC, $v)) {
                    $fn_call_intarget = $v[$v_key];
                    unset($v[$v_key]);
                    if ($_last) {
                        self::_InvokeInLast($_last, $fn_call_intarget, $list, $v_chain_info, $next);
                        continue;
                    }
                }
                self::_DetectDefinition($n, $v, $k, $attribs, $args, $conds, $activate, $fn_c);
            }
            // + | phhp 7.3 not allow key to be upkaced
            if ($_is_php8) {
                $args = array_values($args ?? []);
            } else {
                $args = $args ?? [];
            }
            if (!$v_from_key && ($v instanceof Closure)) {
                if (!$n) {
                    igk_die('missing target node');
                }
                if ($result = $v($n)) {
                    if (is_array($result)) {
                        self::_BindArray($visitor, $n, $result);
                    }
                }
                continue;
            }
            $tpnode = $n; $glue = '';
            list($tagname, $id, $class, $iargs, $v_name, $iattr) = $visitor->explodeTag($k, $n);
            if ($tpnode === $n) {
                $tpnode = null;
                if ($v_new_chain_info) {
                    $n = $v_new_chain_info->parent->n ?? $n;
                }
            }
            if (!is_null($iargs)) {
                if (empty($args)) {
                    $args = $iargs;
                }
            }
            // + | special case handler for php7 
            $tlist = [];
            \IGK\Helper\ArrayUtils::UnpackArrayKeys($args, $tlist);
            $c = $n->$tagname(...$args);
            if ($tlist) {
                self::_BindPackArgs($c, $tlist);
            }
            $c && $visitor->onCreate($c);
            // + | evaluable expression
            if ($v instanceof IHtmlNodeEvaluableExpression) {
                $v_context = (array)$visitor->getContext(); 
                if ($visitor->isInTemplateDefinition()) {
                    if (($v = $v->getValue()) && $v_context)
                        $v = EngineReadArgs::TreatGlobalArgs($v, $v_context);
                } else {
                    $v = $v->evaluate($v_context);
                }
            }
            if (($c instanceof HtmlItemBase) && ($n !== $c)) {
                // + | for new created items .
                if ($id) {
                    $c['id'] = $id;
                }
                if ($class) {
                    $c['class'] = $class;
                    if (is_string($class)) {
                        HtmlUtils::UpdateCoreAttribute($class, $attribs);
                    }
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
                // + | same as childs 
                if (!$v) {
                    continue;
                }
            }
            if (!is_null($conds)) {
                if ($conds instanceof IHtmlNodeConditionEvaluableAttribute) {
                    $conds = $conds->evaluate($visitor->getContext());
                }
                if (!$conds) {
                    $c->remove();
                    continue;
                }
            }
            if (!is_null($activate)) {
                $visitor->activateAttribute($c, $activate);
            }
            if ($fn_c instanceof Closure) {
                $c->host($fn_c);
            }
            $_last = $c;
            $v_new_chain_info = (object)['next' => null, "n" => $c, 'parent' => $v_chain_info, 'formkey' => $v_from_key, 'root' => $tpnode];
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
                        self::_RemoveTarget($v_new_chain_info->n);
                        $v_chain_info = $_p;
                    }
                }
                $_c->host($v);
            } else if ($v && (is_array($v) || is_object($v))) {
                if (is_object($v)) {
                    if ($v instanceof IToArray) {
                        $v = $v->to_array();
                    } else {
                        $v = (array)$v;
                    }
                }
                if ($tpnode) {
                    $n = $tpnode;
                }
                array_unshift($list, ['q' => $q, 'keys' => $keys, 'n' => $n, 'v_chain_info' => $v_chain_info]);
                array_unshift($list, ['q' => $v, 'keys' => null, 'n' => $c,  'v_chain_info' => $v_new_chain_info]);
                $next = true;
                continue;
            } else {
                $c->Content = $v;
            }
            if ($tpnode) {
                $visitor->onClose($c); 
                $n = $tpnode;
            }
        }
    }
    /**
     * bind pack argument 
     * @param mixed $node 
     * @param mixed $list 
     * @return void 
     */
    private static function _BindPackArgs($node, $list)
    {
        $fc_className = function ($node, $v, $k) {
            $node->setClass($v);
        };
        $handler = ['class' => $fc_className, 'content' => function ($node, $v, $k) {
            $node->content = $v;
        }];
        foreach ($list as $k => $v) {
            if ($h = igk_getv($handler, $k)) {
                $h($node, $v, $k);
            }
        }
    }
    /**
    * auto generate doc.
    * @param HtmlItemBase $node
    * @return void
    */
    private function _popTemplateContext(HtmlItemBase $node)
    {
        if (count($this->m_template) > 0) {
            array_shift($this->m_template);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $_last
    * @param mixed $fn_call_intarget
    * @param mixed & $list
    * @param mixed $v_chain_info
    * @param mixed & $next
    * @return
    */
    private static function _InvokeInLast($_last, $fn_call_intarget, &$list, $v_chain_info, &$next)
    {
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
            array_unshift($list, ['q' => $v, 'keys' => null, 'n' => $_last]);
            $next = true;
        }
    }
    /**
     * try remove target
     * @param mixed $_t 
     * @return void 
     */
    private static function  _RemoveTarget($_t)
    {
        $_t->remove();
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
    /**
     * run builder generator on target node
     * @param HtmlItemBase $node 
     * @param array $definition 
     * @return mixed 
     */
    public static function RunBuild(HtmlItemBase $node, array $definition)
    {
        $s = new static($node);
        return $s($definition);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return
    */
    private function _checkForTemplate($n)
    {
        if ($n instanceof HtmlLooperNode) {
            array_unshift($this->m_template, $n);
        }
    }
    /**
     * call on create node 
     */
    public function onCreate(HtmlItemBase $node)
    {
        $this->_checkForTemplate($node);
    }
    /**
     * call on close node 
     * @param HtmlItemBase $node 
     * @return void 
     * @throws Error 
     */
    public function onClose(HtmlItemBase $node)
    {
        $this->_popTemplateContext($node);
    }
}