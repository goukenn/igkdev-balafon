<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerReadSwitchCase.php
// @date: 20221026 17:15:51
namespace IGK\System\Runtime\Compiler\ViewCompiler\Traits;
use IGK\System\Runtime\Compiler\ReadTokenOptions;
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
trait ViewCompilerReadSwitchCaseTrait{

    /**
    * auto generate doc.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    */
    protected function _readSwitchCaseBlock(ReadTokenOptions $options , ?string $id, string $value){
        igk_die("switch case not implement ):)");
        // $this->flagHandler = [$this, '_readHandleSwitchCaseBlock'];       
    }

    /**
    * auto generate doc.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    protected function _readHandleSwitchCaseBlock(ReadTokenOptions $options , ?string $id, string $value):bool{
        return true;
    }

    /**
    * auto generate doc.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    */
    protected function _endHandleSwitchCaseBlock(ReadTokenOptions $options , ?string $id, string $value){
    }
}