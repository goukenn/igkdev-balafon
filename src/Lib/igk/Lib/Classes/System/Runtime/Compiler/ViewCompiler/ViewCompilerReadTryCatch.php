<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerReadTryCatch.php
// @date: 20221026 17:16:12
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use IGK\System\Runtime\Compiler\ReadTokenOptions;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
trait ViewCompilerReadTryCatch{

    /**
    * Read read try catch block.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    */
    protected function _readReadTryCatchBlock(ReadTokenOptions $options , ?string $id, string $value){
        $this->flagHandler = [$this, '_readHandleReadTryCatchBlock'];
    }

    /**
    * Read handle read try catch block.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    protected function _readHandleReadTryCatchBlock(ReadTokenOptions $options , ?string $id, string $value):bool{
        return true;
    }

    /**
    * End handle read try catch block.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    */
    protected function _endHandleReadTryCatchBlock(ReadTokenOptions $options , ?string $id, string $value){
    }
}