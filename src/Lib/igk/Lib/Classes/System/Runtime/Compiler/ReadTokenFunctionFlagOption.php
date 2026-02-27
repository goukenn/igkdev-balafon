<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenFunctionFlagOption.php
// @date: 20221024 02:04:09
namespace IGK\System\Runtime\Compiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenFunctionFlagOption extends ReadTokenFlagOptions{

    /**
    * Property: op.
    * @var mixed
    */
    var $op="name";

    /**
    * Property: depth.
    * @var mixed
    */
    var $depth = 0;

    /**
    * Property: condition.
    * @var mixed
    */
    var $condition = "";

    /**
    * Type of type.
    * @var mixed
    */
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