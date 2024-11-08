<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeBuilder.php
// @date: 20230311 06:46:24
namespace IGK\System\Html;

use Closure;
use Error;
use Exception; 
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
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html
 */
class HtmlNodeBuilder implements IHtmlNodeBuilderVisitor
{
    private $m_context_tab = [];
    private $m_template = [];
    var $fallbackTagName = 'div';
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

    const KEY_CALLBACK_HOST = 'fn()';
    const KEY_INVOKE_ON_LAST = '::';
    const KEY_INVOKE_ON_PARENT_LAST = '::@';
    /**
     * should be use with string method name, ['::fn()'=>function()]
     */
    const KEY_INVOKE_FUNC = '::fn()';

    const TAG_KEY = ':tag';
    /**
     * tag exploder
     */
    protected $explode;

    public function pushContext($new_context) { 
        $l_context = $this->m_context;
        if ($l_context){
            array_push($this->m_context_tab , $l_context);
        }
        $this->m_context = $new_context;
        return $l_context;
    }

    public function popContext() { 
        $l_context = $this->m_context;
        $this->m_context = array_pop($this->m_context_tab);
        return $l_context;
    }

    /**
     * set context object 
     */
    public function setContext(?object $context){
        $this->m_context = $context;
    }
    /**
     * 
     */
    public function getContext(): ?object{
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
    public function isInTemplateDefinition(){
        return count($this->m_template)>0;
    }
    /**
     * create an html builder 
     * @param HtmlItemBase $node core node
     * @return void 
     */
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
    public function build($data, ?HtmlItemBase $target = null, ?IHtmlNodeBuilderVisitor $visitor=null,  $context=null)
    {
        $this->m_context = $context ?? $this->m_context;
        if ($visitor){
            $visitor->setContext($context);
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
     * @param ?object $context context defintion 
     * @param null|HtmlItemBase $target 
     * @return HtmlItemBase 
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
            if ($v_context instanceof IHtmlNodeBuilderVisitor){
                $v_visitor = $v_context; 
                $v_context = $v_num_args >= 4 ? func_get_arg(2) : null;
            }

        }
        return $this->build($data, $target, $v_visitor,  $v_context);
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
        return $this->explode->explode($tag, $node, $context ?? $this->getContext());
    }

    /**
     * parse item to node builder presentations
     * @param HtmlItemBase $n 
     * @return string 
     */
    public static function Generate(HtmlItemBase $n, bool $ignore_empty_string = true):string
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
        $context_container = [];  //current context container
        $tcounter = 0;
        $_fallbackTagName = ($visitor instanceof static ? $visitor->fallbackTagName : null) ?? 'div';
        $b_counter = HtmlLoadingContext::CountCountext(); 
        while (count($list) > 0) {
            // $v_inner = HtmlLoadingContext::CountCountext(); // to remove 
            extract(array_shift($list), EXTR_OVERWRITE);
            // + | when start $keys is null. empty for reach to end section end
            $keys = is_null($keys) ? self::_GetKeys($q) : $keys;
            $next = false;
            if ($keys) {
                // +  enqueue builder parent 
                // igk_html_push_node_parent($n);  
                if ($n instanceof IHtmlContextContainer) {
                    if (!$context_container || ($context_container[0] !== $n)) {
                        array_unshift($context_container, $n); 
                    }
                }
                self::_Loop($visitor, $n, $q, $keys, $next, $list, $v_chain_info, $_last, $_is_php8, $context_container, $_fallbackTagName);
            }
            $tcounter++;
            // $v_inner = HtmlLoadingContext::CountCountext(); // to remove 
            if ($next) { 
                //+ | move to next list item to stop 
                continue;
            }
            // + | dequeue builder parent 
            // if (get_class($n) == VueComponent::class){
            //     Logger::warn("before view : ".$v_inner);
            // }
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
        // igk_html_pop_node_parent();
        if ($context_container && ($n instanceof IHtmlContextContainer)) {
            if ($context_container[0] === $n) {
                // HtmlLoadingContext::PopContext();
                array_shift($context_container);
            }
        }
    }
    private static function _DetectDefinition(HtmlItemBase & $n, array & $v , & $k, 
        & $attribs, 
        & $args,
        & $conds,
        & $activate,
        & $fn_c)
    {
        $v_keys = array_keys($v); 
        if (key_exists(self::KEY_ATTRIBS, $v)){ 
            $attribs = $v[self::KEY_ATTRIBS];
            unset($v[self::KEY_ATTRIBS]);
        }
        if (key_exists(self::TAG_KEY, $v)){
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

        if (key_exists($v_ck = self::KEY_CONDITION, $v)) {// must bind condition
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

    private static function _HandleDefinition(HtmlItemBase & $n, array & $v, & $k){
        $attribs = $args = $fn_c= $attribs= $activate= $conds= null;
        $tag = null;
        self::_DetectDefinition($n, $v, $tag, $attribs, $args,$conds,$activate, $fn_c);
        if ($tag){
            // create a new tag definition
            $n = call_user_func_array([$n, $tag], $args ?? []) ?? igk_die(sprintf('failed to create a tag node [%s]', $tag));
           // $k = $tag;
        }

        if ($attribs ){
            if (is_callable($attribs)){
                $attribs = $attribs($n,$k);
            }
            if (is_array($attribs))
            $n->setAttributes($attribs);
        }
    }

    private static function _BindArray($visitor, HtmlItemBase &$n, array $v){
        $s = null;
        self::_HandleDefinition($n, $v, $s);
        if (!empty($v)){ 
            if ($s){
                $v[] = [self::TAG_KEY=>$s];
            }
            $v_pg = new static($n);
            //+| passing context to the new created item 
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
        // $v_chain_info = null;
        while (!$next && (count($keys) > 0)) {
            $k = array_shift($keys);
            $v = $q[$k];
            $v_from_key = !is_numeric($k);
            $v_invoke_func  = false;


            // if ($v instanceof IHtmlNodeEvaluableExpression){
            //     $v = $v->evaluate((array)$visitor->getContext());
            // }

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
                // igk_wln_e("handle");
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
                        // condiser as a content litteral for the last inserted item 
                        if ($_last && $v_chain_info) {
                            $cn = $v_chain_info->n;
                            if ($cn) {
                                $cn->text($v);
                                continue;
                            }
                        }
                        $n->text($v);
                        continue;
                    }
                } else {
                    $k  = $fallbackTagName; //  $this->fallbackTagName; // falback node 
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
                    // :: invoke the function on the last create item. 
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
                        // invoke function methods in last inserted item
                        if ($target_fc) {
                            call_user_func_array([$target_fc, $k], is_array($v) ? $v : [$v]);
                            //$target_fc->setIsVisible(false);
                            //igk_wln_e("call....".$k, $v, $target_fc->getIsVisible());
                            continue;
                        }
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

            if ($v_invoke_func){
                $fn_call_intarget = $v;
                if ($_last){
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
                        // $v_fc_call = null;
                        // $args = null;
                        // if ($fn_call_intarget instanceof Closure) {
                        //     $v_fc_call = $fn_call_intarget;
                        //     $args = [$v_chain_info->n];
                        // } else {
                        //     if (is_string($fn_call_intarget))
                        //         $fn_call_intarget = [$fn_call_intarget];
                        //     $method = $args = null;
                        //     $method = igk_getv($fn_call_intarget, 0) ?? igk_die('missing method name');
                        //     !is_string($method) && igk_die('method key provided not valid');
                        //     $args = igk_getv($fn_call_intarget, 1, []);
                        //     $v_fc_call = [$_last, $method];
                        // }
                        // call_user_func_array($v_fc_call, $args);
                        // if (!empty($v)) {
                        //     // passing the rest to object 
                        //     array_unshift($list, ['q' => $v, 'keys' => null, 'n' => $_last]);
                        //     $next = true;
                        // }
                        continue;
                    }
                }
                self::_DetectDefinition($n, $v, $k, $attribs, $args,$conds,$activate,$fn_c); 
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
                if ($result = $v($n)){
                    if (is_array($result)){
                        self::_BindArray($visitor, $n, $result);
                    }
                }
                continue;
            }


            $tpnode = $n;
            list($tagname, $id, $class, $iargs, $v_name, $iattr) = $visitor->explodeTag($k, $n);
            if ($tpnode === $n) {
                $tpnode = null;
                // move to visited parent
                if ($v_new_chain_info) {
                    // $n = $n->getParentNode() ?? $n;                
                    $n = $v_new_chain_info->parent->n ?? $n;
                }
            }
            if (!is_null($iargs)) {
                if (empty($args)) {
                    $args = $iargs;
                }
            }

            // special case handler
            $c = $n->$tagname(...$args);

            $c && $visitor->onCreate($c);
            // + | evaluable expression
            if ($v instanceof IHtmlNodeEvaluableExpression){
                if ($visitor->isInTemplateDefinition()){
                    $v = $v->getValue();
                }else{
                    $v = $v->evaluate((array)$visitor->getContext());
                }
            }

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
                // + | same as childs 
                if (!$v) {
                    // continue list to childs list... parent...
                    continue;
                }
            }
            //add($k, $attribs, $args);
            if (!is_null($conds)) { 
                //+ | evaluate in global context
                if ($conds instanceof IHtmlNodeConditionEvaluableAttribute){
                    $conds = $conds->evaluate($visitor->getContext()); 
                } 
                if (!$conds){
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
            if ($tpnode) {
                $visitor->onClose($c);//popTemplateContext($c);
                $n = $tpnode;
            }
        }
    }
    /**
     * 
     * @param HtmlItemBase $node 
     * @return void 
     */
    private function _popTemplateContext(HtmlItemBase $node){
        if (count($this->m_template)>0){
            // if ($this->m_template[0]===$node){
            //     array_shift($this->m_template);
            // }
            array_shift($this->m_template);
        }
    }
    private static function _InvokeInLast($_last, $fn_call_intarget, &$list, $v_chain_info, &$next){
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
    private function _checkForTemplate($n){
        if ($n instanceof HtmlLooperNode){
            array_unshift($this->m_template, $n);
        }
    }
    /**
     * call on create node 
     */
    public function onCreate(HtmlItemBase $node){ 
        $this->_checkForTemplate($node); 
        // igk_debug_wln("create node: ".$node->getTagName());
    }
    /**
     * call on close node 
     * @param HtmlItemBase $node 
     * @return void 
     * @throws Error 
     */
    public function onClose(HtmlItemBase $node){
        // igk_debug_wln("close node :".$node->getTagName());
        $this->_popTemplateContext($node);
    }
}
