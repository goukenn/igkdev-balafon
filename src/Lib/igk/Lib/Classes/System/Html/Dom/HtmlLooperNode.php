<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLooperNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

use ArrayIterator;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helper\SysUtils;
use IGK\Helper\ViewHelper;
use IGK\System\DataArgs;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Html\RenderingContext;
use IGK\System\IO\StringBuilder;
use IGK\System\Polyfill\IteratorTrait;
use IGK\System\Runtime\Compiler\CompilerConstants;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewExpressionArg;
use IGK\System\ViewDataArgs;
use IGKException;
use ReflectionException;
use SebastianBergmann\FileIterator\Iterator;
use IGK\System\Html\IHtmlHostContextContainer;
use IGK\System\Html\IHtmlTemplateHost;

/**
 * summary html array looper.
 * Help write view and article template without the php foreach loop
 * @example usage $t->loop([1,2,3])->host(function($n, $a){\
 *                  $n->li()->Content = "Item ".$a;\
 *              });
 * loop for child template. 
 * it bind to looper node : -:)
 * contextual looping node 
 */
class HtmlLooperNode extends HtmlItemBase implements IHtmlTemplateHost
{

    /**
    * Property: args.
    * @var mixed
    */
    private $args;

    /**
    * Property: node.
    * @var mixed
    */
    private $node;

    /**
    * Callback handler for callback.
    * @var mixed
    */
    private $callback;

    /**
    * Property: template.
    * @var mixed
    */
    private $m_template;

    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "igk:looper";
    /**
     * skip render loop flag
     * @var ?bool
     */
    private $m_skip_rendering;
    /**
     * to indicate that the variables list is a looper key 
     */
    const LOOPER_KEY = '$raw';

    /**
    * Property: rendering context args.
    * @var mixed
    */
    private static $sm_renderingContextArgs;
    /**
     * param to pass 
     * @var mixed
     */
    private $params; //

    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;

    /**
    * Used by var_dump() to customize debug output.
    */
    function __debugInfo()
    {
        return [];
    }
    /**
     * .ctr
     * @param mixed $args item used to loop
     * @param mixed $node parent node that will host data 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public function __construct($args, $node)
    {
        parent::__construct();
        $this->args = $args;
        $this->node = $node;
        $this->setFlag("NO_TEMPLATE", 1);
        $this->m_template = igk_create_notagnode(); 
        $node->setFlag(self::OVERRIDE_PARENT_TAG_FLAG, 1); 
    }

    /**
    * auto generate doc.
    * @param bool $force
    * @return bool
    */

    protected function _add($n, bool $force = false): bool
    {
        if (!is_null($this->callback)) {
            return false;
        }
        return $this->m_template->_add($n);
    }

    /**
    * auto generate doc.
    * @param array|mixed $content
    * @return $this
    */

    public function setContent($content)
    {
        if (is_string($content)) {
            $this->m_template->text($content);
        }
        return $this;
    }

    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
     * render override 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public function render($options = null)
    {
        if ($this->m_skip_rendering) {
            return '';
        }
        if ($this->callback) {
            return null;
        }
        // + | because of renderging the host 
        $this->m_skip_rendering = true;
        $v_childrend = $this->m_template->getChilds();
        $c = $v_childrend->count();
        $v_out = null;
        if ($c) {
            $v_out = $this->generateRender($v_childrend, $options);
        }
        $this->m_skip_rendering = false;
        return $v_out;
    }

    /**
    * auto generate doc.
    * @param mixed $options
    * @return mixed
    */

