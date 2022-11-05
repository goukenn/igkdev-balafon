<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICompilerTokenHandler.php
// @date: 20221019 16:41:43
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
interface ICompilerTokenHandler{
    function HandleToken(ReadTokenOptions $options, ?string $id, string $value): bool;
}