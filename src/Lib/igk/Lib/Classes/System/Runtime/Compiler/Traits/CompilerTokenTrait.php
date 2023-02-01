<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenTrait.php
// @date: 20221019 16:13:42
namespace IGK\System\Runtime\Compiler\Traits;
 
use IGK\System\Runtime\Compiler\ReadTokenOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Traits
*/
trait CompilerTokenTrait{
    private $m_read_options;

    function parseToken(string $source){
        $this->m_read_options = $options = $this->m_read_options ?? $this->createReadOptionsToken() ?? igk_die("failed to create option token"); 
        $this->m_read_options->source = $source;
        $tab = token_get_all($source);
        while (count($tab)>0){
            $id = null;
            $value = array_shift($tab);
            if (is_array($value)){
                $id = $value[0];
                $value = $value[1];
            }
            igk_debug_wln(sprintf("token::%s:%s", $id? token_name($id): $id, $value));

            $this->_checkBracket($options, $value);
            $this->_checkHereDocDocument($options, $id, $value);
            $this->_handleWhiteSpace($options, $id, $value);  
            
          

            $options->close_curl = false;
            if (($value=="{") && ($id== T_CURLY_OPEN)){
                $options->curl_open = true;
            } else if (($value == "}") && ($options->curl_open)){
                $options->curl_open = false;
                $options->close_curl = true;
            }
            // + | handle token
            if (!$this->HandleToken($options, $id, $value)){
                break;
            }
        } 
        $this->endHandleToken($options);
    }
    protected function _checkHereDocDocument($options, $id, $value){
        switch($id){
            case T_START_HEREDOC:
                $options->heredocFlag = true;
                break;
            case T_END_HEREDOC:
                $options->heredocFlag = false;
                break;
        }
    }
    protected function createReadOptionsToken(){
        $g = new ReadTokenOptions;    
        $g->mergeVariable = $this->mergeVariable;
        $g->noComment  = $this->noComment;
        // igk_debug_wln_e("no comment", $this->noComment);
        return $g;
    }
    protected function endHandleToken($options){
        // check that buffer is empty
        if (count($options->buffers)!=0){         
            igk_wln_e("", __FILE__.":".__LINE__, 
            "buffers is not empty");
        }
    }
}