    public function generateRender($children, $options = null)
    {  
        $ctrl = $this->controller ?? ViewHelper::CurrentCtrl()  ?? SysDbController::ctrl();
        $sb = new StringBuilder;
        $t_options = $options ? clone ($options) : (object)[];
        $t_options->renderingContext = RenderingContext::TEMPLATE;
        if (is_null(self::$sm_renderingContextArgs)) {
            self::$sm_renderingContextArgs = [];
        }
        $v_args = $this->args;
        // +| Treat Argument to Match Logger
        $v_args = \IGK\System\Html\Templates\Engine\Helpers\LooperArgs::TreatArgument($v_args);
        if (is_string($v_args) && strstr($v_args, self::LOOPER_KEY)) {
            // render with loopkein definition 
            if (count(self::$sm_renderingContextArgs) == 0) {
                igk_die("not in contextual rendering raw");
            }
            $sb = self::_RenderTemplate($children, $t_options, ['key' => $v_args]);
            return $sb;
        }
        array_unshift(
            self::$sm_renderingContextArgs,
            (object)[
                'source' => $this,
                'raw' => $v_args
            ]
        );
        $sb = self::_RenderTemplate2($this->node, $children, $t_options);
        // + render global data 
        $n = igk_create_notagnode();
        if ($v_args instanceof IViewExpressionArg) {
            $hook_expression = $v_args->getExpression();
            self::_HostChain($n, $sb . "", $v_args, $ctrl, $hook_expression);
            $v_out  = $n->render();
        } else {
            $hook_expression = CompilerConstants::LOOP_CONTEXT_DATA_VAR;
            self::_HostChain($n, $sb . "", $v_args, $ctrl, '$' . $hook_expression);
            $v_out = $n->render();
            if ($v_args instanceof DataArgs) {
                $v_targs = $v_args->getData();
            } else {
                $v_targs = is_array($v_args) ? $v_args : 0;
            }
            if ($v_targs) {
                ob_start();
                $v_out = implode("\n", [self::_TreatFCallback(), $v_out]);
                SysUtils::Eval($v_out, [
                    $hook_expression =>  new LopperEvalData($v_targs),
                    "raw" => $v_args,
                    "ctrl" => $ctrl
                ]);
                $v_out = ob_get_contents();
                ob_end_clean();
            } else {
                $v_out = null;
            }
        }
        array_shift(self::$sm_renderingContextArgs);
        return $v_out;
    }

    /**
    * auto generate doc.
    * @return
    */
    private static function _TreatFCallback()
    {
        return implode("\n", [
            '<?php $__fc_loop_ref = function($a){',
            'if (is_numeric($a)){',
            'return range(0, intval($a));',
            '}',
            'return !is_array($a) && !is_object($a) ? [$a] : $a;',
            '}; ?>',
        ]);
    }

    /**
    * auto generate doc.
    * @param mixed $t_options
    * @return string
    */
    private static function _RenderTemplate($children, $t_options): string
    {
        $sb = new StringBuilder;
        $tb = self::$sm_renderingContextArgs;
        $dc = [];

        foreach ($children->to_array() as $tc) {
            if ($tc->getCanRenderTag()) {
                $tc["*for"] = "\$raw";
                $v_cx = HtmlRenderer::Render($tc, $t_options);
                $sb->append($v_cx);
            } else {
                //+ | binding text content 
                $v_cx = HtmlRenderer::Render($tc, $t_options);
                $dc[] = $v_cx;
                $sb->append($v_cx);
            }
        }

        if ($dc) {
            $v_cx = implode('', $dc);
            $v_var_k = CompilerConstants::LOOP_CONTEXT_DATA_VAR;
            if ($tb) {
                $v_loop_depth = igk_getv($t_options, 'loopDeth');
                $arg = "$" . $v_var_k;
                $keys = ["'raw'"];
                if ($v_loop_depth) {
                    $keys[] = "'key'";
                }

                $sb->appendLine("<?php ");
                $sb->appendLine("\$v_states[] = compact(" . implode(',', $keys) . ", '" . $v_var_k . "');" . $arg . "= \$__fc_loop_ref(\$raw);");
                $sb->appendLine("foreach(" . $arg . " as \$raw): ?>");
                $sb->appendLine($v_cx);
                $sb->append("<?php endforeach; extract(array_pop(\$v_states)); /* 1 */ ?>");
            } else {
                $sb->append("<?php foreach($" . $v_var_k . " as \$raw): ?>");
                $sb->append($v_cx);
                $sb->append("<?php endforeach; /* 2 */ ?>");
            }
        }
        return $sb . '';
    }

    /**
    * auto generate doc.
    * @return void
    */

    public function remove(){
        parent::remove();
        if (is_null($this->m_parent) && $this->node){
            $this->node->setFlag(self::OVERRIDE_PARENT_TAG_FLAG, null);
        }
    }
    /**
     * render loop template 
     * @param mixed $host 
     * @param mixed $children 
     * @param mixed $t_options 
     * @return string
     */
    private static function _RenderTemplate2($host, $children, $t_options)
    { 
        $sb = new StringBuilder;
        $tc = $host; 
        foreach ($children as $child) {
            if (!$child->getIsVisible()) {
                continue;
            }
            $v_cx = HtmlRenderer::Render($child, $t_options);
            $sb->append($v_cx);
        }
        if (!$sb->isEmpty()) {
            if ($tc->getCanRenderTag()) {
                $tc["*for"] = "\$raw"; 
                $tag = $tc->getTagName();
                $sb->set(sprintf(
                    '<%s%s>%s</%s>',
                    $tag,
                    ' '.HtmlRenderer::GetAttributeString($tc, $t_options),
                    $sb.'',
                    $tag
                ));
                $tc["*for"] = null;
            } else {
                 $v_var_k = '$'.CompilerConstants::LOOP_CONTEXT_DATA_VAR;
          
                $sb->set(implode("\n", array_filter([     
                    '<?php',
                    igk_is_debug() ? '/* no tag definition loop */' : null,
                    sprintf('if (isset(%s)):', $v_var_k),
                    sprintf('foreach(%s as $key=>$raw):', $v_var_k), 
                    '?>'.$sb.'<?php endforeach;',
                    'endif;',
                    '?>'
                    ]))
                );

            }
        }
        return $sb . '';
    }

