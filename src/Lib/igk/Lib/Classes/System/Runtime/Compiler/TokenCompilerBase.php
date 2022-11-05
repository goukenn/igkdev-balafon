<?php
// @author: C.A.D. BONDJE DOUE
// @file: TokenCompilerBase.php
// @date: 20221021 08:45:22
namespace IGK\System\Runtime\Compiler;

use IGK\System\Runtime\Compiler\Traits\CompilerTokenTrait;

defined('T_NAME_FULLY_QUALIFIED') || define('T_NAME_FULLY_QUALIFIED', 263);
defined('T_NAME_QUALIFIED') || define('T_NAME_QUALIFIED', 265);


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class TokenCompilerBase implements ICompiler{
    use CompilerTokenTrait;

     /**
     * no comment
     * @var bool
     */
    var $mergeVariable = false;

    /**
     * no comment
     * @var bool
     */
    var $noComment = false;

    abstract function HandleToken(ReadTokenOptions $options, ?string $id, string $value):bool;
}