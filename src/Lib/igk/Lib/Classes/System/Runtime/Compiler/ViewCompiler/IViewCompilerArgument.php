<?php
// @author: C.A.D. BONDJE DOUE
// @file: IViewCompilerArgument.php
// @date: 20221019 14:18:35
namespace IGK\System\Runtime\Compiler\ViewCompiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
interface IViewCompilerArgument{
    /**
    * return instruction that will update the view context
    * @param mixed $reset
    * @return null|string
    */
    function getInstruction($reset=true): ?string;
}