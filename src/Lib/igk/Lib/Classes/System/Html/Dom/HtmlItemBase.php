<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlItemBase.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

require_once IGK_LIB_CLASSES_DIR . "/IGKObject.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Html/Dom/Factory.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Html/Dom/DomNodeBase.php";

use ArrayAccess;
use Closure;
use Error;
use Exception;
use IGK\Helper\IO;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\DomNodeBase;
use IGK\System\Html\HtmlAttributeArray;
use IGK\System\Html\HtmlChildArray;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlInitNodeInfo;
use IGK\System\Html\HtmlLoadingContext;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\Html\HtmlNodeTagExplosionDefinition;
use IGK\System\Html\HtmlNodeType;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\IHtmlContextContainer;
use IGK\System\Html\XML\XmlNode;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\XML\XMLNodeType;
use IGKException;
use IGKHtmlDoc;
use ReflectionException;

/**
 * abstract html item base
 */
abstract class HtmlItemBase extends DomNodeBase implements ArrayAccess
{
    use  ArrayAccessSelfTrait;
    // static $BasicMethod = array(
    //     "AcceptRender"=>"_acceptRender",
    //     "RenderComplete"=>"__RenderComplete"
    // );
    const EVENTS = 0xa1;
    const ACTIVATE = 1;
    const ATTRIBS = 2;
    const ITERATOR = 3;
    const OWNER = 4;
    const CALLBACK_SUFFIX = "Params";
    const FLAG_INIT = IGK_NODETYPE_FLAG;
    const PREFILTER_ATTRIBUTE = 5;
    /**
     * property flag container
     * @var array
     */
    private $_f = [];

    /**
     * 
     * @var mixed
     */
    protected $tagname;
    /**
     * override if can load Content
     * @var ?bool
     */
    protected $canLoadContent;

    public function setPrefilterAttribute(?IHtmlPrefilterAttribute $attribFilter){
        $this->setParam(self::PREFILTER_ATTRIBUTE, $attribFilter);
        return $this;
    }
    public function getPrefilterAttribute(): ?IHtmlPrefilterAttribute{
        return $this->getParam(self::PREFILTER_ATTRIBUTE);
    }

    /**
     * current node content
     * @var mixed
     */
    protected $content = null;

    /**
     * 
     * @var HtmlChildArray
     */
    protected $m_childs;

    /**
     * 
     * @var HtmlAttributeArray
     */
    protected $m_attributes;

    protected $m_callexclude = [];

    /**
     * set text content
     * @param null|string $content 
     * @return $this 
     */
    public function setTextContent(?string $content){
        $this->content = $content;
        return $this;
    }
    protected function setInitNodeTypeInfo(HtmlInitNodeInfo $info)
    {
        $this->setFlag(self::FLAG_INIT, $info);
        return $this;
    }
    public function getInitNodeTypeInfo(): ?HtmlInitNodeInfo
    {
        return $this->getFlag(self::FLAG_INIT);
    }
    /**
     * node parent
     * @var HtmlItemBase node parent
     */
    protected $m_parent;

    protected static $sm_macros;

    /**
     * return 
     * @return string node type  
     */
    public function getType()
    {
        return HtmlNodeType::Node;
    }

    public function Dispose()
    {
        $this->remove();
        parent::Dispose();
    }
    ///<summary></summary>
    ///<param name="host"></param>
    /**
     * 
     * @param mixed $host
     */
    public function setParentHost($host)
    {
        if (($host == null) || igk_reflection_class_extends($host, __CLASS__)) {
            $this->setFlag(IGK_PARENTHOST_FLAG, $host);
        }
    }
    /**
     * register macros for context
     * @param string $name 
     * @param string $class_name 
     * @return true|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function RegisterMacros(string $name, string $class_name)
    {
        if (empty($name)) {
            throw new IGKException("name not defined");
        }
        if (empty($class_name)) {
            throw new IGKException("class name not defined");
        }
        if (self::$sm_macros == null) {
            self::$sm_macros = (object)[
                "context" => [], // "context and classe
                "funcs" => [], //list of function ,
                "preload" => 1,
                "context_funcs" => [], // list of contexted macros function 
            ];
        }
        if (!in_array($class_name, self::$sm_macros->context)) {
            self::$sm_macros->context[$name] = $class_name;
            if (class_exists($class_name, false)) {
                $tab = [];
                \IGK\System\ExtensionUtils::LoadMethods($tab, $class_name, self::class);
                self::$sm_macros->context_funcs[$name] = $tab;
                return true;
            } else {
                //class need to loaded
                if (is_array(self::$sm_macros->preload)) {
                    self::$sm_macros->preload[$name][] = $class_name;
                } else {
                    self::$sm_macros->preload = [$name => [$class_name]];
                }
            }
        }
    }
    protected static function _invokeMacros($name, $argument, &$response = null)
    {

        if (self::$sm_macros) {
            if (is_array(self::$sm_macros->preload)) {
                foreach (self::$sm_macros->preload as $context => $v) {
                    foreach ($v as $p) {
                        if (class_exists($p)) {
                            $tab = [];
                            \IGK\System\ExtensionUtils::LoadMethods($tab, $p, self::class);
                            self::$sm_macros->context_funcs[$context] = $tab;
                            // igk_wln_e("load :::: ".$p);
                        } else {
                            igk_die("failed to load class " . $p);
                        }
                    }
                }
                self::$sm_macros->preload = 0;
            }
            if ($g = HtmlLoadingContext::GetCurrentContext()) {
                if ($g && !(key_exists($g->name, self::$sm_macros->context))) {
                    return;
                }
                if ($tab = igk_getv(self::$sm_macros->context_funcs, $g->name)) {
                    if ($fc = igk_getv($tab, $name)) {
                        if (!is_callable($fc)){
                            if (is_array($fc)){
                                $argument += array_slice($fc, 2);
                                $fc = array_slice($fc, 0, 2);
                            } 
                            // igk_wln_e("not a callable : ", $g->name, $name, is_callable($fc),   $argument );
                        }
                        if (is_callable($fc) && ($fc = closure::fromCallable($fc))) {
                            // igk_wln_e("invoking....");
                            $response =  $fc(...$argument);
                            return true;
                        }
                    }
                } 
            } else {
                return;
            }
            if (isset(self::$sm_macros->funcs[$name])) {
                if ($fc = closure::fromCallable(self::$sm_macros->funcs[$name])) {
                    $response =  $fc(...$argument);
                    return true;
                }
            }
        }
    }

    public function __construct()
    {
        $this->m_attributes = $this->createAttributeArray();
        $this->m_childs = $this->getCanAddChilds() ?  new HtmlChildArray() : null;
    }
    protected function createAttributeArray()
    {
        $attrib = new HtmlAttributeArray();
        if (method_exists($this, '_isAllowedAttribute')) {
            $attrib->add_listener = function ($attrib) {
                return $this->_isAllowedAttribute($attrib);
            };
        }
        return $attrib;
    }
    ///<summary>get if this tag is consider as an empty tag</summary>
    /**
     * get if this tag is consider as an empty tag
     * @return bool
     */
    public function isEmptyTag() : bool
    {
        if (!empty($n =$this->getTagName()))
            return isset(HtmlOptions::$EmptyTag[strtolower($n)]);
        return true;
    }

