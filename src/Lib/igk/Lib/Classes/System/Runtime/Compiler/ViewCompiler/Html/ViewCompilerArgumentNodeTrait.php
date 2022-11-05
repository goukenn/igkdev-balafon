<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerArgumentNodeTrait.php
// @date: 20221019 14:37:34
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Html
*/
trait ViewCompilerArgumentNodeTrait{
    public function getInstruction($reset = true): ?string { 
        $changed = $this->getChildCount()>0;
        if ($changed){

        }
        if ($reset){
            $this->clear();
            $this->clearChilds();
        }
        return null;
    }
}