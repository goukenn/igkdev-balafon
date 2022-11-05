<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerReadSwitchCase.php
// @date: 20221026 17:15:51
namespace IGK\System\Runtime\Compiler\ViewCompiler\Traits;

use IGK\System\Runtime\Compiler\ReadTokenOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
trait ViewCompilerReadSwitchCaseTrait{
    protected function _readSwitchCaseBlock(ReadTokenOptions $options , ?string $id, string $value){
        igk_die("switch case not implement ):)");
        // $this->flagHandler = [$this, '_readHandleSwitchCaseBlock'];       
    }

    protected function _readHandleSwitchCaseBlock(ReadTokenOptions $options , ?string $id, string $value):bool{
        return true;
    }

    protected function _endHandleSwitchCaseBlock(ReadTokenOptions $options , ?string $id, string $value){
    }
}