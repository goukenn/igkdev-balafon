<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenVariableFlagOption.php
// @date: 20221023 14:53:29
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenVariableFlagOption extends ReadTokenFlagOptions{
    var $name;
    var $default;
    var $dependOn = false;
    var $modifiers= [];
    var $render = false;
}