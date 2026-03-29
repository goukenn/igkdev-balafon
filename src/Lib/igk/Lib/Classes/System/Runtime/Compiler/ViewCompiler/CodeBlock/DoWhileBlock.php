<?php
// @author: C.A.D. BONDJE DOUE
// @file: DoWhileBlock.php
// @date: 20221026 17:37:04
namespace IGK\System\Runtime\Compiler\ViewCompiler\CodeBlock;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompilerBockInfo;
/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler\CodeBlock
*/
class DoWhileBlock extends ViewCompilerBockInfo{
    /**
    * .ctr
    */
    public function __construct(){
        parent::__construct("do");
        $this->condition = false;
    }
    /**
    * Starts Block.
    */
    public function startBlock(){
        return sprintf("do{");
    }
    /**
    * End block.
    */
    public function endBlock(){
        return ["}", sprintf("while (%s);", $this->condition)];
    }
}