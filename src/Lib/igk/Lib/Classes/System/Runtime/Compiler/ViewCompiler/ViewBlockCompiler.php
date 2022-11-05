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

///<summary></summary>
/**
 * process and compile every line block
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
class ViewBlockCompiler
{
    var $controller;
    var $detector;
    var $variables = [];
    var $m_extract;
    var $header;
    
    private $m_listener;
    private $m_source;
    private $m_init = false;
    private static $sm_compiler_args = [];
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
    public function __construct(bool $extract=false)
    {
        $this->m_extract = $extract;
     
    }
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

        // igk_wln_e( __FILE__.":".__LINE__, array_keys($vars));

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
                        // replace with ctrl
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

        // $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_ecapsed_string";
        $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_var";
        // $vars["___IGK_PHP_VAR___"] = "igk_express_litteral_var";
        $vars["___IGK_PHP_EXTRACT_VAR___"] = $this->m_extract;
        $vars[ViewExpressionArgHelper::SETTER_VAR] = new ViewExpressionSetter($vars);
        $vars[ViewExpressionArgHelper::GETTER_VAR] = new ViewExpressionGetter($vars, $v_eval_source);
        //  function($src, $args){  
        //     $fc = Closure::fromCallable(function(){
        //         igk_wln_e("class ", static::class);

        //         return call_user_func_array([static::class, 'eval_source'], func_get_args());
        //     })->bindTo($args["ctrl"]);      
        //     return call_user_func_array($fc, [$src, (object)["data"=>$args], $this->header ?? "<?php"]);
        //     // return $this->eval_source($src, (object)["data"=>$args], $this->header ?? "<?php");
        // });
        $vars[ViewExpressionArgHelper::EXPRESSION] = new ViewExpression($vars, $v_eval, $this->m_extract);
        $vars[ViewExpressionArgHelper::RESPONSE] = new ViewExpression($vars, $v_eval, $this->m_extract);
    }
    private static function eval_source(){
        foreach (array_keys(func_get_arg(1)->data) as $_) {
            if ($_ == "_") {
                igk_die("[_] is reserved variable");
            }
            if ($_ == "this") {
                // replace with ctrl
                $_ = "ctrl";
            }
            $$_ = &func_get_arg(1)->data[$_];
        }
        ob_start();
        $p = eval("?>" . implode("\n", [func_get_arg(2), func_get_arg(0)]));
        ob_end_clean();
        return $p;
    }
    private function _initListener(){
        return \Closure::fromCallable(function () {
            foreach (array_keys(func_get_arg(1)->data) as $_) {
                if ($_ == "_") {
                    igk_die("[_] is reserved variable");
                }
                if ($_ == "this") {
                    // replace with ctrl
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
            // igk_debug_wln_e("first:".$buffer, "x value:", $x);
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
            // igk_wln_e(array_keys(get_defined_vars()));

            if ($___IGK_PHP_SETTER_VAR___->getIsUpdate()) {
                $g = $___IGK_PHP_SETTER_VAR___->getExpression(func_get_arg(0));
                $___IGK_PHP_SETTER_VAR___->resetUpdate();
                return $g;
            }
            return func_get_arg(0);
        })->bindTo($this->controller);
    }
    private function _execute()
    {
        if (is_null($this->m_listener)) {
            $this->m_listener  = $this->_initListener();
        }
        $this->_initialize();
        $v_eval = $this->m_listener;
        $vars = &$this->variables;
        $v_detector = $this->detector;

        // $bck = igk_getv($vars, "t", igk_create_node('notagnode'));
        // $vars["t"] = $v_detector;
        // $v_detector->setDocument(igk_getv($vars, "doc"));
        // // $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_ecapsed_string";
        // $vars["___IGK_PHP_EXPRESS_VAR___"] = "igk_express_var";
        // // $vars["___IGK_PHP_VAR___"] = "igk_express_litteral_var";
        // $vars["___IGK_PHP_EXTRACT_VAR___"] = $this->m_extract;
        // $vars[ViewExpressionArgHelper::SETTER_VAR] = new ViewExpressionSetter($vars);
        // $vars[ViewExpressionArgHelper::GETTER_VAR] = new ViewExpressionGetter($vars);
        // $vars[ViewExpressionArgHelper::EXPRESSION] = new ViewExpression($vars, $v_eval, $this->m_extract);
        // $vars[ViewExpressionArgHelper::RESPONSE] = new ViewExpression($vars, $v_eval, $this->m_extract);

        $vars_clone = array_merge($vars);
        ViewExpressionArgHelper::$Variables[] =  (object)[
            "variables" => &$vars
        ];
        is_null($this->header) && $this->header = "<?php\n";
        // parameters to pass to avoid populate eval context.
        $pass = (object)["data" => &$vars];
        $v_detector->setFreezeClearModify(true);
        $n = $v_eval($this->m_source, $pass, $this->header);
        $v_detector->setFreezeClearModify(false);
        // in subchilds
        // if ($v_detector->getModify()) {
        //     $m = HtmlRenderer::GetAttributeArray($v_detector, null);
        //     // $attr = $vars["t"]->getAttributes();
        //     if ($m){
        //         $m = HtmlRenderer::GetAttributeArray($v_detector, null);
        //         if (!empty($m))
        //             $n .= sprintf('$__igk_attr__(%s);', var_export($m, true));
        //         // $n .= '//%{{_ATTRIBS_BEGIN}}' . "\n";
        //         // $n .= '$__IGK_PHP_ATTRIBS___[]=' . var_export($m, true) . ';';
        //         // $n .= 'if (isset($t)) $t->setAttributes($__IGK_PHP_ATTRIBS___[count($__IGK_PHP_ATTRIBS___)-1]);' . "\n";
        //         // $n .= '//%{{_ATTRIBS_END}};' . "\n";
        //     }
        //     $n .= $vars["doc"]?->renderAccessiblity();
        //     $s = $v_detector->render();
        //     if (!empty($s)){
        //         $n = $n . "? >" . $v_detector->render() . "<?php";
        //     }
        //     // clear detector child modification
        //     //$v_detector->clearChilds();
        // }  
        array_pop(ViewExpressionArgHelper::$Variables);
        return $n;
    }

    public function complete(): ?string{
        $v_detector = $this->detector;
        $vars = &$this->variables;
        $n = null;
        if ($v_detector->getModify()) {
            $m = HtmlRenderer::GetAttributeArray($v_detector, null);
            // $attr = $vars["t"]->getAttributes();
            if ($m){
                $m = HtmlRenderer::GetAttributeArray($v_detector, null);
                if (!empty($m))
                    $n .= sprintf('$__igk_attr__(%s);', var_export($m, true));
                // $n .= '//%{{_ATTRIBS_BEGIN}}' . "\n";
                // $n .= '$__IGK_PHP_ATTRIBS___[]=' . var_export($m, true) . ';';
                // $n .= 'if (isset($t)) $t->setAttributes($__IGK_PHP_ATTRIBS___[count($__IGK_PHP_ATTRIBS___)-1]);' . "\n";
                // $n .= '//%{{_ATTRIBS_END}};' . "\n";
            }
            $n .= $vars["doc"]->renderAccessiblity();
            $s = $v_detector->render();
            if (!empty($s)){
                $n = $n . "?>" . $v_detector->render() . "<?php";
            } 
            // clear detector child modification
            $v_detector->clearChilds();
        }
        return $n;
    }
   
}
