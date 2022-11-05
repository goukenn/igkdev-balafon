<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLooperNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helper\ViewHelper;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\CompilerConstants;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewExpressionArg;
use IGKException;

/**
 * summary html array looper.
 * Help write view and article template without the php foreach loop
 * @example usage $t->loop([1,2,3])->host(function($n, $a){\
 *                  $n->li()->Content = "Item ".$a;\
 *              });
 */
class HtmlLooperNode extends HtmlItemBase{
    private $args;
    private $node;  
    private $callback;
    private $params;
    protected $tagname = "igk:looper";
    var $controller;
    public function __construct($args, $node){     
        parent::__construct();   
        $this->args = $args;
        $this->node = $node;
        $this->setFlag("NO_TEMPLATE",1); 
    }   
    public function getCanRenderTag() { return false; }

    public function render($options = null) {  
        if ($this->callback){
            return null;
        }
        $v_childrend = $this->getChilds();
        $c = $v_childrend->count(); 
        $ctrl = $this->controller ?? ViewHelper::CurrentCtrl()  ?? SysDbController::ctrl();     
        if ($c){
            // template rendering 
            $sb = new StringBuilder;
            foreach($v_childrend->to_array() as $c){
                $c["*for"] = "\$raw";
                $sb->append(HtmlRenderer::Render($c));
            }
           
            $n = igk_create_notagnode(); 
            if ($this->args instanceof IViewExpressionArg){
                $hook_expression = $this->args->getExpression();                 
                self::HostChain($n, $sb."", $this->args, $ctrl, $hook_expression);
                $out = $n->render(); 
                $c = $out;
            } else { 
                $hook_expression = CompilerConstants::LOOP_CONTEXT_DATA_VAR; 
                self::HostChain($n, $sb."", $this->args, $ctrl, '$'.$hook_expression);
                $out = $n->render();  
                if (is_array($this->args)){
                    ob_start();
                    extract(
                        [
                            $hook_expression => $this->args,
                            "raw"=>$this->args,
                        ]);  
                              
                    eval("?>".$out);
                    $c = ob_get_contents();
                    ob_end_clean();
                } 
            }
            return $c;
        }
        return null; 
    }

    /**
     * 
     * @param mixed $n target node 
     * @param mixed $content string content
     * @param mixed $data data to pass 
     * @param null|BaseController $ctrl controller source
     * @param null|string $hookExpression expression name that will serve as global variables
     * @return void 
     * @throws IGKException 
     */
    private static function  HostChain($n, $content, $data, ?BaseController $ctrl, ?string $hookExpression=null){
        $ldcontext = igk_init_binding_context($n, $ctrl, $data);
        $ldcontext->transformToEval = true;
        $ldcontext->hookExpression = $hookExpression;
        $file = tempnam(sys_get_temp_dir(), "host");
        igk_push_article_chain($file, $ldcontext);
        igk_html_bind_article_content($n, $content, $data, $ctrl, basename($file), true, $ldcontext);        
        igk_pop_article_chain();
        unlink($file);
    }

    /**
     * bind host callable 
     * @param callable $callback 
     * @param mixed $param 
     * @return void 
     */
    public function host(callable $callback, ...$param){
        foreach($this->args as $k => $c){
            $callback($this->node, $c, $k, ...$param);
        }
        $this->callback = $callback;
        $this->params = $param;
    }   
    public function __getRenderingChildren($options = null){
        $this->host($this->callback, ...$this->params);
        return [];        
    }
}
