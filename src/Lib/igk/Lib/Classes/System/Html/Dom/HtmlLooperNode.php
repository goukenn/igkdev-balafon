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

/**
 * summary html array looper.
 * Help write view and article template without the php foreach loop
 * @example usage $t->loop([1,2,3])->host(function($n, $a){\
 *                  $n->li()->Content = "Item ".$a;\
 *              });
 * it bind to looper node : -:)
 */
class HtmlLooperNode extends HtmlItemBase{
    private $args;
    private $node;  
    private $callback; 
    private $m_template;    
    protected $tagname = "igk:looper";
    /**
     * param to pass 
     * @var mixed
     */
    private $params; // 
    var $controller;
    /**
     * 
     * @param mixed $args 
     * @param mixed $node parent node that will host data 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function __construct($args, $node){     
        parent::__construct();   
        $this->args = $args;
        $this->node = $node;
        $this->setFlag("NO_TEMPLATE",1); 
        $this->m_template = igk_create_notagnode();
    }   
    public function getCanRenderTag() { return false; }

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
    public function render($options = null) {  
        if ($this->callback){
            return null;
        }
        $v_childrend = $this->m_template->getChilds();
        // $v_childrend = $this->getChilds();
        $c = $v_childrend->count(); 
        $v_out = null;      
        if ($c){
            // template rendering 
            $v_out = $this->generateRender($v_childrend, $options);
            return $v_out;
        }
        return null; 
    }
    /**
     * 
     * @param mixed $children 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function generateRender($children, $options = null){
        $ctrl = $this->controller ?? ViewHelper::CurrentCtrl()  ?? SysDbController::ctrl();     
        $sb = new StringBuilder;
        $t_options = $options ? clone($options) : (object)[];
        $t_options->renderingContext = RenderingContext::TEMPLATE;
        foreach($children->to_array() as $tc){
            $tc["*for"] = "\$raw";
            $sb->append(HtmlRenderer::Render($tc, $t_options));
        } 
        $n = igk_create_notagnode(); 
        if ($this->args instanceof IViewExpressionArg){
            $hook_expression = $this->args->getExpression();                 
            self::HostChain($n, $sb."", $this->args, $ctrl, $hook_expression);
            $v_out  = $n->render();  
        } else { 
            $hook_expression = CompilerConstants::LOOP_CONTEXT_DATA_VAR; 
            self::HostChain($n, $sb."", $this->args, $ctrl, '$'.$hook_expression);
            $v_out  = $n->render();
            if ($this->args instanceof ViewDataArgs)  {
                $v_targs = $this->args->getData();
            }else{
                $v_targs = is_array($this->args) ? $this->args : 0;
            }
            if ($v_targs){ 
                ob_start();                    
                SysUtils::Eval($v_out, [
                    $hook_expression =>  new LopperEvalData($this->args),
                    "raw"=>$this->args,
                    "ctrl"=> $ctrl 
                ]); 
                $v_out = ob_get_contents();  
                ob_end_clean();
            } else {
               $v_out=null;
            }
        } 
        return $v_out;
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
        $ldcontext->load_expression = false;
        $file = igk_create_guid();// tempnam(sys_get_temp_dir(), "host");
        $v_base_name = 'igk-temp-hostchain'; // basename($file)
        igk_push_article_chain($file, $ldcontext);
        try{
            igk_html_bind_article_content($n, $content, $data, $ctrl, $v_base_name, true, $ldcontext);        
        }finally{
            igk_pop_article_chain();
        }
    }

    /**
     * bind host callable 
     * @param callable $callback 
     * @param mixed $param 
     * @return void 
     */
    public function host(callable $callback, ...$param){
        // passing to callback 
        foreach($this->args as $k => $c){
            $callback($this->node, $c, $k, ...$param);
        }
        $this->callback = $callback;
        $this->params = $param;
        return $this;
    } 
    protected function _add($n, bool $force = false): bool
    {
        if (!is_null($this->callback)){
            return false;
        }
        return $this->m_template->_add($n);        
    }  
    /**
     * get rendergin children
     * @param mixed $options 
     * @return array 
     */
    protected function _getRenderingChildren($options = null){        
        if (!is_null($this->callback)){
            $this->host($this->callback, ...$this->params);
        }
        if ($this->m_template->getHasChilds()){
            $s = $this->generateRender($this->m_template->getChilds(), $options);
            // html
            $n = igk_create_notagnode(); 
            $n->load($s);
            return [$n];
            //return [new HtmlTextNode($s)]; 
        }
        return [];        
    }
    public function __call($name, $arguments){
        // + | call sub method on parent and return its 
        if ($this->node){
            $a = call_user_func_array([$this->node, $name],$arguments);
            if ($a instanceof HtmlNode){
                $this->add($a);
            }
            return $a;
        }
        return parent::__call($name, $arguments);
    }
    public function clearChilds()
    {
        parent::clearChilds();
        $this->m_template->clearChilds();
    }
}


class LopperEvalData implements  \Iterator{
    use IteratorTrait;
    private $m_data;
    private $m_current;
    private $m_it;
    private $m_keys;
    private $m_pos;
    public function __construct($data){
        $this->m_data = $data;
        $this->m_it = null;
        $this->m_keys = array_keys($data);
        $this->m_pos = 0;
    }
    public function _iterator_current(){
        $g = $this->m_data[$this->m_it];
        if (!is_numeric($g) && !($g instanceof DataArgs)){            
            return new DataArgs($g);
        }
        return $g;
    }
    public function _iterator_key(){
        return $this->m_it;
    }
    public function next():void{
        $this->m_pos++;
        $k = igk_getv($this->m_keys, $this->m_pos);
        $this->m_it = $k; 
    }
    public function _iterator_rewind(){
        $this->m_pos = 0;
        $this->m_it = $this->m_keys[$this->m_pos];
    }
    public function _iterator_valid(){
        if (is_null($this->m_it))
            return false;
        return isset($this->m_data[$this->m_it]);
    }
}