    /**
     * get if this node contains children
     * @return bool 
     */
    public function getHasChilds():bool
    {
        return igk_count($this->m_childs) > 0;
    }
    /**
     * get if this node is empty
     * @return bool 
     */
    public function isEmpty():bool{
        return !$this->getHasChilds() && empty(trim($this->getContent() ?? ''));
    }

    ///<summary></summary>
    ///<param name="className"></param>
    ///<param name="context"></param>
    /**
     * 
     * @param mixed $className
     * @param mixed $context
     */
    function startLoading($className, $context)
    {
        if (!igk_reflection_class_extends(get_called_class(), __CLASS__)) {
            igk_die("not allowed " . __METHOD__);
        }
        $this->setFlag(IGK_LOADINGCONTEXT_FLAG, $context);
        $this->setFlag(IGK_ISLOADING_FLAG, 1);
    }
    ///<summary>update: get visible</summary>
    ///<return>true if flag not set. if callable evaluate callable if 1 visibile</return>
    /**
     * update: get visible
     */
    public function getIsVisible()
    {
        $v = igk_getv($this->_f, IGK_ISVISIBLE_FLAG);
        if (($v != null) && igk_is_callable($v)) {
            $g = igk_invoke_callback_obj($this, $v);
            return $g;
        }
        if ($v === false)
            return 0;
        $s = 0;
        if (!$v || ($v === null))
            $s = 1;
        return $s;
    }

    ///<summary>set is visible and maintain chain</summary>
    /**
     * set is visible and maintain chain
     */
    public function setIsVisible($value)
    {
        $value = igk_getbool($value);
        if ($value === true) {
            $this->setFlag(IGK_ISVISIBLE_FLAG, null);
        } else {
            $this->setFlag(IGK_ISVISIBLE_FLAG, true);
        }
        return $this;
    }
    /**
     * @return array|\IIGKArrayObject return list of children
     */
    public function getChilds()
    {
        return $this->m_childs;
    }
    ///<summary> get if this node accept rendering. and initialeze it  </summary>
    /**
     *  get if this node accept rendering. and initialeze it
     */
    protected function _acceptRender($options = null):bool
    {
        $s = $this->getIsVisible();
        return $s;
    }
    /**
     * get array of rendering children
     * @param mixed $options 
     * @return array 
     */
    protected function _getRenderingChildren($options = null)
    {
        return $this->m_childs ?  $this->m_childs->to_array() : [];
    }
    public function getAttributes()
    {
        return $this->m_attributes;
    }
    /**
     * 
     * @param mixed $options 
     * @return array 
     */
    public function getRenderedChilds($options = null)
    {
        return $this->_getRenderingChildren($options);
    }

