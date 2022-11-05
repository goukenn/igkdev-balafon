<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenCommentHandlerTrait.php
// @date: 20221021 18:58:53
namespace IGK\System\Runtime\Compiler\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Traits
*/
trait CompilerTokenCommentHandlerTrait{
    /**
     * reset command and modifier 
     * @param mixed $options 
     * @return void 
     */
    protected function _resetCommentAndModifier($options)
    {
        $options->modifiers = [];
        $options->comment = "";
        $options->phpDoc = "";
    }
}