<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompiler.php
// @date: 20220909 15:25:33
namespace IGK\System\Runtime\Compiler;

use IGK\System\Exceptions\NotImplementException;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class BalafonViewCompiler{
    /**
     * table of evaluation properties
     * @var array
     */
    var $evalution = [];
    /**
     * options passed to compilator
     * @var mixed
     */
    var $options;

    protected $m_compilerHandler;

    private $m_output;

    public function __construct()
    {
        $this->m_compilerHandler = $this->_createCompilerHandler();    
    }
    protected function _createCompilerHandler(){
        return new BalafonViewCompilerHandler($this);
    }
    /**
     * 
     * @param string $source 
     * @param array|object|BalafonViewCompilerOptions $options 
     * @return false|BalafonViewCompilerResult 
     */
    public static function CompileSource(string $source, $options=null){
        $result = false;
        $g = new static;
        $g->options = $options;
        $_out_str = "";
        $g->m_output = & $_out_str;
        $tstart = igk_sys_request_time();
        $tokens = \token_get_all($source);
        $options = new BalafonViewCompilerReadOptions; 
        $fc = function($e) use (& $_out_str, & $result, $options, $g){
            $g->tokenCompile($e, $_out_str, $result, $options);           
        };
        
        while(!$result && (count($tokens)>0)){
            $q = array_shift($tokens);
            $fc($q);
        }
        $duration = igk_sys_request_time() - $tstart;
        // throw new NotImplementException(__METHOD__);
        if (!$result){
            $tresult = new BalafonViewCompilerResult;
            $tresult->duration = $duration;
            $tresult->source = $_out_str; 
            return $tresult;
        }
        return $result;
    }
    protected function tokenCompile($e, & $_out_str, & $result, $options){
        $skip = $options->skip_line;
        if (is_array($e)){
            $id = $e[0];
            $value= $e[1];
            // igk_debug_wln(token_name($id).":".$value.",".$skip);
            switch($id){
                case T_COMMENT:
                    $options->skip_line = 1;
                    if (preg_match("/#\{\{%(?P<expression>.+)\}\}/", $value, $data)){
                        $this->evaluationComment(trim($data['expression']));
                    }
                    return;
                case T_WHITESPACE:
                    if ($skip){ 
                        return;
                    }
                    $_out_str.= $value;
                    break;
                case T_OPEN_TAG:
                    $options->skip_line = 1;
                    $_out_str.= $value;
                    break;
                default:
                    $_out_str.= $value;
                break;
            }
       
        } else {
            if (!$skip){
                $_out_str.=$e;                    
            }
        }
        if ($skip){
            $options->skip_line = 0;               
        }
    }


    public function evaluationComment($data){
        $this->m_output .= $this->m_compilerHandler->evaluate($data);
  
    }

}