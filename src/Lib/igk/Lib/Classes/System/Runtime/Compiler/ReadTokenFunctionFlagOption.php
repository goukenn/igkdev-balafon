<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenFunctionFlagOption.php
// @date: 20221024 02:04:09
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenFunctionFlagOption extends ReadTokenFlagOptions{
    var $op="name";
    var $depth = 0;
    var $condition = "";
    var $type;
    /**
     * argument name
     * @var mixed
     */
    var $argName;
    /**
     * argument type
     * @var mixed
     */
    var $argType;
}