<?php
// @author: C.A.D. BONDJE DOUE
// @file: DoWhileBlock.php
// @date: 20221026 17:37:04
namespace IGK\System\Runtime\Compiler\ViewCompiler\CodeBlock;

use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompilerBockInfo;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler\CodeBlock
*/
class DoWhileBlock extends ViewCompilerBockInfo{
    public function __construct(){
        parent::__construct("do");
        $this->condition = false;
    }
    public function startBlock(){
        return sprintf("do{");
    }
    public function endBlock(){
        return ["}", sprintf("while (%s);", $this->condition)];
    }

}