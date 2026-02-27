<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICompilerTokenHandler.php
// @date: 20221019 16:41:43
namespace IGK\System\Runtime\Compiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
interface ICompilerTokenHandler{

    /**
    * Handles Token.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    function HandleToken(ReadTokenOptions $options, ?string $id, string $value): bool;
}