<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompiler2.php
// @date: 20221013 14:57:44
namespace IGK\System\Runtime\Compiler;

use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Runtime\Compiler\Html\ConditionBlockNode;
use IGKException;

///<summary></summary>
/**
* compile instruction to view html compiled result 
* @package IGK\System\Runtime\Compiler
*/
class BalafonViewCompiler2 implements IBalafonViewCompiler{
    /**
     * store compilation result
     * @var ?string
     */
    private $_output;
    /**
     * string
     * @var mixed
     */
    var $source;
    /**
     * 
     * @var ViewEnvironmentArgs
     */
    var $options;

    var $variables = [];

    /**
     * .ctr
     * @return void 
     */
    public function __construct(){
    }

    public function output(): ?string { 
        // 
        return $this->_output;    
    }
    /**
     * extract data
     * @param array $blockInstructions 
     * @param bool $extract 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function compile(array $blockInstructions, bool $extract = true, ?string $header = null){
        
        $v_compiler = new BalafonViewCompileInstruction;
        $v_compiler->data = $blockInstructions;
        $v_compiler->variables = & $this->variables;
        $v_compiler->extract = $extract; 
        $v_compiler->header = $header;
        return $v_compiler->compile(); 
    }
    /**
     * append source result
     * @param string $src 
     * @return mixed 
     */
    public function append(string $src){
        $this->_output .= $src;
    }
}