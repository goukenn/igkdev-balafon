<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewBlockCompiler.php
// @date: 20221026 06:53:46
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use Closure;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Runtime\Compiler\ViewCompiler\Html\CompilerNodeModifyDetector;
use IGK\System\Runtime\Compiler\ViewCompiler\Html\ViewDocumentHandler;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewExpression;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewExpressionArgHelper;

require_once __DIR__."/helper-functions.php";
/**
 * process and compile every line block
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
class ViewBlockCompiler
{
    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;
    /**
    * Property: detector.
    * @var mixed
    */
    var $detector;
    /**
    * Property: variables.
    * @var mixed
    */
    var $variables = [];
    /**
    * Property: extract.
    * @var mixed
    */
    private $m_extract;
    /**
    * Property: header.
    * @var mixed
    */
    var $header;
    /**
    * Listener: listener.
    * @var mixed
    */
    private $m_listener;
    /**
    * Property: source.
    * @var mixed
    */
    private $m_source;
    /**
    * Property: init.
    * @var mixed
    */
    private $m_init = false;
    /**
    * Property: compiler args.
    * @var mixed
    */
    private static $sm_compiler_args = [];
    /**
    * Property: source.
    * @var mixed
    */
    private static $sm_SOURCE = null;
    /**
     * compile source
     * @param string $src 
     * @return bool|string 
     */
    public function compile(string $src)
    {
        $this->m_source = $src;
        return $this->_execute();
    }
    /**
    * .ctr
    * @param bool $extract
    */
    public function __construct(bool $extract=false)
    {
        $this->m_extract = $extract;
    }
    /**
    * Initialize.
    */
    protected function _initialize()
    {
        if ($this->m_init){
            return;
        }
        $this->m_init = true;
        $v_detector = $this->detector;
        $vars = &$this->variables;
        $bck = igk_getv($vars, "t", igk_create_node('notagnode'));
        $doc = new ViewDocumentHandler;
        $vars["t"] = $v_detector;
        $vars["doc"] = $doc;
        $v_detector->setDocument($doc); 
        $v_eval = $this->m_listener;
        $header = & $this->header;
        $v_eval_source = Closure::fromCallable(function($src, $args) use($header){
            $v_fc = function(){
                foreach (array_keys(func_get_arg(1)->data) as $_) {
                    if ($_ == "_") {
                        igk_die("[_] is reserved variable");
                    }
                    if ($_ == "this") {
                        $_ = "ctrl";
                    }
                    $$_ = &func_get_arg(1)->data[$_];
                }
                unset($_);
                ob_start();
                $___igk_php_eval_response___ = eval("?>" . implode("\n", [func_get_arg(2), func_get_arg(0)]));
                ob_end_clean();
                return $___igk_php_eval_response___;
            };
            $v_fc->bindTo($this);
            return $v_fc($src, (object)["data"=>$args], $header ?? "<?php");
        })->bindTo($vars["ctrl"]);
        $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_var";
        $vars["___IGK_PHP_EXTRACT_VAR___"] = $this->m_extract;
        $vars[ViewExpressionArgHelper::SETTER_VAR] = new ViewExpressionSetter($vars);
        $vars[ViewExpressionArgHelper::GETTER_VAR] = new ViewExpressionGetter($vars, $v_eval_source);
        $vars[ViewExpressionArgHelper::EXPRESSION] = new ViewExpression($vars, $v_eval, $this->m_extract);
        $vars[ViewExpressionArgHelper::RESPONSE] = new ViewExpression($vars, $v_eval, $this->m_extract);
    }
    /**
    * auto generate doc.
    * @return
    */
    private static function eval_source(){
        foreach (array_keys(func_get_arg(1)->data) as $_) {
            if ($_ == "_") {
                igk_die("[_] is reserved variable");
            }
            if ($_ == "this") {
                $_ = "ctrl";
            }
            $$_ = &func_get_arg(1)->data[$_];
        }
        ob_start();
        $p = eval("?>" . implode("\n", [func_get_arg(2), func_get_arg(0)]));
        ob_end_clean();
        return $p;
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _initListener(){
        return \Closure::fromCallable(function () {
            foreach (array_keys(func_get_arg(1)->data) as $_) {
                if ($_ == "_") {
                    igk_die("[_] is reserved variable");
                }
                if ($_ == "this") {
                    $_ = "ctrl";
                }
                $$_ = &func_get_arg(1)->data[$_];
            }
            unset($_);
            igk_is_debug() && igk_ilog("eval : " . func_get_arg(0));
            ob_start();            
            if (func_num_args() > 2) { 
                $___IGK_PHP_RESPONSE___ = eval("?>" . implode("\n", [func_get_arg(2), func_get_arg(0)]));
            } else {
                $___IGK_PHP_RESPONSE___ = eval(func_get_arg(0));
            }
            $buffer = trim(ob_get_contents());
            ob_end_clean();
            if (!empty($buffer) || $t->getModify()) {
                if ($t->getModify()) {
                    if (!empty($buffer)){
                        $t->text($buffer);
                    }
                    return;
                }
                return func_get_arg(0);
            }
            if ($t->getParam(CompilerNodeModifyDetector::CLEAR_FLAG_PARAM)) {
                $t->setParam(CompilerNodeModifyDetector::CLEAR_FLAG_PARAM, null);
                return null;
            }
            if ($___IGK_PHP_SETTER_VAR___->getIsUpdate()) {
                $g = $___IGK_PHP_SETTER_VAR___->getExpression(func_get_arg(0));
                $___IGK_PHP_SETTER_VAR___->resetUpdate();
                return $g;
            }
            return func_get_arg(0);
        })->bindTo($this->controller);
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _execute()
    {
        if (is_null($this->m_listener)) {
            $this->m_listener  = $this->_initListener();
        }
        $this->_initialize();
        $v_eval = $this->m_listener;
        $vars = &$this->variables;
        $v_detector = $this->detector;
        $vars_clone = array_merge($vars);
        ViewExpressionArgHelper::$Variables[] =  (object)[
            "variables" => &$vars
        ];
        is_null($this->header) && $this->header = "<?php\n";
        $pass = (object)["data" => &$vars];
        $v_detector->setFreezeClearModify(true);
        $n = $v_eval($this->m_source, $pass, $this->header);
        $v_detector->setFreezeClearModify(false);
        array_pop(ViewExpressionArgHelper::$Variables);
        return $n;
    }
    /**
    * Complete.
    * @return ?string
    */
    public function complete(): ?string{
        $v_detector = $this->detector;
        $vars = &$this->variables;
        $n = null;
        if ($v_detector->getModify()) {
            $m = HtmlRenderer::GetAttributeArray($v_detector, null);
            if ($m){
                $m = HtmlRenderer::GetAttributeArray($v_detector, null);
                if (!empty($m))
                    $n .= sprintf('$__igk_attr__(%s);', var_export($m, true));
            }
            $n .= $vars["doc"]->renderAccessiblity();
            $s = $v_detector->render();
            if (!empty($s)){
                $n = $n . "?>" . $v_detector->render() . "<?php";
            } 
            $v_detector->clearChilds();
        }
        return $n;
    }
}