    /**
    * auto generate doc.
    * @param null|string $hookExpression expression name that will serve as global variables
    * @return void
    */
    private static function  _HostChain(HtmlItemBase $n, string $content, $data, ?BaseController $ctrl, ?string $hookExpression = null)
    {
        $ldcontext = igk_init_binding_context($n, $ctrl, is_array($data) ? $data : null);
        $ldcontext->transformToEval = true;
        $ldcontext->hookExpression = $hookExpression;
        $ldcontext->load_expression = false;
        $file = igk_create_guid(); // tempnam(sys_get_temp_dir(), "host");
        $v_base_name = 'igk-temp-hostchain'; // basename($file)
        igk_push_article_chain($file, $ldcontext);
        try {
            igk_html_bind_article_content($n, $content, $data, $ctrl, $v_base_name, true, $ldcontext);
        } finally {
            igk_pop_article_chain();
        }
    }
    /**
     * bind host callable 
     * @param callable $callback 
     * @param mixed $param 
     * @return void 
     */

    public function host(callable $callback, ...$param)
    {
        // passing to callback 
        // clear flag - because of renderging with callback
        $this->node->setFlag(self::OVERRIDE_PARENT_TAG_FLAG, null);
        foreach ($this->args as $k => $c) {
            $callback($this->node, $c, $k, ...$param);
        }
        $this->callback = $callback;
        $this->params = $param;
        return $this;
    }

    /**
     * get rendergin children
     * @param mixed $options 
     * @return array 
     */

    protected function _getRenderingChildren($options = null)
    {
        if (!is_null($this->callback)) {
            $this->host($this->callback, ...$this->params);
        }
        if ($this->m_template->getHasChilds()) {
            $s = $this->generateRender($this->m_template->getChilds(), $options);
            // html
            $n = igk_create_notagnode();
            $n->load($s);
            return [$n];
            //return [new HtmlTextNode($s)]; 
        }
        return [];
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        // + | call sub method on parent and return its 
        if ($this->node) {
            $a = call_user_func_array([$this->node, $name], $arguments);
            if ($a instanceof HtmlItemBase) {
                $this->add($a);
            }
            return $a;
        }
        return parent::__call($name, $arguments);
    }

    /**
    * Clears Childs.
    */
    public function clearChilds()
    {
        parent::clearChilds();
        $this->m_template->clearChilds();
    }
}

/**
* Lopper eval data.
* @package IGK\System\Html\Dom
*/
class LopperEvalData implements \Iterator
{
    use IteratorTrait;

    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;

    /**
    * Property: current.
    * @var mixed
    */
    private $m_current;

    /**
    * Property: it.
    * @var mixed
    */
    private $m_it;

    /**
    * Property: keys.
    * @var mixed
    */
    private $m_keys;

    /**
    * Property: pos.
    * @var mixed
    */
    private $m_pos;

    /**
    * .ctr
    * @param mixed $data
    */
    public function __construct($data)
    {
        $this->m_data = $data;
        $this->m_it = null;
        $this->m_keys = array_keys($data);
        $this->m_pos = 0;
    }

    /**
    * Iterator current.
    */
    public function _iterator_current()
    {
        $g = $this->m_data[$this->m_it];
        if (!is_numeric($g) && !($g instanceof DataArgs)) {
            return new DataArgs($g);
        }
        return $g;
    }

    /**
    * Iterator key.
    */
    public function _iterator_key()
    {
        return $this->m_it;
    }

    /**
    * Next.
    * @return void
    */
    public function next(): void
    {
        $this->m_pos++;
        $k = igk_getv($this->m_keys, $this->m_pos);
        $this->m_it = $k;
    }

    /**
    * Iterator rewind.
    */
    public function _iterator_rewind()
    {
        $this->m_pos = 0;
        $this->m_it = $this->m_keys[$this->m_pos];
    }

    /**
    * Iterator valid.
    */
    public function _iterator_valid()
    {
        if (is_null($this->m_it))
            return false;
        return isset($this->m_data[$this->m_it]);
    }
}

/**
* Html looper template node.
* @package IGK\System\Html\Dom
*/
class HtmlLooperTemplateNode extends HtmlItemBase{

    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag(){
        return false;
    }
}