    ///<summary></summary>
    ///<param name="item"></param>
    /**
     * 
     * @param mixed $item
     */
    public function setAttributes(?array $item = null)
    {
        if (is_array($item)) {
            foreach ($item as $k => $v) {
                $this[$k] = $v; // HtmlUtils::GetValue($v);
            }
        }
        return $this;
    }
    ///<summary>set node attibute</summary>
    ///<param name="key">attribute name</param>
    ///<param name="value">attribute value</param>
    /**
     * 
     * @param mixed $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $this[$key] = $value;
        return $this;
    }
    /**
     * helper to get attribute
     * @param string $key 
     * @return mixed 
     */
    public function getAttribute(string $key){
        return $this[$key];
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getNodeType()
    {
        return XMLNodeType::ELEMENT;
    }
    ///<summary>html item get param implementation</summary>
    /**
     * html item get param implementation
     */
    public function getParam($key, $default = null)
    {
        $p = $this->getFlag(IGK_PARAMS_FLAG, array());
        $v_in = isset($p[$key]);
        $s = igk_getv($p, $key, $default);
        if (!$v_in && is_callable($default)) {
            $p[$key] = $default();
            $this->setFlag(IGK_PARAMS_FLAG, $p);
        }
        return $s;
    }
    /**
     * get inherited param
     * @param mixed $key 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public function getInheritedParam($key, $default=null){
        $rp = [$this];
        while(count($rp)>0){
            $q = array_shift($rp);
            if ($p = $q->getFlag(IGK_PARAMS_FLAG)){
                if (key_exists($key, $p)){
                    return igk_getv($p, $key, $default);
                }
            }
            if ($tp = $q->getParentNode()){
                array_push($rp, $tp);
            }
        }
        return $default;

    }
    /**
     * @return bool get if close tag
     */
    public function closeTag():bool
    {
        return true;
    }
    public function getCanRenderTag()
    {
        return true;
    }
    public function getCanAddChilds()
    {
        return true;
    }
    public function getContent()
    {
        return $this->content;
    }
    /**
     * check if can load content - in current context
     * @param mixed $value 
     * @return bool 
     */
    protected function getcanLoadContent($value): bool
    {   
        if (!is_string($value) || !$this->getCanAddChilds()){
            return false;
        } 
        if (is_bool($this->canLoadContent) && $this->canLoadContent){
            return $this->canLoadContent;
        }

        $ctx = HtmlLoadingContext::GetCurrentContext();
        return is_string($value) && $this->getCanAddChilds() && HtmlUtils::IsHtmlContent($value)
            && (!$ctx || $ctx->load_content);
    }
    /**
     * load content.
     * @param array|mixed $value 
     * @return $this 
     * @throws IGKException 
     */
    public function setContent($value)
    {
        // igk_debug_wln(__FILE__.":".__LINE__,  "setting node ....------".$value);
        if (func_num_args()>1){
            $tab = func_get_args();
            while(count($tab)>0){
                if (!($q = array_shift($tab))){
                    continue;
                } 
                if (is_array($q)){
                    $this->obdata($q);
                    continue;
                } 
                if ($q instanceof self){
                    $this->add($q);
                    continue;
                }              
                if ($this->getcanLoadContent($q)){
                    $this->load($q);
                }else{
                    if (!empty($this->content)){
                        $this->content .= $q;    
                    }else{
                        $this->content = $q;
                    }
                }
            }
        }else{
            if ($this->getcanLoadContent($value)) {
                $this->load($value);
            } else {
                $this->content = $value;
            }
        }
        return $this;
    }
  
    /**
     * get tagname
     * @param mixed $options 
     * @return mixed 
     */
    public function getTagName($options = null)
    {
        return $this->tagname;
    }

    public function getChildCount()
    {
        if ($c = $this->getChilds()) {
            return $c->count();
        }
        return 0;
    }
    /**
     * get if accept renderer
     * @param mixed $options 
     * @return bool true if allow rendereing 
     * @throws IGKException 
     */
    public final function AcceptRender($options = null)
    {

        if ($this->iscallback(__FUNCTION__)) {
            $o = null;
            if ($this->evalCallback(__FUNCTION__, $o, compact("options")))
                return $o;
        }
        return $this->_acceptRender($options);
    }

    public final function RenderComplete($options = null)
    {
        $this->__RenderComplete($options = null);
    }

    public function __get($name)
    {
        if (strpos($name, 'get') === 0) {
            $o = null;
            if ($this->evalCallback($name, $o))
                return $o;
        }
        if (method_exists($this, $fc = "get" . ucfirst($name))) {
            return call_user_func_array([$this, $fc], []);
        } 
        if (method_exists($this, $fc = 'getProperty')){
            return $this->getProperty($name);
        };
    }
    public function __set($key, $value)
    {
        if (0 === strpos($key, "igk:")) {
            $this->setSysAttribute(substr($key, 4), $value, $this->LoadingContext);
            return;
        }
        if (!$this->_setIn($key, $value)) {
            $o = null;
            if (!$this->evalCallback("set" . $key, $o, array("value" => $value))) {
                if (method_exists($this, "setProperty")) {
                    return $this->setProperty($key, $value);
                }
                $this->setParam($key, $value);
            }
        }
    }
    /**
     * 
     * @return bool
     */
    protected function _isCallback()
    {
        return false;
    }
    /**
     * add node to list
     * @param mixed $n node to add
     * @param bool $force bypass can add childs check
     * @return bool 
     */
    protected function _add($n, bool $force = false):bool
    { 
        if (($force || $this->getCanAddChilds()) && $n && ($n !== $this)) {
            if ($n->m_parent) {
                $n->remove();
            }
            $this->m_childs[] = $n;
            $n->m_parent = $this;
            return true;
        }
        return false;
    }
    /**
     * 
     * @param mixed|string|HtmlItemBase script $n 
     * @param mixed $attributes 
     * @param mixed $args 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function add($n, $attributes = null, $args = null)
    {
        if ($invoking = $this->getFlag('__call')){
            $this->setFlag('__call', null);
        }
        $skip = false; 


        #region view compilation model
        ///TODO : remove compilation in core HtmlItemBase 
        // compilation node add
        if ($n instanceof \IGK\System\Runtime\Compiler\ViewCompiler\ViewExpressArg){
            $n = $n->createExpressionNode();
        }
        if ($n instanceof \IGK\System\Runtime\Compiler\ViewCompiler\ViewGetterExpression){
            $v = \IGK\System\Runtime\Compiler\ViewCompiler\ViewGetterExpression::GetRealValue($n);
            // if ($v instanceof self){
            //     $n = $v;
            // }
            // else {
                $n = $n->createExpressionNode();
            // }
        }
        #endregion 
        $lastchild = null;
        $container = null;
        if (is_string($n)){
           igk_html_push_node_parent($this);
           if (igk_is_debug()){

               // if ($this instanceof IHtmlContextContainer){
                   //     $container = $this;
                   $ref_count = HtmlLoadingContext::CountCountext();
                   //     HtmlLoadingContext::PushContext($container->getLoadingContext());
                   // } 
            }
            if (!$invoking && (strpos($n, ">")!==false) && (strpos($n, "?") === false)){       
                $n = HtmlNodeTagExplosionDefinition::Core()->builder->setup($n,
                [], $lastchild);
            } else {
                $n = static::CreateWebNode($n, $attributes, $args);
            }
            $skip = igk_html_is_skipped();
            // if (isset($container)){
            //     HtmlLoadingContext::PopContext();
            // }
            igk_html_pop_node_parent();
        } 
        if ($n && ($skip || ($this->_add($n) !== false))) {
            return $lastchild ?? $n;
        }
        return $this;
    }
    public function addNode($nodeName)
    {
        return $this->add($nodeName);
    }

    ///<summary>used to add non declared element by namespace</summary>
    /**
     * used to add non declared element by namespace
     */
    public function addNS($ns, $name)
    {
        $h = igk_get_env(IGK_ENV_HTML_COMPONENTS);
        if ($h == null)
            return null;
        $c = igk_getv(igk_getv(igk_getv($h, $ns), strtolower($name)), "callback");
        $p = array_slice(func_get_args(), 2);
        if (igk_is_callable($c)) {
            $s = $this->__init_new_node_func($c, $p);
            if ($s) {
                $s->setParam(IGK_NS_PARAM_KEY, $ns);
                $s["xmlns"] = new HtmlNSValueAttribute($s, $ns);
            }
            return $s;
        }
        $k = "add" . $name;
        return call_user_func_array(array($this, $k), $p);
    }
    ///<summary>special function add a node as a callback. if name defined return the name or create and add it with the callback</summary>
    /**
     * special function add a node as a callback. if name defined return the name or create and add it with the callback
     */
    public function addNodeCallback($name, $callback, $host = null)
    {

        $host = $host ?? $this;
        $c = $host->getParam(IGK_NAMED_NODE_PARAM) ?? array();
        if ($n = igk_getv($c, $name)) {
            return $n;
        }
        if (($h = $callback($host)) && $host->add($h)) {
            $c[$name] = $h;
            $host->setParam(IGK_NAMED_NODE_PARAM, $c);
            return $h;
        }
        return null;


        igk_die("not implement " . __METHOD__);

        // if(empty($name)){
        //     igk_die("name must be set");
        // }
        // //+ change the node callback behaviour - store the setting in session
        // $host=$host ?? $this;
        // $c=$host->getParam(IGK_NAMED_NODE_PARAM, array());
        // $f = null;
        // if(isset($c[$name])){
        //     // get setting
        //     $f = $c[$name];
        //     $this->add($f); 
        //     return $f;
        // }
        // $h = $callback($this);
        // if($h){
        //     if (method_exists($h, "getSetting"))
        //         $c[$name]= $h->getSetting();
        //     else {
        //         $c[$name] = (object)[];
        //     }
        //     $h->setParam(IGK_NAMED_ID_PARAM, $name);
        //     $host->setParam(IGK_NAMED_NODE_PARAM, $c);
        //     return $h;
        // }
        // return null;
    }
    ///<param name="o" ref="true"></param>
    /**
     * 
     * @param mixed $name
     * @param mixed * $o
     */
    protected final function evalCallback($name, &$o)
    {
        $v_nsfc = $this->getFlag(IGK_NSFC_FLAG);
        $g = $this->getFlag(IGK_CALLBACK_FLAG);
        if (!($g && isset($g[$name])) && isset($v_nsfc)) {
            $fcname = $v_nsfc . "_" . $name;
            if (function_exists($fcname)) {
                $g = igk_create_invoke_callback($fcname, $this);
                $this->m_callback[$name] = $g;
            }
        }
        if (isset($g[$name])) {
            $b = $g[$name];
            if (is_callable($b)) {
                if (is_array($b)) {
                    if (igk_getv($b, 0) === $this) {
                        $o = call_user_func_array($b, igk_getv(func_get_args(), 2, array()));
                        return true;
                    } else {
                        if (count($b) > 2) {
                            $cp = array_slice($b, 2);
                            $o = call_user_func_array(array_slice($b, 0, 2), $cp);
                            return true;
                        }
                    }
                }
                if (func_num_args() > 2) {
                    $t = array($this);
                    $t = array_merge($t, igk_getv(array_slice(func_get_args(), 2), 0));
                    $o = call_user_func_array($b, $t);
                    return true;
                } else {
                    $o = $b($this);
                    return true;
                }
            } else if (is_string($b)) {
                // if (igk_is_debug())
                //  igk_ilog(__FILE__.":".__LINE__. " evaluate expression: ".$b);
                $param = igk_getv(func_get_args(), 2, array());
                extract($param);
                extract($this->getParam($name . self::CALLBACK_SUFFIX, array()));
                $self = $this;
                igk_set_env(IGK_LAST_EVAL_KEY, $b);
                igk_set_env(IGK_LAST_EVAL_LINE, __LINE__);
                $o = @eval($b);
                igk_set_env(IGK_LAST_EVAL_KEY, null);
                igk_set_env(IGK_LAST_EVAL_LINE, null);
            } else if (is_object($b) && isset($b->clType)) {
                $addArgs = igk_getv(array_slice(func_get_args(), 2), 0);
                $sourceParam = "source\x01:param";
                $sourceDeepth = "source\x01:depth";
                $gc = null;
                if (isset($b->$sourceParam)) {
                    $gc = $b->$sourceParam;
                    $b->$sourceDeepth++;
                } else {
                    $gc = isset($b->clParam) && $b->clParam ? array_slice($b->clParam, 0) : null;
                    $b->$sourceParam = $b->clParam;
                    $b->$sourceDeepth = 1;
                }
                $extra = null;
                $fc_args = igk_getv(func_get_args(), 2, null);
                if (($_v_c = igk_count($addArgs)) > 0) {
                    if ($gc == null) {
                        $gc = $addArgs;
                    } else {
                        if (!is_array($gc))
                            $gc = array($gc);
                        if (($_v_c == 1) && (array_keys($addArgs)[0] === 0)) {
                            $extra = array($addArgs[0]);
                        } else
                            $gc = array_merge($gc, $addArgs);
                    }
                }
                if ($fc_args) {
                    $gc["func:args"] = $fc_args;
                }
                $bck = isset($b->clParam) ? $b->clParam : null;
                $b->clParam = $gc;
                $o = igk_invoke_callback_obj($this, $b, $extra);
                $b->clParam = $bck;
                $b->$sourceDeepth--;
                if ($b->$sourceDeepth <= 0) {
                    unset($b->$sourceDeepth);
                    unset($b->$sourceParam);
                }
                return true;
            } else {
                return false;
            }
            return true;
        }
        return false;
    }
    ///<summary>free callback node</summary>
    /**
     * free callback node
     */
    public function freeNodeCallback($n = null)
    {
        $c = $this->getParam(IGK_NAMED_NODE_PARAM);
        if ($n === null) {
            $c = array();
        } else {
            unset($c[$n]);
        }
        $this->setParam(IGK_NAMED_NODE_PARAM, $c);
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $name
     */
    public function getCallbackNode($name)
    {
        $c = $this->getParam(IGK_NAMED_NODE_PARAM);
        return igk_getv($c, $name);
    }
    ///<summary>get the generated node type</summary>
    /**
     * get the generated node type
     */
    public function getIGKNodeType()
    {
        return $this->getFlag(IGK_NODETYPE_FLAG);
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getIsLoading()
    {
        return $this->getFlag(IGK_ISLOADING_FLAG, 0);
    }

    ///<summary>get the generated node type Name or parameters</summary>
    /**
     * get the generated node type Name or parameters
     */
    public function getIGKNodeTypeName()
    {
        return $this->getFlag(IGK_NODETYPENAME_FLAG) ?? get_class($this);
    }
    public function getInnerHtml()
    {
        return HtmlRenderer::GetInnerHtml($this);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function gethasContent()
    {
        return !empty($this->Content) || $this->getHasChilds();
    }
    ///aditionnal array of expression attributes
    /**
     */
    public function getExpressionAttributes()
    {
        $tab = igk_getctrl(IGK_REFERENCE_CTRL)->getHtmlExpresionTab($this);
        if ($tab !== null) {
            $t = array();
            foreach ($tab as $k => $v) {
                $t[$k] = $this->$k;
            }
            return $t;
        }
        return null;
    }
    /**
     * 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function __call($name, $arguments)
    { 
        $this->setFlag(__FUNCTION__,1);
        if ($name === "set") {
            igk_dev_wln_e('magic call with "set" only name is not allowed.');
            igk_environment()->isDev() && igk_trace();
            igk_die("not allowed");
        }
        if (in_array($name, $this->m_callexclude)) {
            return 0;
        }
        if (strtolower($name) == "iscallback") {
            $o = call_user_func_array(array($this, "_isCallback"), $arguments);
            return $o;
        }
        if ($this->iscallback($name)) {
            $o = null;
            if ($this->evalCallback($name, $o, $arguments)) {
                return $o;
            }
        }
        $tgname = $this->getTagName();
        // + | factory invoke
        $instance = \IGK\System\Html\Dom\Factory::getInstance();
        if ($instance->handle($tgname, $name)) {
            igk_html_push_node_parent($this);
            $r = $instance->Invoke($tgname, $name, $arguments);
            igk_html_pop_node_parent();
            return $r;
        }
        // + | --------------------------------------------------------------------                
        // + | invoke macros 
        // + | 
        if ($this instanceof IHtmlContextContainer){
            // class not found method in         
            $response = null; 
            if (HtmlLoadingContext::SurroundWith($this, function($name, $arguments, & $response){
                $t = static::_invokeMacros($name, array_merge([$this], $arguments), $response);
                if (is_bool($t)){
                    return $t;
                }
                return false;
            },$name, $arguments, $response)){
                return $response;
            }
        }else{
            if (static::_invokeMacros($name, array_merge([$this], $arguments), $response)) {
                return $response;
            }
        }
        if ($this->getCanAddChilds()) {
            if (strpos($name, IGK_ADD_PREFIX) === 0) {
                $k = substr($name, 3);
                if (!empty($k)) {
                    if (substr($k, 0, 2) == "NS") {
                        $n = substr($k, 2);
                        if (empty($n)) {
                            igk_die("Name not set can't create obj");
                        }
                        if (igk_count($arguments) < 1) {
                            igk_die("first argument must be a namespace");
                        }
                        $ns = $arguments[0];
                        $arg = array_merge(array($ns, $n), array_slice($arguments, 1));
                        return call_user_func_array(array($this, "addNS"), $arg);
                    } else {
                        $g = $this->getParam(IGK_NS_PARAM_KEY);

                        if (!empty($g)) {
                            $arg = array_merge(array($g, $k), $arguments);
                            return call_user_func_array(array($this, "addNS"), $arg);
                        }
                    }
                    $tab = array(strtolower($k), null, $arguments);
                    return call_user_func_array(array($this, IGK_ADD_PREFIX), $tab);
                }
            }
            if (igk_environment()->isDev()) {
                if (strpos($name, "get") === 0) { 
                    igk_die("'try to call : " . __METHOD__ . " " . $name);
                }
                if (strpos($name, "set") === 0) { 
                    igk_trace(); 
                    igk_die("'try to call : " . __METHOD__ . " " . $name);
                }
            }
            foreach (["get", "add"] as $prefix) {
                if (method_exists($this, $fc = $prefix . ucfirst($name))) {
                    return call_user_func_array([$this, $fc], $arguments);
                }
            }
            $tab = array($name, null, $arguments);
            return call_user_func_array([$this, IGK_ADD_PREFIX], $tab);
        } else {
            if (method_exists($this, $fc = "get" . ucfirst($name))) {
                return call_user_func_array([$this, $fc], $arguments);
            }
        }
        if (igk_environment()->is('DEV')) {
            igk_wln(__FILE__ . ":" . __LINE__,  get_class($this));
            igk_trace();
            igk_die("call_expression not allowed. " . $name);
        }
    }
    public function getHasAttributes()
    {
        return igk_count($this->m_attributes);
    }
    ///<summary></summary>
    ///<param name="flag"></param>
    ///<param name="default" default="null"></param>
    /**
     * 
     * @param mixed $flag
     * @param mixed $default the default value is null
     * @return mixed
     */
    public function getFlag($flag, $default = null)
    {
        return igk_getv($this->_f, $flag, $default);
    }
    ///<summary></summary>
    /**
     * retrieve the current loading context
     */
    public function getLoadingContext()
    {
        return $this->getFlag(IGK_LOADINGCONTEXT_FLAG, 0);
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $name
     */
    protected final function iscallback($name)
    {
        $g = $this->getFlag(IGK_CALLBACK_FLAG);
        return $g ? isset($g[$name]) : 0;
    }
    ///<summary>get the parent document </summary>
    /**
     * get the parent document
     */
    public function getParentDocument()
    {
        $q = $this;
        while (($p = $q->getParentNode())) {
            if (get_class($p) == IGKHtmlDoc::class) {
                return $p;
            } 
            $q = $p;
        } 
        return null;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getNodeCreationArgs()
    {
        return $this->getTempFlag("creationargs");
    }
    /**
     * 
     * @param mixed $n 
     * @param mixed $attributes 
     * @param mixed $indexOrArgs 
     * @return mixed 
     * @throws IGKException 
     */
    public static function CreateWebNode($n, $attributes = null, $indexOrArgs = null)
    { 
       
        if ($n = HtmlUtils::CreateHtmlComponent($n, $indexOrArgs)) {
            if ($attributes) {
                $n->setAttributes($attributes);
            }
        }
        return $n;
    }
    /**
     * help do html output response 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function output()
    {
        (new \IGK\System\Http\WebResponse($this->render()))->output();
    }
    public function render($options = null)
    {
        return HtmlRenderer::Render($this, $options);
    }

    public function clearChilds()
    {
        $this->m_childs->clear();
        return $this;
    }
    public function clearAttributes()
    {
        $this->m_attributes->clear();
        return $this;
    }
    public function clear()
    {
        $this->clearChilds();
        $this->clearAttributes();
    }
    public function remove()
    {
        if (func_num_args() > 0) {
            igk_die("remove element not allow extra args.");
        }
        if ($this->m_parent) {
            if (is_array($this->m_parent->m_childs)) {
                if (false !== ($index = array_search($this, $this->m_parent->m_childs))) {
                    unset($this->m_parent->m_childs[$index]);
                }
            } else {
                $this->m_parent->m_childs->remove($this);
            }
            $this->m_parent = null;
        }
    }
    /**
     * get parent
     * @return ?HtmlItemBase 
     */
    public function getParentNode()
    {
        return $this->m_parent;
    }
    /**
     * replace current node with 
     * @param mixed $node 
     * @return void 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public function replaceWith(HtmlItemBase $node){
        $p = $this->getParentNode();
        if ($p){
            $this->remove();
            $p->add($node);
        } 
    }
    protected function _access_OffsetGet($k)
    {
        return igk_getv($this->m_attributes, $k);
    }
    protected function _access_OffsetSet($k, $v)
    {
        if ($v === null) {
            unset($this->m_attributes[$k]);
        } else {
            $this->m_attributes[$k] = $v;
        }
        return $this;
    }
    protected function _access_OffsetUnset($n)
    {
        unset($this->m_attributes[$n]);
        return $this;
    }
    protected function _access_offsetExists($n)
    {
        return $this->m_attributes->keyExists($n);
    }
    ///<summary>compatibility with previous version</summary>
    final function renderAJX($options = null)
    {
        echo $this->render($options);
    }
    ///<summary></summary>
    ///<param name="flag"></param>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $flag
     * @param mixed $v
     */
    public function setFlag($flag, $v)
    {
        if (!isset($v) && (func_num_args() < 2)) {
            igk_die("Argument not found " . func_num_args());
            return;
        }
        if ($v == null) {
            unset($this->_f[$flag]);
        } else
            $this->_f[$flag] = $v;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getParentHost()
    {
        return $this->getFlag(IGK_PARENTHOST_FLAG);
    }
    /**
     * load content to current node
     * @param mixed $source text to load
     * @param mixed $contextObj context loading info
     * @param callable|null $creator 
     * @return mixed 
     */
    public function load(string $source, $contextObj = null, callable $creator = null)
    {
        return self::LoadInContext($this, $source, $contextObj ?? HtmlContext::Html, $creator);
    }
    ///<summary>Load html content to this node </summary>
    ///<param name="content">source string to load</param>
    ///<param name="context">context of the loading. mixed string or object</param>
    ///<remark>Data will be evaluated. if you don't what IGK system balafon evaluation used LoadExpression</remark>
    /**
     * Load html content to this node
     * @param static $t
     * @param mixed $content source string to load
     * @param mixed|HtmlLoadingContextOptions $context context of the loading. mixed string or object
     */
    public static function LoadInContext(HtmlItemBase $t, string $content, $context = null, callable $creator = null)
    {
        if (is_null($creator)) {
            // creator that require a function class with loading creator implementation
            $creator = Closure::fromCallable([get_class($t), \LoadingNodeCreator::class]);
        }
        $expression = false;        
        if ($context ){
            $expression = 
                ($context == HtmlContext::XML) || 
                (is_object($context) && igk_getv($context, "Context") == HtmlContext::XML);
        }
        $d = $expression? 
         HtmlReader::LoadExpression($content, $context):
         HtmlReader::Load($content, $context, $creator);
        if ($d){
            $d->CopyTo($t);
            return true;
        }
        return false;
    }
    /**
     * default loading node creator
     * @param string $name 
     * @param null|array $param 
     * @return mixed 
     * @throws IGKException 
     */
    public static function LoadingNodeCreator(string $name, ?array $param = null)
    { 
        static $tag_creating = null;
        if (strpos($name, 'igk:') === 0) {
            $f = igk_create_node(substr($name, 4), null, $param);
            if ($f)
                return $f;
        }
        $tb = explode(':', $name);
        if (count($tb) == 1) {
            // + | create a simple tag name
            return new HtmlNode($name);
        } 
        // + | passing complex tag t_:code as exemple must be handle by the default - tag 
        if ($tag_creating == $name){
            // detect try to create a tag - 
            igk_die(sprintf("not handle tag name [%s]", $name));
        }
        $tag_creating = $name;
        $t = static::CreateWebNode(...func_get_args());
        $tag_creating = null;
        return null;
    }
    ///<summary> load file content .xphtml </summary>
    /**
     *  load file content .xphtml
     * @var string $file file to load
     * @var string|object|array $options context used to load
     * @var string|object|array $args argument to 
     */
    public function loadFile(string $file, $options = null, $args = null)
    {
        if (!is_file($file))
            return false;
            $content = IO::ReadAllText($file);
            if (empty($content)) {
                return $this;
            }
        $op = null;
        if (is_string($options)) {
            $op = ["Context" => $options];
        } else {
            $op = (object)$options;
        }
        $options = igk_create_filterobject($op, ["stripComment" => 0]);
        if ($options->stripComment) {
            $content = igk_html_strip_comment($content);
        }
        if (is_array($args))
            $args = (object)$args;
        else {
            $args = $options;
        } 
        return $this->load($content, $args);
    }

    ///<summary></summary>
    ///<param name="flag"></param>
    /**
     * 
     * @param mixed $flag
     */
    public function unsetFlag($flag)
    {
        unset($this->_f[$flag]);
    }
    ///<summary>override this method to initialize your component</summary>
    /**
     * override this method to initialize your component
     */
    public function loadingComplete()
    {
        if (!igk_reflection_class_extends(get_called_class(), __CLASS__)) {
            igk_die(__FUNCTION__ . " call not allowed");
        }
        if (method_exists($this, "initView"))
            $this->initView();
        $this->unsetFlag(IGK_ISLOADING_FLAG);
        $this->unsetFlag(IGK_LOADINGCONTEXT_FLAG);
    }
    ///Load a single node if found in a text sources
    /**
     */
    public static function LoadNode($text)
    {
        if (empty($text))
            return null;
        $v_dummy = new XmlNode("dummy");
        $v_dummy->Load($text);
        if ($v_dummy->HasChilds) {
            return $v_dummy->Childs[0];
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="articlename"></param>
    /**
     * 
     * @param mixed $ctrl
     * @param mixed $articlename
     */
    public function LoadView($ctrl, $articlename)
    {
        if ($ctrl) {
            $c = $ctrl->getViewContent($articlename, $this);
            if (!empty($c))
                $this->Load($c);
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="callable"></param>
    /**
     * 
     * @param mixed $n
     * @param mixed $callable
     */
    public function setCallback($n, $callable)
    {
        $g = $this->getFlag(IGK_CALLBACK_FLAG);
        $k = $n . "Params";
        if ($callable == null) {
            unset($g[$n]);
            $this->setParam($k, null);
        } else {
            $g[$n] = $callable;
            $tb = array_slice(func_get_args(), 2);
            if ((count($tb) > 0) && is_array($tb[0])) {
                $this->setParam($k, $tb[0]);
            }
            if (!$g) {
                $g = array();
            }
        }

        if (igk_count($g) == 0)
            $this->unsetFlag(IGK_CALLBACK_FLAG);
        else
            $this->setFlag(IGK_CALLBACK_FLAG, $g);
        return $this;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
     * set node tempory properties
     * @param mixed $key
     * @param mixed $value
     */
    public function setParam($key, $value)
    { 
        $p = $this->getFlag(IGK_PARAMS_FLAG, array());
        if ($value == null) {
            unset($p[$key]);
        } else
            $p[$key] = $value;
        $this->setFlag(IGK_PARAMS_FLAG, $p);
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
     * 
     * @param object $options the default value is null
     * @param bool $exit stop on exists
     */
    public function renderXML($options = null, $exit = 1)
    {
        igk_header_set_contenttype("xml");
        $opt = $options ?? HtmlRenderer::CreateRenderOptions();
        $opt->Context = "xml";
        if (igk_getv($opt, 'xmldefinition') == 1) {
            $xml = new \IGK\System\Html\XML\XmlProcessor("xml");
            $xml["version"] = igk_getv($opt, "version") ?? "1.0";
            $xml["encoding"] = igk_getv($opt, "encoding") ?? "utf8";
            $xml->renderAJX();
        }
        $this->renderAJX($opt);
        if ($exit)
            igk_exit();
    }
    ///<summary> get temp flag for node</summary>
    /**
     *  get temp flag for node
     */
    public function getTempFlag($n)
    {
        $t = igk_get_env("sys://node/temp/flags");
        $h = spl_object_hash($this);
        if ($t && isset($t[$h])) {
            return igk_getv($t[$h], $n);
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $n
     * @param mixed $v
     */
    public function setTempFlag($n, $v)
    {
        $t = igk_get_env("sys://node/temp/flags", array());
        $h = spl_object_hash($this);
        if (!isset($t[$h])) {
            $t[$h] = array();
        }
        $t[$h][$n] = $v;
        igk_set_env("sys://node/temp/flags", $t);
    }
    ///<summary></summary>
    ///<param name="value"></param>
    ///<param name="autoindex" default="false"></param>
    /**
     * 
     * @param mixed $value
     * @param mixed $autoindex the default value is false
     */
    public function setIndex($value, $autoindex = false)
    {
        $i = $this->getIndex();
        if ($i !== $value) {
            $i = $value;
            if ($i == null) {
                $this->unsetFlag(IGK_ZINDEX_FLAG);
            } else {
                $this->setFlag(IGK_ZINDEX_FLAG, $i);
            }
        }
        if (!$autoindex) {
            $this->setAutoIndex(-1);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIndex()
    {
        return $this->getFlag(IGK_ZINDEX_FLAG);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    public function setAutoIndex($v)
    {
        $this->setFlag(IGK_AUTODINDEX_FLAG, $v);
        return $this;
    }
    ///<summary> get if tag is close tag</summary>
    /**
     * get if tag is close tag
     * @return bool is close tag
     */
    public function isCloseTag($tag)
    {
        if (0 === strpos($tag, "igk:"))
            $tag = substr($tag, 4);
        if (strtolower($this->tagName) == strtolower($tag))
            return true;
        return $this->_p_isClosedTag($tag);
    }
    protected function _p_isClosedTag($tag)
    {
        return false;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    /**
     * 
     * @param mixed $id
     */
    public function getElementById(string $id)
    {
        $c = $this->getChilds()->to_array();
        if ($c == null) {
            return null;
        }
        $tab = array();
        $s = strtolower($id);
        $q = $this;
        $itab = array($q);
        while ($q = array_pop($itab)) {
            $_id = $q["id"];
            if (is_string($_id) && (strtolower($_id) == $s)) {
                $tab[] = $q;
                continue;
            }
            $v_c = $q->getChilds();
            if ($v_c === null) {
                // igk_dev_wln("is null child node is null ", get_class($q));
                continue;
            }
            if ($c = $v_c->to_array()) {
                foreach ($c as $k) {
                    array_push($itab, $k);
                }
            }
        }
        if (count($tab) == 1)
            return $tab[0];
        return $tab;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<return refout="true"></return>
    /**
     * search for item by tagname
     * @param string|callable $name tag to search
     * @return mixed|array  | * list of element by tagname not implement
     */
    public function getElementsByTagName($name, bool $stop_first = false)
    {
        // igk_trace();
        // $name = "menuname";
        $tab = array();
        $s = '';
        $fc = $name;
        if (is_string($name)){
            $fc = function($n)use($name){
                return $n->getTagName()== $name;
            };
            $s = strtolower($name);
        } else if (!is_callable($name)){
            igk_die("expect a callable. ");
        }
        $c = $this->getChilds();
        $ctab = [$this];

        while ($q = array_shift($ctab)) {
            $c = $q->getChilds();
            if (!$c)
                continue;
            $c_c = $c->to_array();
            if ($s == "*") {
                // select all node
                $tab = array_merge($tab, $c_c);
                foreach ($c_c as $t) {
                    $p = $t->getChilds();
                    if ($p) {
                        $ctab = array_merge($ctab, $p->to_array());
                    }
                }
            } else {
                // seach in childs 
                // + | --------------------------------------------------------------------
                // + | update search with path aglo expression 
                // + |
                
                $result = [];
                while(count($c_c)>0){
                    $p = array_shift($c_c);
                    if ($fc($p)){
                        $result[] = $p;
                        if ($stop_first)
                            break;
                    }
                    if ($ct = $p->getChilds()){
                        if ($cp = $ct->to_array())
                            array_unshift($c_c, ...$cp);
                    }
                }
                return $result;
               
            }
        }
        return $tab;        
    }
}
