<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerReadTryCatch.php
// @date: 20221026 17:16:12
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use IGK\System\Runtime\Compiler\ReadTokenOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
trait ViewCompilerReadTryCatch{
    protected function _readReadTryCatchBlock(ReadTokenOptions $options , ?string $id, string $value){
        $this->flagHandler = [$this, '_readHandleReadTryCatchBlock'];
         
    }  

    protected function _readHandleReadTryCatchBlock(ReadTokenOptions $options , ?string $id, string $value):bool{
        return true;
    }

    protected function _endHandleReadTryCatchBlock(ReadTokenOptions $options , ?string $id, string $value){

    }
}